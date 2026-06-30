<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_User extends Model{
   	/**
	 * select all user
	 */  
	 public function getalluser($id) {
        return DB::select()
          ->from('users')
		  ->where('client_id','=',$id)	
		  ->and_where('role','=',1)			  		  
		  ->execute()
		  ->as_array();
     }
	 /**
	 * select all user
	 */  
	 public function getAllClientsFromUsers() {
        return DB::select('id','email')
          ->from('users')
		  ->where('status','=','1')
		  ->and_where('role','=',5)
		  ->execute()
		  ->as_array();
     }	
	 /**
	 * select single user
	 */  
	 public function getuser($id) {
        return DB::select()
          ->from('users')
		  ->where('id','=',$id)	
		  ->and_where('role','=',1)			  		  
		  ->execute()
		  ->as_array();
     }	
	 /**
	 * Delete user
	 */  
	 public function deleteuser($id) {        
		DB::delete('users')->where('id', '=', $id)->execute();        
     }	
	 /**
	 * Block user
	 */  
	 public function blockuser($id) {
		$cstatus = DB::select('status')
          ->from('users')
		  ->where('id','=',$id)		  
		  ->execute()
		  ->as_array();
		if($cstatus[0]['status'] == 1):
			$sflag = '0';
		else:
			$sflag = '1';
		endif;
		DB::update('users')->set(array('status' => $sflag))->where('id', '=', $id)->execute();		    
     }	
	 /**
	 * edit user
	 */  
	 public function edituser($id,$user) {		
		return DB::update('users')->set($user)->where('id', '=', $id)->execute();		    
     }
	 /**
	 * edit user
	 */	
	public function getdupemail($id,$email) {
	// Select id for check email and url duplication
	
	return DB::select('id')
	  ->from('users')
	  ->where('email','=',$email)
	  ->and_where('id','!=',$id)
	  ->execute()
	  ->as_array();
	
	}
	public function videopermission($id) {
        // block client
		$cstatus = DB::select('video_upload')
          ->from('users')
		  ->where('id','=',$id)		  
		  ->execute()
		  ->as_array();
		if($cstatus[0]['video_upload'] == 1):
			$sflag = '0';
		else:
			$sflag = '1';
		endif;
		DB::update('users')->set(array('video_upload' => $sflag))->where('id', '=', $id)->execute();
		//DB::update('users')->set(array('status' => $sflag))->where('client_id', '=', $id)->execute();		    
     }
	 
	 public function getUserEmailById($id) {
	// Select id for check email and url duplication
	
	return DB::select('email')
	  ->from('users')
	 ->where('id','=',$id)
	  ->execute()
	  ->as_array();
	
	}
	public function getUserEmailByClientId($id){
		return DB::select('email')
	  ->from('users')
	 ->where('client_id','=',$id)
	 ->and_where('role','=',5)
	  ->execute()
	  ->as_array();
	}
}
?>