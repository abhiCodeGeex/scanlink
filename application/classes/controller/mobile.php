<?php

defined('SYSPATH') or die('No direct script access.');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/phpwkhtmltopdf/vendor/autoload.php';

use mikehaertl\wkhtmlto\Pdf;

class Controller_Mobile extends Controller_Template_Mobile
{
    public $types = '';
    public $userid = '';
    public $originalfile = 'images/logo/code/original/';
    public $thumbfile = 'images/logo/code/thumb/';
    public $mediumfile = 'images/logo/code/medium/';

    public function before()
    {
        parent::before();

        //$session = Session::instance();
        //var_dump($session->get('clientuserid'));exit;
        /*         if(!$this->session->get('clientuserid')):
        $this->request->redirect(URL::base().'welcome/index');
        endif; */
        // Default Head view file

        $cur_action = $this->request->action();

        $this->template->head = View::factory('mobile/template/head');
        $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen') /* , array('href'=>'styles/default.css','media'=>'screen'), array('href'=>'styles/colorbox.css','media'=>'screen') */, array('href' => 'photoswipe/photoswipe.css', 'media' => 'screen'), array('href' => 'swiper/css/idangerous.swiper.css', 'media' => 'screen'), array('href' => 'form-builder/css/uniform.default.css', 'media' => 'screen'));

        $actionnm = $this->request->action();
        // All JS Libraries and Scripts
        $this->template->head->javascripts = array(
            'js/jquery.min-1-7-1.js',
            'photoswipe/klass.min.js',
            'photoswipe/code.photoswipe.jquery-3.0.4.min.js',
            'form-builder/js/jquery.uniform.js',
            'js/custome.js',
            'js/modernizr.custom.60079.js',
            'swiper/js/idangerous.swiper-1.9.1.min.js',
            'swiper/js/idangerous.swiper.scrollbar-1.2.js',
        );

        if (!(isset($_REQUEST['ask_for_location']) && $_REQUEST['ask_for_location'] == 'no')) {
            array_push($this->template->head->javascripts, 'js/galatech/geo-min.js');
            array_push($this->template->head->javascripts, 'js/galatech/wpapi.js?' . date('His'));
        }

        // Get Shortcut Image Title, Shortcut Image 1 and Shortcut Image 2
        // Set the default Page Title
        $this->template->head->title = 'Welcome to ScanLink';

        $home = $this->request->detect_uri();
        $params = explode('/', $home);

        if (count($params) > 1 && is_numeric($params[2])) {
            $profile = Model::factory('profiles')->getProfileByIdForMobile($params[2]);

            $clientshortcutdata = Model::factory('client')->getClientById($profile['client_id']);

            $shortcutArr['shortcut_title'] = $clientshortcutdata['shortcut_title'];
            $shortcutArr['shortcut_image1'] = $clientshortcutdata['shortcut_image1'];
            $shortcutArr['shortcut_image2'] = $clientshortcutdata['shortcut_image2'];

            if ($shortcutArr['shortcut_title'] != '') {
                $this->template->head->title = $shortcutArr['shortcut_title'];
            }

            if ($shortcutArr['shortcut_image1'] != '') {
                $this->template->head->shortcut_image1 = $shortcutArr['shortcut_image1'];
            }
            if ($shortcutArr['shortcut_image2'] != '') {
                $this->template->head->shortcut_image2 = $shortcutArr['shortcut_image2'];
            }
        }

        $this->template->headers = View::factory('mobile/template/headers');

        if ($cur_action == 'index') {
            $this->template->footer = View::factory('mobile/template/footer');
        } elseif ($cur_action == 'documents') {
            $this->template->footer = View::factory('mobile/template/doc_footer');
        } elseif ($cur_action == 'videos') {
            $this->template->footer = View::factory('mobile/template/vid_footer');
        } else {
            $this->template->headers = View::factory('mobile/template/other_header');
            $this->template->footer = View::factory('mobile/template/other_footer');
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }


        //$xml = file_get_contents('https://api.ipgeolocation.io/ipgeo?ip=' . $ip . '&apiKey=5179bfaee22047ebbe2dbfe70682125d');
        $xml = file_get_contents('https://api.ipgeolocation.io/ipgeo?apiKey=5179bfaee22047ebbe2dbfe70682125d&ip=' . $ip);

        $xml = json_decode($xml);
	//Log::instance()->add(Log::INFO, 'XML: '.print_r($xml,true));
	//Log::instance()->add(Log::INFO, 'Timezone: '.print_r($_SERVER,true));

        $timezone_name = $xml->time_zone->name;
        if (!empty($timezone_name)) {
            date_default_timezone_set($timezone_name);
        } else {
            date_default_timezone_set('Australia/Adelaide');
        }

        // $xml = simplexml_load_file('http://www.geoplugin.net/xml.gp?ip=' . getRealIpAddr());

        // foreach ($xml as $key => $value) {
        //     array_push($data, $value);
        // } 

        // $simple_xml_url = 'http://www.geoplugin.net/xml.gp?ip=' . $this->getRealIpAddr();
        // $use_errors = libxml_use_internal_errors(true);
        // $xml = simplexml_load_file($simple_xml_url);
        // if ($xml !== false) {
        //     $data = array();
        //     foreach ($xml as $key => $value) {
        //         array_push($data, $value);
        //     }
        //     $timezone_name = !empty($data[19]) ? $data[19] : 'Australia/Adelaide';
        //     date_default_timezone_set($timezone_name);
        // } else {
        //     date_default_timezone_set('Australia/Adelaide');
        // }

        // if (!empty($use_errors)) {
        //     libxml_clear_errors();
        //     libxml_use_internal_errors($use_errors);
        // }
        //$listIdentifier = \DateTimeZone::listIdentifiers();
    }

