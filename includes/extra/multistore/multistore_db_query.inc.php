<?php
/**
 * Refactored Multistore Query Parser mit Sandbox- & Debug-Modus
 * Version: 1.1 (2026)
 */

class MultistoreQueryParser {
    private $idDomain;
    private $isAdmin;
    private $sandboxMode;
    private $logFile;

    private $targetTables = [];

    public function __construct($idDomain, $isAdmin = false, $sandboxMode = false) {
        $this->idDomain = (int)$idDomain;
        $this->isAdmin = (bool)$isAdmin;
        $this->sandboxMode = (bool)$sandboxMode;
        
        // Pfad zur Logdatei (Sollte außerhalb des öffentlichen Web-Ordners liegen oder geschützt sein)
        $this->logFile = DIR_FS_CATALOG . 'log/multistore_sandbox.log';
    }

    /**
     * Hauptfunktion zur Query-Optimierung
     */
    public function parse($query) {
        // Fast-Lane 1: System-Ausschlüsse
        if ($this->isAdmin || !defined('MULTISTORE') || MULTISTORE !== 'true') {
            return $query;
        }

        $lowerQuery = strtolower($query);

        // Fast-Lane 2: Nur SELECT-Abfragen manipulieren
        if (strpos(trim($lowerQuery), 'select') !== 0) {
            return $query;
        }

        // Fast-Lane 3: Prüfen, ob Multistore-relevante Kerntabellen betroffen sind
        if (!$this->isMultistoreTableAffected($lowerQuery)) {
            return $query;
        }

        // Wenn der Sandbox-Modus aktiv ist, isolieren wir die Ausführung
        if ($this->sandboxMode) {
            $this->runSandbox($query);
            return $query; // Im Sandbox-Modus wird IMMER die Original-Query an den Shop zurückgegeben!
        }

        // Normaler Produktiv-Modus: Modifizierte Query zurückgeben
        return $this->executeParsingLogik($query);
    }

    /**
     * Führt das eigentliche Parsing der SQL-Query aus
     */
    private function executeParsingLogik($query) {
        $queryModified = preg_replace('/\s+/', ' ', trim($query));
        $queryModified = str_replace('`', '', $queryModified);
        $queryModified = $this->normalizeCoreAliases($queryModified);
        
        return $this->injectMultistoreFilters($queryModified);
    }

    /**
     * Führt die Query in einer sicheren Umgebung aus und protokolliert das Ergebnis
     */
    private function runSandbox($originalQuery) {
        $timestamp = date('Y-m-d H:i:s');
        $modifiedQuery = '';
        $status = 'Fehlerhaft';
        $errorMessage = 'Keine Fehler';

        try {
            // 1. Generiere die modifizierte Query
            $modifiedQuery = $this->executeParsingLogik($originalQuery);

            // 2. Testweise Ausführung vorbereiten (Wir nutzen EXPLAIN, damit keine Daten verändert werden
            // und die Performance nicht leidet, prüfen aber gleichzeitig die SQL-Syntax)
            $explainQuery = "EXPLAIN " . $modifiedQuery;
            
            // Verwende die native Datenbank-Ressource von modified eCommerce
            global $dbLink; 
            
            if ($dbLink) {
                $testResult = @mysqli_query($dbLink, $explainQuery);
                if ($testResult) {
                    $status = 'Ausführbar';
                    @mysqli_free_result($testResult);
                } else {
                    $status = 'Fehlerhaft';
                    $errorMessage = mysqli_error($dbLink);
                }
            } else {
                $status = 'Fehlerhaft (Keine DB-Verbindung)';
            }

        } catch (Exception $e) {
            $status = 'Fehlerhaft (PHP Exception)';
            $errorMessage = $e->getMessage();
        }

        // 3. Log-Eintrag schreiben
        $this->logToSandboxFile($timestamp, $status, $originalQuery, $modifiedQuery, $errorMessage);
    }

