<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_category_link.inc.php 14836 2022-12-15 16:40:06Z GTB $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2005 XT-Commerce


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  require_once(DIR_FS_INC . 'xtc_get_category_path.inc.php');

  function xtc_category_link($cID, $name = '') {
    $params = 'cPath='.xtc_get_category_path($cID);
    if (SEARCH_ENGINE_FRIENDLY_URLS == 'true' && $name != '') {
      $params .= '&name='.base64_encode(strip_tags($name));
    }

    return $params;
  }
