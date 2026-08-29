<?php

$languages = xtc_get_languages();

#error_reporting(E_NONE);

if ($_GET['action'] == 'update') {

		$POST_KEYS=array_keys($_POST);

		$arr_compare = array();

		$arr_post = array();

    for ($i = 0, $n = sizeof($POST_KEYS); $i < $n; $i ++) {

				 if(substr($POST_KEYS[$i], 0, 9)=='CONSTANT_'){

				    $KEY = $POST_KEYS[$i];

				    $KEY = str_replace('CONSTANT_', '', $KEY);

				    $arr_compare[] = $KEY;

            $arr_post[] = array('KEY' => $KEY, 'SOURCE' => $_POST['SOURCE_'.$KEY]);

				 }

    }



		$domain_query = xtc_db_query("SELECT distinct constant FROM " . TABLE_DOMAINS_CONFIGURATION);

		while ($domain_data = xtc_db_fetch_array($domain_query)) {

       if(!in_array($domain_data['constant'], $arr_compare))

		       xtc_db_query("Delete FROM " . TABLE_DOMAINS_CONFIGURATION . " where constant = '".$domain_data['constant']."'");

		}

		$arrDomain=xtc_get_array_domains();


		$key_domains=array_keys($arrDomain);

		for ($p = 0; $p <  sizeof($arr_post); $p ++) {

			if(!empty($arr_post[$p]['KEY'])){
        for ($j = 0; $j <  sizeof($key_domains); $j ++) {
					$domain_query = xtc_db_query("SELECT distinct constant FROM " . TABLE_DOMAINS_CONFIGURATION . " where constant = '".$arr_post[$p]['KEY']."' and domain_id = '".$key_domains[$j]."'");
		      if(xtc_db_num_rows($domain_query)<1) {
					    for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
					      $xtc_value = @constant($arr_post[$p]['KEY'])?@constant($arr_post[$p]['KEY']):'';
								$domain_query = xtc_db_query("INSERT into " . TABLE_DOMAINS_CONFIGURATION . " (domain_id,	language_id, constant, value, source) values('".$key_domains[$j]."', '".$languages[$i]['id']."', '".$arr_post[$p]['KEY']."', '".$xtc_value."', '".$arr_post[$p]['SOURCE']."')");
							}
					}
				}
			}
    }

    xtc_redirect(xtc_href_link(FILENAME_DOMAIN_KONFIGURATOR));

}






include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/configuration.php');

$datei = DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/'.$_SESSION['language'].'.bak';

