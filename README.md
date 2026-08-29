Shopsystem: Modified ECommerce 
Version: 3.3.0-r16825
Eigenentwicklung: Multistore Erweiterung 

USE-CASE
Verwaltung beliebig vieler Shops über ein gemeinsames Backend.
Hauptfunktion ist die Shopzuordnung von Hauptkategorien.
Technisch erfolgt die Shopzuordnung über das Parsen der Server URL.

Die Multistore Funktionalität ist vor allem zu finden in:
https://github.com/shellbeat-jpg/multistore/tree/main/includes/extra/multistore

Zur Wahrung der Shopspezifischen Zuordnungen ist die Funktion xtc_db_query() erweitert.
Darin wird multistore_db_query.inc.php inkludiert zwecks Parsing aller Mysql Queries.  
Die verwendeten Konstanten liegen in multistore_config.php.

