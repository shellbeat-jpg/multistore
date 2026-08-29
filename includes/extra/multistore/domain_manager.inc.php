<?php

  require_once (DIR_WS_MULTISTORE.'domain_manager.mod.php');    
	include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/configuration.php');



	$languages = xtc_get_languages();

	$arr_source=array('payment', 'shipping', 'ordertotal');

	for($d=0; $d < count($arr_source); $d++){

	  $source = $arr_source[$d];

	  $source=='ordertotal'?$dir_source='order_total':$dir_source=$source;
  
	  if ($dir = opendir(DIR_FS_CATALOG.DIR_WS_MODULES.$dir_source.'/')) {

			while (($file = readdir($dir)) !== false) {            
			   if(strpos($file, ".php")>0 && strpos($file, ".php.bak")<1)
	          @include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/'.$dir_source.'/' . $file);

	    }

	  }

	}

	$datei = DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/'.$_SESSION['language'].'.bak';

 if(!@file($datei)){

		copy(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/'.$_SESSION['language'].'.php', $datei);

		$suchmuster = 'xtc_date_raw';

		$ersetze = 'xtc__date__raw';

		$inhalt = file_get_contents($datei);

		$inhalt = preg_replace("/$suchmuster/", $ersetze, $inhalt);

		$file = fopen($datei, "r+");

		fwrite($file, $inhalt);

		fclose($file);

	}

	include($datei);
  


	$arr_constants = array();

	$arr_sql = array();



	if ($_GET['action'] == 'new' || $_GET['action'] == 'insert') {

		$domain_query = xtc_db_query("SELECT distinct id, language_id , constant, source, configuration_value as value from ".TABLE_DOMAINS_CONFIGURATION.", ".TABLE_CONFIGURATION." where domains_configuration.constant = configuration.configuration_key and domains_configuration.source != '' and domains_configuration.value != ''");

	}else{

		$domain_query = xtc_db_query("SELECT id, language_id ,	 constant, 	value ,	source FROM " . TABLE_DOMAINS_CONFIGURATION . ' where domain_id = "'.$_GET['doID'].'" order by source, id, constant');

	}

	while ($domain_data = xtc_db_fetch_array($domain_query)) {

		 if(!in_array($domain_data['constant'], $arr_sql))

	   	$arr_sql[] = $domain_data['constant'];

	   $configuration_query = xtc_db_query("SELECT *  FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = '". $domain_data['constant'] ."'");

	   $configuration_data = xtc_db_fetch_array($configuration_query);

       if(defined($domain_data['constant'].'_TITLE'))  {           
  	     $title = @constant($domain_data['constant'].'_TITLE');
         $title = strip_tags($title);       
       }

     

	    $class_display=$domain_data['source'];

	    $keysModuleConfigReplace = array_keys($arrModuleConfigReplace);
	    for($k=0;$k<count($keysModuleConfigReplace);$k++){
	    	$class_display = str_replace($arrModuleConfigReplace[$keysModuleConfigReplace[$k]] , strtolower($keysModuleConfigReplace[$k]), $class_display);
	    }
    
		 if(substr($class_display, 0, 7) =='payment'){

        if(defined('MODULE_'.strtoupper($class_display).'_TEXT_TITLE')) 
        $label = constant('MODULE_'.strtoupper($class_display).'_TEXT_TITLE');

	    	#$label = constant('MODULE_'.strtoupper($class_display).'_TEXT_TITLE');


		 }elseif(substr($class_display, 0, 8) =='shipping'){
            if(defined('MODULE_'.strtoupper($class_display).'_TEXT_TITLE')) 
	    	$label = constant('MODULE_'.strtoupper($class_display).'_TEXT_TITLE');

		 }elseif(substr($class_display, 0, 10) =='ordertotal'){

       $class_display=str_replace('ot_', '', $class_display);

       $class_display=str_replace('ordertotal', 'order_total', $class_display);
        
  		 if(defined('MODULE_'.strtoupper($class_display).'_TEXT_TITLE') && @constant('MODULE_'.strtoupper($class_display).'_TEXT_TITLE')){

           # => payment und shipping

					 $label = constant('MODULE_'.strtoupper($class_display).'_TEXT_TITLE');

			 }elseif(defined('MODULE_'.strtoupper($class_display).'_TITLE')) { # => ordertotal

           $label = constant('MODULE_'.strtoupper($class_display).'_TITLE');

			 }

		 }elseif(defined('BOX_CONFIGURATION_' . $class_display) && @constant('BOX_CONFIGURATION_' . $class_display)){

	    	$label = constant('BOX_CONFIGURATION_' . $class_display);

		 }else {

	      $label = '';

		 }
     # D.L. Zusatzmodule (wie IT-Recht Kanzlei) 
     if(isset($arrPrefixTitle[$domain_data['source']]) && strpos($domain_data['constant'], $arrPrefixTitle[$domain_data['source']]['constant']) !== false){
      $label = $arrPrefixTitle[$domain_data['source']]['prefix'] . $label;
     }
                                                
			#if($domain_data['source'] == 'payment_pn_sofortueberweisung')
      $title=str_replace('<br />', ' ', $title);
      #$title=strip_tags($title);


		  if(!isset($arr_constants[$label]))

            $arr_constants[$label] = array();

      
      $set_function = '';
      if(!empty($configuration_data['set_function'])){
        $set_function = $configuration_data['set_function'];
      }elseif(isset($arrPrefixTitle[$domain_data['source']]) && strpos($domain_data['constant'], $arrPrefixTitle[$domain_data['source']]['constant']) !== false){
        # D.L. Zusatzmodule (wie IT-Recht Kanzlei) 
        $set_function = $arrPrefixTitle[$domain_data['source']]['set_function'];
      }
     
		  if(!isset($arr_constants[$label][$domain_data['constant']])) {

		      $arr_constants[$label][$domain_data['constant']] = array(

						'title' => $title,

					  'constant' => $domain_data['constant'],

					  'label' => $label,

					  'set_function' => $set_function,

					  'values' => array()

					);

			}
			
			if($_SESSION['customer_id'] != 1 && ($domain_data['constant']  == 'PAYPAL_API_USER' || $domain_data['constant']  == 'PAYPAL_API_SANDBOX_PWD' || $domain_data['constant']  == 'PAYPAL_API_SANDBOX_SIGNATURE' || $domain_data['constant']  == 'PAYPAL_API_SANDBOX_USER' || $domain_data['constant']  == 'PAYPAL_API_SIGNATURE' || $domain_data['constant']  == 'PAYPAL_API_USER' || $domain_data['constant']  == 'PAYPAL_API_PWD'))
					$domain_data['value'] = '***********************';

	    $arr_constants[$label][$domain_data['constant']]['values'][$domain_data['language_id']]=$domain_data['value'];
      if($_GET['dl2016'] > 0 ) #  && $domain_data['constant'] == 'ORDERS_NR_TYPE'
      echo $label .' - '. $domain_data['constant'].' - '. $domain_data['language_id'].' = '.  $domain_data['value']."<br />";



	}



if ($_GET['action'] == 'setcflag') {

    if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {

        if ($_GET['doID']) {

            xtc_db_query("UPDATE " . TABLE_DOMAINS . " SET domain_status = '" . $_GET['flag'] . "' WHERE domain_id = '" . $_GET['doID'] . "'");

        }

    }

    xtc_redirect(xtc_href_link(FILENAME_DOMAIN_MANAGER));

} elseif ($_GET['action'] == 'insert' || $_GET['action'] == 'update') {

	  checkMsLicense();

		# TABLE_DOMAINS

    $domain_id = xtc_db_prepare_input($_GET['doID']);

    $domain_http = xtc_db_prepare_input($_POST['domain_http']);

    #$domain_http = str_replace("www.", "", $domain_http);

    $domain_http = str_replace("http://", "", $domain_http);

    $domain_https = xtc_db_prepare_input($_POST['domain_https']);

    #$domain_https = str_replace("www.", "", $domain_https);

    $domain_https = str_replace("https://", "", $domain_https);

    if(empty($domain_https))

      $domain_https = $domain_http;

    $template = xtc_db_prepare_input($_POST['CURRENT_TEMPLATE']);

    $id_languages = xtc_db_prepare_input($_POST['id_languages']);

    $domain_status = xtc_db_prepare_input($_POST['domain_status']);

    $sql_data_array = array (

     'domain_http' => $domain_http,

     'domain_https' => $domain_https,

     'current_template' => $template,

		 'id_languages' => $id_languages,

     'domain_status' => $domain_status

    );

    if (trim(ADD_DOMAIN_FIELDS) != '') {
	    $add_data_array = explode(',',preg_replace("'[\r\n\s]+'",'', ADD_DOMAIN_FIELDS));
	    for ($i = 0, $n = sizeof($add_data_array); $i < $n; $i ++) {
	        $sql_data_array[$add_data_array[$i]] = xtc_db_prepare_input($_POST[$add_data_array[$i]]);
	    }
		}

    # Bestellnummern: separater Nummernkreis
		if(isset($_POST['order_id_next'])){
	    $order_id_next = xtc_db_prepare_input($_POST['order_id_next']);
	    if(is_numeric($order_id_next))
	    	$sql_data_array['order_id_next'] = $order_id_next;
		}


    if ($_GET['action'] == 'insert') {
        xtc_db_perform(TABLE_DOMAINS, $sql_data_array); 
        $domain_id = xtc_db_insert_id();    
    } elseif ($_GET['action'] == 'update') { 
        xtc_db_perform(TABLE_DOMAINS, $sql_data_array, 'update', 'domain_id = \'' . $domain_id . '\'');
    }
                      
    
    
		# Bestellnummer: gemeinsame Nummernkreise
		if(isset($_POST['order_id_next'])){
	    $arrOrderIDs='';
	    if(isset($_POST['order_ids_shop']) && count($_POST['order_ids_shop'])>0){
        foreach ($_POST['order_ids_shop'] AS $dID) {
  		    xtc_db_query("UPDATE shop_configuration set configuration_value = '' where configuration_value like '%".$dID.",%'");
  		 	}		 	
  			$arrOrderIDs=  join(",", $_POST['order_ids_shop']);  
        $arrOrderIDs.=",$domain_id,";    
      }

			if ($_GET['action'] == 'insert') {
			    $sql_data_array = array (
			     	'configuration_key' => "ORDER_IDS_".$domain_id,
			     	'configuration_value' => $arrOrderIDs
			    );
	        xtc_db_perform("shop_configuration", $sql_data_array);
	        
	        
	    } elseif ($_GET['action'] == 'update') {
			    $sql_data_array = array (
			     	'configuration_value' => $arrOrderIDs
			    );
			    xtc_db_perform("shop_configuration", $sql_data_array, 'update', 'configuration_key = \'' . "ORDER_IDS_".$domain_id . '\'');
			}
			if ($_GET['action'] == 'update'){ 			
        if(isset($_POST['order_ids_shop']) && count($_POST['order_ids_shop'])>0){
					if(isset($_POST['ORDER_IDS_OLD']) && count($_POST['ORDER_IDS_OLD'])>0){
            foreach ($_POST['ORDER_IDS_OLD'] AS $dIDold) {
  					  if(!in_array($dIDold, $_POST['order_ids_shop'])) { 					  
  	            xtc_db_query("UPDATE shop_configuration set configuration_value = '' where domain_id =	'ORDER_IDS_".$dIDold."'");
  						} elseif(isset($order_id_next)){
                 xtc_db_query("UPDATE ".TABLE_DOMAINS." set order_id_next = '".$order_id_next."' where domain_id = '$dIDold'");
              } 					 
  					}  
          }                         
				} else {
            xtc_db_query("UPDATE shop_configuration set configuration_value = replace(configuration_value, '".$domain_id.",', '') where configuration_key like '%ORDER_IDS_%'");
				} 
			}
			if(isset($_POST['order_ids_shop']) && count($_POST['order_ids_shop'])>0){
  			foreach ($_POST['order_ids_shop'] AS $dID) {
  				    $sql_data_array = array (
  				     	'configuration_value' => $arrOrderIDs
  				    );
  				    xtc_db_perform("shop_configuration", $sql_data_array, 'update', 'configuration_key = \'' . "ORDER_IDS_".$dID . '\'');
  			}
			}
		} 
    
    
    
    
    
    
    
    
    
    
		if (is_array($_POST['domain2lang'])) {

		  xtc_db_query("DELETE FROM " . TABLE_LANGUAGES_TO_DOMAINS . " WHERE domain_id = '$domain_id'");

			foreach ($_POST['domain2lang'] AS $dest_lang_id) {

         xtc_db_query("INSERT INTO " . TABLE_LANGUAGES_TO_DOMAINS . " (languages_id, domain_id) VALUES('$dest_lang_id', '$domain_id')");

			}

		}



		# Konstanten

		for($d=0; $d < count($arr_sql); $d++){

		  $constant = $arr_sql[$d];

      if ($_GET['action'] == 'insert') {

				$group_query = xtc_db_query("SELECT distinct source from ".TABLE_DOMAINS_CONFIGURATION.", ".TABLE_CONFIGURATION." where domains_configuration.constant = configuration.configuration_key and configuration.configuration_key = '$constant'  and domains_configuration.source != '' and domains_configuration.value != ''");

	      $group_data = xtc_db_fetch_array($group_query);

				$group = $group_data['source'];

      }

			for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {

					 if(isset($_POST[$constant.'_'.$languages[$i]['id']])) {

					    if ($_GET['action'] == 'insert') {

					      xtc_db_query("Insert into ".TABLE_DOMAINS_CONFIGURATION." (value, domain_id, language_id, constant, source) values('".xtc_db_prepare_input($_POST[$constant.'_'.$languages[$i]['id']])."', '".$domain_id."', '".$languages[$i]['id']."', '".$constant."', '".$group."')");

					    } elseif ($_GET['action'] == 'update') {

					      xtc_db_query("Update ".TABLE_DOMAINS_CONFIGURATION." set value = '".xtc_db_prepare_input($_POST[$constant.'_'.$languages[$i]['id']])."' where domain_id	= '".$domain_id."' and language_id = '".$languages[$i]['id']."' and constant = '".$constant."'");

					    }

					 }

		  }

		}
		/*
		if ($_GET['action'] == 'insert') {
			$domain_query = xtc_db_query("INSERT into ".TABLE_DOMAINS_CONFIGURATION." SELECT  $domain_id as domain_id, language_id , constant,  configuration_value as value  , source, id
																		from ".TABLE_DOMAINS_CONFIGURATION." left join ".TABLE_CONFIGURATION."
																		on  domains_configuration.constant = configuration.configuration_key
																		where domain_id = 1 order by id, constant";
		}
		*/  

  # Shop-Zuordnungen übernehmen 
  $arr_group_ids=array();
  $sql_exclude=array();
  $source_id = xtc_db_prepare_input($_POST['copyData']);     
  if($source_id > 0){          
    $l = (strlen($source_id)+1);              
    $sql_source = sprintf(MULTISTORE_SQL_SEARCH_WHERE2d, $source_id, $l, $source_id, $l, $source_id, $source_id, $source_id);
    $l = (strlen($domain_id)+1);              
    $sql_domain = sprintf(MULTISTORE_SQL_SEARCH_WHERE2d, $domain_id, $l, $domain_id, $l, $domain_id, $domain_id, $domain_id);

    for($t=0; $t < count( $_POST['sngCopySrc']); $t++){
      $group_id = xtc_db_prepare_input($_POST['sngCopySrc'][$t]);
      if($group_id>0){
        $arr_group_ids[]=$group_id;   
        $cpy_query = xtc_db_query("SELECT * from ".TABLE_CONTENT_MANAGER." WHERE content_group = '$group_id' and $sql_source and content_delete = 0");
         
        $sql_debug .=" SELECT * from ".TABLE_CONTENT_MANAGER." WHERE content_group = '$group_id' and $sql_source and content_delete = 0\n";
        $sql_debug .= xtc_db_num_rows($cpy_query) . " x INSERT cID: $group_id SRC: $source_id ADD: $domain_id\n";
        while($sql_data_array = xtc_db_fetch_array($cpy_query)){
              unset($sql_data_array['content_id']);
              $sql_data_array['string_domains']=$domain_id; 
              xtc_db_perform(TABLE_CONTENT_MANAGER, $sql_data_array);  
          }              
      }               
    }    
     
    if(isset($arr_group_ids) && count($arr_group_ids)>0){
      $sql_exclude[TABLE_CONTENT_MANAGER] = " AND content_group NOT IN (" . join(",", $arr_group_ids) . ")";
    }            

  	for($t=0; $t < count($arrTblAdoption); $t++){
  	  $table = $arrTblAdoption[$t];
  	  # D.L.
        if(in_array($table, $_POST['copySrc'])) {
  	      $str_exclude = "";
            # gewählte Einzelseiten aussliessen
            if($table == TABLE_CONTENT_MANAGER && $sql_exclude[TABLE_CONTENT_MANAGER])
              $str_exclude = $sql_exclude[TABLE_CONTENT_MANAGER];
            # vorhandene Zuordnungen ggf. ergänzen
            $compare_query = xtc_db_query("SELECT * from $table WHERE 1 = 1 $str_exclude and $sql_source AND NOT $sql_domain");
            $sql_debug .= "SELECT * from $table WHERE 1 = 1 $str_exclude and $sql_source AND NOT $sql_domain;\n";
            # echo "SELECT * from $table WHERE 1 = 1 $str_exclude and $sql_source AND NOT $sql_domain<br />";
            while($compare_array = xtc_db_fetch_array($compare_query)){
              $key = $arrKeysAdoption[$table];
              $idx = $compare_array[$key];
              $sql_debug .= "UPDATE " . $table. " set string_domains = concat(string_domains, ';$domain_id') where $key = '$idx';\n";
              xtc_db_query("UPDATE " . $table. " set string_domains = concat(string_domains, ';$domain_id') where $key = '$idx'");
              # Unterkategorien ebenfalls aktualisieren
              if($table == TABLE_CATEGORIES){
                $sql_debug .= "set_categories_recursive($idx);\n";
                set_categories_recursive($idx);
              }                     
            }         

        }
  	}   
    if($_SERVER["REMOTE_ADDR"] == '#78.55.140.178')  {
        echo nl2br($sql_debug) . "<br />";
        echo "Speicherung ohne Shopzuordnungen erfolgreich. TESTMODUS Ende<br />";
        exit;
    }      
    # echo "UPDATE ".TABLE_DOMAINS." set sql_debug = concat(sql_debug, \"". nl2br($sql_debug)."\") where domain_id = '$domain_id'";
    xtc_db_query("UPDATE ".TABLE_DOMAINS." set sql_debug = \"".$sql_debug."\" where domain_id = '$domain_id'");
 
  }      
        
  xtc_redirect(xtc_href_link(FILENAME_DOMAIN_MANAGER));

} elseif ($_GET['action'] == 'delete') {

  $domain_id = xtc_db_prepare_input($_GET['doID']);

  if(!empty($domain_id)){
    xtc_db_query("delete  from shop_configuration where configuration_key = 'ORDER_IDS_".$domain_id."'");
    
	  xtc_db_query("delete  from " . TABLE_DOMAINS. " where domain_id = '".$domain_id."'");

	  xtc_db_query("delete  from " . TABLE_DOMAINS_CONFIGURATION. " where domain_id = '".$domain_id."'");

	  xtc_db_query("delete  from " . TABLE_PRODUCTS_DESCRIPTION. " where domain_id = '".$domain_id."'");

	  xtc_db_query("delete  from " . TABLE_CATEGORIES_DESCRIPTION. " where domain_id = '".$domain_id."'");

	  xtc_db_query("delete  from " . TABLE_LANGUAGES_TO_DOMAINS. " where domain_id = '".$domain_id."'");

		for($t=0; $t < count($arrTblAdoption); $t++){
				$table = $arrTblAdoption[$t];
				xtc_db_query("update " . $table. " set string_domains = '' where string_domains = '$domain_id'");
			  xtc_db_query("update " . $table. " set string_domains = replace(string_domains, '$domain_id;', '') where left(string_domains, ".(strlen($domain_id)+1).") = '$domain_id;'");
			  xtc_db_query("update " . $table. " set string_domains = replace(string_domains, ';$domain_id', '') where right(string_domains, ".(strlen($domain_id)+1).") = ';$domain_id'");
			  xtc_db_query("update " . $table. " set string_domains = replace(string_domains, ';$domain_id;', ';')");
		}
	}

	xtc_redirect(xtc_href_link(FILENAME_DOMAIN_MANAGER));

}

?>