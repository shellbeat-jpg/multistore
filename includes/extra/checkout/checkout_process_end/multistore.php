<?php

	# MODULE MULTISTORE
	if(defined("MULTISTORE") &&  MULTISTORE=='true' && isset($insert_id)){
        $sql_data_array = array();
		$sql_data_array['id_domain'] = ID_DOMAIN;
		$sql_data_array['store_name'] = xtc_get_store_name();
		$sql_data_array['id_languages'] = $_SESSION['languages_id'];
        # separate Bestellnummern pro Shop   
        $sql_data_array['orders_id_shop'] = ms_get_order_id_next();
        xtc_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = "'.$insert_id.'"');
	}
    
?>