<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Otpverifier.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Auth_LoginOtpverifier extends Siteotpverifier_Form_Auth_Otpverifier
{
  public function setOTPMessage($address, $type)
  {
    $expirytime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
    $string = Engine_Api::_()->getApi('core', 'siteotpverifier')->convertTime($expirytime);
    if( $type == 'mobile' ) {
      $length = strlen($address) - 4;
      $address = str_repeat('*', $length) . substr($address, -4);
    } else if( $this->linkfor_testmode_mobile ) {
      $this->removeElement('linkfor_testmode_mobile');
    }
    $description = sprintf($this->getTranslator()->translate('A One Time Password (OTP) has been sent to  %s, please enter the same here to login. <br/> <b>Note:</b> OTP Code is valid for %s.'), $address, $string);
    $this->setDescription($description);
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
  }

  public function init()
  {
    parent::init();
    $this->setAttrib('id', 'siteotpverifier_login_form_verify')
//      ->setTitle('Enter One Time Password')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => 'siteotpverifier',
          'controller' => 'verifier',
          'action' => 'index',
          ), 'default'));
    $loginoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.login.allowoption', 'default');
    if( $loginoption === 'otp' ) {
      $this->setTitle('Two Step Verification');
    }
  }

}
