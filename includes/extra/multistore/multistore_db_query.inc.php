<?php

  function ms_db_query($query) {
   # error_reporting(-1); @ini_set('display_errors', true);
    global $strAccessSql, $sqlCompare, $sqlCompareC, $debugSql, $echoSql, $called_by_admin;   
    $debugSql = true;
    $debugItem=0;
    $arrDebug=array();
    $PHP_SELF = set_php_self();
    $MULTISTORE_SQL_PDESCRIPTION = '';

	if ($called_by_admin || 
		$_SESSION['sql_ms_through'] || # D.L. Verwendung u.a. in orders_history.php 	
		defined('RUN_MODE_ADMIN') || 
		(defined('FILENAME_BINGFEED') && strpos($PHP_SELF, FILENAME_BINGFEED) !== false) ||
		(defined('FILENAME_CSV_BACKEND') && strpos($PHP_SELF, FILENAME_CSV_BACKEND) !== false) ) {
     	$_SESSION['sql_ms_through'] = false;
		return $query;
    }       
  
    
    if(defined("MULTISTORE") &&  MULTISTORE=='true'){
        $queryTemp = $queryModified = strtolower(trim($query));
  		if (stripos(trim($queryModified), "-- ") !== false) {              
            $arrModified = explode((stripos($queryModified, "\r\n")?"\r\n":"\n" ), $queryModified);  
            if(count($arrModified)>0)
              $queryModified = "";
            for($i=0; $i < count($arrModified); $i++){
                if(substr(trim($arrModified[$i]), 0, 3)== "-- "){  
                  if($i==0)
                    $comment = $arrModified[0];               
                }else{
                  $queryModified .= $arrModified[$i];
                }
            }     
        }    
        /*
        if(substr(trim($queryModified), 0, 3)== "-- "){
              $arrModified = explode((stripos($queryModified, "\r\n")?"\r\n":"\n" ), $queryModified);                    
              $comment = $arrModified[0];
              array_shift($arrModified);
              $queryModified = join(" ", $arrModified);                   
        } 
        */
        $queryTemp = $queryModified = trim( preg_replace('/\s+/', ' ', $queryModified) );
        
        if(defined('RUN_MODE_ADMIN') && $_SESSION['superadmin'])                  
            $sqlCompare = "";
        if(!strpos($queryModified, '`option`'))    
            $queryModified = str_replace("`", "", $queryModified);
        $queryModified = str_replace("products as p", "products p", $queryModified);
        $queryModified = str_replace("products_to_categories as p2c", "products_to_categories p2c", $queryModified);
        $queryModified = str_replace("products_description as pd", "products_description pd", $queryModified);
        $queryModified = str_replace("products_to_categories as p2c", "products_to_categories p2c", $queryModified);
        $queryModified = str_replace("products_attributes as pa", "products_attributes pa", $queryModified);
        $queryModified = str_replace("specials as s", "specials s", $queryModified);
        if(!strpos($queryModified, '`option`'))    
            $queryModified = str_replace("`", "", $queryModified);
                            
        if(!defined('RUN_MODE_ADMIN')){    
          if (substr((trim($queryModified)), 0, 6) == 'select') {   
              if(stripos(trim($queryModified), 'where ') == false){ 
                if(stripos(trim($queryModified), "group by ") !== false){   
                   $subStrSearchReplace = "group by ";  
                }elseif(stripos(trim($queryModified), ' order by ') !== false){
                   $subStrSearchReplace = "order by ";     
                }elseif(stripos(trim($queryModified), ' limit ') !== false){
                   $subStrSearchReplace = "";  
                } else {
                }     
              }elseif(stripos(trim($queryModified), ' limit ') !== false){
                $subStrSearchReplace = "";
              }elseif (stripos(trim($queryModified), "join ".TABLE_CONTENT_MANAGER." c2") !== false) { 
                  return $queryModified; 
              }elseif (stripos(trim($queryModified), "from ".TABLE_CUSTOMERS) !== false && stripos(trim($queryModified), "where customers_email_address = ") !== false) { 
                if(isset($_POST['email_address']) && LOGIN_STRICT == '1'){
                  return sprintf(MULTISTORE_SQL_LOGIN, xtc_db_prepare_input($_POST['email_address'])); 
                }
              } 
              $queryModified = str_replace("count(distinct *)", "count(*)", $queryModified);
              # TABLE_CATEGORIES_DESCRIPTION
              if (stripos(trim($queryModified), TABLE_CATEGORIES_DESCRIPTION) == false) {               
                if (defined('MULTISTORE_SQL_C2DESCRIPTION') && stripos(trim($queryModified), trim(MULTISTORE_SQL_C2DESCRIPTION)) !== false) {
                    # Filtern von MULTISTORE_SQL_C2DESCRIPTION in CATEGORIES_CONDITIONS falls TABLE_CATEGORIES_DESCRIPTION nicht enthalten ist 
                    $arrDebug[]=81;
                    $queryModified = str_replace(MULTISTORE_SQL_C2DESCRIPTION, "", $queryModified);      
                }  
              } elseif (defined('MULTISTORE_SQL_C2DESCRIPTION') && MULTISTORE_SQL_C2DESCRIPTION != "" && stripos(trim($queryModified), trim(MULTISTORE_SQL_C2DESCRIPTION)) == false) {
                  # Ergänzung von MULTISTORE_SQL_C2DESCRIPTION
                  $MULTISTORE_SQL_C2DESCRIPTION = stripos(trim($queryModified), "cd.") !== false ? MULTISTORE_SQL_C2DESCRIPTION : str_replace("cd.", "", MULTISTORE_SQL_C2DESCRIPTION); 
                  if(!isset($subStrSearchReplace) || $subStrSearchReplace == ""){
                     if(stripos(trim($queryModified), "group by ") !== false){  
                      $arrDebug[]=89;
                      $queryModified = str_replace( "group by ", $MULTISTORE_SQL_C2DESCRIPTION .  " group by ", $queryModified);
                     }elseif(stripos(trim($queryModified), "order by ") !== false){
                      $arrDebug[]=92;
                      $arrDebug[]=93;$queryModified = str_replace( "order by ", $MULTISTORE_SQL_C2DESCRIPTION .  " order by ", $queryModified);
                     }elseif(stripos(trim($queryModified), "limit ") !== false){  

                      $arrDebug[]=96;$queryModified = str_replace( "limit ", $MULTISTORE_SQL_C2DESCRIPTION .  " limit ", $queryModified);
                     }else{

                      $arrDebug[]=99;$queryModified .= $MULTISTORE_SQL_C2DESCRIPTION;
                     }
                  } elseif($subStrSearchReplace != ""){

                    $arrDebug[]=103;$queryModified = str_replace($subStrSearchReplace, " where 1=1 " . $MULTISTORE_SQL_C2DESCRIPTION . $subStrSearchReplace, $queryModified);
                  }        
                  if(stripos($queryModified, " categories_name") !== false && stripos($queryModified, "categories_id = '") !== false && stripos($queryModified, "domain_id = '") !== false)
                    return $queryModified;  
              }
   
              # TABLE_PRODUCTS_DESCRIPTION
              if (stripos(trim($queryModified), TABLE_PRODUCTS_DESCRIPTION) == false) {
                if (defined('MULTISTORE_SQL_PDESCRIPTION') && stripos(trim($queryModified), trim(MULTISTORE_SQL_PDESCRIPTION)) !== false) {
                    # Filtern von MULTISTORE_SQL_C2DESCRIPTION in PRODUCTS_CONDITIONS falls TABLE_PRODUCTS_DESCRIPTION nicht enthalten ist 

                    $arrDebug[]=114;$queryModified = str_replace(MULTISTORE_SQL_PDESCRIPTION, "", $queryModified);
                }  
              } elseif (defined('MULTISTORE_SQL_PDESCRIPTION') && MULTISTORE_SQL_PDESCRIPTION != "" && stripos(trim($queryModified), trim(MULTISTORE_SQL_PDESCRIPTION)) == false) {
                  # Ergänzung von MULTISTORE_SQL_PDESCRIPTION
                  $MULTISTORE_SQL_PDESCRIPTION = stripos(trim($queryModified), "pd.") !== false ? MULTISTORE_SQL_PDESCRIPTION : str_replace("pd.", "", MULTISTORE_SQL_PDESCRIPTION); 
                  
       #  if (stripos(trim($queryModified), "96") !== false)    
              # echo " <br /><br /><br /><br /><br />$subStrSearchReplace !: $queryModified<br />";                  
                  if(!isset($subStrSearchReplace) || $subStrSearchReplace == ""){  
                     if(stripos(trim($queryModified), "group by ") !== false){  

                      $arrDebug[]=125;$queryModified = str_replace( "group by ", $MULTISTORE_SQL_PDESCRIPTION .  " group by ", $queryModified);
                     }elseif(stripos(trim($queryModified), "order by ") !== false){  

                      $arrDebug[]=128;$queryModified = str_replace( "order by ", $MULTISTORE_SQL_PDESCRIPTION .  " order by ", $queryModified);
                     }elseif(stripos(trim($queryModified), "limit ") !== false){  

                      $arrDebug[]=131;$queryModified = str_replace( "limit ", $MULTISTORE_SQL_PDESCRIPTION .  " limit ", $queryModified);
                     }else{

                      $arrDebug[]=134;$queryModified .= $MULTISTORE_SQL_PDESCRIPTION;
                     }
                  } elseif($subStrSearchReplace != ""){

                    $arrDebug[]=138;$queryModified = str_replace($subStrSearchReplace, " where 1=1 " .$MULTISTORE_SQL_PDESCRIPTION . $subStrSearchReplace, $queryModified);
                  }  
                  # D.L.
                  if (strpos($PHP_SELF, FILENAME_ADVANCED_SEARCH_RESULT) !== false && stripos(trim($queryModified), " count(") != false) 
                      return $queryModified;

                 #  echo " <br /><br /><br /><br /><br />!: $queryModified<br />"; 
              
  
                  if(MULTISTORE_SQL_PDESCRIPTION != "" && stripos(trim($queryModified), "distinct ") == false) {

                    $arrDebug[]=149;$queryModified = str_replace("select ", "select distinct ", $queryModified);
                  }
                  if(stripos($queryModified, " products_name") !== false && stripos($queryModified, "products_id = '") !== false && stripos($queryModified, "domain_id = '") !== false)
                     return $queryModified;       
              }elseif((!defined('MULTISTORE_SQL_PDESCRIPTION') || MULTISTORE_SQL_PDESCRIPTION == "") && stripos(trim($queryModified), TABLE_PRODUCTS_DESCRIPTION) !== false){
                  # echo "<br /><br /><br /> ORIGL: $queryModified<br />"; 
                  if(stripos($queryModified, "select products_name") !== false && stripos($queryModified, " products_id = '") !== false)
                    return $queryModified;                 
              }  
          }    
 
          if (stripos(trim($queryModified), TABLE_PRODUCTS_DESCRIPTION) !== false &&
                defined('MULTISTORE_SQL_PDESCRIPTION') &&
                stripos(trim($queryModified), trim(MULTISTORE_SQL_PDESCRIPTION)) !== false &&
                MULTISTORE_SQL_PDESCRIPTION != "" && stripos(trim($queryModified), "distinct ") == false) {

              $arrDebug[]=162;$queryModified = str_replace("select ", "select distinct ", $queryModified);
          }

          if (stripos(trim($queryModified), TABLE_CATEGORIES_DESCRIPTION) !== false && stripos(trim($queryModified), trim(MULTISTORE_SQL_C2DESCRIPTION)) !== false && MULTISTORE_SQL_C2DESCRIPTION != "" && stripos(trim($queryModified), "distinct ") == false) {

              $arrDebug[]=167;$queryModified = str_replace("select ", "select distinct ", $queryModified);
          }

        
          $subStrSearchReplace = "where ";  
          
          if (stripos((trim($queryModified)), "select ") !== false) { 
            if (stripos(trim($queryModified), "from " . TABLE_CONTENT_MANAGER . "") !== false) {
                # Content Manager                                     
                if(stripos(trim($queryModified), "where ") !== false) {

                    $arrDebug[]=178;$queryModified = str_replace("where ", "where  1=1 " . MULTISTORE_SQL_CONTENT . " and ", $queryModified);
                }


            }elseif (isset($_SESSION['customer_id']) && stripos(trim($queryModified), " " . TABLE_CUSTOMERS_BASKET) !== false && stripos(trim($queryModified), "customers_id") !== false && stripos(trim($queryModified), "products_id") !== false && stripos(trim($queryModified), $_SESSION['customer_id']) !== false) { 
                # Warenkorb
                if(stripos((trim($queryModified)), "select products_id") !== false){
                    if(stripos(trim($queryModified), ", id_domain") == false){

                      $arrDebug[]=187;$queryModified = str_replace("select products_id", "select products_id, id_domain ", $queryModified);

                      $arrDebug[]=189;$queryModified = str_replace("select products_id", "select products_id, id_domain ", $queryModified);
                    }                   
                }
                if(MS_MULTIBASKET !='true' && stripos(trim($queryModified), MULTISTORE_ID_DOMAIN) == false){
                    if(stripos(trim($queryModified), "where ") !== false){ 

                       $arrDebug[]=195;$queryModified = str_replace("where ", "where 1=1 " . MULTISTORE_ID_DOMAIN . " and ", $queryModified);
                    }elseif(stripos(trim($queryModified), "group by ") !== false){ 

                       $arrDebug[]=198;$queryModified = str_replace("group by ", "where 1=1 " . MULTISTORE_ID_DOMAIN . "group by ", $queryModified);
                    }elseif(stripos(trim($queryModified), "order by ") !== false){                         

                       $arrDebug[]=201;$queryModified = str_replace("order by ", "where 1=1 " . MULTISTORE_ID_DOMAIN . "order by ", $queryModified);
                    }                    
                }    
            }elseif(stripos(trim($queryModified), TABLE_CATEGORIES_DESCRIPTION) !== false){

                  $arrDebug[]=206;$queryModified = str_replace("select ", "select ", $queryModified);
                  # Template: Kategorie-Abfrage
                  if(stripos(trim($queryModified), "sort_order, cd.categories_name") !== false ||
                    stripos(trim($queryModified), "sort_order, c.parent_id, cd.categories_name") !== false
                  ){ 
                    if(stripos(trim($queryModified), "distinct") == false){

                      $arrDebug[]=213;$queryModified = str_replace("select ", "select distinct ", $queryModified);
                    }
                    $arrDebug[]=215;$queryModified = str_replace("c.sort_order,", "sort_order,", $queryModified);
                                      
                    if(stripos(trim($queryModified), "group by ") === false){

                      $arrDebug[]=219;$queryModified = str_replace("order by sort_order, cd.categories_name", " group by c.categories_id order by sort_order, cd.categories_name", $queryModified);
                    }

                    if(stripos(trim($queryModified), "categories_id = '") === false && stripos(trim($queryModified), "c.categories_status = '1'") !== false){ 
                      # \includes\modules\default.php

                      $arrDebug[]=225;$queryModified = str_replace("c.categories_status = '1'", "c.categories_status = '1'" . MULTISTORE_SQL_SEARCH_WHERE2a, $queryModified);
                    }                          
                  }              
            }elseif(stripos(trim($queryModified), ' '.TABLE_ORDERS_PRODUCTS.' ') !== false || stripos(trim($queryModified), "order by p.products_ordered") !== false){  
                  if(stripos(trim($queryModified), "o.customers_id =") !== false){ 

                    $arrDebug[]=231;$queryModified = str_replace("where ", "where 1=1 " . MULTISTORE_ID_DOMAIN . " and ", $queryModified);
                  } elseif(stripos(trim($queryModified), TABLE_ORDERS.' ') !== false){ 
                    # \tpl\source\boxes\best_sellers.php

                    $arrDebug[]=235;$queryModified = str_replace("group by ", MULTISTORE_SQL_ORDERS_WHERE . "group by ", $queryModified);
                  } elseif(stripos(trim($queryModified), "order by p.products_ordered") !== false){ 
                    # \wolkenkraft-rainbow\source\boxes\best_sellers.php

                    $arrDebug[]=239;$queryModified = trim( preg_replace('/\s+/', ' ', $queryModified) );

                    $arrDebug[]=241;$queryModified = str_replace(', products_to_categories p2c, categories c', "", $queryModified);

                    $arrDebug[]=243;$queryModified = str_replace('and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id', "", $queryModified);
                    
                    if(stripos($queryModified, "join categories c on p2c.categories_id = c.categories_id") == false                     
                       && stripos($queryModified, "join categories c on c.categories_id = p2c.categories_id") == false
                       ){
                      if(stripos($queryModified, TABLE_PRODUCTS_TO_CATEGORIES." p2c") !== false){

                        $arrDebug[]=248;$queryModified = str_replace("on p.products_id = p2c.products_id", "on p.products_id = p2c.products_id" . MULTISTORE_SQL_SEARCH_JOIN2, $queryModified);

                        $arrDebug[]=250;$queryModified = str_replace("on p2c.products_id = p.products_id", "on p.products_id = p2c.products_id" . MULTISTORE_SQL_SEARCH_JOIN2, $queryModified);
                      } else {                         

                        $arrDebug[]=253;$queryModified = str_replace("from products p", " from products p" . MULTISTORE_SQL_SEARCH_JOIN1 . MULTISTORE_SQL_SEARCH_JOIN2, $queryModified);
                      }                       
					}

                    $arrDebug[]=257;$queryModified = str_replace("group by p.products_id", "", $queryModified);

                    $arrDebug[]=259;$queryModified = str_replace("order by ", MULTISTORE_SQL_SEARCH_WHERE2a . $MULTISTORE_SQL_PDESCRIPTION . "group by	p.products_id order by ", $queryModified);
                   # if($_GET['debugSql']>0) 
                   # echo "\:$queryModified<br />";         
                  }
                  if(stripos(trim($queryModified), "op.orders_id =") !== false && stripos(trim($queryModified), TABLE_PRODUCTS_DESCRIPTION) !== false){ 
                    # getAlsoPurchased()
                    if(stripos($queryModified, "join categories c on p2c.categories_id = c.categories_id") == false
                       && stripos($queryModified, "join categories c on c.categories_id = p2c.categories_id") == false)  {
                      $arrDebug[]=267;$queryModified = str_replace("where ", (stripos($queryModified, TABLE_PRODUCTS_TO_CATEGORIES." p2c") !== false ? '' : MULTISTORE_SQL_SEARCH_JOIN1) . MULTISTORE_SQL_SEARCH_JOIN2 . "where ", $queryModified);
                    }
                    if(stripos(trim($queryModified), "group by") == false){

                      $arrDebug[]=271;$queryModified .=  MULTISTORE_SQL_SEARCH_WHERE2a;
                    }
                  } elseif(stripos(trim($queryModified), "p.products_id IN") !== false && stripos(trim($queryModified), TABLE_PRODUCTS_DESCRIPTION) !== false){
                    # \boxes\best_sellers.php
                    if(stripos($queryModified, "join categories c on p2c.categories_id = c.categories_id") == false
                       && stripos($queryModified, "join categories c on c.categories_id = p2c.categories_id") == false){

                      $arrDebug[]=277;$queryModified = str_replace("where ", (stripos($queryModified, TABLE_PRODUCTS_TO_CATEGORIES." p2c") !== false ? '' : MULTISTORE_SQL_SEARCH_JOIN1) . MULTISTORE_SQL_SEARCH_JOIN2 . "where ", $queryModified);
                    }
                    if(stripos(trim($queryModified), "group by") !== false){

                      $arrDebug[]=291;$queryModified = str_replace("group by ", MULTISTORE_SQL_SEARCH_WHERE2a . "group by ", $queryModified);
                    }
                  }  
            }elseif(stripos(trim($queryModified), ' '.TABLE_PRODUCTS_DESCRIPTION.' ') !== false){     
 
                  if(stripos(trim($queryModified), "join ".TABLE_PRODUCTS_TO_CATEGORIES) !== false){   
                       if(stripos(trim($queryModified), ' '.TABLE_CATEGORIES.' ') == false && stripos(trim($queryModified), MULTISTORE_SQL_SEARCH_JOIN2.' ') == false){
                          if(stripos(trim($queryModified), ' where ') === false){

                            $arrDebug[]=290;$queryModified .= MULTISTORE_SQL_SEARCH_JOIN2;
                          } else{

                            $arrDebug[]=293;$queryModified = str_replace(" where ", MULTISTORE_SQL_SEARCH_JOIN2 . " where ", $queryModified);
                          }  
                       }                               
                       if(stripos(trim($queryModified), MULTISTORE_SQL_SEARCH_WHERE2a) == false){
                            # 16.11.2023
                            if(stripos(trim($queryModified), ' where ') == false){                                   
                              if(stripos(trim($queryModified), 'group by') == false){
                                 $arrDebug[]=299;$queryModified .= " where 1=1 " . MULTISTORE_SQL_SEARCH_WHERE2a;
                              }else{
                                 $arrDebug[]=299;$queryModified = str_replace("group by",  " where  1=1 " . MULTISTORE_SQL_SEARCH_WHERE2a . "group by", $queryModified);
                              }  
                            }else{    
                               # $arrDebug[]=302;$queryModified = str_replace("p.products_status = '1'", "p.products_status = '1' " . MULTISTORE_SQL_SEARCH_WHERE2a, $queryModified);
                               # $arrDebug[]=304;$queryModified = str_replace("p.products_status = 1", "p.products_status = '1' " . MULTISTORE_SQL_SEARCH_WHERE2a, $queryModified);
                               $arrDebug[]=310;$queryModified = str_replace(" where ", MULTISTORE_SQL_SEARCH_WHERE2a . " where ", $queryModified);
                            }                                                            
                       } 
                       
                       
                  }elseif(stripos(trim($queryModified), 's.specials_date_added') !== false &&  stripos(trim($queryModified), TABLE_SPECIALS.' ') !== false && stripos(trim($queryModified), TABLE_PRODUCTS.' ') !== false && stripos(trim($queryModified), TABLE_PRODUCTS_DESCRIPTION.' ') !== false){  
                      # \wolkenkraft-rainbow\source\boxes\specials.php

                      $arrDebug[]=313;$queryModified = str_replace("from products p", " from products p" . MULTISTORE_SQL_SEARCH_JOIN1 . MULTISTORE_SQL_SEARCH_JOIN2, $queryModified);
                      $arrDebug[]=314;$queryModified = str_replace("order by ", MULTISTORE_SQL_SEARCH_WHERE2a . $MULTISTORE_SQL_PDESCRIPTION . "group by	p.products_id order by ", $queryModified);
               
                  } else{  
                   
                     # Aufruf Artikeldetails / product_info.php
                     $subStrAdd = "" ;                 
                     if(stripos(trim($queryModified), ' where ') == false){ 
                        if(stripos(trim($queryModified), "group by ") !== false){ 

                           $arrDebug[]=323;$queryModified = str_replace(" group by ", " where group by ", $queryModified);
                        }elseif(stripos(trim($queryModified), ' order by ') !== false){

                           $arrDebug[]=326;$queryModified = str_replace(" order by ", " where order by ", $queryModified);
                        } else{

                           $arrDebug[]=329;$queryModified .= " where ";
                        }     
                     } else {
                        $subStrAdd = " and ";
                     }    
                     
	                 if(stripos(trim($queryModified), TABLE_PRODUCTS_TO_CATEGORIES." p2c") == false){
	                     if(stripos(trim($queryModified), "join products p") !== false && stripos(trim($queryModified), "p.products_id = r.products_id") !== false){ 

                         $arrDebug[]=338;$queryModified = str_replace("p.products_id = r.products_id", "p.products_id = r.products_id " . MULTISTORE_SQL_SEARCH_JOIN1, $queryModified);
	                     } elseif(stripos(trim($queryModified), "join products p") !== false && stripos(trim($queryModified), "products_id = p.products_id") !== false){ 

                         $arrDebug[]=341;$queryModified = str_replace("products_id = p.products_id", "products_id = p.products_id " . MULTISTORE_SQL_SEARCH_JOIN1, $queryModified);
	                     } elseif(stripos(trim($queryModified), "products p") !== false){ 
	                         if(stripos(trim($queryModified), "products p on") !== false && stripos(trim($queryModified), 'join '.TABLE_PRODUCTS_DESCRIPTION) !== false){
	                            # function getCrossSells()

                              $arrDebug[]=346;$queryModified = str_replace('join '.TABLE_PRODUCTS_DESCRIPTION, MULTISTORE_SQL_SEARCH_JOIN1 . 'join ' . TABLE_PRODUCTS_DESCRIPTION, $queryModified);
	                            if(MULTISTORE_SQL_PDESCRIPTION != "" && stripos(trim($queryModified), "distinct ") == false) {

                                $arrDebug[]=349;$queryModified = str_replace("select ", "select distinct ", $queryModified);
                              }
	                         }else{

                              $arrDebug[]=353;$queryModified = str_replace(" products p", " products p " . MULTISTORE_SQL_SEARCH_JOIN1, $queryModified);
	                         }  
	                         # $queryModified = str_replace(" products p", " products p " . MULTISTORE_SQL_SEARCH_JOIN1, $queryModified);
	                     } else {

                         $arrDebug[]=358;$queryModified = str_replace(" where ", MULTISTORE_SQL_SEARCH_JOIN1 . " where ", $queryModified);
	                     }
	                 } elseif(stripos(trim($queryModified), ' '.TABLE_CATEGORIES.' ') == false){ 
	                    # HK: gunnartProductsList()

                      $arrDebug[]=363;$queryModified = str_replace(TABLE_PRODUCTS_TO_CATEGORIES." p2c", TABLE_PRODUCTS_TO_CATEGORIES." p2c" . MULTISTORE_SQL_SEARCH_JOIN2, $queryModified);
	                 } 
                     if(stripos(trim($queryModified), ' '.TABLE_CATEGORIES.' ') == false){ 

                        $arrDebug[]=367;$queryModified = str_replace(MULTISTORE_SQL_SEARCH_JOIN1 , MULTISTORE_SQL_SEARCH_JOIN1 . MULTISTORE_SQL_SEARCH_JOIN2 , $queryModified);
                     }                

                     $arrDebug[]=370;$queryModified = str_replace(" where ", " where 1=1 " . MULTISTORE_SQL_SEARCH_WHERE2a . $subStrAdd, $queryModified);
                     if(stripos(trim($queryModified), ' '.TABLE_CATEGORIES.' ') !== false && stripos(trim($queryModified), ' categories_id ') !== false && stripos(trim($queryModified), ' c.categories_id ') == false){

                        $arrDebug[]=373;$queryModified = str_replace(" categories_id ", " c.categories_id ", $queryModified);
                     }  
                  }  
                                     
            }elseif(stripos(trim($queryModified), ' '.TABLE_PRODUCTS) !== false){ 
            
                if(stripos(trim($queryModified), ' '.TABLE_MANUFACTURERS.' ') !== false && stripos(trim($queryModified), 'm.manufacturers_id, m.manufacturers_name') !== false && stripos(trim($queryModified), 'order by m.manufacturers_name') !== false){ 
                      # \tpl\source\boxes\manufacturers.php

                      $arrDebug[]=382;$queryModified = MULTISTORE_SQL_MANUFACTURERS;
                }elseif(stripos(trim($queryModified), 'where products_model') !== false){   
                  # \includes\cart_actions.php - Add a quickie   
                  # workaround prefix  

                  $arrDebug[]=387;$queryModified = str_replace("products_id", TABLE_PRODUCTS.".products_id", $queryModified);
                  $arrDebug[]=388;$queryModified = str_replace("group_permission_", TABLE_PRODUCTS.".group_permission_", $queryModified);
                  $MULTISTORE_SQL_JOIN = str_replace("p.", TABLE_PRODUCTS.".", MULTISTORE_SQL_JOIN);
                  $MULTISTORE_SQL_SEARCH_WHERE = str_replace("p.", TABLE_PRODUCTS.".", MULTISTORE_SQL_SEARCH_WHERE2a);
                           
                  $arrDebug[]=392;$queryModified = str_replace("from " . TABLE_PRODUCTS, "from " . TABLE_PRODUCTS . $MULTISTORE_SQL_JOIN, $queryModified);
                  $arrDebug[]=393;$queryModified = str_replace("and products_status = '1'",  "and products_status = '1'" . $MULTISTORE_SQL_SEARCH_WHERE, $queryModified);
                }elseif(stripos(trim($queryModified), ' '.TABLE_MANUFACTURERS.' ') !== false){ 
                   if(stripos(trim($queryModified), ' order by ') !== false){   

                      $arrDebug[]=397;$queryModified = str_replace(' order by ', MULTISTORE_SQL_SEARCH_JOIN1 . ' order by ', $queryModified);
                      $arrDebug[]=398;$queryModified = str_replace(' order by ', MULTISTORE_SQL_SEARCH_JOIN2 . ' order by ', $queryModified);
                      $arrDebug[]=399;$queryModified = str_replace(" order by ", " where 1=1 " . MULTISTORE_SQL_SEARCH_WHERE2a . " order by ", $queryModified);
                   }                       
                }elseif(stripos(trim($queryModified), ' '.TABLE_PRODUCTS_TO_CATEGORIES.' ') !== false){ 
                  if(stripos(trim($queryModified), ' where ') !== false){
                    if(stripos(trim($queryModified), ' '.TABLE_CATEGORIES.' ') == false) {

                       $arrDebug[]=405;$queryModified = str_replace(' where ', MULTISTORE_SQL_SEARCH_JOIN2 . ' where ', $queryModified);
                    }
                    $arrDebug[]=407;$queryModified = str_replace(" where ", " where 1=1 " . MULTISTORE_SQL_SEARCH_WHERE2a . " and ", $queryModified);
                    # ALIAS ergänzen
                    if(stripos(trim($queryModified), ' categories_id ') !== false) {

                        $arrDebug[]=411;$queryModified = str_replace(' categories_id ', ' c.categories_id =', $queryModified);
                    }
                    if(stripos(trim($queryModified), TABLE_PRODUCTS_TO_CATEGORIES . ' p2c') == false){

                      $arrDebug[]=415;$queryModified = str_replace(' '.TABLE_PRODUCTS_TO_CATEGORIES.' ', ' '.TABLE_PRODUCTS_TO_CATEGORIES.' p2c ', $queryModified);
                    }

      			        if(stripos(trim($queryModified), ' date_added ') !== false && stripos(trim($queryModified), TABLE_PRODUCTS_TO_CATEGORIES) !== false && stripos(trim($queryModified), TABLE_CATEGORIES) !== false){

                        $arrDebug[]=420;$queryModified = str_replace(' date_added ', ' p2c.date_added ', $queryModified);
                    }
                  }
                }  
            } elseif (stripos(trim($queryModified), trim(TABLE_COUNTRIES)) !== false && stripos(trim($queryModified), 'countries_name') !== false && stripos(trim($queryModified), 'countries_id =') == false) {
 				         if(stripos(trim($queryModified),' where ') == false) {

                  $arrDebug[]=427;$queryModified = str_replace(' order by ', MULTISTORE_SQL_COUNTRIES . ' order by ', $queryModified);
                }else{

                  $arrDebug[]=430;$queryModified = str_replace(' where ', MULTISTORE_SQL_COUNTRIES . ' and ', $queryModified);
                }
            }              
          }       
                       
          if($echoSql)  {
            echo "<br /><br />\$echoSql: $queryModified<br />"; 
            $echoSql=false; 
          }
          
      
        
            
        }  else { 
            # BACKEND    
                                               
            if(stripos(trim($queryModified), "where ") !== false){ 
               $subStrSearchReplace = "where "; 
               $subStrAdd = " and ";    
            }elseif(stripos(trim($queryModified), "group by ") !== false){ 
               $subStrSearchReplace = "group by ";   
               $subStrAdd = $subStrSearchReplace;
            }elseif(stripos(trim($queryModified), "order by ") !== false){
               $subStrSearchReplace = "order by "; 
               $subStrAdd = $subStrSearchReplace;
            }  
               
            if (stripos(trim($queryModified), TABLE_CATEGORIES_DESCRIPTION) !== false && stripos(trim($queryModified), TABLE_CATEGORIES . " ") == false) {               
                # XML Sitemap 
                if (stripos(trim($queryModified), trim(MULTISTORE_SQL_C2DESCRIPTION)) == false && isset($_POST['id_domain']) && stripos(trim($queryModified), "group by ") == false && stripos(trim($queryModified), "order by ") == false) {

                    $arrDebug[]=461;$queryModified .= sprintf(MULTISTORE_SQL_CDESCRIPTION, $_POST['id_domain']);
                }  
            }elseif (stripos(trim($queryModified), TABLE_PRODUCTS_DESCRIPTION) !== false && stripos(trim($queryModified), TABLE_PRODUCTS . " ") == false) {               
                # XML Sitemap 
                if (stripos(trim($queryModified), trim(MULTISTORE_SQL_ADESCRIPTION)) == false && isset($_POST['id_domain']) && stripos(trim($queryModified), "group by ") == false && stripos(trim($queryModified), "order by ") == false) {

                    $arrDebug[]=467;$queryModified .= sprintf(MULTISTORE_SQL_ADESCRIPTION, $_POST['id_domain']);
                   # echo "<br /><br /><br />!: $queryModified<br />"; 
                }  
            }  
            
            if (stripos((trim($queryModified)), "select ") !== false && stripos(trim($queryModified), " from " . TABLE_ORDERS . " ") !== false) {       
               # Bestellungen / Verkaufsstatistik
               $prefix = "";  
               #if(stripos(trim($queryModified), " from " . TABLE_ORDERS . " o ") !== false){
               #   $prefix = "o.";
               #}  
               #echo "<br /><br /><br />!: $queryModified<br />";  
               if(!$_SESSION['superadmin'] && isset($subStrSearchReplace)){

                $arrDebug[]=481;$queryModified =  str_replace($subStrSearchReplace, " where instr('$strAccessSql', concat(\"{\", ".$prefix."id_domain, \"}\")) $subStrAdd ", $queryModified);
               }
              # 
            } elseif (stripos((trim($queryModified)), "select ") !== false && stripos(trim($queryModified), "from " . TABLE_CONTENT_MANAGER . "") !== false) {
               # Content Manager           
              # echo "<br /><br /><br /><br /><br />!: $queryModified<br />";
               if(isset($subStrSearchReplace) && $sqlCompare != "") {

                  $arrDebug[]=489;$queryModified = str_replace($subStrSearchReplace, " $subStrSearchReplace $sqlCompare $subStrAdd ", $queryModified);
               }
            }elseif (stripos((trim($queryModified)), "select ") !== false && stripos(trim($queryModified), "from " . TABLE_WHOS_ONLINE . "") !== false) {
               # Who's online
               if(!$_SESSION['superadmin'] && isset($subStrSearchReplace)) {

                  $arrDebug[]=495;$queryModified = str_replace($subStrSearchReplace, $subStrSearchReplace." instr('$strAccessSql', concat(\"{\", id_domain, \"}\")) and ", $queryModified);
               }
                #echo " !: $queryModified<br />";
            } elseif (stripos(trim($queryModified), " count(") == false && stripos(trim($queryModified), " distinct") == false && stripos(trim($queryModified), "" . TABLE_PRODUCTS . " ") !== false && stripos(trim($queryModified), " join " . TABLE_SPECIALS . "") !== false && stripos(trim($queryModified), "" . TABLE_PRODUCTS_DESCRIPTION . " ") !== false) {
               # Crosselling

               $arrDebug[]=501;$queryModified = str_replace("select ", " select distinct ", $queryModified);
               #echo " <br /><br /><br /><br /><br />!: $queryModified<br />"; 
            } elseif (stripos(trim($queryModified), " count(") == false && stripos(trim($queryModified), " distinct ") == false && stripos(trim($queryModified), "" . TABLE_CUSTOMERS . " ") !== false && stripos(trim($queryModified), "" . TABLE_ORDERS . "") !== false && stripos(trim($queryModified), "" . TABLE_ORDERS_PRODUCTS . " ") !== false) {
               # Kunden Bestellstatistik
               $prefix = "c.";
               if(isset($subStrSearchReplace)) {

                  $arrDebug[]=508;$queryModified = str_replace($subStrSearchReplace, $subStrSearchReplace." instr('$strAccessSql', concat(\"{\", ".$prefix."id_domain, \"}\")) and ", $queryModified);
               }
               #echo " <br /><br /><br /><br /><br />!: $queryModified<br />"; 
            } elseif(stripos(trim($queryModified), " count(") == false && stripos(trim($queryModified), ' '.TABLE_PRODUCTS.' ') !== false && stripos(trim($queryModified), ' '.TABLE_PRODUCTS_TO_CATEGORIES.' ') !== false && stripos(trim($queryModified), ' '.TABLE_PRODUCTS_DESCRIPTION.' ') !== false){ 
              # Meistverkaufte Artikel Statistik
              if(stripos(trim($queryModified), ' '.TABLE_CATEGORIES.' ') == false){ 
                 if(isset($subStrSearchReplace)) {

                  $arrDebug[]=516;$queryModified = str_replace($subStrSearchReplace, MULTISTORE_SQL_SEARCH_JOIN2 . $subStrSearchReplace, $queryModified);
                 }
                #echo " <br /><br /><br /><br /><br />!: $queryModified<br />";
              }               
            } elseif(stripos(trim($queryModified), " count(") == false && stripos(trim($queryModified), "pd.products_viewed") !== false &&  stripos(trim($queryModified), ' '.TABLE_PRODUCTS.' ') !== false && stripos(trim($queryModified), ' '.TABLE_PRODUCTS_TO_CATEGORIES.' ') == false && stripos(trim($queryModified), ' '.TABLE_CATEGORIES.' ') == false && stripos(trim($queryModified), ' '.TABLE_PRODUCTS_DESCRIPTION.' ') !== false){ 
               # Meistbesuchte Artikel Statistik
               $prefix = "c.";
               if(stripos(trim($queryModified), " distinct") == false) {

                $arrDebug[]=525;$queryModified = str_replace("select ", " select distinct ", $queryModified);
               }
                /*
               if(isset($subStrSearchReplace) && $sqlCompareC != "") {
                 $queryModified = str_replace($subStrSearchReplace, MULTISTORE_SQL_SEARCH_JOIN1 . MULTISTORE_SQL_SEARCH_JOIN2 .  $subStrSearchReplace, $queryModified);
                 $queryModified = str_replace($subStrSearchReplace, " where  ".$sqlCompareC." $subStrAdd ", $queryModified);
               } 
               $queryModified = str_replace($subStrAdd, " group by p.products_id $subStrAdd", $queryModified);   
               */ 
               #   echo " <br /><br /><br /><br />$subStrAdd<br />!: $queryModified<br />";
            }elseif(stripos(trim($queryModified), " sum(") !== false && stripos(trim($queryModified), ' '.TABLE_ORDERS.' ') !== false){  
               #echo " <br /><br /><br /><br /><br />!: $queryModified<br />";
            } 
        }
    } 
      
    $queryModified = strtolower($queryModified);
    if($queryTemp != $queryModified) {
       $queryModified = "$comment\n-- modified by multistore (" . join("/", $arrDebug) . ")\n" . $queryModified;
       return $queryModified;
    } else {
       return $query;
    }   
  }
?>