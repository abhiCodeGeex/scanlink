<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_formbuilderorder extends Model {

  /**
   * select all user
   */
  public function getAllOrders($client_id = NULL) 
  {
   
     
    if ($client_id !== NULL) {
      $orders = DB::select("*")
              ->from('form_builder_orders')
			  ->join('form_builder_order_detail')
			  ->on('form_builder_order_id', '=', 'form_builder_orders.id')
              ->where('client_id', '=', $client_id)
              ->order_by('form_builder_orders.created_at', 'DESC')
              ->execute()
              ->as_array();
      return $orders;
    } else {
      $orders = DB::select("*")
              ->from('form_builder_orders')
			  ->join('form_builder_order_detail')
			  ->on('form_builder_order_id', '=', 'form_builder_orders.id')
			  ->order_by('form_builder_orders.created_at', 'DESC')
              ->execute()
              ->as_array();
      return $orders;
    }
  }

  public function getOrdersByStatus($status, $client_id = NULL) {
    if ($client_id !== NULL) {
      $orders = DB::select('*')
	  		  ->from('form_builder_orders')
	  		  ->join('form_builder_order_detail')
			  ->on('form_builder_order_id', '=', 'form_builder_orders.id')
              ->where('status', '=', $status)
              ->and_where('client_id', '=', $client_id)
              ->order_by('form_builder_orders.created_at', 'DESC')
              ->execute()
              ->as_array();
      return $orders;
    } else {
      $orders = DB::select('*')
	  		  ->from('form_builder_orders')
	  		  ->join('form_builder_order_detail')
			  ->on('form_builder_order_id', '=', 'form_builder_orders.id')
              ->where('status', '=', $status)
              ->order_by('form_builder_orders.created_at', 'DESC')
              ->execute()
              ->as_array();
      return $orders;
    }
  }

  /**
   * select single user
   */
  public function updateOrder($id, $data) {
    return DB::update('form_builder_orders')->set($data)->where('id', '=', $id)->execute();
  }

  public function getOrder($id) {
    $order = DB::select()
            ->from('form_builder_orders')
            ->where('id', '=', $id)
            ->execute()
            ->as_array();
    return $order[0];
  }

  
  

  
}

?>