<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Plugin_Core {

  //DELETE USERS BELONGINGS BEFORE THAT USER DELETION
  public function onUserDeleteBefore($event) {
    $payload = $event->getPayload();
    if ($payload instanceof User_Model_User) {

      //VIDEO TABLE
      $sitepagevideoTable = Engine_Api::_()->getDbtable('videos', 'sitepagevideo');
      $sitepagevideoSelect = $sitepagevideoTable->select()->where('owner_id = ?', $payload->getIdentity());

      //RATING TABLE
      $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitepagevideo');

      $ratingTable->delete(array('user_id = ?' => $payload->getIdentity()));

      foreach ($sitepagevideoTable->fetchAll($sitepagevideoSelect) as $sitepagevideo) {
				$ratingTable->delete(array('video_id = ?' => $sitepagevideo->video_id));
        $sitepagevideo->delete();
      }
    }
  }

}
?>