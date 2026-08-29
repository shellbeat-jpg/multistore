<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class multistore4orders {  //Important same name as filename
  
    //--- BEGIN DEFAULT CLASS METHODS ---//
    function __construct()
    {                  
        $this->code = 'multistore4orders'; //Important same name as class name
        $this->title = 'MultistoreForOrders *';
        $this->description = 'GetMultistoreOrderInfo';        
        $this->name = 'MODULE_ORDER_'.strtoupper($this->code);
        $this->enabled = defined($this->name.'_STATUS') && constant($this->name.'_STATUS') == 'true' ? true : false;
        $this->sort_order = defined($this->name.'_SORT_ORDER') ? constant($this->name.'_SORT_ORDER') : '';
        
        $this->translate();
    }
    
    function translate() {
        switch ($_SESSION['language_code']) {
          case 'de':
            $this->description = 'Bestellungen: Erfassung von shopspezifischen Bestellinfos.<br />*Die Verwendung ist für den Multistore Betrieb Voraussetzung.<br />*Die De-Installation ist an den Multistore-Modus gekoppelt unter: Erweiterte Konfiguration - Zusatzmodule.';
            $this->title = 'Multistore: shopspezifische Bestellinfos';
       
            break;
          default:
            $this->title = 'Orders';
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
    function order_data($order_data,$order_data_values,$oID,$order_lang_id) 
    {         
      global $arrAccess, $order;                                     
      
      if(defined("MULTISTORE") &&  MULTISTORE=='true')   {
        if(defined('RUN_MODE_ADMIN') && isset($arrAccess) && isset($order->info['id_domain']) && !in_array($order->info['id_domain'], $arrAccess))   {
             $this->customer['ID']=0;             
             return;     
        }  
      
        $order_data['order_id'] = ms_build_order_id($order_data);
      
  			if(!isset($order->info['id_domain'])){
          $order_query = xtc_db_query("SELECT *
                                     FROM " . TABLE_ORDERS . "
                                     WHERE orders_id = '" . $oID . "'");
          $order_data = xtc_db_fetch_array($order_query);         
  				$order->info['id_domain']=$order_data['id_domain'];
  				$order->info['id_languages']=$order_data['id_languages'];
  				$order->info['store_name']=$order_data['store_name'];
          $order->info['orders_id_shop']=$order_data['orders_id_shop'];
  			}
      }   
      return $order_data;
    }
    
    function cart_products($products_data, $products_id) 
    {
       return $products_data;
    }

}