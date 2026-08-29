<?php
  # D.L.
  if(isset($listing_split) && isset($listing_split->sql_query) && $category_depth=='products' && (basename($PHP_SELF) == FILENAME_DEFAULT && $subcat_list != '')) {
        $_SESSION['sql_ms_through']= true; 
  }
?>