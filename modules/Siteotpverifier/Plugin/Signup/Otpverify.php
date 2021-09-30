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
class Siteotpverifier_Plugin_Signup_Otpverify extends Core_Plugin_FormSequence_Abstract
{

  protected $_name = 'otpverify';
  protected $_formClass = 'Siteotpverifier_Form_Signup_Otpverify';
  protected $_script = array('signup/form/otpverify.tpl', 'siteotpverifier');
  protected $_adminFormClass = 'Siteotpverifier_Form_Admin_Signup_Otpverify';
  protected $_adminScript = array('admin-signup/otpverify.tpl', 'siteotpverifier');
  public $email = null;

  public function init()
  {
    $accountSession = new Zend_Session_Namespace('User_Plugin_Signup_Account');
    $active = true;
    if( $accountSession->active ) {
      $request = Zend_Controller_Front::getInstance()->getRequest();
      $active = $request->isPost() && $request->getParam("phoneno");
      $this->setActive($active);
    }
  }

  public function onView()
  {
    $otpverifySession = new Zend_Session_Namespace('Siteotpverifier_otpverify');
    $otpverifySkip = new Zend_Session_Namespace('Siteotpverifier_otpverifyskip');
    $phoneNo = $otpverifySession->phoneno;
    $this->getForm()->formdata($phoneNo);
    $otpverifySkip->skip = false;
    if( empty($phoneNo) ) {
      $otpverifySkip->skip = true;
      $this->getForm()->setTitle('')->setDescription('');
      $this->getForm()->removeElement('Code');
      $this->getForm()->removeElement('otp_submit');
      $this->getForm()->removeElement('resend');
      return;
    }

    if( empty($otpverifySession->otp_code) ) {
      $code = Engine_Api::_()->getApi('core', 'siteotpverifier')->generateCode();
      $otpverifySession->otp_code = $code;
      Engine_Api::_()->getApi('core', 'siteotpverifier')->verifyMobileNo($phoneNo, $code);
    }
  }

  public function onSubmit(Zend_Controller_Request_Abstract $request)
  {
    $otpverifySkip = new Zend_Session_Namespace('Siteotpverifier_otpverifyskip');
    if( $otpverifySkip->skip ) {
      $this->setActive(false);
      $this->onSubmitIsValid();
      $otpverifySkip->skip = false;
    } else {
      $code = $request->getParam("code");
      if( $this->getForm()->isValid($request->getPost()) ) {
        $otpverifySession = new Zend_Session_Namespace('Siteotpverifier_otpverify');
        $otpcode = $otpverifySession->otp_code;
        $expiaryTime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.lifetime', 600);
        $min_creation_date = time() - ($expiaryTime);
        if( $otpverifySession->time < $min_creation_date ) {
          $this->getForm()->addError("The OTP code you entered has expired. Please click on 'RESEND' to get new OTP code.");
          $this->getSession()->active = true;
        } elseif( $otpcode != $code ) {
          $this->getForm()->addError("The OTP code you entered is invalid. Please enter the correct OTP code.");
          $this->getSession()->active = true;
        } else {
          $this->setActive(false);
          $this->onSubmitIsValid();
          $otpverifySession->unsetAll();
        }
      }
    }
  }

  public function onAdminProcess($form)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $values = $form->getValues();
    $settings->user_signup = $values;
    $step_table = Engine_Api::_()->getDbtable('signup', 'user');
    $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'Siteotpverifier_Plugin_Signup_Otpverify'));
    $step_row->enable = $values['enableotp'] ? 1 : 0;
    $step_row->save();
    $form->addNotice('Your changes have been saved.');
  }

}