copy(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/'.$_SESSION['language'].'.php', $datei);

$suchmuster = 'xtc_date_raw';

$ersetze = 'xtc__date__raw';

$inhalt = file_get_contents($datei);

$inhalt = preg_replace("/$suchmuster/", $ersetze, $inhalt);

$file = fopen($datei, "r+");

fwrite($file, $inhalt);

fclose($file);



@include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/'.$_SESSION['language'].'.bak');

$arrExclude =array(

	'PRICE_IS_BRUTTO',

	'PRICE_PRECISION',

	'CC_KEYCHAIN',

	'USE_ADMIN_TOP_MENU',

	'CURRENT_CSS',

	'MULTISTORE_LICENSE',

	'MS_MULTIGROUPS',

	'CSS_INCLUDE',

	'USE_WYSIWYG',

	'SESSION_CHECK_USER_AGENT',

	'SESSION_CHECK_IP_ADDRESS',

	'CURRENT_TEMPLATE',

	'USE_ADMIN_LANG_TABS',

	'QUICKLINK_ACTIVATED',

	'MS_MULTIBASKET',

	'MS_MULTIIMG'

);

for($e=0; $e < count($arrExclude); $e++){

    $exclude .= ' configuration_key != "'.$arrExclude[$e].'" and ';

}

for($d=0; $d < count($arrConfigurationGroups); $d++){

  $configuration_group_id = $arrConfigurationGroups[$d];
  
  if(defined('BOX_CONFIGURATION_'.$configuration_group_id))
    $name = @constant('BOX_CONFIGURATION_'.$configuration_group_id);
  
  if($configuration_group_id==19 && defined('BOX_CONFIGURATION_'.$configuration_group_id.'b'))
	$name = @constant('BOX_CONFIGURATION_'.$configuration_group_id.'b');

  $configurator_array[$name]=array();

  $error = 0;

	$configuration_query = xtc_db_query("SELECT *  FROM " . TABLE_CONFIGURATION . " WHERE  	configuration_id > 0 and $exclude configuration_group_id = '$configuration_group_id' order by sort_order");

	while ($configuration_data = xtc_db_fetch_array($configuration_query)) {

     $key = $configuration_data['configuration_key'];

     $key = strtoupper($key);
     if(defined($key.'_TITLE'))
        $title = @constant($key.'_TITLE');

	 if(empty($title)) {

            $error = 1;

            $title = $key;

	 }else{    

            $configurator_array[$name][] = array ('group' => $configuration_group_id, 'error' => $error, 'key' => $key, 'title' => $title);

	 }

	}

}

$configurator_keys = array_keys($configurator_array);









$arr_source=array('payment', 'shipping', 'ordertotal');

for($d=0; $d < count($arr_source); $d++){

  $source = $arr_source[$d];

  $source=='ordertotal'?$dir_source='order_total':$dir_source=$source;

  $configurator_array[$source]=array();

	if ($dir = opendir(DIR_FS_CATALOG.DIR_WS_MODULES.$dir_source.'/')) {

    $arrFiles=array();

		while (($file = readdir($dir)) !== false) {

     if(strpos($file, ".php") && strpos($file, ".php.bak")<1) $arrFiles[]=$file;

    }

		closedir($dir);

		sort($arrFiles);

		for($f=0;$f<count($arrFiles);$f++){

        $file=$arrFiles[$f];

			if(strpos($file, ".php")>0 && strpos($file, ".php.bak")<1){

				 $class = substr($file, 0, strpos($file, ".php"));

	       include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/'.$dir_source.'/' . $file);
	       include(DIR_FS_CATALOG.DIR_WS_MODULES . $dir_source.'/' . $file);

         #$class=='ordertotal'?$class_display='order_total':$class_display=$class;

         $class_display=str_replace('ot_', '', $class);
         $keysModuleConfigReplace = array_keys($arrModuleConfigReplace);
         for($k=0;$k<count($keysModuleConfigReplace);$k++){
             $class_display = str_replace($arrModuleConfigReplace[$keysModuleConfigReplace[$k]] , strtolower($keysModuleConfigReplace[$k]), $class_display);
         }

				 if(defined('MODULE_'.strtoupper($dir_source).'_'.strtoupper($class_display).'_TEXT_TITLE') &&
                    @constant('MODULE_'.strtoupper($dir_source).'_'.strtoupper($class_display).'_TEXT_TITLE')){
             # => payment und shipping
						  
                            $name = constant('MODULE_'.strtoupper($dir_source).'_'.strtoupper($class_display).'_TEXT_TITLE');
				 }elseif(defined('MODULE_'.strtoupper($dir_source).'_'.strtoupper($class_display).'_TITLE') &&
                       @constant('MODULE_'.strtoupper($dir_source).'_'.strtoupper($class_display).'_TITLE')){
				 		# => ordertotal
                        
             $name = constant('MODULE_'.strtoupper($dir_source).'_'.strtoupper($class_display).'_TITLE');
				 }

				 $error = 0;
                 
                  
				 if(defined('MODULE_'.strtoupper($dir_source).'_'.strtoupper($class_display).'_STATUS') &&
                    strtolower(@constant('MODULE_'.strtoupper($dir_source).'_'.strtoupper($class_display).'_STATUS')) == 'true'){
  	       $temp_class = new $class;
  	       $keys = $temp_class->keys();

					 for($i=0; $i < count($keys); $i++){
                             if(defined($keys[$i].'_TITLE'))
							 $TITLE = @constant($keys[$i].'_TITLE');
	             if(empty($TITLE))
	                $TITLE = $keys[$i]."*";
							 if(!empty($TITLE)){
			      		 if($class == 'pn_sofortueberweisung')
								    $TITLE = str_replace('MODULE_PAYMENT_PN_SOFORTUEBERWEISUNG_HASH_ALGORITHM', '', $TITLE);
		             $TITLE = str_replace('<br />', ' ', $TITLE);
		             $configurator_array[$source][] = array ('box' => $source, 'error' => $error, 'source' => $source, 'file' => $file, 'class' => $class, 'name' => $name, 'key' => $keys[$i], 'title' => $TITLE);
							 }
					 }
				 }
			}
	  }
	}
}

$arr_data = array();

$domain_query = xtc_db_query("SELECT distinct constant FROM " . TABLE_DOMAINS_CONFIGURATION);

while ($domain_data = xtc_db_fetch_array($domain_query)) {

	 $arr_data[] = $domain_data['constant'];

}

?>