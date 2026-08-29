<?php 
if(MULTISTORE == 'true'){  
  if($_GET['cPath'] == '0'){   
    $string_domains = $category['string_domains'];
    $arrDomains = explode( ";", $string_domains);     
    if($cInfo->string_domains != '' && !checkAdminAccess($cInfo->string_domains, $cInfo->categories_id)){
       die( 'Direct Access to this location is not allowed.' );
       exit;
  	} 
  ?>
  
      <div style="padding:4px;">
        <div class="main div_header"><?php echo MULTISTORE_HEADER; ?></div>
        <div class="div_box" style="margin-bottom:0;">
          <div class="main flt-l" style="width:265px"><?php echo TEXT_DOMAIN; ?>:</div>
          <div class="main customers-groups" style="width: auto;">
          <?php
          for ($i=0;$n=sizeof($domain_array),$i<$n;$i++) { 
          		$arrIDdomains[$domain_array[$i]['id']]=$domain_array[$i]['text'];
          		if ($domain_array[$i]['id'] > 0 && isset($arrDomains) && (in_array($domain_array[$i]['id'], $arrDomains) || in_array($domain_array[$i]['id'], $domain_array_active)))
          		    echo xtc_draw_hidden_field('string_domains_old[]', $domain_array[$i]['id'])."\n";
          		if ($domain_array[$i]['id'] > 0 && isset($arrDomains) &&  in_array($domain_array[$i]['id'], $arrDomains)) {
          			$checked='checked ';
          		} else {
          			$checked='';
          		}          
          		echo '<div style="width:100%;height:35px"><label style="float:left"><input type="checkbox" '.($i==0?'onChange="SwitchItems(\'new_category\', \'string_domains[]\')"':'').' name="string_domains[]" value="'.$domain_array[$i]['id'].'" '.$checked.'> '.$domain_array[$i]['text'].'</label>';

              if($i>0 && defined('MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS') && MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS == 'true' && sizeof($domain_array)>2 && $_GET['action'] == 'edit_category'){
                 echo '<label style="float:right;margin-top:-3px;margin-left:15px;">';
                 echo RADIO_TEXT_SUB;
                 echo "&nbsp;"; 
              	 if(count($domain_array)>1){
        				   $arrSelectbox = array();
        				   for ($j=1;$m=sizeof($domain_array),$j<$m;$j++) {
        								if($domain_array[$i]['id'] > 0 && $domain_array[$j]['id'] != $domain_array[$i]['id'] && $domain_array_az[$domain_array[$j]['id']]['ctotal'] > 0 )
        								   $arrSelectbox[]=array('id' => $domain_array[$j]['id'], 'text' => $domain_array[$j]['text']);
        				   }
        				 }
        		     $checked=($domain_array_az[$domain_array[$i]['id']]['ctotal']>0);
                 $arrSelectbox[]=array('id' => 0, 'text' => RADIO_NOCHANGE);
                 $default = 0;
        				 echo xtc_draw_pull_down_menu('copyDescription_'.$domain_array[$i]['id'], $arrSelectbox, $default, 'style="min-width:220px"');         			
                 echo '</label>';
              } 
              echo '<br />';  
              echo '</div>'. PHP_EOL; 
          }
          ?>      
          </div>  
          <div style="clear:both"></div>  
        </div>      
      </div>  
  <?php
  } 
} 
 
?>