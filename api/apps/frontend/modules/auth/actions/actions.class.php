<?php

/**
 * auth actions.
 *
 * @package    analytics
 * @subpackage auth
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class authActions extends sfActions {

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request) {
    $this->redirect('@homepage');
  }

  /*
   * @method              : login
   * @author              : Ajay Makwana <ajay@webplanex.net>
   */

  public function executeLogin(sfWebRequest $request) {

    //echo md5('admin'); exit;
    if ($this->getUser()->isAuthenticated()) {
      $this->redirect('dashboard/index');
    }

    // Create a login form object
    $this->form = new loginForm();
    if ($request->isMethod('post')) {
      $this->form->bind($request->getParameter('login'));

      if ($this->form->isValid()) {
        $formData = $this->form->getValues();
        $user = AnaUserLoginTable::doLogin($formData['username'], $formData['password']);
        $this->getUser()->login($user);
        $loginObj = new AnaUserLogin();
        $loginObj->setIdUser($this->getUser()->getAttribute('id'));
        $loginObj->setIpAdd($request->getRemoteAddress());
        $loginObj->setLoginTime(date('Y-m-d H:i:s', time()));
        $loginObj->save();
        $this->redirect('dashboard/index');
      }
    }
  }

  /*
   * @method              : logout
   * @author              : Ajay Makwana <ajay@webplanex.net>
   */

  public function executeLogout(sfWebRequest $request) {
    $this->getUser()->logout();
    $this->redirect('auth/login');
  }

  public function executeForgotpassword(sfWebRequest $request) {
    $this->form = new forgotpasswordForm();
    $this->setLayout("layout");

    if ($request->isMethod('post')) {
      $this->form->bind($request->getParameter('forgotpassword'));
      if ($this->form->isValid()) {
        $formData = $this->form->getValues();
        $user = Doctrine::getTable('AnaUsers')->findByEmail($formData['email']);
        $password = substr(md5(time()), 5, 10);
        $mailTo = $formData['email'];
        $mailFrom = 'info@analytics.com.au';
        $mailSubject = 'Get your Password';
        $mailMessage = '<p> Dear ' . $user[0]->getFirstName() . ',</p>
				<p> You have requested to get your password from <a href="http://webplanex.com/demo/analytics/web/frontend_dev.php/">Analytics</a></p>
				<p> <strong>your  password is :</strong>' . $password . '</p>
				<p><strong>Email address </strong>: ' . $formData['email'] . '</p>
				<p> you can change your password after login to your account</p>
				<p>&nbsp; </p>
				<p> Sincerely,</p>
				<p> Analytics</p>';

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        @mail($mailTo, $mailSubject, $mailMessage, $headers);
        $user[0]->setPassword(md5($password));
        $user[0]->save();
        $this->getUser()->setFlash("success", "Your login details sent to " . $formData['email']);
        $this->redirect("auth/forgotpassword");
      }
    }
  }

}
