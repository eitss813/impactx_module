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
class Sitevideo_Widget_UserRatingsController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.rating', 1)) {
            return $this->setNoRender();
        }

        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        $subjectType = $subject->getType();
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $this->view->update_permission = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideorating.update', 1);
        $sitevideoRating = Zend_Registry::isRegistered('sitevideoRating') ? Zend_Registry::get('sitevideoRating') : null;
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $allowRating = Engine_Api::_()->authorization()->getPermission($level_id, $subjectType, 'rate');
        if (!empty($viewer_id) && !empty($allowRating)) {
            $this->view->canRate = 1;
        } else {
            $this->view->canRate = 0;
        }
        if(empty($sitevideoRating))
            return $this->setNoRender();
        $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitevideo');
        $this->view->rating_count = $ratingTable->ratingCount(array('resource_id' => $subject->getIdentity(), 'resource_type' => $subject->getType()));
        $this->view->rated = $ratingTable->checkRated(array('resource_id' => $subject->getIdentity(), 'resource_type' => $subject->getType()));
    }

}
