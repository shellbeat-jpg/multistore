<?php

defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

if(MULTISTORE == 'true'){  
  switch ($_SESSION['language_code']) {
    case 'de':
      define('MENU_NAME_EXAMPLE_MAIN','Multistore');
      define('MENU_NAME_EXAMPLE_SUB1','Domainmanager');
      define('MENU_NAME_EXAMPLE_SUB2','Domainkonfigurator');
      break;
    default:
      define('MENU_NAME_EXAMPLE_MAIN','Multistore');
      define('MENU_NAME_EXAMPLE_SUB1','Domainmanager');
      define('MENU_NAME_EXAMPLE_SUB2','Domainkonfigurator');
      break;
  }   
  
  $add_contents[BOX_HEADING_TOOLS][MENU_NAME_EXAMPLE_MAIN][] = array(
      'admin_access_name' => 'domain_manager',    
      'boxname' => MENU_NAME_EXAMPLE_MAIN,                     
      'has_subs' => 1                    
    );
  
  
  $add_contents[BOX_HEADING_TOOLS][MENU_NAME_EXAMPLE_MAIN][] = array(
      'admin_access_name' => 'domain_manager',
      'boxname' => MENU_NAME_EXAMPLE_SUB1
    );
  
  
  $add_contents[BOX_HEADING_TOOLS][MENU_NAME_EXAMPLE_MAIN][] = array(
      'admin_access_name' => 'domain_konfigurator',
      'boxname' => MENU_NAME_EXAMPLE_SUB2
    );
  
}