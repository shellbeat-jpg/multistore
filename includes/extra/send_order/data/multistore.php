<?php

  # MODULE MULTISTORE
  if(defined("MULTISTORE") &&  MULTISTORE=='true'){
    $CURRENT_TEMPLATE = CURRENT_TEMPLATE;
    $HTTP_SERVER = HTTP_SERVER;
    if (isset($send_by_admin)) {
      $CURRENT_TEMPLATE = xtc_get_template_by_domain($order->info['id_domain'], $domain_array);
      $HTTP_SERVER =  xtc_get_domain_server($order->info['id_domain'], (ENABLE_SSL_CATALOG == 'true'?'https':'http'));    
    }    
        
    $smarty->assign('oID', ms_build_order_id($order));    
    $smarty->assign('tpl_path', $HTTP_SERVER.DIR_WS_CATALOG.'templates/'.$CURRENT_TEMPLATE.'/');
    $smarty->assign('logo_path', $HTTP_SERVER.DIR_WS_CATALOG.'templates/'.$CURRENT_TEMPLATE.'/img/');
    $smarty->assign('img_path', $HTTP_SERVER.DIR_WS_CATALOG.DIR_WS_IMAGES.'product_images/'. (defined('SHOW_IMAGES_IN_EMAIL_DIR')? SHOW_IMAGES_IN_EMAIL_DIR : 'thumbnail').'_images/');
  }

  
?>