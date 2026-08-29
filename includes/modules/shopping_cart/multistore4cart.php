<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class multistore4cart {  //Important same name as filename
     
    //--- BEGIN DEFAULT CLASS METHODS ---//
    function __construct()
    {                  
        $this->code = 'multistore4cart'; //Important same name as class name
        $this->title = 'MultistoreForShoppingCart *';
        $this->description = 'GetMultistoreCartInfo';        
        $this->name = 'MODULE_SHOPPING_CART_'.strtoupper($this->code);
        $this->enabled = defined($this->name.'_STATUS') && constant($this->name.'_STATUS') == 'true' ? true : false;
        $this->sort_order = defined($this->name.'_SORT_ORDER') ? constant($this->name.'_SORT_ORDER') : '';
        $this->contentsTemp = array();
        $this->translate();
    }
    
    function translate() {
        switch ($_SESSION['language_code']) {
          case 'de':
            $this->description = 'Multistore: Erfassung von shopspezifischen Warenkörben.<br />*Die Verwendung ist für den Multistore Betrieb Voraussetzung.<br />*Die De-Installation ist an den Multistore-Modus gekoppelt unter: Erweiterte Konfiguration - Zusatzmodule.';
            $this->title = 'Multistore: shopspezifische Bestellinfos';
       
            break;
          default:
            $this->title = 'Multistore: Shopping Cart';
            $this->description = '';
            break;
        }
    }
    
    function check() {
        if (!isset($this->_check)) {
          $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = '".$this->name."_STATUS'");
          $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }
    
    function keys() {
        define($this->name.'_STATUS_TITLE', TEXT_DEFAULT_STATUS_TITLE);
        define($this->name.'_STATUS_DESC', TEXT_DEFAULT_STATUS_DESC);
        define($this->name.'_SORT_ORDER_TITLE', TEXT_DEFAULT_SORT_ORDER_TITLE);
        define($this->name.'_SORT_ORDER_DESC', TEXT_DEFAULT_SORT_ORDER_DESC);
        
        return array(
            $this->name.'_STATUS', 
            $this->name.'_SORT_ORDER'
        );
    }

    function install() {
        # DeInstallatuion erfolgt mit dem Multistore-Modus unter: Erweiterte Konfiguration - Zusatzmodule
         xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('".$this->name."_STATUS', 'true','6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
         xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('".$this->name."_SORT_ORDER', '10','6', '2', now())");
    }

    function remove() {
        # DeInstallatuion erfolgt mit dem Multistore-Modus unter: Erweiterte Konfiguration - Zusatzmodule
        # xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE '".$this->name."_%'");
    }
    
    
    //--- BEGIN CUSTOM  CLASS METHODS ---//
    
     function restore_contents_products_db($sql_data_array, $products_id, $table_basket, $qty, $type){
      if(defined("MULTISTORE") &&  MULTISTORE=='true'){
    		$sql_data_array['id_domain']=ID_DOMAIN;
    		$sql_data_array['sid']=session_id();       
      }        
      return $sql_data_array;
     }
     
     function add_cart_products_db($sql_data_array){  
      if(defined("MULTISTORE") &&  MULTISTORE=='true'){
    		$sql_data_array['id_domain']=ID_DOMAIN;
    		$sql_data_array['sid']=session_id();
      }    
      return $sql_data_array;
     }    
     
     function restore_contents_attributes_db($sql_data_array, $products_id, $value, $type){       
      if(defined("MULTISTORE") &&  MULTISTORE=='true'){
    		$sql_data_array['id_domain']=ID_DOMAIN;
      }             
      return $sql_data_array;
     }             
     
     function add_cart_attributes_db($sql_data_array){       
      if(defined("MULTISTORE") &&  MULTISTORE=='true'){
    		$sql_data_array['id_domain']=ID_DOMAIN;
      }           
      return $sql_data_array;
     }
     
     function restore_contents_products_session($products,$table_basket,$type){
      if(defined("MULTISTORE") &&  MULTISTORE=='true' && MS_MULTIBASKET=='true' && !empty($products['id_domain'])){   
    	  $_SESSION['cart']->contents[$products['products_id']]['id_domain'] = $products['id_domain'];        
      }     
     }       
     
     function add_cart_products_session($products_id,$type){  
      if(defined("MULTISTORE") &&  MULTISTORE=='true' && isset($_SESSION['cart']->contents[$products_id])){   
    	  $_SESSION['cart']->contents[$products_id]['id_domain'] = $_SESSION['cart']->contents[$products_id]['id_domain']>0?$_SESSION['cart']->contents[$products_id]['id_domain']:ID_DOMAIN;        
      } 
     }     
     function get_products($products_data, $product, $contents){
            require_once(DIR_FS_INC . 'xtc_get_products_name.inc.php');
            # MODULE MULTISTORE
            if(defined("MULTISTORE") &&  MULTISTORE=='true' && MS_MULTIBASKET=='true'){
						  if($contents['id_domain'] != ID_DOMAIN)
                $products_data['name'] = xtc_get_products_name($products_data['id'], '', $contents['id_domain']);
              $products_data['http'] = ms_get_string_http($products_data['id']);
            }
            return $products_data;
     }


}