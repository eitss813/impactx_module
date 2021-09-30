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
class Sitepage_Widget_DiscussionSitepageController extends Engine_Content_Widget_Abstract {

  protected $_childCount;
  
  //ACTION FOR FETCHING THE DISCUSSIONS FOR THE PAGES
  public function indexAction() { 	
  	
    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    //GET SUBJECT
    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
    	$this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    }
    else {
      $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
    }
   
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
    $topicCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'topics');  
    
    $topicComment = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');
    
    if (empty($topicComment) && empty($topicCount) && !(Engine_Api::_()->sitepage()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }
    
//    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->setNoRender();
//    }
    $this->view->canPost =  Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');
    //END MANAGE-ADMIN CHECK
    
    //GET WHICH LAYOUT IS SET BY THE ADMIN
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    
    //GET THIRD TYPE LAYOUT IS SET OR NOT
    $this->view->widgets = $widgets = Engine_Api::_()->sitepage()->getwidget($layout, $sitepage->page_id);
    
    //GET TAB ID
    $this->view->content_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.discussion-sitepage', $sitepage->page_id, $layout);
    
    //GET CURRENT TAB ID
    $this->view->module_tabid = $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    
    //CHECK REQUEST IS ISAJAX OR NOT
    $this->view->isajax = $isajax = $this->_getParam('isajax', null);
    
    //SHOW TOP TITLE
    $this->view->showtoptitle = Engine_Api::_()->sitepage()->showtoptitle($layout, $sitepage->page_id);
    
    //CHECK REQUEST IS AJAX OR NOT OR CURRENT TAB ID OR LAYOUT
    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {
      $this->view->identity_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;  

      //GET CURRENT PAGE NUMBER     
      $page = $this->_getParam('page', 1);
      
      //GET PAGINATORS
      $this->view->paginators = $paginators = Engine_Api::_()->getDbtable('topics', 'sitepage')->getPageTopics($sitepage->page_id);
      $paginators->setItemCountPerPage(10)->setCurrentPageNumber($page);

      //ADD COUNT TO TITLE IF CONFIGURED
      if ($this->_getParam('titleCount', false) && $paginators->getTotalItemCount() > 0) {
        $this->_childCount = $paginators->getTotalItemCount();
      }
    } else {
      $this->view->show_content = false;
      $this->view->identity_temp = $this->view->identity;
      $this->_childCount = Engine_Api::_()->sitepage()->getTotalCount( $sitepage->page_id, 'sitepage', 'topics');
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }
}

?>