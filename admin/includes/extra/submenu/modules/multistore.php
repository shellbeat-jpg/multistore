<?php  
  # triggered in: 
  # \admin\modules.php - \includes\modules\categories\multistore4descriptions.php
  if (isset($_GET['action']) && xtc_not_null($_GET['action']) && $_GET['action'] == 'save' && isset($_POST['configuration'])) {
    
    reset($_POST['configuration']);               
      
    if(isset($_POST['configuration']['MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS'])){
      if(constant('MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS') && MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS != $_POST['configuration']['MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS']) {
        if(MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS=='true' && countMdItems(false) > 0){
  				# Zurücksetzen der Mehrfachbeschreibungen
          minMdItems();  
  			}elseif(MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS=='false' && countMdItems() > 0){
  			  # Setzen der Mehrfachbeschreibungen
          maxMdItems();
  			}   
      }    
    } elseif(isset($_POST['configuration']['MODULE_CATEGORIES_MULTISTORE2CATEGORIES_STATUS'])){
      if(defined("MULTISTORE") &&  MULTISTORE=='true' && $_POST['configuration']['MODULE_CATEGORIES_MULTISTORE2CATEGORIES_STATUS']!='true')
        $_POST['configuration']['MODULE_CATEGORIES_MULTISTORE2CATEGORIES_STATUS']='true';    
    }
  }

?>