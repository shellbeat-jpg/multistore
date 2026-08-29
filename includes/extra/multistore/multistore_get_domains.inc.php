<?php

    function ms_cfg_input_email_language($parameters) {       
      // include needed function
      require_once(DIR_FS_INC.'parse_multi_language_value.inc.php');   

      // build input fileds
      $arr_email_fields = array(); 
      $email_field = '';            
      if (strpos($parameters[0], '||') !== false) { 
        $value = parse_multi_language_value($parameters[0], $parameters[3], true);
      }else{
        $value = $parameters[0];
      }
      if (trim($parameters[1]) == 'SMTP_PASSWORD') {
        $email_field .= xtc_draw_password_field(trim($parameters[1]).'_'.$parameters[2], $value,'size=40');
      } else {
        $email_field .= xtc_draw_input_field(trim($parameters[1]).'_'.$parameters[2], $value,'size=40');
      } 
      return $email_field;
    }
  
    function ms_build_order_id($data, $id=0) {   
      global $insert_id, $oID;
       
      if(MULTISTORE!='true')
         return false;
      
      if(!defined('ORDER_ID_NEXT') || ORDER_ID_NEXT < 1){
          if(is_object($data) && isset($data->info['order_id'])){
            return $data->info['order_id'];
         }elseif(isset($insert_id)){
            return $insert_id;
         }elseif(isset($oID)){
            return $oID;
         }elseif(isset($_GET['oID'])){
            return $_GET['oID'];
         }else{
            return $id;         
         } 
      } 
       
      if(is_object($data)) {    
         if(isset($data->info['date_purchased'])){
            $date = $data->info['date_purchased']; 
            $id_domain = $data->info['id_domain'];   
            $id_languages = $data->info['id_languages'];     
         }elseif(isset($data->date_purchased)){
            $date = $data->date_purchased;
            $id_domain = $data->id_domain;
            $id_languages = $data->id_languages;   
         }     
      
         if(isset($data->info['orders_id_shop']) && $data->info['orders_id_shop'] > 0){
            $orders_id = $data->info['orders_id_shop'];         
         }elseif(isset($data->orders_id_shop)){
            $orders_id = $data->orders_id_shop;         
         }elseif(isset($data->info['order_id'])){
            $orders_id = $data->info['order_id'];
         }elseif(isset($data->orders_id)){
            $orders_id = $data->orders_id;         
         }  
      }elseif(is_array($data) && isset($data['date_purchased'])) {
         $date = $data['date_purchased'];    
         $id_domain = $data['id_domain'];  
         $id_languages = $data['id_languages']; 
         
         if(isset($data['orders_id_shop']) && $data['orders_id_shop'] > 0){
            $orders_id = $data['orders_id_shop'];         
         }elseif(isset($data['orders_id'])){
            $orders_id = $data['orders_id'];
         }
      }
    
      if(!isset($id_domain))
        $id_domain = ID_DOMAIN;
      if(!isset($id_languages))
        $id_languages = (int) $_SESSION['languages_id']; 
      if($id>0)
        $orders_id = $id;  
      if($date!=''){
         $date = strtotime($date);
      } else{
         $date = time();
      } 
      $ORDERS_NR_TYPE = getMultistoreConfigValue('ORDERS_NR_TYPE', $id_domain, $id_languages);    
    
      if($ORDERS_NR_TYPE=='1'){
        return sprintf("%d%02d%02d-%05d", strftime("%y", $date), strftime("%m", $date), strftime("%d", $date), $orders_id);
      }elseif($ORDERS_NR_TYPE=='2'){
        return sprintf("%05d", $orders_id).'-'.$id_domain;
      }else{
        return $orders_id;
      }
    }
    
    # separate Bestellnummern pro Shop
    # Aufruf in: checkout_process.php
    function ms_get_order_id_next($ID_DOMAIN = 0) {
      if(ORDER_ID_NEXT < 1){        
            return 0;         
      } 
      if (!function_exists('xtc_get_shop_conf'))
      require_once(DIR_FS_INC . 'xtc_get_shop_conf.inc.php'); 
        if($ID_DOMAIN == 0)
          $ID_DOMAIN = ID_DOMAIN;   
      $domain_query = xtc_db_query("SELECT order_id_next FROM " . TABLE_DOMAINS . " WHERE domain_id = '$ID_DOMAIN'");
      $domain = xtc_db_fetch_array($domain_query);
        $order_id_next = $domain['order_id_next']+1;   
      $ORDER_IDS = xtc_get_shop_conf('ORDER_IDS_'. $ID_DOMAIN);
      $arrOrderIDS=explode(",",$ORDER_IDS);
      if(count($arrOrderIDS)>0){
            foreach ($arrOrderIDS AS $dID) {
                xtc_db_query("UPDATE " . TABLE_DOMAINS . " set order_id_next = '$order_id_next' where domain_id = '$dID'");
            }
        } 
        xtc_db_query("UPDATE " . TABLE_DOMAINS . " set order_id_next = '$order_id_next' where domain_id = '$ID_DOMAIN'");
        return $order_id_next;
    }   

    function minMdItems(){
    # > 0 auf 0 setzen - alle anderen löschen
            # Kategoriebeschreibungen reduzieren
            $query = xtc_db_query("SELECT  cd . * FROM " . TABLE_CATEGORIES_DESCRIPTION . " cd WHERE cd.domain_id = (SELECT min(domain_id) FROM " . TABLE_CATEGORIES_DESCRIPTION . " cd2 where cd2.categories_id = cd.categories_id) GROUP BY categories_id, language_id");
            while($data = xtc_db_fetch_array($query)){
            xtc_db_query("UPDATE " . TABLE_CATEGORIES_DESCRIPTION . " set domain_id = 0 where categories_id =   ".$data['categories_id']." and domain_id =  ".$data['domain_id']." and language_id =    ".$data['language_id']);
            }
      xtc_db_query("DELETE FROM " . TABLE_CATEGORIES_DESCRIPTION . " where domain_id > 0");
      # Produktbeschreibungen reduzieren
            $query = xtc_db_query("SELECT  pd . * FROM " . TABLE_PRODUCTS_DESCRIPTION . "   pd WHERE pd.domain_id = (SELECT min(domain_id) FROM " .   TABLE_PRODUCTS_DESCRIPTION . " pd2 where pd2.products_id = pd.products_id) GROUP BY products_id, language_id");
            while($data = xtc_db_fetch_array($query)){
            xtc_db_query("UPDATE " . TABLE_PRODUCTS_DESCRIPTION . " set domain_id = 0 where products_id =   ".$data['products_id']." and domain_id =    ".$data['domain_id']." and language_id =    ".$data['language_id']);
            }
      xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_DESCRIPTION . " where domain_id > 0");
    }

    function maxMdItems(){
      # 0 auf vorhandene Domains verteilen und anschliessend löschen
    $domain_array = xtc_get_domains();
    # Kategoriebeschreibungen kopieren
      $query = xtc_db_query("SELECT * FROM " . TABLE_CATEGORIES_DESCRIPTION . " where domain_id = 0");
        while($data = xtc_db_fetch_array($query)){
          for ($i = 0, $n = sizeof($domain_array); $i < $n; $i++) {
            $id_domain = $domain_array[$i]['id'];
            $compare = xtc_db_query("SELECT * FROM " . TABLE_CATEGORIES_DESCRIPTION . " where domain_id = $id_domain and categories_id =    ".$data['categories_id']." and language_id =    ".$data['language_id']);
                        if(xtc_db_num_rows($compare)>0){
                          xtc_db_query("UPDATE " . TABLE_CATEGORIES_DESCRIPTION . " set
                                categories_name = '".xtc_db_input($data['categories_name'])."',
                                            categories_heading_title = '".xtc_db_input($data['categories_heading_title'])."',
                                            categories_description = '".xtc_db_input($data['categories_description'])."',
                                            categories_meta_title = '".xtc_db_input($data['categories_meta_title'])."',
                                categories_meta_description = '".xtc_db_input($data['categories_meta_description'])."',
                                            categories_meta_keywords = '".xtc_db_input($data['categories_meta_keywords'])."'
                                            where domain_id = $id_domain and categories_id =    ".$data['categories_id']." and language_id =    ".$data['language_id']);
                        }else{
                xtc_db_query("INSERT INTO " . TABLE_CATEGORIES_DESCRIPTION . "
                                (categories_id, domain_id, language_id, categories_name,    categories_heading_title,   categories_description, categories_meta_title, categories_meta_description, categories_meta_keywords)
                                 value(".$data['categories_id'].", $id_domain, ".$data['language_id'].", '".xtc_db_input($data['categories_name'])."', '".xtc_db_input($data['categories_heading_title'])."','".xtc_db_input($data['categories_description'])."','".xtc_db_input($data['categories_meta_title'])."','".xtc_db_input($data['categories_meta_description'])."','".xtc_db_input($data['categories_meta_keywords'])."')");
                        }
          }
        }
    xtc_db_query("DELETE FROM " . TABLE_CATEGORIES_DESCRIPTION . " where domain_id = 0");
    # Produktbeschreibungen kopieren
        $query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_DESCRIPTION . " where domain_id = 0");
        while($data = xtc_db_fetch_array($query)){
          for ($i = 0, $n = sizeof($domain_array); $i < $n; $i++) {
            $id_domain = $domain_array[$i]['id'];
            $compare = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_DESCRIPTION . " where domain_id = $id_domain and products_id =    ".$data['products_id']." and language_id =  ".$data['language_id']);
                        if(xtc_db_num_rows($compare)>0){
                          xtc_db_query("UPDATE " . TABLE_PRODUCTS_DESCRIPTION . " set
                                products_name = '".xtc_db_input($data['products_name'])."',
                                            products_description = '".xtc_db_input($data['products_description'])."',
                                            products_short_description = '".xtc_db_input($data['products_short_description'])."',
                                            products_keywords = '".xtc_db_input($data['products_keywords'])."',
                                products_meta_title = '".xtc_db_input($data['products_meta_title'])."',
                                            products_meta_description = '".xtc_db_input($data['products_meta_description'])."',
                                            products_meta_keywords = '".xtc_db_input($data['products_meta_keywords'])."',
                                            products_url = '".xtc_db_input($data['products_url'])."',
                                            products_viewed = '".$data['products_viewed']."'
                                            where domain_id = $id_domain and products_id =  ".$data['products_id']." and language_id =  ".$data['language_id']);
                        }else{
                xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_DESCRIPTION . "
                                (products_id, domain_id, language_id, products_name,    products_description,   products_short_description, products_keywords, products_meta_title, products_meta_description, products_meta_keywords, products_url, products_viewed)
                                 value(".$data['products_id'].", $id_domain, ".$data['language_id'].", '".xtc_db_input($data['products_name'])."', '".xtc_db_input($data['products_description'])."','".xtc_db_input($data['products_short_description'])."','".xtc_db_input($data['products_keywords'])."','".xtc_db_input($data['products_meta_title'])."','".xtc_db_input($data['products_meta_description'])."','".xtc_db_input($data['products_meta_keywords'])."','".xtc_db_input($data['products_url'])."','".$data['products_viewed']."')");
                        }
          }
        }
    xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_DESCRIPTION . " where domain_id = 0");
    }

    function countMdItems($activate=true){
      $activate==true?$op="=":$op=">";
      $query = xtc_db_query("select count(categories_name) as total  from " . TABLE_CATEGORIES_DESCRIPTION . " where domain_id $op 0");
      $data = xtc_db_fetch_array($query);
      $total=$data['total'];
            $query = xtc_db_query("select count(products_name) as total  from " . TABLE_PRODUCTS_DESCRIPTION . " where domain_id $op 0");
      $data = xtc_db_fetch_array($query);
      $total+=$data['total'];
      return $total;
    }


    function getMultistoreConfiguration($ID_DOMAIN=0, $ID_LANGUAGE=0){
            if($ID_DOMAIN==0)$ID_DOMAIN=ID_DOMAIN;       
            if($ID_LANGUAGE==0)$ID_LANGUAGE=$_SESSION['languages_id'];
            $domain_config_query = xtc_db_query("SELECT *  FROM " . TABLE_DOMAINS_CONFIGURATION . " WHERE domain_id='" . $ID_DOMAIN . "' and language_id = '".$ID_LANGUAGE."'");
      if(xtc_db_num_rows($domain_config_query)){
            while($domain_config = xtc_db_fetch_array($domain_config_query)){
                    if($domain_config['value']!='') {
                define('MS_'.$domain_config['constant'], $domain_config['value']);
                    }
              }
            }
            # Original Email Konstanten
      $original_config_query = xtc_db_query("SELECT *  FROM " . TABLE_CONFIGURATION . " where configuration_group_id  = 12");
        while($config = xtc_db_fetch_array($original_config_query)){
        define('MS_'.$config['configuration_key'], $config['configuration_value']);
        $arrReturn[$config['configuration_key']] = constant('MS_'.$config['configuration_key']);
          }
          return $arrReturn;
    }


    function getPreconfig(){
        global $_GET, $arrMsPreconfig, $HTTP_SERVER, $CURRENT_TEMPLATE;
        $arrPreconfig=array();
      $SCRIPT_NAME = basename($_SERVER['SCRIPT_NAME']);
      if(in_array($SCRIPT_NAME, $arrMsPreconfig)){
      if(isset($_GET['cID'])){
         $cID = (int) $_GET['cID'];
            }
            if(isset($_GET['oID'])){
                 $oID = (int) $_GET['oID'];
            }
        }
        if(isset($oID)){
         require_once (DIR_WS_CLASSES.'order.php');
         $order = new order($oID);
         $CURRENT_TEMPLATE = xtc_get_template_by_domain ($order->info['id_domain'], xtc_get_domains());
             $HTTP_SERVER =  xtc_get_domain_server($order->info['id_domain'], (ENABLE_SSL_CATALOG == 'true'?'https':'http'));
             define('CURRENT_TEMPLATE', $CURRENT_TEMPLATE);
             ms_initConstants($order->info['id_domain'], $order->info['id_languages']);
        } elseif(isset($cID)){
        }
    }

  function checkAdminAccess($stringDomains='', $categories_id = 0){
        global $arrAccess, $category_root, $_GET;
                             
   if($_SESSION['customer_id']==1 || sizeof($arrAccess)<1)
      return true;

     if($stringDomains!=''){
       $arrDomain = explode(";", $stringDomains);
         for ($i = 0, $n = sizeof($arrDomain); $i < $n; $i++) {
             for ($j = 0, $m = sizeof($arrAccess); $j < $m; $j++) {
                 if($arrDomain[$i] == $arrAccess[$j])
                  return true;
             }
         }
     }

     if($category_root>0){
         $group_check = '';
         if(GROUP_CHECK == 'true' && isset($_SESSION['customers_status_id'])) {                  
             $group_check = " AND group_permission_". $_SESSION['customers_status_id'] ."=1 ";
         }
     $categories_query = "select * from " . TABLE_CATEGORIES . " where categories_id = '" . $category_root . "'".$group_check;
         $categories_query = xtDBquery($categories_query);
         $categories = xtc_db_fetch_array($categories_query,true);
       $arrDomain = explode(";", $categories['string_domains']);
         for ($i = 0, $n = sizeof($arrDomain); $i < $n; $i++) {
             for ($j = 0, $m = sizeof($arrAccess); $j < $m; $j++) {
                 if($arrDomain[$i] == $arrAccess[$j])
                  return true;
             }
         }
     }

     if($categories_id > 0){
       for ($i = 0, $m = sizeof($arrAccess); $i < $m; $i++) {
                 if(xtc_count_products_in_domain($categories_id, $arrAccess[$i], true)>0)
                  return true;
         }
   }
 }

 function str_email($strEmail, $host = ''){
    return $strEmail;
        global $_SERVER;
        if($host=='')$host = $_SERVER["HTTP_HOST"];
        $arrTemp = explode("@", $strEmail);
        $arrTemp2 = explode(".", $host);
        $arrTemp2[0]='';
        $strTemp=join(".", $arrTemp2);
        $strTemp=substr($strTemp, 1, strlen($strTemp)-1);
        return $arrTemp[0] . "@" . $strTemp;
 }

 function getArrDomainLang($domain_array_active, $domain_array, $languages ){
     $j=0;
     for ($d = 0; $d < sizeof($domain_array_active); $d++) {
      $id_domain = $domain_array_active[$d];
      if($id_domain>0){
          $arrDomainLang[$j]=array('id_domain'=>$id_domain);
            for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
             if(xtc_validate_domain($id_domain, $languages[$i]['id'])){
                for ($k=0; $k<sizeof($domain_array); $k++) {
                    if ($domain_array[$k]['id']==$id_domain) {
                  $domain_name = $domain_array[$k]['text'];
                  $arrDomainLang[$j]['text']=$domain_name;
                    }
                }
                $arrDomainLang[$j]['id_lang'][]=$languages[$i]['id'];
                $arrDomainLang[$j]['indexLang'][]=$i;
             }
          }
          $j++;
        }


     }
     return $arrDomainLang;
 }

 function admin_access_language($customers_id, $languages_id=0){
    $query_raw = "SELECT *  FROM " . TABLE_ADMIN_ACCESS_LANGUAGES . " WHERE customers_id ='$customers_id'";
        if($languages_id>0){
       $query_raw .= " and languages_id = '$languages_id'";
         $query = xtc_db_query($query_raw);
       return (xtc_db_num_rows($query)>0);
        }else{
       $arr_return=array();
         $query = xtc_db_query($query_raw);
         while($languages = xtc_db_fetch_array($query)){
           $arr_return[] = $languages['languages_id'];
         }
         return $arr_return ;
        }
 }

 function admin_access_domains($customers_id, $domain_id=0){
     global $sqlCompare, $sqlCompareC;
     $query_raw = "SELECT *  FROM " . TABLE_ADMIN_ACCESS_DOMAINS . " WHERE customers_id ='$customers_id'";
     
     $sqlCompare = $sqlCompareC = "";
        if($domain_id>0){
       $query_raw .= " and domain_id = '$domain_id'";
         $query = xtc_db_query($query_raw);
       return (xtc_db_num_rows($query)>0);
        }else{
       $arr_return=array();
         $query = xtc_db_query($query_raw);
         while($domains = xtc_db_fetch_array($query)){
           $arr_return[] = $domains['domain_id'];            
         }
          

         if(count($arr_return)>0){
            return $arr_return ;
             } else {
            $_SESSION['superadmin'] = true;     
            $query = xtc_db_query("SELECT *  FROM " . TABLE_DOMAINS);
                while($domains = xtc_db_fetch_array($query)){
                  $arr_return[] = $domains['domain_id'];
                   $sqlCompare .= str_replace("%s", $domains['domain_id'], MULTISTORE_SQL_SEARCH_WHERE2d) . " OR ";
                }
                if($sqlCompare != ""){
                    $sqlCompare = " (" . substr($sqlCompare, 0, strlen($sqlCompare)-4) . ")";
                    $sqlCompareC = str_replace("string_domains", "c.string_domains", $sqlCompare);
                }
                
            return $arr_return ;
             }
        }
 }

 function xtc_get_host() {

    $HTTP_HOST = $_SERVER['HTTP_HOST'];

  if( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) && ($_SERVER['HTTP_X_FORWARDED_HOST']!=$_SERVER['HTTP_HOST']))
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];


    # SSL Proxy entfernen
  #if(!empty($_SERVER['HTTP_X_FORWARDED_SERVER']))
  #  $HTTP_HOST = str_replace($_SERVER['HTTP_X_FORWARDED_SERVER'] . '/', '', $HTTP_HOST);
  $http_host = strtolower($HTTP_HOST);
  $http_host2 = strtolower(getenv("HTTP_HOST"));
  if( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) && ($_SERVER['HTTP_X_FORWARDED_HOST']!=$http_host2))
    $http_host2 = $_SERVER['HTTP_X_FORWARDED_HOST'];
  $http_host = ($http_host == $http_host2) ? array($http_host) : array($http_host, $http_host2);
  #var_dump($http_host);
  if($http_host) return $http_host;
 }

 function xtc_get_sqlField() {
        global $request_type ;
      #(getenv('HTTPS')=='1'||getenv('HTTPS')=='on'||substr($_SERVER["SCRIPT_URI"], 0, 5)=='https')?$http_field='domain_https':$http_field='domain_http';
      $request_type=='SSL'?$http_field='domain_https':$http_field='domain_http';
        return $http_field;
 }

 # MULTISTORE MULTISELECT
 function xtc_check_domain($string_domains, $categories_id=0) {
     $arrDomain = explode(";", $string_domains);
     if(ID_DOMAIN > 0 && in_array(ID_DOMAIN, $arrDomain))   #$string_domains = '' ||
      return true;
   if($categories_id > 0){

      $count_products = xtc_count_products_in_domain($categories_id, ID_DOMAIN);
      if($count_products>0){
        return $count_products+1;
            }else{
        return false;
            }
     } else {
     return false;
     }

 }
  function sprintf_domain($ID_DOMAIN=0, $p=0){
        if($ID_DOMAIN==0)
          $ID_DOMAIN = ID_DOMAIN;
    if($p==0){
         return sprintf(MULTISTORE_SQL_SEARCH_WHERE2d, $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, $ID_DOMAIN, $ID_DOMAIN);
        }elseif($p==2){
         return sprintf(MULTISTORE_SQL_SEARCH_WHERE2da, $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, $ID_DOMAIN, $ID_DOMAIN, $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, $ID_DOMAIN, $ID_DOMAIN);
        } elseif($p==1){  # MULTISTORE_SQL_SEARCH_WHERE2e
             return " AND " . sprintf(MULTISTORE_SQL_SEARCH_WHERE2da, $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, $ID_DOMAIN, $ID_DOMAIN, $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, $ID_DOMAIN, $ID_DOMAIN);
        }
    }

  function xtc_count_products_in_domain($category_id, $ID_DOMAIN, $include_inactive = false) {
    $group_check = '';
    $group_check = '';
    if(GROUP_CHECK == 'true' && isset($_SESSION['customers_status_id'])) {
            $group_check = " AND p.group_permission_".$_SESSION['customers_status_id']."=1 ";
      $group_check_c = " AND group_permission_".$_SESSION['customers_status_id']."=1 ";
        }
        # MULTISTORE MULTISELECT
    $domain_check = sprintf_domain($ID_DOMAIN);
    $products_count = 0;
    /*
    if ($include_inactive == true) {
     $products_query = "select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . $category_id . "' AND ".$domain_check.$group_check;
    } else {
         $products_query = "select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . $category_id . "' AND ".$domain_check.$group_check;
        }

    $products_query = xtDBquery($products_query);
    $products = xtc_db_fetch_array($products_query,true);
    $products_count += $products['total'];
        if($products_count>0)
           return true;
    */

    $categories_query = xtDBquery("select count(*) as total from " . TABLE_CATEGORIES . " where categories_id = '" . $category_id . "' AND ".$domain_check.$group_check_c);
    $categories = xtc_db_fetch_array($categories_query,true);
        if($categories['total']>0)
           return true;

    $child_categories_query = "select categories_id from " . TABLE_CATEGORIES . " where  parent_id = '" . $category_id . "' ".$group_check_c;
        $child_categories_query = xtDBquery($child_categories_query);
    if (xtc_db_num_rows($child_categories_query,true)) {
      while ($child_categories = xtc_db_fetch_array($child_categories_query,true)) {
        $products_count += xtc_count_products_in_domain($child_categories['categories_id'], $ID_DOMAIN, $include_inactive);
                if($products_count>0)
                   return true;
            }
    }
    return ($products_count>0);
  }


 function xtc_validate_product($string_domains, $categories_id, $ID_DOMAIN){

    $arrDomains=explode(";", $string_domains);
        if(in_array($ID_DOMAIN, $arrDomains))
             return true;
        $rootCategory = getRootCategory($categories_id);
    $child_categories_query = "select string_domains from " . TABLE_CATEGORIES . " where categories_id = '" . $rootCategory . "'";
        $child_categories_query = xtDBquery($child_categories_query);
        $child_categories = xtc_db_fetch_array($child_categories_query,true);
        $arrDomains=explode(";", $child_categories['string_domains']);
        if(in_array($ID_DOMAIN, $arrDomains))
             return true;
        return false;
 }

 function getRootCategory($category_id){
    $child_categories_query = "select categories_id, parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . $category_id . "'";
        $child_categories_query = xtDBquery($child_categories_query);
    if (xtc_db_num_rows($child_categories_query,true)) {
      $child_categories = xtc_db_fetch_array($child_categories_query,true);
        if($child_categories['parent_id']<1)
          return $category_id;
        return getRootCategory($child_categories['parent_id']);
    }
    $child_categories_query = "select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . $category_id . "'";
    $child_categories_query = xtDBquery($child_categories_query);
    $child_categories = xtc_db_fetch_array($child_categories_query,true);
    if($child_categories['parent_id']<1)
      return $category_id;
 }



