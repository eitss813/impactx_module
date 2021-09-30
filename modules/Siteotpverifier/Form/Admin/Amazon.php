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
class Siteotpverifier_Form_Admin_Amazon extends Engine_Form
{
  public function init()
  {
    $description = vsprintf('You can now integrate Amazon service to start sending OTP verification code. To do so, you need below details. Follow these <a href="%s" target="_blank">steps</a> to configure Amazon service.', array(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
        'module' => 'siteotpverifier',
        'controller' => 'settings',
        'action' => 'faq',
        'faq' => 'faq_1'
        ), 'admin_default', true)));
    $this
      ->setTitle('Amazon Integration')
      ->setDescription($description)
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod("POST");
    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Text', 'clientId', array(
      'label' => 'Client ID',
      'filters' => array(
        'StringTrim',
      ),
    ));

    $this->addElement('Text', 'clientSecret', array(
      'label' => 'Client Secret Key',
      'filters' => array(
        'StringTrim',
      ),
    ));
    $populatearray = array();
    if( !empty($coreSettings->getSetting('siteotpverifier_amazon')) )
      $populatearray = $coreSettings->getSetting('siteotpverifier_amazon');
    $this->populate($populatearray);
    $service = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration');
    $this->addElement('Radio', 'enable', array(
      'label' => 'Enabled?',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'value' => $service == "amazon",
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

}
