<?php
  require('includes/application_top.php'); 
  require(DIR_WS_MULTISTORE.'domain_manager.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_shop_conf.inc.php');

  if($_GET['action'] == 'new' || $_GET['action'] == 'edit'){
		$arrDomains = xtc_get_domains();       
		# Weiterleitung zur Stammdomain falls die verwendete Anmeldedomain dieser nicht entspricht
		if(str_replace("www.", "", $arrDomains[0]['text']) != str_replace("www.", "", $_SERVER['HTTP_HOST'])) {
      xtc_redirect('http://'.$arrDomains[0]['text'].DIR_WS_CATALOG.'/admin/'.FILENAME_DOMAIN_MANAGER);
    } 
    if($_GET['action'] == 'edit' && !in_array((int)$_GET['doID'], $arrAccess))
      die("not allowed");     
	}
  if ($_GET['action'] == 'insert' || $_GET['action'] == 'update') {
      $domain_id = xtc_db_prepare_input($_GET['doID']);
      if(!is_dir(DIR_FS_CATALOG.'templates_c/'.$domain_id.'/')) {        
        mkdir(DIR_FS_CATALOG.'templates_c/'.$domain_id);   
      }    
      #if (!is_file(DIR_FS_CATALOG.'templates/'.$template.'/css/custom_'.$domain_id.'.css')) {
      #    copy(DIR_FS_CATALOG.'templates/'.$template.'/css/custom.css', DIR_FS_CATALOG.'templates/'.$template.'/css/custom_'.$domain_id.'.css');
      #}         
  }
  
require (DIR_WS_INCLUDES.'head.php');
?></head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH;
?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH;
?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php');
?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS . 'heading/icon_categories.png');
?></td>                                         
    <td class="pageHeading"><?php echo HEADING_TITLE;
?></td>
  </tr>
  <tr>
    <td class="main" valign="top">XTC Configuration</td>
  </tr>
</table>
</td>
      </tr>
      <tr>
        <td>
        <table width="100%" border="0">
          <tr>
            <td>