function xtc_get_array_domains(){
  $domain_query_raw = "SELECT *  FROM " . TABLE_DOMAINS;
  $domain_query = xtc_db_query($domain_query_raw);
    while($domain = xtc_db_fetch_array($domain_query)){
   $arrReturn[$domain['domain_id']] = getFirstDomain($domain['domain_http']);
    }
    return $arrReturn;
}

function xtc_getUrl_byString($string_domains='', $arrUrl, $delimiter = '<br>'){
    if(!empty($string_domains)){
     $arrDomains=explode(";", $string_domains);
         for($i=0; $i < count($arrDomains); $i++){
         $id = (int) $arrDomains[$i];
         if(isset($arrUrl[$id]))
          $strReturn .= $arrUrl[$id] . $delimiter;
     }
     return $strReturn;
    }
}

 function xtc_get_sql_whereIn($MULTISTORE_SQL, $category_id){
        $subcategories_array = array ();
        xtc_get_subcategories($subcategories_array, $category_id);
        $SQL_MULTISTORE = sprintf($MULTISTORE_SQL, $category_id);
        foreach ($subcategories_array AS $scat) {
            $SQL_MULTISTORE .= ", '".$scat."'";
        }
        $SQL_MULTISTORE .= ") ";
        return $SQL_MULTISTORE;
 }


 function xtc_get_domains($string_domains = '', $arr_return = Null, $status="=1", $delimiter=' ', $checkAccess=false) {
    global $strAccessSql, $arrAccess;     	

    if($_SESSION['customer_id']==1 || sizeof($arrAccess)<1)
      $checkAccess = false;
    $arrDomains=explode(";", $string_domains);
    $domain_query_raw = "SELECT *  FROM " . TABLE_DOMAINS . " WHERE domain_status $status";
    if($checkAccess)
     $domain_query_raw .= " AND INSTR('$strAccessSql', concat(\"{\", domain_id, \"}\")) ";
    if($string_domains != '' && is_string($string_domains) && $string_domains!='az' && count($arrDomains)>0){
      $domain_query_raw .= " and (";
    for($i=0; $i < count($arrDomains); $i++){
            if(!empty($arrDomains[$i]) && $arrDomains[$i]>0)
            $domain_query_raw .= " domain_id = ". $arrDomains[$i];
        if($i < count($arrDomains)-1 && !empty($arrDomains[$i+1])  && $arrDomains[$i]>0)
          $domain_query_raw .= " or ";
    }
    $domain_query_raw .= ")";
    $domain_query_raw = str_replace("and ()", "", $domain_query_raw);      
    $domain_query = xtc_db_query($domain_query_raw);
        while($domain = xtc_db_fetch_array($domain_query)){
             $strReturn .= getFirstDomain($domain['domain_http']).$delimiter;
        }
  
    return (is_string($arr_return)?$arr_return :'').trim($strReturn);
  }elseif((is_numeric($string_domains) && $string_domains > 0) || $string_domains=='az'){
    $domain_query = xtc_db_query($domain_query_raw);
    $arr_return = array();   
    while($domain = xtc_db_fetch_array($domain_query)){
       $arr_return[$domain['domain_id']]['text'] = getFirstDomain($domain['domain_http']);
             if(is_numeric($string_domains) && $string_domains > 0){
                 $SQL_MULTISTORE = xtc_get_sql_whereIn(MULTISTORE_SQL_SEARCH_WHERE1a, $string_domains);
           $description_query = xtc_db_query("select distinct pd.products_id from (".TABLE_PRODUCTS_DESCRIPTION." pd left join ".TABLE_PRODUCTS." p on pd.products_id = p.products_id) left join ".TABLE_PRODUCTS_TO_CATEGORIES." p2c on p.products_id = p2c.products_id where pd.domain_id = '".$domain['domain_id']."' $SQL_MULTISTORE");
           $total = xtc_db_num_rows($description_query);
           #$arr_return[$domain['domain_id']]['ptotal'] = $total;
                 $SQL_MULTISTORE = xtc_get_sql_whereIn(MULTISTORE_SQL_SEARCH_WHERE1ab, $string_domains);
           $description_query = xtc_db_query("select distinct c.categories_id from (" . TABLE_CATEGORIES . " c left join " . TABLE_CATEGORIES_DESCRIPTION . " cd on c.categories_id = cd.categories_id) where cd.domain_id = '".$domain['domain_id']."' and (1=1 $SQL_MULTISTORE or c.categories_id = $string_domains)"); #and cd.categories_name != ''
                 #echo "select distinct c.categories_id from (" . TABLE_CATEGORIES . " c left join " . TABLE_CATEGORIES_DESCRIPTION . " cd on c.categories_id = cd.categories_id) where cd.domain_id = '".$domain['domain_id']."' $SQL_MULTISTORE"    ;


                 $total+= xtc_db_num_rows($description_query);
           $arr_return[$domain['domain_id']]['ctotal'] = $total;
             }
    }  
    return $arr_return;
  }else{
    $domain_query = xtc_db_query($domain_query_raw);    
    $arr_fields=array();
    $i=0;
    if(DB_MYSQL_TYPE == 'mysqli'){
      while ($i < mysqli_num_fields($domain_query)) {
          $meta = xtc_db_fetch_fields($domain_query, $i);
          $arr_fields[]=$meta->name;
          $i ++;
      }
    } else {
      while ($i < mysql_num_fields($domain_query)) {
          $meta = xtc_db_fetch_fields($domain_query, $i);
          $arr_fields[]=$meta->name;
          $i ++;
      }
    }

    if(!isset($arr_return))
       $arr_return = array();
    $d=count($arr_return);
    
    while($domain = xtc_db_fetch_array($domain_query)){
              $arr_return[$d] = array('id' => $domain['domain_id'], 'text' => getFirstDomain($domain['domain_http']), 'template' => $domain['current_template']);
        for($i=0; $i < count($arr_fields); $i++){
            $arr_return[$d][$arr_fields[$i]] = $domain[$arr_fields[$i]];
                }
                $d++;
        }
    return $arr_return;
  }
 }

 function xtc_get_template_by_domain($domain_id, $domain_array) {
  for($i=0; $i < count($domain_array); $i++){
    if($domain_array[$i]['id'] == $domain_id)
      return $domain_array[$i]['template'];
  }
  return CURRENT_TEMPLATE;
 }

 function xtc_validate_currency($domain_id = 0, $currencies_id) {
    $domain_query = xtc_db_query("SELECT *  FROM " . TABLE_CURRENCIES_TO_DOMAINS . " WHERE currencies_id = '$currencies_id' and domain_id  = '$domain_id'");
        if(xtc_db_num_rows($domain_query))
         return true;
 }

 function xtc_validate_domain($domain_id = 0, $language_id) {
        $domain_query = xtc_db_query("SELECT *  FROM " . TABLE_LANGUAGES_TO_DOMAINS . " WHERE languages_id = '$language_id' and domain_id  = '$domain_id'");
        if(xtc_db_num_rows($domain_query))
         return true;
 }

 function xtc_js_languages4domains($domain_id, $arrayValues, $arrayLanguageCodes) {
    $domain_query = xtc_db_query("SELECT *  FROM " . TABLE_LANGUAGES_TO_DOMAINS . " where domain_id  = '$domain_id' order by languages_id");
        $arrData=array();
        for($i=0; $i < count($arrayLanguageCodes); $i++){
            $str_return  .= "arrayLanguageCodes['".$arrayLanguageCodes[$i]['id']."'] = new Array();\n";
            $str_return  .= "arrayLanguageCodes['".$arrayLanguageCodes[$i]['id']."']['id'] = ".$arrayLanguageCodes[$i]['idLang'].";\n";
            $str_return  .= "arrayLanguageCodes['".$arrayLanguageCodes[$i]['id']."']['code'] = '".$arrayLanguageCodes[$i]['id']."';\n";
        $str_return  .= "arrayLanguageCodes['".$arrayLanguageCodes[$i]['id']."']['text'] = '".$arrayLanguageCodes[$i]['text']."';\n";
        $arrCode[$arrayLanguageCodes[$i]['idLang']]=$arrayLanguageCodes[$i]['id'];
        }
        $str_return  .= "\n\n";
        if(xtc_db_num_rows($domain_query)){
            $str_return .= "arrDomain[$domain_id] = new Array();\n";
            $t=0;
            while($domain = xtc_db_fetch_array($domain_query)){
              $str_return  .= "arrDomain[$domain_id][$t] = new Array();\n";
                $str_return  .= "arrDomain[$domain_id][$t]['id'] = ".$domain['languages_id'].";\n";
                $str_return  .= "arrDomain[$domain_id][$t]['code'] = '".$arrCode[$domain['languages_id']]."';\n";
                $str_return  .= "arrDomain[$domain_id][$t]['language'] = '".$arrayValues[$domain['languages_id']]."';\n";
        $t++;
            }
        }
        $str_return  .= "\n\n";
        return $str_return;
 }

 function xtc_js_domains4languages($languages_id, $array) {
    $domain_query = xtc_db_query("SELECT *  FROM " . TABLE_LANGUAGES_TO_DOMAINS . " where languages_id  = '$languages_id' order by domain_id");
        $arrData=array();
        if(xtc_db_num_rows($domain_query)){
            $str_return .= "arrLanguage[$languages_id] = new Array();\n";
            $t=0;
        while($domain = xtc_db_fetch_array($domain_query)){
        $str_return  .= "arrLanguage[$languages_id][$t] = new Array();\n";
                $str_return  .= "arrLanguage[$languages_id][$t]['id'] = ".$domain['domain_id'].";\n";
                $str_return  .= "arrLanguage[$languages_id][$t]['domain'] = '".$array[$domain['domain_id']]."';\n";
        $t++;
            }
        }
        $str_return  .= "\n\n";
        return $str_return;
 }

 function xtc_get_categoriesDomain($categories_id) {
    $domain_query = xtc_db_query("SELECT string_domains  FROM " . TABLE_CATEGORIES . " where categories_id  = '$categories_id'");
    $domain = xtc_db_fetch_array($domain_query);
    $arrDomain = explode(";", $domain['string_domains']);
    return $arrDomain[0];
 }


 function getArrayActiveDomains($categories_id, $pID=0, $checkAccess = false, $strict = false) {
     global $cPath_array, $strAccessSql, $arrAccess;
     # D.L. 2016
     if($_SESSION['customer_id']==1 || sizeof($arrAccess)<1)
        $checkAccess = false;
        
     $arrDomain = array();
     if($cPath_array[1]>0)
        $categories_id = $cPath_array[1];
     if($categories_id>0){
            # kategorien
        $domain_query = xtc_db_query("SELECT string_domains  FROM " . TABLE_CATEGORIES . " where categories_id  = '$categories_id'");
        $domain = xtc_db_fetch_array($domain_query);
       
        if($domain['string_domains']!=''){          
         if($checkAccess){
           $arrDomain_temp = explode(";", $domain['string_domains']);
           for ($i = 0; $i < sizeof($arrDomain_temp); $i++) {
                if((count($arrAccess) < 1 || in_array($arrDomain_temp[$i], $arrAccess)) && !in_array($arrDomain_temp[$i], $arrDomain) && $arrDomain_temp[$i] != '')
                  
                  $arrDomain[]=$arrDomain_temp[$i];
           }          
         } else{
           $arrDomain = explode(";", $domain['string_domains']);
         }             
            } 
        
      
            if($pID==0){
                # Aufruf nur aus der Kategoriebearbeitung
                # In Produkte zugeordnet
                $domain_query_raw = "SELECT *  FROM " . TABLE_DOMAINS;
                if($checkAccess)
                    $domain_query_raw .= " WHERE INSTR('$strAccessSql', concat(\"{\", domain_id, \"}\")) ";
            $domain_query_raw = xtc_db_query($domain_query_raw);
                while($domain = xtc_db_fetch_array($domain_query_raw)){
            if(!in_array($domain['domain_id'], $arrDomain) && $domain['domain_id'] != '' && xtc_count_products_in_domain($categories_id, $domain['domain_id']) > 0)
                        $arrDomain[]=$domain['domain_id'];
          }
            }


   }

   if($pID>0){
          $products_query=xtc_db_query("SELECT * FROM ".TABLE_PRODUCTS."   WHERE products_id='".$pID."'");
          $product=xtc_db_fetch_array($products_query);
          if($product['string_domains']!=''){
         $arrTemp = explode(";", $product['string_domains']);
         for ($i = 0; $i < sizeof($arrTemp); $i++) {
              if(!in_array($arrTemp[$i], $arrDomain) && $arrTemp[$i] != '')
                $arrDomain[]=$arrTemp[$i];
         }
            }
     }
     if($strict == false && count($arrDomain)<1){
            $domain_query_raw = "SELECT *  FROM " . TABLE_DOMAINS . " WHERE domain_status = 1";
            if($checkAccess)
                $domain_query_raw .= " AND INSTR('$strAccessSql', concat(\"{\", domain_id, \"}\")) ";
        $domain_query_raw = xtc_db_query($domain_query_raw);
            while($domain = xtc_db_fetch_array($domain_query_raw)){
                 $arrDomain[]=$domain['domain_id'];
            }
     }
     for ($i = 0; $i < sizeof($arrDomain); $i++) {
        if($arrDomain[$i]=="") unset($arrDomain[$i]);
     }
     
     
     sort($arrDomain); 
   return $arrDomain;
 }

 function xtc_get_domain_server($dID=0, $http = 'http') {
    if($dID<1)
             return HTTP_CATALOG_SERVER;
    $domain_query = xtc_db_query("SELECT    *  FROM " . TABLE_DOMAINS . " where domain_id     = '$dID'");
        if(xtc_db_num_rows($domain_query) < 1)
             return HTTP_CATALOG_SERVER;
        $domain = xtc_db_fetch_array($domain_query);
    return $http.'://' . getFirstDomain($domain['domain_'.$http]);
 }

 function xtc_sql_pwhere($cDomain=0) {
  # if($cDomain!=ID_DOMAIN && $cDomain > 0)
  #    return MULTISTORE_SQL_PRODUCTS_WHERE;
  return "";
 }

        function getArrayDomains($addEmpty=false, $checkAccess = false, $status = "=1"){
         $domain_array = array();
         $domain_array[] = array('id' => '', 'text' => TEXT_ALL, 'template' => '');
         if($addEmpty)
          $domain_array[] = array('id' => -1, 'text' => SELECT_NOSELECTION, 'template' => '');              
             $domain_array = xtc_get_domains('', $domain_array, $status, ' ', $checkAccess);
             
             return $domain_array;
        }

        function getCurrentTemplate($parent_category_id=0, $domain_array){
         $CURRENT_TEMPLATE = CURRENT_TEMPLATE;
         #if(MULTIDOMAINS!='true')
             #    $CURRENT_TEMPLATE = xtc_get_template_by_domain(xtc_get_categoriesDomain($parent_category_id), $domain_array);
             return $CURRENT_TEMPLATE;
        }

    function setArrayDomains($languages, $domain_array , $arrayLanguages, $languages_array){
        $strArrayDomains = "";
        $strArrayDomains = "\n<script>\n";
        $strArrayDomains .= "arrDomain = new Array();\n";
        $strArrayDomains .= "arrayLanguageCodes = new Array();\n";
        $strArrayDomains .= "arrLanguage = new Array();\n";
        $strArrayDomains .= "arrayDomainCodes = new Array();\n";
      for($i=1; $i < count($domain_array); $i++){
        $arrayDomain[$domain_array[$i]['id']] = $domain_array[$i]['text'];
        $strArrayDomains .= "arrayDomainCodes[".($i-1)."] = new Array();\n";
            $strArrayDomains .= "arrayDomainCodes[".($i-1)."]['id'] = ".$domain_array[$i]['id'].";\n";
            $strArrayDomains .= "arrayDomainCodes[".($i-1)."]['text'] = '".$domain_array[$i]['text']."';\n";
      }

        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $strArrayDomains .= xtc_js_domains4languages($languages[$i]['id'], $arrayDomain);
        }

        for ($i = 1, $n = sizeof($domain_array); $i < $n; $i++) {
            $strArrayDomains .= xtc_js_languages4domains($domain_array[$i]['id'], $arrayLanguages, $languages_array);
        }
        $strArrayDomains .= "</script>";
        return $strArrayDomains;
    }

