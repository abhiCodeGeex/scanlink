<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Marketing extends Controller_Template_Master {
    public $clientlogin = false;
    public function before() {

        parent::before();

        if ($this->session->get('clientuserid')):
            $this->clientlogin = true;
        endif;

        // Default Head view file
        $this->template->head = View::factory('template/head');

        // Set the default Page Title
        $this->template->head->title = 'Marketing';

        // All CSS Stylesheets
        $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'), array('href' => 'styles/prettyPhoto.css', 'media' => 'screen'));

        // All JS Libraries and Scripts
        $this->template->head->javascripts = array('js/jquery.min.js', 'js/coin-slider.min.js', 'js/jquery.validate.min.js', 'js/jquery.prettyPhoto.js', 'js/jquery.colorbox.js', 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js');
        $this->template->head->extrascripts = 'slider';

        // Default Header view file
        $this->template->headers = View::factory('template/headers');

        // Default menu
        $this->template->headers->menu = View::factory('template/menu');
        
        if($this->voc_user_logged_in==1)
        {
            $this->template->headers->menu = View::factory('template/vocmenu');
        }
                
        if ($this->session->get('clientuserid') || $this->voc_user_logged_in==1):
            $this->template->headers->menu->logoutlnk = $this->session->get('clientusername');
        endif;
        
        
        $this->template->headers->menu->menu = $this->menu;
        
        $this->template->headers->menu->voc_user_logged_in = $this->voc_user_logged_in;
        
        /* WP_MS START  */
        $this->template->headers->menu->menuSubmenu = $this->menuSubmenu;
        /* WP_MS END  */
        $this->template->headers->menu->currentactive = $this->request->controller() . "/" . $this->request->action();
        // Default Content template
        $this->template->footer = View::factory('template/footer');

        if ($this->template->footer):
            // All JS Libraries and Scripts
            $this->template->footer->scripts = array();
        endif;
    }
    public function action_index() {
        // Default Content template
        $this->template->currentPage = 'marketing';
        $this->template->content = View::factory('marketing/index')
                ->bind("client_login", $this->clientlogin);
    }
    public function action_foryou() {
        // Default Content template
       // $this->template->currentPage = 'marketing';
        $this->template->content = View::factory('marketing/foryou')
                ->bind("client_login", $this->clientlogin);
    }
}

// End Welcome
