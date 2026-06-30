<?php

/**
 * AnaItems form base class.
 *
 * @method AnaItems getObject() Returns the current form's model object
 *
 * @package    analytics
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAnaItemsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'id_project' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AnaProjects'), 'add_empty' => false)),
      'url'        => new sfWidgetFormInputText(),
      'item_key'   => new sfWidgetFormInputText(),
      'status'     => new sfWidgetFormInputCheckbox(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'id_project' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('AnaProjects'))),
      'url'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'item_key'   => new sfValidatorString(array('max_length' => 50)),
      'status'     => new sfValidatorBoolean(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'AnaItems', 'column' => array('url'))),
        new sfValidatorDoctrineUnique(array('model' => 'AnaItems', 'column' => array('item_key'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ana_items[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AnaItems';
  }

}
