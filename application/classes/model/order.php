<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Order extends Model{
	public function placeOrder($data){		

		$order_id = DB::insert('orders', array('user_id', 'client_id', 'profile_id', 'qty_small', 'qty_large', 'price_small', 'price_large', 'status', 'transaction_id'))
        	->values(array($data["user_id"], $data["client_id"], $data["profile_id"], $data["qty_small"], $data["qty_large"], $data["price_small"], $data["price_large"], $data["status"], $data["transaction_id"]))
			->execute();
		return $order_id[0];
	}
	
	public function setTransactionId($data,$id){
		DB::update('orders')->set(array("transaction_id"=>$data["transaction_id"], "status"=>$data["status"]))->where('id', '=', $id)->execute();
	}
	
	public function getOrders($client=null){
		
		$orders=DB::select("*")->from("orders");
		if($client!=null){
			$orders=$orders->where('client_id',"=",$client);
		}
		
		$orders=$orders->execute()->as_array();
		
		if($orders){
			return $orders;
		}else{
			return array();
		}
	}
	
	
	public function placeTempOrder($data){		

		$order_id = DB::insert('orders', array('user_id', 'client_id', 'profile_id', 'qty_small', 'qty_large', 'price_small', 'price_large', 'status', 'first_name', 'last_name', 'address1', 'address2', 'city' ,'state', 'zip', 'country', 'email', 'contact'))
        	->values(array($data["user_id"], $data["client_id"], $data["profile_id"], $data["qty_small"], $data["qty_large"], $data["price_small"], $data["price_large"], $data["status"], $data["first_name"], $data["last_name"], $data["address1"], $data["address2"], $data["city"], $data["state"], $data["zip"], $data["country"], $data["email"], $data["contact"]))->execute();
		return $order_id[0];
	}
	
}
?>