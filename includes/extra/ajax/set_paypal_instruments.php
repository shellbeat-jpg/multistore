<?php
/* -----------------------------------------------------------------------------------------
   $Id: set_paypal_instruments.php 14191 2022-03-24 07:03:40Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  function set_paypal_instruments() {
    if (isset($_POST['paypal_instruments'])
        && $_POST['paypal_instruments'] != ''
        ) 
    {
      $_SESSION['paypal_instruments'] = $_POST['paypal_instruments'];
    }
  }
