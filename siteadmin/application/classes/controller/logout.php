<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Logout extends Controller_Template_Master {	

	public function action_index()
	{
		$session = Session::instance();	
		$session->delete('adminuserid');
		$session->delete('adminuser');
		session_unset();
		$this->request->redirect('welcome/index');
	}

} // End Welcome
