<?php
	 # Domainmanager / Domainkonfigurator: Gruppen IDs der Shopeinstellungen (Tabelle configuration) zur separaten Zuschaltung
	 $arrConfigurationGroups = array(1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 21, 22, 25, 31, 24, 40, 111125);

   # Domainmanager: Übernahme der Shop-Zuordnungen
	 $arrTblAdoption = array(TABLE_CATEGORIES, TABLE_CONTENT_MANAGER, TABLE_PRODUCTS_IMAGES);
     $arrKeysAdoption = array();
     $arrKeysAdoption[TABLE_CATEGORIES] = 'categories_id';
     $arrKeysAdoption[TABLE_CONTENT_MANAGER] = 'content_id';
     $arrKeysAdoption[TABLE_PRODUCTS_IMAGES] = 'image_id'; 
     
   # Autoload constants from table domains   
   define('ADD_DOMAIN_FIELDS','current_css,default_currency,default_tax,domain_user,login_strict'); # ,current_css_mobile,logo,logo_email,logo_mobile 

	 if(!defined('MULTISTORE') || MULTISTORE!='true'){
       # Multistoremodus nicht aktiv: Multistore-Konstanten => leere Zeichenfolge
		   define('MULTISTORE_ID_DOMAIN', "");
       define('MULTISTORE_SQL_PDESCRIPTION',  "");
		   define('MULTISTORE_SQL_PSDESCRIPTION',  "");
		   define('MULTISTORE_SQL_P2DESCRIPTION',  "");
		   define('MULTISTORE_SQL_C1DESCRIPTION',  ""); 
		   define('MULTISTORE_SQL_C2DESCRIPTION',  "");
		   define('MULTISTORE_SQL_ADESCRIPTION',  "");
		   define('MULTISTORE_SQL_C3DESCRIPTION',  "");
		   define('MULTISTORE_SQL_CDESCRIPTION',  "");
		   define('MULTISTORE_SQL_SEARCH_JOIN1', "");
			 define('MULTISTORE_SQL_SEARCH_JOIN2', "");
			 define('MULTISTORE_SQL_SEARCH_JOIN3', "");
			 define('MULTISTORE_SQL_SEARCH_JOIN4', "");
			 define('MULTISTORE_SQL_SEARCH_WHERE1ab', "");
			 define('MULTISTORE_SQL_SEARCH_WHERE1a', "");
			 define('MULTISTORE_SQL_SEARCH_WHERE1b', "");
			 define('MULTISTORE_SQL_SEARCH_WHERE2a', "");
			 define('MULTISTORE_SQL_SEARCH_WHERE2b', "");
			 define('MULTISTORE_SQL_SEARCH_WHERE2c', "");
			 define('MULTISTORE_SQL_SEARCH_WHERE2d', "");
			 define('MULTISTORE_SQL_SEARCH_WHERE2da', "");
			 define('MULTISTORE_SQL_SEARCH_WHERE2e', "");
			 define('MULTISTORE_SQL_DOMAIN', "");
			 define('MULTISTORE_SQL_JOIN', "");
			 define('MULTISTORE_SQL_PRODUCTS_WHERE', "");
			 define('MULTISTORE_SQL_ORDERS_WHERE', "");
			 define('MULTISTORE_SQL_WHERE', "");
			 define('MULTISTORE_SQL_MANUFACTURERS', "");
			 define('MULTISTORE_SQL_CONTENT', "");
			 define('MULTISTORE_SQL_BESTSELLERS', "");
			 define('MULTISTORE_SQL_CONCAT', "");
			 define('MULTISTORE_SQL_COUNT', "");
			 define('MULTISTORE_SQL_COUNTRIES', "");
			 define('MULTISTORE_SQL_D2DESCRIPTION', "");
       define('MULTISTORE_SQL_CONTENT_NOSTRICT', "");
       define('MULTISTORE_SQL_LPM', "");
	 } else {
     # Erstaufruf
     if(!defined('ID_DOMAIN')) define('ID_DOMAIN', 1);
     # Multistoremodus aktiv
	   if(defined('MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS') &&
        MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS=='true'){
			 # Shopspezifische Angabe für Artikel- und Kategoriebeschreibungen aktiv: Filter nach Beschreibung
		   define('MULTISTORE_SQL_ADESCRIPTION',  " and domain_id = '%s' and products_name != 'NULL' ");    
       define('MULTISTORE_SQL_CDESCRIPTION',  " and domain_id = '%s' and categories_name != 'NULL' ");
			   define('MULTISTORE_SQL_PSDESCRIPTION',  " and pd.domain_id = '%s' and pd.products_name != 'NULL' ");
		   define('MULTISTORE_SQL_PDESCRIPTION',  " and pd.domain_id = '".ID_DOMAIN."' and pd.products_name != 'NULL' ");
         define('MULTISTORE_SQL_D2DESCRIPTION',  " and domain_id = '".ID_DOMAIN."' and categories_name != 'NULL' ");
			   define('MULTISTORE_SQL_P2DESCRIPTION',  " and domain_id = '".ID_DOMAIN."' and products_name != 'NULL' ");   
			   define('MULTISTORE_SQL_C1DESCRIPTION',  " and cd.domain_id = '".ID_DOMAIN."' and cd.categories_name != 'NULL' and trim(cd.categories_name) != '' group by c.categories_id");
		   define('MULTISTORE_SQL_C2DESCRIPTION',  " and cd.domain_id = '".ID_DOMAIN."' and cd.categories_name != 'NULL' ");
		     define('MULTISTORE_SQL_C3DESCRIPTION',  " and cd.domain_id = '%s' and cd.categories_name != 'NULL' ");
		 }else{
		   define('MULTISTORE_SQL_PDESCRIPTION',  "");
		   define('MULTISTORE_SQL_PSDESCRIPTION',  "");
		   define('MULTISTORE_SQL_P2DESCRIPTION',  "");
		   define('MULTISTORE_SQL_C1DESCRIPTION',  ""); 			 
		   define('MULTISTORE_SQL_C2DESCRIPTION',  "");
		   define('MULTISTORE_SQL_CDESCRIPTION',  "");
		   define('MULTISTORE_SQL_ADESCRIPTION',  "");
		   define('MULTISTORE_SQL_C3DESCRIPTION',  "");
		   define('MULTISTORE_SQL_D2DESCRIPTION', "");
		 }
		                              
		 define('MULTISTORE_ID_DOMAIN', " and id_domain = '".ID_DOMAIN."'");  
		 define('MULTISTORE_ORDERS_ID_DOMAIN', " and o.id_domain = '%s'");
		 # Left Join für Artikel => Artikel-Kategorienzuordnung
	   define('MULTISTORE_SQL_SEARCH_JOIN1', " left outer join ".TABLE_PRODUCTS_TO_CATEGORIES." p2c ON (p.products_id = p2c.products_id) ");
     # Left Join für Artikel-Kategorienzuordnung => Kategorien
		 define('MULTISTORE_SQL_SEARCH_JOIN2', " left join ".TABLE_CATEGORIES." c on c.categories_id = p2c.categories_id ");
     # Left Join für Kategorien => Artikel-Kategorienzuordnung
		   define('MULTISTORE_SQL_SEARCH_JOIN3', " left outer join ".TABLE_PRODUCTS_TO_CATEGORIES." as p2c ON (c.categories_id = p2c.categories_id) ");
     # Left Join für Kategorien <> Artikel-Kategorienzuordnung <> Artikel
		   define('MULTISTORE_SQL_SEARCH_JOIN4', MULTISTORE_SQL_SEARCH_JOIN3 . " left join ".TABLE_PRODUCTS." as p ON (p2c.products_id = p.products_id) ");
		 # Left Join für Artikel => Artikel-Kategorienzuordnung <> Kategorien
		 define('MULTISTORE_SQL_JOIN', " left join ".TABLE_PRODUCTS_TO_CATEGORIES." as p2c ON (p.products_id = p2c.products_id)  left join ".TABLE_CATEGORIES." c on c.categories_id = p2c.categories_id ");

		 # verwendet beim Filter nach Domains in der Artikel- Kategorieübersicht (Backend) - xtc_get_domains()
		 define('MULTISTORE_SQL_SEARCH_WHERE1ab', " and c.parent_id IN ('%s' ");
	   # verwendet bei der Suche innerhalb einer Kategorie (advanced_search_result.php)  - xtc_get_domains()
		 define('MULTISTORE_SQL_SEARCH_WHERE1a', " and p2c.categories_id IN ('%s' ");
     # verwendet bei der Suche innerhalb einer Kategorie (advanced_search_result.php)
		   define('MULTISTORE_SQL_SEARCH_WHERE1b', " and p2c.categories_id = '%s' ");
		 # Filter für Kategorien (Frontend, c.string_domains)
		 define('MULTISTORE_SQL_SEARCH_WHERE2a', " and ((instr(c.string_domains, ';".ID_DOMAIN.";') or right(c.string_domains, ".(strlen(ID_DOMAIN)+1).") = ';".ID_DOMAIN."' or left(c.string_domains, ".(strlen(ID_DOMAIN)+1).") = '".ID_DOMAIN.";' or c.string_domains = '".ID_DOMAIN."' or c.string_domains = '".ID_DOMAIN.";')) ");    #or c.string_domains = ''
     # Filter für Kategorien und Artikel (Frontend, c.string_domains / p.string_domains)
		#   define('MULTISTORE_SQL_SEARCH_WHERE2b', " and ((instr(c.string_domains, ';".ID_DOMAIN.";') or right(c.string_domains, ".(strlen(ID_DOMAIN)+1).") = ';".ID_DOMAIN."' or left(c.string_domains, ".(strlen(ID_DOMAIN)+1).") = '".ID_DOMAIN.";' or c.string_domains = '".ID_DOMAIN."' or c.string_domains = '".ID_DOMAIN.";') or (instr(p.string_domains, ';".ID_DOMAIN."') or instr(p.string_domains, '".ID_DOMAIN.";') or p.string_domains = '".ID_DOMAIN."'))");  # or c.string_domains = ''
     # Filter für Artikel (Frontend, p.string_domains)
		   define('MULTISTORE_SQL_SEARCH_WHERE2c', " and ((instr(p.string_domains, ';".ID_DOMAIN.";') or right(p.string_domains, ".(strlen(ID_DOMAIN)+1).") = ';".ID_DOMAIN."' or left(p.string_domains, ".(strlen(ID_DOMAIN)+1).") = '".ID_DOMAIN.";' or p.string_domains = '".ID_DOMAIN."' or p.string_domains = '".ID_DOMAIN.";')) ");
     # Filter für Kategorien und Artikel (Backend, Bezug auf [concat(c.string_domains, p.string_domains) as string_domains])
		 define('MULTISTORE_SQL_SEARCH_WHERE2d', " ((instr(string_domains, ';%s;') or right(string_domains, %s) = ';%s' or left(string_domains, %s) = '%s;' or string_domains = '%s' or string_domains = '%s;')) ");
		 #define('MULTISTORE_SQL_CONCAT', " concat(c.string_domains, \";\", p.string_domains) as string_domains ");
		 # Filter für Kategorien (Backend)  - sprintf_domain()
		 define('MULTISTORE_SQL_SEARCH_WHERE2da', " ((instr(c.string_domains, ';%s;') or right(c.string_domains, %s) = ';%s' or left(c.string_domains, %s) = '%s;' or c.string_domains = '%s' or c.string_domains = '%s;')) ");
     # Filter für Kategorien und Artikel (Backend) - sprintf_domain()
		   #define('MULTISTORE_SQL_SEARCH_WHERE2e', " and ((instr(c.string_domains, ';%s;') or right(c.string_domains, %s) = ';%s' or left(c.string_domains, %s) = '%s;' or c.string_domains = '%s' or c.string_domains = '%s;'))");
		   define('MULTISTORE_SQL_PRODUCTS_WHERE', MULTISTORE_SQL_SEARCH_WHERE2c);
     #define('MULTISTORE_SQL_WHERE', MULTISTORE_SQL_SEARCH_WHERE2b);
		 # Feldname Kategorien
		   define('MULTISTORE_SQL_DOMAIN', " c.string_domains, ");
		 # Filter für Bestellungen (Frontend)
	   define('MULTISTORE_SQL_ORDERS_WHERE', " and (o.id_domain = '".ID_DOMAIN."' or o.id_domain < 1) ");
     # Filter für Content (Frontend - Kategorien oder Artikel)
		 define('MULTISTORE_SQL_CONTENT', " and ((instr(string_domains, ';".ID_DOMAIN.";') or right(string_domains, ".(strlen(ID_DOMAIN)+1).") = ';".ID_DOMAIN."' or left(string_domains, ".(strlen(ID_DOMAIN)+1).") = '".ID_DOMAIN.";' or string_domains = '".ID_DOMAIN."' or string_domains = '".ID_DOMAIN.";')) "); 
     # Filter für Länderauswahl (Frontend)
     define('MULTISTORE_SQL_COUNTRIES', " where ((string_domains = '' or instr(string_domains, ';".ID_DOMAIN.";') or right(string_domains, ".(strlen(ID_DOMAIN)+1).") = ';".ID_DOMAIN."' or left(string_domains, ".(strlen(ID_DOMAIN)+1).") = '".ID_DOMAIN.";' or string_domains = '".ID_DOMAIN."' or string_domains = '".ID_DOMAIN.";')) "); #or string_domains = ''
		 
     # Filter für Content (Frontend - Attribute)
		 define('MULTISTORE_SQL_CONTENT_PA', " and ((instr(pa.string_domains, ';".ID_DOMAIN.";') or right(pa.string_domains, ".(strlen(ID_DOMAIN)+1).") = ';".ID_DOMAIN."' or left(pa.string_domains, ".(strlen(ID_DOMAIN)+1).") = '".ID_DOMAIN.";' or pa.string_domains = '".ID_DOMAIN."' or pa.string_domains = '".ID_DOMAIN.";')) "); #or string_domains = ''
		 # Filter für Bestsellers (Backend)
		 define('MULTISTORE_SQL_CONTENT_NOSTRICT', " and ((instr(string_domains, ';".ID_DOMAIN.";') or right(string_domains, ".(strlen(ID_DOMAIN)+1).") = ';".ID_DOMAIN."' or left(string_domains, ".(strlen(ID_DOMAIN)+1).") = '".ID_DOMAIN.";' or string_domains = '".ID_DOMAIN."' or string_domains = '".ID_DOMAIN.";') or string_domains = '') ");  
     
     # Login
		 define('MULTISTORE_SQL_LOGIN', "select c.* FROM ".TABLE_CUSTOMERS." c ".(MS_MULTIGROUPS=="true"?" left join ".TABLE_CUSTOMERS_STATUS." cs on c.customers_status = cs.customers_status_id ":"")." WHERE c.customers_email_address = '%s' ".(MS_MULTIGROUPS=="true"?MULTISTORE_SQL_CONTENT_NOSTRICT:"")." and c.account_type = '0'" . (defined('LOGIN_STRICT') && LOGIN_STRICT=='1'?" and id_domain = '".ID_DOMAIN."'":""));

		   define('MULTISTORE_SQL_BESTSELLERS',  "select distinct p.*, pd.products_short_description, pd.products_name from (".TABLE_PRODUCTS." p left join ".TABLE_PRODUCTS_DESCRIPTION." pd on p.products_id = pd.products_id) left join ".TABLE_PRODUCTS_TO_CATEGORIES." p2c on p.products_id = p2c.products_id left join ".TABLE_CATEGORIES." c on p2c.categories_id = c.categories_id  where p.products_status = '1' and c.categories_status = '1' and p.products_ordered > 0   and pd.language_id = '%s' %s %s ".MULTISTORE_SQL_PDESCRIPTION." group by p.products_id order by p.products_ordered desc limit %s");
     # SQL String für Hersteller (Frontend)
	     define('MULTISTORE_SQL_MANUFACTURERS', "select distinct m.manufacturers_id, m.manufacturers_name FROM (".TABLE_PRODUCTS." p left join ".TABLE_MANUFACTURERS." m on p.manufacturers_id = m.manufacturers_id) left join ".TABLE_PRODUCTS_TO_CATEGORIES." p2c on p.products_id = p2c.products_id left join ".TABLE_CATEGORIES." c on p2c.categories_id = c.categories_id WHERE 1=1 ".MULTISTORE_SQL_SEARCH_WHERE2a."ORDER BY m.sort_order, m.manufacturers_name");
     # SQL String für Anzahl Artikel im Shop (Backend)
		 define('MULTISTORE_SQL_COUNT', "select  distinct p.products_id  from (".TABLE_PRODUCTS." p left join ".TABLE_PRODUCTS_DESCRIPTION." pd on p.products_id = pd.products_id) left join ".TABLE_PRODUCTS_TO_CATEGORIES." p2c on p.products_id = p2c.products_id left join ".TABLE_CATEGORIES." c on p2c.categories_id = c.categories_id where  1 = 1 " . MULTISTORE_SQL_SEARCH_WHERE2a . " group by p.products_id");
     # SQL String für Import/Export (Backend)
		 define('MULTISTORE_SQL_EXPORT', "select  distinct p.*  from (".TABLE_PRODUCTS." p left join ".TABLE_PRODUCTS_DESCRIPTION." pd on p.products_id = pd.products_id) left join ".TABLE_PRODUCTS_TO_CATEGORIES." p2c on p.products_id = p2c.products_id left join ".TABLE_CATEGORIES." c on p2c.categories_id = c.categories_id where  1 = 1 %s group by p.products_id");
     # SQL String für Exportmodule (Backend)
		   define('MULTISTORE_SQL_EXPORT_MODULE', "select  distinct p.*, pd.products_name, pd.products_description, m.manufacturers_name  from (".TABLE_PRODUCTS." p  left outer join " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id left join ".TABLE_PRODUCTS_DESCRIPTION." pd on p.products_id = pd.products_id left join " . TABLE_SPECIALS . " s  ON p.products_id = s.products_id) left join ".TABLE_PRODUCTS_TO_CATEGORIES." p2c on p.products_id = p2c.products_id left join ".TABLE_CATEGORIES." c on p2c.categories_id = c.categories_id where p.products_status = 1 and p.products_price > 0 and pd.language_id = '%s' %s ".MULTISTORE_SQL_PSDESCRIPTION." group by p.products_id ORDER BY p.products_date_added DESC, pd.products_name");
     # Filter für LPM Box 
     define('MULTISTORE_SQL_LPM', " and (tn.string_domains = '' or (((instr(tn.string_domains, ';".ID_DOMAIN.";') or right(tn.string_domains, ".(strlen(ID_DOMAIN)+1).") = ';".ID_DOMAIN."' or left(tn.string_domains, ".(strlen(ID_DOMAIN)+1).") = '".ID_DOMAIN.";' or tn.string_domains = '".ID_DOMAIN."' or tn.string_domains = '".ID_DOMAIN.";'))))");
   }


	 /* Domainmanager / Domainkonfigurator
	    Modulkonstanten müssen zur Erfassung den originalen Klassennamen beinhalten
	    Beispiel Verwendung: modules - payment - class paypal <=> MODULE_PAYMENT_PAYPAL_STATUS
	    Beispiel Korrektur:  modules - payment - class sofort_lastschrift <=> MODULE_PAYMENT_SOFORT_LS_STATUS wird geändert in: MODULE_PAYMENT_SOFORT_LASTSCHRIFT_STATUS
	 */
	 $arrModuleConfigReplace=array();
	 /*
   $arrModuleConfigReplace['SOFORT_LS'] = 'sofort_lastschrift';
	 $arrModuleConfigReplace['SOFORT_SL'] = 'sofort_sofortlastschrift';
	 $arrModuleConfigReplace['SOFORT_SR'] = 'sofort_sofortrechnung';
	 $arrModuleConfigReplace['SOFORT_SU'] = 'sofort_sofortueberweisung';
	 $arrModuleConfigReplace['SOFORT_SV'] = 'sofort_sofortvorkasse';
     */

# Artikelbearbeitung => manuelle Exportregelung (\admin\includes\modules\new_product.php)
/*
$arrConfigCheckboxes = array();
$arrConfigCheckboxes[] = array('field' => 'products_export', 'header' => 'f&uuml;r Preissuchmaschine sperren');

$arr_domains_exclude = array();
for ($j = 0; $m = sizeof($arrConfigCheckboxes), $j < $m; $j++) {
    $arr_domains_exclude[] = 'p.' . $arrConfigCheckboxes[$j]['field'] . '_multistore';
}
$sql_domains_exclude = join(',', $arr_domains_exclude);
*/
?>