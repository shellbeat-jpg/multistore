<?php
require('includes/application_top.php');
require(DIR_WS_MULTISTORE.'domain_konfigurator.inc.php');
require (DIR_WS_INCLUDES.'head.php');
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

<!-- header_eof //-->

<!-- body //-->
<?php
echo xtc_draw_form('edit_content', FILENAME_DOMAIN_KONFIGURATOR, 'action=update', 'post')  ;
?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH;
?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH;
?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php');
?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="80" rowspan="2"><?php echo xtc_image(DIR_WS_ICONS . 'heading/icon_categories.png');
?></td>
    <td class="pageHeading"><?php echo HEADING_TITLE;
?></td>
  </tr>

		      <tr>
		      <td class="main"><?php echo TEXT_MANAGER;?></td>
					</tr>
</table>
</td>
      </tr>
      <tr>
        <td>
        <table width="100%" border="0">
          <tr>


 		<td valign="top">
		     <table class="main"  border="0">
		      <tr>
		      <td colspan="3">&nbsp;<h3><?php echo BOX_HEADING_CONFIGURATION;?></h3></td>
					</tr>
            
            
		      <tr>
		      <td nowrap valign="top"><span style="text-decoration: underline;"><?php echo  TABLE_HEADING_KEY; ?></span></td>
		      <td>&nbsp;</td>
		      <td nowrap valign="top"><span style="text-decoration: underline;"><?php echo  TABLE_HEADING_SELECT; ?></span></td>
					</tr>

<?php
	for($j=0; $j < count($configurator_keys); $j++){
	  	$key = $configurator_keys[$j];
	  	if(count($configurator_array[$key])>0){
				echo  "<tr>";
				echo  '<td colspan="3"><b>'. $key.'</b></td>';
				echo  "</tr>";
				for($i=0; $i < count($configurator_array[$key]); $i++){
					echo  "<tr>";
					echo  '<td><i>'. $configurator_array[$key][$i]['title'].'</i></td>';
					echo '<td>&nbsp;</td>';
					echo  '<td>';
					echo xtc_draw_checkbox_field('CONSTANT_'.$configurator_array[$key][$i]['key'], 1, (in_array($configurator_array[$key][$i]['key'], $arr_data)));
					echo xtc_draw_hidden_field('SOURCE_'.$configurator_array[$key][$i]['key'], $configurator_array[$key][$i]['group']);
					echo  '</td>';
					echo  "</tr>";
				}
			}
	}
?>
    </table>
		</td>




					<td valign="top">
		     <table class="main"  border="0">
		      <tr>
		      <td colspan="3">&nbsp;<h3><?php echo BOX_PAYMENT;?></h3></td>
					</tr>
					
    <?php if(count($configurator_array['payment'])>0){ ?>
		      <tr>
		      <td nowrap valign="top"><span style="text-decoration: underline;"><?php echo  TABLE_HEADING_KEY; ?></span></td>
		      <td>&nbsp;</td>
		      <td nowrap valign="top"><span style="text-decoration: underline;"><?php echo  TABLE_HEADING_SELECT; ?></span></td>
					</tr>
                    
                    <?php }else{ ?>
              <tr>
              <td nowrap valign="top" colspan="3"><?php echo TEXT_PAYMENT_ERROR; ?></td>  
                    </tr>
          <?php } ?>
					
 


<?php
for($i=0; $i < count($configurator_array['payment']); $i++){
		$key = $configurator_keys[$j];
		$mark = '';
		if($configurator_array[$key][0]['error']>0)
		   $mark = '*';
	if($i==0){
		echo  "<tr>";
		echo  '<td colspan="3"><b>'. $configurator_array['payment'][$i]['name'].$mark.'</b></td>';
		echo  "</tr>";
	}
	echo  "<tr>";
	echo  '<td><i>'. $configurator_array['payment'][$i]['title'].'</i></td>';
	echo '<td>&nbsp;</td>';
	echo  '<td>';
	echo xtc_draw_checkbox_field('CONSTANT_'.$configurator_array['payment'][$i]['key'], 1, (in_array($configurator_array['payment'][$i]['key'], $arr_data)));
  echo xtc_draw_hidden_field('SOURCE_'.$configurator_array['payment'][$i]['key'], 'payment_'.$configurator_array['payment'][$i]['class']);
	echo '</td>';
	echo  "</tr>";
	if($configurator_array['payment'][$i+1]['name'] != $configurator_array['payment'][$i]['name']){
		echo  "<tr>";
		echo  '<td colspan="3"><b>'. $configurator_array['payment'][$i+1]['name'].$mark.'</b></td>';
		echo  "</tr>";
	}
}
?>
    </table>
		</td>




		<td valign="top">
		     <table class="main"  border="0">
		      <tr>
		      <td colspan="3">&nbsp;<h3><?php echo BOX_SHIPPING;?></h3></td>
					</tr>
                <?php if(count($configurator_array['shipping'])>0){ ?>
		      <tr>
		      <td nowrap valign="top"><span style="text-decoration: underline;"><?php echo  TABLE_HEADING_KEY; ?></span></td>
		      <td>&nbsp;</td>
		      <td nowrap valign="top"><span style="text-decoration: underline;"><?php echo  TABLE_HEADING_SELECT; ?></span></td>
					</tr>
                    <?php }else{ ?>
              <tr>
              <td nowrap valign="top" colspan="3"><?php echo TEXT_SHIPPING_ERROR; ?></td>  
                    </tr>
          <?php } ?>

