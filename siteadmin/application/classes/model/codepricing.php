<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Codepricing extends Model {

  public function getallprising() {
    // Select All code prising
    return DB::select()
                    ->from('code_prising')
                    ->execute()
                    ->as_array();
  }

  public function editallprising($data) {

    foreach ($data as $key => $val) {
      DB::update('code_prising')->set(array('amount' => $val))->where('id', '=', $key)->execute();
    }
  }
  
  public function editallresellerprising($data) {

    foreach ($data as $key => $val) {
      DB::update('code_prising')->set(array('reseller_amount' => $val))->where('id', '=', $key)->execute();
    }
  }
  
}

?>