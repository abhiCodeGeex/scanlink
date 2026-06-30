<?php

/**
 * Description of form builder order 
 * 
 * @author 
 */
class Model_formbuilderorders extends Model {
  /*   * ** Insert into form_builder_orders , form_builder_order_detail and udpate profiles->form_active
   * **** @param client_id integer
   * **** @param data array
   * **** @param user_id integer
   * **** @param form_builder_profile_id integer
   * ** */

  public function insert_form_builder_orders($client_id, $user_id, $data,$form_builder_profile_id)
  {
	  
	  if(!isset($data['is_reseller_pricing_code'])){
		  $data['is_reseller_pricing_code'] = '0';
	  }
	  if(!isset($data['reseller_client_id'])){
		  $data['reseller_client_id'] = '';
	  }

	//echo "<pre>";print_r($data);echo "</pre>";
	
	$order_id = DB::insert(
                'form_builder_orders', array(
                'client_id',
				'email',
				'town',
                'first_name',
                'last_name',
                'company_name',
                'billing_address',
                'phone',
                'postal_code',
                'no_of_codes',
                'per_code_amount',
                'created_at',
                'status',
                'enable',
                'exipry_date',
				'is_reseller_pricing_code'
                    )
            )
            ->values(
                    array(
                        $client_id,
						$data['email'],
						$data['town'],
						$data['first_name'],
                        $data['last_name'],
                        $data['company_name'],
                        $data['billing_address'],
                        $data['phone'],
                        $data['postal_code'],
                        $data['no_of_codes'],
                        $data['per_code_amount'],
                        date("Y-m-d H:i:s"),
                        '0',
                        '0',
                        self::add_date(date("Y-m-d H:i:s"), 365),
						$data['is_reseller_pricing_code'] 
                    )
            )
            ->execute();
	
    $form_builder_order_id = $order_id[0];
    
	// update form builder for this profile
	$dataFill = array(
						"form_active" => 1,
						"pop_up_formbuilder" =>1,
						"form_is_enable" =>1
					);
	$query = DB::update('profiles')->set($dataFill)->where('id', '=', $form_builder_profile_id);
	$query->execute();
     
    $code_purchase_detail = DB::insert(
                      'form_builder_order_detail', array(
                  'form_builder_order_id',
                  'profile_id',
                  'created_at'
                      )
              )
              ->values(
                      array(
                          $form_builder_order_id,
                          $form_builder_profile_id,
                          date("Y-m-d H:i:s"),
                      )
              )
              ->execute();
   
   	
	return $form_builder_order_id;
	
    }
	

  
    
  /**** 
  ***** add date 
  ****/
  protected function add_date($givendate, $day = 0, $mth = 0, $yr = 0) {
    $cd = strtotime($givendate);
    $newdate = date('Y-m-d H:i:s', mktime(date('H', $cd), date('i', $cd), date('s', $cd), date('m', $cd) + $mth, date('d', $cd) + $day, date('Y', $cd) + $yr));
    return $newdate;
  }

}

?>