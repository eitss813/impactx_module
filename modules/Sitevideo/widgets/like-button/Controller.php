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
class Sitevideo_Widget_LikeButtonController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $sitevideoLikeButton = Zend_Registry::isRegistered('sitevideoLikeButton') ? Zend_Registry::get('sitevideoLikeButton') : null;
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if(empty($sitevideoLikeButton))
            return $this->setNoRender();
        
        if (!Engine_Api::_()->core()->hasSubject() || empty($viewer_id)) {
            return $this->setNoRender();
        }
    }

}
