<?php

/**
 * UserLogin form.
 *
 * @package    property
 * @subpackage form
 * @author     Ajay Makwana <ajay@webplanex.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class forgotpasswordForm extends BaseAnaAdminForm {

  public function configure() {
    unset($this['id'], $this['status'],$this['password'], $this['created_at'], $this['updated_at']);

    $this->setWidgets(array(
        'email' => new sfWidgetFormInputText(),
        
    ));

    
    $this->getWidgetSchema()->setNameFormat('forgotpassword[%s]');

    $this->setValidators(array(
        'email' => new sfValidatorEmail(
                array(),
                array('invalid' => 'Please enter only valid email address')
        ),
       
    ));
   
    $this->validatorSchema->setPostValidator(
            new sfValidatorCallback(array
                ('callback' => array($this, 'forgotpassword_check_callback'))));
				
  }
  
  public function forgotpassword_check_callback($validator, $values) {
	$user = Doctrine_Query::create()
            ->from('AnaUsers u')
            ->where('u.email = ?', $values['email'])
            ->execute();

    if (count($user) <= 0) {
      throw new sfValidatorError($validator, 'Email does not exists');
    }
    return $values;
  }

}
