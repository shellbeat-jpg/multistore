<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class multistore4descriptions {  //Important same name as filename
  
    //--- BEGIN DEFAULT CLASS METHODS ---//
    function __construct()
    {
        $this->code = 'multistore4descriptions'; //Important same name as class name
        $this->title = 'MultistoreForDescriptions*';
        $this->description = 'SaveDescriptions2Stores';        
        $this->name = 'MODULE_CATEGORIES_'.strtoupper($this->code);
        $this->enabled = defined($this->name.'_STATUS') && constant($this->name.'_STATUS') == 'true' ? true : false;
        $this->sort_order = defined($this->name.'_SORT_ORDER') ? constant($this->name.'_SORT_ORDER') : '';
        
        $this->translate();
    }
    
    function translate() {
        switch ($_SESSION['language_code']) {
          case 'de':
            $this->description = 'Kategorien: Verwendung von shopspezifischen Kategorie- und Artikelbeschreibungen:<br>Artikelname, Artikelbeschreibung, Herstellerlink, Kategorie Name, Kategorie Überschrift, Kategorie Beschreibung, Zusatz-Begriffe und Meta-Tags.<br /><br />*Die Verwendung ist optional.<br />*Die De-Installation ist an den Multistore-Modus gekoppelt unter: Erweiterte Konfiguration - Zusatzmodule';
            break;
          default:
            $this->description = 'Categories: save unique category descriptions per shop';
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
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('".$this->name."_STATUS', 'false','6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('".$this->name."_SORT_ORDER', '10','6', '2', now())");
    }

    function remove() {
        # DeInstallatuion erfolgt mit dem Multistore-Modus unter: Erweiterte Konfiguration - Zusatzmodule
        #xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE '".$this->name."_%'");
    }
    
    
    //--- BEGIN CUSTOM  CLASS METHODS ---//
    function insert_product_desc($sql_data_array,$products_data,$products_id,$language_id) 
    { 
      global $action, $arrDomainLang, $arrAccess; 
        /*
      echo "<pre>";
      print_r($arrAccess);
      print_r($arrDomainLang);
      print_r($sql_data_array);
      print_r($products_data);
      echo "</pre>";
      echo "<br /><br /><br />\$products_id: $products_id<br />";
      echo "\$language_id: $language_id<br />";
      exit;
      */
      if(defined("MULTISTORE") &&  MULTISTORE=='true' && MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS=='true')  {
        	for ($dom = 0; $dom < sizeof($arrDomainLang); $dom++) {
    				$id_domain = $arrDomainLang[$dom]['id_domain'];
    				if($id_domain>0){
    				 if($_SESSION['superadmin'] || in_array($id_domain, $arrAccess)){              
        	       #if(!isset($products_data['string_domains_old']) ||  in_array($id_domain, $products_data['string_domains_old'])){
        				  for ($i = 0, $n = sizeof($arrDomainLang[$dom]['id_lang']); $i < $n; $i++) {
        		        if($arrDomainLang[$dom]['id_lang'][$i]==$language_id){
        								# Artikelname leer? im Adminbereich ggf. als 'NULL' anzeigen falls kein anderer Name (Shops/Sprachen) verfügbar ist
        							  if($action == 'insert_product' && empty($products_data['products_name'][$id_domain][$language_id]))
        						  		$products_data['products_name'][$id_domain][$language_id] = 'NULL';                                                                                                                                                                                                                                                                                                                                                                              
        								$sql_data_array = array ('products_name' => xtc_db_prepare_input($products_data['products_name'][$id_domain][$language_id]), 'products_description' => xtc_db_prepare_input($products_data['products_description_'.$id_domain.'_'.$language_id]), 'products_short_description' => xtc_db_prepare_input($products_data['products_short_description_'.$id_domain.'_'.$language_id]), 'products_keywords' => xtc_db_prepare_input($products_data['products_keywords'][$id_domain][$language_id]), 'products_url' => xtc_db_prepare_input($products_data['products_url'][$id_domain][$language_id]), 'products_meta_title' => xtc_db_prepare_input($products_data['products_meta_title'][$id_domain][$language_id]), 'products_meta_description' => xtc_db_prepare_input($products_data['products_meta_description'][$id_domain][$language_id]), 'products_meta_keywords' => xtc_db_prepare_input($products_data['products_meta_keywords'][$id_domain][$language_id]));
        					      if (trim(ADD_PRODUCTS_DESCRIPTION_FIELDS)) {
        					        $sql_data_array = array_merge($sql_data_array, $this->ms_add_data_fields(ADD_PRODUCTS_DESCRIPTION_FIELDS,$products_data,$language_id,$id_domain));
        					      }
        								if ($action == 'insert_product') {
        									$insert_sql_data = array ('products_id' => $products_id, 'domain_id' => $id_domain, 'language_id' => $language_id);
        									$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
        									xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
        								} elseif ($action == 'update_product') {
        					        //BOF - web28 - 2010-07-11 - BUGFIX no entry stored for previous deactivated languages
        					        $product_query = xtc_db_query("SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION."
        					                                               WHERE language_id = '".$language_id."'
        																												 				AND domain_id = '".$id_domain."'
        					                                                 			AND products_id = '".$products_id."'");
        					        if (xtc_db_num_rows($product_query) == 0)
        					          xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, array ('products_id' => $products_id, 'domain_id' => $id_domain, 'language_id' => $language_id));
                          # Produktname leer? im Adminbereich ggf. als 'NULL' anzeigen falls kein anderer Name (Shops/Sprachen) verfügbar ist
                          if(trim($sql_data_array['products_name'])=='') $sql_data_array['products_name']='NULL';
        									xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \''.xtc_db_input($products_id).'\' and language_id = \''.$language_id.'\' and domain_id = \''.$id_domain.'\'');
                        }
        							}
        						}
        					} elseif(isset($products_data['string_domains_old']) && !in_array($id_domain, $products_data['string_domains_old'])){
        						# unnötige Kategoriebeschreibungen löschen?
        						# echo xtc_count_products_in_domain($categories_id, $domain['domain_id'])
        					}
    					}
    			}
    			
    			for ($i = 0; $i < sizeof($products_data['string_domains_old']); $i++) {
    					 if($products_data['string_domains_old'][$i] > 0 && !in_array($products_data['string_domains_old'][$i], $domain_array_active)){
                   xtc_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where domain_id = '".$products_data['string_domains_old'][$i]."'");
    					 }
    			}
    			
    			unset($sql_data_array);
      }         
      return $sql_data_array;     
    }  

    function insert_category_desc($sql_data_array, $categories_data, $categories_id, $language_id) 
    { 
      global $action, $arrDomainLang, $arrAccess; 
      /*
      echo "<pre>";
      print_r($arrDomainLang);
      print_r($sql_data_array);
      print_r($categories_data);
      echo "</pre>";
      echo "<br /><br /><br />\$categories_id: $categories_id<br />";
      echo "\$language_id: $language_id<br />";    
      exit; 
      */ 
  		if(defined("MULTISTORE") &&  MULTISTORE=='true' && MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS=='true')  {  
          for ($dom = 0; $dom < sizeof($arrDomainLang); $dom++) {
						$id_domain = $arrDomainLang[$dom]['id_domain'];
			      if($_SESSION['superadmin'] || in_array($id_domain, $arrAccess)){
              if(!isset($categories_data['string_domains_old']) ||  in_array($id_domain, $categories_data['string_domains_old'])){
  						  for ($i = 0, $n = sizeof($arrDomainLang[$dom]['id_lang']); $i < $n; $i++) {
  								if($arrDomainLang[$dom]['id_lang'][$i]==$language_id){
  										# Kategoriename leer? im Adminbereich ggf. als 'NULL' anzeigen falls kein anderer Name (Shops/Sprachen) verfügbar ist
  	         					if($action == 'insert_category' && empty($categories_data['categories_name'][$id_domain][$language_id]))
  										  $categories_data['categories_name'][$id_domain][$language_id] = 'NULL';
  										$sql_data_array = array ('categories_name' => xtc_db_prepare_input($categories_data['categories_name'][$id_domain][$language_id]), 'categories_heading_title' => xtc_db_prepare_input($categories_data['categories_heading_title'][$id_domain][$language_id]), 'categories_description' => xtc_db_prepare_input($categories_data['categories_description'][$id_domain][$language_id]), 'categories_meta_title' => xtc_db_prepare_input($categories_data['categories_meta_title'][$id_domain][$language_id]), 'categories_meta_description' => xtc_db_prepare_input($categories_data['categories_meta_description'][$id_domain][$language_id]), 'categories_meta_keywords' => xtc_db_prepare_input($categories_data['categories_meta_keywords'][$id_domain][$language_id]));
                      if (trim(ADD_CATEGORIES_DESCRIPTION_FIELDS) != '') {
                        $sql_data_array = array_merge($sql_data_array, $this->ms_add_data_fields(ADD_CATEGORIES_DESCRIPTION_FIELDS,$categories_data,$language_id,$id_domain));
                      }  
  										if ($action == 'insert_category') {
  											$insert_sql_data = array ('categories_id' => $categories_id,  'domain_id' => $id_domain,  'language_id' => $language_id);
  											$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
  											xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
  										} elseif ($action == 'update_category') {
  							        $category_query = xtc_db_query("SELECT * FROM ".TABLE_CATEGORIES_DESCRIPTION."
  							                                               WHERE language_id = '".$language_id."'
  																														 				AND domain_id = '".$id_domain."'
  							                                                 			AND categories_id = '".$categories_id."'");
  							        if (xtc_db_num_rows($category_query) == 0)
  							          xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, array ('categories_id' => $categories_id, 'domain_id' => $id_domain, 'language_id' => $language_id));
  											
                        # Kategoriename leer? im Adminbereich ggf. als 'NULL' anzeigen falls kein anderer Name (Shops/Sprachen) verfügbar ist
                        if(trim($sql_data_array['categories_name'])=='') $sql_data_array['categories_name']='NULL';
                        xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', 'categories_id = \''.$categories_id.'\' and language_id = \''.$language_id.'\' and domain_id = \''.$id_domain.'\'');
                      }
  									}
  								}
  							}
							}
					}
					
					# optionale Datenübernahme
				  for ($dom = 0; $dom < sizeof($arrDomainLang); $dom++) {
						$id_domain = $arrDomainLang[$dom]['id_domain'];
	          $copyDescription = $categories_data['copyDescription_'.$id_domain];
	          if($copyDescription>0){ # $copyDescription==-1 ||
					    for ($i = 0, $n = sizeof($arrDomainLang[$dom]['id_lang']); $i < $n; $i++) {
								  if($arrDomainLang[$dom]['id_lang'][$i]==$language_id){
										$this->set_categoriesDescription($categories_id, $id_domain, $language_id, $copyDescription);
									}
							}
						}
					} 
          unset($sql_data_array); 
      } 
      
      return $sql_data_array;
    }
    
    # Originalfunktion ergänzt um Shop-ID
    function ms_add_data_fields($add_data_string, $data, $language_id = '', $id_domain=0) {
      $add_data_array = explode(',',preg_replace("'[\r\n\s]+'",'',$add_data_string));
      $add_data_fields_array = array();
      for ($i = 0, $n = sizeof($add_data_array); $i < $n; $i ++) {
        if ($language_id != '') {
          # MODULE MULTISTORE
  				if($id_domain>0){
          	$add_data_fields_array[$add_data_array[$i]] = xtc_db_prepare_input($data[$add_data_array[$i]][$id_domain][$language_id]);
  				}else{
         		$add_data_fields_array[$add_data_array[$i]] = xtc_db_prepare_input($data[$add_data_array[$i]][$language_id]);
  				}
        } else {
          $add_data_fields_array[$add_data_array[$i]] = xtc_db_prepare_input($data[$add_data_array[$i]]);
        } 
      }
      return $add_data_fields_array;
    }
    
    function set_categoriesDescription($categories_id, $id_domain, $language_id, $copyDescription){
      # Kategoriedaten
      $arrCategoriesParent = xtc_get_category_tree($categories_id, '', 0, '', true);#
      # Übernahme Shop
      $copy_query = xtc_db_query("Select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '$categories_id' and domain_id = '$copyDescription' and language_id = '$language_id'");
      $result_query = xtc_db_fetch_array($copy_query);
      # Übernahme Shop UKs
      for ($p = 0; $p < sizeof($arrCategoriesParent); $p++) {
          $sql_data_array = array ('categories_name' => ($result_query['categories_name']), 'categories_heading_title' => ($result_query['categories_heading_title']), 'categories_description' => ($result_query['categories_description']), 'categories_meta_title' => ($result_query['categories_meta_title']), 'categories_meta_description' => ($result_query['categories_meta_description']), 'categories_meta_keywords' => ($result_query['categories_meta_keywords']));
          $cID = $arrCategoriesParent[$p]['id'];
          if($cID>0){
            $copy_query = xtc_db_query("Select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '$cID' and domain_id = '$copyDescription' and language_id = '$language_id'");
            $result_copy = xtc_db_fetch_array($copy_query);
            $sql_data_array = array ('categories_name' => ($result_copy['categories_name']), 'categories_heading_title' => ($result_copy['categories_heading_title']), 'categories_description' => ($result_copy['categories_description']), 'categories_meta_title' => ($result_copy['categories_meta_title']), 'categories_meta_description' => ($result_copy['categories_meta_description']), 'categories_meta_keywords' => ($result_copy['categories_meta_keywords']));
  
            $compare_query = xtc_db_query("Select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '$cID' and domain_id = '$id_domain' and language_id = '$language_id'");
            # Insert
            if(xtc_db_num_rows($compare_query)==0){                     
                $merged_data_array = array ('categories_id' => $cID,  'domain_id' => $id_domain,  'language_id' => $language_id);
                $sql_data_array = xtc_array_merge($sql_data_array, $merged_data_array);
                xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
            } else {
                # Update
                xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', 'categories_id = \''.$cID.'\' and language_id = \''.$language_id.'\' and domain_id = \''.$id_domain.'\'');
            }
        }
      }
  
      # Artikeldaten
      for ($p = 0; $p < sizeof($arrCategoriesParent); $p++) {
         $cID = $arrCategoriesParent[$p]['id'];
         if($cID>0){
            $src_query = xtc_db_query("Select * from " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c left join ".TABLE_PRODUCTS_DESCRIPTION." pd on p2c.products_id = pd.products_id where p2c.categories_id = '$cID' and pd.domain_id = '$copyDescription' and pd.language_id = '$language_id'");
          if(xtc_db_num_rows($src_query)>0){
            while($result_query = xtc_db_fetch_array($src_query)){
              $sql_data_array = array ('products_name' => ($result_query['products_name']), 'products_description' => ($result_query['products_description']), 'products_short_description' => ($result_query['products_short_description']), 'products_keywords' => ($result_query['products_keywords']), 'products_meta_title' => ($result_query['products_meta_title']), 'products_meta_description' => ($result_query['products_meta_description']));

              $copy_query = xtc_db_query("Select * from " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c left join ".TABLE_PRODUCTS_DESCRIPTION." pd on p2c.products_id = pd.products_id where p2c.categories_id = '$cID' and pd.domain_id = '$id_domain' and pd.language_id = '$language_id' and pd.products_id = '".$result_query['products_id']."'");
              # Insert
              if(xtc_db_num_rows($copy_query)==0){
                    $merged_data_array = array ('products_id' => $result_query['products_id'],  'domain_id' => $id_domain,  'language_id' => $language_id);
                    $sql_data_array = xtc_array_merge($sql_data_array, $merged_data_array);
                    xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
              }else{
                # Update
                    xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \''.$result_query['products_id'].'\' and language_id = \''.$language_id.'\' and domain_id = \''.$id_domain.'\'');
              }
            }
          }
        }
      }  
    }

}