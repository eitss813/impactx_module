<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
class Sitepage_Widget_SitemobileDiscussionSitepageController extends Engine_Content_Widget_Abstract {

  protected $_childCount;
  
  //ACTION FOR FETCHING THE DISCUSSIONS FOR THE PAGES
  public function indexAction() { 	

    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      return $this->setNoRender();
    }
    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    //GET SITEPAGE SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    
    //GET PAGE ID
    $this->view->page_id = $sitepage->page_id;


    //START PACKAGE LEVEL CHECK
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagediscussion")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdicreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //END PACKAGE LEVEL CHECK
    //TOTAL TOPICS
    $topicCount = Engine_Api::_()->sitepage()->getTotalCount($this->view->page_id, 'sitepage', 'topics');  
    
    $topicComment = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');
    
    
    if (empty($topicComment) && empty($topicCount) && !(Engine_Api::_()->sitepage()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }
    

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    $this->view->canPost =  Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');
    //END MANAGE-ADMIN CHECK
    
		//GET PAGINATORS
		$this->view->paginator = $paginator = Engine_Api::_()->getDbtable('topics', 'sitepage')->getPageTopics($sitepage->page_id);
		$this->view->paginator->setItemCountPerPage(10)->setCurrentPageNumber($this->_getParam('page', 1));
	  $this->_childCount = $paginator->getTotalItemCount();

  }

  public function getChildCount() {
    return $this->_childCount;
  }

}