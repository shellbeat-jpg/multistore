<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_build_order_id.php  2011-07-13 hechicero $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2011 hechicero
   
   Mit tatkräftiger Unterstützung von Matt. Vielen Dank!
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003  nextcommerce (xtc_array_to_string.inc.php,v 1.3 2003/08/13); www.nextcommerce.org 

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
 
function xtc_build_order_id($data, $id=0) {   
  if(MULTISTORE!='true')
     return false;

  if(is_object($data)) {    
     if(isset($data->info['date_purchased'])){
        $date = $data->info['date_purchased']; 
        $id_domain = $data->info['id_domain'];   
        $id_languages = $data->info['id_languages'];     
     }elseif(isset($data->date_purchased)){
        $date = $data->date_purchased;
        $id_domain = $data->id_domain;
        $id_languages = $data->id_languages;   
     }     
  
     if(isset($data->info['orders_id_shop']) && $data->info['orders_id_shop'] > 0){
        $orders_id = $data->info['orders_id_shop'];         
     }elseif(isset($data->orders_id_shop)){
        $orders_id = $data->orders_id_shop;         
     }elseif(isset($data->info['order_id'])){
        $orders_id = $data->info['order_id'];
     }elseif(isset($data->orders_id)){
        $orders_id = $data->orders_id;         
     }
  }elseif(is_array($data) && isset($data['date_purchased'])) {
     $date = $data['date_purchased'];    
     $id_domain = $data['id_domain'];  
     $id_languages = $data['id_languages']; 
     
     if(isset($data['orders_id_shop']) && $data['orders_id_shop'] > 0){
        $orders_id = $data['orders_id_shop'];         
     }elseif(isset($data['orders_id'])){
        $orders_id = $data['orders_id'];
     }
  }

  if(!isset($id_domain))
    $id_domain = ID_DOMAIN;
  if(!isset($id_languages))
    $id_languages = (int) $_SESSION['languages_id']; 
  if($id>0)
    $orders_id = $id;  
  if($date!=''){
     $date = strtotime($date);
  } else{
     $date = time();
  } 
  $ORDERS_NR_TYPE = getMultistoreConfigValue('ORDERS_NR_TYPE', $id_domain, $id_languages);
 
  if($ORDERS_NR_TYPE=='1'){
    return sprintf("%d%02d%02d-%05d", strftime("%y", $date), strftime("%m", $date), strftime("%d", $date), $orders_id);
  }elseif($ORDERS_NR_TYPE=='2'){
    return sprintf("%05d", $orders_id).'-'.$id_domain;
  }else{
    return $orders_id;
  }
}

?>