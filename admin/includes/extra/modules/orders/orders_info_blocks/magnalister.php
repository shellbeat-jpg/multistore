<?php
/* -----------------------------------------------------------------------------------------
   $Id: magnalister.php 15225 2023-06-13 11:43:20Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

  if (defined('MODULE_MAGNALISTER_STATUS')
      && MODULE_MAGNALISTER_STATUS == 'True'
      )
  {
    if (function_exists('magnaExecute')) echo magnaExecute('magnaRenderOrderDetails', array('oID' => $oID), array('order_details.php'));
  }
