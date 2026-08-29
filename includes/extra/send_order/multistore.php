<?php

  # MODULE MULTISTORE
  if(defined("MULTISTORE") &&  MULTISTORE=='true'){
    $smarty->assign('oID', ms_build_order_id($order));    
    $smarty->assign('tpl_path', $HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/');
    $smarty->assign('logo_path', $HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
    $smarty->assign('img_path', $HTTP_SERVER.DIR_WS_CATALOG.DIR_WS_IMAGES.'product_images/'. (defined('SHOW_IMAGES_IN_EMAIL_DIR')? SHOW_IMAGES_IN_EMAIL_DIR : 'thumbnail').'_images/');
  }

  
?>