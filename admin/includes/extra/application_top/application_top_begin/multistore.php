<?php    
# MUDULE MULTISTORE
define('MULTISTORE', defined('MULTISTORE') ? MULTISTORE : false);


# MODULE MULTISTORE
if(defined("MULTISTORE") &&  MULTISTORE=='true'){     
  define('FILENAME_DOMAIN_MANAGER', 'domain_manager.php');
  define('FILENAME_DOMAIN_KONFIGURATOR', 'domain_konfigurator.php');
  define('DIR_WS_MULTISTORE', DIR_FS_CATALOG . 'includes/extra/multistore/');
  
  $called_by_admin = true;

  require_once (DIR_WS_MULTISTORE.'multistore_get_domains.inc.php');
  require_once (DIR_WS_MULTISTORE.'multistore_db_query.inc.php');

  /*
	$arrMsPreconfig[] = array();
	$arrMsPreconfig[] = FILENAME_ORDERS;
  $arrMsPreconfig[] = FILENAME_ORDERS_EDIT;
  $arrMsPreconfig[] = FILENAME_PRINT_PACKINGSLIP;
  $arrMsPreconfig[] = FILENAME_PRINT_ORDER;

  # temporäre Deklaration aller originalen Shop URLs und Pfade der Bestellung
  # Order Klasse wird in getPreconfig() deklariert
  if(isset($_GET['oID'])) 
     getPreconfig();   
  */  
 
  # neuen Kunden speichern
	if(basename($_SERVER['SCRIPT_NAME']) == FILENAME_CREATE_ACCOUNT && isset($_GET['action']) && $_GET['action'] == 'edit') {
  	ms_initConstants($_POST['id_domain'], $_POST['languages_id'], "source = '12'");
    $CURRENT_TEMPLATE = xtc_get_template_by_domain ($_POST['id_domain'], xtc_get_domains());
	  $HTTP_SERVER =  xtc_get_domain_server($_POST['id_domain'], (ENABLE_SSL_CATALOG == 'true'?'https':'http'));
  }
  # Google Sitemap speichern
  if(basename($_SERVER['SCRIPT_NAME']) == FILENAME_MODULE_EXPORT && isset($_POST['id_domain'])) {
    ms_initConstants($_POST['id_domain'], 2, "source = '16'");      
  } 
  
  xtc_db_data_seek($configuration_query, 0);
  while ($configuration = xtc_db_fetch_array($configuration_query)) {                 
    // EOF - Tomcraft - 2009-10-03 - Paypal Express Modul (Cache im Admin AUS!)
    if ($configuration['cfgKey'] != 'STORE_DB_TRANSACTIONS' && ($configuration['cfgKey'] != 'CURRENT_TEMPLATE' || $configuration['cfgValue'] != '')) {
      define($configuration['cfgKey'], $configuration['cfgValue']);
    }
  }      
  require_once (DIR_WS_MULTISTORE.'multistore_config.php');
	if(!@constant('CURRENT_TEMPLATE')){
		$arrDomains=xtc_get_domains();
	  define('CURRENT_TEMPLATE', $arrDomains[0]['template']);
	} 
	
  $category_root = 0;
  if (strlen($_GET['cPath']) > 0) {
    $cPath_array = xtc_parse_category_path($_GET['cPath']);
    $category_root = $cPath_array[0];
  } elseif(isset($_GET['cID'])){
    $category_root = $_GET['cID'];
  }



  if(!is_array($languages))
    $languages = xtc_get_languages();          
}  
?>