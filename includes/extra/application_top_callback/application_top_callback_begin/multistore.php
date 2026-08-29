<?php

  if(!isset($_SESSION['language'])) $_SESSION['language'] = 2;
     
# 1. Aufruf oben in \includes\application_top.php
#if(!isset($template)){
  define('DIR_WS_MULTISTORE', DIR_FS_CATALOG . 'includes/extra/multistore/');
  
  if(defined("MULTISTORE") &&  MULTISTORE=='true'){   
  	require_once (DIR_WS_MULTISTORE.'multistore_get_domains.inc.php');
  	require_once (DIR_WS_MULTISTORE.'multistore_db_query.inc.php');
  	# true: Rüchgabe des Shop-Templates | false: Deklaration des Shop-Templates als CURRENT_TEMPLATE
  	$template = initMultistore(true);	 
    xtc_db_data_seek($configuration_query, 0);
                           
  	while ($configuration = xtc_db_fetch_array($configuration_query)) {
      define($configuration['configuration_key'].'_BAK', $configuration['cfgValue']);
      if(checkSessionKey($configuration['configuration_key']) && $configuration['configuration_key']!="CURRENT_TEMPLATE") {
        if($configuration['configuration_key'] == 'ENABLE_SSL' || $configuration['configuration_key'] == 'USE_SSL_PROXY')
           define($configuration['configuration_key'], $configuration['configuration_value']=='true'); 
        define($configuration['configuration_key'], stripslashes($configuration['configuration_value']));    
      }
  	}
  } 
  # auch falls Multistore-Modus inaktiv ist werden leere Konstanten benötigt 
  require_once (DIR_WS_MULTISTORE.'multistore_config.php');
     
#} else {
  
  # 2. Aufruf unten in \includes\application_top.php
  if(defined("MULTISTORE") &&  MULTISTORE=='true'){
  	ms_initConstants();
    if(file_exists(('templates/'.CURRENT_TEMPLATE.'/lang/'.$_SESSION['language'].'/'.$_SESSION['language'].'.custom.php')))
  	require (DIR_FS_CATALOG .'templates/'.CURRENT_TEMPLATE.'/lang/'.$_SESSION['language'].'/'.$_SESSION['language'].'.custom.php');	
    if (file_exists('includes/local/template.php'))
    	include ('includes/local/template.php');
    # zuvor nicht gesetzt mitSession-Template (z.B. mobile Version) ? => Original-Template
    define("CURRENT_TEMPLATE", $template);
    define("DOMAIN_ID", ID_DOMAIN); 
    
    $categories_conditions_c = (MULTISTORE=='true'?MULTISTORE_SQL_C2DESCRIPTION:'');
    $products_conditions_p = (MULTISTORE=='true'?MULTISTORE_SQL_PDESCRIPTION:'');
  }
#}

 
?>