    public function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public function action_index()
    {
        // Default Content template

        $current_url = URL::base() . $this->request->uri();

        array_push($this->template->head->stylesheets, array('href' => 'mobile/styles/prettyCheckable.css', 'media' => 'screen'));
        array_push($this->template->head->stylesheets, array('href' => 'https://code.jquery.com/ui/1.9.0/themes/dark-hive/jquery-ui.css', 'media' => 'screen'));
        array_push($this->template->head->stylesheets, array('href' => 'photoswipe/photoswipe.css', 'media' => 'screen'));
        array_push($this->template->head->javascripts, 'mobile/js/prettyCheckable.js');
        array_push($this->template->head->javascripts, 'mobile/styles/lib/klass.min.js');
        array_push($this->template->head->javascripts, 'mobile/js/code.photoswipe-3.0.5.min.js');
        array_push($this->template->head->javascripts, 'https://code.jquery.com/ui/1.9.0/jquery-ui.js');
        array_push($this->template->head->javascripts, 'js/jquery-ui-timepicker-addon.js');

        $home = $this->request->detect_uri();
        $params = explode('/', $home);
        //array_shift($params);    //please comment this line for live env and uncomment it for local
        //$client = Model::factory('client')->getClientByURI($params[0]);
        //echo $params[1] ;
        //exit;

        // Delete email Atteched pdf file
        if ($this->session->get('form_submission_pdf') != '') {
            unlink('images/formbuilder_upload/' . $this->session->get('form_submission_pdf') . '.pdf');
            $session = Session::instance();
            $session->delete('form_submission_pdf');
        }
        $profile = Model::factory('profiles')->getProfileByIdForMobile($params[1]);
        if (is_array($profile) && count($profile) <= 0) {
            $profile = Model::factory('profiles')->getProfileByIdForMobile($params[2]);
            if (is_array($profile) && count($profile) <= 0) {
                $this->request->redirect(url::base() . 'mobile/error');
                exit;
            } else {
                $clientId = $profile['client_id'];
                $profileId = $profile['id'];
            }
        } else {
            $clientId = $profile['client_id'];
            $profileId = $profile['id'];
        }

        //get footer logo
        $userdata = Model::factory('user')->getUserByID($profile['user_id']);
        $footer_logo = $userdata['footer_logo'];

        if ($footer_logo != '' && file_exists('images/logo/' . $footer_logo)) {
            $this->template->footer_logo = 'images/logo/' . $footer_logo;
        } else {
            $this->template->footer_logo = 'images/PoweredbyScanLink.png';
        }

        //get footer logo end

        if (isset($_REQUEST['app_user_firstname'])) {
            $this->session->set('app_user_firstname', $_REQUEST['app_user_firstname']);
        } else {
            $this->session->set('app_user_firstname', '');
        }

        if (isset($_REQUEST['app_user_lastname'])) {
            $this->session->set('app_user_lastname', $_REQUEST['app_user_lastname']);
        } else {
            $this->session->set('app_user_lastname', '');
        }

        if (isset($_REQUEST['app_user_email'])) {
            $this->session->set('app_user_email', $_REQUEST['app_user_email']);
        } else {
            $this->session->set('app_user_email', '');
        }

        if (isset($_REQUEST['app_user_mobile'])) {
            $this->session->set('app_user_mobile', $_REQUEST['app_user_mobile']);
        } else {
            $this->session->set('app_user_mobile', '');
        }

        $questions_arr = Model::factory('Formbuilder')->get_profile_question($profileId);

        /* echo "<pre>";

        print_r($params);
        print_r($profile);
        exit; */

        $client = Model::factory('client')->getClientById($clientId);

        if (empty($client) || empty($profile)) {
            $this->request->redirect(url::base() . 'mobile/error');
            exit;
        }

        if (strtotime($profile['expired_at']) < strtotime(date('Y-m-d H:i:s')) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            //$this->request->redirect(url::base() . "mobile/expired");
            $this->request->redirect(url::base());
            exit;
        }

        $logo = Model::factory('logo')->getLogoByProfileId($profileId);
        $pictures = Model::factory('picture')->getPicturesByProfileId($profileId);
        $documents = Model::factory('documents')->getDocumentsByProfileId($profileId);
        $checklist_items = Model::factory('checklistitem')->getChecklist_itemsByProfileId($profileId);
        $checklist_items_checked = Model::factory('checklistitem')->getChecklist_itemsByProfileId_ForCurrentSession($profileId);
        $checklist_option = Model::factory('user')->getChecklistOption($clientId);

        $documents = Model::factory('documents')->getDocumentsByProfileId($profileId);
        $videos = Model::factory('video')->getVideoByProfileId($profileId);
        $weblink = Model::factory('weblink')->getWeblinkByProfileId($profileId);
        $contact = Model::factory('profilecontact')->getContactByProfileId($profileId);
        if ($profile['protect'] == '1' && $this->session->get('secure_profile') != $profile['id'] && $this->session->get('form_submit_success', false) == false) {
            //if ($profile["protect"] == "1") {
            $this->session->set('profile_id', $profile['id']);
            $this->session->set('path', url::base() . $profile['id'] . '/1');
            $this->request->redirect(url::base() . 'mobile/authenticate');
        }

        //echo $current_uri = URL::site($this->request->uri).'?'.http_build_query($_GET, '&');
        $form_submit_success = '';

        if ($this->session->get('form_submit_success') == 1) {
            $form_submit_success = 'Thank You<br><br>Your response has been<br>submitted successfully';
            $this->session->delete('form_submit_success');
            $this->template->head->temp_var = 'save_form_builder';
        }
        if ($this->session->get('form_submit_success') == 2) {
            $form_submit_success = 'Error<br><br>There is the problem with the form submission.';
            $this->session->delete('form_submit_success');
            $this->template->head->temp_var = 'save_form_builder';
        }

        if ($profile['enable_data_collection'] == '1') {
            // START Collact user information
            $errors = array();
            $post = array();
            $postArr = array();
            if ($_POST) :
                $user_name = trim($_POST['user_name']);
                $user_email = trim($_POST['user_email']);

                $errors['user_name'] = '';
                $errors['user_email'] = '';
                $postArr['profile_id'] = $profile['id'];

                if ($user_name == '') {
                    $errors['user_name'] = 'Please enter your name..!';
                } else {
                    $postArr['user_name'] = $user_name;
                }

                if ($user_email == '') {
                    $errors['user_email'] = 'Please enter your e-mail..!';
                } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                    $user_email = '';
                    $errors['user_email'] = 'Please enter valid e-mail..!';
                } else {
                    $postArr['user_email'] = $user_email;
                }

                if ($user_name != '' && $user_email != '') {
                    Model::factory('userinfo')->insert_user_info($postArr);
                    Session::instance()->set('user_info', $postArr['profile_id']);
                    $this->request->redirect(url::base() . $home);
                }
            endif;
            // END Collact user information
        }

        $this->template->headers->type_id = $profile['type_id'];
        $this->template->headers->home = $home;
        $this->template->headers->show_header = $profile['show_header'];
        $this->template->footer->home = $home;
        $this->template->footer->profile = $profile;
        $this->template->headers->profile_id = $profile['id'];
        if (!empty($logo) && isset($logo['logo_name'])) {
            $this->template->headers->logo = $logo['logo_name'];
        }

        $ask_for_location = 'yes';
        if (isset($_REQUEST['ask_for_location'])) {
            $ask_for_location = $_REQUEST['ask_for_location'];
        }

        if ($profile['telephone'] != '') {
            //$profile['telephone'] =  $this->formatPhoneNumber($profile['telephone']);
        }

        if ($profile['name_company'] != '') {
            //$profile['name_company'] =  $this->formatPhoneNumber($profile['name_company']);
        }

