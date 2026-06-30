<?php defined('SYSPATH') or die('No direct script access.');

/*

mc_gross=>13.00
protection_eligibility=>Eligible
address_status=>confirmed
payer_id=>HEBL4Z9LD69GN
tax=>0.00
address_street=>1 Main St
payment_date=>03:35:23 Apr 16, 2012 PDT
payment_status=>Pending
charset=>windows-1252
address_zip=>95131
first_name=>bhagirath
address_country_code=>US
address_name=>bhagirath chauhan
notify_version=>3.4
custom=>1|1
payer_status=>verified
business=>bhagir_1326696375_biz@gmail.com
address_country=>United States
address_city=>San Jose
quantity=>1
verify_sign=>AgCzAkHwOmr6hNyitYdnStd8Md14A7-7SlU5sAnKNMfGkvug.UKq3l45
payer_email=>bhagir_1326696532_per@gmail.com
txn_id=>3T2709479S896434B
payment_type=>instant
last_name=>chauhan
address_state=>CA
receiver_email=>bhagir_1326696375_biz@gmail.com
receiver_id=>RB26V4YQS3HK2
pending_reason=>multi_currency
txn_type=>web_accept
item_name=>ScanLink QR Label
mc_currency=>AUD
item_number=>1
residence_country=>US
test_ipn=>1
handling_amount=>0.00
transaction_subject=>1|1
payment_gross=>
shipping=>0.00
ipn_track_id=>656e9d4ba4a8d

*/

class Controller_Notify extends Controller {
	public function action_index(){
		$str="";
		foreach($_REQUEST as $key=>$value){
			$str.=$key."=>".$value."\r\n";
		}
		
		if($_REQUEST['payment_status']=="Pending" && isset($_REQUEST['txn_id']) && $_REQUEST['txn_id']!=""){

			$data=array();

			$data['transaction_id']=$_REQUEST['txn_id'];
			$data['status']="Paid";
			
			Model::factory("order")->setTransactionId($data,$_REQUEST["custom"]);
			/*if($order_id){
				mail("bhagirath.webplanex@gmail.com","paypal sub",$str." ok");
			}else{
				mail("bhagirath.webplanex@gmail.com","paypal sub",$str."  not added ok");
			}*/
		}
	}
	
	public function action_tempOrder(){
				
			$data=array();
			
			$profile=Model::factory('profiles')->getProfileById($_REQUEST['item_number']);
//			Model::factory('profiles')->getClientById();
			$custom=explode("|",$_REQUEST["custom"]);

			$data["client_id"]=$profile["client_id"];
			$data['qty_small']=$custom[0];
			$data['qty_large']=$custom[1];
			$data['user_id']=$custom[2];
			$data['profile_id']=$_REQUEST['item_number'];
			$data['price_small']=3;
			$data['price_large']=5;
			$data['status']="New";
			
			$data['first_name']=$_REQUEST['first_name'];
			$data['last_name']=$_REQUEST['last_name'];
			$data['address1']=$_REQUEST['address1'];
			$data['address2']=$_REQUEST['address2'];
			$data['city']=$_REQUEST['city'];
			$data['state']=$_REQUEST['state'];
			$data['zip']=$_REQUEST['zip'];
			$data['country']=$_REQUEST['country'];
			$data['email']=$_REQUEST['email'];
			$data['contact']=$_REQUEST['contact'];
			
			
			$order_id =	Model::factory("order")->placeTempOrder($data);
		
			if($order_id){
				echo $order_id;
			}		
	}
}
?>