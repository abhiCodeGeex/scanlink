<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller_Template_Master
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

        $this->template->headers->menu->menu = $this->menu;

        $this->template->headers->menu->voc_user_logged_in = $this->voc_user_logged_in;

        /* WP_MS START  */
        $this->template->headers->menu->menuSubmenu = $this->menuSubmenu;
        /* WP_MS END  */
        $this->template->headers->menu->currentactive = $this->request->controller() . '/' . $this->request->action();
        // Default Content template
        $this->template->footer = View::factory('template/footer');
        if (!$this->session->get('clientuserid')) :
            $this->template->footer->loginbox = View::factory('template/loginbox');
            $this->template->footer->loginbox->currentactive = $this->request->controller() . '/' . $this->request->action();
        endif;
        if ($this->template->footer) :
            // All JS Libraries and Scripts
            $this->template->footer->scripts = array();
        endif;
    }

    public function monthsBetween($startDate, $endDate)
    {
        $retval = '';
        // Assume YYYY-mm-dd - as is common MYSQL format
        $splitStart = explode('-', $startDate);
        $splitEnd = explode('-', $endDate);
        if (is_array($splitStart) && is_array($splitEnd)) {
            $difYears = $splitEnd[0] - $splitStart[0];
            $difMonths = $splitEnd[1] - $splitStart[1];
            $difDays = $splitEnd[2] - $splitStart[2];
            $retval = ($difDays > 0) ? $difMonths : $difMonths - 1;
            $retval += $difYears * 12;
        }

        return $retval;
    }

    public function action_sendusernotification()
    {
        $users = Model::factory('user')->getAllUsers();
        if (is_array($users) && sizeof($users) > 0) {
            foreach ($users as $user) {
                $month = $this->monthsBetween(date('Y-m-d H:i:s', time()), $user['expire_at']) . '<br>';
                $mailTo = $user['email'];
                $fromemail = Model::factory('settings')->getValueByTitle('contact_email');
                $subject = 'ScanLink re-new account notification.';
                $message = '<html>
				<head>
				<title>ScanLink re-new account notification.</title>
				</head>
				<body>
				<p>Hi there!</p>
				<p>As usual, ScanLink is always happy to help!</p>
				<p>This is a notification from ScanLink that your account is going to expire on ' . $user['expire_at'] . ', so do not delay and contact admin to re-new your account.</p>
				<p>Best regards,</p>
				<p><img src="' . URL::base() . 'images/qr-img-regards.png" /></p>
				</body>
				</html>';
                // To send HTML mail, the Content-type header must be set
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
                $dataArr = array();
                /*
                ** notice : month == 2 (spend 10, 2 rmain) month == 1 (spend 11, 1 remain)
                ** notice = 0,1,2 for mail sent flag as per month.
                **/

                if ($month == 2 && $user['notice'] == 0) {
                    DB::update('users')->set(array('notice' => 1))->where('id', '=', $user['id'])->execute();
                    @mail($mailTo, $subject, $message, $headers);
                } elseif ($month == 1 && $user['notice'] == 1) {
                    DB::update('users')->set(array('notice' => 2))->where('id', '=', $user['id'])->execute();
                    @mail($mailTo, $subject, $message, $headers);
                }
            }
        }
    }

    public function action_index()
    {   //echo $this->session->get('register_complete');

        // below to check popup show for nearly done registration
        if ($this->session->get('register_complete')) {
            $nearly_done_popup_show = true;
        } else {
            $nearly_done_popup_show = false;
        }

        // Default Content template
        $this->template->currentPage = 'home';
        $this->template->content = View::factory('index')
            ->bind('nearly_done_popup_show', $nearly_done_popup_show);

        $this->template->content->nearly_done_popup = View::factory('template/nearly_done')
            ->bind('nearly_done_popup_show', $nearly_done_popup_show);

        //unset session data
        $this->session->delete('register_complete');

        $this->action_login();
    }

    /*public function action_instruction()
    {
        // Default Content template
        $this->template->content = View::factory('instruction');
    }*/
    public function action_gallery()
    {
        // Default Content template
        $images = Model::factory('gallery')->getAllImages();
        $this->template->content = View::factory('gallery')->bind('images', $images);
    }

    public function action_packages()
    {
        // Default Content template
        $this->template->content = View::factory('packages');
    }

    public function action_testimonial()
    {
        $testimonials = Model::factory('testimonial')->getAllTestimonial();
        // Default Content template
        $this->template->content = View::factory('testimonial')
            ->bind('testimonials', $testimonials);
    }

    public function action_benefits()
    {
        // Default Content template
        $this->template->content = View::factory('benefits');
    }

    public function action_forgot()
    {
        $return = array();
        // Default Content template
        $errors = '';
        $success = '';
        if (isset($_REQUEST['btnforgot']) && $_REQUEST['btnforgot'] != '') {
            $email = addslashes(trim($_REQUEST['txtemail']));
            if ($email) {
                $userdetail = Model::factory('user')->getIdByEmail($email);

                if (!empty($userdetail)) {
                    $return['test'] = $userdetail;
                    $return['id'] = $userdetail[0]['id'];

                    $txtnewpass = $this->generateRandStr(10);

                    mail('support@scanlink.com.au','Core php mail send',$txtnewpass);


                    Model::factory('user')->editpass($userdetail[0]['id'], array('password' => $txtnewpass));

                    $userdetail = $userdetail[0];
                    $to = $userdetail['email'];
                    $adminemail = 'support@scanlink.com.au';

                    //$fromemail = Model::factory('settings')->getValueByTitle('contact_email');

                    include_once 'mailsend.php';
                    forgot_mail_client($to, $txtnewpass, $adminemail, url::base());

                    $success = 'Mail sent successfully. Please Check your account..!';
                    $return['error'] = '1';
                    $return['message'] = $success;
                } else {
                    $errors = 'This email address is not exists..!';
                    $return['error'] = '2';
                    $return['message'] = $errors;
                }
            } else {
                $errors = 'Please provide your email address...';
                $return['error'] = '2';
                $return['message'] = $errors;
            }
            echo json_encode($return);
            exit;
        } else {
            $this->request->redirect('welcome/index');
            exit();
        }
        $this->template->content = View::factory('forgot')
            ->bind('errors', $errors)
            ->bind('success', $success);
    }

    public function action_login()
    {
        //if(isset($this->template->footer->loginbox)):
        if (!$this->session->get('clientuserid')) :

            $errors = array();
            $post = array();
            $postArr = array();
            if ($_POST) :
                $email = trim($_POST['email']);
                $pass = trim($_POST['password']);

                if ($email == '') {
                    $errors['eadd'] = "Email address can't left blank..!";
                } else if (strpos('@', $email) != false) {
                    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$/i", $email)) {
                        $errors['eadd'] = 'Please enter valid email address..!';
                    }
                } elseif ($pass == '') {

                    $errors['pass'] = "Password can't left blank..!";
                } else {

                    $postArr['email'] = $email;
                    $postArr['password'] = ($pass);

                    $data = Model::factory('user')->getlogin($postArr);

                    if (empty($data)) {
                        $errors['eadd'] = 'Invalid Username or Password...!';
                        //elseif($data[0]['expire_at'] < date('Y-m-d H:i:s')):
                        //	$errors['eadd'] = "Oops! your account is expired, please contact admin to re-new account.";
                    } else {

                        $client_data = Model::factory('client')->getClientById($data[0]['client_id']);


                        if ($client_data['approve'] == '0') {
                            $errors['eadd'] = 'Oops! your account is blocked, please contact admin to un-block account.';
                        } else {
                            $user_data = Model::factory('user')->getUserByID($data[0]['id']);

                            //check whether user is sub user and blocked
                            if ($data[0]['is_sub_user'] == '1' && $user_data['status'] == '0') {
                                $errors['eadd'] = 'Oops! your account is blocked.';
                            } else {
                                $sdata = $this->session->set('client_id', $data[0]['client_id']);
                                $sdata = $this->session->set('clientusername', $email);
                                $sdata = $this->session->set('clientuserid', $data[0]['id']);
                                $sdata = $this->session->set('is_sub_user', $data[0]['is_sub_user']);
                                $sdata = $this->session->set('is_password_change', $data[0]['is_password_change']);
                                $this->request->redirect(URL::base() . 'dashboard/mastercode');
                            }
                        }
                    }
                }
            endif;
            //$this->template->footer->loginbox->post = $_POST;
            //$this->template->footer->loginbox->errors = $errors;
            $this->template->headers->menu->post = $_POST;
            $this->template->headers->menu->errors = $errors;

        endif;
    }

    public function action_login_from_other_user() //only needed in add form from another account
    {
        $errors = array();
        $post = array();
        $postArr = array();
        if ($_REQUEST) :
            $email = trim($_REQUEST['email']);
            $pass = trim($_REQUEST['password']);

            if ($email == '') :
                $data_ret = "Email address can't left blank..!";
            elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$/i", $email)) :
                $data_ret = 'Please enter valid email address..!';
            elseif ($pass == '') :
                $data_ret = "Password can't left blank..!";
            else :

                $postArr['email'] = $email;
                $postArr['password'] = $pass;
                $data = Model::factory('user')->getlogin($postArr);

                if (empty($data)) :

                    $data_ret = 'Invalid Username or Password...!';
                else :

                    $client_data = Model::factory('client')->getClientById($data[0]['client_id']);

                    if ($client_data['approve'] == '0') {
                        $data_ret = 'Oops! your account is blocked, please contact admin to un-block account.';
                    } else {
                        $user_data = Model::factory('user')->getUserByID($data[0]['id']);

                        //check whether user is sub user and blocked
                        if ($data[0]['is_sub_user'] == '1' && $user_data['status'] == '0') {
                            $data_ret = 'Oops! your account is blocked.';
                        } else {
                            $data_ret = $data[0]['id'];
                        }
                    }

                endif;
            endif;
        endif;

        echo $data_ret;
        exit;
    }

    public function action_contact()
    {
        //$captcha = new Captcha('alpha');
        // Set the Page Title
        $this->template->head->title = 'Contact US';

        $captcha = Captcha::instance();

        $this->template->content = View::factory('contact')
            ->bind('captcha', $captcha);

        // Default Content template
        if (isset($_POST['submit'])) {
            if (trim($_POST['name']) != '' && trim($_POST['email']) != '' && trim($_POST['comments']) != '' && trim($_POST['captcha']) != '') {
                if (Captcha::valid($_POST['captcha'])) {
                    $to = Model::factory('settings')->getValueByTitle('contact_email');
                    $name = trim($_POST['name']);
                    $email = trim($_POST['email']);
                    $comments = trim($_POST['comments']);

                    // subject
                    $subject = 'Contact Us - From ' . $name;

                    // message
                    $message = '
				<html>
				<head>
				  <title>Contact Us - From ' . $name . '</title>
				</head>
				<body>
				  <p>Here are the Contact Us Information</p>
				  <table cellpadding="3" cellspacing="0" >
					<tr>
					  <th align="right">Name : </th>
					  <td>' . $name . '</td>
					</tr>
					<tr>
					  <th align="right">Email : </th>
					  <td>' . $email . '</td>
					</tr>
					<tr>
					  <th align="right">Comments : </th>
					  <td>' . $comments . '</td>
					</tr>
				  </table>
				</body>
				</html>
				';

                    // To send HTML mail, the Content-type header must be set
                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                    // Additional headers
                    if (isset($_POST['sendCopy'])) {
                        $headers .= 'CC: ScanLink <' . $email . '>' . "\r\n";
                    }

                    $headers .= 'From: ScanLink Contact Us <' . $email . '>' . "\r\n";

                    // Mail it
                    mail($to, $subject, $message, $headers);
                } else {
                    $this->template->content->errors = 'Invalid Verification Code...';
                }
            } else {
                $this->template->content->errors = 'All fields are required...';
            }
        }
    }

    public function action_enquiry()
    {
        $this->template->content = View::factory('enquiry');
        // Default Content template
        if (isset($_POST['submit']) && $_POST['submit'] != '') {
            $companyName = trim($_POST['companyName']);
            $email = trim($_POST['email']);
            if ($companyName != '' && $email != '') {
                if (preg_match('/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,6}$/', $email)) {
                    $to = Model::factory('settings')->getValueByTitle('enquiry_email');
                    $address = trim($_POST['address']);
                    $tel = trim($_POST['tel']);
                    $contactName = trim($_POST['contactName']);
                    $industryType = trim($_POST['industryType']);
                    $companySize = trim($_POST['companySize']);
                    $briefDescription = trim($_POST['briefDescription']);

                    $facilityAsset = (isset($_POST['facilityAsset']) && $_POST['facilityAsset'] == 1) ? $_POST['facilityAsset'] : '';
                    $plantEquipment = (isset($_POST['plantEquipment']) && $_POST['plantEquipment'] == 1) ? $_POST['plantEquipment'] : '';
                    $productProcedure = (isset($_POST['productProcedure']) && $_POST['productProcedure'] == 1) ? $_POST['productProcedure'] : '';
                    $communicationTraining = (isset($_POST['communicationTraining']) && $_POST['communicationTraining'] == 1) ? $_POST['communicationTraining'] : '';
                    $videoProduction = (isset($_POST['videoProduction']) && $_POST['videoProduction'] == 1) ? $_POST['videoProduction'] : '';
                    $QRCodeVisual = (isset($_POST['QRCodeVisual']) && $_POST['QRCodeVisual'] == 1) ? $_POST['QRCodeVisual'] : '';

                    $comments = trim($_POST['comments']);
                    // subject
                    $subject = 'Enquiry From ' . $companyName;
                    $ressubject = 'Response from ScanLink Solutions for your enquiry';

                    // message
                    $message = '
				<html>
				<head>
				  <title>Enquiry From ' . $companyName . '</title>
				</head>
				<body>
				  <p>Here are the ScanLink Solutions Enquiry Form Details</p>
				  <table cellpadding="3" cellspacing="0" >
					<tr>
					  <th align="right">Company Name: </th>
					  <td>' . $companyName . '</td>
					</tr>
					<tr>
					  <th align="right">Address: </th>
					  <td>' . $address . '</td>
					</tr>
					<tr>
					  <th align="right">Tel:</th>
					  <td>' . $tel . '</td>
					</tr>
					<tr>
					  <th align="right">Email:</th>
					  <td>' . $email . '</td>
					</tr>
					<tr>
					  <th align="right">Contact Name:</th>
					  <td>' . $contactName . '</td>
					</tr>
					<tr>
					  <th align="right">Industry Type:</th>
					  <td>' . $industryType . '</td>
					</tr>
					<tr>
					  <th align="right">Company Size:</th>
					  <td>' . $companySize . '</td>
					</tr>
					<tr>
					  <th align="right">Brief Description of Company Focus:</th>
					  <td>' . $briefDescription . '</td>
					</tr>
					<tr>
						<th align="right" valign="top">The ScanLink Application interested in:</th>
						<td>';
                    if ($facilityAsset != '' || $plantEquipment != '' || $productProcedure != '' || $communicationTraining != '' || $videoProduction != '' || $QRCodeVisual != '') {
                        $message .= '<table>';
                        if ($facilityAsset != '') {
                            $message .= '<tr>
							  <td>Facility Asset Management</td>
							</tr>';
                        }
                        if ($plantEquipment != '') {
                            $message .= '<tr>
							  <td>Plant & Equipment Document Management</td>
							</tr>';
                        }
                        if ($productProcedure != '') {
                            $message .= '<tr>
							  <td>Product & Procedure Management</td>
							</tr>';
                        }
                        if ($communicationTraining != '') {
                            $message .= '<tr>
							  <td>Communication Training</td>
							</tr>';
                        }
                        if ($videoProduction != '') {
                            $message .= '<tr>
							  <td>Video Production, or Re-Editing for Smartphone use</td>
							</tr>';
                        }
                        if ($QRCodeVisual != '') {
                            $message .= '<tr>
							  <td>QR Code Visual Communication</td>
							</tr>';
                        }
                        $message .= '</table>';
                    } else {
                        $message .= 'Not Provided';
                    }
                    $message .= '</td>
					</tr>
					<tr>
					  <th align="right">Comments:</th>
					  <td>' . $comments . '</td>
					</tr>
				  </table>
				</body>
				</html>
				';
                    $companyName = $companyName ? $companyName : 'there';
                    $resmessage = '
				<html>
				<head>
				<title>Response from ScanLink Solutions for your enquiry</title>
				</head>
				<body>
				<p>Hi ' . $companyName . '!</p>
				<p>As usual, ScanLink is always happy to help!</p>
				<p>Thank you for your enquiry. Here is the information you requested.</p>
				<table cellpadding="3" cellspacing="0" >
					<tr>
					  <th align="right" width="140">Company Name: </th>
					  <td>' . $companyName . '</td>
					</tr>
					<tr>
					  <th align="right">Address: </th>
					  <td>' . $address . '</td>
					</tr>
					<tr>
					  <th align="right">Tel:</th>
					  <td>' . $tel . '</td>
					</tr>
					<tr>
					  <th align="right">Email:</th>
					  <td>' . $email . '</td>
					</tr>
					<tr>
					  <th align="right">Contact Name:</th>
					  <td>' . $contactName . '</td>
					</tr>
					<tr>
					  <th align="right">Industry Type:</th>
					  <td>' . $industryType . '</td>
					</tr>
					<tr>
					  <th align="right">Company Size:</th>
					  <td>' . $companySize . '</td>
					</tr>
					<tr>
					  <th align="right">Brief Description of Company Focus:</th>
					  <td>' . $briefDescription . '</td>
					</tr>
					<tr>
						<th align="right" valign="top">The ScanLink Application interested in:</th>
						<td>';
                    if ($facilityAsset != '' || $plantEquipment != '' || $productProcedure != '' || $communicationTraining != '' || $videoProduction != '' || $QRCodeVisual != '') {
                        $resmessage .= '<table>';
                        if ($facilityAsset != '') {
                            $resmessage .= '<tr>
							  <td>Facility Asset Management</td>
							</tr>';
                        }
                        if ($plantEquipment != '') {
                            $resmessage .= '<tr>
							  <td>Plant & Equipment Document Management</td>
							</tr>';
                        }
                        if ($productProcedure != '') {
                            $resmessage .= '<tr>
							  <td>Product & Procedure Management</td>
							</tr>';
                        }
                        if ($communicationTraining != '') {
                            $resmessage .= '<tr>
							  <td>Communication Training</td>
							</tr>';
                        }
                        if ($videoProduction != '') {
                            $resmessage .= '<tr>
							  <td>Video Production, or Re-Editing for Smartphone use</td>
							</tr>';
                        }
                        if ($QRCodeVisual != '') {
                            $resmessage .= '<tr>
							  <td>QR Code Visual Communication</td>
							</tr>';
                        }
                        $resmessage .= '</table>';
                    } else {
                        $resmessage .= 'Not Provided';
                    }
                    $resmessage .= '</td>
					</tr>
					<tr>
					  <th align="right">Comments:</th>
					  <td>' . $comments . '</td>
					</tr>
				  </table>
				  <br />
				<p>Best regards,</p>
				<p><img src="' . URL::base() . 'images/qr-img-regards.png" /></p>
				</body>
				</html>
				';

                    // To send HTML mail, the Content-type header must be set
                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                    $headers .= 'From: ScanLink Solutions Enquiry Form <' . $email . '>' . "\r\n";

                    $resheaders = 'MIME-Version: 1.0' . "\r\n";
                    $resheaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                    $resheaders .= 'From: ScanLink Solutions <' . $to . '>' . "\r\n";

                    // Mail it
                    mail($to, $subject, $message, $headers);
                    mail($email, $ressubject, $resmessage, $resheaders);
                } else {
                    $this->template->content->errors = 'Please Enter Valid Email Address...';
                }
            } else {
                $this->template->content->errors = 'Please Provide Company Name and Email Address...';
            }
        }
    }

    protected function generateRandStr($length)
    {
        $randstr = '';
        for ($i = 0; $i < $length; ++$i) {
            $randnum = mt_rand(0, 61);
            if ($randnum < 10) {
                $randstr .= chr($randnum + 48);
            } elseif ($randnum < 36) {
                $randstr .= chr($randnum + 55);
            } else {
                $randstr .= chr($randnum + 61);
            }
        }

        return $randstr;
    }
} // End Welcome
