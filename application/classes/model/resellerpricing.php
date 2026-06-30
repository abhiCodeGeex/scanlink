<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Resellerpricing extends Model {

  public function getAllResellerCode() {
    // Select All code prising
    return DB::select()
                    ->from('reseller_pricing')
                    ->execute()
                    ->as_array();
  }
  
  

 
}

?>