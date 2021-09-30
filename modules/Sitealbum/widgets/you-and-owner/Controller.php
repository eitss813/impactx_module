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
class Sitealbum_Widget_YouAndOwnerController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    if ((!Engine_Api::_()->core()->hasSubject('user') && !Engine_Api::_()->core()->hasSubject('album') && !Engine_Api::_()->core()->hasSubject('album_photo')) || empty($viewer_id)) {
      return $this->setNoRender();
    }

    $owner = $subject = Engine_Api::_()->core()->getSubject();
    if (!Engine_Api::_()->core()->hasSubject('user'))
      $owner = $subject->getOwner();

    if ($owner->isSelf($viewer))
      return $this->setNoRender();
    $this->view->owner = $owner;
    $this->view->limit = $limit = $this->_getParam('itemCountPerPage', 2);

    $owner_id = $owner->getIdentity();
    $this->view->youAndOwner = $youAndOwner = Engine_Api::_()->getDbTable('photos', 'sitealbum')->getTaggedYouAndOwnerPhotos($viewer_id, $owner_id, $limit);
    $count = count($youAndOwner);
    if ($count <= 0) {
      return $this->setNoRender();
    }
    $count = $youAndOwner->getTotalItemCount();
    $youAndOwner->setItemCountPerPage($limit);
    $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
    $this->view->count = $this->_childCount = $count;
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}