    /**
     * Schreibt die strukturierten Debug-Angaben in die Logdatei
     */
    private function logToSandboxFile($timestamp, $status, $original, $modified, $error) {
        // Sicherstellen, dass das Log-Verzeichnis existiert
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $logEntry = sprintf(
            "[%s] STATUS: %s\n",
            $timestamp,
            $status
        );
        
        if ($status !== 'Ausführbar') {
            $logEntry .= sprintf("⚠️ DB-FEHLER: %s\n", $error);
        }
        
        $logEntry .= sprintf("🔹 ORIGINAL: %s\n", trim($original));
        $logEntry .= sprintf("🔸 MODIFIED: %s\n", trim($modified));
        $logEntry .= str_repeat("-", 80) . "\n";

        @file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    private function isMultistoreTableAffected($lowerQuery) {
        $criticalTables = [
            defined('TABLE_PRODUCTS') ? TABLE_PRODUCTS : 'products',
            defined('TABLE_CATEGORIES') ? TABLE_CATEGORIES : 'categories',
            defined('TABLE_CONTENT_MANAGER') ? TABLE_CONTENT_MANAGER : 'content_manager',
            defined('TABLE_ORDERS') ? TABLE_ORDERS : 'orders',
            defined('TABLE_COUNTRIES') ? TABLE_COUNTRIES : 'countries',
            defined('TABLE_CUSTOMERS_BASKET') ? TABLE_CUSTOMERS_BASKET : 'customers_basket'
        ];

        foreach ($criticalTables as $table) {
            if (strpos($lowerQuery, $table) !== false) {
                return true;
            }
        }
        return false;
    }

    private function normalizeCoreAliases($query) {
        $replacements = [
            TABLE_PRODUCTS . ' as p' => TABLE_PRODUCTS . ' p',
            TABLE_PRODUCTS_TO_CATEGORIES . ' as p2c' => TABLE_PRODUCTS_TO_CATEGORIES . ' p2c',
            TABLE_PRODUCTS_DESCRIPTION . ' as pd' => TABLE_PRODUCTS_DESCRIPTION . ' pd',
            TABLE_CATEGORIES . ' as c' => TABLE_CATEGORIES . ' c',
            TABLE_SPECIALS . ' as s' => TABLE_SPECIALS . ' s'
        ];
        return str_ireplace(array_keys($replacements), array_values($replacements), $query);
    }

    /**
     * Injiziert Multistore-Bedingungen unter strikter Berücksichtigung von Subqueries und Join-Klammern
     */
    private function injectMultistoreFilters($query) {
        // Speicher für maskierte Blöcke initialisieren
        $this->maskedBlocks = [];

        // Falls keine Klammern existieren, direkt verarbeiten
        if (strpos($query, '(') === false) {
            return $this->injectFiltersToSingleScope($query);
        }

        $executionCount = 0;
        $maxLoops = 15; // Erhöht für tiefer verschachtelte Queries

        // STRATEGIE: Wir arbeiten uns von den innersten Klammern nach außen vor
        while (preg_match('/\(([^()]+)\)/i', $query, $matches, PREG_OFFSET_CAPTURE) && $executionCount < $maxLoops) {
            $executionCount++;
            
            $innerSql = $matches[1][0];
            $startOffset = $matches[0][1]; // Exakter Startpunkt inkl. der Klammer (
            $length = strlen($matches[0][0]); // Gesamtlänge inkl. Klammern ()

            // Handelt es sich um eine echte Subquery (enthält SELECT)?
            if (preg_match('/\bselect\b/i', $innerSql)) {
                $processedInnerSql = $this->injectFiltersToSingleScope($innerSql);
                // Subquery verarbeitet zurückschreiben (Klammern bleiben erhalten)
                $query = substr_replace($query, '(' . $processedInnerSql . ')', $startOffset, $length);
            } else {
                // Keine Subquery (Join-Priorisierung, IN-Liste, Funktion wie IFNULL)
                // Wir maskieren den Block absolut neutral über eine ID
                $maskPlaceholder = '___MASKED_BLOCK_' . md5($innerSql . $startOffset) . '___';
                $this->maskedBlocks[$maskPlaceholder] = $innerSql;
                
                // Wir ersetzen den Block im String temporär durch den Platzhalter (OHNE Klammern, um die Schleife zu füttern)
                $query = substr_replace($query, $maskPlaceholder, $startOffset, $length);
            }
        }

        // Jetzt verarbeiten wir den verbliebenen, äußeren Haupt-Scope
        $query = $this->injectFiltersToSingleScope($query);

        // 💡 DIE WICHTIGE KORREKTUR: Demaskierung stellt den Originalzustand 1:1 wieder her
        if (!empty($this->maskedBlocks)) {
            // Wir müssen die Ersetzung ggf. mehrmals durchlaufen, falls Platzhalter verschachtelt wurden
            $demaskLoop = 0;
            while (strpos($query, '___MASKED_BLOCK_') !== false && $demaskLoop < 10) {
                $demaskLoop++;
                foreach ($this->maskedBlocks as $placeholder => $originalInner) {
                    // Wir setzen den Inhalt exakt wieder in seine originalen runden Klammern ()
                    $query = str_replace($placeholder, '(' . $originalInner . ')', $query);
                }
            }
            $this->maskedBlocks = []; // Speicher leeren
        }

        return $query;
    }

    /**
     * Hilfsfunktion: Filtert isoliert exakt EINE Query-Ebene (Scope)
     */
    private function injectFiltersToSingleScope($query) {
        $lowerQuery = strtolower($query);
        
        // Ignoriere den Block komplett, wenn er nur noch aus einem Maskierungs-String besteht
        if (preg_match('/^___masked_block_[a-z0-9]+___$/i', trim($query))) {
            return $query;
        }

        $pDescriptionFilter = defined('MULTISTORE_SQL_PDESCRIPTION') ? MULTISTORE_SQL_PDESCRIPTION : '';
        $injectionPointPattern = '/\s+(group\s+by|order\s+by|limit)\b/i';

        // FROM-Block dieser Ebene isolieren (beachtet auch maskierte Platzhalter)
        $fromBlock = '';
        if (preg_match('/(?:\bfrom\b\s+)(.*?)(?=\bwhere\b|\bgroup\s+by\b|\border\s+by\b|\blimit\b|$)/i', $query, $fromMatches)) {
            $fromBlock = $fromMatches[1];
        } else {
            if (preg_match('/(?:\bfrom\b\s+)(.*)/i', $query, $fromMatches)) {
                $fromBlock = $fromMatches[1];
            }
        }

        // Falls wir im FROM-Block Platzhalter haben, demaskieren wir diesen temporär NUR für die Tabellen-Prüfung
        $checkFromBlock = $fromBlock;
        if (!empty($this->maskedBlocks)) {
            foreach ($this->maskedBlocks as $placeholder => $originalInner) {
                $checkFromBlock = str_replace($placeholder, $originalInner, $checkFromBlock);
            }
        }

        if (empty($checkFromBlock)) {
            return $query;
        }

        // --- A. KATEGORIEN-FILTER ---
        $tableCategoriesName = defined('TABLE_CATEGORIES') ? TABLE_CATEGORIES : 'categories';
        if (preg_match('/\b' . preg_quote($tableCategoriesName, '/') . '\b/i', $checkFromBlock)) {
            
            // 💡 PRÜFUNG: Wenn der Multistore-Filter bereits in der Query existiert, überspringen!
            // Das verhindert doppelte Injektionen bei Core-Queries, die den Filter schon mitbringen.
            if (strpos($lowerQuery, 'c.string_domains') !== false || strpos($lowerQuery, $tableCategoriesName . '.string_domains') !== false) {
                return $query; 
            }

            $baseFilter = defined('MULTISTORE_SQL_SEARCH_WHERE2a') ? MULTISTORE_SQL_SEARCH_WHERE2a : '';
            $aliasPattern = '/\b' . preg_quote($tableCategoriesName, '/') . '\s+(?:as\s+)?([a-z0-9_]+)\b/i';
            
            if (preg_match($aliasPattern, $checkFromBlock, $matches)) {
                $forbiddenKeywords = ['join', 'on', 'left', 'right', 'inner', 'straight_join', 'where'];
                if (in_array(strtolower($matches[1]), $forbiddenKeywords) || strpos($checkFromBlock, ',' . $matches[1]) !== false) {
                    $actualAlias = $tableCategoriesName;
                } else {
                    $actualAlias = $matches[1];
                }
            } else {
                $actualAlias = $tableCategoriesName;
            }

            $dynamicFilter = str_replace('c.string_domains', $actualAlias . '.string_domains', $baseFilter);

            if (!empty($dynamicFilter)) {
                if (strpos($lowerQuery, 'select distinct') === false && strpos($lowerQuery, 'select ') === 0) {
                    $query = preg_replace('/^select /i', 'SELECT DISTINCT ', $query);
                }

                if (strpos($lowerQuery, ' where ') !== false) {
                    $query = preg_replace('/ where /i', ' WHERE 1=1 ' . $dynamicFilter . ' AND ', $query);
                } else {
                    if (preg_match($injectionPointPattern, $query, $matches, PREG_OFFSET_CAPTURE)) {
                        $offset = $matches[0][1];
                        $query = substr($query, 0, $offset) . ' WHERE 1=1 ' . $dynamicFilter . ' ' . substr($query, $offset);
                    } else {
                        $query .= ' WHERE 1=1 ' . $dynamicFilter;
                    }
                }
            }
        }

        $lowerQuery = strtolower($query);

        // --- B. PRODUKT-BESCHREIBUNGEN-FILTER ---
        $tablePdName = defined('TABLE_PRODUCTS_DESCRIPTION') ? TABLE_PRODUCTS_DESCRIPTION : 'products_description';
        if (preg_match('/\b' . preg_quote($tablePdName, '/') . '\b/i', $checkFromBlock) && !empty($pDescriptionFilter)) {
            
            if (strpos($lowerQuery, 'pd.products_name') !== false || strpos($lowerQuery, $tablePdName . '.products_name') !== false) {
                return $query;
            }

            $pdAliasPattern = '/\b' . preg_quote($tablePdName, '/') . '\s+(?:as\s+)?([a-z0-9_]+)\b/i';
            
            if (preg_match($pdAliasPattern, $checkFromBlock, $pdMatches)) {
                $forbiddenKeywords = ['join', 'on', 'left', 'right', 'inner', 'where'];
                if (in_array(strtolower($pdMatches[1]), $forbiddenKeywords)) {
                    $actualPdAlias = $tablePdName;
                } else {
                    $actualPdAlias = $pdMatches[1];
                }
            } else {
                $actualPdAlias = $tablePdName;
            }

            $dynamicPdFilter = str_replace('pd.', $actualPdAlias . '.', $pDescriptionFilter);

            if (strpos($lowerQuery, ' where ') !== false) {
                $query = preg_replace('/ where /i', ' WHERE 1=1 ' . $dynamicPdFilter . ' AND ', $query);
            } else {
                if (preg_match($injectionPointPattern, $query, $matches, PREG_OFFSET_CAPTURE)) {
                    $offset = $matches[0][1];
                    $query = substr($query, 0, $offset) . ' WHERE 1=1 ' . $dynamicPdFilter . ' ' . substr($query, $offset);
                } else {
                    $query .= ' WHERE 1=1 ' . $dynamicPdFilter;
                }
            }
        }

        return $query;
    }
    

    
}
 

/**
 * Globaler Wrapper (ms_db_query)
 */
function ms_db_query($query) {
    global $called_by_admin;

    $domainId = defined('ID_DOMAIN') ? ID_DOMAIN : 1;
    
    $isAdminMode = (
        $called_by_admin || 
        defined('RUN_MODE_ADMIN') || 
        ($_SESSION['sql_ms_through'] ?? false) ||
        (defined('FILENAME_BINGFEED') && strpos($_SERVER['PHP_SELF'], FILENAME_BINGFEED) !== false)
    );

    if ($isAdminMode) {
        $_SESSION['sql_ms_through'] = false;
        return $query;
    }

    // 💡 HIER SCHALTEN SIE DEN SANDBOX-MODUS EIN/AUS
    // true = Sandbox aktiv (kein Risiko im Livesystem, loggt nur Modifikationen)
    // false = Produktiv (neues Parsing ist aktiv)
    $sandboxActive = true; 

    $parser = new MultistoreQueryParser($domainId, $isAdminMode, $sandboxActive);
    return $parser->parse($query);
}
