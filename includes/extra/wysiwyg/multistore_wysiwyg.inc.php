<?php
if(defined("MULTISTORE") &&  MULTISTORE=='true' && MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS == 'true'){
  global $id_domain_wysiwyg;
  switch($type) {
      // WYSIWYG editor categories_description textarea named categories_description[langID]
      case 'categories_description':              
          $editorName = 'categories_description['.$id_domain_wysiwyg.']['.$langID.']'; 
          $default_editor_height = 300;
          break;
          
      // WYSIWYG editor products_description textarea named products_description_langID
      case 'products_description':
          $editorName = 'products_description_'.$id_domain_wysiwyg.'_'.$langID;
          $default_editor_height = 400;
          break;
      // WYSIWYG editor products short description textarea named products_short_description_langID
      case 'products_short_description':
          $editorName = 'products_short_description_'.$id_domain_wysiwyg.'_'.$langID;
          $default_editor_height = 300;
          break;
  }
}
?>