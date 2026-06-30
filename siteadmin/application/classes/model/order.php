<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Order extends Model{
   	/**
	 * select all user
	 */  
	 public function getAllOrders($client_id=NULL) {
		if($client_id!==NULL){
			$orders = DB::select("*")
			->from('orders')
			->where('client_id','=',$client_id)
			->order_by('ordered_on', 'DESC')
			->execute()
			->as_array();
			return $orders;
		}else{
			$orders = DB::select("*")
			->from('orders')
			->order_by('ordered_on', 'DESC')
			->execute()
			->as_array();
			return $orders;
		}
     }
	 
	 
	public function getOrdersByStatus($status, $client_id=NULL){
		if($client_id!==NULL){
			$orders = DB::select('*')->from('orders')
		  		->where('status','=',$status)	
				->and_where('client_id','=',$client_id)
				->order_by('ordered_on', 'DESC')
				->execute()
				->as_array();
			return $orders;
		}else{
			$orders = DB::select('*')->from('orders')
		  		->where('status','=',$status)
				->order_by('ordered_on', 'DESC')				
				->execute()
				->as_array();
			return $orders;
		}
		
	}
	 /**
	 * select single user
	 */  
	public function updateOrder($id,$data){
		return DB::update('orders')->set($data)->where('id', '=', $id)->execute();		    
	}
	 
	 public function getOrder($id) {
        $order = DB::select()
          ->from('orders')
		  ->where('id','=',$id)	
		  ->execute()
		  ->as_array();
		return $order[0];
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
	 public function editOrder($id,$user) {		
		return DB::update('orders')->set($user)->where('id', '=', $id)->execute();		    
     }
	 /**
	 * edit user
	 */	
}
?>