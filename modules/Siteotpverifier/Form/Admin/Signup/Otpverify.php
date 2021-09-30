<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Otpverify.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Admin_Signup_Otpverify extends Engine_Form
{
  public function init()
  {
    // Get step and step number
    $stepTable = Engine_Api::_()->getDbtable('signup', 'user');
    $stepSelect = $stepTable->select()->where('class = ?', str_replace('_Form_Admin_', '_Plugin_', get_class($this)));
    $step = $stepTable->fetchRow($stepSelect);
    $stepNumber = 1 + $stepTable->select()
        ->from($stepTable, new Zend_Db_Expr('COUNT(signup_id)'))
        ->where('`order` < ?', $step->order)
        ->query()
        ->fetchColumn()
    ;
    $stepString = $this->getView()->translate('Step %1$s', $stepNumber);
    $this->setDisableTranslator(true);


    // Custom
    $this->setTitle($this->getView()->translate('%1$s: Mobile Number Verification (OTP)', $stepString));

    $description = $this->getView()->translate('In the next step of the signup process the member will verify their mobile number. Click <a href="%s" target="_blank" >here</a> to enable the mobile number for signup process.', Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'siteotpverifier', 'controller' => 'settings'), 'admin_default', true).'#singupUserPhone-wrapper');
    $this->setDescription($description);
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Radio', 'enableotp', array(
      'label' => 'Enable OTP Verification?',
      'description' => 'Do you want to enable OTP Verification option for your users to verify their Signup?',
      'multiOptions' => array(
        1 => 'Yes, enable OTP Verification option for your users to verify their Signup.',
        0 => 'No, do not enable OTP Verification option for your users to verify their Signup.'
      ),
      'value' => 1,
    ));
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
    $this->populate($settings->getSetting('user_signup'));
  }

}