<?php
if (!$_GET['action'] || $_GET['action']  == 'updateConfiguration') {

    ?>
<div class="pageHeading"><br /><?php echo HEADING_CONTENT;
    ?><br /></div>

<?php
$categories_query = xtc_db_query("select d.* from " . TABLE_DOMAINS . " d 
LEFT JOIN " . TABLE_DOMAINS_CONFIGURATION . " c   
ON d.domain_id = c.domain_id AND language_id = '". $_SESSION['languages_id']."'
WHERE c.constant = 'STORE_NAME'
order by d.domain_status DESC, c.value ASC");              
?>
</table>


<?php
} else {


if($_GET['action'] == 'new' || $_GET['action'] == 'edit'){
            if ($_GET['action'] != 'new') {
                $content_query = xtc_db_query("SELECT *  FROM " . TABLE_DOMAINS . " WHERE domain_id='" . (int)$_GET['doID'] . "'");
                $content = xtc_db_fetch_array($content_query);
                echo xtc_draw_form('edit_content', FILENAME_DOMAIN_MANAGER, 'action=update&doID=' . $_GET['doID'], 'post', 'enctype="multipart/form-data"') . xtc_draw_hidden_field('doID', $_GET['doID']);
            } else {
                echo xtc_draw_form('edit_content', FILENAME_DOMAIN_MANAGER, 'action=insert', 'post', 'enctype="multipart/form-data"') . xtc_draw_hidden_field('doID', $_GET['doID']);
            }

?>






 <table class="main"  border="0">

		      <tr>
					<td nowrap  valign="top" colspan="7"><br><h3><?php echo  HEADING_CONFIGURATION; ?></h3></td>
					</tr>
            	  <tr>
            <td><?php echo TEXT_STATUS; ?></td>
            <td><?php echo xtc_draw_selection_field('domain_status', 'checkbox', '1',$content['domain_status']==1 ? true : false); ?></td>
          </tr>
      <tr>
      <td nowrap><?php echo TEXT_TITLE_DOMAIN;
            ?></td>
      <td><?php echo xtc_draw_input_field('domain_http', $content['domain_http'], 'size="60"')."&nbsp;&nbsp;<span class='dataTableContent'>".DESCRIPTION_URL."</span>";
            ?></td>
   </tr>
         <tr>
      <td nowrap><?php echo TEXT_TITLE_DOMAIN_SSL;
            ?></td>
      <td><?php echo xtc_draw_input_field('domain_https', $content['domain_https'], 'size="60"')."&nbsp;&nbsp;<span class='dataTableContent'>".DESCRIPTION_URLS."</span>";
            ?></td>
   </tr>
      <tr>

      <td nowrap><?php echo TEXT_DOMAIN_USER;

            ?></td>

      <td><?php echo xtc_draw_input_field('domain_user', $content['domain_user'], 'size="60"')."&nbsp;&nbsp;<span class='dataTableContent'></span>";

            ?></td>

   </tr>

         <tr>

      <td nowrap><?php echo TEXT_LOGIN_STRICT;

            ?></td>

    <td><?php echo xtc_draw_selection_field('login_strict', 'checkbox', '1',$content['login_strict']==1 ? true : false); ?></td>
 

   </tr>

      <tr>
      <td nowrap><?php echo TEXT_TEMPLATE;
            ?></td>
      <td><?php
			# MS 1.06
			echo ms_cfg_pull_down_template_sets($content['current_template']);
            ?></td>
   </tr>

      <tr>
      <td nowrap><?php echo TEXT_CURRENCY;
            ?></td>
      <td><?php
			echo xtc_cfg_pull_down_currencies_sets('default_currency', $content['default_currency'], (USE_DEFAULT_LANGUAGE_CURRENCY=='true'?'disabled':''))."&nbsp;&nbsp;<span class='dataTableContent'>".DESCRIPTION_CURRENCY."</span>";
            ?></td>
   </tr>
      <tr>
      <td nowrap><?php echo TEXT_COUNTRY_TAX;
            ?></td>
      <td><?php
			echo xtc_cfg_pull_down_country_list(isset($content['default_tax'])?$content['default_tax']:STORE_COUNTRY, 'default_tax')."&nbsp;&nbsp;<span class='dataTableContent'>".DESCRIPTION_COUNTRY_TAX."</span>";
            ?></td>
   </tr>
<?php if($_GET['action'] == 'edit'){ ?>
      <tr>
      <td nowrap><?php echo TEXT_CURRENT_CSS;
            ?></td>
      <td><?php
			echo ms_cfg_pull_down_css_sets('current_css', $content['current_css'], $content['current_template'])." <span class='dataTableContent'>(". sprintf(TEXT_PATH_CSS, $content['current_template']) .")</span>";
            ?></td>
   </tr>
<?php
/*
?>
      <tr>
      <td nowrap><?php echo TEXT_CURRENT_CSS_MOBILE;
            ?></td>
      <td><?php
			echo sprintf(TEXT_PATH_CSS, $content['current_template']) .ms_cfg_pull_down_css_sets('current_css_mobile', $content['current_css_mobile'], $content['current_template']);
            ?></td>
   </tr>
      <tr>
      <td nowrap><?php echo TEXT_LOGO;
            ?></td>
      <td><?php
			echo sprintf(TEXT_PATH, $content['current_template']) . ms_cfg_pull_down_logo_sets('logo', $content['logo'], $content['current_template']);
            ?></td>
   </tr>
      <tr>
      <td nowrap><?php echo TEXT_LOGO_EMAIL;
            ?></td>
      <td><?php
			echo sprintf(TEXT_PATH, $content['current_template']) .ms_cfg_pull_down_logo_sets('logo_email', $content['logo_email'], $content['current_template']);
            ?></td>
   </tr>

      <tr>
      <td nowrap><?php echo TEXT_LOGO_MOBILE;
            ?></td>
      <td><?php
			echo sprintf(TEXT_PATH, $content['current_template']) . ms_cfg_pull_down_logo_sets('logo_mobile', $content['logo_mobile'], $content['current_template']);
            ?></td>
   </tr>

<?php
*/
}
?>


   <tr>
      <td nowrap valign="top"><?php echo TEXT_TITLE_ORDER_ID;
            ?></td>
      <td><?php
                  echo xtc_draw_input_field('order_id_next', (isset($content['order_id_next'])?$content['order_id_next']:1), 'size="10"');
                $domain_query = xtc_db_query("SELECT *  FROM " . TABLE_DOMAINS);
                $arrData[] = array('id' => 0, 'text' => 'übernehmen von:');
                    while($domain = xtc_db_fetch_array($domain_query)){
                      $arrData[] = array('id' => $domain['order_id_next'], 'text' => getFirstDomain($domain['domain_http']));
                    }
            echo xtc_draw_pull_down_menu('selStartID', $arrData, 0, "onChange=\"document.forms['edit_content'].order_id_next.value=this.value\"");
            echo "<br><span class='dataTableContent'>".TEXT_TITLE_ORDER_ID_DESCRIPTION."</span>";
         ?></td>
   </tr>   
   
   <?php  if($_GET['doID']>0){   ?>
   <tr>
      <td nowrap valign="bottom"><?php echo "Bestellummernkreis gemeinsam verwenden mit:";
            ?></td>
          <td nowrap valign="top">
             <?php
              xtc_db_data_seek($domain_query, 0);
              $ORDER_IDS = xtc_get_shop_conf('ORDER_IDS_'.$_GET['doID']);     

              $arrOrderIDS=explode(",",$ORDER_IDS);
                            while($domain = xtc_db_fetch_array($domain_query)){
                
                $checked = in_array($domain['domain_id'], $arrOrderIDS)?" checked ":"";
                                if($domain['domain_id']!=$_GET['doID']){
                    echo  '<input type="checkbox" name="order_ids_shop[]" ' . $checked . ' value="'.$domain['domain_id'].'">&nbsp;'.getFirstDomain($domain['domain_http']);
                                        if(in_array($domain['domain_id'], $arrOrderIDS))
                                            echo xtc_draw_hidden_field('ORDER_IDS_OLD[]', $domain['domain_id']);
                                }

                            }
                            ?>
                     </td>
       </tr>
        <?php }  ?>
       
       
      <tr>
      <td><?php echo HEADING_RELATIONS;
            ?></td>
      <td>&nbsp;</td>
   </tr>




      <tr>

      <td colspan="2"><?php

					?>
		     <table class="main"  border="0">
		      <tr>
					<td nowrap  valign="top"><?php echo  TEXT_LANGUAGE; ?></td>
					<td>&nbsp;</td>
		      <td nowrap valign="top"><?php echo  TEXT_STANDARD; ?></td>
					</tr>

			  <?php
				$lng_query = xtc_db_query("select * from " . TABLE_LANGUAGES_TO_DOMAINS . " WHERE domain_id = '" . xtc_db_input($_GET['doID']) . "'");
        $arrDomains2lang=array();
				while($domains2lang = xtc_db_fetch_array($lng_query)){
							$arrDomains2lang[]=$domains2lang['languages_id'];
				}

				for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {

				  echo  "<tr>";
					$checked = "";
				  if(in_array($languages[$i]['id'], $arrDomains2lang) || ($i==0 && $_GET['action'] == 'new'))
				  	$checked = "checked";
					echo '<td align="right"><font size="1">'.$languages[$i]['name'].'</font>&nbsp;<input type="checkbox" name="domain2lang[]" ' . $checked . ' value="'.$languages[$i]['id'].'"></td>';

				  $checked = "";
				  if($languages[$i]['id']==$content['id_languages'])
				  	$checked = "checked";
				  echo '<td>< </td>';
				  echo  '<td><input type="radio" name="id_languages" ' . $checked . ' value="'.$languages[$i]['id'].'"></td>';
    			echo  "</tr>";
				}
				  echo  "<tr>";
					$checked = "";
				  if($content['id_languages']<1)
				  	$checked = "checked";
				  echo  '<td>&nbsp;</td><td>&nbsp;</td><td colspan="5"><input type="radio" name="id_languages" ' . $checked . ' value="0"> > &nbsp;<font size="1">'.DESCRIPTION_LANGUAGE.'</font></td>';
					echo  "</tr>";
					echo  "<tr><td align='right'>";   					
					echo"&nbsp;&nbsp;<span class='dataTableContent'>".TEXT_LANGUAGE_HELP."</span>";
					echo  "</td><td></td><td></td></tr>";
       # echo xtc_cfg_pull_down_language_sets('language_id', $content['language_id'], $languages)."&nbsp;&nbsp;<span class='dataTableContent'>".DESCRIPTION_LANGUAGE."</span>";

                # nur aktive Sprachen in Bearbeitungsansicht verwenden 
                if($_GET['action'] != 'new'){
                    $msLanguages = array();
                    for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) {           
                      if(in_array($languages[$i]['id'], $arrDomains2lang)) 
                         $msLanguages[] = $languages[$i];             
                    } 
                    $languages = $msLanguages;  
                } 
			?>
     </table>

	</td>
   </tr>

	<?php
		if($_GET['action'] == 'edit'){
		   $arrDomains = xtc_get_domains('', NULL, "=1 and domain_id != " . (int)$_GET['doID']);
		   $arrDomains = xtc_get_domains('', $arrDomains, "=0 and domain_id != " . (int)$_GET['doID']);
		} else{
		   $arrDomains = xtc_get_domains('', $arrDomains, "=0");
		}
	  $arrDomains[]=array('id' => 0, 'text' => TEXT_NOCOPY);
	?>
	      <tr>           
	      	<td nowrap valign="top"><?php echo  HEADING_COPY; ?></td>
	        <td nowrap valign="top"><?php 
                echo  xtc_draw_pull_down_menu('copyData', $arrDomains, 0, 'id="copyData" onChange="showList(this)"'); 
                echo '<div id="selCopySrc" style="display: none">';            
                echo '<input type="checkbox" name="copySrc[]" value="'.TABLE_CATEGORIES.'">&nbsp;Kategorien<br />';
                echo '<input type="checkbox" name="copySrc[]" ' . $checked . ' value="'.TABLE_CONTENT_MANAGER.'">&nbsp;Content<br />';
                echo "<br />Einzelseiten:<br /><br />";
                echo  xtc_cfg_select_content('sngCopySrc[]', 5)."<br />";
                echo  xtc_cfg_select_content('sngCopySrc[]')."<br />";
                echo  xtc_cfg_select_content('sngCopySrc[]')."<br />";
                echo  xtc_cfg_select_content('sngCopySrc[]')."<br />";
                echo  xtc_cfg_select_content('sngCopySrc[]')."<br />";
                echo  xtc_cfg_select_content('sngCopySrc[]')."<br />";
                echo  xtc_cfg_select_content('sngCopySrc[]')."<br />";
                echo  xtc_cfg_select_content('sngCopySrc[]')."<br />";
                echo '</div>';
          ?></td>
	      </td>
	    </tr>
	<?php


	if(isset($arr_constants)){
?>

		      <tr>
					<td nowrap  valign="top" colspan="7"><br><br><h3><?php echo  HEADING_CONSTANTS; ?></h3><?php echo  TEXT_CONFIGURATION; ?><br><br></td>
					</tr>

	      <tr>
					<td nowrap  valign="top" colspan="7">   
<table class="main"  border="0">




<?php
$arr_constants[$label][$title]['values'][$domain_data['language_id']]=$domain_data['value'];
$arr_label = array_keys($arr_constants);
for($i=0; $i < count($arr_label); $i++){
		$label = $arr_label[$i];
		$arr_title = array_keys($arr_constants[$label]);

		echo '<tr><td>&nbsp;<b>'.$label."</b></td>";
    for ($k = 0; $k < sizeof($languages); $k ++) {
	    echo '<td valign="top">'.$languages[$k]['name'].'&nbsp;</td>';
	  }
		echo '</tr>';
		for ($j = 0, $m = sizeof($arr_title); $j < $m; $j ++) {
		  $KEY = $arr_title[$j];
		  $TITLE = $arr_constants[$label][$KEY]['title'];
			$CONSTANT = $arr_constants[$label][$KEY]['constant'];
			if($CONSTANT!=''){
				$VALUE = @constant($CONSTANT);
				$LABEL = $arr_constants[$label][$KEY]['label'];
				$set_function = $arr_constants[$label][$KEY]['set_function'];
			  echo  "<tr>";
			  echo  '<td  valign="top" align="right">'.$TITLE.':&nbsp;</td>';
	      
        if (strpos($set_function, 'xtc_cfg_input_email_language') !== false) {    		      
            $parameters = explode(';', $set_function);
            $function = trim($parameters[0]);
            $function = str_replace("xtc_", "ms_", $function);             
            for ($k = 0; $k < sizeof($languages); $k ++) {
              $value = $arr_constants[$label][$KEY]['values'][$languages[$k]['id']];
              $parameters[0] = encode_htmlspecialchars($value); 
              $parameters[2] = $languages[$k]['id']; 
              $parameters[3] = $languages[$k]['code']; 
              echo '<td   valign="top">'. xtc_call_function($function, $parameters) .'&nbsp;</td>';     
            }   
        } else {      
            for ($k = 0; $k < sizeof($languages); $k ++) {
      		     $value = $arr_constants[$label][$KEY]['values'][$languages[$k]['id']];
      				 $value=='False'?$disabled=' disabled ':$disabled='';
      				 if ($set_function) {               
                  if (strpos($set_function, '(') !== false) {
                    #eval('$value_field = ' . $set_function . ' "' . encode_htmlspecialchars($value) . '");');
                    eval('$value_field = ' . $set_function . '"' . htmlspecialchars($value) . '", "'.$CONSTANT.'_'.$languages[$k]['id'].'", "", 1);');
                  } else {
                    echo "$set_function ???";
                    exit;
                  }              
      				 } else {
      			     $value_field = xtc_draw_input_field($CONSTANT.'_'.$languages[$k]['id'], $value,'size=40');
      			   }
      				 if (strstr($value_field,'configuration_value'))
      				 	$value=str_replace('configuration_value',$value, $value_field);
      				 if (strstr($value_field, 'configuration[')){
      				   $value_field=str_replace('configuration[', '', $value_field);
      				   $value_field=str_replace(']', '', $value_field);
      				 }
      			   echo '<td   valign="top">'. $value_field .'&nbsp;</td>';
    			  }        
        }
 
			 echo  "</tr>";
			}
		}
		echo '<tr colspan="7"><td>&nbsp;</td></tr>';
}
?>
</table>
					</td>
					</tr>

 <?php
}
?>


       <tr>
       <td class="main">&nbsp;</td>
        <td  class="main"><br><?php echo '<input style="width:80px" type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>';
            ?><br><br><a class="button" style="width:80px"  onClick="this.blur();" href="<?php echo xtc_href_link(FILENAME_DOMAIN_MANAGER);
            ?>"><?php echo BUTTON_BACK;
            ?></a></td>
   </tr>
   </form>
   </table>
</form>
<?php
}
}



