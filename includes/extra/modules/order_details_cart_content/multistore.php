<?php
               
	 if(defined("MULTISTORE") &&  MULTISTORE=='true' && MS_MULTIBASKET=='true' && isset($products[$i]['http']) && isset($module_content[$i]) && HTTP_SERVER != $products[$i]['http'])
      $module_content[$i]['PRODUCTS_LINK'] = str_replace(HTTP_SERVER, $products[$i]['http'], $module_content[$i]['PRODUCTS_LINK']);
     
?>