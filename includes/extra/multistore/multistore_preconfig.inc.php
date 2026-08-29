<?php

  # MODULE MULTISTORE   
  /*     
    Beispiele für SSL-Proxies (Stand 2015):
    Domainfactory: "sslsites.de/"
    Strato: "ssl-id.de/"
    1&1: "ssl.kundenserver.de/"    
  */ 
  # Angabe ohne "https://" und mit abschliessendem Slash, siehe Beispiele  
  $SSL_PROXY_URL = '';

  if(isset($_SERVER['SCRIPT_URI'])) {
    $HOST_URI = parse_url($_SERVER['SCRIPT_URI']);
    if (isset($HOST_URI['host']) && isset($_SERVER['HTTP_HOST']) && ($HOST_URI['host'] != $_SERVER['HTTP_HOST']))
        $_SERVER['HTTP_HOST'] = $HOST_URI['host'];
  }
  if(isset($_SERVER['HTTP_HOST'])) {
    define('HTTP_SERVER', 'http://' . $_SERVER["HTTP_HOST"]);
    define('HTTPS_SERVER', 'https://' . $SSL_PROXY_URL . $_SERVER["HTTP_HOST"]);
  }

?>
