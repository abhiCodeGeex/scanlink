<?php

/**
 * AnaUserLogin form base class.
 *
 * @method AnaUserLogin getObject() Returns the current form's model object
 *
 * @package    analytics
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAnaUserLoginForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'id_user'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaUsers'), 'add_empty' => false)),
      'ip_add'     => new sfWidgetFormInputText(),
      'login_time' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'id_user'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('AnaUsers'))),
      'ip_add'     => new sfValidatorString(array('max_length' => 30, 'required' => false)),
      'login_time' => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ana_user_login[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AnaUserLogin';
  }

}
