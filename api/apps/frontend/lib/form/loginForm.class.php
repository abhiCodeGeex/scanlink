<?php

/**
 * Admin form.
 *
 * @package    ezrep
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class loginForm extends BaseAnaAdminForm {

  public function configure() {
    unset($this['id'], $this['email'], $this['first_name'], $this['last_name'], $this['created_at'], $this['updated_at']);

    $this->setWidgets(array(
        'username' => new sfWidgetFormInputText(),
        'password' => new sfWidgetFormInputPassword(),
    ));

    $this->getWidgetSchema()->setNameFormat('login[%s]');

    $this->setValidators(array(
        'username' => new sfValidatorString(
                array('required' => true),
                array('required' => 'Please enter username')
        ),
        'password' => new sfValidatorString(
                array('required' => true, 'min_length' => 4),
                array('required' => 'Please enter password', 'min_length' => 'Password length must be atleast 4 characters')
        )
    ));


    $this->validatorSchema->setPostValidator(
            new sfValidatorCallback(array
                ('callback' => array($this, 'login_check_callback'))));
  }

  public function login_check_callback($validator, $values) {
    $customer_count = Doctrine_Query::create()
            ->from('AnaUsers u')
            ->where('u.username = ?', $values['username'])
            ->andWhere('u.password = ?', md5($values['password']))
						->andWhere('u.status = ?', '1')
            ->count();

    if (!$customer_count) {
      throw new sfValidatorError($validator, 'Invalid username and password');
    }

    return $values;
  }

}