?>
</td>
          </tr>
        </table>
 <?php
if (!$_GET['action']  || $_GET['action']  == 'updateConfiguration') {
?>
 <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr class="dataTableHeadingRow">
     <td class="dataTableHeadingContent" nowrap  ><?php echo TABLE_HEADING_PRODUCTS_ID;
    ?></td>
    <td class="dataTableHeadingContent"  align="left"><?php echo TABLE_HEADING_NAME;?></td>
     <td class="dataTableHeadingContent"  align="left"><?php echo TABLE_HEADING_DOMAIN;?></td>
      <td class="dataTableHeadingContent"  align="left"><?php echo TABLE_HEADING_DOMAIN_SSL;?></td>

		 <td class="dataTableHeadingContent"  align="left"><?php echo BOX_GOOGLE_SITEMAP;?></td>
		 <td class="dataTableHeadingContent"  align="center"><?php echo TABLE_HEADING_COUNT;?></td>
     <td class="dataTableHeadingContent"  align="center"><?php echo TABLE_HEADING_DOMAIN_STATUS;?></td>

		 <td class="dataTableHeadingContent"  align="left"><?php echo TABLE_HEADING_TEMPLATE;?></td>
     <td class="dataTableHeadingContent"  align="left"><?php echo TABLE_HEADING_LANGUAGE;?></td>

       <td class="dataTableHeadingContent"  align="left"><?php echo TABLE_HEADING_DOMAIN_ACTION;?></td>
</tr>
<?php

   
while ($categories_data = xtc_db_fetch_array($categories_query)) {
 if(in_array($categories_data['domain_id'], $arrAccess)){
    	$dom++;
		$checkTotal = countProductsInDomain($categories_data['domain_id']);
    if($checkTotal>0)$categoriesFound=true;

 echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\'" onmouseout="this.className=\'dataTableRow\'">' . "\n";
 ?>
 <td class="dataTableContent" align="center"><?php echo  $categories_data['domain_id']; ?> </td>
    <td class="dataTableContent" ><?php echo xtc_get_store_name($categories_data['domain_id']); ?> </td>

 <td class="dataTableContent" align="left"><a href="http://<?php echo  getFirstDomain($categories_data['domain_http']); ?>" target="_blank"><?php echo  $categories_data['domain_http']; ?></a> </td>
 <td class="dataTableContent" align="left"><a href="https://<?php echo  getFirstDomain($categories_data['domain_https']); ?>" target="_blank"><?php echo  $categories_data['domain_https']; ?></a> </td>
   <td class="dataTableContent" align="left" nowrap><?php
  $file_extension = '.xml';
  $sitemap_filename = 'sitemap_.xml';
  $sitemap_filename = str_replace($file_extension, getFirstDomain($categories_data['domain_http']).$file_extension, $sitemap_filename);
  echo '<a href="' .  xtc_href_link(FILENAME_MODULE_EXPORT, 'set=system&module=sitemaporg&action=edit&dID='.$categories_data['domain_id'], 'NONSSL') . '" target="_blank">Aktualisieren</a> &nbsp;&nbsp;&nbsp;';
  if(is_file('../'.$sitemap_filename))
	 echo '<a href="http://'.getFirstDomain($categories_data['domain_http']).'/'.$sitemap_filename.'" target="_blank">Download</a>';

	?> </td>
	<td class="dataTableContent" align="center"><?php echo $checkTotal; ?> </td>



	<td class="dataTableContent" align="center"><?php
              if ($categories_data['domain_status'] == '1') {
                 echo xtc_image(DIR_WS_IMAGES . 'icon_status_green.gif', TEXT_YES, 10, 10) . '&nbsp;&nbsp;';
								 #if($checkTotal<1)
								 echo '<a href="' . xtc_href_link(FILENAME_DOMAIN_MANAGER, xtc_get_all_get_params(array('action', 'doID')) . 'action=setcflag&flag=0&doID=' . $categories_data['domain_id']) . '">';
								 echo xtc_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', TEXT_NO, 10, 10);
								 #if($checkTotal<1)
								 echo '</a>';
             } else {
                 echo '<a href="' . xtc_href_link(FILENAME_DOMAIN_MANAGER, xtc_get_all_get_params(array('action', 'doID')) . 'action=setcflag&flag=1&doID=' . $categories_data['domain_id']) . '">';
								 echo xtc_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', TEXT_YES, 10, 10);
								 echo '</a>&nbsp;&nbsp;' . xtc_image(DIR_WS_IMAGES . 'icon_status_red.gif', TEXT_NO, 10, 10);
						 }
?> </td>

 <td class="dataTableContent" align="left"><?php echo  $categories_data['current_template']; ?> </td>
  <td class="dataTableContent" align="left"><?php echo ms_getLanguageName($languages, $categories_data['id_languages']) ; ?> </td>
 <td class="dataTableContent" align="left" nowrap>
 <a href="<?php echo xtc_href_link(FILENAME_DOMAIN_MANAGER,'action=edit&doID='.$categories_data['domain_id']); ?>">
<?php echo xtc_image(DIR_WS_ICONS.'icon_edit.gif','Edit','','','style="cursor:pointer"').'  '.TEXT_EDIT.'</a>'; ?>
<?php
 if($categories_data['domain_id']>1){
?>
	 <a href="<?php echo xtc_href_link(FILENAME_DOMAIN_MANAGER,'action=delete&doID='.$categories_data['domain_id']); ?>" onClick="return confirm('<?php echo CONFIRM_DELETE; ?>')">
 <?php
	 echo xtc_image(DIR_WS_ICONS.'delete.gif','Delete','','','style="cursor:pointer" onClick="return confirm(\''.DELETE_ENTRY.'\')"').'  '.TEXT_DELETE.'</a>';
	 if($checkTotal>0)echo "*";
	 echo '&nbsp;&nbsp;';
 }
?>

</td>
</tr>
<?php
    }
}
?>


 </table> <br>
 <a class="button" onClick="this.blur();" href="<?php echo xtc_href_link(FILENAME_DOMAIN_MANAGER, 'action=new');
    ?>"><?php echo BUTTON_NEW_DOMAIN;
    ?></a>
         <br>



 <?php
} // if !$_GET['action']
?>

        </td>
      </tr>

 <?php
 if ((!$_GET['action'] || $_GET['action']  == 'updateConfiguration') && $categoriesFound)
 echo "<tr><td align=\"right\" class=\"dataTableContent\"> *: ".FOOTER_DELETE."</td></tr>";
?>
  <tr><td class="main">

	</td></tr>









    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof //-->
<script>

function showList(elem){
 if($(elem).val()>0){
  $('#selCopySrc').css('display', 'block');
 } else{
  $('#selCopySrc').css('display', 'none');
 }  
}

</script>
</body>
</html>

<?php require(DIR_WS_INCLUDES . 'application_bottom.php');
?>