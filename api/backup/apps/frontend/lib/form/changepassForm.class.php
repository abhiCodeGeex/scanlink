<?php

/**
 * Change Password form.
 *
 * @package    property
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class changepassForm extends sfForm {

  public function configure() {
    //unset($this['id'], $this['created_at'], $this['updated_at'], $this['email']);

    $this->setWidgets(array(
        'password' => new sfWidgetFormInputPassword(),
    ));

    $this->setWidget('new_password', new sfWidgetFormInputPassword());
    $this->setWidget('confirm_password', new sfWidgetFormInputPassword());

    $this->getWidgetSchema()->setNameFormat('changepass[%s]');

    $this->setValidators(array(
        'password' => new sfValidatorString(
                array('min_length' => 4),
                array('min_length' => 'Password length must be atleast 4 characters')
        ),
        'new_password' => new sfValidatorString(
                array('min_length' => 4),
                array('min_length' => 'Password length must be atleast 4 characters')
        ),
        'confirm_password' => new sfValidatorString(
                array('min_length' => 4),
                array('min_length' => 'Password length must be atleast 4 characters')
        ),
    ));


    $this->validatorSchema->setPostValidator(
            new sfValidatorCallback(array
                ('callback' => array($this, 'newpass_check_callback'))));
  }

  public function newpass_check_callback($validator, $values) {
	
	if ($values['new_password'] != $values['confirm_password']) {
      throw new sfValidatorError($validator, 'Both new and confirm password are not matched');
      return $values;
    }

    $password_count = Doctrine_Query::create()
            ->from('AnaUsers u')
            ->andWhere('u.password = ?', md5($values['password']))
            ->count();

    if (!$password_count) {
      throw new sfValidatorError($validator, 'Your old password is wrong! please try again');
      return $values;
    }
    
    return $values;
  }

}
