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
class Sitepage_Widget_SitemobilePagecoverPhotoInformationController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
  $this->_mobileAppFile = true;
    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $this->view->strachPhoto = $this->_getParam('strachPhoto', 0);
    $this->view->showContent = $this->_getParam('showContent', array("mainPhoto", "title", "category", "subcategory", "subsubcategory",  "likeButton", "followButton", "joinButton", "addButton", "description", "phone", "email", "website", "location", "tags", "price", "badge"));

    if(empty($this->view->showContent)) {
      $this->view->showContent = array();
    }

    //GET VIEWER INFORMATION
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    $this->view->allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    $this->view->cover_params = array('top' => 0, 'left' => 0);

    if(Engine_Api::_()->hasModuleBootstrap('sitepagebadge') && isset($sitepage->badge_id))  {
			$this->view->sitepagebadges_value = Engine_Api::_()->getApi('settings', 'core')->sitepagebadge_badgeprofile_widgets;
			$this->view->sitepagebadge = Engine_Api::_()->getItem('sitepagebadge_badge', $sitepage->badge_id);
    }

    if (Engine_Api::_()->hasModuleBootstrap('sitepagealbum') && $sitepage->page_cover) {
      $this->view->photo = $photo = Engine_Api::_()->getItem('sitepage_photo', $sitepage->page_cover);
      $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitepage');
      $album = $tableAlbum->getSpecialAlbum($sitepage, 'cover');
      if ($album->cover_params)
        $this->view->cover_params = $album->cover_params;
    }
    $this->view->sitepageTags = $sitepage->tags()->getTagMaps();
    $this->view->resource_id = $resource_id = $sitepage->getIdentity();
    $this->view->resource_type = $resource_type = $sitepage->getType();
    $this->view->follow_count = Engine_Api::_()->getDbtable('follows', 'seaocore')->numberOfFollow($resource_type, $resource_id);
    $this->view->subcategory_name = '';
    $this->view->subsubcategory_name = '';
    $categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitepage');
    $this->view->category_name = $categoriesTable->getCategory($sitepage->category_id)->category_name;
    if(isset($categoriesTable->getCategory($sitepage->subcategory_id)->category_name))
    $this->view->subcategory_name = $categoriesTable->getCategory($sitepage->subcategory_id)->category_name;
    if(isset($categoriesTable->getCategory($sitepage->subsubcategory_id)->category_name))
    $this->view->subsubcategory_name = $categoriesTable->getCategory($sitepage->subsubcategory_id)->category_name;
    
    //PARAMS
    $this->view->can_share = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.share', 1);
    if(isset($viewer->level_id))
    $this->view->showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
    
  }

}