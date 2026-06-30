<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Settings extends Model{
	/*
	   * Insert Client
	   */
	  /*public function addclient($client) {
        // Create a new client record in the database
        $insert_id = DB::insert('clients', array_keys($client))
            ->values($client)
            ->execute();
		DB::insert('users', array('client_id','email','password','role','created_at'))
            ->values(array($insert_id[0],$client['email'],$client['password'],5,$client['created_at']))
            ->execute();
        return $insert_id;
     }
     public function adduser($user) {
        // Create a new user record in the database
        $insert_id = DB::insert('users', array_keys($user))
            ->values($user)
            ->execute();		
        return $insert_id;
     }
	 public function getdupurl($client) {
        // Select id for check email and url duplication   
	   $result = DB::select('id')
          ->from('clients')
		  ->where('url','=',$client['url']);
		  if(isset($client['mode']) && $client['mode'] == 'edit'){
			  $result->and_where('id','!=',$client['id']);
		  }
		  return $result->execute()
		  ->as_array();
		
     }	
	 public function getdupemail($client) {
        // Select id for check email and url duplication
		
	   $result = DB::select('id')
          ->from('users')
		  ->where('email','=',$client['email']);
		  if(isset($client['mode']) && $client['mode'] == 'edit'){
			  $subselect = DB::select('id')
          ->from('users')
		  ->where('client_id','=',$client['id'])
		  ->and_where('role','=',5);
			  $result->and_where('id','!=',$subselect);
		  }
		  return $result->execute()
		  ->as_array();
		
     }	
	 public function getuseremail($email) {
        // Select id for check email duplication
        return DB::select('id')
          ->from('users')
		  ->where('email','=',$email)
		  ->execute()
		  ->as_array();
     }*/		
	 public function getallsettings() {
        // Select All client
        return DB::select()
          ->from('settings')
		  ->execute()
		  ->as_array();
     }
	 public function editallsettings($data){
		 
		 foreach($data as $key=>$val){
		 	DB::update('settings')->set(array('values' => $val))->where('title', '=', $key)->execute();
		 }
	 }
	 /*public function deleteclient($id) {
        // Delete client
		DB::delete('clients')->where('id', '=', $id)->execute();
		DB::delete('users')->where('client_id', '=', $id)->execute();        
     }	
	 public function blockclient($id) {
        // block client
		$cstatus = DB::select('approve')
          ->from('clients')
		  ->where('id','=',$id)		  
		  ->execute()
		  ->as_array();
		if($cstatus[0]['approve'] == 1):
			$sflag = '0';
		else:
			$sflag = '1';
		endif;
		DB::update('clients')->set(array('approve' => $sflag))->where('id', '=', $id)->execute();
		//DB::update('users')->set(array('status' => $sflag))->where('client_id', '=', $id)->execute();		    
     }	
	  public function getclientbyid($id) {
        // Select id for check email duplication
        return DB::select()
          ->from('clients')
		  ->where('id','=',$id)
		  ->execute()
		  ->as_array();
     }*/	
	 public function editclient($id,$client,$user){
		 DB::update('clients')->set($client)->where('id', '=', $id)->execute();
		DB::update('users')->set($user)->where('client_id', '=', $id)->and_where('role', '=', 5)->execute();
	 }
}
?>