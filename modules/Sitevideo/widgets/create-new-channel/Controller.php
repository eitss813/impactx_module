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
class Sitevideo_Widget_CreateNewChannelController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return $this->setNoRender();
        }
        //DONT SHOW ADD LINK TO VISITOR
        if (empty($viewer_id)) {
            return $this->setNoRender();
        }

        $sitevideoCreateChannel = Zend_Registry::isRegistered('sitevideoCreateChannel') ? Zend_Registry::get('sitevideoCreateChannel') : null;
        if(empty($sitevideoCreateChannel))
            return $this->setNoRender();
        
        //CHECK CHANNEL CREATION PRIVACY
        if (!empty($viewer_id) && !Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', $viewer, "create")) {
            return $this->setNoRender();
        }
    }

}
