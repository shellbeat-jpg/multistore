<?php
  /* --------------------------------------------------------------
   $Id: array_map_recursive.inc.php 16163 2024-10-08 14:23:06Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   based on:
   (c) 2015 Timo Paul Dienstleistungen

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  function array_map_recursive($callback, $array) {
    if (is_array($array)) {
      foreach ($array as $key => $val) {
        if (is_array($val)) {
          $array[$key] = array_map_recursive($callback, $val);
        } else {
           $array[$key] = call_user_func($callback, $val);
        }
      }
    }
    
    return $array;
  }
