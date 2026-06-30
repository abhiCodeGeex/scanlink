<?php

class myUser extends sfBasicSecurityUser {

	public function login($user = "") {
    if (!$this->isAuthenticated()) {
      $this->setAuthenticated(true);
			if (is_object($user) && count($user) > 0) {
        foreach ($user as $obj) {
					
					$this->addCredential($obj->getUserType());
          $this->setAttribute('id', $obj->getId());
          $this->setAttribute('first_name', $obj->getFirstName());
          $this->setAttribute('last_name', $obj->getLastName());
					$this->setAttribute('username', $obj->getUsername());
          $this->setAttribute('email', $obj->getEmail());
        }
      }
    }
  }
 /* public function login() {
    if (!$this->isAuthenticated()) {
      $this->setAuthenticated(true);
      $this->addCredential('admin');
			$loginObj = new AnaUserLogin();
			$loginObj->setIdUser($this->getUser()->getAttribute('id'));
			$loginObj->setIpAdd($request->getRemoteAddress());
			$loginObj->setLoginTime(date('Y-m-d H:i:s',time()));
			$loginObj->save();
    }
  }*/

  public function logout() {
    if ($this->isAuthenticated()) {
      $this->setAuthenticated(false);
      $this->removeCredential('admin');
    }
  }

  public function checkLogin() {
    if ($this->isAuthenticated()) {
      return true;
    } else {
      return false;
    }
  }

}
