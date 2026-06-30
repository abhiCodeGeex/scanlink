<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Codeorder extends Model {

  /**
   * select all user
   */
  public function getAllOrders($client_id = NULL) {
   
   // to filter out free code order
   /*$profile_id_arr = DB::select("code_purchase_id")
              ->from('profiles')
              ->where('free_code', '=', '0')
              ->execute()
              ->as_array();
			  */
  
   
    if ($client_id !== NULL) {
      $orders = DB::select("*")
              ->from('code_purchase')
              ->where('client_id', '=', $client_id)
              ->order_by('ordered_on', 'DESC')
              ->execute()
              ->as_array();
      return $orders;
    } else {
      $orders = DB::select("*")
              ->from('code_purchase')
			  //->where('id','in',$profile_id_arr)
			  ->order_by('created_at', 'DESC')
              ->execute()
              ->as_array();
      return $orders;
    }
  }

  public function getOrdersByStatus($status, $client_id = NULL) {
    if ($client_id !== NULL) {
      $orders = DB::select('*')->from('code_purchase')
              ->where('status', '=', $status)
              ->and_where('client_id', '=', $client_id)
              ->order_by('created_at', 'DESC')
              ->execute()
              ->as_array();
      return $orders;
    } else {
      $orders = DB::select('*')->from('code_purchase')
              ->where('status', '=', $status)
              ->order_by('created_at', 'DESC')
              ->execute()
              ->as_array();
      return $orders;
    }
  }

  /**
   * select single user
   */
  public function updateOrder($id, $data) {
    return DB::update('code_purchase')->set($data)->where('id', '=', $id)->execute();
  }

  public function getOrder($id) {
    $order = DB::select()
            ->from('code_purchase')
            ->where('id', '=', $id)
            ->execute()
            ->as_array();
    return $order[0];
  }
  
  public function getOrderDetails($id) {
    $order_details = DB::select()
            ->from('code_purchase_detail')
            ->where('code_purchase_id', '=', $id)
            ->execute()
            ->as_array();
    return $order_details;
  }

  /**
   * Delete user
   */
  public function deleteOrder($id) {
    DB::delete('orders')->where('id', '=', $id)->execute();
  }

  /**
   * edit user
   */
  public function editOrder($id, $user) {
    return DB::update('orders')->set($user)->where('id', '=', $id)->execute();
  }

  /**
   * add free code
   */
   
 public function add_free_code($client_id, $user_id, $data,$free_code=1) {
	  
	 $data['is_reseller_pricing_code'] = '0';
	 $data['reseller_client_id'] = '';
	
  
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
    
    for ($i = 0; $i < $data['no_of_codes']; $i++) 
    {
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
				  'free_code',
				  'renewal_required'
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
								  $free_code,
								  '0'
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