function checkSessionKey($constant){
  $domain_query = xtc_db_query("SELECT * FROM " . TABLE_DOMAINS_CONFIGURATION . " where domain_id  = '".ID_DOMAIN."' and constant = '".$constant."'");
    return (!xtc_db_num_rows($domain_query));
}

function max_post_vars(){
    $max_input_vars = (int) ini_get('max_input_vars');
    if($max_input_vars > 0){
      $max_input_vars = $max_input_vars -100;
      $query_total = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE language_id = '" . $_SESSION['languages_id'] . "'";
      $query_total = xtc_db_query($query_total);
      $matches_total = xtc_db_num_rows($query_total)*8;
        return ($matches_total<$max_input_vars);
    }else{
    return true;
    }
}

function xtc_get_store_name($ID_DOMAIN=0) {
  if($ID_DOMAIN==0)
        $ID_DOMAIN=ID_DOMAIN;
  $domain_query = xtc_db_query("SELECT value FROM " . TABLE_DOMAINS_CONFIGURATION . " where constant = 'STORE_NAME' and domain_id     = '".$ID_DOMAIN."' and language_id = '".$_SESSION['languages_id']."'");
    if(!xtc_db_num_rows($domain_query))
      return STORE_NAME;
    $domain = xtc_db_fetch_array($domain_query);
    if(empty($domain['value']))
      return STORE_NAME;
  return  $domain['value'];
}

