<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class multistore2categories {  //Important same name as filename
  
    //--- BEGIN DEFAULT CLASS METHODS ---//
    function __construct()
    {
        $this->code = 'multistore2categories'; //Important same name as class name
        $this->title = 'MultistoreForCategories *';
        $this->description = 'SaveCat2Stores';        
        $this->name = 'MODULE_CATEGORIES_'.strtoupper($this->code);
        $this->enabled = defined($this->name.'_STATUS') && constant($this->name.'_STATUS') == 'true' ? true : false;
        $this->sort_order = defined($this->name.'_SORT_ORDER') ? constant($this->name.'_SORT_ORDER') : '';
         
        $this->translate();
    }
    
    function translate() {
        switch ($_SESSION['language_code']) {
          case 'de':
            $this->description = 'Kategorien: Speicherung der gewählten Shopzuordnung.<br />*Die Verwendung ist für den Multistore Betrieb Voraussetzung.<br />*Die De-Installation ist an den Multistore-Modus gekoppelt unter: Erweiterte Konfiguration - Zusatzmodule.';
            break;
          default:
            $this->description = 'Categories: save selected shop relations';
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
        #xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE '".$this->name."_%'");
    }
    
    
    //--- BEGIN CUSTOM  CLASS METHODS ---//
    function insert_product_after($products_data,$products_id) 
    {     
  		if(defined("MULTISTORE") &&  MULTISTORE=='true' && MS_MULTIIMG == 'true') {       		
        for ($img = 0; $img < MO_PICS; $img ++) {     
  		      $mo_img = array ();
            $mo_img['string_domains'] = (count($products_data['mopics_domains'][$img])>0?join(';', $products_data['mopics_domains'][$img]):'');
            if(substr($mo_img['string_domains'], 0, 1)==';')
              $mo_img['string_domains'] = substr($mo_img['string_domains'], 1, strlen($mo_img['string_domains']));               
            xtc_db_perform(TABLE_PRODUCTS_IMAGES, $mo_img, 'update', "products_id = '".$products_id."' AND image_nr = " . ($img +1));
        }     
      } 
      
    }
    
    function insert_category_after($categories_data,$categories_id) 
    {     
  		if(defined("MULTISTORE") &&  MULTISTORE=='true')  {
        global $category_root, $current_category_id;  
	      $sql_data_array = array ();
        $sql_data_array['string_domains']='';  
 
        if ($current_category_id>0){  # Shopzuordnung übernehmen von übergeordneter Kategorie
           $categories_query = xtc_db_query("SELECT string_domains FROM ".TABLE_CATEGORIES." WHERE categories_id='".$current_category_id."'");
           $categories = xtc_db_fetch_array($categories_query);
           $sql_data_array['string_domains'] = $categories['string_domains'];         
        }else{ # Shopzuordnung auf untergeordnete Kategorien vererben
           $sql_data_array['string_domains'] = join(';', $categories_data['string_domains']); 
           $this->set_categoriesDomain_recursive($categories_id, $sql_data_array['string_domains']); 
        }
        xtc_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', "categories_id = '".$categories_id."'");
      } 
    }
    
    
    function insert_category_before($sql_data_array,$categories_data) 
    {  
      global $current_category_id, $action;

  	  if(defined("MULTISTORE") &&  MULTISTORE=='true' && $action == 'insert_category' && $current_category_id > 0){  
           $categories_query = xtc_db_query("SELECT string_domains FROM ".TABLE_CATEGORIES." WHERE categories_id='".$current_category_id."'");
           $categories = xtc_db_fetch_array($categories_query);
           $sql_data_array['string_domains'] = $categories['string_domains'];
  		}
      return $sql_data_array;
    }
    
    function set_categoriesDomain_recursive($categories_id, $string_domains, $recursive=false) {
      global $arrAccess;     
      if($recursive || !$_SESSION['superadmin']){
          $string_domains_new = $string_domains;        
      }else{
         # 1. Aufruf = Hauptkategorie - Rechteabgleich
         $categories_query = xtc_db_query("SELECT string_domains FROM ".TABLE_CATEGORIES." WHERE categories_id='".$categories_id."'");
         $categories = xtc_db_fetch_array($categories_query);
         $string_domains_current = $categories['string_domains'];
         $arr_domains_current = explode(";", $string_domains_current);
         $string_domains_new = "";
         for ($p = 0; $p < sizeof($arr_domains_current); $p++) {
              if(!in_array($arr_domains_current[$p], $arrAccess) && $arr_domains_current[$p] != '')
                 $string_domains_new .= $arr_domains_current[$p].";";             
         } 
         if($string_domains!="")   
          $string_domains_new .= $string_domains.";";            
      }   
      #echo $_SESSION['superadmin'] . " - ($string_domains_new != $string_domains) - $categories_id (+$recursive)<br />";
      if($recursive){
        xtc_db_query("UPDATE ".TABLE_CATEGORIES." SET string_domains = '".$string_domains_new."' WHERE categories_id = '".$categories_id."'");
        #echo "($string_domains_new != $string_domains) - UPDATE ".TABLE_CATEGORIES." SET string_domains = '".$string_domains_new."' WHERE categories_id = '".$categories_id."'<br />";
      }   
      $categories_query = xtc_db_query("SELECT categories_id FROM ".TABLE_CATEGORIES." WHERE parent_id='".$categories_id."'");
      while ($categories = xtc_db_fetch_array($categories_query)) {
          $this->set_categoriesDomain_recursive($categories['categories_id'], $string_domains_new, 1);
      }
    }
    

}