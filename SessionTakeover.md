# Projekt-Kontext: Refactoring Multistore-Erweiterung (modified eCommerce)

### 📊 System-Umgebung
- **Basis-Shopsystem:** modified eCommerce (Version 3.3.0-r16825)
- **Erweiterung:** Eigenentwickeltes Multistore-System zur Verwaltung mehrerer Shops über eine gemeinsame Datenbank und ein gemeinsames Backend.
- **Zentraler Hebel:** Die Core-Funktion `xtc_db_query()` wurde erweitert und inkludiert die Datei `includes/extra/multistore/multistore_db_query.inc.php`. Diese fängt SQL-Abfragen ab und manipuliert (parst) sie zur Laufzeit, um Store-spezifische Filter (z.B. für Kategorien und Produkte) zu injizieren.
- **Repository:** https://github.com/shellbeat-jpg/multistore.git

---

### ⚙️ Aktueller Entwicklungsstand & Architektur-Refactoring
Das ursprüngliche prozedurale und rein textbasierte Suchen-und-Ersetzen (`str_replace`/`preg_replace`) wurde in eine objektorientierte Struktur (`MultistoreQueryParser`) überführt.

#### Kernkomponenten des neuen Parsers:
1. **Objektorientierung & Kapselung:** Die Logik ist in der Klasse `MultistoreQueryParser` gebündelt. Der Einstieg erfolgt abwärtskompatibel über die globale Funktion `ms_db_query($query)`.
2. **Dynamische Core-Konstanten:** Statt hartcodierter Strings nutzt das Parsing die nativen Tabellenkonstanten des modified-Cores (z.B. `TABLE_CATEGORIES`, `TABLE_PRODUCTS_DESCRIPTION`), wodurch das System kompatibel mit Tabellen-Präfixen bleibt.
3. **Rekursives Klammer-Parsing (Subquery-Isolierung):** Um fehlerhafte Injektionen in verschachtelten Queries zu verhindern, zerlegt der Parser SQL-Statements von innen nach außen in ihre logischen Klammer-Ebenen. Multistore-Filter werden präzise in dem Scope injiziert, in dem die jeweilige Tabelle deklariert ist.
4. **Präzise Alias- & Join-Erkennung:** Der Parser extrahiert den `FROM`/`JOIN`-Block jeder Ebene. Er erkennt sowohl explizite Joins als auch implizite Joins (Komma-Syntax / Implicit Joins) und liest den tatsächlich verwendeten Tabellen-Alias dynamisch aus, um Filter (wie `alias.string_domains`) korrekt anzupassen.
5. **Sandbox- & Debug-Modus:** Ein integrierter Testmodus validiert modifizierte Queries mittels `EXPLAIN` über die native `xtc_db_query()`-Funktion vorab auf Ausführbarkeit. Ein Endlosschleifen-Schutz wird über das Session-Flag `$_SESSION['sql_ms_through']` gesteuert. Ergebnisse werden strukturiert in `log/multistore_sandbox.log` protokolliert.

---

### 🔍 Offene Punkte & Nächste Schritte
- **Validierung komplexer Query-Strukturen:** Fortlaufende Prüfung des Sandbox-Logs auf spezifische SQL-Syntaxfehler bei unkonventionellen Fremdmodul-Abfragen.
- **Sicherheits-Audit Session-Management:** Absicherung des Multistore-Bypass-Flags (`$_SESSION['sql_ms_through']`) gegen unbefugte Manipulationen und Gewährleistung einer strikten Isolation von Session-Cookies (`XTCsid`) über die verschiedenen Store-Domains hinweg, um store-übergreifende Datenzugriffe auszuschließen.
