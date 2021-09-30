<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Menus.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Plugin_Menus
{
  public function canShow()
  {
    // Check subject
    if( !Engine_Api::_()->core()->hasSubject('user') ) {
      return false;
    }
    // Check viewer
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    $otpSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.enableotp', 1);
    if( empty($otpSetting) ) {
      return false;
    }
    return Engine_Api::_()->getApi('core', 'siteotpverifier')->enabledOTPClient();
  }

}
