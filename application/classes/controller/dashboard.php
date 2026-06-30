<?php
defined('SYSPATH') or die('No direct script access.');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/phpwkhtmltopdf/vendor/autoload.php';
use mikehaertl\wkhtmlto\Pdf;

class Controller_Dashboard extends Controller_Template_Master
{
    public $types = '';
    public $userid = '';
    public $small_price = 3;
    public $big_price = 5;
    public $postage = 0;
    public function before()
    {
        parent::before();
        if (!$this->session->get('clientuserid') && !$this->session->get('adminuserid') && !$this->session->get('voc_user_id')):
            $this->request->redirect(URL::base() . 'welcome/index');
        endif;
        // Default Head view file
        $this->template->head = View::factory('template/head');
        // Set the default Page Title
        $this->template->head->title = 'Welcome to ScanLink';
        // All CSS Stylesheets
        $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen') /* , array('href'=>'styles/default.css','media'=>'screen'), array('href'=>'styles/colorbox.css','media'=>'screen') */, array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'));
        $actionnm = $this->request->action();
        // All JS Libraries and Scripts
        if ($actionnm != 'changepwd'):
            if ($actionnm == "scanalytics") {
                $this->template->head->javascripts = array('js/jquery.min.js', 'js/dashboard/superfish.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/dashboard/script.js', 'js/jquery.validate.min.js', 'js/dashboard/jquery.colorbox.js', 'js/functions.js', 'js/ui/jquery.ui.core.js', 'js/ui/jquery.ui.widget.js', 'js/ui/jquery.ui.tabs.js', "js/paginator.js", "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', "js/chart/js/jquery-ui.js", "js/chart/js/jquery.themeswitcher.js", "js/chart/js/jquery.tooltip.js", "js/chart/js/jquery.ddchart.js");
                $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/chart/style/ddchart.css', 'media' => 'screen'), array('href' => 'styles/chart/style/theme.css', 'media' => 'screen'));
            } else {
                $this->template->head->javascripts = array('js/jquery.min.js', 'js/dashboard/superfish.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/dashboard/script.js', 'js/jquery.validate.min.js', 'js/dashboard/jquery.colorbox.js', 'js/functions.js', 'js/ui/jquery.ui.core.js', 'js/ui/jquery.ui.widget.js', 'js/ui/jquery.ui.tabs.js', "js/paginator.js", "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js');
            } else :
            $this->template->head->javascripts = array('js/dashboard/jquery-1.4.2.js', 'js/jquery.validate.min.js', "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js');
        endif;
        // Default Header view file
        $this->template->headers = View::factory('template/headers');
        // Default menu
        $this->template->headers->menu = View::factory('template/menu');
        if ($this->session->get('clientuserid')):
            $this->template->headers->menu->logoutlnk = $this->session->get('clientusername');
        endif;
        $this->template->headers->menu->menu = $this->menu;
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
        $this->types = Model::factory('dashboard')->getTypes();
        $this->userid = $this->session->get('clientuserid');
    }
    public function action_index()
    {
        //echo "test";exit;
        // set title tag
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        $this->template->head->title = "Plant & Equipment Dashboard";
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('plant');
        $typeid = $typeidArr[0]['id'];
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getProfilesByType($typeid, $client['id']);
        $param = 'dashboard/index';
        $user = Model::factory('user')->getUserByID($this->userid);
        if (isset($user['pop_up_orderlabel']) && $user['pop_up_orderlabel'] == '1') {
            $datafill = array();
            $datafill['pop_up_orderlabel'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $orderlabel_popup_show = true;
        } else {
            $orderlabel_popup_show = false;
        }
        if (isset($user['pop_up_purchase']) && $user['pop_up_purchase'] == '1') {
            $datafill = array();
            $datafill['pop_up_purchase'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $purchase_popup_show = true;
        } else {
            $purchase_popup_show = false;
        }
        if (isset($user['pop_up_renew']) && $user['pop_up_renew'] == '1') {
            $datafill = array();
            $datafill['pop_up_renew'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $renew_popup_show = true;
        } else {
            $renew_popup_show = false;
        }
        if (isset($user['pop_up_welcome']) && $user['pop_up_welcome'] == '1') {
            $datafill = array();
            $datafill['pop_up_welcome'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $welcome_registration_popup_show = true;
        } else {
            $welcome_registration_popup_show = false;
        }
        $pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));
        $is_sub_user = $this->session->get('is_sub_user');
        // Default Content template
        $this->template->content = View::factory('dashboard/index')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('is_sub_user', $is_sub_user)->bind('user', $user)->bind('errormessage', $errormessage)->bind('successmessage', $successmessage)->bind('orderlabel_popup_show', $orderlabel_popup_show)->bind('welcome_registration_popup_show', $welcome_registration_popup_show)->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $colorcode_display = true;
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param)->bind('colorcode_display', $colorcode_display);
        $this->template->content->code_not_available = View::factory('template/code_not_available')->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $this->template->content->orderlabel_popup = View::factory('template/orderlabel')->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $this->template->content->purchase_code_popup = View::factory('template/purchase_code')->bind('purchase_popup_show', $purchase_popup_show);
        $this->template->content->renew_code_popup = View::factory('template/renew_code')->bind('renew_popup_show', $renew_popup_show);
        $this->template->content->welcome_registration_popup = View::factory('template/welcome_registration')->bind('welcome_registration_popup_show', $welcome_registration_popup_show);
        $this->template->content->expired_popup = View::factory('template/expried');
    }
    public function action_location()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Location Dashboard";
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        $this->session->get("successmessage");
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('location');
        $typeid = $typeidArr[0]['id'];
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getProfilesByType($typeid, $client['id']);
        $param = 'dashboard/location';
        $user = Model::factory('user')->getUserByID($this->userid);
        if (isset($user['pop_up_orderlabel']) && $user['pop_up_orderlabel'] == '1') {
            $datafill = array();
            $datafill['pop_up_orderlabel'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $orderlabel_popup_show = true;
        } else {
            $orderlabel_popup_show = false;
        }
        if (isset($user['pop_up_purchase']) && $user['pop_up_purchase'] == '1') {
            $datafill = array();
            $datafill['pop_up_purchase'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $purchase_popup_show = true;
        } else {
            $purchase_popup_show = false;
        }
        if (isset($user['pop_up_renew']) && $user['pop_up_renew'] == '1') {
            $datafill = array();
            $datafill['pop_up_renew'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $renew_popup_show = true;
        } else {
            $renew_popup_show = false;
        }
        $pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));
        $is_sub_user = $this->session->get('is_sub_user');
        // Default Content template
        $this->template->content = View::factory('dashboard/location')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('is_sub_user', $is_sub_user)->bind('user', $user)->bind('errormessage', $errormessage)->bind('successmessage', $successmessage)->bind('orderlabel_popup_show', $orderlabel_popup_show)->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $colorcode_display = true;
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param)->bind('colorcode_display', $colorcode_display);
        $this->template->content->code_not_available = View::factory('template/code_not_available')->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $this->template->content->orderlabel_popup = View::factory('template/orderlabel')->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $this->template->content->renew_code_popup = View::factory('template/renew_code')->bind('renew_popup_show', $renew_popup_show);
        $this->template->content->purchase_code_popup = View::factory('template/purchase_code')->bind('purchase_popup_show', $purchase_popup_show);
        $this->template->content->expired_popup = View::factory('template/expried');
    }
    public function action_asset()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Person/Business Dashboard";
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('asset');
        $typeid = $typeidArr[0]['id'];
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getProfilesByType($typeid, $client['id']);
        $user = Model::factory('user')->getUserByID($this->userid);
        if (isset($user['pop_up_orderlabel']) && $user['pop_up_orderlabel'] == '1') {
            $datafill = array();
            $datafill['pop_up_orderlabel'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $orderlabel_popup_show = true;
        } else {
            $orderlabel_popup_show = false;
        }
        if (isset($user['pop_up_purchase']) && $user['pop_up_purchase'] == '1') {
            $datafill = array();
            $datafill['pop_up_purchase'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $purchase_popup_show = true;
        } else {
            $purchase_popup_show = false;
        }
        if (isset($user['pop_up_renew']) && $user['pop_up_renew'] == '1') {
            $datafill = array();
            $datafill['pop_up_renew'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $renew_popup_show = true;
        } else {
            $renew_popup_show = false;
        }
        $pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));
        $is_sub_user = $this->session->get('is_sub_user');
        $param = 'dashboard/asset';
        // Default Content template
        $this->template->content = View::factory('dashboard/asset')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('is_sub_user', $is_sub_user)->bind('user', $user)->bind('errormessage', $errormessage)->bind('successmessage', $successmessage)->bind('orderlabel_popup_show', $orderlabel_popup_show)->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $colorcode_display = true;
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param)->bind('colorcode_display', $colorcode_display);
        $this->template->content->code_not_available = View::factory('template/code_not_available')->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $this->template->content->orderlabel_popup = View::factory('template/orderlabel')->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $this->template->content->purchase_code_popup = View::factory('template/purchase_code')->bind('purchase_popup_show', $purchase_popup_show);
        $this->template->content->renew_code_popup = View::factory('template/renew_code')->bind('renew_popup_show', $renew_popup_show);
        $this->template->content->expired_popup = View::factory('template/expried');
    }
    public function action_product()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Product Dashboard";
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('product');
        $typeid = $typeidArr[0]['id'];
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getProfilesByType($typeid, $client['id']);
        $user = Model::factory('user')->getUserByID($this->userid);
        if (isset($user['pop_up_orderlabel']) && $user['pop_up_orderlabel'] == '1') {
            $datafill = array();
            $datafill['pop_up_orderlabel'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $orderlabel_popup_show = true;
        } else {
            $orderlabel_popup_show = false;
        }
        if (isset($user['pop_up_purchase']) && $user['pop_up_purchase'] == '1') {
            $datafill = array();
            $datafill['pop_up_purchase'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $purchase_popup_show = true;
        } else {
            $purchase_popup_show = false;
        }
        if (isset($user['pop_up_renew']) && $user['pop_up_renew'] == '1') {
            $datafill = array();
            $datafill['pop_up_renew'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $renew_popup_show = true;
        } else {
            $renew_popup_show = false;
        }
        $pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));
        $is_sub_user = $this->session->get('is_sub_user');
        $param = 'dashboard/product';
        // Default Content template
        $this->template->content = View::factory('dashboard/product')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('is_sub_user', $is_sub_user)->bind('user', $user)->bind('errormessage', $errormessage)->bind('successmessage', $successmessage)->bind('orderlabel_popup_show', $orderlabel_popup_show)->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $colorcode_display = true;
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param)->bind('colorcode_display', $colorcode_display);
        $this->template->content->code_not_available = View::factory('template/code_not_available')->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $this->template->content->orderlabel_popup = View::factory('template/orderlabel')->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $this->template->content->purchase_code_popup = View::factory('template/purchase_code')->bind('purchase_popup_show', $purchase_popup_show);
        $this->template->content->renew_code_popup = View::factory('template/renew_code')->bind('renew_popup_show', $renew_popup_show);
        $this->template->content->expired_popup = View::factory('template/expried');
    }
    public function action_procedure()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Procedure Dashboard";
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('procedure');
        $typeid = $typeidArr[0]['id'];
        //print_r($this->types);exit;
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getProfilesByType($typeid, $client['id']);
        $user = Model::factory('user')->getUserByID($this->userid);
        if (isset($user['pop_up_orderlabel']) && $user['pop_up_orderlabel'] == '1') {
            $datafill = array();
            $datafill['pop_up_orderlabel'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $orderlabel_popup_show = true;
        } else {
            $orderlabel_popup_show = false;
        }
        if (isset($user['pop_up_purchase']) && $user['pop_up_purchase'] == '1') {
            $datafill = array();
            $datafill['pop_up_purchase'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $purchase_popup_show = true;
        } else {
            $purchase_popup_show = false;
        }
        if (isset($user['pop_up_renew']) && $user['pop_up_renew'] == '1') {
            $datafill = array();
            $datafill['pop_up_renew'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $renew_popup_show = true;
        } else {
            $renew_popup_show = false;
        }
        $pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));
        $is_sub_user = $this->session->get('is_sub_user');
        $param = 'dashboard/procedure';
        // Default Content template
        $this->template->content = View::factory('dashboard/procedure')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('is_sub_user', $is_sub_user)->bind('user', $user)->bind('errormessage', $errormessage)->bind('successmessage', $successmessage)->bind('orderlabel_popup_show', $orderlabel_popup_show)->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $colorcode_display = true;
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param)->bind('colorcode_display', $colorcode_display);
        $this->template->content->code_not_available = View::factory('template/code_not_available')->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $this->template->content->orderlabel_popup = View::factory('template/orderlabel')->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $this->template->content->purchase_code_popup = View::factory('template/purchase_code')->bind('purchase_popup_show', $purchase_popup_show);
        $this->template->content->renew_code_popup = View::factory('template/renew_code')->bind('renew_popup_show', $renew_popup_show);
        $this->template->content->expired_popup = View::factory('template/expried');
    }
    public function action_survey()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Form/Survey/Checklist Dashboard";
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('survey');
        $typeid = $typeidArr[0]['id'];
        //print_r($this->types);exit;
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getProfilesByType($typeid, $client['id']);
        $user = Model::factory('user')->getUserByID($this->userid);
        if (isset($user['pop_up_orderlabel']) && $user['pop_up_orderlabel'] == '1') {
            $datafill = array();
            $datafill['pop_up_orderlabel'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $orderlabel_popup_show = true;
        } else {
            $orderlabel_popup_show = false;
        }
        if (isset($user['pop_up_purchase']) && $user['pop_up_purchase'] == '1') {
            $datafill = array();
            $datafill['pop_up_purchase'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $purchase_popup_show = true;
        } else {
            $purchase_popup_show = false;
        }
        if (isset($user['pop_up_renew']) && $user['pop_up_renew'] == '1') {
            $datafill = array();
            $datafill['pop_up_renew'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $renew_popup_show = true;
        } else {
            $renew_popup_show = false;
        }
        $pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));
        $is_sub_user = $this->session->get('is_sub_user');
        $param = 'dashboard/survey';
        // Default Content template
        $this->template->content = View::factory('dashboard/survey')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('is_sub_user', $is_sub_user)->bind('user', $user)->bind('errormessage', $errormessage)->bind('successmessage', $successmessage)->bind('orderlabel_popup_show', $orderlabel_popup_show)->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $colorcode_display = true;
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param)->bind('colorcode_display', $colorcode_display);
        $this->template->content->code_not_available = View::factory('template/code_not_available')->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $this->template->content->orderlabel_popup = View::factory('template/orderlabel')->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $this->template->content->purchase_code_popup = View::factory('template/purchase_code')->bind('purchase_popup_show', $purchase_popup_show);
        $this->template->content->renew_code_popup = View::factory('template/renew_code')->bind('renew_popup_show', $renew_popup_show);
        $this->template->content->expired_popup = View::factory('template/expried');
    }
    public function action_people()
    {
        // set title tag
        $this->template->head->title = "People Dashboard";
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('people');
        $typeid = $typeidArr[0]['id'];
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getProfilesByType($typeid, $client['id']);
        $param = 'dashboard/people';
        // Default Content template
        $this->template->content = View::factory('dashboard/people')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('errormessage', $errormessage)->bind('successmessage', $successmessage);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param)->bind('colorcode_display', true);
    }
    public function action_misc()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Misc Dashboard";
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('misc');
        $typeid = $typeidArr[0]['id'];
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getProfilesByType($typeid, $client['id']);
        $user = Model::factory('user')->getUserByID($this->userid);
        if (isset($user['pop_up_orderlabel']) && $user['pop_up_orderlabel'] == '1') {
            $datafill = array();
            $datafill['pop_up_orderlabel'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $orderlabel_popup_show = true;
        } else {
            $orderlabel_popup_show = false;
        }
        if (isset($user['pop_up_purchase']) && $user['pop_up_purchase'] == '1') {
            $datafill = array();
            $datafill['pop_up_purchase'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $purchase_popup_show = true;
        } else {
            $purchase_popup_show = false;
        }
        if (isset($user['pop_up_renew']) && $user['pop_up_renew'] == '1') {
            $datafill = array();
            $datafill['pop_up_renew'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $renew_popup_show = true;
        } else {
            $renew_popup_show = false;
        }
        $pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));
        $is_sub_user = $this->session->get('is_sub_user');
        $param = 'dashboard/misc';
        // Default Content template
        $this->template->content = View::factory('dashboard/misc')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('is_sub_user', $is_sub_user)->bind('user', $user)->bind('errormessage', $errormessage)->bind('successmessage', $successmessage)->bind('orderlabel_popup_show', $orderlabel_popup_show)->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $colorcode_display = true;
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param)->bind('colorcode_display', $colorcode_display);
        $this->template->content->code_not_available = View::factory('template/code_not_available')->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $this->template->content->orderlabel_popup = View::factory('template/orderlabel')->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $this->template->content->purchase_code_popup = View::factory('template/purchase_code')->bind('purchase_popup_show', $purchase_popup_show);
        $this->template->content->renew_code_popup = View::factory('template/renew_code')->bind('renew_popup_show', $renew_popup_show);
        $this->template->content->expired_popup = View::factory('template/expried');
    }
    public function action_exhibit()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Exhibit Dashboard";
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('exhibit');
        $typeid = $typeidArr[0]['id'];
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getProfilesByType($typeid, $client['id']);
        $user = Model::factory('user')->getUserByID($this->userid);
        if (isset($user['pop_up_orderlabel']) && $user['pop_up_orderlabel'] == '1') {
            $datafill = array();
            $datafill['pop_up_orderlabel'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $orderlabel_popup_show = true;
        } else {
            $orderlabel_popup_show = false;
        }
        if (isset($user['pop_up_purchase']) && $user['pop_up_purchase'] == '1') {
            $datafill = array();
            $datafill['pop_up_purchase'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $purchase_popup_show = true;
        } else {
            $purchase_popup_show = false;
        }
        if (isset($user['pop_up_renew']) && $user['pop_up_renew'] == '1') {
            $datafill = array();
            $datafill['pop_up_renew'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $renew_popup_show = true;
        } else {
            $renew_popup_show = false;
        }
        $pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));
        $is_sub_user = $this->session->get('is_sub_user');
        $param = 'dashboard/exhibit';
        // Default Content template
        $this->template->content = View::factory('dashboard/exhibit')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('is_sub_user', $is_sub_user)->bind('user', $user)->bind('errormessage', $errormessage)->bind('successmessage', $successmessage)->bind('orderlabel_popup_show', $orderlabel_popup_show)->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $colorcode_display = true;
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param)->bind('colorcode_display', $colorcode_display);
        $this->template->content->code_not_available = View::factory('template/code_not_available')->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $this->template->content->orderlabel_popup = View::factory('template/orderlabel')->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $this->template->content->purchase_code_popup = View::factory('template/purchase_code')->bind('purchase_popup_show', $purchase_popup_show);
        $this->template->content->renew_code_popup = View::factory('template/renew_code')->bind('renew_popup_show', $renew_popup_show);
        $this->template->content->expired_popup = View::factory('template/expried');
    }
    public function action_voc()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "VOCC Dashboard";
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('voc');
        $typeid = $typeidArr[0]['id'];
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getProfilesByType($typeid, $client['id']);
        $user = Model::factory('user')->getUserByID($this->userid);
        if (isset($user['pop_up_orderlabel']) && $user['pop_up_orderlabel'] == '1') {
            $datafill = array();
            $datafill['pop_up_orderlabel'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $orderlabel_popup_show = true;
        } else {
            $orderlabel_popup_show = false;
        }
        if (isset($user['pop_up_purchase']) && $user['pop_up_purchase'] == '1') {
            $datafill = array();
            $datafill['pop_up_purchase'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $purchase_popup_show = true;
        } else {
            $purchase_popup_show = false;
        }
        if (isset($user['pop_up_renew']) && $user['pop_up_renew'] == '1') {
            $datafill = array();
            $datafill['pop_up_renew'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $renew_popup_show = true;
        } else {
            $renew_popup_show = false;
        }
        $pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));
        $is_sub_user = $this->session->get('is_sub_user');
        $param = 'dashboard/voc';
        // Default Content template
        $this->template->content = View::factory('dashboard/voc')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('is_sub_user', $is_sub_user)->bind('user', $user)->bind('errormessage', $errormessage)->bind('successmessage', $successmessage)->bind('orderlabel_popup_show', $orderlabel_popup_show)->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $colorcode_display = true;
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param)->bind('colorcode_display', $colorcode_display);
        $this->template->content->code_not_available = View::factory('template/code_not_available')->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $this->template->content->orderlabel_popup = View::factory('template/orderlabel')->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $this->template->content->purchase_code_popup = View::factory('template/purchase_code')->bind('purchase_popup_show', $purchase_popup_show);
        $this->template->content->renew_code_popup = View::factory('template/renew_code')->bind('renew_popup_show', $renew_popup_show);
        $this->template->content->expired_popup = View::factory('template/expried');
    }
    //this is for voc user login view
    public function action_voc_user()
    {
        // set title tag
        $this->template->head->title = "VOCC Dashboard";
        $this->template->headers->menu = View::factory('template/vocmenu');
        if ($this->session->get('voc_user_id')):
            $this->template->headers->menu->logoutlnk = $this->session->get('clientusername');
        endif;
        $this->template->headers->menu->voc_user_logged_in = $this->voc_user_logged_in;
        $this->template->headers->menu->menu = '';
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $profile = $this->session->get('profile_id_array');
        $voc_user_id = $this->session->get('voc_user_id');
        // Default Content template
        $this->template->content = View::factory('dashboard/voc_user')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('errormessage', $errormessage)->bind('successmessage', $successmessage)->bind('voc_user_id', $voc_user_id);
        $colorcode_display = true;
        $this->template->content->innermenu = "";
    }
    public function action_customqr()
    {
        // set title tag
        $this->template->head->title = "Customer QR CODE";
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('customqr');
        $typeid = $typeidArr[0]['id'];
        //print_r($this->types);exit;
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getProfilesByType($typeid, $client['id']);
        $param = 'dashboard/customqr';
        // Default Content template
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        if ($customqr == '0') {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $this->template->content = View::factory('dashboard/customqr')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('type_id', $typeid)->bind('qr', $qr);
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param);
    }
    public function action_changepwd()
    {
        // set title tag
        $this->template->head->title = "Change password";
        $errors = '';
        $successmsg = '';
        if (isset($_POST) && $_POST):
            $txtnewpass = addslashes(trim($_POST['txtnewpass']));
            $txtconfirmpass = addslashes(trim($_POST['txtconfirmpass']));
            if ($txtnewpass == ''):
                $errors['txtnewpass'] = "New Password can't left blank..!";
            elseif ((strlen($txtnewpass) < 5) || (strlen($txtnewpass) > 12)):
                $errors['txtnewpass'] = "Password must between 5-12 character..!";
            elseif ($txtconfirmpass == ''):
                $errors['txtconfirmpass'] = "Confirm Password can't left blank..!";
            elseif ($txtnewpass !== $txtconfirmpass):
                $errors['txtconfirmpass'] = "Confirm Password must be same as New Password..!";
            else:
                $changedpass = Model::factory('user')->editpass($this->userid, array('password' => ($txtnewpass)));
                $user = Model::factory('user')->getUserByID($this->userid);
                if ($user['role'] == 5) {
                    Model::factory('client')->editpass($user['client_id'], array('password' => ($txtnewpass)));
                }
                $successmsg = 'Your Password has been changed..!';
            endif;
            //endif;

        endif;
        $param = 'dashboard/changepwd';
        // Default Content template
        $this->template->content = View::factory('dashboard/changepwd')->bind('errors', $errors)->bind('successmsg', $successmsg);
        $client = Model::factory('user')->getClientById($this->userid);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param);
    }
    public function action_getQR()
    {
        // set title tag
        $this->template->head->title = "Get QR CODE";
        if (isset($_REQUEST["download_as"])) {
            $download_as = $_REQUEST["download_as"];
        } else {
            $download_as = "pdf";
        }
        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        if ($user_data["is_sub_user"] == 1 && $user_data["access_download"] == 0) {
            Session::instance()->set('errormessage', 'Oops! You have no access to download code.');
            $this->request->redirect('dashboard');
        }
        $id = $this->request->param("id");
        $profile_id = Model::factory('profiles')->getProfileIdByQrId($id);
        if (isset($profile_id) && is_array($profile_id) && empty($profile_id)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $profile = Model::factory('profiles')->getprofileById($profile_id);
        if (isset($profile) && is_array($profile) && empty($profile)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $this->session->set("errormessage", "You can not perform this action on expired profile.");
            $this->request->redirect('dashboard/index');
            exit;
        }
        $img_code_type = Model::factory('profiles')->getCodeTypeByProfileId($profile_id);
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        if ($profile['type_id'] == '7') {
            $client = Model::factory('user')->getClientById($this->userid);
            $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
            $customqr = $customqr_option[0]['customqr_option'];
            if ($customqr == '0') {
                $this->request->redirect('dashboard/index');
                exit;
            }
        }
        $qr = Model::factory('qrcode')->getQRCodeById($id);
        $qr_path = $qr["qrimg_name"];
        if ($download_as != "pdf") {
            switch ($download_as) {
                case "png":
                    $download_filename = 'code_' . $profile["id"] . '.png';
                    $download_file_path = $qr_path;
                    break;
                case "jpg":
                    $download_filename = 'code_' . $profile["id"] . '.jpg';
                    $jpgfile = "images/qrcode/code_" . $profile["id"] . ".jpg";
                    exec('convert ' . $qr_path . ' ' . $jpgfile);
                    $download_file_path = $jpgfile;
                    break;
                case "tiff":
                    $download_filename = 'code_' . $profile["id"] . '.tiff';
                    $tifffile = "images/qrcode/code_" . $profile["id"] . ".tiff";
                    exec('convert ' . $qr_path . ' ' . $tifffile);
                    $download_file_path = $tifffile;
                    break;
                case "eps":
                    $download_filename = 'code_' . $profile["id"] . '.eps';
                    $espfile = "images/qrcode/code_" . $profile["id"] . ".eps";
                    exec('convert ' . $qr_path . ' ' . $espfile);
                    $download_file_path = $espfile;
                    break;
            }
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $download_filename);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($download_file_path));
            ob_clean();
            flush();
            readfile($download_file_path);
            @unlink($tifffile);
            @unlink($epsfile);
            exit;
        } else {
            if ($img_code_type == 0) {
                $img_code_type = "QR";
            } else {
                $img_code_type = "DM";
            }
            /* include_once("classes/fpdf.php");

            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            $pdf->Image($qr_path, 50, 50);
            $pdf->Output();
            header('Content-disposition: attachment; filename="' . $img_code_type . ".pdf" . '"');
            header('Content-type: application/pdf');
             */
            //to generate pdf and download code start
            // require_once 'pdf.php';
            // $content1 = 'Profile No. : ' . $profile['id'];
            // $content2 = 'Name : ' . $profile['code_profile_name'];
            // if ($profile['url'] != '') {
            //     $content3 = 'URL :' . $profile['url'];
            // }
            // $pdf = new PDF();
            // $pdf->AddPage();
            // $pdf->Image('images/logo.png', 90, 12, 40, 0, '', URL::base());
            // $pdf->SetLeftMargin(45);
            // $pdf->SetFont('Arial', '', 20);
            // $pdf->SetFontSize(14);
            // $pdf->Ln(25);
            // $pdf->WriteHTML($content1);
            // $pdf->Ln(10);
            // $pdf->WriteHTML($content2);
            // if ($profile['url'] != '') {
            //     $pdf->Ln(10);
            //     $pdf->WriteHTML($content3);
            // }
            // $pdf->Image($qr_path, 90, 80, 40, 0, '', URL::base());
            // $pdf->Output('code_' . $profile["id"] . '.pdf', 'D');


            $pdf_html = '<img style="margin-left:18%;" src="'. URL::base().'/images/logo.png"><br>';
            // $pdf_html = '<img style="margin-left:18%;" src="'. URL::base().'/images/logo.png"><br>';
            $pdf_html .= '<p>Profile No. : ' . $profile['id'].'<p>';
            $pdf_html .= '<p>Name : ' . $profile['code_profile_name'] . '</p>';
            $pdf_html .= '<p style="'.(!empty($profile['url']) ? '' : 'none').'">URL : '. (!empty($profile['url']) ? $profile['url'] : '').'</p>';

            $pdf_html .= '<img  height="175" width="175" style="margin-top:25px;margin-left:26%;" src='. URL::base().'/'.$qr_path.'>';
            $pdf = new Pdf(array(
                'binary' => '/usr/local/bin/wkhtmltopdf',
                'margin-top'    => 10,
                'margin-right'  => 10,
                'margin-bottom' => 10,
                'margin-left'   => 45,
                "zoom" => 1.25
            ));

            $pdf->addPage($pdf_html);
        
            if (!$pdf->send('code_' . $profile["id"] . '.pdf')) {
                $error = $pdf->getError();
                mail('admin@scanlink.net.au', 'Download QR Code Image ', $error);
            }
        }
        exit;
    }
    public function action_orderLabel()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Order Label ";
        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        if ($user_data["is_sub_user"] == 1 && $user_data["access_label"] == 0) {
            $this->session->set("errormessage", "Oops! You have no access to order label.");
            $this->request->redirect('dashboard/index');
            exit;
        }
        $errormessage = '';
        $errors = '';
        $successmsg = '';
        $expried_or_not = false;
        $id = $this->request->param("id");
        if ($id == '' || $id == 0) {
            $this->session->set("errormessage", "Please select profile.");
            $this->request->redirect('dashboard');
            exit;
        }
        $errormessage = $this->session->get("errormessage");
        $qr = Model::factory('qrcode')->getQRCodeById($id);
        $profile = Model::factory('profiles')->getprofileById($qr['profile_id']);
        if (isset($profile) && is_array($profile) && empty($profile)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $expried_or_not = true;
        }
        //if post request than insert into database
        if ($this->request->method() === Request::POST):
            if (($_POST['quantity_small'] != '' && $_POST['quantity_small'] != '0') || ($_POST['quantity_big'] != '' && $_POST['quantity_big'] != '0')) {
                $quantitydata = array();
                $quantitydata['profile_id'] = $qr['profile_id'];
                $quantitydata['quantity_small'] = $_POST['quantity_small'];
                $quantitydata['quantity_big'] = $_POST['quantity_big'];
                if ($quantitydata['quantity_small'] == '') {
                    $quantitydata['quantity_small'] = 0;
                }
                if ($quantitydata['quantity_big'] == '') {
                    $quantitydata['quantity_big'] = 0;
                }
                $this->session->set("quantitydata", $quantitydata);
                $this->request->redirect('dashboard/ordersummary');
                exit;
            } else {
                $errormessage = "Select quantity.";
            } else :
            $this->session->delete("quantitydata");
        endif;
        $paypal_email = Model::factory('settings')->getValueByTitle('paypal_email');
        $slag = $eq_type[0]['slag'];
        $this->template->head->title .= " of " . $eq_type[0]['name'];
        if ($slag == "plant") {
            $slag = "index";
        }
        $param = 'dashboard/' . $slag;
        $user_id = $this->session->get('clientuserid');
        // Default Content template
        $this->template->content = View::factory('dashboard/orderlabel')->bind('qr', $qr)->bind('user_id', $user_id)->bind('eq_type', $eq_type)->bind('profile', $profile)->bind('errors', $errors)->bind('paypal_email', $paypal_email)->bind('successmsg', $successmsg)->bind('errormsg', $errormessage)->bind('id', $id)->bind('expried_or_not', $expried_or_not);
        $client = Model::factory('user')->getClientById($this->userid);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param);
    }
    public function action_visitorlog()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Visitor Log ";
        $id = $this->request->param("id");
        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        if ($user_data["is_sub_user"] == 1 && $user_data["access_log"] == 0) {
            Session::instance()->set('errormessage', 'Oops! You have no access to view visitor log.');
            $this->request->redirect('dashboard');
        }
        $profile = Model::factory('profiles')->getprofileById($id);
        if (isset($profile) && is_array($profile) && empty($profile)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $this->session->set("errormessage", "You can not perform this action on expired profile.");
            $this->request->redirect('dashboard/index');
            exit;
        }
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        $client = Model::factory('user')->getClientById($this->userid);
        if ($profile['type_id'] == '7') {
            $url = $profile['name'];
            $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
            $customqr = $customqr_option[0]['customqr_option'];
            if ($customqr == '0') {
                $this->request->redirect('dashboard/index');
                exit;
            }
        } else {
            $clienturl = Model::factory('client')->geturl($id);
            $url = URL::base() . $clienturl . "/" . $id;
        }
        $slag = $eq_type[0]['slag'];
        $this->template->head->title .= " of " . $eq_type[0]['name'];
        if ($slag == "plant") {
            $slag = "index";
        }
        $param = 'dashboard/' . $slag;
        $user_id = $this->session->get('clientuserid');
        $this->template->content = View::factory('dashboard/visitorlog')->bind('eq_type', $eq_type)->bind('user_id', $user_id)->bind('profile', $profile)->bind('contacts', $contacts);
        $contacts = Model::factory('contacts')->getContactsById($id);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param);
    }
    public function action_cumulativeAnalytics()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        if (empty($_POST)) {
            $this->request->redirect(URL::base() . 'dashboard/index');
            exit;
        }
        // set title tag
        $this->template->head->title = "Cumulative Analytics  ";
        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        if ($user_data["is_sub_user"] == 1 && $user_data["access_analytics"] == 0) {
            Session::instance()->set('errormessage', 'Oops! You have no access to view analytics.');
            $this->request->redirect('dashboard');
        }
        $cumulative_profile_id = $_POST['cumulative_code_profile'];
        $profile_id_arr = explode(",", $cumulative_profile_id);
        $profile = Model::factory('profiles')->getprofileById($profile_id_arr[0]);
        $form_submission_count_arr = array();
        foreach ($profile_id_arr as $profile_id) {
            $all_profile[] = Model::factory('profiles')->getprofileById($profile_id);
            $formbuilder_ans = Model::factory('formbuilder')->get_profile_answers($profile_id);
            if ($formbuilder_ans) {
                $form_submission_count_arr[$profile_id] = count($formbuilder_ans) . '<br>';
            }
            $form_analytics_questions[] = Model::factory('formbuilder')->get_analytics_questions($profile_id);
        }
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        $client = Model::factory('user')->getClientById($this->userid);
        $expried_or_not = false;
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $expried_or_not = true;
        }
        $slag = $eq_type[0]['slag'];
        $this->template->head->title .= " of " . $eq_type[0]['name'];
        if ($slag == "plant") {
            $slag = "index";
        }
        $param = 'dashboard/' . $slag;
        $this->template->content = View::factory('dashboard/cumulative_analytics')->bind('profile', $profile)->bind('expried_or_not', $expried_or_not)->bind('profile_id_arr', $profile_id_arr)->bind('form_submission_count_arr', $form_submission_count_arr)->bind('form_analytics_questions', $form_analytics_questions)->bind('cumulative_profile_id', $cumulative_profile_id)->bind('all_profile', $all_profile);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param);
    }
    public function action_cumulative_analytics_print()
    {
        // set title tag
        $this->template->head->title = "Cumulative Analytics  ";
        $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'print'), array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'print'), array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'print'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'print'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'print'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/chart/style/ddchart.css', 'media' => 'print'), array('href' => 'styles/chart/style/ddchart.css', 'media' => 'screen'), array('href' => 'styles/chart/style/theme.css', 'media' => 'print'), array('href' => 'styles/chart/style/theme.css', 'media' => 'screen'));
        $this->template->head->javascripts = array('js/jquery.min.js', 'js/dashboard/superfish.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/dashboard/script.js', 'js/jquery.validate.min.js', 'js/dashboard/jquery.colorbox.js', 'js/functions.js', 'js/ui/jquery.ui.core.js', 'js/ui/jquery.ui.widget.js', 'js/ui/jquery.ui.tabs.js', "js/paginator.js", "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', "js/chart/js/jquery-ui.js", "js/chart/js/jquery.themeswitcher.js", "js/chart/js/jquery.tooltip.js", "js/chart/js/jquery.ddchart.js");
        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        if ($user_data["is_sub_user"] == 1 && $user_data["access_analytics"] == 0) {
            Session::instance()->set('errormessage', 'Oops! You have no access to view analytics.');
            $this->request->redirect('dashboard');
        }
        $cumulative_profile_id = $_REQUEST['cumulative_code_profile'];
        $profile_id_arr = explode(",", $cumulative_profile_id);
        $profile = Model::factory('profiles')->getprofileById($profile_id_arr[0]);
        $form_submission_count_arr = array();
        foreach ($profile_id_arr as $profile_id) {
            $all_profile[] = Model::factory('profiles')->getprofileById($profile_id);
            $formbuilder_ans = Model::factory('formbuilder')->get_profile_answers($profile_id);
            if ($formbuilder_ans) {
                $form_submission_count_arr[$profile_id] = count($formbuilder_ans) . '<br>';
            }
            $form_analytics_questions[] = Model::factory('formbuilder')->get_analytics_questions($profile_id);
        }
        if (isset($profile) && is_array($profile) && empty($profile)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        $client = Model::factory('user')->getClientById($this->userid);
        $expried_or_not = false;
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $expried_or_not = true;
        }
        $slag = $eq_type[0]['slag'];
        $this->template->head->title .= " of " . $eq_type[0]['name'];
        if ($slag == "plant") {
            $slag = "index";
        }
        $param = 'dashboard/' . $slag;
        $user_id = $this->session->get('clientuserid');
        $this->template->content = View::factory('dashboard/cumulative_analytics_print')->bind('eq_type', $eq_type)->bind('user_id', $user_id)->bind('profile', $profile)->bind('expried_or_not', $expried_or_not)->bind('profile_id_arr', $profile_id_arr)->bind('form_submission_count_arr', $form_submission_count_arr)->bind('form_analytics_questions', $form_analytics_questions)->bind('cumulative_profile_id', $cumulative_profile_id)->bind('all_profile', $all_profile);
    }
    public function action_cumulative_analytics_print_pdf()
    {
        // set title tag
        $this->template->head->title = "Cumulative Analytics  ";
        $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'print'), array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'print'), array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'print'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'print'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'print'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/chart/style/ddchart.css', 'media' => 'print'), array('href' => 'styles/chart/style/ddchart.css', 'media' => 'screen'), array('href' => 'styles/chart/style/theme.css', 'media' => 'print'), array('href' => 'styles/chart/style/theme.css', 'media' => 'screen'));
        $this->template->head->javascripts = array('js/jquery.min.js', 'js/dashboard/superfish.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/dashboard/script.js', 'js/jquery.validate.min.js', 'js/dashboard/jquery.colorbox.js', 'js/functions.js', 'js/ui/jquery.ui.core.js', 'js/ui/jquery.ui.widget.js', 'js/ui/jquery.ui.tabs.js', "js/paginator.js", "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', "js/chart/js/jquery-ui.js", "js/chart/js/jquery.themeswitcher.js", "js/chart/js/jquery.tooltip.js", "js/chart/js/jquery.ddchart.js");
        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        //if($user_data["is_sub_user"]==1 && $user_data["access_analytics"]==0)
        //{
        //   Session::instance()->set('errormessage', 'Oops! You have no access to view analytics.');
        // $this->request->redirect('dashboard');
        //}
        if (isset($_REQUEST["html"])) {
            error_reporting(E_ALL);
            $html_content = $_REQUEST["html"];
            // create new PDF document
            ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
            $footer = array('odd' => array('L' => array('content' => '', 'font-size' => 10, 'font-style' => 'B', 'font-family' => 'arial', 'color' => '#000000'), 'C' => array('content' => '', 'font-size' => 10, 'font-style' => 'B', 'font-family' => 'arial', 'color' => '#000000'), 'R' => array('content' => "Date: " . date("d/m/Y H:i:s"), 'font-size' => 10, 'font-style' => 'B', 'font-family' => 'arial', 'color' => '#000000'), 'line' => 1), 'even' => array('L' => array('content' => '', 'font-size' => 10, 'font-style' => 'B', 'font-family' => 'arial', 'color' => '#000000'), 'C' => array('content' => '', 'font-size' => 10, 'font-style' => 'B', 'font-family' => 'arial', 'color' => '#000000'), 'R' => array('content' => "Date: " . date("d/m/Y H:i:s"), 'font-size' => 10, 'font-style' => 'B', 'font-family' => 'arial', 'color' => '#000000'), 'line' => 1));
            // require "mpdf/mpdf.php";
            // $mpdf = new mPDF('c', 'A4', '', 'arial');
            // $mpdf->SetDisplayMode('fullpage');
            // $mpdf->SetFooter($footer);
            // $mpdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list
            // // LOAD a stylesheet
            // $mpdf->WriteHTML($html_content);
            // //$mpdf->Output('doc.pdf','D');
            // $mpdf->Output('doc.pdf', 'I');

            $pdf = new Pdf(array(
                'binary' => '/usr/local/bin/wkhtmltopdf',
                'margin-top'    => 10,
                'margin-right'  => 10,
                'margin-bottom' => 10,
                'margin-left'   => 45,
                "zoom" => 1.25,
                'footer-html'=>$footer
            ));

            $pdf->addPage($html_content);
        
            if (!$pdf->send('doc.pdf', 'I')) {
                $error = $pdf->getError();
                mail('admin@scanlink.net.au', 'Download QR Code Image ', $error);
            }
            exit;
            exit;
        }
        $cumulative_profile_id = $_REQUEST;
        $profile_id_arr = explode(",", $cumulative_profile_id['cumulative_code_profile']);
        $profile = Model::factory('profiles')->getprofileById($profile_id_arr[0]);
        //$id = $this->request->param("id");
        //$profile = Model::factory('profiles')->getprofileById($id);
        $form_submission_count_arr = array();
        foreach ($profile_id_arr as $profile_id) {
            $all_profile[] = Model::factory('profiles')->getprofileById($profile_id);
            $formbuilder_ans = Model::factory('formbuilder')->get_profile_answers($profile_id);
            if ($formbuilder_ans) {
                $form_submission_count_arr[$profile_id] = count($formbuilder_ans) . '<br>';
            }
            $form_analytics_questions[] = Model::factory('formbuilder')->get_analytics_questions($profile_id);
        }
        //$formbuilder_ans = Model::factory('formbuilder')->get_profile_answers($id);
        //$form_submission_count = 0;
        //if($formbuilder_ans)
        //{
        //$form_submission_count = count($formbuilder_ans);
        //}
        //$form_analytics_questions=  Model::factory('formbuilder')->get_analytics_questions($id);
        if (isset($profile) && is_array($profile) && empty($profile)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        $client = Model::factory('user')->getClientById($this->userid);
        $expried_or_not = false;
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $expried_or_not = true;
        }
        $slag = $eq_type[0]['slag'];
        $this->template->head->title .= " of " . $eq_type[0]['name'];
        if ($slag == "plant") {
            $slag = "index";
        }
        $param = 'dashboard/' . $slag;
        $user_id = $this->session->get('clientuserid');
        $this->template->content = View::factory('dashboard/cumulative_analytics_print_pdf')->bind('eq_type', $eq_type)->bind('user_id', $user_id)->bind('profile', $profile)->bind('profile_id_arr', $profile_id_arr)->bind('expried_or_not', $expried_or_not)->bind('form_submission_count_arr', $form_submission_count_arr)->bind('all_profile', $all_profile)->bind('form_analytics_questions', $form_analytics_questions);
    }
    public function action_scanalytics()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Scanalytics  ";
        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        if ($user_data["is_sub_user"] == 1 && $user_data["access_analytics"] == 0) {
            Session::instance()->set('errormessage', 'Oops! You have no access to view analytics.');
            $this->request->redirect('dashboard');
        }
        $id = $this->request->param("id");
        $profile = Model::factory('profiles')->getprofileById($id);
        $formbuilder_ans = Model::factory('formbuilder')->get_profile_answers($id);
        $form_submission_count = 0;
        if ($formbuilder_ans) {
            $form_submission_count = count($formbuilder_ans);
        }
        $form_analytics_questions = Model::factory('formbuilder')->get_analytics_questions($id);
        if (isset($profile) && is_array($profile) && empty($profile)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        $client = Model::factory('user')->getClientById($this->userid);
        $expried_or_not = false;
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $expried_or_not = true;
        }
        if ($profile['type_id'] == '7') {
            $url = $profile['name'];
            $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
            $customqr = $customqr_option[0]['customqr_option'];
            if ($customqr == '0') {
                $this->request->redirect('dashboard/index');
                exit;
            }
        } else {
            $clienturl = Model::factory('client')->geturl($id);
            $url = URL::base() . $clienturl . "/" . $id;
        }
        $analytics = @file_get_contents(GALATECH_API . "data/getdata?key=" . $profile["analytic_key"]);
        $analytics = array();
        $json_country = "";
        $json_device = "";
        $json_browser = "";
        if ($analytics) {
            $analytics = json_decode($analytics);
            $json_country = json_encode($analytics->country);
            $json_device = json_encode($analytics->device);
            $json_browser = json_encode($analytics->browsers);
        }
        $slag = $eq_type[0]['slag'];
        $this->template->head->title .= " of " . $eq_type[0]['name'];
        if ($slag == "plant") {
            $slag = "index";
        }
        $param = 'dashboard/' . $slag;
        $user_id = $this->session->get('clientuserid');
        $this->template->content = View::factory('dashboard/scanalytics')->bind('eq_type', $eq_type)->bind('user_id', $user_id)->bind('profile', $profile)->bind('jsonCountry', $json_country)->bind('jsonDevice', $json_device)->bind('jsonBrowser', $json_browser)->bind('expried_or_not', $expried_or_not)->bind('form_submission_count', $form_submission_count)->bind('form_analytics_answers', $form_analytics_answers)->bind('form_analytics_questions', $form_analytics_questions);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param);
    }
    public function action_scanalytics_print()
    {
        // set title tag
        $this->template->head->title = "Scanalytics  ";
        $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'print'), array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'print'), array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'print'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'print'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'print'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/chart/style/ddchart.css', 'media' => 'print'), array('href' => 'styles/chart/style/ddchart.css', 'media' => 'screen'), array('href' => 'styles/chart/style/theme.css', 'media' => 'print'), array('href' => 'styles/chart/style/theme.css', 'media' => 'screen'));
        $this->template->head->javascripts = array('js/jquery.min.js', 'js/dashboard/superfish.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/dashboard/script.js', 'js/jquery.validate.min.js', 'js/dashboard/jquery.colorbox.js', 'js/functions.js', 'js/ui/jquery.ui.core.js', 'js/ui/jquery.ui.widget.js', 'js/ui/jquery.ui.tabs.js', "js/paginator.js", "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', "js/chart/js/jquery-ui.js", "js/chart/js/jquery.themeswitcher.js", "js/chart/js/jquery.tooltip.js", "js/chart/js/jquery.ddchart.js");
        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        if ($user_data["is_sub_user"] == 1 && $user_data["access_analytics"] == 0) {
            Session::instance()->set('errormessage', 'Oops! You have no access to view analytics.');
            $this->request->redirect('dashboard');
        }
        $id = $this->request->param("id");
        $profile = Model::factory('profiles')->getprofileById($id);
        $formbuilder_ans = Model::factory('formbuilder')->get_profile_answers($id);
        $form_submission_count = 0;
        if ($formbuilder_ans) {
            $form_submission_count = count($formbuilder_ans);
        }
        $form_analytics_questions = Model::factory('formbuilder')->get_analytics_questions($id);
        if (isset($profile) && is_array($profile) && empty($profile)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        $client = Model::factory('user')->getClientById($this->userid);
        $expried_or_not = false;
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $expried_or_not = true;
        }
        if ($profile['type_id'] == '7') {
            $url = $profile['name'];
            $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
            $customqr = $customqr_option[0]['customqr_option'];
            if ($customqr == '0') {
                $this->request->redirect('dashboard/index');
                exit;
            }
        } else {
            $clienturl = Model::factory('client')->geturl($id);
            $url = URL::base() . $clienturl . "/" . $id;
        }
        $analytics = @file_get_contents(GALATECH_API . "data/getdata?key=" . $profile["analytic_key"]);
        $analytics = json_decode($analytics);
        $json_country = json_encode($analytics->country);
        $json_device = json_encode($analytics->device);
        $json_browser = json_encode($analytics->browsers);
        $slag = $eq_type[0]['slag'];
        $this->template->head->title .= " of " . $eq_type[0]['name'];
        if ($slag == "plant") {
            $slag = "index";
        }
        $param = 'dashboard/' . $slag;
        $user_id = $this->session->get('clientuserid');
        $this->template->content = View::factory('dashboard/scanalytics_print')->bind('eq_type', $eq_type)->bind('user_id', $user_id)->bind('profile', $profile)->bind('jsonCountry', $json_country)->bind('jsonDevice', $json_device)->bind('jsonBrowser', $json_browser)->bind('expried_or_not', $expried_or_not)->bind('form_submission_count', $form_submission_count)->bind('form_analytics_answers', $form_analytics_answers)->bind('form_analytics_questions', $form_analytics_questions);
    }
    public function action_scanalytics_print_pdf()
    {
        // set title tag
        $this->template->head->title = "Scanalytics  ";
        $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'print'), array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'print'), array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'print'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'print'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'print'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/chart/style/ddchart.css', 'media' => 'print'), array('href' => 'styles/chart/style/ddchart.css', 'media' => 'screen'), array('href' => 'styles/chart/style/theme.css', 'media' => 'print'), array('href' => 'styles/chart/style/theme.css', 'media' => 'screen'));
        $this->template->head->javascripts = array('js/jquery.min.js', 'js/dashboard/superfish.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/dashboard/script.js', 'js/jquery.validate.min.js', 'js/dashboard/jquery.colorbox.js', 'js/functions.js', 'js/ui/jquery.ui.core.js', 'js/ui/jquery.ui.widget.js', 'js/ui/jquery.ui.tabs.js', "js/paginator.js", "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', "js/chart/js/jquery-ui.js", "js/chart/js/jquery.themeswitcher.js", "js/chart/js/jquery.tooltip.js", "js/chart/js/jquery.ddchart.js");
        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        if ($user_data["is_sub_user"] == 1 && $user_data["access_analytics"] == 0) {
            Session::instance()->set('errormessage', 'Oops! You have no access to view analytics.');
            $this->request->redirect('dashboard');
        }
        if (isset($_REQUEST["html"])) {
            error_reporting(E_ALL);
            $html_content = $_REQUEST["html"];
            //$html_content = str_replace(URL::base(), ABS_PATH, $html_content); //used when test in local
            //echo "123".$html_content."456";
            //        $fp = fopen('data.txt', 'w');
            //        fwrite($fp, $html_content);
            //        fclose($fp);
            //
            //        $filename = "data.txt";
            //        $handle = fopen($filename, "r");
            //        $html_content = fread($handle, filesize($filename));
            //        fclose($handle);
            //
            //
            //        // create new PDF document
           // ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
            /*
            require("PHPExcel/PHPExcel/Shared/PDF/tcpdf_6/tcpdf.php");

            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetPrintHeader(false);
            $pdf->SetPrintFooter(false);
            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // set font
            $fontname = $pdf->addTTFfont('calibri.ttf', 'TrueTypeUnicode', '', 32);
            $pdf->SetFont($fontname, '', 12);

            //$pdf->SetFont('helvetica', '', 12);

            // add a page
            $pdf->AddPage();

            // output the HTML content
            $html_content = str_replace("'","&#039;",$html_content);

            $pdf->writeHTML($html_content, true, false, true, false, '');

            $pdf->Output('test.pdf', 'D');
             */
            $footer = array('odd' => array('L' => array('content' => '', 'font-size' => 10, 'font-style' => 'B', 'font-family' => 'arial', 'color' => '#000000'), 'C' => array('content' => '', 'font-size' => 10, 'font-style' => 'B', 'font-family' => 'arial', 'color' => '#000000'), 'R' => array('content' => "Date: " . date("d/m/Y H:i:s"), 'font-size' => 10, 'font-style' => 'B', 'font-family' => 'arial', 'color' => '#000000'), 'line' => 1), 'even' => array('L' => array('content' => '', 'font-size' => 10, 'font-style' => 'B', 'font-family' => 'arial', 'color' => '#000000'), 'C' => array('content' => '', 'font-size' => 10, 'font-style' => 'B', 'font-family' => 'arial', 'color' => '#000000'), 'R' => array('content' => "Date: " . date("d/m/Y H:i:s"), 'font-size' => 10, 'font-style' => 'B', 'font-family' => 'arial', 'color' => '#000000'), 'line' => 1));
            // require "mpdf/mpdf.php";
            // $mpdf = new mPDF('c', 'A4', '', 'arial');
            // $mpdf->SetDisplayMode('fullpage');
            // $mpdf->SetFooter($footer);
            // $mpdf->list_indent_first_level = 0; // 1 or 0 - whether to indent the first level of a list
            // // LOAD a stylesheet
            // $mpdf->WriteHTML($html_content);
            // //$mpdf->Output('doc.pdf','D');
            // $mpdf->Output('doc.pdf', 'I');
            $pdf = new Pdf(array(
                'binary' => '/usr/local/bin/wkhtmltopdf',
                'margin-top'    => 10,
                'margin-right'  => 10,
                'margin-bottom' => 10,
                'margin-left'   => 45,
                "zoom" => 1.25,
                'footer-html' => $footer
            ));

            $pdf->addPage($html_content);

            if (!$pdf->send('doc.pdf', 'I')) {
                $error = $pdf->getError();
                mail('admin@scanlink.net.au', 'Scanalytics Print Pdf ', $error);
            }
            exit;
        }
        $id = $this->request->param("id");
        $profile = Model::factory('profiles')->getprofileById($id);
        $formbuilder_ans = Model::factory('formbuilder')->get_profile_answers($id);
        $form_submission_count = 0;
        if ($formbuilder_ans) {
            $form_submission_count = count($formbuilder_ans);
        }
        $form_analytics_questions = Model::factory('formbuilder')->get_analytics_questions($id);
        if (isset($profile) && is_array($profile) && empty($profile)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        $client = Model::factory('user')->getClientById($this->userid);
        $expried_or_not = false;
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $expried_or_not = true;
        }
        if ($profile['type_id'] == '7') {
            $url = $profile['name'];
            $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
            $customqr = $customqr_option[0]['customqr_option'];
            if ($customqr == '0') {
                $this->request->redirect('dashboard/index');
                exit;
            }
        } else {
            $clienturl = Model::factory('client')->geturl($id);
            $url = URL::base() . $clienturl . "/" . $id;
        }
        $analytics = @file_get_contents(GALATECH_API . "data/getdata?key=" . $profile["analytic_key"]);
        $analytics = json_decode($analytics);
        $json_country = json_encode($analytics->country);
        $json_device = json_encode($analytics->device);
        $json_browser = json_encode($analytics->browsers);
        $slag = $eq_type[0]['slag'];
        $this->template->head->title .= " of " . $eq_type[0]['name'];
        if ($slag == "plant") {
            $slag = "index";
        }
        $param = 'dashboard/' . $slag;
        $user_id = $this->session->get('clientuserid');
        $this->template->content = View::factory('dashboard/scanalytics_print_pdf')->bind('eq_type', $eq_type)->bind('user_id', $user_id)->bind('profile', $profile)->bind('jsonCountry', $json_country)->bind('jsonDevice', $json_device)->bind('jsonBrowser', $json_browser)->bind('expried_or_not', $expried_or_not)->bind('form_submission_count', $form_submission_count)->bind('form_analytics_answers', $form_analytics_answers)->bind('form_analytics_questions', $form_analytics_questions);
    }
    public function action_scanalytics_map()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Scanalytics Map ";
        $expried_or_not = false;
        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        if ($user_data["is_sub_user"] == 1 && $user_data["access_analytics"] == 0) {
            Session::instance()->set('errormessage', 'Oops! You have no access to view analytics.');
            $this->request->redirect('dashboard');
        }
        $id = $this->request->param("id");
        $profile = Model::factory('profiles')->getprofileById($id);
        if (isset($profile) && is_array($profile) && empty($profile)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        $client = Model::factory('user')->getClientById($this->userid);
        if ($profile['type_id'] == '7') {
            $url = $profile['name'];
            $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
            $customqr = $customqr_option[0]['customqr_option'];
            if ($customqr == '0') {
                $this->request->redirect('dashboard/index');
                exit;
            }
        } else {
            $clienturl = Model::factory('client')->geturl($id);
            $url = URL::base() . $clienturl . "/" . $id;
        }
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $expried_or_not = true;
        }
        $mapAnalytics = @file_get_contents(GALATECH_API . "data/map?key=" . $profile["analytic_key"]);
        $slag = $eq_type[0]['slag'];
        $this->template->head->title .= " of " . $eq_type[0]['name'];
        if ($slag == "plant") {
            $slag = "index";
        }
        $param = 'dashboard/' . $slag;
        $user_id = $this->session->get('clientuserid');
        $this->template->content = View::factory('dashboard/scanalytics_map')->bind('eq_type', $eq_type)->bind('user_id', $user_id)->bind('profile', $profile)->bind('mapAnalytics', $mapAnalytics)->bind('expried_or_not', $expried_or_not);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param);
    }
    public function action_scanalytics_map_country()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Scanalytics Map Country ";
        $expried_or_not = false;
        $id = $this->request->param("id");
        $country = $this->request->param("clientid");
        $profile = Model::factory('profiles')->getprofileById($id);
        if (isset($profile) && is_array($profile) && empty($profile)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        $client = Model::factory('user')->getClientById($this->userid);
        if ($profile['type_id'] == '7') {
            $url = $profile['name'];
            $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
            $customqr = $customqr_option[0]['customqr_option'];
            if ($customqr == '0') {
                $this->request->redirect('dashboard/index');
                exit;
            }
        } else {
            $clienturl = Model::factory('client')->geturl($id);
            $url = URL::base() . $clienturl . "/" . $id;
        }
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $expried_or_not = true;
        }
        $url = GALATECH_API . "data/countrymap?encode=true&key=" . $profile["analytic_key"] . '&country=' . base64_encode($country);
        $mapAnalytics = @file_get_contents($url);
        $slag = $eq_type[0]['slag'];
        $this->template->head->title .= " of " . $eq_type[0]['name'];
        if ($slag == "plant") {
            $slag = "index";
        }
        $param = 'dashboard/' . $slag;
        $user_id = $this->session->get('clientuserid');
        $this->template->content = View::factory('dashboard/scanalytics_map_country')->bind('eq_type', $eq_type)->bind('user_id', $user_id)->bind('profile', $profile)->bind('country', $country)->bind('mapAnalytics', $mapAnalytics)->bind('expried_or_not', $expried_or_not);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param);
    }
    public function action_scanalytics_locations()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Scanalytics Location ";
        $expried_or_not = false;
        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        if ($user_data["is_sub_user"] == 1 && $user_data["access_analytics"] == 0) {
            Session::instance()->set('errormessage', 'Oops! You have no access to view analytics.');
            $this->request->redirect('dashboard');
        }
        $id = $this->request->param("id");
        if (isset($_POST["sortby"])) {
            $sortby = $_POST["sortby"];
        } else {
            $sortby = "";
        }
        $profile = Model::factory('profiles')->getprofileById($id);
        if (isset($profile) && is_array($profile) && empty($profile)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        $client = Model::factory('user')->getClientById($this->userid);
        if ($profile['type_id'] == '7') {
            $url = $profile['name'];
            $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
            $customqr = $customqr_option[0]['customqr_option'];
            if ($customqr == '0') {
                $this->request->redirect('dashboard/index');
                exit;
            }
        } else {
            $clienturl = Model::factory('client')->geturl($id);
            $url = URL::base() . $clienturl . "/" . $id;
        }
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $expried_or_not = true;
        }
        $apiUrl = GALATECH_API . "data/scanalytics?key=" . $profile["analytic_key"];
        $apiUrl .= '&orderby=' . $sortby;
        $mapAnalytics = @file_get_contents($apiUrl);
        $mapAnalytics = json_decode($mapAnalytics);
        $slag = $eq_type[0]['slag'];
        $this->template->head->title .= " of " . $eq_type[0]['name'];
        if ($slag == "plant") {
            $slag = "index";
        }
        $param = 'dashboard/' . $slag;
        $user_id = $this->session->get('clientuserid');
        $this->template->content = View::factory('dashboard/scanalytics_locations')->bind('eq_type', $eq_type)->bind('user_id', $user_id)->bind('profile', $profile)->bind('orderby', $sortby)->bind('mapAnalytics', $mapAnalytics)->bind('expried_or_not', $expried_or_not);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param);
    }
    public function action_scanalytics_showlocation()
    {
        // set title tag
        $this->template->head->title = "Scanalytics Location ";
        $expried_or_not = false;
        $id = $this->request->param("id");
        $analytic_id = $this->request->param("clientid");
        $profile = Model::factory('profiles')->getprofileById($id);
        if (isset($profile) && is_array($profile) && empty($profile)) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        $client = Model::factory('user')->getClientById($this->userid);
        if ($profile['type_id'] == '7') {
            $url = $profile['name'];
            $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
            $customqr = $customqr_option[0]['customqr_option'];
            if ($customqr == '0') {
                $this->request->redirect('dashboard/index');
                exit;
            }
        } else {
            $clienturl = Model::factory('client')->geturl($id);
            $url = URL::base() . $clienturl . "/" . $id;
        }
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $expried_or_not = true;
        }
        $analyticsArr = @file_get_contents(GALATECH_API . "data/findwpanalyticsbyid?key=" . $profile["analytic_key"] . '&id=' . $analytic_id);
        $location = json_decode($analyticsArr);
        if (is_object($location)) {
            $location = get_object_vars($location);
        }
        $location = json_encode($location);
        $slag = $eq_type[0]['slag'];
        $this->template->head->title .= " of " . $eq_type[0]['name'];
        if ($slag == "plant") {
            $slag = "index";
        }
        $param = 'dashboard/' . $slag;
        $user_id = $this->session->get('clientuserid');
        $this->template->content = View::factory('dashboard/scanalytics_showlocation')->bind('eq_type', $eq_type)->bind('user_id', $user_id)->bind('profile', $profile)->bind('location', $location)->bind('expried_or_not', $expried_or_not);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param);
    }
    public function action_scanalytics_export()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Export Scanalytics ";
        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        if ($user_data["is_sub_user"] == 1 && $user_data["access_analytics"] == 0) {
            Session::instance()->set('errormessage', 'Oops! You have no access to view analytics.');
            $this->request->redirect('dashboard');
        }
        $id = $this->request->param("id");
        if ($this->request->param("clientid")) {
            $sortby = $this->request->param("clientid");
        } else {
            $sortby = "";
        }
        $profile = Model::factory('profiles')->getprofileById($id);
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        $client = Model::factory('user')->getClientById($this->userid);
        if ($profile['type_id'] == '7') {
            $url = $profile['name'];
            $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
            $customqr = $customqr_option[0]['customqr_option'];
            if ($customqr == '0') {
                $this->request->redirect('dashboard/index');
                exit;
            }
        } else {
            $clienturl = Model::factory('client')->geturl($id);
            $url = URL::base() . $clienturl . "/" . $id;
        }
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s"))) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        $apiUrl = GALATECH_API . "data/scanalytics?key=" . $profile["analytic_key"];
        $apiUrl .= '&orderby=' . $sortby;
        $mapAnalytics = @file_get_contents($apiUrl);
        $locationlist = json_decode($mapAnalytics);
        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require "PHPExcel/PHPExcel.php";
        $workbook = new PHPExcel();
        $sheet = $workbook->getActiveSheet();
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Date Time');
        $sheet->setCellValue('C1', 'Location');
        $sheet->setCellValue('D1', 'Latitude');
        $sheet->setCellValue('E1', 'Longitude');
        for ($b = 0; $b < sizeof($locationlist); $b++) {
            $sheet->setCellValue("A" . ($b + 2), $b + 1);
            $sheet->setCellValue("B" . ($b + 2), $locationlist[$b]->created_at);
            $sheet->setCellValue("C" . ($b + 2), $locationlist[$b]->city_name);
            $sheet->setCellValue("D" . ($b + 2), $locationlist[$b]->latitude);
            $sheet->setCellValue("E" . ($b + 2), $locationlist[$b]->longitude);
        }
        $writer = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
        $writer->save('php://output');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="excelreport.xls"');
        header('Cache-Control: max-age=0');
        exit;
    }
    public function action_exportScanalytics()
    {
        // set title tag
        $this->template->head->title = "Export Scanalytics ";
        $data = $this->request->param("id");
        $data = explode("||", $data);
        $id = $data[0];
        $active = $data[1];
        $clienturl = Model::factory('client')->geturl($id);
        $url = URL::base() . $clienturl . "/" . $id;
        require_once "classes/shortURL.php";
        $short_url = shortenUrl($url);
        $analytics = CreateAnalytics($short_url['id']);
        $profile = Model::factory('profiles')->getprofileById($id);
        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s"))) {
            $this->request->redirect('dashboard/index');
            exit;
        }
        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require "PHPExcel/PHPExcel.php";
        $workbook = new PHPExcel();
        $sheet = $workbook->getActiveSheet();
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->setCellValue('A1', 'ScanLink, Scanalytics for ' . $analytics['longUrl']);
        $sheet->getRowDimension(1)->setRowHeight(45);
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        if ($active == 0) {
            $key = "allTime";
            $sheet->setCellValue('A2', 'All Time');
        } else if ($active == 1) {
            $key = "month";
            $sheet->setCellValue('A2', 'Monthly');
        } else if ($active == 2) {
            $key = "week";
            $sheet->setCellValue('A2', 'Weekly');
        } else if ($active == 3) {
            $key = "day";
            $sheet->setCellValue('A2', 'Daily');
        }
        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $sheet->setCellValue('A3', 'countries');
        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A4', 'Country');
        $sheet->setCellValue('B4', 'Click Count');
        $rowC = 5;
        foreach ($analytics['analytics'][$key]['countries'] as $country) {
            $sheet->setCellValue('A' . $rowC, $country['id']);
            $sheet->setCellValue('B' . $rowC, $country['count']);
            $rowC++;
        }
        $sheet->setCellValue('D3', 'browsers');
        $sheet->mergeCells('D3:E3');
        $sheet->setCellValue('D4', 'Browser');
        $sheet->setCellValue('E4', 'Click Count');
        $rowB = 5;
        foreach ($analytics['analytics'][$key]['browsers'] as $browser) {
            $sheet->setCellValue('D' . $rowB, $browser['id']);
            $sheet->setCellValue('E' . $rowB, $browser['count']);
            $rowB++;
        }
        $sheet->setCellValue('G3', 'platforms');
        $sheet->mergeCells('G3:H3');
        $sheet->setCellValue('G4', 'Platform');
        $sheet->setCellValue('H4', 'Click Count');
        $rowP = 5;
        foreach ($analytics['analytics'][$key]['platforms'] as $platform) {
            $sheet->setCellValue('G' . $rowP, $platform['id']);
            $sheet->setCellValue('H' . $rowP, $platform['count']);
            $rowP++;
        }
        $rowh = max($rowB, $rowC, $rowP);
        $sheet->getStyle('A2:H' . $rowh)->applyFromArray($styleArray);
        unset($styleArray);
        $writer = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
        $writer->save('scanalytics.xls');
        $filename = "excelreport.xls";
        $contents = file_get_contents('scanalytics.xls');
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $contents;
        exit;
    }
    public function action_saveDocFile()
    {
        if (isset($_REQUEST['profile_id_document']) && $_REQUEST['profile_id_document'] != '' && isset($_REQUEST['type_id']) && $_REQUEST['type_id'] != '') {
            $type_id = $_REQUEST['type_id'];
            $profile_id = $_REQUEST['profile_id_document'];
            // set title tag
            $this->template->head->title = "Save Document ";
            include_once "fileuploader/classes/easy_upload/upload_class.php"; //classes is the map where the class file is stored
            $upload = new file_upload();
            $upload = new file_upload();
            /* $upload->upload_dir = 'images/tmp_files/'; */
            $upload->upload_dir = 'images/doc/';
            $upload->extensions = array('.png', '.jpg', '.jpeg', '.gif', '.pdf', '.doc', '.docx'); // specify the allowed extensions here
            $upload->rename_file = true;
            if (!empty($_FILES)) {
                $upload->the_temp_file = $_FILES['userfile']['tmp_name'];
                $upload->the_file = $_FILES['userfile']['name'];
                $upload->http_error = $_FILES['userfile']['error'];
                $upload->filename = trim($_POST['filename']);
                $upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
                if ($upload->upload()) {
                    $doc_id = Model::factory('documents')->saveTempDocument($this->userid, $_POST['filename'], $_POST['btn_color'], $upload->upload_dir . $upload->file_copy, $_POST['txt_align'], $profile_id, 1);
                    //Model::factory('profiles')->updateProfileIdForUploadData($profile_id, $type_id);
                    $tmp_files = $this->session->get('tmp_files', false);
                    if (!$tmp_files) {
                        $tmp_files = array();
                    }
                    array_push($tmp_files, $doc_id[0]);
                    $this->session->set('tmp_files', $tmp_files);
                    echo '<br/><div id="status">success ' . $upload->file_copy . '</div>';
                    exit;
                    //               echo '<div id="message">' . $upload->file_copy . ' Successfully Uploaded</div>';
                    //return the upload file
                    //             echo '<div id="uploadedfile">' . $upload->file_copy . '</div>';

                } else {
                    echo '<br/><div id="status">' . $upload->show_error_string() . '</div>';
                    exit;
                    //echo '<div id="message">' . $upload->show_error_string() . '</div>';
                    //echo '<div id="uploadedfile">' . $upload->show_error_string() . '</div>';

                }
            } else {
                echo '<br/><div id="status">' . $upload->show_error_string() . '</div>';
                exit;
                //echo '<div id="message">' . $upload->show_error_string() . '</div>';
                //echo '<div id="uploadedfile">' . $upload->show_error_string() . '</div>';

            }
        }
    }
    public function action_saveDocOrder()
    {
        $order = $_REQUEST['order'];
        Model::factory('documents')->saveDocumentOrder($order);
        exit;
    }
    public function action_savePictureFile()
    {
        if (isset($_REQUEST['profile_id_picture']) && $_REQUEST['profile_id_picture'] != '' && isset($_REQUEST['type_id']) && $_REQUEST['type_id'] != '') {
            $type_id = $_REQUEST['type_id'];
            $txt_footer = $_REQUEST["txt_footer"];
            $profile_id = $_REQUEST['profile_id_picture'];
            // set title tag
            $this->template->head->title = "Save Picture ";
            include_once "fileuploader/classes/easy_upload/upload_class.php"; //classes is the map where the class file is stored
            $upload = new file_upload();
            //$upload->upload_dir = 'images/tmp_files/';
            $upload->upload_dir = 'images/picture/';
            $upload->extensions = array('.png', '.jpg', '.jpeg', '.gif'); // specify the allowed extensions here
            $upload->rename_file = true;
            if (!empty($_FILES)) {
                $upload->the_temp_file = $_FILES['userfile']['tmp_name'];
                $upload->the_file = $_FILES['userfile']['name'];
                $upload->http_error = $_FILES['userfile']['error'];
                $upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
                if ($upload->upload()) {
                    $picture_id = Model::factory('picture')->saveTempPictures($this->userid, $upload->upload_dir . $upload->file_copy, $profile_id, $txt_footer, 1);
                    //Model::factory('profiles')->updateProfileIdForUploadData($profile_id, $type_id);
                    $tmp_files = $this->session->get('tmp_files', false);
                    if (!$tmp_files) {
                        $tmp_files = array();
                    }
                    array_push($tmp_files, $picture_id[0]);
                    $this->session->set('tmp_files', $tmp_files);
                    echo '<div id="status">success ' . $upload->file_copy . '</div>';
                    exit;
                    //echo '<div id="message">' . $upload->file_copy . ' Successfully Uploaded</div>';
                    //return the upload file
                    //echo '<div id="uploadedfile">' . $upload->file_copy . '</div>';

                } else {
                    echo '<div id="status">' . $upload->show_error_string() . '</div>';
                    exit;
                    //echo '<div id="message">' . $upload->show_error_string() . '</div>';
                    //echo '<div id="uploadedfile">' . $upload->show_error_string() . '</div>';

                }
            }
        }
    }
    public function action_savePictureFile_extra()
    { //used in extra picture tile in exhibit profile
        if (isset($_REQUEST['profile_id_picture']) && $_REQUEST['profile_id_picture'] != '' && isset($_REQUEST['type_id']) && $_REQUEST['type_id'] != '') {
            $type_id = $_REQUEST['type_id'];
            $txt_footer = $_REQUEST["txt_footer"];
            $profile_id = $_REQUEST['profile_id_picture'];
            // set title tag
            $this->template->head->title = "Save Picture ";
            include_once "fileuploader/classes/easy_upload/upload_class.php"; //classes is the map where the class file is stored
            $upload = new file_upload();
            //$upload->upload_dir = 'images/tmp_files/';
            $upload->upload_dir = 'images/picture/';
            $upload->extensions = array('.png', '.jpg', '.jpeg', '.gif'); // specify the allowed extensions here
            $upload->rename_file = true;
            if (!empty($_FILES)) {
                $upload->the_temp_file = $_FILES['userfile']['tmp_name'];
                $upload->the_file = $_FILES['userfile']['name'];
                $upload->http_error = $_FILES['userfile']['error'];
                $upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
                if ($upload->upload()) {
                    $picture_id = Model::factory('picture')->saveTempPictures_extra($this->userid, $upload->upload_dir . $upload->file_copy, $profile_id, $txt_footer, 1);
                    //Model::factory('profiles')->updateProfileIdForUploadData($profile_id, $type_id);
                    $tmp_files = $this->session->get('tmp_files', false);
                    if (!$tmp_files) {
                        $tmp_files = array();
                    }
                    array_push($tmp_files, $picture_id[0]);
                    $this->session->set('tmp_files', $tmp_files);
                    echo '<div id="status">success ' . $upload->file_copy . '</div>';
                    exit;
                    //echo '<div id="message">' . $upload->file_copy . ' Successfully Uploaded</div>';
                    //return the upload file
                    //echo '<div id="uploadedfile">' . $upload->file_copy . '</div>';

                } else {
                    echo '<div id="status">' . $upload->show_error_string() . '</div>';
                    exit;
                    //echo '<div id="message">' . $upload->show_error_string() . '</div>';
                    //echo '<div id="uploadedfile">' . $upload->show_error_string() . '</div>';

                }
            }
        }
    }
    public function action_savePictureFile_voc()
    { //used in profile picture tile in VOC profile
        if (isset($_REQUEST['profile_id_picture']) && $_REQUEST['profile_id_picture'] != '' && isset($_REQUEST['type_id']) && $_REQUEST['type_id'] != '') {
            $type_id = $_REQUEST['type_id'];
            $profile_id = $_REQUEST['profile_id_picture'];
            // set title tag
            $this->template->head->title = "Save Picture ";
            include_once "fileuploader/classes/easy_upload/upload_class.php"; //classes is the map where the class file is stored
            $upload = new file_upload();
            //$upload->upload_dir = 'images/tmp_files/';
            $upload->upload_dir = 'images/picture/';
            $upload->extensions = array('.png', '.jpg', '.jpeg', '.gif'); // specify the allowed extensions here
            $upload->rename_file = true;
            if (!empty($_FILES)) {
                $upload->the_temp_file = $_FILES['userfile']['tmp_name'];
                $upload->the_file = $_FILES['userfile']['name'];
                $upload->http_error = $_FILES['userfile']['error'];
                $upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
                if ($upload->upload()) {
                    $picture_id = Model::factory('picture')->saveTempPictures_voc($this->userid, $upload->upload_dir . $upload->file_copy, $profile_id, 1);
                    //Model::factory('profiles')->updateProfileIdForUploadData($profile_id, $type_id);
                    $tmp_files = $this->session->get('tmp_files', false);
                    if (!$tmp_files) {
                        $tmp_files = array();
                    }
                    array_push($tmp_files, $picture_id[0]);
                    $this->session->set('tmp_files', $tmp_files);
                    echo '<div id="status">success ' . $upload->file_copy . '</div>';
                    exit;
                    //echo '<div id="message">' . $upload->file_copy . ' Successfully Uploaded</div>';
                    //return the upload file
                    //echo '<div id="uploadedfile">' . $upload->file_copy . '</div>';

                } else {
                    echo '<div id="status">' . $upload->show_error_string() . '</div>';
                    exit;
                    //echo '<div id="message">' . $upload->show_error_string() . '</div>';
                    //echo '<div id="uploadedfile">' . $upload->show_error_string() . '</div>';

                }
            }
        }
    }
    public function action_saveLogoFile()
    {
        if (isset($_REQUEST['profile_id_logo']) && $_REQUEST['profile_id_logo'] != '' && isset($_REQUEST['type_id']) && $_REQUEST['type_id'] != '') {
            $type_id = $_REQUEST['type_id'];
            $profile_id = $_REQUEST['profile_id_logo'];
            // set title tag
            $this->template->head->title = "Save Logo  ";
            include_once "fileuploader/classes/easy_upload/upload_class.php"; //classes is the map where the class file is stored
            $upload = new file_upload();
            //$upload->upload_dir = 'images/tmp_files/';
            $upload->upload_dir = 'images/logo/';
            $upload->extensions = array('.png', '.jpg', '.jpeg', '.gif'); // specify the allowed extensions here
            $upload->rename_file = true;
            if (!empty($_FILES)) {
                $upload->the_temp_file = $_FILES['userfile']['tmp_name'];
                $upload->the_file = $_FILES['userfile']['name'];
                $upload->http_error = $_FILES['userfile']['error'];
                $upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
                if ($upload->upload()) {
                    $logo_id = Model::factory('logo')->saveTempLogo($this->userid, $upload->upload_dir . $upload->file_copy, $profile_id, 1);
                    //Model::factory('profiles')->updateProfileIdForUploadData($profile_id, $type_id);
                    $tmp_files = $this->session->get('tmp_files', false);
                    if (!$tmp_files) {
                        $tmp_files = array();
                    }
                    array_push($tmp_files, $logo_id[0]);
                    $this->session->set('tmp_files', $tmp_files);
                    echo '<div id="status">success ' . $upload->file_copy . ' </div>';
                    exit;
                    //        echo '<div id="message">' . $upload->file_copy . ' Successfully Uploaded</div>';
                    //return the upload file
                    //echo '<div id="uploadedfile">' . $upload->file_copy . '</div>';

                } else {
                    echo '<div id="status">' . $upload->show_error_string() . '</div>';
                    exit;
                    //echo '<div id="message">' . $upload->show_error_string() . '</div>';
                    //echo '<div id="uploadedfile">' . $upload->show_error_string() . '</div>';

                }
            }
        }
    }
    public function action_saveLogoFile_extra()
    {
        if (isset($_REQUEST['profile_id_logo']) && $_REQUEST['profile_id_logo'] != '' && isset($_REQUEST['type_id']) && $_REQUEST['type_id'] != '') {
            $type_id = $_REQUEST['type_id'];
            $profile_id = $_REQUEST['profile_id_logo'];
            // set title tag
            $this->template->head->title = "Save Logo  ";
            include_once "fileuploader/classes/easy_upload/upload_class.php"; //classes is the map where the class file is stored
            $upload = new file_upload();
            //$upload->upload_dir = 'images/tmp_files/';
            $upload->upload_dir = 'images/logo/';
            $upload->extensions = array('.png', '.jpg', '.jpeg', '.gif'); // specify the allowed extensions here
            $upload->rename_file = true;
            if (!empty($_FILES)) {
                $upload->the_temp_file = $_FILES['userfile']['tmp_name'];
                $upload->the_file = $_FILES['userfile']['name'];
                $upload->http_error = $_FILES['userfile']['error'];
                $upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
                if ($upload->upload()) {
                    $logo_id = Model::factory('logo')->saveTempLogo_extra($this->userid, $upload->upload_dir . $upload->file_copy, $profile_id, 1);
                    //Model::factory('profiles')->updateProfileIdForUploadData($profile_id, $type_id);
                    $tmp_files = $this->session->get('tmp_files', false);
                    if (!$tmp_files) {
                        $tmp_files = array();
                    }
                    array_push($tmp_files, $logo_id[0]);
                    $this->session->set('tmp_files', $tmp_files);
                    echo '<div id="status">success ' . $upload->file_copy . ' </div>';
                    exit;
                    //        echo '<div id="message">' . $upload->file_copy . ' Successfully Uploaded</div>';
                    //return the upload file
                    //echo '<div id="uploadedfile">' . $upload->file_copy . '</div>';

                } else {
                    echo '<div id="status">' . $upload->show_error_string() . '</div>';
                    exit;
                    //echo '<div id="message">' . $upload->show_error_string() . '</div>';
                    //echo '<div id="uploadedfile">' . $upload->show_error_string() . '</div>';

                }
            }
        }
    }
    public function action_saveLogoFile_footer()
    {
        include_once "fileuploader/classes/easy_upload/upload_class.php"; //classes is the map where the class file is stored
        $upload = new file_upload();
        $upload->upload_dir = 'images/logo/';
        $upload->extensions = array('.png', '.jpg', '.jpeg', '.gif'); // specify the allowed extensions here
        $upload->rename_file = true;
        if (!empty($_FILES)) {
            // Check file size
            if ($_FILES["userfile"]["size"] > 1000000) {
                echo '<div id="status">Sorry, your file is large than 1MB.</div>';
                exit;
            }
            $upload->the_temp_file = $_FILES['userfile']['tmp_name'];
            $upload->the_file = $_FILES['userfile']['name'];
            $upload->http_error = $_FILES['userfile']['error'];
            $upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
            if ($upload->upload()) {
                $this->session->set('logo_for_footer_temp', $upload->file_copy);
                echo '<div id="status">success ' . $upload->file_copy . ' </div>';
                exit;
            } else {
                echo '<div id="status">' . $upload->show_error_string() . '</div>';
                exit;
            }
        }
    }
    public function action_saveTempPictureFile()
    {
        if (isset($_REQUEST['profile_id']) && $_REQUEST['profile_id'] != '') {
            $profile_id = $_REQUEST['profile_id'];
            include_once "fileuploader/classes/easy_upload/upload_class.php"; //classes is the map where the class file is stored
            $upload = new file_upload();
            $upload->upload_dir = 'images/form_builder_uploaded_images/';
            $upload->extensions = array('.png', '.jpg', '.jpeg', '.gif'); // specify the allowed extensions here
            $upload->rename_file = true;
            if (!empty($_FILES)) {
                $upload->the_temp_file = $_FILES['userfile']['tmp_name'];
                $upload->the_file = $_FILES['userfile']['name'];
                $upload->http_error = $_FILES['userfile']['error'];
                $upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
                if ($upload->upload()) {
                    $this->session->set('form_builder_image_temp', $upload->file_copy);
                    echo '<div id="status">success ' . $upload->file_copy . '</div>';
                    exit;
                    //        echo '<div id="message">' . $upload->file_copy . ' Successfully Uploaded</div>';
                    //return the upload file
                    //echo '<div id="uploadedfile">' . $upload->file_copy . '</div>';

                } else {
                    echo '<div id="status">' . $upload->show_error_string() . '</div>';
                    exit;
                    //echo '<div id="message">' . $upload->show_error_string() . '</div>';
                    //echo '<div id="uploadedfile">' . $upload->show_error_string() . '</div>';

                }
            }
        }
    }
    public function action_datacollection_export()
    {
        // set title tag
        $this->template->head->title = "Vistior Log  Export";
        $id = $this->request->param("id");
        if ($this->request->param("clientid")) {
            $sortby = $this->request->param("clientid");
        } else {
            $sortby = "";
        }
        $user_info = Model::factory('userinfo')->getUserinfoByProfileId($id);
        $contacts = Model::factory('contacts')->getContactsById($id);
        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require "PHPExcel/PHPExcel.php";
        $workbook = new PHPExcel();
        $sheet = $workbook->getActiveSheet();
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->setCellValue('A1', 'ID');
        //$sheet->setCellValue('B1', 'Profile');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Name');
        $sheet->setCellValue('D1', 'Surname');
        $sheet->setCellValue('E1', 'Mobile');
        $sheet->setCellValue('F1', 'Email');
        for ($b = 0; $b < sizeof($contacts); $b++) {
            $sheet->setCellValue("A" . ($b + 2), $id);
            //$sheet->setCellValue("B" . ($b + 2), $profile["name"]);
            $sheet->setCellValue("B" . ($b + 2), date("d/m/Y H:i", strtotime($contacts[$b]["created_at"])));
            $sheet->setCellValue("C" . ($b + 2), $contacts[$b]["name"]);
            $sheet->setCellValue("D" . ($b + 2), $contacts[$b]["surname"]);
            $sheet->setCellValue("E" . ($b + 2), $contacts[$b]["mobile"]);
            $sheet->setCellValue("F" . ($b + 2), $contacts[$b]["email"]);
        }
        $writer = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
        $writer->save('php://output');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="data_collection_excel.xls"');
        header('Cache-Control: max-age=0');
        exit;
    }
    public function action_code()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "URL Link Dashboard";
        // to display pop up after registration
        $user = Model::factory('user')->getUserByID($this->userid);
        if (isset($user['pop_up_register']) && $user['pop_up_register'] == '0') {
            $datafill = array();
            $datafill['pop_up_register'] = '1';
            Model::factory('user')->updateuser($datafill, $this->userid);
        }
        if (isset($user['pop_up_purchase']) && $user['pop_up_purchase'] == '1') {
            $datafill = array();
            $datafill['pop_up_purchase'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $purchase_popup_show = true;
        } else {
            $purchase_popup_show = false;
        }
        if (isset($user['pop_up_orderlabel']) && $user['pop_up_orderlabel'] == '1') {
            $datafill = array();
            $datafill['pop_up_orderlabel'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $orderlabel_popup_show = true;
        } else {
            $orderlabel_popup_show = false;
        }
        $pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));
        $SuccessMessage = "";
        if ($this->session->get("SuccessMessage") != "") {
            $SuccessMessage = $this->session->get("SuccessMessage");
            $this->session->delete("SuccessMessage");
        }
        if ($this->session->get("successmessage") != "") {
            $message = $this->session->get("successmessage");
            if ($message != '') {
                $SuccessMessage = $message;
            }
            $this->session->delete("successmessage");
        }
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('code');
        $typeid = $typeidArr[0]['id'];
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getProfilesByType($typeid, $client['id']);
        $param = 'dashboard/code';
        $is_sub_user = $this->session->get('is_sub_user');
        // Default Content template
        $this->template->content = View::factory('dashboard/code')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('is_sub_user', $is_sub_user)->bind('user', $user)->bind('errormessage', $errormessage)->bind('SuccessMessage', $SuccessMessage)->bind('pending_codes', $pending_codes['0']['pending_codes'])->bind('purchase_popup_show', $purchase_popup_show)->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $colorcode_display = true;
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param)->bind('pending_codes', $pending_codes['0']['pending_codes'])->bind('colorcode_display', $colorcode_display);
        $this->template->content->code_not_available = View::factory('template/code_not_available')->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $this->template->content->orderlabel_popup = View::factory('template/orderlabel')->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $this->template->content->purchase_code_popup = View::factory('template/purchase_code')->bind('purchase_popup_show', $purchase_popup_show);
        $this->template->content->expired_popup = View::factory('template/expried');
    }
    public function action_preview()
    {
        // set title tag
        $this->template->head->title = "Preview";
        $this->template->head = View::factory('template/preview_head');
        $this->template->head->stylesheets = array(array('href' => 'styles/preview_style.css', 'media' => 'screen'));
        $this->template->head->javascripts = array();
        $this->template->headers = "";
        $this->template->footer = "";
        $profile_type = $this->request->param("id");
        // Default Content template
        $this->template->content = View::factory('dashboard/preview')->bind('profile_type', $profile_type);
    }
    public function action_mastercode()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        // set title tag
        $this->template->head->title = "Master Code";
        // to display pop up after registration
        $user = Model::factory('user')->getUserByID($this->userid);
        if (isset($user['pop_up_register']) && $user['pop_up_register'] == '0') {
            $datafill = array();
            $datafill['pop_up_register'] = '1';
            Model::factory('user')->updateuser($datafill, $this->userid);
        }
        if (isset($user['pop_up_purchase']) && $user['pop_up_purchase'] == '1') {
            $datafill = array();
            $datafill['pop_up_purchase'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $purchase_popup_show = true;
        } else {
            $purchase_popup_show = false;
        }
        if (isset($user['pop_up_renew']) && $user['pop_up_renew'] == '1') {
            $datafill = array();
            $datafill['pop_up_renew'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $renew_popup_show = true;
        } else {
            $renew_popup_show = false;
        }
        if (isset($user['pop_up_orderlabel']) && $user['pop_up_orderlabel'] == '1') {
            $datafill = array();
            $datafill['pop_up_orderlabel'] = '0';
            Model::factory('user')->updateuser($datafill, $this->userid);
            $orderlabel_popup_show = true;
        } else {
            $orderlabel_popup_show = false;
        }
        $pending_codes = Model::factory('code')->getPendingCodes($this->session->get('client_id'));
        $SuccessMessage = "";
        if ($this->session->get("SuccessMessage") != "") {
            $SuccessMessage = $this->session->get("SuccessMessage");
            $this->session->delete("SuccessMessage");
        }
        if ($this->session->get("successmessage") != "") {
            $message = $this->session->get("successmessage");
            if ($message != '') {
                $SuccessMessage = $message;
            }
            $this->session->delete("successmessage");
        }
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $typeidArr = Model::factory('dashboard')->getTypeBySlag('code');
        $typeid = $typeidArr[0]['id'];
        $client = Model::factory('user')->getClientById($this->userid);
        $profile = Model::factory('dashboard')->getAllProfiles($client['id']);
        $param = 'dashboard/mastercode';
        // Default Content template
        $this->template->content = View::factory('dashboard/mastercode')->bind('profile', $profile)->bind('client_id', $client['id'])->bind('qr', $qr)->bind('errormessage', $errormessage)->bind('SuccessMessage', $SuccessMessage)->bind('pending_codes', $pending_codes['0']['pending_codes'])->bind('purchase_popup_show', $purchase_popup_show)->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $customqr_option = Model::factory('client')->isCustomqrEnabledToClient($client['id']);
        $customqr = $customqr_option[0]['customqr_option'];
        $colorcode_display = true;
        $this->template->content->innermenu = View::factory('template/innermenu')->bind('equiptypes', $this->types)->bind('customqr', $customqr)->bind('activemenu', $param)->bind('pending_codes', $pending_codes['0']['pending_codes'])->bind('colorcode_display', $colorcode_display);
        $this->template->content->code_not_available = View::factory('template/code_not_available')->bind('pending_codes', $pending_codes['0']['pending_codes']);
        $this->template->content->orderlabel_popup = View::factory('template/orderlabel')->bind('orderlabel_popup_show', $orderlabel_popup_show);
        $this->template->content->purchase_code_popup = View::factory('template/purchase_code')->bind('purchase_popup_show', $purchase_popup_show);
        $this->template->content->renew_code_popup = View::factory('template/renew_code')->bind('renew_popup_show', $renew_popup_show);
        $this->template->content->expired_popup = View::factory('template/expried');
    }
    /*   * *
     * *** After order label order summary page display
     * * */
    public function action_ordersummary()
    {
        // set title tag
        $this->template->head->title = "Order Summary Of order label";
        /*     * *  get session data ** */
        $quantitydata = $this->session->get("quantitydata");
        /*     * * check session data exist  ** */
        if (isset($quantitydata) && !empty($quantitydata)) {
            /*       * * check profile id  exist  ** */
            if ($quantitydata['profile_id'] != '' && $quantitydata['profile_id'] != '0') {
                /*         * * check quantity set    ** */
                if (($quantitydata['quantity_small'] != '' && $quantitydata['quantity_small'] != '0') || ($quantitydata['quantity_big'] != '' && $quantitydata['quantity_big'] != '0')) {
                    /*           * * if quantity null then assign 0 ** */
                    if ($quantitydata['quantity_small'] == '') {
                        $quantitydata['quantity_small'] = 0;
                    }
                    if ($quantitydata['quantity_big'] == '') {
                        $quantitydata['quantity_big'] = 0;
                    }
                    //if post request than insert into database
                    if ($this->request->method() === Request::POST):
                        // get profile information
                        $profile = Model::factory('profiles')->getProfileById($quantitydata['profile_id']);
                        // get type information
                        $eq_type = Model::factory('dashboard')->getTypeById($profile['type_id']);
                        // get user information
                        $userdata = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
                        // asign data to make order
                        $data = array();
                        $data["client_id"] = $profile["client_id"];
                        $data['qty_small'] = $quantitydata['quantity_small'];
                        $data['qty_large'] = $quantitydata['quantity_big'];
                        $data['user_id'] = $this->session->get('clientuserid');
                        $data['profile_id'] = $quantitydata['profile_id'];
                        $data['price_small'] = $this->small_price;
                        $data['price_large'] = $this->big_price;
                        $data['status'] = "New";
                        $data['first_name'] = $userdata['first_name'];
                        $data['last_name'] = $userdata['last_name'];
                        $data['address1'] = $userdata['billing_address'];
                        $data['address2'] = $userdata['billing_address'];
                        $data['city'] = $userdata['town'];
                        $data['state'] = $userdata['town'];
                        $data['zip'] = $userdata['postal_code'];
                        $data['country'] = $userdata['town'];
                        $data['email'] = $userdata['email'];
                        $data['contact'] = $userdata['phone'];
                        $order_id = Model::factory("order")->placeTempOrder($data);
                        $datafill = array();
                        $datafill['pop_up_orderlabel'] = '1';
                        Model::factory('user')->updateuser($datafill, $userdata['id']);
                        // send mail  START
                        include_once "mailsend.php";
                        $adminemail = Model::factory('settings')->getValueByTitle('contact_email');
                        $adminemail = "admin@scanlink.net.au";
                        $baseurl = URL::base();
                        //send mail to user START
                        orderlabel_mail_client($adminemail, $baseurl, $data);
                        //send mail to user END
                        //send mail to admin START
                        orderlabel_mail_admin($adminemail, $baseurl, $data);
                        //send mail to admin END
                        // send mail  END
                        // after payment information of order set
                        $data = array();
                        $data['transaction_id'] = "After new order set";
                        $data['status'] = "Paid";
                        Model::factory("order")->setTransactionId($data, $order_id);
                        // redirect to proper
                        $slag = $eq_type[0]['slag'];
                        if ($slag == 'plant') {
                            $slag = "index";
                        }
                        $this->session->delete("quantitydata");
                        //$this->session->set("successmessage", "Successfully add order label for " . $eq_type[0]['name'] . " .");
                        $this->request->redirect('dashboard/' . $slag);
                        exit;
                    endif;
                    // Default Content template
                    $this->template->content = View::factory('dashboard/ordersummary')->bind('quantity_small', $quantitydata['quantity_small'])->bind('quantity_big', $quantitydata['quantity_big'])->bind('small_price', $this->small_price)->bind('big_price', $this->big_price)->bind('postage', $this->postage);
                } else {
                    $errormessage = "Select quantity.";
                    $this->request->redirect('dashboard/orderLabel/' . $quantitydata['profile_id']);
                    exit;
                }
            } else {
                $this->session->set("errormessage", "Please select profile.");
                $this->request->redirect('dashboard');
                exit;
            }
        } else {
            $this->session->set("errormessage", "Please select profile.");
            $this->request->redirect('dashboard');
            exit;
        }
    }
    public function action_deleteweblink()
    {
        $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : '';
        $weblink_id = isset($_POST['weblinkid']) ? $_POST['weblinkid'] : '';
        $returnarray = array();
        if ($profile_id != '' && $weblink_id != '') {
            // get profile data
            $profile = Model::factory('profiles')->getProfileById($profile_id);
            if (is_array($profile) && !empty($profile)) {
                // get weblink data
                $weblink = Model::factory('weblink')->getWeblinkById($weblink_id);
                if (is_array($weblink) && !empty($weblink)) {
                    if ($weblink['profile_id'] == $profile_id) {
                        Model::factory('weblink')->deleteWeblinkById($weblink_id);
                        $returnarray['error'] = 2;
                        $returnarray['message'] = "Web link deleted successfully.";
                    } else {
                        $returnarray['error'] = 1;
                        $returnarray['message'] = "You can not delete other profile web link.";
                    }
                } else {
                    $returnarray['error'] = 1;
                    $returnarray['message'] = "You can not delete web link.";
                }
            } else {
                $returnarray['error'] = 1;
                $returnarray['message'] = "You can not delete other user profile web link.";
            }
        } else {
            $returnarray['error'] = 1;
            $returnarray['message'] = "You can not delete web link.";
        }
        echo json_encode($returnarray);
        exit;
    }
    public function action_deletecontact()
    {
        $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : '';
        $contact_id = isset($_POST['contactid']) ? $_POST['contactid'] : '';
        $returnarray = array();
        if ($profile_id != '' && $contact_id != '') {
            // get profile data
            $profile = Model::factory('profiles')->getProfileById($profile_id);
            if (is_array($profile) && !empty($profile)) {
                // get weblink data
                $contact = Model::factory('profilecontact')->getContactById($contact_id);
                if (is_array($contact) && !empty($contact)) {
                    if ($contact['profile_id'] == $profile_id) {
                        Model::factory('profilecontact')->deleteContactById($contact_id);
                        $returnarray['error'] = 2;
                        $returnarray['message'] = "Contact deleted successfully.";
                    } else {
                        $returnarray['error'] = 1;
                        $returnarray['message'] = "You can not delete other profile contact.";
                    }
                } else {
                    $returnarray['error'] = 1;
                    $returnarray['message'] = "You can not delete contact.";
                }
            } else {
                $returnarray['error'] = 1;
                $returnarray['message'] = "You can not delete other user profile contact.";
            }
        } else {
            $returnarray['error'] = 1;
            $returnarray['message'] = "You can not delete contact.";
        }
        echo json_encode($returnarray);
        exit;
    }
    public function action_deletelogo()
    {
        $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : '';
        $returnarray = array();
        if ($profile_id != '') {
            // get profile data
            $profile = Model::factory('profiles')->getProfileById($profile_id);
            if (is_array($profile) && !empty($profile)) {
                // get weblink data
                $logo = Model::factory('logo')->getLogoById($profile_id);
                if (is_array($logo) && !empty($logo)) {
                    if ($logo['profile_id'] == $profile_id) {
                        if (unlink(realpath($logo["logo_name"]))) {
                        }
                        Model::factory('logo')->deleteLogoByProfileId($profile_id);
                        $returnarray['error'] = 2;
                        $returnarray['message'] = "Logo deleted successfully.";
                    } else {
                        $returnarray['error'] = 1;
                        $returnarray['message'] = "You can not delete other profile logo.";
                    }
                } else {
                    $returnarray['error'] = 1;
                    $returnarray['message'] = "You can not delete logo.";
                }
            } else {
                $returnarray['error'] = 1;
                $returnarray['message'] = "You can not delete other user profile logo.";
            }
        } else {
            $returnarray['error'] = 1;
            $returnarray['message'] = "You can not delete logo.";
        }
        echo json_encode($returnarray);
        exit;
    }
    public function action_deletelogo_extra()
    {
        $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : '';
        $returnarray = array();
        if ($profile_id != '') {
            // get profile data
            $profile = Model::factory('profiles')->getProfileById($profile_id);
            if (is_array($profile) && !empty($profile)) {
                // get weblink data
                $logo = Model::factory('logo')->getLogoExtraById($profile_id);
                if (is_array($logo) && !empty($logo)) {
                    if ($logo['profile_id'] == $profile_id) {
                        if (unlink(realpath($logo["logo_name"]))) {
                        }
                        Model::factory('logo')->deleteLogoExtraByProfileId($profile_id);
                        $returnarray['error'] = 2;
                        $returnarray['message'] = "Logo deleted successfully.";
                    } else {
                        $returnarray['error'] = 1;
                        $returnarray['message'] = "You can not delete other profile logo.";
                    }
                } else {
                    $returnarray['error'] = 1;
                    $returnarray['message'] = "You can not delete logo.";
                }
            } else {
                $returnarray['error'] = 1;
                $returnarray['message'] = "You can not delete other user profile logo.";
            }
        } else {
            $returnarray['error'] = 1;
            $returnarray['message'] = "You can not delete logo.";
        }
        echo json_encode($returnarray);
        exit;
    }
    public function action_deletelogo_footer()
    {
        // get profile data
        $userdata = Model::factory('user')->getUserByID($this->session->get('clientuserid'));
        $footer_logo = $userdata['footer_logo'];
        if ($footer_logo != "" && file_exists("images/logo/" . $footer_logo)) {
            unlink("images/logo/" . $footer_logo);
            $fields2['footer_logo'] = '';
            Model::factory('user')->updateuser($fields2, $this->session->get('clientuserid'));
            $returnarray['error'] = 2;
            $returnarray['message'] = "Logo deleted successfully.";
        } else {
            $returnarray['error'] = 1;
            $returnarray['message'] = "You can not delete other profile logo.";
        }
        echo json_encode($returnarray);
        exit;
    }
    public function action_multiple_code_renewal()
    {
        if ($this->session->get('voc_user_id') != "") {
            $this->request->redirect(URL::base() . 'dashboard/voc_user');
            exit;
        }
        //check whether current user is subuser then redirect
        if ($this->session->get('is_sub_user') == "1") {
            $this->request->redirect('dashboard');
        }
        // set title tag
        $this->template->head->title = "Multiple Code Renewal";
        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        $this->session->get("successmessage");
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $profiles = Model::factory('profiles')->get_all_profiles(" user_id='" . $this->userid . "' and (free_code=0 or (free_code=1 and renewal_required=1) ) and expired_at!='0000-00-00 00:00:00' and  (DATEDIFF(expired_at,now()))<=30 ");
        $is_sub_user = $this->session->get('is_sub_user');
        // Default Content template
        $this->template->content = View::factory('dashboard/multiple_code_renewal')->bind('profiles', $profiles)->bind('is_sub_user', $is_sub_user)->bind('errormessage', $errormessage)->bind('successmessage', $successmessage)->bind('user_id', $this->userid);
    }
    public function action_saveTempDocFile()
    {
        if (isset($_REQUEST['profile_id_document']) && $_REQUEST['profile_id_document'] != '') {
            $profile_id = $_REQUEST['profile_id_document'];
            // set title tag
            $this->template->head->title = "Save Document ";
            include_once "fileuploader/classes/easy_upload/upload_class.php"; //classes is the map where the class file is stored
            $upload = new file_upload();
            $upload->upload_dir = 'images/form_builder_uploaded_images/';
            $upload->extensions = array('.png', '.jpg', '.jpeg', '.gif', '.pdf', '.doc', '.docx'); // specify the allowed extensions here
            $upload->rename_file = true;
            if (!empty($_FILES)) {
                $upload->the_temp_file = $_FILES['userfile']['tmp_name'];
                $upload->the_file = $_FILES['userfile']['name'];
                $upload->http_error = $_FILES['userfile']['error'];
                $upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
                if ($upload->upload()) {
                    $tmp_files = $this->session->get('tmp_files', false);
                    if (!$tmp_files) {
                        $tmp_files = array();
                    }
                    $this->session->set('form_builder_doc_temp', $upload->file_copy);
                    echo '<br/><div id="status">success ' . $upload->file_copy . '</div>';
                    exit;
                } else {
                    echo '<br/><div id="status">' . $upload->show_error_string() . '</div>';
                    exit;
                }
            } else {
                echo '<br/><div id="status">' . $upload->show_error_string() . '</div>';
                exit;
            }
        }
    }
    public function action_saveTempDocMultiFile()
    {
        if (isset($_REQUEST['profile_id_document']) && $_REQUEST['profile_id_document'] != '') {
            $profile_id = $_REQUEST['profile_id_document'];
            // set title tag
            $this->template->head->title = "Save Document ";
            include_once "fileuploader/classes/easy_upload/upload_class.php"; //classes is the map where the class file is stored
            $upload = new file_upload();
            $upload->upload_dir = 'images/formbuilder_upload/';
            $upload->extensions = array('.png', '.jpg', '.jpeg', '.gif', '.pdf', '.doc', '.docx'); // specify the allowed extensions here
            $upload->rename_file = true;
            if (!empty($_FILES)) {
                $upload->the_temp_file = $_FILES['userfile']['tmp_name'];
                $upload->the_file = $_FILES['userfile']['name'];
                $upload->http_error = $_FILES['userfile']['error'];
                $upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
                if ($upload->upload()) {
                    $tmp_files = $this->session->get('tmp_files', false);
                    if (!$tmp_files) {
                        $tmp_files = array();
                    }
                    if ($this->session->get('form_builder_doc_multi_temp') == "") {
                        $this->session->set('form_builder_doc_multi_temp', $upload->file_copy);
                    } else {
                        $this->session->set('form_builder_doc_multi_temp', $this->session->get('form_builder_doc_multi_temp') . ',' . $upload->file_copy);
                    }
                    echo '<br/><div id="status">File uploaded successfully</div>';
                    exit;
                } else {
                    echo '<br/><div id="status">' . $upload->show_error_string() . '</div>';
                    exit;
                }
            } else {
                echo '<br/><div id="status">' . $upload->show_error_string() . '</div>';
                exit;
            }
        }
    }

    public function action_upload_swms_multiple_file_upload(){

        $multiple_file_upload_swms_hazard = array();
        foreach ($_FILES as $key => $value) {
            $file_str = "";
            $key_name = explode("hwms_question_", $key);
            $question_id = $key_name[1];

            $session_id = rand(1111, 9999) . "-" . date('dmyHis');

            foreach ($value["name"] as $key1 => $value1) {
                if ($_FILES[$key]['size'][$key1] > 0) {
                    $arr = pathinfo($_FILES[$key]['name'][$key1]);
                    $ext = $arr["extension"];

                    $filename = $session_id . "_" . $question_id . $key1 . "." . $ext;

                    if ($file_str == "") {
                        $file_str = $filename;
                    } else {
                        $file_str = $file_str . "," . $filename;
                    }

                    move_uploaded_file($_FILES[$key]['tmp_name'][$key1], "images/formbuilder_upload/" . $filename);
                    $multiple_file_upload_swms_hazard[$question_id][] = URL::base() . "images/formbuilder_upload/" . $filename;
                }

            }
        }
        echo json_encode($multiple_file_upload_swms_hazard);
        exit;


    }
    public function action_remove_swms_multiple_uploaded_file(){
        $file_name = end(explode("/",$_POST['file_name']));
        
        $is_unlink = unlink('images/formbuilder_upload/'.$file_name);
        if($is_unlink){
            echo json_encode('file remove successfully.');
        }else{
            echo json_encode('error in remove files.');
        }
        exit;
    }
}
// End Welcome
