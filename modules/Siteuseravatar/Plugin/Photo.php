<?php

/**
 * SocialEngine
 *
 * @category   Application_Module
 * @package    Siteuseravatar
 * @copyright  Copyright 2017-2018 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Photo.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteuseravatar_Plugin_Photo
{
  public function onUserSignupAfter($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User && empty($payload->photo_id) ) {
      Engine_Api::_()->siteuseravatar()->setDefaultAvatar($payload->getIdentity());
    }
  }

  public function onUserUpdateAfter($event)
  {
    $payload = $event->getPayload();
    if( !($payload instanceof User_Model_User) ) {
      return;
    }

    if( method_exists($payload, 'getModifiedFieldsName') && in_array('photo_id', $payload->getModifiedFieldsName()) ) {
      Engine_Api::_()->getDbtable('avatars', 'siteuseravatar')->remove($payload);
    }

    $hasNeedToAdd = empty($payload->photo_id) || (method_exists($payload, 'getModifiedFieldsName') && in_array('displayname', $payload->getModifiedFieldsName()) && Engine_Api::_()->getDbtable('avatars', 'siteuseravatar')->get($payload));
    if( $hasNeedToAdd ) {
      Engine_Api::_()->siteuseravatar()->setDefaultAvatar($payload->getIdentity(), $payload->getTitle());
    }
  }

}
