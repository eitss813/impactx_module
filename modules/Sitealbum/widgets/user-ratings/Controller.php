<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_UserRatingsController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1)) {
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    
     $this->view->update_permission = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbumrating.update', 1);
     
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    $allowRating = Engine_Api::_()->authorization()->getPermission($level_id, 'album', 'rate');
    if (!empty($viewer_id) && !empty($allowRating)) {
      $this->view->canRate = 1;
    } else {
      $this->view->canRate = 0;
    }

    $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitealbum');
    $this->view->rating_count = $ratingTable->ratingCount(array('resource_id' => $subject->getIdentity(), 'resource_type' => $subject->getType()));
    $this->view->rated = $ratingTable->checkRated(array('resource_id' => $subject->getIdentity(), 'resource_type' => $subject->getType()));
  }

}