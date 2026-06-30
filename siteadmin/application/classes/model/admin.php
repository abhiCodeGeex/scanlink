<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Admin extends Model{
	/*
	   * Load the news for the first page
	   */
	  public function loadAdmin($post){
		return DB::select('id','username', 'password')
		  ->from('admin')
		  ->where('username','=',$post['username'])
		  ->and_where('password','=',$post['password'])
		  ->execute()
		  ->as_array();
	  }
	  public function getpass($id,$pass){
		return DB::select('id','password')
		  ->from('admin')
		  ->where('id','=',$id)
		  ->and_where('password','=',$pass)
		  ->execute()
		  ->as_array();
	  }
	  public function editpass($id,$pass){		
		  DB::update('admin')->set($pass)->where('id', '=', $id)->execute();
	  }

}

?>