function ms_initConstants($ID_DOMAIN=0, $language_id = 0, $source = ''){
  global $array_configuration;
  if($ID_DOMAIN==0)$ID_DOMAIN=ID_DOMAIN;
  if($language_id==0 && !$language_id=$_SESSION['languages_id']) $language_id=2;
  if($source != '')
    $source = " and ($source)";
    if($language_id > 0){
        $domain_description_query = xtc_db_query("SELECT constant, value  FROM " . TABLE_DOMAINS_CONFIGURATION . " WHERE domain_id='" . $ID_DOMAIN . "' and language_id = '".$language_id."' $source");
    while($domain_description = xtc_db_fetch_array($domain_description_query)){
        if(!empty($domain_description['value']) || $domain_description['value']=='0'){
          if($domain_description['constant'] == 'ENABLE_SSL' || $domain_description['constant'] == 'USE_SSL_PROXY')
           define($domain_description['constant'], ($domain_description['value']=='true'));     
                define($domain_description['constant'], $domain_description['value']);      
      }
      }
    }
    $configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from '.TABLE_CONFIGURATION . ' order by configuration_id');
    while ($configuration = xtc_db_fetch_array($configuration_query)) {
        if(!defined($configuration['cfgKey']))  {
        if($configuration['cfgKey'] == 'ENABLE_SSL' || $configuration['cfgKey'] == 'USE_SSL_PROXY')
           define($configuration['cfgKey'], ($configuration['cfgValue']=='true'));    
            define($configuration['cfgKey'], $configuration['cfgValue']);    
    }
    }

}


 function ms_get_array_http($all=false) {
    $domain_query_raw = "SELECT *  FROM " . TABLE_DOMAINS;
    $domain_query = xtc_db_query($domain_query_raw);
    $arr_return = array();
    while($domain = xtc_db_fetch_array($domain_query)){
            if($all){
                $arr_domains = explode(",", $domain['domain_http']);
                for($i=0; $i < count($arr_domains); $i++){
                    $arr_return[$domain['domain_id']] = $arr_domains[$i];
                }
            }else{
                $arr_return[$domain['domain_id']] = getFirstDomain($domain['domain_http']);
            }
    }
    return $arr_return;
  }

 function ms_get_string_http($pID=0, $cID=0) {
        $pID = xtc_get_prid($pID);

        $arrHttp = ms_get_array_http(true);
        
        # MODULE MULTISTORE NEU
		$_SESSION['sql_ms_through'] = true;        
        
        if($pID>0){
            $categorie_query=xtc_db_query("SELECT c.string_domains 
                                                                        FROM " . TABLE_CATEGORIES . " c
                                                                        left join " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c on c.categories_id = p2c.categories_id
                                                                        left join ".TABLE_PRODUCTS." p on p2c.products_id = p.products_id
                                                                        where p.products_id = '$pID'");
        }elseif($cID>0){
            $categorie_query=xtc_db_query("SELECT c.string_domains
                                                                        FROM " . TABLE_CATEGORIES . " c
                                                                        WHERE c.categories_id = '$cID'");
        } else {
      return HTTP_SERVER;
        }

        if(!xtc_db_num_rows($categorie_query))
            return HTTP_SERVER ;

        $categorie_data=xtc_db_fetch_array($categorie_query);
        $arrDomain = explode(";", $categorie_data['string_domains']);
        for ($i = 0; $i < count($arrDomain); $i ++) {
             if(!empty($arrHttp[$arrDomain[$i]]) && (!isset($http_domain) || $_SERVER["HTTP_HOST"]==$arrHttp[$arrDomain[$i]])){
             $http_domain = $arrHttp[$arrDomain[$i]];
                 }
        }
        if(!isset($http_domain))
            return HTTP_SERVER ;

        return 'http://'.$http_domain;
  }

    function getMultistoreConfigValue($KEY='', $ID_DOMAIN=0, $ID_LANGUAGE=0){
            if($ID_DOMAIN==0)$ID_DOMAIN=ID_DOMAIN;
            if($ID_LANGUAGE==0)$ID_LANGUAGE=$_SESSION['languages_id'];
            $domain_config_query = xtc_db_query("SELECT value FROM " . TABLE_DOMAINS_CONFIGURATION . " WHERE constant = '$KEY' and domain_id='" . $ID_DOMAIN . "' and language_id = '".$ID_LANGUAGE."'");
            if(xtc_db_num_rows($domain_config_query)){
            $domain_config = xtc_db_fetch_array($domain_config_query);
                    return $domain_config['value'];
            } else {
                # Original Konstante
        return constant($KEY);
            }
        }


  function countProductsInMultiDomain($arrDomain){
    $str_sql = "";
        for ($i = 0; $i < count($arrDomain); $i ++) {
             $str_sql .= str_replace("%s", $arrDomain[$i], MULTISTORE_SQL_SEARCH_WHERE2da) . " or ";
        }
        if($str_sql != ""){
       $str_sql = substr($str_sql, 0, strlen($str_sql)-3);
       $domain_count_query = xtc_db_query("select count(if(p.products_status = 0, p.products_id, null)) inactive_count, count(if(p.products_status = 1, p.products_id, null)) active_count, count(*) total_count from ".TABLE_PRODUCTS." p left join ".TABLE_PRODUCTS_TO_CATEGORIES." p2c on p.products_id = p2c.products_id left join ".TABLE_CATEGORIES." c on p2c.categories_id = c.categories_id where " . $str_sql);
       $domain_count = xtc_db_fetch_array($domain_count_query);
             return $domain_count;
        }
  }

    function countProductsInDomain($ID_DOMAIN){
         if(!empty($ID_DOMAIN)){
           $MULTISTORE_SQL_COUNT = sprintf(MULTISTORE_SQL_COUNT, $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, $ID_DOMAIN, $ID_DOMAIN, $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, (strlen($ID_DOMAIN)+1), $ID_DOMAIN, $ID_DOMAIN, $ID_DOMAIN, $ID_DOMAIN);
         $MULTISTORE_SQL_COUNT = str_replace("ID_DOMAIN", $ID_DOMAIN, $MULTISTORE_SQL_COUNT);
             $query = xtc_db_query($MULTISTORE_SQL_COUNT);
             return xtc_db_num_rows($query);
         }
         return 0;
    }

    function getFirstDomain($arr_domain){
            $arr_domains = explode(",", $arr_domain);
            return $arr_domains[0];
    }

    function initMultistore($temp_template=false){
      global $http_host, $_GET;
     
        if($http_host = xtc_get_host()){
          #$sqlField = xtc_get_sqlField();
          #$sqlField = "domain_http";
          


          for($h = 0; $h < count($http_host); $h++){
            $host = $http_host[$h];
              $http_host2 = str_replace('www.', '', $host);
              $http_host3 = 'www.'.$host;

              if($http_host2!=$host || $http_host3!=$host){
                $domain_query = xtc_db_query("select * from " . TABLE_DOMAINS . " where (domain_http like '%".$host."%' or domain_http like '%".$http_host2."%' or domain_http like '%".$http_host3."%') and domain_status = '1'");
                }else{
                    $domain_query = xtc_db_query("select * from " . TABLE_DOMAINS . " where domain_http like '%".$host."%' and domain_status = '1'");
                }    
                if(!xtc_db_num_rows($domain_query))
                    $domain_query = xtc_db_query("select * from " . TABLE_DOMAINS . " where domain_https like '%".$host."%' and domain_status = '1'");
                
        if(!xtc_db_num_rows($domain_query)) {
            $tok = strtok ($_SERVER['HTTP_HOST'],".");
            while ($tok) {
               $dom = $tld;
               $tld = $tok;
               $tok = strtok (".");
            }
            $domain = strtolower("$dom.$tld");
                    $domain_query = xtc_db_query("select * from " . TABLE_DOMAINS . " where domain_https like '%".$domain."%' and domain_status = '1'");        
        }

                      


              if(xtc_db_num_rows($domain_query)){
                $h = count($http_host);
                $domain = xtc_db_fetch_array($domain_query);   
                $i = 0;
                if(DB_MYSQL_TYPE == 'mysqli'){
                  while ($i < mysqli_num_fields($domain_query)) {                       
                      $meta = xtc_db_fetch_fields($domain_query, $i); 
                      if ($meta && (!$temp_template || strtoupper($meta->name) != 'CURRENT_TEMPLATE'))    # && $meta->type == 'string' && 
                        define(strtoupper($meta->name), $domain[$meta->name]);
                      $i ++;
                  }
                } else {
                  while ($i < mysql_num_fields($domain_query)) {                       
                      $meta = xtc_db_fetch_fields($domain_query, $i); 
                      if ($meta && (!$temp_template || strtoupper($meta->name) != 'CURRENT_TEMPLATE'))    # && $meta->type == 'string' && 
                        define(strtoupper($meta->name), $domain[$meta->name]);
                      $i ++;
                  }                
                }
                
                $current_template = $domain['current_template'];    
                define('ID_DOMAIN', $domain['domain_id']);
                define('ID_LANG',  $domain['id_languages']);
                $arr_domain_http = explode(",", $domain['domain_http']);

                    if(isset($domain['domain_https'])){
                  $arr_domain_https = explode(",", $domain['domain_https']);
                  for($d = 0; $d < count($arr_domain_http); $d++){
                            if($arr_domain_http[$d]==$host || $arr_domain_http[$d]==$http_host3)
                    define('MS_HTTPS_SERVER',  'https://' . $arr_domain_https[$d]);
            }
                    }
                    if(count($arr_domain_http)>1 && $host != $arr_domain_http[0]){
             define('CANONICAL_URL', $arr_domain_http[0]);
                    }
              }else{
           echo "<h2>Fehler: Domain nicht zugeordnet</h2>";
                 #exit;
              }
          }
          if(!defined('MS_HTTPS_SERVER'))
              define('MS_HTTPS_SERVER',  HTTPS_SERVER);
              
            if(!$temp_template){
        define('CURRENT_TEMPLATE', $current_template);
            }else{
                return $current_template;
            }
        }
    }

    function checkMsLicense(){
      @require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');
      if( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) && ($_SERVER['HTTP_X_FORWARDED_HOST']!=$_SERVER['HTTP_HOST']))
          $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];

      $array_domains = xtc_get_array_domains_all();

      for($d = 0; $d < count($array_domains); $d++){
          $src1 = $array_domains[$d];
          $src1 = preg_replace('/\A(http:\/\/)?(www\.)?/s', '', $src1);
            if(substr_count($src1, '.')>1)
               $src1 = substr($src1, strpos($src1, '.')+1, 100);
            if(MULTISTORE_LICENSE == ms_dec($src1))
                break;
      }

      $src2 = preg_replace('/\A(http:\/\/)?(www\.)?/s', '', HTTP_CATALOG_SERVER);
      $plw123thh = parse_url($_SERVER['SCRIPT_URI']);
      
      $src3 = $_SERVER['HTTP_HOST'];
      if( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) && ($_SERVER['HTTP_X_FORWARDED_HOST']!=$_SERVER['HTTP_HOST']))
      $src3 = $_SERVER['HTTP_X_FORWARDED_HOST'];
      $src3 = preg_replace('/\A(http:\/\/)?(www\.)?/s', '', $src3);

      # sub.domain.xyz 
      if(substr_count($src2, '.')>1)
         $src2 = substr($src2, strpos($src2, '.')+1, 100);
      if(substr_count($src3, '.')>1)
         $src3 = substr($src3, strpos($src3, '.')+1, 100);   
      if($src1 == $src2 && $src1 == $src3 && MULTISTORE_LICENSE == ms_dec($src1)){
      }else{             
        /*
        $smarty = new Smarty;
        #@require_once (DIR_FS_CATALOG.DIR_WS_CLASSES.'class.phpmailer.php');
        @require_once (DIR_FS_INC.'xtc_php_mail.inc.php');

        xtc_php_mail(STORE_OWNER_EMAIL_ADDRESS,
             STORE_OWNER,
             "post@webknecht.net",
             "Multistore",
             '',
             STORE_OWNER_EMAIL_ADDRESS,
             STORE_OWNER,
             '',
             '',
             "MS: checkMsLicense auf ".HTTP_CATALOG_SERVER,
             STORE_NAME."<br>$src1<br>$src2<br>$src3<br>".MULTISTORE_LICENSE."<br>".ms_dec(substr($src1, strpos($src1, '.')+1, 100)),
             "$src1 - $src2 - $src3"
             );
        xtc_redirect(xtc_href_link(FILENAME_CONFIGURATION, 'gID=17&ms_error='.$src3));
        */
      }
    }

