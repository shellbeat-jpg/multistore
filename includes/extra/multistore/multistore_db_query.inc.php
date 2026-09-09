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
     * Injiziert Multistore-Bedingungen unter strikter Berücksichtigung von FROM/JOIN-Klauseln und Wortgrenzen
     */
    private function injectMultistoreFilters($query) {
        $lowerQuery = strtolower($query);
        $pDescriptionFilter = defined('MULTISTORE_SQL_PDESCRIPTION') ? MULTISTORE_SQL_PDESCRIPTION : '';
        $injectionPointPattern = '/\s+(group\s+by|order\s+by|limit)\b/i';
        
        // --- 1. PRÄZISE PRÜFUNG & ALIAS-ERKENNUNG FÜR KATEGORIEN ---
        // Der Regex stellt sicher: Es MUSS ein 'FROM' oder 'JOIN' vor dem Tabellennamen stehen.
        // \b verhindert das Match von 'categories_id' oder 'categories_description'.
        $tableCategoriesName = defined('TABLE_CATEGORIES') ? TABLE_CATEGORIES : 'categories';
        $fromJoinPattern = '/\b(from|join)\s+' . preg_quote($tableCategoriesName, '/') . '\b/i';

        if (preg_match($fromJoinPattern, $query)) {
            
            $baseFilter = defined('MULTISTORE_SQL_SEARCH_WHERE2a') ? MULTISTORE_SQL_SEARCH_WHERE2a : '';
            
            // Holt den exakten Alias direkt hinter dem echten Tabellennamen (z.B. "FROM categories c" oder "FROM categories AS cat")
            $aliasPattern = '/\b' . preg_quote($tableCategoriesName, '/') . '\s+(?:as\s+)?([a-z0-9_]+)\b/i';
            
            if (preg_match($aliasPattern, $query, $matches)) {
                // Falls das nächste Wort ein SQL-Schlüsselwort wie WHERE, JOIN, ON ist, gibt es keinen Alias
                $forbiddenKeywords = ['where', 'join', 'on', 'group', 'order', 'limit', 'left', 'right', 'inner'];
                if (in_array(strtolower($matches[1]), $forbiddenKeywords)) {
                    $actualAlias = $tableCategoriesName;
                } else {
                    $actualAlias = $matches[1]; // Gefundener Alias (z.B. "c")
                }
            } else {
                $actualAlias = $tableCategoriesName; // Kein Alias definiert
            }

            // Wir passen den Filter dynamisch an den isolierten Alias an
            $dynamicFilter = str_replace('c.string_domains', $actualAlias . '.string_domains', $baseFilter);

            if (!empty($dynamicFilter)) {
                if (strpos($lowerQuery, 'select distinct') === false) {
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

        // --- 2. PRÄZISE PRÜFUNG & ALIAS-ERKENNUNG FÜR PRODUKT-BESCHREIBUNGEN ---
        $tablePdName = defined('TABLE_PRODUCTS_DESCRIPTION') ? TABLE_PRODUCTS_DESCRIPTION : 'products_description';
        $pdFromJoinPattern = '/\b(from|join)\s+' . preg_quote($tablePdName, '/') . '\b/i';

        if (preg_match($pdFromJoinPattern, $query) && !empty($pDescriptionFilter)) {
            
            $pdAliasPattern = '/\b' . preg_quote($tablePdName, '/') . '\s+(?:as\s+)?([a-z0-9_]+)\b/i';
            
            if (preg_match($pdAliasPattern, $query, $pdMatches)) {
                $forbiddenKeywords = ['where', 'join', 'on', 'group', 'order', 'limit', 'left', 'right', 'inner'];
                if (in_array(strtolower($pdMatches[1]), $forbiddenKeywords)) {
                    $actualPdAlias = $tablePdName;
                } else {
                    $actualPdAlias = $pdMatches[1]; // Meistens "pd"
                }
            } else {
                $actualPdAlias = $tablePdName;
            }

            // Ersetzt das standardmäßige "pd." durch den dynamisch ermittelten Alias
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