        if ($profile['type_id'] == '9') {
            $this->template = 'mobile/template/code_template';
            parent::before();
            $this->template->headers = '';
            $this->template->footer = '';

            $this->template->head = View::factory('mobile/template/head');
            $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen') /* , array('href'=>'styles/default.css','media'=>'screen'), array('href'=>'styles/colorbox.css','media'=>'screen') */, array('href' => 'photoswipe/photoswipe.css', 'media' => 'screen'), array('href' => 'swiper/css/idangerous.swiper.css', 'media' => 'screen'));
            $actionnm = $this->request->action();
            // All JS Libraries and Scripts
            $this->template->head->javascripts = array(
                'js/jquery.min-1-7-1.js',
                'photoswipe/klass.min.js',
                'photoswipe/code.photoswipe.jquery-3.0.4.min.js',
                'js/jquery.uniform.js',
                'js/custome.js',
                'js/modernizr.custom.60079.js',
                'swiper/js/idangerous.swiper-1.9.1.min.js',
                'swiper/js/idangerous.swiper.scrollbar-1.2.js',
            );

            /*if (!(isset($_REQUEST['ask_for_location']) && $_REQUEST['ask_for_location'] == 'no')) {
            array_push($this->template->head->javascripts, "js/galatech/geo-min.js");
            array_push($this->template->head->javascripts, "js/galatech/wpapi.js");
            }*/

            array_push($this->template->head->javascripts, 'js/galatech/geo-min.js');
            array_push($this->template->head->javascripts, 'js/galatech/wpapi.js?' . date('His'));

            array_push($this->template->head->stylesheets, array('href' => 'mobile/styles/prettyCheckable.css', 'media' => 'screen'));
            array_push($this->template->head->stylesheets, array('href' => 'https://code.jquery.com/ui/1.9.0/themes/dark-hive/jquery-ui.css', 'media' => 'screen'));

            array_push($this->template->head->javascripts, 'mobile/js/prettyCheckable.js');
            array_push($this->template->head->javascripts, 'https://code.jquery.com/ui/1.9.0/jquery-ui.js');

            // Set the default Page Title
            $this->template->head->title = 'Welcome to ScanLink';

            $logodata = $logo;
            if (!empty($logodata)) {
                if ($logodata['logo_name'] != '') {
                    $profile['originalfile'] = $this->originalfile . $logodata['logo_name'];
                    $profile['thumbfile'] = $this->thumbfile . $logodata['logo_name'];
                    $profile['mediumfile'] = $this->mediumfile . $logodata['logo_name'];
                }
            }
            $this->template->content = View::factory('mobile/code')
                ->bind('profile', $profile)
                ->bind('errors', $errors);
        } elseif ($profile['type_id'] == '11') { //exhibit
            $this->template->headers = '';

            Session::instance()->set('user_info', '');
            $this->session->set('secure_profile', '');

            $logo2 = Model::factory('logo')->getLogoExtraByProfileId($profileId);
            $videos2 = Model::factory('video')->getVideoExtraByProfileId($profileId);
            $pictures2 = Model::factory('picture')->getPicturesExtraByProfileId($profileId);

            $this->template->content = View::factory('mobile/exhibit_mobile')
                ->bind('profile', $profile)
                ->bind('errors', $errors)
                ->bind('logo', $logo)
                ->bind('logo_extra', $logo2)
                ->bind('pictures', $pictures)
                ->bind('pictures2', $pictures2)
                ->bind('checklist_items', $checklist_items)
                ->bind('checklist_items_checked', $checklist_items_checked)
                ->bind('checklist_option', $checklist_option[0]['checklist_option'])
                ->bind('home', $home)
                ->bind('documents', $documents)
                ->bind('videos', $videos)
                ->bind('videos2', $videos2)
                ->bind('ask_for_location', $ask_for_location)
                ->bind('weblink', $weblink)
                ->bind('contact', $contact)
                ->bind('questions_arr', $questions_arr)
                ->bind('form_submit_success', $form_submit_success)
                ->bind('current_url', $current_url);
        } elseif ($profile['type_id'] == '12') { //voc
            $this->template->headers = '';

            Session::instance()->set('user_info', '');
            $this->session->set('secure_profile', '');

            $pictures_voc = Model::factory('picture')->getPicturesVocByProfileId($profileId);
            $documents_voc = Model::factory('voc')->getDocumentsByProfileId($profileId);

            $this->template->content = View::factory('mobile/voc_mobile')
                ->bind('profile', $profile)
                ->bind('errors', $errors)
                ->bind('logo', $logo)
                ->bind('home', $home)
                ->bind('pictures_voc', $pictures_voc)
                ->bind('documents_voc', $documents_voc)
                ->bind('ask_for_location', $ask_for_location)
                ->bind('questions_arr', $questions_arr)
                ->bind('form_submit_success', $form_submit_success)
                ->bind('current_url', $current_url);
        } elseif (isset($_REQUEST['test']) && $_REQUEST['test'] == 1) {
            Session::instance()->set('user_info', '');
            $this->session->set('secure_profile', '');

            $this->template->content = View::factory('mobile/index_test')
                ->bind('profile', $profile)
                ->bind('errors', $errors)
                ->bind('pictures', $pictures)
                ->bind('checklist_items', $checklist_items)
                ->bind('checklist_items_checked', $checklist_items_checked)
                ->bind('checklist_option', $checklist_option[0]['checklist_option'])
                ->bind('home', $home)
                ->bind('documents', $documents)
                ->bind('videos', $videos)
                ->bind('ask_for_location', $ask_for_location)
                ->bind('weblink', $weblink)
                ->bind('contact', $contact)
                ->bind('questions_arr', $questions_arr)
                ->bind('form_submit_success', $form_submit_success)
                ->bind('current_url', $current_url);
        } else {
            Session::instance()->set('user_info', '');
            $this->session->set('secure_profile', '');

            $this->template->content = View::factory('mobile/index')
                ->bind('profile', $profile)
                ->bind('errors', $errors)
                ->bind('pictures', $pictures)
                ->bind('checklist_items', $checklist_items)
                ->bind('checklist_items_checked', $checklist_items_checked)
                ->bind('checklist_option', $checklist_option[0]['checklist_option'])
                ->bind('home', $home)
                ->bind('documents', $documents)
                ->bind('videos', $videos)
                ->bind('ask_for_location', $ask_for_location)
                ->bind('weblink', $weblink)
                ->bind('contact', $contact)
                ->bind('questions_arr', $questions_arr)
                ->bind('form_submit_success', $form_submit_success)
                ->bind('current_url', $current_url);
        }
    }

    public function action_save_form_builder_data()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //Log::instance()->add(Log::INFO, 'form submit start at '.date('Y-m-d H:i:s'));

        try {

            if (isset($_POST['form_builder'])) { // if user has submitted form
                $inserted_id = array();
                //mail("shaileshk@webplanex.com", "Raw Data of the Form for profile " . $_POST['profile_id'], json_encode($_POST));
                if ($this->session->get('app_user_firstname', '') != '') {
                    $app_user_firstname = $this->session->get('app_user_firstname');
                } else {
                    $app_user_firstname = '';
                }

                if ($this->session->get('app_user_lastname', '') != '') {
                    $app_user_lastname = $this->session->get('app_user_lastname');
                } else {
                    $app_user_lastname = '';
                }

                if ($this->session->get('app_user_email', '') != '') {
                    $app_user_email = $this->session->get('app_user_email');
                } else {
                    $app_user_email = '';
                }

                if ($this->session->get('app_user_mobile', '') != '') {
                    $app_user_mobile = $this->session->get('app_user_mobile');
                } else {
                    $app_user_mobile = '';
                }

                $form_id = $_POST['form_id'];
                $profile_id = $_POST['profile_id'];
                $profile_name = $_POST['profile_name'];

                $logo = Model::factory('logo')->getLogoByProfileId($profile_id);

                $profile_logo = '';

                if (!empty($logo) && isset($logo['logo_name']) && file_exists($logo['logo_name'])) {
                    $profile_logo = $logo['logo_name'];
                }

                $session_id = rand(1111, 9999) . '-' . date('dmyHis');

                /////testing purpose end
                foreach ($_POST as $name => $value) {
                    if (strstr($name, 'question_') != '') {
                        $post_name_arr = explode('question_', $name);

                        if (count($post_name_arr) > 0) {
                            $question_id = $post_name_arr[1];

                            if (!is_array($value)) {

                                if (strstr($post_name_arr[1], '_row') != '') {
                                    //Log::instance()->add(Log::INFO, 'form process normal inputs at '.date('Y-m-d H:i:s'));
                                    $grid_id = explode('_row', $post_name_arr[1]);
                                    $question_id = $grid_id[0];
                                    $row_number = $grid_id[1];

                                    $inserted_id[] = Model::factory('Formbuilder')->save_form_builder_answers($question_id, $profile_id, $value, $row_number, $session_id, $app_user_firstname, $app_user_lastname, $app_user_email, $app_user_mobile);
                                } else {
                                    //Log::instance()->add(Log::INFO, 'form process signature at '.date('Y-m-d H:i:s'));
                                    $signature_image = '';

                                    $question_obj = Model::factory('Formbuilder')->get_question_by_question_id($question_id);
                                    if ($question_obj) {
                                        foreach ($question_obj as $question_obj_item) {
                                            if ($question_obj_item['question_type_id'] == '16') { // i.e. signature type
                                                $json = $value; // From Signature Pad
                                                if ($json != '') {
                                                    $image_width = $_REQUEST['sign_width_' . $question_id];

                                                    $img = sigJsonToImage($json, array('imageSize' => array($image_width, 200)));

                                                    imagepng($img, 'images/signature/' . $question_id . $session_id . '.png');
                                                    imagedestroy($img);
                                                }
                                                $signature_image = $question_id . $session_id . '.png';
                                            }
                                            if ($question_obj_item['question_type_id'] == '18') { // i.e. participant name, change status of participant to is_participated=1
                                                Model::factory('Formbuilder')->update_participant_status($_POST['question_' . $question_id], $profile_id);
                                            }
                                        }
                                    }

                                    //save answer
                                    if ($signature_image != '') {
                                        $inserted_id[] = Model::factory('Formbuilder')->save_form_builder_answers($question_id, $profile_id, $signature_image, 0, $session_id, $app_user_firstname, $app_user_lastname, $app_user_email, $app_user_mobile);
                                    } else {
                                        $inserted_id[] = Model::factory('Formbuilder')->save_form_builder_answers($question_id, $profile_id, $value, 0, $session_id, $app_user_firstname, $app_user_lastname, $app_user_email, $app_user_mobile);
                                    }
                                    //Log::instance()->add(Log::INFO, 'form process signature end at '.date('Y-m-d H:i:s'));
                                }
                            } else {
                                //check for signature image
                                //Log::instance()->add(Log::INFO, 'form process array signature at '.date('Y-m-d H:i:s'));
                                $signature_image = '';
                                $signature_name_text = '';
                                $signature_name_text2 = '';
                                $signature_name_text3 = '';
                                $signature_name_text4 = '';

                                //check for participant signature image
                                $p_signature_image = '';
                                $p_emp_name = '';

                                $question_obj = Model::factory('Formbuilder')->get_question_by_question_id($question_id);
                                if ($question_obj) {
                                    foreach ($question_obj as $question_obj_item) {
                                        if ($question_obj_item['question_type_id'] == '16') { // i.e. signature type
                                            for ($s = 0; $s < count($value); ++$s) {
                                                $json = $value[$s]; // From Signature Pad

                                                if ($json != '') {
                                                    $image_width = $_REQUEST['sign_width_' . $question_id];

                                                    $img = sigJsonToImage($json, array('imageSize' => array($image_width, 200)));

                                                    imagepng($img, 'images/signature/' . $question_id . $session_id . $s . '.png');
                                                    imagedestroy($img);
                                                }

                                                if ($signature_image == '') {
                                                    $signature_image = $question_id . $session_id . $s . '.png';
                                                } else {
                                                    $signature_image = $signature_image . ',' . $question_id . $session_id . $s . '.png';
                                                }

                                                if ($question_obj_item['include_name'] == '1') {
                                                    if ($signature_name_text == '') {
                                                        $signature_name_text = $_POST['sub_ques_' . $question_id][$s];
                                                    } else {
                                                        $signature_name_text = $signature_name_text . ':::' . $_POST['sub_ques_' . $question_id][$s];
                                                    }
                                                }

                                                if ($question_obj_item['include_employer'] == '1') {
                                                    if ($signature_name_text2 == '') {
                                                        $signature_name_text2 = $_POST['sub_ques2_' . $question_id][$s];
                                                    } else {
                                                        $signature_name_text2 = $signature_name_text2 . ':::' . $_POST['sub_ques2_' . $question_id][$s];
                                                    }
                                                }

                                                if ($question_obj_item['include_email'] == '1') {
                                                    if ($signature_name_text3 == '') {
                                                        $signature_name_text3 = $_POST['sub_ques3_' . $question_id][$s];
                                                    } else {
                                                        $signature_name_text3 = $signature_name_text3 . ':::' . $_POST['sub_ques3_' . $question_id][$s];
                                                    }
                                                }
                                                if ($question_obj_item['include_phone'] == '1') {
                                                    if ($signature_name_text4 == '') {
                                                        $signature_name_text4 = $_POST['sub_ques4_' . $question_id][$s];
                                                    } else {
                                                        $signature_name_text4 = $signature_name_text4 . ':::' . $_POST['sub_ques4_' . $question_id][$s];
                                                    }
                                                }
                                            }
                                        }
                                        if ($question_obj_item['question_type_id'] == '18') { // i.e. participant name, change status of participant to is_participated=1
                                            for ($s = 0; $s < count($value); ++$s) {
                                                if ($question_obj_item['participant_include_signature'] == '1') {
                                                    $json_part = $_POST['sign_que_' . $question_id][$s]; // From Signature Pad
                                                    if ($json_part) {
                                                        $image_width = $_REQUEST['p_sign_width_' . $question_id];
                                                        $img = sigJsonToImage($json_part, array('imageSize' => array($image_width, 200)));
                                                        imagepng($img, 'images/signature/' . $question_id . $session_id . $s . '.png');
                                                        imagedestroy($img);
                                                    }
                                                    if ($p_signature_image == '') {
                                                        $p_signature_image = $question_id . $session_id . $s . '.png';
                                                    } else {
                                                        $p_signature_image = $p_signature_image . ',' . $question_id . $session_id . $s . '.png';
                                                    }
                                                }
                                                $participant_cmp = '';
                                                if (isset($_POST['parti_que_' . $question_id][$s])) {
                                                    $participant_cmp = $_POST['parti_que_' . $question_id][$s];
                                                }
                                                Model::factory('Formbuilder')->update_participant_status($value[$s], $profile_id, $participant_cmp);

                                                if ($question_obj_item['participant_include_employer'] == '1') {
                                                    if ($p_emp_name == '') {
                                                        $p_emp_name = $_POST['parti_que_' . $question_id][$s];
                                                    } else {
                                                        $p_emp_name = $p_emp_name . ':::' . $_POST['parti_que_' . $question_id][$s];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($signature_image != '') {
                                    $inserted_id[] = Model::factory('Formbuilder')->save_form_builder_answers($question_id, $profile_id, $signature_image . '###' . $signature_name_text . '###' . $signature_name_text2 . '###' . $signature_name_text3 . '###' . $signature_name_text4, 0, $session_id, $app_user_firstname, $app_user_lastname, $app_user_email, $app_user_mobile, $p_signature_image, $p_emp_name);
                                } else {

                                    if ($question_obj_item['question_type_id'] == 25) {

                                        $data = array(
                                            'visitor_name' => $_POST['covid_question_' . $question_id][0],
                                            'visitor_phone' => $_POST['covid_question_' . $question_id][1],
                                            'date' => $_POST['covid_question_' . $question_id][2],
                                            'time' => $_POST['covid_question_' . $question_id][3],
                                            'venue_name' => $_POST['covid_question_' . $question_id][4],
                                            'venue_address' => $_POST['covid_question_' . $question_id][5],
                                            'location_description' => $_POST['covid_question_' . $question_id][6],
                                        );

                                        Model::factory('Formbuilder')->form_builder_question_update($question_obj_item['question_id'], $data);
                                    }
                                    $arr_value = implode(':::', $value);
                                    $inserted_id[] = Model::factory('Formbuilder')->save_form_builder_answers($question_id, $profile_id, $arr_value, 0, $session_id, $app_user_firstname, $app_user_lastname, $app_user_email, $app_user_mobile, $p_signature_image, $p_emp_name);
                                }
                                //Log::instance()->add(Log::INFO, 'form process array signature end at '.date('Y-m-d H:i:s'));
                            }
                        } //end if(count
                    } //end if
                } //end foreach $_POST

                $multiple_file_upload_swms_hazard = array();

                $file_str = '';
                $file_name = array();
                $file_name_array = array();

                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'hwms_uploaded_file_') !== false) {

                        $key_name = explode('hwms_uploaded_file_', $key);
                        $question_id = $key_name[1];
                        $file_name_array[$question_id] = $_POST[$key];


                        foreach ($value as $key => $file_paths) {
                            $file_explode_name = explode("/", $file_paths);
                            $file_name[] = end($file_explode_name);
                        }
                    }
                }

                if (is_array($file_name) && !empty($file_name)) {
                    $file_str = implode(',', $file_name);
                }

                foreach ($_FILES as $key => $value) {

                    if (strpos($key, 'hwms_question_') !== false) {

                        // $key_name = explode('hwms_question_', $key);
                        // $question_id = $key_name[1];

                        // foreach ($value['name'] as $key1 => $value1) {
                        //     if ($_FILES[$key]['size'][$key1] > 0) {
                        //         $arr = pathinfo($_FILES[$key]['name'][$key1]);
                        //         $ext = $arr['extension'];

                        //         $filename = $session_id.'_'.$question_id.$key1.'.'.$ext;

                        //         if ($file_str == '') {
                        //             $file_str = $filename;
                        //         } else {
                        //             $file_str = $file_str.','.$filename;
                        //         }

                        //         move_uploaded_file($_FILES[$key]['tmp_name'][$key1], 'images/formbuilder_upload/'.$filename);
                        //         $multiple_file_upload_swms_hazard[$question_id][] = URL::base().'images/formbuilder_upload/'.$filename;
                        //     }
                        // }
                    } else {

                        if (strpos($key, 'hwms_question_') !== true) {

                            $key_name = explode('question_', $key);

                            $question_id = $key_name[1];
			    $file_str = ''; 
                            foreach ($value['name'] as $key1 => $value1) {
                                if ($_FILES[$key]['size'][$key1] > 0) {
                                    $arr = pathinfo($_FILES[$key]['name'][$key1]);
                                    $ext = $arr['extension'];

                                    $filename = $session_id . '_' . $question_id . $key1 . '.' . $ext;

                                    if ($file_str == '') {
                                        $file_str = $filename;
                                    } else {
                                        $file_str = $file_str . ',' . $filename;
                                    }

				    $destination_img = 'images/formbuilder_upload/'.''.$filename;
                                    move_uploaded_file($_FILES[$key]['tmp_name'][$key1], 'images/formbuilder_upload/' . $filename);
				    resizeImage($destination_img, $destination_img, 1000, 1000,70);
                                }
                            }
                        }
                    }

                    if ($file_str != '') {
                        $inserted_id[] = Model::factory('Formbuilder')->save_form_builder_answers($question_id, $profile_id, $file_str, 0, $session_id, $app_user_firstname, $app_user_lastname, $app_user_email, $app_user_mobile);
                    }
                } //end foreach $_FILES


                // $return = Model::factory('Formbuilder')->get_max_order_form_builder_answer();

                // if (!empty($return['timezone'])) {
                //     date_default_timezone_set($return['timezone']);
                // }


                // $is_update =  DB::query(Database::UPDATE, "update form_builder_answers set `date_time` = '" . $return['date'] . "' where `form_builder_answer_id` = '" . $return['form_answer_id'] . "'")->execute();


                // build html for mail
                $recipient_email_arr = Model::factory('Formbuilder')->form_builder_recipient_list($form_id);
                $additional_recipients = array();

                include_once 'mailsend.php';
                $fromemail = 'info@scanlink.com.au';
                $baseurl = URL::base();

                $details_html = ''; // used in mail
                $uploaded_image = '';
                $uploaded_image_arr = '';
                // Log::instance()->add(Log::INFO, 'form email start at '.date('Y-m-d H:i:s'));

                if ($details_html == '') {
                    $questions_arr_for_mail = Model::factory('Formbuilder')->get_profile_question($profile_id);

                    $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

                    $logo = Model::factory('logo')->getLogoByProfileId($profile_id);

                    $profile_logo = 'images/logo.png';

                    if (!empty($logo) && isset($logo['logo_name']) && file_exists($logo['logo_name'])) {
                        $profile_logo = $logo['logo_name'];
                    }

                    $profile_data = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

                    $form_submission_format = $profile_data['form_submission_format'];


                    if ($form_submission_format == 1) {
                        $details_html = '<img src="' . URL::base() . $profile_logo . '" height="108" alt=""  /><br><hr>&nbsp;';

                        $details_html .= '<div><b>Profile Number</b> : ' . $profile_id . '</div>';

                        $details_html .= '<div><b>Profile Name</b> : ' . $profile['code_profile_name'] . '</div>';

                        $details_html .= '<div><b>Submission Date/Time</b> : ' . date('d/m/Y H:i') . '</div><br>';
                        $details_html .= '<hr>';
                    }

                    foreach ($questions_arr_for_mail as $questions) {
                        $question_id = $questions['question_id'];
                        $question_text = $questions['question_text'];

                        switch ($questions['question_type_id']) {
                            case '1': // Text Field
                                $details_html .= '<b>User Input:</b> ' . $_POST['question_' . $question_id] . '<br>';

                                break;
                            case '2': // Text

                                $details_html .= '<br>' . $question_text . '<br>';
                                break;
                            case '3': // Mutliple choise

                                $question_options = Model::factory('Formbuilder')->get_question_option($question_id);

                                $question_option_text = '';
                                foreach ($question_options as $question_option) {
                                    if ($question_option_text == '') {
                                        $question_option_text = $question_option['option_name'];
                                    } else {
                                        $question_option_text = $question_option_text . '<br> ' . $question_option['option_name'];
                                    }
                                }

                                //$details_html .=  "Options: ".$question_option_text;
                                $details_html .= '<b>User Input:</b> ';

                                if (isset($_POST['question_' . $question_id])) {
                                    $details_html .= $_POST['question_' . $question_id];
                                }

                                $details_html .= '<br>';
                                break;
                            case '4': // Checkboxes

                                $question_options = Model::factory('Formbuilder')->get_question_option($question_id);

                                $question_option_text = '';
                                foreach ($question_options as $question_option) {
                                    if ($question_option_text == '') {
                                        $question_option_text = $question_option['option_name'];
                                    } else {
                                        $question_option_text = $question_option_text . '<br> ' . $question_option['option_name'];
                                    }
                                }

                                //$details_html .=  "Options: ".$question_option_text;

                                $details_html .= '<b>User Input:</b> ';

                                if (isset($_POST['question_' . $question_id])) {
                                    $details_html .= implode('<br>', $_POST['question_' . $question_id]);
                                }

                                $details_html .= '<br>';
                                break;
                            case '5': // Drop Down Menu

                                $question_options = Model::factory('Formbuilder')->get_question_option($question_id);

                                $question_option_text = '';
                                foreach ($question_options as $question_option) {
                                    if ($question_option_text == '') {
                                        $question_option_text = $question_option['option_name'];
                                    } else {
                                        $question_option_text = $question_option_text . '<br> ' . $question_option['option_name'];
                                    }
                                }

                                //$details_html .=  "Options: ".$question_option_text;

                                $details_html .= '<b>User Input:</b> ' . $_POST['question_' . $question_id];
                                $details_html .= '<br>';

                                break;
                            case '6': // Number Scale
                                $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                $scale_from = $question_options[0]['option_name'];

                                $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                $scale_to = $question_options[0]['option_name'];

                                //$details_html .=  "Range: From ".$scale_from." To ".$scale_to;

                                $details_html .= '<b>User Input:</b> ';

                                if (isset($_POST['question_' . $question_id])) {
                                    $details_html .= $_POST['question_' . $question_id];
                                }

                                $details_html .= '<br>';

                                break;
                            case '7': // Grid

                                $question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $row_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                $question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $column_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                $details_html .= "<table border='0' cellpadding='6' cellspacing='0'>";
                                $details_html .= '<tr>';
                                $details_html .= '<td>&nbsp;</td>';
                                for ($i = 0; $i < count($column_text); ++$i) {
                                    $details_html .= "<td align='center'>" . $column_text[$i]['option_name'] . '</td>';
                                }

                                $details_html .= '</tr>';

                                for ($i = 0; $i < count($row_text); ++$i) {
                                    $details_html .= '<tr>';
                                    $details_html .= '<td>' . $row_text[$i]['option_name'] . '</td>';

                                    for ($j = 0; $j < count($column_text); ++$j) {
                                        if (isset($_POST['question_' . $question_id . '_row' . $i]) && $_POST['question_' . $question_id . '_row' . $i] == $j) {
                                            //$checked = "<img width='18' src='".URL::base()."images/green_check_mark_icon.png' >";
                                            $checked = '<img width="18" src="' . URL::base() . 'images/green_check_mark_icon.png" >';
                                        } else {
                                            $checked = '';
                                        }

                                        $details_html .= "<td  align='center'>" . $checked . '</td>';
                                    }

                                    $details_html .= '</tr>';
                                }

                                $details_html .= '</table>';

                                $details_html .= '<br>';
                                break;
                            case '8': // Date

                                $details_html .= '<b>User Input:</b> ' . $_POST['question_' . $question_id] . '<br>';

                                break;
                            case '9': // time

                                $details_html .= '<b>User Input:</b> ' . $_POST['question_' . $question_id] . '<br>';

                                break;
                            case '10': // heading
                                $details_html .= "<h1 style='font-size:20px;'>" . $question_text . '</h1>';
                                break;
                            case '12': // sub heading
                                $details_html .= "<h3  style='font-size:16px;'>" . $question_text . '</h3>';
                                break;
                            case '15': // comments
                                $details_html .= '<b>User Input:</b> ' . $_POST['question_' . $question_id] . '<br>';
                                break;
                            case '16': // signature

                                $arr_ans = Model::factory('Formbuilder')->form_builder_answers(" and question_id='" . $question_id . "' and profile_id='" . $profile_id . "' and session_id='" . $session_id . "' ");

                                if ($arr_ans) {
                                    $signature_image = explode(',', $arr_ans[0]['question_answer']);
                                    $signature_name_text = explode(':::', $arr_ans[0]['signature_name_text']);
                                    $signature_name_text2 = explode(':::', $arr_ans[0]['signature_employer_text']);
                                    $signature_name_text3 = explode(':::', $arr_ans[0]['signature_email_text']);
                                    $signature_name_text4 = explode(':::', $arr_ans[0]['signature_phone_text']);


                                    for ($sig_cnt = 0; $sig_cnt < count($signature_image); ++$sig_cnt) {
                                        if (!empty($signature_name_text[$sig_cnt])) {
                                            $details_html .= '<br>Name<br><b>User Input:</b>' . $signature_name_text[$sig_cnt] . '<br>';
                                        }

                                        if (!empty($signature_name_text2[$sig_cnt])) {
                                            $details_html .= '<br>Employer/Company<br><b>User Input:</b>' . $signature_name_text2[$sig_cnt] . '<br>';
                                        }

                                        if (!empty($signature_name_text3[$sig_cnt])) {
                                            $details_html .= '<br>Email<br><b>User Input:</b>' . $signature_name_text3[$sig_cnt] . '<br>';
                                        }

                                        if (!empty($signature_name_text4[$sig_cnt])) {
                                            $details_html .= '<br>Phone<br><b>User Input:</b>' . $signature_name_text4[$sig_cnt] . '<br>';
                                        }

                                        if ($question_text != '') {
                                            $details_html .= '<br>' . $question_text . '<br>';
                                        }

                                        $details_html .= '<br><h2>Authority Signature</h2><br>';

                                        if (file_exists('images/signature/' . $signature_image[$sig_cnt]) && filesize('images/signature/' . $signature_image[$sig_cnt]) > 0) {
                                            $details_html .= '<img src="' . URL::base() . 'images/signature/' . $signature_image[$sig_cnt] . '" />';
                                        }
                                    }
                                }

                                break;
                            case '17': // upload button
                                if ($question_text != '') {
                                    $details_html .= '<br>' . $question_text . '<br>';
                                }

                                $arr_ans = Model::factory('Formbuilder')->form_builder_answers(" and question_id='" . $question_id . "' and profile_id='" . $profile_id . "' and session_id='" . $session_id . "' ");

                                if ($arr_ans) {
                                    $uploaded_image = $arr_ans[0]['question_answer'];
                                    $uploaded_file_arr = explode(',', $uploaded_image);
                                    $individual_file = "";
                                   

                                    $uploaded_file_arr = array_unique($uploaded_file_arr);

                                    $file_html = '';
                                    for ($cn = 0; $cn < count($uploaded_file_arr); ++$cn) {
                                        $individual_file = $uploaded_file_arr[$cn];

                                        $ext = explode('.', $individual_file);

                                        $file_ext = strtolower($ext[count($ext) - 1]);

                                        if ($file_ext == 'jpg' || $file_ext == 'jpeg' || $file_ext == 'gif' || $file_ext == 'png') {
                                            $data = getimagesize('images/formbuilder_upload/' . $individual_file);
                                            $img_width = $data[0];
                                            if (file_exists('images/formbuilder_upload/' . $individual_file) && filesize('images/formbuilder_upload/' . $individual_file) > 0) {
                                                $file_html .= '<img src="' . $baseurl . 'images/formbuilder_upload/' . $individual_file . '" width="265"><br><br>';
                                            }
                                        }
                                    }
                                    $details_html .= $file_html;  
                                    if ($uploaded_image_arr == '') {
                                        $uploaded_image_arr = $arr_ans[0]['question_answer'];
                                    } else {
                                        $uploaded_image_arr = $uploaded_image_arr . ',' . $arr_ans[0]['question_answer'];
                                    }
                                }

                                if (count($arr_ans) > 0) {
                                    $details_html .= 'See Attached.<br>';
                                }

                                break;
                            case '18':

                                $arr_ans = Model::factory('Formbuilder')->form_builder_answers(" and question_id='" . $question_id . "' and profile_id='" . $profile_id . "' and session_id='" . $session_id . "' ");

                                if ($arr_ans) {
                                    $p_signature_image = explode(',', $arr_ans[0]['participant_signature_image']);
                                    $p_emp_name = explode(':::', $arr_ans[0]['participant_employer_text']);
                                    for ($sig_cnt = 0; $sig_cnt < count($p_signature_image); ++$sig_cnt) {
                                        if ($question_text != '') {
                                            $details_html .= '<br>' . $question_text . '<br>';
                                        } else {
                                            $details_html .= '<br>Name<br>';
                                        }

                                        $details_html .= '<b>User Input:</b> ' . $_POST['question_' . $question_id][$sig_cnt] . '<br>';

                                        if (!empty($p_emp_name[$sig_cnt])) {
                                            $details_html .= '<br>Employer/Company<br><b>User Input:</b>' . $p_emp_name[$sig_cnt] . '<br>';
                                        }

                                        if (file_exists('images/signature/' . $p_signature_image[$sig_cnt]) && filesize('images/signature/' . $p_signature_image[$sig_cnt]) > 0) {
                                            $details_html .= '<img src="' . URL::base() . 'images/signature/' . $p_signature_image[$sig_cnt] . '" />';
                                        }
                                    }
                                }

                                break;
                            case '19': // location function

                                if ($question_text != '') {
                                    $details_html .= '<br>' . $question_text . '<br>';
                                }

                                $details_html .= '<b>User Input:</b> ' . $_POST['question_' . $question_id] . '<br>';

                                break;

                            case '22': // SWMS Hazard/Risk

                                $details_html .= '<br>';
                                $i = 0;
                                $j = 0;

                                while ($i < count($_POST['question_' . $question_id])) {
                                    if ($j == 0) {
                                        if ($question_text != '') {
                                            $details_html .= '<br>' . $question_text . '<br><br>';
                                        }
                                        $details_html .= 'Task | Activity';
                                    }
                                    if ($j == 1) {
                                        $details_html .= 'Potential Hazards';
                                    }

                                    if ($j == 2) {
                                        $details_html .= 'Risk Score (Before)';
                                    }

                                    if ($j == 3) {
                                        // mail('shaileshk@webplanex.com', 'FILE name', json_encode($file_name_array));
                                        // mail('shaileshk@webplanex.com', 'Question id', $question_id);
                                        // mail('shaileshk@webplanex.com', 'File string', $file_str);
                                        // if (!empty($file_name_array)) {
                                        //     $fd = explode(",", $file_name_array[$question_id]);

                                        //     foreach ($fd as $key => $attachment) {

                                        //         $uploaded_image_arr .= $attachment . ",";
                                        //         mail('shaileshk@webplanex.com', 'Attachment', $attachment);
                                        //         $details_html .= 'Attachment : <a target="_blank" href="' . url::base() . 'images/formbuilder_upload/' . $attachment . '"> View Attachment</a><br>';
                                        //     }
                                        // }
                                        if (isset($file_name_array[$question_id])) {
                                            foreach ($file_name_array[$question_id] as $key => $file) {
                                                $details_html .= '<div style="display:table;margin-left:15px;"><a href=' . $file . '>View Attachment</a></div>';

                                                // $details_html .= "Attachment Data";


                                                // $details_html .= '<a href="' . URL::base() . 'images/formbuilder_upload/' . $file . '" target="_blank">Attachment</a>';

                                                $file_name = end(explode('/', $file));

                                                $uploaded_image_arr .= $file_name . ',';
                                            }
                                        }
                                    }

                                    if ($j == 4) {
                                        $details_html .= 'Control Measures';
                                    }

                                    if ($j == 5) {
                                        $details_html .= 'Risk Score (After)';
                                    }

                                    $details_html .= '<br><b>User Input:</b> ' . $_POST['question_' . $question_id][$i] . '<br><br>';

                                    ++$i;
                                    ++$j;

                                    if ($j > 5) {
                                        $j = 0;
                                    }
                                }
                                break;

                            case '23': // Document Menu
                                $arr_ans = Model::factory('Formbuilder')->form_builder_answers(" and question_id='" . $question_id . "' and profile_id='" . $profile_id . "' and session_id='" . $session_id . "' ");
                                $arr_que = Model::factory('Formbuilder')->form_builder_questions(" and question_id='" . $question_id . "' and profile_id='" . $profile_id . "' ");
                                if ($arr_ans) {
                                    $uploaded_image = $arr_ans[0]['question_answer'];
                                    $uploaded_file_arr = explode(':::', $uploaded_image);

                                    $arr_doc_title = explode(',', $arr_que[0]['doc_title']);
                                    $arr_doc_key = explode(',', $arr_que[0]['question_text']);

                                    for ($cn = 0; $cn < count($uploaded_file_arr); ++$cn) {
                                        $individual_file = $uploaded_file_arr[$cn];

                                        $ext = explode('.', $individual_file);

                                        $file_ext = strtolower($ext[count($ext) - 1]);

                                        $doc_key = array_search($individual_file, $arr_doc_key);
                                        //if($file_ext=="jpg" || $file_ext=="jpeg" || $file_ext=="gif" || $file_ext=="png")
                                        //{

                                        //$data = getimagesize('images/formbuilder_upload/'.$individual_file);

                                        //$img_width = $data[0];

                                        //}

                                    }
                                    if (file_exists('images/formbuilder_upload/' . $individual_file) && filesize('images/formbuilder_upload/' . $individual_file) > 0) {
                                        $details_html .= '<br><a target="_blank" href="' . $baseurl . 'images/formbuilder_upload/' . $individual_file . '" >' . $arr_doc_title[$doc_key] . '</a>';
                                    }
                                    if ($uploaded_image_arr == '') {
                                        $uploaded_image_arr = $individual_file;
                                    } else {
                                        $uploaded_image_arr = $uploaded_image_arr . ',' . $individual_file;
                                    }
                                }

                                if (count($arr_ans) > 0) {
                                    $details_html .= '<br>See Attached<br>';
                                }

                                break;
                            case '24': // additional recipients
                                $additional_recipients = $_POST['email_recipient_additional_' . $question_id];
                                break;
                            case '25':
                                $details_html .= '<br>' . $question_text . '<br>';

                                for ($i = 0; $i < count($_POST['covid_question_' . $question_id]); $i++) {
                                    if ($i == 0) {
                                        $details_html .= '<br><b>Visitor name:</b> ' . (!empty($_POST['covid_question_' . $question_id][$i]) ? $_POST['covid_question_' . $question_id][$i] : '-') . '<br><br>';
                                    } else if ($i == 1) {
                                        $details_html .= '<br><b>Visitor phone:</b> ' . (!empty($_POST['covid_question_' . $question_id][$i]) ? $_POST['covid_question_' . $question_id][$i] : '-') . '<br><br>';
                                    } else if ($i == 2) {
                                        $details_html .= '<br><b>Date:</b> ' . (!empty($_POST['covid_question_' . $question_id][$i]) ? $_POST['covid_question_' . $question_id][$i] : '-') . '<br><br>';
                                    } else if ($i == 3) {
                                        $details_html .= '<br><b>Time:</b> ' . (!empty($_POST['covid_question_' . $question_id][$i]) ? $_POST['covid_question_' . $question_id][$i] : '-') . '<br><br>';
                                    } else if ($i == 4) {
                                        $details_html .= '<br><b>Venue Name:</b> ' . (!empty($_POST['covid_question_' . $question_id][$i]) ? $_POST['covid_question_' . $question_id][$i] : '-') . '<br><br>';
                                    } else if ($i == 5) {
                                        $location_address = (!empty($_POST['covid_question_' . $question_id][$i]) ? $_POST['covid_question_' . $question_id][$i] : '');
                                        $details_html .= '<br><b>Venue Address:</b> ' . (!empty($location_address) ? $location_address : '-') . '<br><br>';
                                    } else if ($i == 6) {
                                        $location_address = (!empty($_POST['covid_question_' . $question_id][$i]) ? $_POST['covid_question_' . $question_id][$i] : '');
                                        $details_html .= '<br><b>Location Description/Type:</b> ' . (!empty($location_address) ? $location_address : '-') . '<br><br>';
                                        if (!empty($location_address) && $location_address == 'Vehicle') {
                                            $details_html .= '<br><b>Vehicle Registration ID:</b> ' . (!empty($_POST['covid_question_' . $question_id][$i + 1]) ? $_POST['covid_question_' . $question_id][$i + 1] : '-') . '<br><br>';
                                        }
                                        if (!empty($location_address) && $location_address == 'Other') {
                                            $details_html .= '<br><b>Other details:</b> ' . (!empty($_POST['covid_question_' . $question_id][$i + 1]) ? $_POST['covid_question_' . $question_id][$i + 1] : '-') . '<br><br>';
                                        }
                                    }
                                }
                                break;
                        } //end switch case
                    } //end
                }
                //echo $details_html;exit;

                // build html for mail end
                //Log::instance()->add(Log::INFO, 'form email send start at '.date('Y-m-d H:i:s'));
                if ($form_submission_format == 1) {
                    $pdf_name = $profile_id . '_' . $session_id;

                    $pdf = new Pdf(array(
                        'binary' => '/usr/local/bin/wkhtmltopdf',
                        'margin-top'    => 10,
                        'margin-right'  => 10,
                        'margin-bottom' => 10,
                        'margin-left'   => 10,
                    ));
                    $pdf->addPage($details_html);

                    if (!$pdf->saveAs('images/formbuilder_upload/' . $pdf_name . '.pdf')) {
                        $error = $pdf->getError();
                        // print "<pre>"; print_r($error);print "</pre>";
                    }
                    $this->session->set('form_submission_pdf', $pdf_name);

                    if ($uploaded_image_arr == '') {
                        $uploaded_image_arr = $pdf_name . '.pdf';
                    } else {
                        $uploaded_image_arr = $uploaded_image_arr . ',' . $pdf_name . '.pdf';
                    }
                }

                $custom_email_subject = "";

                $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

                if (!empty($profile['form_email_tag'])) {
                    $custom_email_subject = $profile['form_email_tag'];
                }


                foreach ($recipient_email_arr as $recipient_email) {
                    if ($form_submission_format == 1) {
                        client_mail_for_formbuilder_answer_as_pdf($fromemail, $baseurl, $recipient_email['recipient_email'], $details_html, $profile_id, $profile_name, $profile_logo, $uploaded_image_arr, $custom_email_subject);
                    } else {
                        //send mail to client of this profile for form builder answer START
                        $mail = client_mail_for_formbuilder_answer($fromemail, $baseurl, $recipient_email['recipient_email'], $details_html, $profile_id, $profile_name, $profile_logo, $uploaded_image_arr, $custom_email_subject);
                        //send mail to client of this profile for form builder answer END
                    }
                }

                foreach ($additional_recipients as $address) {
			if (!empty($address)){
	            		if ($form_submission_format == 1) {
                        		client_mail_for_formbuilder_answer_as_pdf($fromemail, $baseurl, $address, $details_html, $profile_id, $profile_name, $profile_logo, $uploaded_image_arr, $custom_email_subject);
                    		} else {
                        		//send mail to client of this profile for form builder answer START
                        		$mail = client_mail_for_formbuilder_answer($fromemail, $baseurl, $address, $details_html, $profile_id, $profile_name, $profile_logo, $uploaded_image_arr, $custom_email_subject);
                        		//send mail to client of this profile for form builder answer END
                    		}
			}
                }

                // mail('support@scanlink.com.au', 'Mail Attachment', $uploaded_image_arr);
                setcookie('form_save_cookie', 'cookie', time() + (86400 * 30), "/");
                $this->session->set('form_submit_success', 1);
                //Log::instance()->add(Log::INFO, 'form submit end at '.date('Y-m-d H:i:s'));
                $this->request->redirect($this->request->referrer());
            }
        } catch (\Exception $e) {
            echo '<pre>';
            print_r($e->getMessage());
            echo '</pre>';
            exit;
            $this->session->set('form_submit_success', 2);
            mail('support@scanlink.com.au', 'Scanlink Live Error', json_encode($e));
            file_put_contents('form_submission_log.log', 'Form Submission Exception \n', FILE_APPEND);
            $this->request->redirect($this->request->referrer());
        }
    }

    public function action_addUserInfo()
    {
        $postArr['name'] = '';
        $postArr['surname'] = '';
        $postArr['mobile'] = '';
        $postArr['email'] = '';
        $postArr['id_profile'] = '';
        $postArr['created_at'] = date('Y-m-d h:i:s');

        if (isset($_POST['name']) && trim($_POST['name']) != '') {
            $postArr['name'] = addslashes($_POST['name']);
        }

        if (isset($_POST['surname']) && trim($_POST['surname']) != '') {
            $postArr['surname'] = addslashes($_POST['surname']);
        }

        if (isset($_POST['mobile_number']) && trim($_POST['mobile_number']) != '') {
            $postArr['mobile'] = addslashes($_POST['mobile_number']);
        }

        if (isset($_POST['email_address']) && trim($_POST['email_address']) != '') {
            $postArr['email'] = addslashes($_POST['email_address']);
        }

        if (isset($_POST['profile_id']) && trim($_POST['profile_id']) != '') {
            $postArr['id_profile'] = addslashes($_POST['profile_id']);
        }

        if (isset($_POST['datetime']) && trim($_POST['datetime']) != '') {
            $postArr['created_at'] = addslashes($_POST['datetime']);
        }

        /* echo "<pre>";
        print_r($postArr);
        exit; */

        if ($postArr['email'] != '' || $postArr['mobile'] != '' || $postArr['name'] != '') {
            Model::factory('contacts')->addContact($postArr);
        }

        Session::instance()->set('user_info', $postArr['id_profile']);

        exit;
    }

    public function action_documents()
    {
        $home = $this->request->detect_uri();

        $params = explode('/', $home);

        array_pop($params);
        $home = implode('/', $params);

        $profile = Model::factory('profiles')->getProfileByIdForMobile($params[1]);

        $client = Model::factory('client')->getClientById($profile['client_id']);

        if (empty($client) || empty($profile)) {
            $this->request->redirect(url::base() . 'mobile/error');
        }

        if ($profile['protect'] == '1' && ($this->session->get('clientuserid') == '' || $this->session->get('clientuserid') == null)) {
            $this->session->set('path', $home);
            $this->request->redirect(url::base() . 'mobile/authenticate');
        }

        $logo = Model::factory('logo')->getLogoByProfileId($params[1]);
        $pictures = Model::factory('picture')->getPicturesByProfileId($params[1]);
        $documents = Model::factory('documents')->getDocumentsByProfileId($params[1]);

        $this->template->headers->profile_id = $profile['id'];
        $this->template->headers->logo = $logo['logo_name'];
        $this->template->headers->home = $home;
        $this->template->footer->home = $home;
        $this->template->footer->profile = $profile;
        $this->template->content = View::factory('mobile/documents')
            ->bind('profile', $profile)
            ->bind('picture', $pictures[0]['picture_name'])
            ->bind('documents', $documents);
    }

    public function action_videos()
    {
        $home = $this->request->detect_uri();
        $params = explode('/', $home);
        array_pop($params);
        $home = implode('/', $params);

        $profile = Model::factory('profiles')->getProfileByIdForMobile($params[1]);
        $client = Model::factory('client')->getClientById($profile['client_id']);

        if (empty($client) || empty($profile)) {
            $this->request->redirect(url::base() . 'mobile/error');
        }

        if ($profile['protect'] == '1' && ($this->session->get('clientuserid') == '' || $this->session->get('clientuserid') == null)) {
            $this->session->set('path', $home);
            $this->request->redirect(url::base() . 'mobile/authenticate');
        }

        $logo = Model::factory('logo')->getLogoByProfileId($params[1]);
        $pictures = Model::factory('picture')->getPicturesByProfileId($params[1]);
        $videos = Model::factory('video')->getVideoByProfileId($params[1]);

        $this->template->headers->profile_id = $profile['id'];
        $this->template->headers->logo = $logo['logo_name'];
        $this->template->headers->home = $home;
        $this->template->footer->home = $home;
        $this->template->footer->profile = $profile;
        $this->template->content = View::factory('mobile/videos')
            ->bind('profile', $profile)
            ->bind('picture', $pictures[0]['picture_name'])
            ->bind('videos', $videos);
    }

    public function action_login()
    {
        $errors = array();
        $post = array();
        $postArr = array();
        if ($_POST) :
            //            echo "<pre>";
            //            print_r($_POST);
            //exit;
            $profile = trim($_POST['profile']);
            $pass = trim($_POST['password']);
            $errors['profile'] = $profile;
            if ($pass == '') :
                $errors['pass'] = "Password can't left blank..!";
            else :
                $postArr['profile'] = $profile;
                $postArr['password'] = ($pass);
                $profile = explode('/', $profile);
                $profile_id = $profile[1];
                $uri = $profile[0];
                $profile_id = $this->session->get('profile_id');
                $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);
                if ($profile['password'] != $pass) {
                    $errors['pass'] = 'Invalid Username or Password...!';
                } else {
                    $this->session->set('secure_profile', $profile['id']);
                    $this->request->redirect(URL::base() . $profile_id . '/' . $profile_id);
                }

            /* $client = Model::factory('client')->getClientById($profile['client_id']);
            $users = Model::factory('user')->getUsersByClientId($client['id']);
            $pass = ($pass);
            foreach ($users as $user) {
            if ($pass == $user['password']) {
            $sdata = $user["email"];
            $sdata = $this->session->set('clientuserid', $user['id']);
            $path = $this->session->get("path");
            $this->request->redirect(URL::base() . $path);
            exit;
            }
            } */

            endif;
        endif;

        $this->template->content = View::factory('mobile/authenticate')
            ->bind('errors', $errors);
    }

    public function action_authenticate()
    {
        $profile_id = $this->session->get('profile_id');

        $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

        $userdata = Model::factory('user')->getUserByID($profile['user_id']);
        $footer_logo = $userdata['footer_logo'];

        if ($footer_logo != '' && file_exists('images/logo/' . $footer_logo)) {
            $this->template->footer_logo = 'images/logo/' . $footer_logo;
        } else {
            $this->template->footer_logo = 'images/PoweredbyScanLink.png';
        }

        $path = $this->session->get('path');
        $this->template->content = View::factory('mobile/authenticate')
            ->bind('profile', $path)
            ->bind('profile_id', $profile_id);
    }

    public function action_error()
    {
        $message = 'Wrong Request';
        $this->template->content = View::factory('mobile/error')->bind('message', $message);
    }

    public function action_expired()
    {
        $message = 'You can not perform this action on expired profile.';
        $this->template->content = View::factory('mobile/error')->bind('message', $message);
    }

    public function action_save_userinfo()
    {
        $errors = array();
        $post = array();
        $postArr = array();
        if ($_POST) :

            $user_name = trim($_POST['user_name']);
            $user_email = trim($_POST['user_email']);

            $errors['user_name'] = $user_name;
            $errors['user_email'] = $user_email;

            if ($user_name == '') {
                $errors['pass'] = 'Please enter your name..!';
            } else {
                $postArr['user_name'] = $user_name;
            }

            if ($user_email == '') {
                $errors['pass'] = 'Please enter your e-mail..!';
            } else {
                $postArr['user_email'] = $user_email;
            }

            Model::factory('user_info')->insert_user_info($postArr);

        endif;
        //$this->request->redirect('mobile/index');
    }

    protected function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (strlen($phoneNumber) > 10) {
            $countryCode = substr($phoneNumber, 0, strlen($phoneNumber) - 10);
            $areaCode = substr($phoneNumber, -10, 3);
            $nextThree = substr($phoneNumber, -7, 3);
            $lastFour = substr($phoneNumber, -4, 4);

            $phoneNumber = '+' . $countryCode . ' (' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
        } elseif (strlen($phoneNumber) == 10) {
            $areaCode = substr($phoneNumber, 0, 3);
            $nextThree = substr($phoneNumber, 3, 3);
            $lastFour = substr($phoneNumber, 6, 4);

            $phoneNumber = '(' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
        } elseif (strlen($phoneNumber) == 7) {
            $nextThree = substr($phoneNumber, 0, 3);
            $lastFour = substr($phoneNumber, 3, 4);

            $phoneNumber = $nextThree . '-' . $lastFour;
        }

        return $phoneNumber;
    }
}

