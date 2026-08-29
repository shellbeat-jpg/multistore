<?php
# MODULE MULTISTORE

  require_once (DIR_FS_INC . 'xtc_get_shop_conf.inc.php');

	function xtc_cfg_pull_down_currencies_sets($name, $current, $parameters='') {
      $currency_array = array();
			$sql_currencies = xtc_db_query("SELECT code, currencies_id from " . TABLE_CURRENCIES);
   		while($currency = xtc_db_fetch_array($sql_currencies)){
		    $currency_array[] = array ('id' => $currency['code'], 'text' => $currency['code']);
			}
			return xtc_draw_pull_down_menu($name, $currency_array, $current, $parameters);
	}
    
  function xtc_cfg_pull_down_order_nr_list($id, $key = '', $overwrite = false) {
    $name = (($key) ? 'configuration['.$key.']' : 'configuration_value');
    if(MULTISTORE == 'true' && $overwrite != false)
         $name = $key;
    $array = array (array ('id' => '0', 'text' => TEXT_ORDER_NR_ID));
    $array[] = array ('id' => 1, 'text' => TEXT_ORDER_NR_DATE);
    $array[] = array ('id' => 2, 'text' => TEXT_ORDER_NR_SHOP); 
      return xtc_draw_pull_down_menu($name, $array, $id);   
  }  
           
  function xtc_get_order_nr_title($id) {
    if ($id == '0') {
      return TEXT_ORDER_NR_ID;
    } elseif ($id == '1') {
      return TEXT_ORDER_NR_DATE;
    } elseif ($id == '2') {
      return TEXT_ORDER_NR_SHOP;
    }
  }
  
    function set_categories_recursive($categories_id, $string_domains='', $recursive=false) {
      if($recursive){
        # echo "UPDATE ".TABLE_CATEGORIES." SET string_domains = '".$string_domains."' WHERE categories_id = '".$categories_id."'<br />";
        xtc_db_query("UPDATE ".TABLE_CATEGORIES." SET string_domains = '".$string_domains."' WHERE categories_id = '".$categories_id."'");
      } else {
        #echo "  set_categories_recursive($categories_id)<br />";
        # 1. Durchlauf: Zuordnungen von Hauptkategorie erfassen
        $categories_query = xtc_db_query("SELECT string_domains FROM ".TABLE_CATEGORIES." WHERE categories_id='".$categories_id."' AND parent_id = 0");
        $categories = xtc_db_fetch_array($categories_query);
        $string_domains_current = $categories['string_domains'];

        $arr_domains_current = explode(";", $string_domains_current);
        $string_domains = "";
        for ($p = 0; $p < sizeof($arr_domains_current); $p++) {
             if($arr_domains_current[$p] != '')
                $string_domains .= $arr_domains_current[$p].";";             
        }        
      }  
      $categories_query = xtc_db_query("SELECT categories_id FROM ".TABLE_CATEGORIES." WHERE parent_id='".$categories_id."'");
      while ($categories = xtc_db_fetch_array($categories_query)) {
          set_categories_recursive($categories['categories_id'], $string_domains, 1);
      }
    }
  
?>