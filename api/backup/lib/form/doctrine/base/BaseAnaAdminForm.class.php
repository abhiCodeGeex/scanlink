<?php

/**
 * AnaAdmin form base class.
 *
 * @method AnaAdmin getObject() Returns the current form's model object
 *
 * @package    analytics
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAnaAdminForm extends BaseFormDoctrine
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
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'AnaAdmin', 'column' => array('email'))),
        new sfValidatorDoctrineUnique(array('model' => 'AnaAdmin', 'column' => array('username'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ana_admin[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AnaAdmin';
  }

}
