<?php 
# MODULE MULTISTORE 
if(defined("MULTISTORE") &&  MULTISTORE=='true'){   
  include('includes/extra/modules/multistore/lang_tabs_menu/lang_tabs.php'); 
  
  if($action=='edit_category' || $action=='new_category'){
      # shopspezifische Kategoriebeschreibungen
      for ($dom = 0; $dom < sizeof($arrDomainLang); $dom++) {
        $domID = $arrDomainLang[$dom]['id_domain'];
    	  for ($i = 0, $n = sizeof($arrDomainLang[$dom]['id_lang']); $i < $n; $i++) {
    			if($arrDomainLang[$dom]['id_lang'][$i]>0){
    			  $indexLang = $arrDomainLang[$dom]['indexLang'][$i];   
            echo ('<div id="tab_lang'.$dom.'_' . $i . '">');
            $lng_image = xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/admin/images/'. $languages[$indexLang]['image'], $languages[$i]['name']);
            $categories_desc_fields = ms_get_categories_desc_fields($cInfo->categories_id, $languages[$indexLang]['id'], $domID);
            ?>
            <div class="bg_notice" style="height:5px;"></div>
            <div class="main bg_notice" style="padding:3px; line-height:20px;">
              <?php echo $lng_image ?>&nbsp;<b><?php echo TEXT_EDIT_CATEGORIES_NAME; ?>&nbsp;</b><?php echo xtc_draw_input_field('categories_name[' . $domID . '][' . $languages[$indexLang]['id'] . ']', (isset($categories_name[$languages[$indexLang]['id']]) ? stripslashes($categories_name[$languages[$indexLang]['id']]) : $categories_desc_fields['categories_name']), 'style="width:80%" maxlength="255"'); ?>
            </div>
            <div class="main" style="padding: 3px; line-height:20px;">
              <?php echo $lng_image ?>&nbsp;<b><?php echo TEXT_EDIT_CATEGORIES_HEADING_TITLE; ?>&nbsp;</b><?php echo xtc_draw_input_field('categories_heading_title[' . $domID . '][' . $languages[$indexLang]['id'] . ']', (isset($categories_name[$languages[$indexLang]['id']]) ? stripslashes($categories_name[$languages[$indexLang]['id']]) : $categories_desc_fields['categories_heading_title']), 'style="width:80%" maxlength="255"'); ?>
            </div>
            <div class="main" style="padding: 3px; line-height:20px;">
              <b><?php echo $lng_image . '&nbsp;' . TEXT_EDIT_CATEGORIES_DESCRIPTION; ?></b><br />
              <?php echo xtc_draw_textarea_field('categories_description[' . $domID . '][' . $languages[$indexLang]['id'] . ']', 'soft', '100', '25', (isset($categories_description[$languages[$indexLang]['id']]) ? stripslashes($categories_description[$languages[$indexLang]['id']]) : $categories_desc_fields['categories_description'])); ?>
            </div>
            <div class="main" style="vertical-align:top; padding: 3px; line-height:20px;">
              <?php echo $lng_image . '&nbsp;' . TEXT_META_TITLE .' (max. ' . META_TITLE_LENGTH . ' ' . TEXT_CHARACTERS .')'; ?> <br/>
              <?php echo xtc_draw_input_field('categories_meta_title[' . $domID . '][' . $languages[$indexLang]['id'] . ']',(isset($categories_meta_title[$languages[$indexLang]['id']]) ? stripslashes($categories_meta_title[$languages[$indexLang]['id']]) : $categories_desc_fields['categories_meta_title']), 'style="width:100%" maxlength="' . META_TITLE_LENGTH . '"'); ?><br/>
              <?php echo $lng_image . '&nbsp;' . TEXT_META_DESCRIPTION .' (max. ' . META_DESCRIPTION_LENGTH . ' ' . TEXT_CHARACTERS .')'; ?> <br/>
              <?php echo xtc_draw_input_field('categories_meta_description[' . $domID . '][' . $languages[$indexLang]['id'] . ']', (isset($categories_meta_description[$languages[$indexLang]['id']]) ? stripslashes($categories_meta_description[$languages[$indexLang]['id']]) : $categories_desc_fields['categories_meta_description']),'style="width:100%" maxlength="' . META_DESCRIPTION_LENGTH . '"'); ?><br/>
              <?php echo $lng_image . '&nbsp;' . TEXT_META_KEYWORDS .' (max. ' . META_KEYWORDS_LENGTH . ' ' . TEXT_CHARACTERS .')'; ?> <br/>
              <?php echo xtc_draw_input_field('categories_meta_keywords[' . $domID . '][' . $languages[$indexLang]['id'] . ']',(isset($categories_meta_keywords[$languages[$indexLang]['id']]) ? stripslashes($categories_meta_keywords[$languages[$indexLang]['id']]) : $categories_desc_fields['categories_meta_keywords']),'style="width:100%" maxlength="' . META_KEYWORDS_LENGTH . '"'); ?>
            </div>
          </div>   
          <?php
          }
        }        
      }         
  } elseif($action=='edit_product' || $action=='new_product'){     
    # shopspezifische Artikelbeschreibungen
    for ($dom = 0; $dom < sizeof($arrDomainLang); $dom++) {
      $domID = $arrDomainLang[$dom]['id_domain'];
  	  for ($i = 0, $n = sizeof($arrDomainLang[$dom]['id_lang']); $i < $n; $i++) {
  			if($arrDomainLang[$dom]['id_lang'][$i]>0){
          $indexLang = $arrDomainLang[$dom]['indexLang'][$i];
          echo ('<div id="tab_lang'.$dom.'_' . $i . '">');
          $lng_image = xtc_image(DIR_WS_LANGUAGES . $languages[$indexLang]['directory'] .'/admin/images/'. $languages[$indexLang]['image'], $languages[$indexLang]['name']);
          $products_desc_fields = ms_get_products_desc_fields($pInfo->products_id, $languages[$indexLang]['id'], $domID);
          ?>
          <div class="bg_notice" style="height:5px;"></div>
          <div class="main bg_notice" style="padding:3px; line-height:20px;">                                                                                                   
            <?php echo $lng_image ?>&nbsp;<b><?php echo TEXT_PRODUCTS_NAME; ?>&nbsp;</b><?php echo xtc_draw_input_field('products_name[' . $domID . '][' . $languages[$indexLang]['id'] . ']', (isset($products_name[$languages[$indexLang]['id']]) ? stripslashes($products_name[$languages[$indexLang]['id']]) : $products_desc_fields['products_name']),'style="width:80%" maxlength="255"'); ?>
          </div>
          <div class="main" style="padding: 3px; line-height:20px;">
             <?php echo $lng_image. '&nbsp;'.TEXT_PRODUCTS_URL . '&nbsp;<small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>&nbsp;'; ?><?php echo xtc_draw_input_field('products_url[' . $domID . '][' . $languages[$indexLang]['id'] . ']', (isset($products_url[$languages[$indexLang]['id']]) ? stripslashes($products_url[$languages[$indexLang]['id']]) : $products_desc_fields['products_url']),'style="width:70%" maxlength="255"'); ?>
          </div>
          <!-- input boxes desc, meta etc -->
          <div class="main" style="padding: 3px; line-height:20px;">
             <b><?php echo $lng_image . '&nbsp;' . TEXT_PRODUCTS_DESCRIPTION; ?></b><br />
             <?php echo xtc_draw_textarea_field('products_description_'.$domID.'_' . $languages[$indexLang]['id'], 'soft', '103', '30', (isset($products_description[$languages[$indexLang]['id']]) ? stripslashes($products_description[$languages[$indexLang]['id']]) : $products_desc_fields['products_description'])); ?>
          </div>
          <div style="height: 8px;"></div>
          <div class="main" style="vertical-align:top; padding: 3px; line-height:20px;">
            <b><?php echo $lng_image . '&nbsp;' . TEXT_PRODUCTS_SHORT_DESCRIPTION; ?></b><br />
            <?php echo xtc_draw_textarea_field('products_short_description_'.$domID.'_' . $languages[$indexLang]['id'], 'soft', '103', '20', (isset($products_short_description[$languages[$indexLang]['id']]) ? stripslashes($products_short_description[$languages[$indexLang]['id']]) : $products_desc_fields['products_short_description'])); ?>
          </div>
          <div class="main" style="vertical-align:top; padding: 3px; line-height:20px;">
            <b><?php echo $lng_image . '&nbsp;' . TEXT_PRODUCTS_ORDER_DESCRIPTION; ?></b><br />
            <?php echo xtc_draw_textarea_field('products_order_description[' . $domID . '][' . $languages[$indexLang]['id'] . ']', 'soft', '103', '10', (isset($products_order_description[$languages[$indexLang]['id']]) ? stripslashes($products_order_description[$languages[$indexLang]['id']]) : $products_desc_fields['products_order_description']), 'style="width:100%; height:50px;"'); ?>
          </div>
          <div class="main" style="vertical-align:top; padding: 3px; line-height:20px;">
              <?php echo $lng_image. '&nbsp;'. TEXT_PRODUCTS_KEYWORDS . ' (max. ' . META_PRODUCTS_KEYWORDS_LENGTH . ' ' . TEXT_CHARACTERS .')'; ?> <br/>
              <?php echo xtc_draw_input_field('products_keywords[' . $domID . '][' . $languages[$indexLang]['id'] . ']',(isset($products_keywords[$languages[$indexLang]['id']]) ? stripslashes($products_keywords[$languages[$indexLang]['id']]) : $products_desc_fields['products_keywords']), 'style="width:100%" maxlength="' . META_PRODUCTS_KEYWORDS_LENGTH . '"'); ?><br/>
              <?php echo $lng_image. '&nbsp;'. TEXT_META_TITLE. ' (max. ' . META_TITLE_LENGTH . ' ' . TEXT_CHARACTERS .')'; ?> <br/>
              <?php echo xtc_draw_input_field('products_meta_title[' . $domID . '][' . $languages[$indexLang]['id'] . ']',(isset($products_meta_title[$languages[$indexLang]['id']]) ? stripslashes($products_meta_title[$languages[$indexLang]['id']]) : $products_desc_fields['products_meta_title']), 'style="width:100%" maxlength="' . META_TITLE_LENGTH . '"'); ?><br/>
              <?php echo $lng_image. '&nbsp;'. TEXT_META_DESCRIPTION. ' (max. ' . META_DESCRIPTION_LENGTH . ' ' . TEXT_CHARACTERS .')'; ?> <br/>
              <?php echo xtc_draw_input_field('products_meta_description[' . $domID . '][' . $languages[$indexLang]['id'] . ']',(isset($products_meta_description[$languages[$indexLang]['id']]) ? stripslashes($products_meta_description[$languages[$indexLang]['id']]) : $products_desc_fields['products_meta_description']), 'style="width:100%" maxlength="' . META_DESCRIPTION_LENGTH . '"'); ?><br/>
              <?php echo $lng_image. '&nbsp;'. TEXT_META_KEYWORDS. ' (max. ' . META_KEYWORDS_LENGTH . ' ' . TEXT_CHARACTERS .')'; ?> <br/>
              <?php echo xtc_draw_input_field('products_meta_keywords[' . $domID . '][' . $languages[$indexLang]['id'] . ']', (isset($products_meta_keywords[$languages[$indexLang]['id']]) ? stripslashes($products_meta_keywords[$languages[$indexLang]['id']]) : $products_desc_fields['products_meta_keywords']), 'style="width:100%" maxlength="' . META_KEYWORDS_LENGTH . '"'); ?>
          </div>
          <?php
          
          if (file_exists("includes/modules/new_products_content.php")) {
            include("includes/modules/new_products_content.php");
          }
  
          echo ('</div>');
        }
      }
    }   
  }     
  
}
?>