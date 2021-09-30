<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Widget_SitemobileProfileSitepagevideosController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT AND PAGE ID
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $page_id = $sitepage->page_id;

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET LEVEL INFO
    if (!empty($viewer_id)) {
      $this->view->level_id = $level_id = $viewer->level_id;
    } else {
      $this->view->level_id = $level_id = 0;
    }

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }

    //PACKAGE BASE PRIYACY END
    
    //TOTAL VIDEO
    $videoCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepagevideo', 'videos');   
    $videoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
        
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }

    if (empty($videoCount) && empty($videoCreate) && empty($can_edit) && !(Engine_Api::_()->sitepage()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
  
    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = true;
    } 

		//START MANAGE-ADMIN CHECK
		$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
		if (empty($isManageAdmin) && empty($can_edit)) {
			$this->view->can_create = 0;
		} else {
			$this->view->can_create = 1;
		}
		//END MANAGE-ADMIN CHECK

		$this->view->page_id = $values['page_id'] = $page_id;

		//FETCH VIDEOS
		if ($can_edit) {
			$values['show_video'] = 0;
			$this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitepagevideo_video')->getSitepagevideosPaginator($values);
		} else {
			$values['show_video'] = 1;
			$values['video_owner_id'] = $viewer_id;
			$this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitepagevideo_video')->getSitepagevideosPaginator($values);
		}

		$this->view->paginator->setItemCountPerPage(10)->setCurrentPageNumber($this->_getParam('page', 1));
		$this->_childCount = $paginator->getTotalItemCount();

  }

  public function getChildCount() {
    return $this->_childCount;
  }

}
