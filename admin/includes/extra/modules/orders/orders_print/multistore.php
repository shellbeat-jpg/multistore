<?php
  $smarty->assign('logo_path',(MULTISTORE=='true'?$HTTP_SERVER:HTTP_SERVER) . DIR_WS_CATALOG.'templates/'.(MULTISTORE=='true'?$CURRENT_TEMPLATE:CURRENT_TEMPLATE).'/img/');
  $smarty->assign('tpl_path',(MULTISTORE=='true'?$HTTP_SERVER:HTTP_SERVER) . DIR_WS_CATALOG.'templates/'.(MULTISTORE=='true'?$CURRENT_TEMPLATE:CURRENT_TEMPLATE).'/');                     
  $smarty->assign('oID', MULTISTORE=='true'?ms_build_order_id($order):$order->info['order_id']);
?>