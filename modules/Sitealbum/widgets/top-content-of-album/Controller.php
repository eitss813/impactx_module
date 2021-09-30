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
class Sitealbum_Widget_TopContentOfAlbumController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->core()->hasSubject('album')) {
      return $this->setNoRender();
    }

    $this->view->album = $album = Engine_Api::_()->core()->getSubject('album');

    $this->view->showInformationOptions = $this->_getParam('showInformationOptions', array('likeButton', 'title', 'description', 'owner', 'location', 'updateddate', 'facebooklikebutton', 'commentViewEnabled', 'editmenus'));
    $this->view->showLayout = $this->_getParam('showLayout', 'center');

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $this->view->canEdit = $canComment = $album->authorization()->isAllowed($viewer, 'edit');
    $this->view->canComment = $canComment = $album->authorization()->isAllowed($viewer, 'comment');
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('album_profile');
//    $front = Zend_Controller_Front::getInstance();
//    $this->view->comment_view = $comment_view = $front->getRequest()->getParam('comment', 'false');
    // Do other stuff
    $this->view->mine = true;
    if (!$album->getOwner()->isSelf($viewer)) {
      $this->view->mine = false;
    }

    $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitealbum');
    $this->view->category_name = '';
    if (!empty($album->category_id)) {
      $this->view->category_name = $tableCategory->getCategory($album->category_id)->category_name;
    }

    $this->view->sitealbumTags = $album->tags()->getTagMaps();

    //SET COUNT TO THE TITLE
    $this->view->allowView = $this->view->canMakeFeatured = false;
    if (!empty($viewer_id) && ($viewer->level_id == 1 || $viewer->level_id == 2)) {
      $this->view->canMakeFeatured = true;
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($album, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($album, 'registered', 'view') === 1 ? true : false;
    }
  }

}