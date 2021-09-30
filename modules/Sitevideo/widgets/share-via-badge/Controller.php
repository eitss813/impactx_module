<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_ShareViaBadgeController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        // Must be logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return $this->setNoRender();
        }
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return $this->setNoRender();
        }
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitevideo_channel')) {
            return $this->setNoRender();
        }
        // Badge is Enable or Not
        $badge_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.badge', 1);
        if (empty($badge_enable)) {
            return $this->setNoRender();
        }

        $this->view->channel = $channel = Engine_Api::_()->core()->getSubject('sitevideo_channel');
        $sitevideoVideosList = Zend_Registry::isRegistered('sitevideoVideosList') ? Zend_Registry::get('sitevideoVideosList') : null;
        if (empty($sitevideoVideosList))
            return $this->setNoRender();
        
        if(($channel->owner_id != $viewer->getIdentity()) && $viewer->level_id != 1) {
            return $this->setNoRender();
        }
        
        
    }

}
