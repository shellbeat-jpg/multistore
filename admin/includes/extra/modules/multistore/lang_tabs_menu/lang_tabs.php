<?php
/* --------------------------------------------------------------
   $Id: lang_tabs.php 6490 2014-03-28 10:39:12Z web28 $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   --------------------------------------------------------------
   Released under the GNU General Public License
   --------------------------------------------------------------*/
  defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
  
  ?>  
  <link rel="stylesheet" type="text/css" href="includes/lang_tabs_menu/lang_tabs_menu.css">
  <script type="text/javascript" src="includes/extra/modules/multistore/lang_tabs_menu/lang_tabs_menu.js"></script>
  <?php 

  $langtabs = '<div class="tablangmenu"><ul>';
	# MODULE MULTISTORE
  $cssDomain = 'background: #d0d0d0;';
  for ($d = 0; $d < sizeof($domain_array_active); $d++) {
		$title = $domain_array_az[$domain_array_active[$d]]['text'];
		$enabled = 1;
    $cssDomain .= ($enabled>0?'color: #000000;':'color: #aaaaaa;');
    $langtabs .= '<li title="'.$title.'" onclick="showTabDom('.$d.', '.$n.', '.sizeof($domain_array_active).')" style="'.$cssDomain.'cursor: pointer;" id="domselect_'.$d.'">'.$domain_array_az[$domain_array_active[$d]]['text'].'</li>';

		$cssDomain='';
  }
  $langtabs .= '</ul></div>';

  for ($dom = 0; $dom < sizeof($arrDomainLang); $dom++) {

    if(isset($arrDomainLang[$dom]['id_lang'])){

		  $csstabstyle = 'border: 1px solid #aaaaaa; padding: 5px; width: 850px; margin-top: -1px; margin-bottom: 10px; float: left;background: #F3F3F3;';
      if($dom < 1)$csstab = '<style type="text/css">' .  '#tab_lang'.$dom.'_0' . '{display: block;' . $csstabstyle . '}';
      $langtabs .= '<div id="tablangmenu_'.$dom.'" class="tablangmenu" style="display: '.($dom<1?'block':'none').';"><ul>';
			$csstab_nojs = '<style type="text/css">';
		  for ($i = 0, $n = sizeof($arrDomainLang[$dom]['id_lang']); $i < $n; $i++) {
				$tabtmp = "\'tab_lang".$dom."_$i\'," ;
				$langtabs.= '<li onclick="showTab('. $tabtmp. $n.', '.$dom.', '.sizeof($domain_array_active).')" style="cursor: pointer;" id="tabselect'.$dom.'_' . $i .'">' .xtc_image(DIR_WS_LANGUAGES . $languages[$arrDomainLang[$dom]['indexLang'][$i]]['directory'] .'/admin/images/'. $languages[$arrDomainLang[$dom]['indexLang'][$i]]['image'], $languages[$arrDomainLang[$dom]['indexLang'][$i]]['name']) . ' ' . $languages[$arrDomainLang[$dom]['indexLang'][$i]]['name'].  '</li>';
		    if($i > 0 || $dom > 0) $csstab .= '#tab_lang'.$dom.'_' . $i .'{display: none;' . $csstabstyle . '}';
		    $csstab_nojs .= '#tab_lang'.$dom.'_' . $i .'{display: block;' . $csstabstyle . '}';
		  }
		  $langtabs.= '</ul></div>';
	  }
	   $csstab  .= "#tabselect".$dom."_0{  background: #d0d0d0; color: #000000; } ";
  }
  $csstab .= '</style>';
  $csstab_nojs .= '</style>';
  ?>
  <?php if (USE_ADMIN_LANG_TABS != 'false') { ?>
  <script type="text/javascript">
    document.write('<?php echo ($csstab);?>');
    document.write('<?php echo ($langtabs);?>');
    //alert ("TEST");
  </script>
  <?php } else echo ($csstab_nojs);?>
  <noscript>
    <?php echo ($csstab_nojs);?>
  </noscript>
