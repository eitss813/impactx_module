<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Global.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Admin_Twilio extends Engine_Form
{
  public function init()
  {
    $description = vsprintf('You can now integrate Twilio service to start sending OTP verification code. To do so, you need below details. Follow these <a href="%s" target="_blank">steps</a> to configure Twilio service.', array(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
        'module' => 'siteotpverifier',
        'controller' => 'settings',
        'action' => 'faq',
        'faq' => 'faq_2'
        ), 'admin_default', true)));
    $this
      ->setTitle('Twilio Integration')
      ->setDescription($description)
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod("POST");
    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Text', 'accountsid', array(
      'label' => 'Account SID',
      'filters' => array(
        'StringTrim',
      ),
    ));
    $this->addElement('Text', 'apikey', array(
      'label' => 'Authorization Token',
      'filters' => array(
        'StringTrim',
      ),
    ));
    $this->addElement('Text', 'phoneno', array(
      'label' => 'Phone Number',
      'placeholder' => 'Phone Number',
      'allowEmpty' => false,
      'required' => true,
      'description' => 'Please enter the phone number you have purchased in your Twilio account. [Note: It should include country code with no space or any other character in between the phone number. Example: +84XXXXXXXX. ]',
      'filters' => array(
        'StringTrim',
      ),
      'validators' => array(
        array('NotEmpty', true),
        array('Regex', true, "/^(\+[1-9]{1}[0-9]{3,15})+$/"),
      ),
    ));
    $service = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration');
    $this->addElement('Radio', 'enable', array(
      'label' => 'Enabled?',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'value' => $service == "twilio",
    ));
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
    $populatearray = array();

    if( !empty($coreSettings->getSetting('siteotpverifier_twilio')) )
      $populatearray = $coreSettings->getSetting('siteotpverifier_twilio');

    $this->populate($populatearray);
  }

}
