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
class Sitepagevideo_Widget_ProfileSitepagevideosController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
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

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET LEVEL INFO
    if (!empty($viewer_id)) {
      $this->view->level_id = $level_id = $viewer->level_id;
    } else {
      $this->view->level_id = $level_id = 0;
    }
    $sitepage_isProfile = Zend_Registry::isRegistered('sitepagevideo_isProfile') ? Zend_Registry::get('sitepagevideo_isProfile') : null;

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

    if (empty($sitepage_isProfile)) {
      return $this->setNoRender();
    }
    //PACKAGE BASE PRIYACY END
    
    //TOTAL VIDEO
    $videoCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagevideo', 'videos');   
    $videoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');

    if ($videoCount == 0) {
      return $this->setNoRender();
    }
        
//    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->setNoRender();
//    }

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
      $this->view->allowView = true;
    } 

    $this->view->module_tabid = $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    //Getting the tab id from the content table.
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    $this->view->widgets = $widgets = Engine_Api::_()->sitepage()->getwidget($layout, $sitepage->page_id);
    $this->view->content_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagevideo.profile-sitepagevideos', $sitepage->page_id, $layout);
    $isajax = $this->_getParam('isajax', null);
    $this->view->showtoptitle = $showtoptitle = Engine_Api::_()->sitepage()->showtoptitle($layout, $sitepage->page_id);
    $this->view->isajax = $isajax;
    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {
      $this->view->identity_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;

      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
      if (empty($isManageAdmin) && empty($can_edit)) {
        $this->view->can_create = 0;
      } else {
        $this->view->can_create = 1;
      }
      //END MANAGE-ADMIN CHECK
      //GET SEARCHING PARAMETERS
      $this->view->page = $page = $this->_getParam('page', 1);
      $this->view->search = $search = $this->_getParam('search');
      $this->view->selectbox = $selectbox = $this->_getParam('selectbox');
      $this->view->checkbox = $checkbox = $this->_getParam('checkbox');
      $values = array();
      $values['orderby'] = '';
      if (!empty($selectbox) && $selectbox == 'featured') {
        $values['featured'] = 1;
        $values['orderby'] = 'creation_date';
      }
      if (!empty($search)) {
        $values['search'] = $search;
      }
      if (!empty($selectbox)) {
        $values['orderby'] = $selectbox;
      } 
      if (!empty($checkbox) && $checkbox == 1) {
        $values['owner_id'] = $viewer_id;
      }

      $values['page_id'] = $sitepage->page_id;

      //FETCH VIDEOS
      if ($can_edit) {
        $values['show_video'] = 0;
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitepagevideo_video')->getSitepagevideosPaginator($values);
      } else {
        $values['show_video'] = 1;
        $values['video_owner_id'] = $viewer_id;
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitepagevideo_video')->getSitepagevideosPaginator($values);
      }

      $this->view->paginator = $paginator->setItemCountPerPage(10);

      $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));

      if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
        $this->_childCount = $paginator->getTotalItemCount();
      }

      $this->view->current_count = $paginator->getTotalItemCount();
    } else {
      $this->view->show_content = false;
      $this->view->identity_temp = $this->view->identity;

      $values = array();
      $values['orderby'] = 'creation_date';
      $values['page_id'] = $sitepage->page_id;
      $values['show_count'] = 1;
      if ($can_edit) {
        $values['show_video'] = 0;
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitepagevideo_video')->getSitepagevideosPaginator($values);
      } else {
        $values['show_video'] = 1;
        $values['video_owner_id'] = $viewer_id;
        $paginator = Engine_Api::_()->getItemTable('sitepagevideo_video')->getSitepagevideosPaginator($values);
      }

      $this->_childCount = $paginator->getTotalItemCount();
    }
     $this->view->sitevideoviewEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideoview');
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}
?>