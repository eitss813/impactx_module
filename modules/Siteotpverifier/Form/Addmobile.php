<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Addmobile.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Addmobile extends Engine_Form
{
  public function init()
  {
    $this->setAttrib('id', 'siteotpverifier_form_addmobile');
    $this->setAttrib('title', 'Add Phone Number');
    // init email
    $countrycodes=Engine_Api::_()->getApi('core', 'siteotpverifier')->countryCode();
    $this->addElement('Select', 'country_code', array(
                'label' => 'Country',
                'description' => 'Please select your country.',
                'multiOptions' => $countrycodes, 
                'value' => Engine_Api::_()->siteotpverifier()->getDefaultCountry(),
    ));
    $this->country_code->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
    $this->addElement('Text', 'mobile', array(
      'description' => 'Please enter your phone number.',
      'label' => 'Phone Number',  
      'required' => true,
      'allowEmpty' => false,
//      'placeholder'=>'xxxxxxxxxx',
      'validators' => array(
                array('NotEmpty',true),
                array('Regex', true, array("/^[1-9][0-9]{4,15}$/")),
                ),
      'tabindex' => 1,
    ));
   // $this->mobile->getValidator('NotEmpty')->setMessage('Please enter a valid phone number.', 'isEmpty');
    $this->mobile->getValidator('Regex')->setMessage('Please enter a valid phone number.', 'regexNotMatch');
    $this->mobile->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

// Init submit
    $this->addElement('Button', 'add_submit', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => 2,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

  }
}