/**
 *  Signature to Image: A supplemental script for Signature Pad that
 *  generates an image of the signature’s JSON output server-side using PHP.
 *
 *  @project ca.thomasjbradley.applications.signaturetoimage
 *
 *  @author Thomas J Bradley <hey@thomasjbradley.ca>
 *
 *  @see http://thomasjbradley.ca/lab/signature-to-image
 *  @see http://github.com/thomasjbradley/signature-to-image
 *
 *  @copyright Copyright MMXI–, Thomas J Bradley
 *  @license New BSD License
 *
 *  @version 1.1.0
 */

/**
 *  Accepts a signature created by signature pad in Json format
 *  Converts it to an image resource
 *  The image resource can then be changed into png, jpg whatever PHP GD supports.
 *
 *  To create a nicely anti-aliased graphic the signature is drawn 12 times it's original size then shrunken
 *
 *  @param string|array $json
 *  @param array $options OPTIONAL; the options for image creation
 *    imageSize => array(width, height)
 *    bgColour => array(red, green, blue) | transparent
 *    penWidth => int
 *    penColour => array(red, green, blue)
 *    drawMultiplier => int
 *
 *  @return object
 */
function sigJsonToImage($json, $options = array())
{
    $defaultOptions = array(
        'imageSize' => array(198, 55),
        'bgColour' => array(0xff, 0xff, 0xff),
        'penWidth' => 2,
        'penColour' => array(0x00, 0x00, 0x00),
        'drawMultiplier' => 12,
    );

    $options = array_merge($defaultOptions, $options);

    $img = imagecreatetruecolor($options['imageSize'][0] * $options['drawMultiplier'], $options['imageSize'][1] * $options['drawMultiplier']);

    if ($options['bgColour'] == 'transparent') {
        imagesavealpha($img, true);
        $bg = imagecolorallocatealpha($img, 0, 0, 0, 127);
    } else {
        $bg = imagecolorallocate($img, $options['bgColour'][0], $options['bgColour'][1], $options['bgColour'][2]);
    }

    $pen = imagecolorallocate($img, $options['penColour'][0], $options['penColour'][1], $options['penColour'][2]);
    imagefill($img, 0, 0, $bg);

    if (is_string($json)) {
        $json = json_decode(stripslashes($json));
    }

    foreach ($json as $v) {
        drawThickLine($img, $v->lx * $options['drawMultiplier'], $v->ly * $options['drawMultiplier'], $v->mx * $options['drawMultiplier'], $v->my * $options['drawMultiplier'], $pen, $options['penWidth'] * ($options['drawMultiplier'] / 2));
    }

    $imgDest = imagecreatetruecolor($options['imageSize'][0], $options['imageSize'][1]);

    if ($options['bgColour'] == 'transparent') {
        imagealphablending($imgDest, false);
        imagesavealpha($imgDest, true);
    }

    imagecopyresampled($imgDest, $img, 0, 0, 0, 0, $options['imageSize'][0], $options['imageSize'][0], $options['imageSize'][0] * $options['drawMultiplier'], $options['imageSize'][0] * $options['drawMultiplier']);
    imagedestroy($img);

    return $imgDest;
}

