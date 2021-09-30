<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 6590 2017-03-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    $GLOBALS['countryCodes'] = include_once APPLICATION_PATH . '/application/modules/Siteotpverifier/settings/phoneCountryCode.php';
    $serviceenabled = Engine_Api::_()->getApi('core', 'siteotpverifier')->enabledOTPClient();
    if( $serviceenabled ) {
      include_once APPLICATION_PATH . '/application/modules/Siteotpverifier/Form/Login.php';
      include_once APPLICATION_PATH . '/application/modules/Seaocore/Plugin/Signup/Account.php';
      $module = $request->getModuleName();
      $controller = $request->getControllerName();
      $action = $request->getActionName();
      if( $module == "user" && $controller == "auth" && in_array($action, array("login","forgot")) ) {
        $request->setModuleName('siteotpverifier');
      }
    }
  }
  public function onRenderLayoutMobileSMDefault($event) {
    $view = $event->getPayload();
    if (!($view instanceof Zend_View_Interface)) {
      return;
    }
    $view->headScriptSM()
            ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Siteotpverifier/externals/scripts/sitemobile/core.js');
  }
  
  public function onUserDeleteAfter($event) {
    $payload = $event->getPayload();
    $user_id = $payload['identity'];
    // Remove user from OTP plugin.
    $otpUserTable = Engine_Api::_()->getDbtable('users', 'siteotpverifier');
    $otpUserTable->delete(array(
        'user_id = ?' => $user_id
    ));
  }
}
