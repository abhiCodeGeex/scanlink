<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Voclogin extends Controller_Template_Master
{

        public function before()
        {

                parent::before();

                // Default Head view file
                $this->template->head = View::factory('template/head_home');

                // Set the default Page Title
                $this->template->head->title = 'Welcome to ScanLink';

                // All CSS Stylesheets
                $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'), array('href' => 'styles/prettyPhoto.css', 'media' => 'screen'));

                // All JS Libraries and Scripts
                $this->template->head->javascripts = array('js/jquery.min.js', 'js/coin-slider.min.js', 'js/jquery.validate.min.js', 'js/jquery.prettyPhoto.js', 'js/jquery.colorbox.js', 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', 'js/jquery.jcarousel.min.js');
                $this->template->head->extrascripts        = 'slider';

                // Default Header view file
                $this->template->headers = View::factory('template/headers');

                // Default menu
                $this->template->headers->menu = View::factory('template/vocmenu');

                $this->template->headers->menu->voc_user_logged_in = $this->voc_user_logged_in;
                $this->template->headers->menu->menu = '';
                /* WP_MS START  */

                /* WP_MS END  */
                $this->template->headers->menu->currentactive = $this->request->controller() . "/" . $this->request->action();
                // Default Content template
                $this->template->footer = View::factory('template/footer');

                if ($this->template->footer) :
                        // All JS Libraries and Scripts
                        $this->template->footer->scripts = array();
                endif;
        }




        public function action_index()
        {
                if ($this->session->get('voc_user_id') != "") {
                        $this->request->redirect(URL::base() . 'dashboard/voc_user');
                        exit;
                }

                if (isset($_POST['email']) && isset($_POST['password'])) {
                        $email = addslashes(trim($_REQUEST['email']));
                        $password = addslashes(trim($_REQUEST['password']));
                        if ($email != "" && $password != "") {
                                $userdetail = Model::factory('voc')->get_voc_user_by_cond(" email='" . $email . "' and password='" . $password . "'");

                                if (count($userdetail) > 0) {
                                        $profile_id_array = array();
                                        foreach ($userdetail as $detail) {
                                                array_push($profile_id_array, $detail["profile_id"]);
                                        }


                                        $sdata = $this->session->set('clientusername', $email);
                                        $sdata = $this->session->set('voc_user_id', $userdetail[0]["voc_user_id"]);
                                        $sdata = $this->session->set('profile_id_array', $profile_id_array);

                                        if (count($profile_id_array) > 0)
                                                $this->request->redirect(URL::base() . 'dashboard/voc_user');
                                        else
                                                $this->request->redirect(URL::base() . 'welcome/index');
                                        exit;
                                } else {
                                        $errors = "Email address or password does not match..!";
                                        $return['error'] = '2';
                                        $return['message'] = $errors;
                                }
                        } else {
                                $errors = "Please provide your email address...";
                                $return['error'] = '2';
                                $return['message'] = $errors;
                        }
                }
                // Default Content template
                $this->template->currentPage = 'home';
                $this->template->content = View::factory('voclogin')
                        ->bind('errors', $errors)
                        ->bind('success', $success);
        }


        public function action_forgot()
        {
                if (isset($_POST['email'])) {
                        $email = addslashes(trim($_REQUEST['email']));
                        if ($email != "") {
                                $userdetail = Model::factory('voc')->get_voc_user_by_cond(" email='" . $email . "'");

                                if (count($userdetail) > 0) {

                                        $txtnewpass = $userdetail[0]["password"];


                                        $to = $email;
                                        $adminemail = "support@scanlink.com.au";

                                        include_once("mailsend.php");
                                        forgot_mail_client_voc($to, $txtnewpass, $adminemail, url::base());

                                        $success = "Mail sent successfully. Please Check your account..!";
                                        $return['error'] = '1';
                                        $return['message'] = $success;
                                } else {
                                        $errors = "This email address does not exist..!";
                                        $return['error'] = '2';
                                        $return['message'] = $errors;
                                }
                        } else {
                                $errors = "Please provide your email address...";
                                $return['error'] = '2';
                                $return['message'] = $errors;
                        }
                }
                // Default Content template
                $this->template->currentPage = 'home';
                $this->template->content = View::factory('vocforgot')
                        ->bind('errors', $errors)
                        ->bind('success', $success);
        }
} // End Voclogin
