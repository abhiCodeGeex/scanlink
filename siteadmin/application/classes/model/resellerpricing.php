<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Resellerpricing extends Model {

  public function getallpricing() {
    // Select All code prising
    return DB::select()
                    ->from('reseller_pricing')
                    ->execute()
                    ->as_array();
  }

  public function editallpricing($data) {

    foreach ($data as $key => $val) {
      DB::update('reseller_pricing')->set(array('amount' => $val))->where('id', '=', $key)->execute();
    }
  }
  
}

?>