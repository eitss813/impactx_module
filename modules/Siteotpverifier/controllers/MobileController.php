<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    VerifierController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

class Siteotpverifier_MobileController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $service = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration');
    if( $service !== "testmode" ) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $otpMessageSession = new Zend_Session_Namespace('Siteotpverifier_OTP_MESSAGE');
    $this->_helper->layout->setLayout('default-simple');
    $this->view->messages = $otpMessageSession->smsOTPInfo;
  }

}
