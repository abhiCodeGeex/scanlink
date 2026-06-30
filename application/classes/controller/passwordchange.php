<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Passwordchange extends Controller_Template_Master
{
    public function action_change_password_iframe()
    {
        $this->template->content = View::factory('profile/password_change');
    }

    public function action_update_password()
    {
        $userdata = Model::factory('user')->getUserByID($this->session->get('clientuserid'));


        $txtpass = addslashes(trim($_POST['txtpass']));
        $password_check = $txtpass;
        if (empty($userdata['is_password_change'])) {
            $password_check = md5($txtpass);
        }
        $txtnewpass = addslashes(trim($_POST['txtnewpass']));
        $txtconfirmpass = addslashes(trim($_POST['txtconfirmpass']));
        if ($txtpass == '') {
            $errors['txtpass'] = "Enter a old password.";
        } elseif ($userdata['password'] != $password_check) {
            $errors['txtpass'] = "Old password does not match with current password.";
        } elseif ($txtnewpass == '') {
            $errors['txtnewpass'] = "Enter a new password.";
        } elseif ((strlen($txtnewpass) < 5) || (strlen($txtnewpass) > 12)) {
            $errors['txtnewpass'] = "Password must between 5-12 character..!";
        } elseif ($txtconfirmpass == '') {
            $errors['txtconfirmpass'] = "Enter a confirm new password.";
        } elseif ($txtnewpass !== $txtconfirmpass) {
            $errors['txtconfirmpass'] = "Confirm Password must be same as New Password..!";
        } else {
            $changedpass = Model::factory('user')->editpass($this->session->get('clientuserid'), array('password' => $txtnewpass, 'is_password_change' => 1));
            $pwdsessionmsg = "Your Password has been changed successfully.";
        }

        if ($userdata['role'] == 5) {
            Model::factory('client')->editpass($userdata['client_id'], array('password' => $txtnewpass, 'is_password_change' => 1));
        }

        $this->template->content = View::factory('profile/password_change')
            ->bind('errors', $errors)
            ->bind('pwdsuccessmessage', $pwdsessionmsg);
    }
}