function  checkMsError(){
    global $_GET;
    if($_GET['gID'] == 17 && isset($_GET['ms_error']) && $_GET['ms_error'] != ''){
       echo "<script language=\"javascript\">
                <!--
                        alert(\"Speichern nicht möglich. Bitte überprüfen Sie die Multistore-Lizenz für '".$_GET['ms_error']."'\");
                  //document.forms[\"configuration\"].MULTISTORE_LICENSE.focus();
                //-->
                </script>";
    }
}


define('CRYPT_KEY', 'multistore');


function ms_base32_decode($str)
{
    $tab="0123456789abcdefghijklmnopqrstuv";
    $dec="";
    $c=0;
    $a=0;
    $s=0;
    $cv=0;

    while ($c<strlen($str))
    {
        $cv=0;
        while ($cv<32)
        {
            if ($tab[$cv] == $str[$c]) break;
            $cv++;
        }

        $a = $a | (($cv<<$s) & 0xff);
        if ($s+5>=8)
        {
            $dec .= chr($a);
            $a = $cv>>(8-$s);
        }
        $s = ($s+5)%8;
        $c++;
    }
    return $dec;
}

function ms_dec($bcstr)
{
    $cstr = ms_base32_decode($bcstr);
    $cstr = md5($cstr);
    return $cstr ;
}

function xtc_get_root_category($categories_id, $current_category_id = 0) {
    $parent_categories_query = "select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . $categories_id . "'";
  $parent_categories_query  = xtDBquery($parent_categories_query);
    if(xtc_db_num_rows($parent_categories_query)>0){
        while ($parent_categories = xtc_db_fetch_array($parent_categories_query,true)) {
            if ($parent_categories['parent_id'] == $current_category_id){
                    return $categories_id;
            } else {
          if ($parent_categories['parent_id'] != $categories_id) {
                            return xtc_get_root_category($parent_categories['parent_id'], $current_category_id);
                }
            }
    }
  }
}

# nicht verwendet
function getListboxDomains($addNeg=false){
    global $arrAccess, $_GET;
   $arrayListbox = array();
   $arrayListbox[] = array('id' => 0, 'text' => TEXT_ALL);
   if($addNeg)
    $arrayListbox[] = array('id' => -1, 'text' => SELECT_NOSELECTION);
     $domain_array = xtc_get_domains();
   for ($k=0; $k<count($domain_array); $k++) {
         for ($i = 0; $i < count($arrAccess); $i++) {
             if($domain_array[$k]['id'] == $arrAccess[$i])
          $arrayListbox[] = array('id' => $domain_array[$k]['id'], 'text' => $domain_array[$k]['text'], 'template' => $domain_array[$k]['template']);
         }
   }
     return $arrayListbox;
}

function get_sqlAccess($arrAccess){
  $strAccessSql = '';
  $arrAccessSql = array();
  
  for ($k=0; $k<count($arrAccess); $k++) {
    $arrAccessSql[] = "{".$arrAccess[$k]."}";
  }
  if(count($arrAccessSql)>0)
    $strAccessSql = join("", $arrAccessSql);
    return $strAccessSql ;
}

function xtc_get_array_domains_all(){
  $domain_query_raw = "SELECT *  FROM " . TABLE_DOMAINS;
  $domain_query = xtc_db_query($domain_query_raw);
    while($domain = xtc_db_fetch_array($domain_query)){
        $arr_domains = explode(",", $domain['domain_http']);
    for($i=0; $i < count($arr_domains); $i++){
        $arrReturn[] = $arr_domains[$i];
    }
    }
    return $arrReturn;
}

?>