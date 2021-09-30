<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagemember_Widget_PagecoverPhotoSitepagemembersController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
      return $this->setNoRender();
    }

    $this->view->columnHeight = $this->_getParam('columnHeight', '300');
    $this->view->memberCount = $this->_getParam('memberCount', '8');
    
    $this->view->onlyMemberWithPhoto = $onlyMemberWithPhoto = $this->_getParam('onlyMemberWithPhoto', 1);
    $this->view->showContent = $this->_getParam('showContent', array("title", "followButton", "likeButton", "joinButton", "addButton"));
    $this->view->statistics = $this->_getParam('statistics', array("followCount", "likeCount", "memberCount"));

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET SITEPAGE SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $this->view->photo = $photo = Engine_Api::_()->getItem('sitepage_photo', $sitepage->page_cover);

    //START MANAGE-ADMIN CHECK
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    $this->view->allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');

    //CHECK REQUEST IS ISAJAX OR NOT
    $this->view->isAjax = $isAjax = $this->_getParam('isAjax', null);

    if (empty($sitepage->page_cover) && empty($can_edit)) {
      $this->view->members = $members = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($sitepage->page_id, null, null, $onlyMemberWithPhoto);
      $this->view->membersCount = $members->getTotalItemCount();
      if (empty($this->view->membersCount))
        return $this->setNoRender();
    }

    $this->view->cover_params = array('top' => 0, 'left' => 0);
    if ($sitepage->page_cover) {
      $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitepage');
      $album = $tableAlbum->getSpecialAlbum($sitepage, 'cover');

      if ($album->cover_params)
        $this->view->cover_params = $album->cover_params;
    }
  }

}