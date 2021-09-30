<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Otpverify.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Signup_Otpverify extends Siteotpverifier_Form_Auth_Otpverifier
{
  //protected $_string;
  public function formdata($mobileno)
  {
    $expirytime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
    $string = Engine_Api::_()->getApi('core', 'siteotpverifier')->convertTime($expirytime);

    $description = $this->getTranslator()->translate('Enter the verification code you have received on ' . $mobileno . '.<br/> <b>Note:</b> OTP Code is valid for ' . $string . '.');
    $this->setDescription($description);
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
  }

  public function init()
  {
    parent::init();
    $this->setAttrib('id', 'siteotpverifier_signupform_verify')->setTitle('OTP Verification');
    $this->resend->setAttrib('onClick', 'resendSinupCode()');
  }

}