/**
 *  Draws a thick line
 *  Changing the thickness of a line using imagesetthickness doesn't produce as nice of result.
 *
 *  @param object $img
 *  @param int $startX
 *  @param int $startY
 *  @param int $endX
 *  @param int $endY
 *  @param object $colour
 *  @param int $thickness
 *
 *  @return void
 */
function drawThickLine($img, $startX, $startY, $endX, $endY, $colour, $thickness)
{
    $angle = (atan2(($startY - $endY), ($endX - $startX)));

    $dist_x = $thickness * (sin($angle));
    $dist_y = $thickness * (cos($angle));

    $p1x = ceil(($startX + $dist_x));
    $p1y = ceil(($startY + $dist_y));
    $p2x = ceil(($endX + $dist_x));
    $p2y = ceil(($endY + $dist_y));
    $p3x = ceil(($endX - $dist_x));
    $p3y = ceil(($endY - $dist_y));
    $p4x = ceil(($startX - $dist_x));
    $p4y = ceil(($startY - $dist_y));

    $array = array(0 => $p1x, $p1y, $p2x, $p2y, $p3x, $p3y, $p4x, $p4y);
    imagefilledpolygon($img, $array, (count($array) / 2), $colour);
} 

function resizeImage($sourceImage, $targetImage, $maxWidth, $maxHeight, $quality = 80)
{
    // Obtain image from given source file.
    if (!$image = @imagecreatefromjpeg($sourceImage))
    {
        return false;
    }

    // Get dimensions of source image.
    list($origWidth, $origHeight) = getimagesize($sourceImage);

    if ($maxWidth == 0)
    {
        $maxWidth  = $origWidth;
    }

    if ($maxHeight == 0)
    {
        $maxHeight = $origHeight;
    }

    // Calculate ratio of desired maximum sizes and original sizes.
    $widthRatio = $maxWidth / $origWidth;
    $heightRatio = $maxHeight / $origHeight;

    // Ratio used for calculating new image dimensions.
    $ratio = min($widthRatio, $heightRatio);

    // Calculate new image dimensions.
    $newWidth  = (int)$origWidth  * $ratio;
    $newHeight = (int)$origHeight * $ratio;

    // Create final image with new dimensions.
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
    imagejpeg($newImage, $targetImage, $quality);

    // Free up the memory.
    imagedestroy($image);
    imagedestroy($newImage);

    return true;
}

// End Welcome