<?php
for($i=0; $i < count($configurator_array['shipping']); $i++){
	$key = $configurator_keys[$j];
	$mark = '';
	if($i==0){
	 if($configurator_array['shipping'][$i]['error']>0)
		   $mark = '*';
		echo  "<tr>";
		echo  '<td colspan="3"><b>'. $configurator_array['shipping'][$i]['name'].$mark.'</b></td>';
		echo  "</tr>";
	}
	echo  "<tr>";
	echo  '<td><i>'. $configurator_array['shipping'][$i]['title'].'</i></td>';
	echo '<td>&nbsp;</td>';
	echo  '<td>';
	echo xtc_draw_checkbox_field('CONSTANT_'.$configurator_array['shipping'][$i]['key'], 1, (in_array($configurator_array['shipping'][$i]['key'], $arr_data)));
  echo xtc_draw_hidden_field('SOURCE_'.$configurator_array['shipping'][$i]['key'], 'shipping_'.$configurator_array['shipping'][$i]['class']);
	echo '</td>';
	echo  "</tr>";
	if($configurator_array['shipping'][$i+1]['name'] != $configurator_array['shipping'][$i]['name']){
    $mark = '';
		if($configurator_array['shipping'][$i+1]['error']>0)
    	$mark = '*';
		echo  "<tr>";
		echo  '<td colspan="3"><b>'. $configurator_array['shipping'][$i+1]['name'].$mark.'</b></td>';
		echo  "</tr>";
	}
}
?>





    </table>
		</td>

		<td valign="top">
		     <table class="main"  border="0">
		      <tr>
		      <td colspan="3">&nbsp;<h3><?php echo BOX_ORDER_TOTAL;?></h3></td>
					</tr>
		      <tr>
		      <td nowrap valign="top"><span style="text-decoration: underline;"><?php echo  TABLE_HEADING_KEY; ?></span></td>
		      <td>&nbsp;</td>
		      <td nowrap valign="top"><span style="text-decoration: underline;"><?php echo  TABLE_HEADING_SELECT; ?></span></td>
					</tr>


<?php
for($i=0; $i < count($configurator_array['ordertotal']); $i++){
	$key = $configurator_keys[$j];
	$mark = '';
	if($i==0){
	 if($configurator_array['ordertotal'][$i]['error']>0)
		   $mark = '*';
		echo  "<tr>";
		echo  '<td colspan="3"><b>'. $configurator_array['ordertotal'][$i]['name'].$mark.'</b></td>';
		echo  "</tr>";
	}
	echo  "<tr>";
	echo  '<td><i>'. $configurator_array['ordertotal'][$i]['title'].'</i></td>';
	echo '<td>&nbsp;</td>';
	echo  '<td>';
	echo xtc_draw_checkbox_field('CONSTANT_'.$configurator_array['ordertotal'][$i]['key'], 1, (in_array($configurator_array['ordertotal'][$i]['key'], $arr_data)));
  echo xtc_draw_hidden_field('SOURCE_'.$configurator_array['ordertotal'][$i]['key'], 'ordertotal_'.$configurator_array['ordertotal'][$i]['class']);
	echo '</td>';
	echo  "</tr>";
	if($configurator_array['ordertotal'][$i+1]['name'] != $configurator_array['ordertotal'][$i]['name']){
    $mark = '';
		if($configurator_array['ordertotal'][$i+1]['error']>0)
    	$mark = '*';
		echo  "<tr>";
		echo  '<td colspan="3"><b>'. $configurator_array['ordertotal'][$i+1]['name'].$mark.'</b></td>';
		echo  "</tr>";
	}
}
?>





    </table>
		</td>
































  </tr>

       <tr>

        <td colspan="3" align="center"><br><?php echo '<input style="width:80px" type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>';?></td>
   </tr>

</table>
 </td>
  </tr>
</table>












<!-- body_eof //-->
</form>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof //-->
</body>
</html>

<?php require(DIR_WS_INCLUDES . 'application_bottom.php');
?>