<?php

/**
 * AnaUsers form base class.
 *
 * @method AnaUsers getObject() Returns the current form's model object
 *
 * @package    analytics
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAnaUsersForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'first_name' => new sfWidgetFormInputText(),
      'last_name'  => new sfWidgetFormInputText(),
      'email'      => new sfWidgetFormInputText(),
      'username'   => new sfWidgetFormInputText(),
      'password'   => new sfWidgetFormInputText(),
      'address'    => new sfWidgetFormInputText(),
      'user_type'  => new sfWidgetFormChoice(array('choices' => array('user' => 'user', 'admin' => 'admin'))),
      'status'     => new sfWidgetFormInputCheckbox(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'first_name' => new sfValidatorString(array('max_length' => 255)),
      'last_name'  => new sfValidatorString(array('max_length' => 255)),
      'email'      => new sfValidatorString(array('max_length' => 255)),
      'username'   => new sfValidatorString(array('max_length' => 255)),
      'password'   => new sfValidatorString(array('max_length' => 255)),
      'address'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'user_type'  => new sfValidatorChoice(array('choices' => array(0 => 'user', 1 => 'admin'), 'required' => false)),
      'status'     => new sfValidatorBoolean(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'AnaUsers', 'column' => array('email'))),
        new sfValidatorDoctrineUnique(array('model' => 'AnaUsers', 'column' => array('username'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ana_users[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AnaUsers';
  }

}
