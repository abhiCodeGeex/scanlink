<?php defined('SYSPATH') or die('No direct script access.');
abstract class Controller_Template_Mobile extends Controller_Template
{
	// Define the template to use
	public $template = 'mobile/template/template';
	public $session;
	
	public function before()
	{
		parent::before();
		
		$this->session = Session::instance();
		// Page Title
		$this->template->title = '';
		
		// Meta Tags
		$this->template->meta_tags = array();	
		
		// Stylesheets
		$this->template->stylesheets = array();
		
		// Javascripts
		$this->template->javascripts = array();
		
		// header
		$this->template->headers = '';	
		
		
		// No content by default
		$this->template->content = '';		

		// footer
		$this->template->footer = '';
	}
}
?>