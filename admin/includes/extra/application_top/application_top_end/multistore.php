<?php
# MODULE MULTISTORE 
if(defined("MULTISTORE") &&  MULTISTORE=='true'){   
  $ms_sid = session_id();
  
	$arrMsPreconfig[] = array();
	$arrMsPreconfig[] = FILENAME_ORDERS;
  $arrMsPreconfig[] = FILENAME_ORDERS_EDIT;
  $arrMsPreconfig[] = FILENAME_PRINT_PACKINGSLIP;
  $arrMsPreconfig[] = FILENAME_PRINT_ORDER;
  # D.L.
  $arrayDomains = ms_get_array_http();
  # temporäre Deklaration aller originalen Shop URLs und Pfade der Bestellung
  # Order Klasse wird in getPreconfig() deklariert
  if(isset($_GET['oID'])) 
     getPreconfig();  
             
	$arrAccess = admin_access_domains($_SESSION['customer_id']);
	$strAccessSql = get_sqlAccess($arrAccess);  
        
  $SCRIPT_NAME = basename($_SERVER['SCRIPT_NAME']);         
	if($SCRIPT_NAME == FILENAME_STATS_CUSTOMERS){ 
  	$domain_array = $arrMultistoreSelectbox = getArrayDomains(true, true);  	 
    $frmAction = $SCRIPT_NAME;   
    if($_GET['id_domain']>0){         
        $ms_default_select =  " where 1=1 " . sprintf(MULTISTORE_ORDERS_ID_DOMAIN, (int)$_GET['id_domain']);
    }elseif($_GET['id_domain']==-1){
        $ms_default_select = " where (o.id_domain = '' || o.id_domain = '0') ";
    }else{
        $ms_default_select = "";
    }
  }elseif($SCRIPT_NAME == FILENAME_SALES_REPORT){ 
  	$domain_array = $arrMultistoreSelectbox = getArrayDomains(true, true);   
    if($_GET['id_domain']>0){         
        $ms_default_select =  sprintf(MULTISTORE_ORDERS_ID_DOMAIN, (int)$_GET['id_domain']);
    }elseif($_GET['id_domain']==-1){
        $ms_default_select = "and (o.id_domain = '' || o.id_domain = '0') ";
    }else{
        $ms_default_select = "";
    }
  }elseif($SCRIPT_NAME == FILENAME_STATS_PRODUCTS_VIEWED ||
     $SCRIPT_NAME == FILENAME_STATS_PRODUCTS_PURCHASED){ 
  	$domain_array = $arrMultistoreSelectbox = getArrayDomains(true, true);  	 
    $frmAction = $SCRIPT_NAME;   
    if($_GET['id_domain']>0){         
        $ms_default_select = " and " . sprintf(MULTISTORE_SQL_SEARCH_WHERE2d, (int)$_GET['id_domain'], strlen((int)$_GET['id_domain'])+1, (int)$_GET['id_domain'], strlen((int)$_GET['id_domain'])+1, (int)$_GET['id_domain'], (int)$_GET['id_domain'], (int)$_GET['id_domain']);
        $ms_default_select .= sprintf(MULTISTORE_SQL_PSDESCRIPTION, (int)$_GET['id_domain'], strlen((int)$_GET['id_domain'])+1, (int)$_GET['id_domain'], strlen((int)$_GET['id_domain'])+1, (int)$_GET['id_domain'], (int)$_GET['id_domain'], (int)$_GET['id_domain']);
    }elseif($_GET['id_domain']==-1){
        $ms_default_select = " AND string_domains = '' ";
    }else{
        $ms_default_select = "";
    }
  }elseif($SCRIPT_NAME == FILENAME_MODULE_EXPORT){ 
  	 $domain_array = $arrMultistoreSelectbox = xtc_get_domains();   
  }elseif($SCRIPT_NAME == FILENAME_WHOS_ONLINE){ 
  	 $domain_array = $arrMultistoreSelectbox = getArrayDomains(false, false);
  	 $frmAction = $SCRIPT_NAME;
     if($_GET['id_domain']>0){
        $ms_default_select = " WHERE id_domain = '".xtc_db_prepare_input($_GET['id_domain'])."' ";
     }elseif($_GET['id_domain']==-1){
        $ms_default_select = " WHERE id_domain = '' ";
     }else{
        $ms_default_select = "";
     } 
  }elseif($SCRIPT_NAME == FILENAME_START){ 
  	 $domain_array = $arrMultistoreSelectbox = getArrayDomains(true, true);
  	 $frmAction = $SCRIPT_NAME;
     $ms_sub_select = "";
     if($_GET['id_domain']>0){
    		$ms_default_select = " id_domain = '".xtc_db_prepare_input($_GET['id_domain'])."' ";
    		$arrAccess=array(xtc_db_prepare_input($_GET['id_domain']));
    		$ms_sub_select = " WHERE " . sprintf(MULTISTORE_SQL_SEARCH_WHERE2d, (int)$_GET['id_domain'], strlen((int)$_GET['id_domain'])+1, (int)$_GET['id_domain'], strlen((int)$_GET['id_domain'])+1, (int)$_GET['id_domain'], (int)$_GET['id_domain'], (int)$_GET['id_domain']);
     }elseif($_GET['id_domain']==-1){
        $ms_default_select = " id_domain = '' ";
        $arrAccess=array(xtc_db_prepare_input($_GET['id_domain']));
        $ms_sub_select = " WHERE string_domains = ''";
     }else{
        $ms_default_select = " INSTR('$strAccessSql', concat(\"{\", id_domain, \"}\")) ";
     }	 
  }elseif($SCRIPT_NAME == FILENAME_CREATE_ACCOUNT){   
    $domain_array = xtc_get_domains();
  }elseif($SCRIPT_NAME == 'shop_offline.php'){ 
	  $domain_array = xtc_get_domains();  
  }elseif($SCRIPT_NAME == FILENAME_MODULE_NEWSLETTER){
    $domain_array = getArrayDomains(false, true);
  }elseif($SCRIPT_NAME == FILENAME_COUNTRIES){
    $domain_array = getArrayDomains(false, true);
	}elseif($SCRIPT_NAME == FILENAME_CUSTOMERS_STATUS){
    if(MS_MULTIGROUPS=='true')
      $domain_array = getArrayDomains(); 
  }elseif($SCRIPT_NAME == FILENAME_CONTENT_MANAGER){ 
    $domain_array = $arrMultistoreSelectbox = getArrayDomains(false, true);
    $frmAction = $SCRIPT_NAME;
    if($_GET['id_domain']>0){         
        $ms_default_select = " and " . sprintf(MULTISTORE_SQL_SEARCH_WHERE2d, (int)$_GET['id_domain'], strlen((int)$_GET['id_domain'])+1, (int)$_GET['id_domain'], strlen((int)$_GET['id_domain'])+1, (int)$_GET['id_domain'], (int)$_GET['id_domain'], (int)$_GET['id_domain']);
    }elseif($_GET['id_domain']==-1){
        $ms_default_select = " AND string_domains = '' ";
    }else{
        $ms_default_select = "";
    }
  }elseif($SCRIPT_NAME == FILENAME_CUSTOMERS){ 
    $domain_array = $arrMultistoreSelectbox = getArrayDomains(true, true);
  }elseif($SCRIPT_NAME == FILENAME_CSV_BACKEND){
    $domain_array  = $arrMultistoreSelectbox = xtc_get_domains();
    sort($domain_array);
  }elseif($SCRIPT_NAME == FILENAME_CATEGORIES){
  	require_once (DIR_FS_INC.'xtc_get_subcategories.inc.php');
  	require_once (DIR_FS_INC.'xtc_get_parent_categories.inc.php');
  	require_once (DIR_FS_INC.'xtc_get_product_path.inc.php');
  	$languages = xtc_get_languages();
  	$domain_array = $arrMultistoreSelectbox = getArrayDomains(false, true);
  	$domain_array_all = getArrayDomains(false);
  	if(isset($_GET['cID']) && $_GET['action'] == 'edit_category'){
  	   $domain_array_az = xtc_get_domains((int) $_GET['cID'], Null, "=1", ' ', true);
  	}else{
  	   $domain_array_az = xtc_get_domains('az', Null, "=1", ' ', true);
  	}
  	$arrDomains = xtc_get_array_domains();   	 
  	$CURRENT_TEMPLATE  = getCurrentTemplate($parent_category_id, $domain_array);  
    # View Categories
  	if(!isset($action)){
        $arrCategoriesShown=array();
        $last_cid = 0;
    } 
    if(isset($_GET['action'])) {
      	$domain_array_active = getArrayActiveDomains($category_root, -1, true); # , true
      	$arrDomainLang = getArrDomainLang($domain_array_active, $domain_array, $languages );
    }
  }elseif($SCRIPT_NAME == FILENAME_ORDERS){       
	 require_once (DIR_FS_INC.'xtc_get_subcategories.inc.php');
	 $domain_array = $arrMultistoreSelectbox = getArrayDomains(true, true);
	} 
	
  $strMultistoreSelectbox = "";
  if(isset($arrMultistoreSelectbox)){   
    if($frmAction) $strMultistoreSelectbox .= TEXT_DOMAIN .": ".xtc_draw_form('filter', $frmAction, '', 'get');
    $strMultistoreSelectbox .= xtc_draw_pull_down_menu('id_domain', $arrMultistoreSelectbox, (isset($_POST['id_domain'])?$_POST['id_domain']:$_GET['id_domain']), 'onChange="this.form.submit();"');
    if($frmAction) $strMultistoreSelectbox .= "</form>";
  }

  if(isset($_GET['ms_error'])) {
      $messageStack->add_session(ERROR_MS_LICENSE, 'error');
      xtc_redirect(xtc_href_link(FILENAME_CONFIGURATION, 'gID=17'));
  }
  





  function ms_sqlDefaultSelect($tbl=''){
  	 global $ms_default_select;
     switch ($tbl) {
    	case TABLE_CUSTOMERS:
  		 $prefix = 'c.';
      break;
    	case TABLE_CATEGORIES:
  		 $prefix = 'c.';
      break;
    	case TABLE_ORDERS:
  		 $prefix = 'o.';
      break;
    	default :
  		 $prefix = '';
    	break;
     }
     return str_replace('id_domain', $prefix.'id_domain', $ms_default_select);
  }
  
  # check Categories View
  function ms_checkCategoriesView($categories){
    global $arrCategoriesShown, $last_cid;
    if($categories['string_domains'] == '' || $last_cid == $categories['categories_id'] || checkAdminAccess($categories['string_domains'], $categories['categories_id'])){
       if(!in_array($categories['categories_id'], $arrCategoriesShown)){  					
        		$last_cid = $categories['categories_id'];
						$arrCategoriesShown[]=$categories['categories_id'];     						
			 }
       return true;	
    }   
  }
  
	function  ms_getLanguageName($languages, $language_id){
		for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
			if($languages[$i]['id']==$language_id)
	      return $languages[$i]['name'];
		}
	}
 
  
	function ms_cfg_pull_down_logo_sets($name, $current_css, $template) {
	  $templates_array[] = array ('id' => 0, 'text' => '');
		if ($dir = opendir(DIR_FS_CATALOG.'templates/'.$template.'/img/')) {
			while (($templates = readdir($dir)) !== false) {
				if (strpos($templates, '.gif') || strpos($templates, '.jpg') || strpos($templates, '.png')) {
					$templates_array[] = array ('id' => $templates, 'text' => $templates);
				}
			}
			closedir($dir);
			sort($templates_array);
			return xtc_draw_pull_down_menu($name, $templates_array, $current_css);
		}
	}  
	
	
  function ms_cfg_pull_down_css_sets($name, $current_css='', $template='') {
      if($template=='')
       return $template;
	  $templates_array[] = array ('id' => 0, 'text' => '');
		if ($dir = opendir(DIR_FS_CATALOG.'templates/'.$template.'/css/')) {
			while (($templates = readdir($dir)) !== false) {
				if (strpos($templates, '.css') && !strpos($templates, '.php')) {
					$templates_array[] = array ('id' => $templates, 'text' => $templates);
				}
			}
			closedir($dir);
			sort($templates_array);
			return xtc_draw_pull_down_menu($name, $templates_array, $current_css);
		}
	}  
	
  if(!function_exists('ms_get_category_tree')) {
  
    function ms_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
      if (!is_array($category_tree_array)) {
        $category_tree_array = array ();
      }
      if ((sizeof($category_tree_array) < 1) && ($exclude != '0')) {
        $category_tree_array[] = array ('id' => '0', 'text' => TEXT_TOP);
      }
      if ($include_itself) {
        # MODULE MULTISTORE
        $category_query = xtc_db_query("SELECT distinct cd.categories_name
                                          FROM ".TABLE_CATEGORIES_DESCRIPTION." cd
                                         WHERE cd.language_id = '".(int)$_SESSION['languages_id']."'
                                           AND cd.categories_id = '".(int)$parent_id."' and cd.categories_name != 'NULL'");
        $category = xtc_db_fetch_array($category_query);
        $category_tree_array[] = array ('id' => $parent_id, 'text' => $category['categories_name']);
      } 
      # MODULE MULTISTORE
      $categories_query = xtc_db_query("SELECT distinct c.categories_id,
                                               c.string_domains,
                                               cd.categories_name,
                                               c.parent_id
                                          FROM ".TABLE_CATEGORIES." c
                                          JOIN ".TABLE_CATEGORIES_DESCRIPTION." cd
                                               ON c.categories_id = cd.categories_id
                                                  AND cd.language_id = '".(int)$_SESSION['languages_id']."'
                                         WHERE c.parent_id = '".(int)$parent_id."' and cd.categories_name != 'NULL'
                                         GROUP BY c.categories_id
                                      ORDER BY c.sort_order, cd.categories_name");
                                   
      while ($categories = xtc_db_fetch_array($categories_query)) {
        # MODULE MULTISTORE
        if ($exclude != $categories['categories_id'] && ($categories['string_domains'] == '' || checkAdminAccess($categories['string_domains'], $categories['categories_id']))) {
          $category_tree_array[] = array ('id' => $categories['categories_id'], 'text' => $spacing.$categories['categories_name']);
        }
        $category_tree_array = ms_get_category_tree($categories['categories_id'], $spacing.'&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
      }
      return $category_tree_array;
    }  
  }
  
  /* modified xtc_-function ... original: xtc_draw_products_pull_down() */
  function ms_draw_products_pull_down($name, $parameters = '', $exclude = '', $add_price = true, $add_model = true) {
    global $xtPrice;
    
    if (empty($exclude)) {
      $exclude = array ();
    }
    $select_string = '<select name="'.$name.'"';
    if ($parameters) {
      $select_string .= ' '.$parameters;
    }
    $select_string .= '>';
    # MODULE MULTISTORE
    $products_query = xtc_db_query("SELECT distinct p.products_id,
                                           p.products_model,
                                           pd.products_name,
                                           p.products_tax_class_id,
                                           p.products_price
                                      FROM ".TABLE_PRODUCTS." p
                                      JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd
                                           ON p.products_id = pd.products_id
                                              AND pd.language_id = '".(int)$_SESSION['languages_id']."'
                                  GROUP BY p.products_id
                                  ORDER BY pd.products_name"
                                  );
    while ($products = xtc_db_fetch_array($products_query)) {
      if (!in_array($products['products_id'], $exclude)) {
        //brutto admin:
        if (PRICE_IS_BRUTTO == 'true') {
          $products['products_price'] = xtc_round($products['products_price'] * ((100 + xtc_get_tax_rate($products['products_tax_class_id'])) / 100), PRICE_PRECISION);
        }
        $products_price = $add_price ? ' ('.trim($xtPrice->xtcFormat($products['products_price'],true)).')' : '';
        $products_model = $add_model ? ' ['.TEXT_GLOBAL_PRODUCTS_MODEL.': '.$products['products_model'].']' : '';
        $select_string .= '<option value="'.$products['products_id'].'">'.$products['products_name'].$products_price.$products_model.'</option>';
      }
    }
    $select_string .= '</select>';
    return $select_string;
  }       

	function ms_cfg_pull_down_domain_sets($id_domain=0) {       
      $arrDomains = xtc_get_domains();
			return xtc_draw_pull_down_menu('id_domain', $arrDomains, ($id_domain>0?$id_domain:ID_DOMAIN));
	}
 
  /* modified xtc_-function ... original: xtc_draw_products_pull_down() */
	function ms_cfg_pull_down_language_sets($name, $current, $languages) {
	    $templates_array[] = array ('id' => 0, 'text' =>'');
			for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
		    $templates_array[] = array ('id' => $languages[$i]['id'], 'text' => $languages[$i]['name']);
			}
			return xtc_draw_pull_down_menu($name, $templates_array, $current);
	}


	/* modified xtc_-function ... original: xtc_cfg_pull_down_template_sets() */
  function ms_cfg_pull_down_template_sets($CURRENT_TEMPLATE="") {
		$CURRENT_TEMPLATE==""?$CURRENT_TEMPLATE=CURRENT_TEMPLATE:$CURRENT_TEMPLATE=$CURRENT_TEMPLATE;     
    if ($dir = opendir(DIR_FS_CATALOG.'templates/')) {
      while (($templates = readdir($dir)) !== false) {
        if (is_dir(DIR_FS_CATALOG.'templates/'."//".$templates) and ($templates != "global") and  ($templates != "CVS") and ($templates != ".") and ($templates != "..")) {
						$templates_array[] = array ('id' => $templates, 'text' => $templates);
        }
      }
      closedir($dir);
      sort($templates_array);
      return xtc_draw_pull_down_menu("CURRENT_TEMPLATE", $templates_array, $CURRENT_TEMPLATE);
    }
  }
  
  # Aufruf in admin\categories.php => admin\includes\extra\modules\multistore\multidescription.php
  function ms_get_categories_desc_fields($category_id, $language_id, $id_domain=0) {
    if (!empty($category_id)) {
      if (empty($language_id)) {
        $language_id = $_SESSION['languages_id'];
      }
			if ($id_domain == 0)
				$id_domain = ID_DOMAIN;
	  	$sql_domain = sprintf(MULTISTORE_SQL_CDESCRIPTION, $id_domain);
      $category_query = xtc_db_query("SELECT *
                                        FROM ".TABLE_CATEGORIES_DESCRIPTION."
                                       WHERE categories_id = '".(int)$category_id."'
                                         AND language_id = '".(int)$language_id."'" . $sql_domain);
      return xtc_db_fetch_array($category_query);
    }
  } 
    
  # Aufruf in admin\categories.php => admin\includes\extra\modules\multistore\multidescription.php
  function ms_get_products_desc_fields($product_id, $language_id, $id_domain=0) {
    if (!empty($product_id)) {
      if (empty($language_id)) {
        $language_id = $_SESSION['languages_id'];
      }     
			if ($id_domain == 0)
				$id_domain = ID_DOMAIN;
	  	$sql_domain = sprintf(MULTISTORE_SQL_ADESCRIPTION, $id_domain);

      $product_query = xtc_db_query("SELECT *
                                       FROM ".TABLE_PRODUCTS_DESCRIPTION."
                                      WHERE products_id = '".(int)$product_id."'
                                        AND language_id = '".(int)$language_id."'" . $sql_domain);
      return xtc_db_fetch_array($product_query);
    }
  }        
    
}


?>