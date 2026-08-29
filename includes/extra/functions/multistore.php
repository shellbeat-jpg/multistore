<?php   

 function extra_configuration(){
    global $configuration;     
    if (defined('MULTISTORE') && MULTISTORE=='true' && $configuration['configuration_key'] != 'MULTISTORE'){
       $configuration='';         
    }      
 }

 function preinitMultistore($temp_template=false){
     global $http_host, $_GET;

     if($http_host = xtc_get_host()){
       for($h = 0; $h < count($http_host); $h++){
          $host = $http_host[$h];
          $http_host2 = str_replace('www.', '', $host);
          $http_host3 = 'www.'.$host;
          $domain_query = xtc_db_query("select * from " . TABLE_DOMAINS . "
          where (domain_http = '".$host."' or
          domain_http = '".$http_host2."' or
          domain_http = '".$http_host3."')
          and domain_status = '1'");
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
            if(!defined('MS_HTTPS_SERVER'))
              define('MS_HTTPS_SERVER',  HTTPS_SERVER);

            if(!$temp_template){
              define('CURRENT_TEMPLATE', $current_template);
            }else{
              if($_GET['dl56']>0) {
                echo "$request_type: " . $request_type . "<br />";
                echo "ID_LANG: " . ID_LANG . "<br />";
                echo "MS_HTTPS_SERVER: " . MS_HTTPS_SERVER . "<br />";
                echo "HTTP_SERVER: " . HTTP_SERVER . "<br />";
                echo "HTTPS_SERVER: " . HTTPS_SERVER . "<br />";
                echo "CURRENT_TEMPLATE: " . CURRENT_TEMPLATE . "<br />";
                echo "CANONICAL_URL: " . CANONICAL_URL . "<br />";
              }
              return $current_template;
            }
          }
        }
      }
  }
?>