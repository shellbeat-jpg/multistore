<?php

  # Zusatzmodul IT-Recht Kanzlei            
  $arrPrefixTitle[6] = array('constant' => 'MODULE_API_IT_RECHT_KANZLEI', 'prefix'=> 'IT-Recht Kanzlei: ');
  @include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/system/it_recht_kanzlei.php');
  
  # Paypal+ Modul 
  @include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/paypal_config.php');
  # Workaround für abweichende Konvention in der Benennung
  define('PAYPAL_CLIENT_ID_LIVE_TITLE', TEXT_PAYPAL_CONFIG_CLIENT_LIVE);
  define('PAYPAL_SECRET_LIVE_TITLE', TEXT_PAYPAL_CONFIG_SECRET_LIVE);
  define('PAYPAL_CLIENT_ID_SANDBOX_TITLE', TEXT_PAYPAL_CONFIG_CLIENT_SANDBOX);
  define('PAYPAL_SECRET_SANDBOX_TITLE', TEXT_PAYPAL_CONFIG_SECRET_SANDBOX);
  define('PAYPAL_MODE_TITLE', TEXT_PAYPAL_CONFIG_MODE);
  
  $arrPrefixTitle['payment_paypalplus'] = array('constant' => 'PAYPAL_MODE', 'prefix'=> '', 'set_function'=> 'xtc_cfg_select_option(array(\'sandbox\', \'live\'),');
         
           
  
?>