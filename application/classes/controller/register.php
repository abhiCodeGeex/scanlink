<?php

defined('SYSPATH') or die('No direct script access.');

/* * **
 * **** register client and add user 
 * ** */

class Controller_register extends Controller_Template_Master
{
    /*     * * 
     * *** Page header and footer set
     * ** */

    public function before()
    {
        /**  parent before	* */
        parent::before();


        // Default Head view file
        $this->template->head = View::factory('template/head');

        // Set the default Page Title
        $this->template->head->title = 'Register with ScanLink';

        // All CSS Stylesheets
        $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'), array('href' => 'styles/prettyPhoto.css', 'media' => 'screen'));

        // All JS Libraries and Scripts
        $this->template->head->javascripts = array('js/jquery.min.js', 'js/coin-slider.min.js', 'js/jquery.validate.min.js', 'js/jquery.prettyPhoto.js', 'js/jquery.colorbox.js', 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js');
        $this->template->head->extrascripts = 'slider';

        // Default Header view file
        $this->template->headers = View::factory('template/headers');

        // Default menu
        $this->template->headers->menu = View::factory('template/menu');

        if ($this->voc_user_logged_in == 1) {
            $this->template->headers->menu = View::factory('template/vocmenu');
        }

        if ($this->session->get('clientuserid') || $this->voc_user_logged_in == 1) :
            $this->template->headers->menu->logoutlnk = $this->session->get('clientusername');
        endif;


        $this->template->headers->menu->voc_user_logged_in = $this->voc_user_logged_in;

        $this->template->headers->menu->menu = $this->menu;
        $this->template->headers->menu->currentactive = $this->request->controller() . "/" . $this->request->action();
        // Default Content template
        $this->template->footer = View::factory('template/footer');
        //endif;
        if ($this->template->footer) :
            // All JS Libraries and Scripts
            $this->template->footer->scripts = array();
        endif;
    }

    /*     * * Register step 1
     * *** Middle portion of register page step 1 
     * ** */

    public function action_index()
    {

        if ($this->session->get('clientuserid')) :
            $this->request->redirect(URL::base() . 'dashboard/index');
        endif;

        // after validation value to be return 
        $fields = array();
        // after validation error to be  return 
        $errors = array();
        /*         * *** validation on press of next button *** */

        /*         * ** captcha code start  **** */
        $captcha = Captcha::instance();
        /*         * ** captcha code end  **** */


        if ($this->request->method() === Request::POST) {

            /*             * *** get post variable *** */
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $company_name = trim($_POST['company_name']);
            $billing_address = trim($_POST['billing_address']);
            $email = trim($_POST['email']);
            $town = trim($_POST['town']);
            $phone = trim($_POST['phone']);
            $postal_code = trim($_POST['postal_code']);
            $client_reseller_code = trim($_POST['client_reseller_code']); //code of reseller who is creating new user
            $password = trim($_POST['password']);
            $cpassword = trim($_POST['cpassword']);
            $captcha = trim($_POST['captcha']);



            /*             * *** Validation START  *** */

            if ($client_reseller_code != '') //check only when reseller code entered by user
            {
                $is_reseller_code_exists = Model::factory('client')->isResellerCodeExit($client_reseller_code);

                if ($is_reseller_code_exists == "")
                    $errors['client_reseller_code'] = "Enter a valid Reseller Code.";
            }

            if ($first_name == '') {
                $errors['first_name'] = "Enter a first name.";
            }

            if ($last_name == '') {
                $errors['last_name'] = "Enter a last name.";
            }

            if ($company_name == '') {
                $errors['company_name'] = "Enter a company name.";
            }

            if ($billing_address == '') {
                $errors['billing_address'] = "Enter a billing address.";
            }

            if ($email == '') {
                $errors['email'] = 'Enter a email.';
            } else {
                // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                //     $errors['email'] = 'Enter a valid email.';
                // } else {
                $emailexist = Model::factory('user')->getIdByEmail($email);
                if (!empty($emailexist)) {
                    $errors['email'] = 'This email is already exists.';
                }
                // }
            }

            if ($town == '') {
                $errors['town'] = "Enter a town.";
            }
            if ($phone == '') {
                $errors['phone'] = "Enter a phone.";
            }
            if ($postal_code == '') {
                $errors['postal_code'] = "Enter a postal code.";
            }
            if ($password == '') {
                $errors['password'] = "Enter a password.";
            } else {
                if ((strlen($password) < 5) || (strlen($password) > 12)) {
                    $errors['password'] = "Password must between 5-12 character..!";
                }
            }
            if ($cpassword == '') {
                $errors['cpassword'] = "Enter a confirm password.";
            }
            if ($password != '' && $cpassword != '' && $password != $cpassword) {
                $errors['cpassword'] = "Password and confirm password does not match.";
            }
            if ($captcha == '') {
                $errors[''] = "Enter a captcha.";
            } else {
                if (!Captcha::valid($captcha)) {
                    $errors['captcha'] = "Enter a valid captcha.";
                }
            }
            /*             * *** Validation END  *** */



            /*             * * assign field variable ** */
            $fields['first_name'] = $first_name;
            $fields['last_name'] = $last_name;
            $fields['company_name'] = $company_name;
            $fields['billing_address'] = $billing_address;
            $fields['email'] = $email;
            $fields['town'] = $town;
            $fields['phone'] = $phone;
            $fields['postal_code'] = $postal_code;
            $fields['password'] = $password;
            $fields['client_reseller_code'] = $client_reseller_code;

            /*             * * if sucessfully complete the step the redirect to next step ** */
            if (empty($errors)) {

                //give one free code
                $fields['noorder'] = 1; // 1 free code
                $fields['amount'] = "";
                $fields['totalsubscrption'] = "";

                $this->session->set('registerdata', $fields);
                //$this->request->redirect('register/secondstep/');

                // now bypass step 2 and 3				
                $this->action_bypass_steps();
                exit();
            } else {
                /*                 * ** captcha code start  **** */
                $captcha = Captcha::instance();
                /*                 * ** captcha code end  **** */
            }
        }



        // Default Content template
        $this->template->content = View::factory('register/index')
            ->set('captchacode', $captcha)
            ->bind('errors', $errors)
            ->bind('fields', $fields);
    }

    /*     * * Register step 2
     * *** Middle portion of register page step 2 
     * ** */

    public function action_secondstep()
    {
        if ($this->session->get('clientuserid')) :
            $this->request->redirect(URL::base() . 'dashboard/index');
        endif;
        /*         * * get session data of register page step 1 ** */
        $registerdata = $this->session->get('registerdata');
        /*         * ** if empty session redirect to step 1 *** */
        if (empty($registerdata)) {
            $this->request->redirect('register/index/');
            exit();
        }

        // Default Content template
        $this->template->content = View::factory('register/selectcode');
    }

    /*     * * Register step 2
     * *** process of step 2 after pressing button calculate and order summary 
     * ** */

    public function action_getData()
    {

        $this->session->delete('reseller_data');

        /*         * * get session data of register page step 1 ** */
        $registerdata = $this->session->get('registerdata');

        $errors = array();

        $noorder = isset($_POST['no_codes']) ? $_POST['no_codes'] : '';
        $submit_button_press = isset($_POST['submit_button_press']) ? $_POST['submit_button_press'] : false;

        if ($noorder == '') {
            $errors['no_codes'] = 'Enter a number of code required.';
        } else if ($noorder > 1000) {
            $errors['no_codes'] = 'Enter a number of code less than 1000.';
        }

        $permonthamount = 0.00;
        $totalsubscrption = 0.00;

        if (empty($errors)) {
            $codeamount = Model::factory('codeprising')->getAmountByQuantity($noorder);
            $permonthamount = number_format($codeamount[0]['amount'], 2);
            $totalsubscrption = number_format($codeamount[0]['amount'] * $noorder * 12, 2);
        }
        if ($submit_button_press) {
            $registerdata['noorder'] = $noorder;
            $registerdata['amount'] = $permonthamount;
            $registerdata['totalsubscrption'] = $totalsubscrption;
            $this->session->set('registerdata', $registerdata);
        }
        $returnarray = array();
        $returnarray['errors'] = $errors;
        $returnarray['amount'] = "$permonthamount";
        $returnarray['totalsubscrption'] = "$totalsubscrption";

        // store codes amount into session
        $this->session->set('code_amount', $_POST['no_codes']);
        $this->session->set('per_code_amount', $permonthamount);

        echo json_encode($returnarray);
        exit();
    }

    /*     * * Register step 3
     * *** Middle portion of register page step 3 
     * ** */

    public function action_thirdstep()
    {
        if ($this->session->get('clientuserid')) :
            $this->request->redirect(URL::base() . 'dashboard/index');
        endif;
        /*         * * get session data of register page step 1 ** */
        $registerdata = $this->session->get('registerdata');
        /*         * ** if empty session redirect to step 1 *** */
        if (empty($registerdata)) {
            $this->request->redirect('register/index/');
            exit();
        }
        /*         * ** if order summary is not set in  session redirect to step 2 *** */
        if (!isset($registerdata['noorder'])) {
            $this->request->redirect('register/index/');
            exit();
        }
        /*         * ** registration process complete *** */
        if ($this->request->method() === Request::POST) {

            //insert into client table to register as a client
            //$registerdata['client_url'] = $this->clean($registerdata['first_name'].$registerdata['last_name']);
            $registerdata['client_url'] = $this->clean($registerdata['company_name']);
            $registerdata['password'] = $registerdata['password'];

            $clientid = Model::factory('client')->insertClient($registerdata);

            //insert into user table client as a user
            $userid = Model::factory('user')->insertUser($registerdata, $clientid[0]);

            $registerdata['no_of_codes'] = $registerdata['noorder'];
            $registerdata['per_code_amount'] = $registerdata['amount'];



            //code purchase entry 
            Model::factory('codepurchase')->insertcodepurchase($clientid[0], $userid[0], $registerdata);


            // send mail  START
            include_once("mailsend.php");
            $adminemail = Model::factory('settings')->getValueByTitle('contact_email');
            $adminemail = "support@scanlink.com.au";
            $baseurl = URL::base();

            //send mail to user START 
            registration_mail_client($adminemail, $baseurl, $registerdata['email'], $registerdata['first_name'], $registerdata['last_name'], $registerdata['password']);
            //send mail to user END
            //send mail to admin START 
            registration_mail_admin($adminemail, $baseurl, $registerdata['email'], $registerdata['first_name'], $registerdata['last_name']);
            //send mail to admin END
            // send mail  END
            //login data saved
            $sdata = $this->session->set('client_id', $clientid[0]);
            $sdata = $this->session->set('clientusername', $registerdata['email']);
            $sdata = $this->session->set('clientuserid', $userid[0]);

            //uset session data 
            $this->session->delete('registerdata');

            //redirect to thank you page
            $this->request->redirect('dashboard/mastercode');
            //$this->request->redirect('register/thankyou/');
            exit();
        }

        /* echo $registerdata['noorder'];echo "<br/>";
          echo $registerdata['totalsubscrption']; */

        // Default Content template
        $this->template->content = View::factory('register/ordersummary')
            ->bind('nocode', $registerdata['noorder'])
            ->bind('totalsubscription', $registerdata['totalsubscrption']);
    }

    /*     * * Register step 4
     * *** Thank you for register
     * ** */

    public function action_thankyou()
    {
        // Default Content template
        $this->template->content = View::factory('register/thankyou');
    }


    public function action_bypass_steps()
    {
        if ($this->session->get('clientuserid')) :
            $this->request->redirect(URL::base() . 'dashboard/index');
        endif;
        /*         * * get session data of register page step 1 ** */
        $registerdata = $this->session->get('registerdata');
        /*         * ** if empty session redirect to step 1 *** */
        if (empty($registerdata)) {
            $this->request->redirect('register/index/');
            exit();
        }

        /** ** registration process complete *** */


        //insert into client table to register as a client
        //$registerdata['client_url'] = $this->clean($registerdata['first_name'].$registerdata['last_name']);
        $registerdata['client_url'] = $this->clean($registerdata['company_name']);
        $registerdata['password'] = $registerdata['password'];
        $registerdata['is_password_change'] = 1;

        $clientid = Model::factory('client')->insertClient($registerdata);

        //insert into user table client as a user
        $userid = Model::factory('user')->insertUser($registerdata, $clientid[0]);

        $registerdata['no_of_codes'] = 1;
        $registerdata['per_code_amount'] = $registerdata['amount'];

        $this->session->set('register_complete', 1); // use for popup on home page

        //code purchase entry 
        $free_code = 1;
        Model::factory('codepurchase')->insertcodepurchase($clientid[0], $userid[0], $registerdata, $free_code);


        // send mail  START
        include_once("mailsend.php");
        $adminemail = Model::factory('settings')->getValueByTitle('contact_email');
        $adminemail = "support@scanlink.com.au";
        $baseurl = URL::base();

        //send mail to user START 
        registration_mail_client($adminemail, $baseurl, $registerdata['email'], $registerdata['first_name'], $registerdata['last_name'], $registerdata['password']);
        //send mail to user END

        $client_reseller_name = "";
        if ($registerdata['client_reseller_code'] != "") //get reseller name if reseller code is entered by user
        {
            $client_reseller_name = Model::factory('client')->get_client_name_using_reseller_code($registerdata['client_reseller_code']);
        }

        //send mail to admin START 
        registration_mail_admin($adminemail, $baseurl, $registerdata['email'], $registerdata['first_name'], $registerdata['last_name'], $client_reseller_name);
        //send mail to admin END

        // send mail  END	    

        //unset session data 
        $this->session->delete('registerdata');

        //redirect to thank you page
        $this->request->redirect('welcome/index');

        exit();
    }

    /*     * * clean the string 
     * *** remove all space and special charcter
     * *** replace cpatial letter to lower letter
     * ** */

    protected function clean($string)
    {
        $string = strtolower($string); // convert to lower case
        $string = preg_replace('/\s+/', '', $string); // Removes white spaces
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
