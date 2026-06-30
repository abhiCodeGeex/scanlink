<?php

/**
 * AnaUsers form.
 *
 * @package    analytics
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class AnaUsersForm extends BaseAnaUsersForm {

  public function configure() {
    //:unset unsed field.
    unset($this['id'], $this['created_at'], $this['updated_at'], $this['user_type']);


    $this->validatorSchema['email'] = new sfValidatorEmail(array('max_length' => 255), array('max_length' => 'Maximum 255 charactres are allowed'));

    if ($this->isNew()) {
      $this->widgetSchema['password'] = new sfWidgetFormInputPassword();
      //: set confirm password for new client.
      $this->widgetSchema['confirm_password'] = new sfWidgetFormInputPassword();
      $this->validatorSchema['confirm_password'] = new sfValidatorString(array('min_length' => 4));
      //: to check passwor and confirm password
    } else {
      unset($this['password']); // unset password in edit form

      $this->validatorSchema->setPostValidator(
              new sfValidatorCallback(array
                  ('callback' => array($this, 'check_unique_callback'))));
    }

    $this->getWidgetSchema()->setNameFormat('client[%s]');

    if ($this->isNew()) {
      $this->mergePostValidator(
              new sfValidatorSchemaCompare(
                      'password', sfValidatorSchemaCompare::EQUAL, 'confirm_password',
                      array(),
                      array('invalid' => 'Both password do not match with each other')
              )
      );
    }
  }

  /*
   * @method      :           doSave
   * @author      :           Ajay Makwana
   * @desc        :           Override doSave method to set activations key
   */

  protected function doSave($con = null) {
    // check for new client
    parent::doSave($con);
    if ($this->isNew()) {
      $this->getObject()->setPassword(md5($this->getObject()->getPassword()));
    }
    //exit;
    $this->getObject()->save($conn);
  }

  /*
   * @method      :           check_unique_callback
   * @author      :           Ajay Makwana
   * @desc        :           Check for unique username and email at edit form
   */

  public function check_unique_callback($validator, $values) {

    $user_count = Doctrine_Query::create()
            ->from('AnaUsers u')
            ->where('u.email = ?', $values['email'])
            ->andWhere('u.id != ?', $this->getObject()->getId())
            ->count();

    if ($user_count) {
      throw new sfValidatorError($validator, 'Email already exist');
    }

    $user_count = Doctrine_Query::create()
            ->from('AnaUsers u')
            ->where('u.username = ?', $values['username'])
            ->andWhere('u.id != ?', $this->getObject()->getId())
            ->count();

    if ($user_count) {
      throw new sfValidatorError($validator, 'Username already exist');
    }

    return $values;
  }

}
