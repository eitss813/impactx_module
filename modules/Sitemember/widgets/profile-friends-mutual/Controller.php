<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_ProfileFriendsMutualController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->show = $show = $this->_getParam('show', 'friends');
    $this->view->limit = $limit = $this->_getParam('itemCountPerPage', 6);
    $this->view->titlePosition = $this->_getParam('titlePosition', 1);
    $this->view->photoWidth = $this->_getParam('photoWidth', 64);
    $this->view->photoHeight = $this->_getParam('photoHeight', 64);
    $this->view->circularImage = $this->_getParam('circularImage', 0);
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject() || !Engine_Api::_()->core()->hasSubject('user')) {
      return $this->setNoRender();
    }

    // Don't render this if friendships are disabled
    if (!Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible) {
      return $this->setNoRender();
    }

    $sitemember_mutual_friend = Zend_Registry::isRegistered('sitemember_mutual_friend') ? Zend_Registry::get('sitemember_mutual_friend'): null;
    if (empty($sitemember_mutual_friend)) {
      $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
    if ($show == 'mutualfriends') {
      if (!$viewer->getIdentity() || $viewer->isSelf($subject)) {
        return $this->setNoRender();
      }
    } else {
      if (!$subject->authorization()->isAllowed($viewer, 'view')) {
        return $this->setNoRender();
      }
    }
    
    if ($show == 'mutualfriends') {
      $this->view->paginator = $paginator = Engine_Api::_()->seaocore()->getMutualFriend($subject->getIdentity(), $limit);
      $paginator->setItemCountPerPage($limit);
    } else {
      $select = $subject->membership()->getMembersOfSelect();
      $this->view->paginator = $friends = $paginator = Zend_Paginator::factory($select);
      
      $paginator->setItemCountPerPage($limit);

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->resource_id;
      }
      $this->view->friendIds = $ids;

      // Get the items
      $friendUsers = array();
      foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser) {
        $friendUsers[$friendUser->getIdentity()] = $friendUser;
      }
      $this->view->friendUsers = $friendUsers;
    }

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }

    $this->view->contentDetails = Engine_Api::_()->sitemember()->getWidgetInfo('sitemember.profile-friends-sitemember', $this->view->identity);

    if (empty($this->view->contentDetails)) {
      $this->view->contentDetails->content_id = 0;
    }
  }

}