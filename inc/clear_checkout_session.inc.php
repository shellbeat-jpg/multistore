<?php
/* -----------------------------------------------------------------------------------------
   $Id: clear_checkout_session.inc.php 14368 2022-04-25 10:56:11Z GTB $

   modified eCommerce Shopsoftware - community made shopping
   http://www.modified-shop.org

   Copyright (c) 2009 - 2012 modified eCommerce Shopsoftware
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  function clear_checkout_session() {
    unset($_SESSION['sendto']);
    unset($_SESSION['billto']);
    unset($_SESSION['shipping']);
    unset($_SESSION['payment']);
    unset($_SESSION['delivery_zone']);
    unset($_SESSION['billing_zone']);
  }
