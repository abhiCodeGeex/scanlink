<?php

/**
 * Description of code_purchase 
 * 
 * @author Megha shah
 */
class Model_codepurchase extends Model {
  /*   * ** Insert into code purchase , profile and code_purchase_detail 
   * **** @param client_id integer
   * **** @param data array
   * **** @param user_id integer
   * ** */

  
  public function get_purchase_detail_by_profile_id($profile_id) {
    $data = DB::select('code_purchase_id')
            ->from('code_purchase_detail')
            ->where('profile_id', '=', $profile_id)
            ->execute()
            ->as_array();

    if ($data) {
      return $data[0];
    }
  }
  
  public function get_code__by_profile_id($profile_id) {
    $data = DB::select('code_purchase_id')
            ->from('code_purchase_detail')
            ->where('profile_id', '=', $profile_id)
            ->execute()
            ->as_array();

    if ($data) {
      return $data[0];
    }
  }
  
  public function get_per_code_amount_by_id($code_purchase_id) {
    $data = DB::select('per_code_amount')
            ->from('code_purchase')
            ->where('id', '=', $code_purchase_id)
            ->execute()
            ->as_array();

    if ($data) {
      return $data[0];
    }
  }

  public function insertcodepurchase($client_id, $user_id, $data,$free_code=0) {
	  
	  if(!isset($data['is_reseller_pricing_code'])){
		  $data['is_reseller_pricing_code'] = '0';
	  }
	  if(!isset($data['reseller_client_id'])){
		  $data['reseller_client_id'] = '';
	  }

	//echo "<pre>";print_r($data);echo "</pre>";
	
	$code_purchase_id = DB::insert(
                'code_purchase', array(
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
	
    $type_id = DB::select('id')
            ->from('equipment_types')
            ->where('slag', '=', 'code')
            ->execute()
            ->as_array();

    $typeid = $type_id[0]['id'];
    $code_purchase_id = $code_purchase_id[0];
    for ($i = 0; $i < $data['no_of_codes']; $i++) {
      $profile_id = DB::insert(
                      'profiles', array(
                  'client_id',
                  'user_id',
                  'code_purchase_id',
                  'type_id',
                  'created_at',
                  'updated_at',
                  'expired_at',
                  'payment_status',
                  'update_or_not',
				  'is_reseller_code',
				  'reseller_client_id',
				  'free_code'
                      )
              )
              ->values(
                      array(
                          $client_id,
                          $user_id,
                          $code_purchase_id,
                          $typeid,
                          date("Y-m-d H:i:s"),
                          date("Y-m-d H:i:s"),
                          self::add_date(date("Y-m-d H:i:s"), 365),
                          '0',
                          '0',
						  $data['is_reseller_pricing_code'],
						  $data['reseller_client_id'],
						  $free_code
                      )
              )
              ->execute();
      $code_purchase_detail = DB::insert(
                      'code_purchase_detail', array(
                  'code_purchase_id',
                  'profile_id',
                  'created_at'
                      )
              )
              ->values(
                      array(
                          $code_purchase_id,
                          $profile_id[0],
                          date("Y-m-d H:i:s"),
                      )
              )
              ->execute();
    }
  }
  
  /**** Renew into code purchase , profile and code_purchase_detail 
  ***** @param client_id integer
  ***** @param data array
  ***** @param user_id integer
  ****/

  public function renewcodepurchase($client_id, $user_id, $data) {

	

	$email_reseller_array = array();
	$email_own_array = 0;
	if(!isset($data['is_reseller_pricing_code'])){
		  $data['is_reseller_pricing_code'] = '0';
	}
	
	if(is_array($data['per_code_amount']))
	{
		if(count($data['per_code_amount'])==1 )
		{
			$per_code_amount = $data['per_code_amount'][0];
		}
		else
		{
			$temp = array_unique($data['per_code_amount']);
			
			if(count($temp)==1)
				$per_code_amount = $temp[0];
			else
				$per_code_amount = "0";
		}
	}
	else
		$per_code_amount = $data['per_code_amount'];
	
	
	
    $code_purchase_id = DB::insert(
                 'code_purchase', array(
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
				'is_reseller_pricing_code',
				'total_amount'
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
                        $per_code_amount,
                        date("Y-m-d H:i:s"),
                        '1',
                        '0',
                        self::add_date(date("Y-m-d H:i:s"), 365),
						$data['is_reseller_pricing_code'],
						$data['total_amount']
                    )
            )
            ->execute();
	
   /* $type_id = DB::select('id')
            ->from('equipment_types')
            ->where('slag', '=', 'code')
            ->execute()
            ->as_array();

    $typeid = $type_id[0]['id'];
	*/
    $code_purchase_id = $code_purchase_id[0];
	
	$sametype = true;
	$previous_type = '';
	
    for ($i = 0; $i < $data['no_of_codes']; $i++) 
	{
       $query = DB::select()
            ->select('expired_at','type_id','is_reseller_code','reseller_client_id')
            ->from('profiles')
            ->where('id', '=', $data['profile_id'][$i])
			;
	  $profile_info= $query->execute()
                    ->as_array();
	  
	 
	  //email send data
	  if($profile_info[0]['is_reseller_code'] == '1'){
	  	if(!empty($email_reseller_array)){
			if(array_key_exists($profile_info[0]['reseller_client_id'],$email_reseller_array)){
				$email_reseller_array[$profile_info[0]['reseller_client_id']] = $email_reseller_array[$profile_info[0]['reseller_client_id']] + 1;
			}else{
				$email_reseller_array[$profile_info[0]['reseller_client_id']] = 1;
			}
		}else{
			$email_reseller_array[$profile_info[0]['reseller_client_id']] = 1;
		}
	  }else{
	  	$email_own_array = $email_own_array + 1;
	  }
	  			
	  
	  if($sametype && $i != 0 ){
		  if($profile_info[0]['type_id'] != $previous_type){
			  $sametype = false;
		  }
	  }else if($i == 0 ){
		  $previous_type = $profile_info[0]['type_id'];
	  }
	  
	  $expriy_data = $profile_info[0]['expired_at'];
	  if(strtotime($expriy_data) > strtotime(date("Y-m-d H:i:s"))){
		  $renew_expriy_date = self::add_date($expriy_data, 365);
	  }else{
		  $renew_expriy_date = self::add_date(date("Y-m-d H:i:s"), 365);
	  }
	 
					
	  $dataFill = array(
						"expired_at" => $renew_expriy_date,
						"updated_at"=>date("Y-m-d H:i:s")
					);
	  $query = DB::update('profiles')->set($dataFill)->where('id', '=', $data['profile_id'][$i]);
	  $query->execute();
		
      $code_purchase_detail = DB::insert(
                      'code_purchase_detail', array(
                  'code_purchase_id',
                  'profile_id',
				  'amount',
                  'created_at'
                      )
              )
              ->values(
                      array(
                          $code_purchase_id,
                          $data['profile_id'][$i],
						  $data['per_code_amount'][$i],
                          date("Y-m-d H:i:s"),
                      )
              )
              ->execute();
			  
			  
		
    }
	
	$return_array = array();
	$return_array['type_id'] = $previous_type;
	$return_array['same_type'] = $sametype;
	$return_array['email_reseller_array'] = $email_reseller_array;
	$return_array['email_own_array'] = $email_own_array;
	
	return $return_array;
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