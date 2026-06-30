<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Gallery extends Controller_Template_Master {
	
	public function before() {
		
		parent::before();
		
		$session = Session::instance();	
		if(!$session->get('adminuserid')):
			$this->request->redirect('welcome/index');
		endif;
		
		// Default Head view file
		$this->template->head = View::factory('template/head');
		
		// Set the default Page Title
		$this->template->head->title = 'Welcome to ScanLink';
		
		// All CSS Stylesheets
		$this->template->head->stylesheets = array(array('href'=>'styles/site.css','media'=>'screen'), array('href'=>'styles/dropdown.css','media'=>'screen'), array('href'=>'styles/default.css','media'=>'screen'), array('href'=>'styles/colorbox.css','media'=>'screen'), array('href'=>'styles/jquery-ui.css','media'=>'screen'), array('href'=>'styles/style.css','media'=>'screen'));
		
		// All JS Libraries and Scripts
		$this->template->head->javascripts = array('js/jquery.min.js','js/jquery.colorbox.js','js/jquery-ui.min.js','js/functions.js', "js/paginator.js");
		
		// Default Header view file
		$this->template->headers = View::factory('template/headers');
		$this->template->headers->logoutlnk = View::factory('template/logoutlnk');
		
		// Default Header view file
		$this->template->menu = View::factory('template/menu');
		
		$this->template->menu->HeadLinksArray = $this->menu;
		
		
		$this->template->footer = '';
		
		if($this->template->footer):
		// All JS Libraries and Scripts
		$this->template->footer->scripts = array();
		endif;
		
	}

	public function action_index()
	{
		$imageList = Model::factory('gallery')->getAllImages();
		// Default Content template
		
		$this->template->content = View::factory('gallery/index')
		->bind('images',$imageList);
		
	}
	public function action_delete()
	{
		$id = $this->request->param('id');
		if(isset($id) && $id):
			Model::factory('gallery')->deleteImage($id);
		endif;
		$this->request->redirect('gallery/index');
	}
	public function action_blocked()
	{
		$id = $this->request->param('id');
		if(isset($id) && $id):
			Model::factory('gallery')->blockImage($id);
		endif;
		$this->request->redirect('gallery/index');
	}
	public function action_edit()
	{
		$id = $this->request->param('id');
		$image = Model::factory('gallery')->getImageById($id);
		// Default Content template
		$this->template->content = View::factory('gallery/edit')
		->bind('post',$image);
	}
	public function action_add()
	{
		if(isset($_POST['btnAddGallery']) && $_POST['btnAddGallery']!=''){
			if(isset($_FILES['gallery']) && !empty($_FILES['gallery']))
            {
				$directory = DOCROOT.'images/galleryApi/fullscreen/';
				foreach( $_FILES['gallery']['name'] as $k=>$fsd ){
					if(strstr($_FILES['gallery']['type'][$k],'image')){
						//$ext = pathinfo($fsd, PATHINFO_EXTENSION);
						//$fname = pathinfo($fsd,PATHINFO_FILENAME);
						$fname = time()."_".str_replace(" ","_",$fsd);
						///$lastId = Model::factory('gallery')->getLastId();
						$fnameThumb = "thumb_".$fname;//."_".($lastId + 1).".".$ext;
						//$fnameThumb = $fname."_".($lastId + 1)."_thumb.".$ext;
						Model::factory('gallery')->saveImages($fname);
						move_uploaded_file($_FILES['gallery']['tmp_name'][$k],$directory.$fname);
						Image::factory($directory.$fname)
						->resize(200, 200, Image::WIDTH)
						->save($directory.$fnameThumb,100);
					}
				}
				$this->request->redirect('gallery/');
				exit;						
            }
		}
		$this->template->content = View::factory('gallery/add');
	}
	protected function _save_image($image)
    {
        if (
            ! Upload::valid($image) OR
            ! Upload::not_empty($image) OR
            ! Upload::type($image, array('jpg', 'jpeg', 'png', 'gif')))
        {
            return FALSE;
        }
 		
        $directory = DOCROOT.'images/galleryApi/fullscreen/';
 
        if ($file = Upload::save($image, NULL, $directory))
        {
            $filename = strtolower(Text::random('alnum', 20)).'.jpg';
 
            Image::factory($file)
                ->resize(200, 200, Image::AUTO)
                ->save($directory.$filename);
 
            // Delete the temporary file
            //unlink($file);
 
            return $filename;
        }
 
        return FALSE;
    }
} // End Welcome
