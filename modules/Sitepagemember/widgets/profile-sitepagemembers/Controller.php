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
class Sitepagemember_Widget_ProfileSitepagemembersController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DONT RENDER THIS IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return $this->setNoRender();
    }
    $this->view->params = $params = $this->_getAllParams();
    //GET SITEPAGE SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    if (!Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate')) {
      return $this->setNoRender();
    }
    
//    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->setNoRender();
//    }

    $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitepagemember');

    $this->view->show_option = $show_option = $this->_getParam('show_option', 1);
    $this->view->roles_id = $roles_id = $this->_getParam('roles_id', null);
//    $isajax = $this->_getParam('isajax', null);
    $pageAdminRoles = '';
    $othersRoles = '';
    $rolesParams = array();
    if ($show_option == 0) {
      if(is_array($roles_id)) {
        $othersRoles = in_array('0', $roles_id);
        $pageAdminRoles = in_array('pageadminRole', $roles_id);
      }

//      if ($othersRoles) {
//				if (!empty($role_id)) {
//					unset($role_id[array_search('0', $role_id)]);
//				}
//      }

      if (!empty($roles_id))
        $rolesParams = $rolesTable->getSiteAdminRoles(array("page_category_id" => $sitepage->category_id, 'role_ids' => $roles_id));

      if (!empty($pageAdminRoles)) {
        $pageAdminRole = $rolesTable->getPageadminsRoles($sitepage->page_id);
        $rolesParams = array_merge($rolesParams, $pageAdminRole);
      }
      if ($othersRoles) {
        $rolesParams[] = '0';
      }
      if (count($rolesParams) < 1)
        return $this->setNoRender();
    }
    //}
    
    $sitepagememberProfile = Zend_Registry::isRegistered('sitepagememberProfile') ? Zend_Registry::get('sitepagememberProfile') : null;
    if (empty($sitepagememberProfile)) {
      return $this->setNoRender();
    }

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    $pageMemberPhraseNum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.phrase.num', null);

    $memberJoinType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.join.type', null);
    if ($memberJoinType != $pageMemberPhraseNum) {
      return $this->setNoRender();
    }   

    //TOTAL members
    $memberCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'membership');

    $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

    if (empty($memberCount) && empty($can_edit)) {
      return $this->setNoRender();
    }

    $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');    
    
    $values = array();
    //END MANAGE-ADMIN CHECK
    if (!empty($rolesParams)) {
      $values['roles_id'] = $rolesParams;
    }
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    $this->view->widgets = $widgets = Engine_Api::_()->sitepage()->getwidget($layout, $sitepage->page_id);

    $this->view->module_tabid = $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $isajax = $this->_getParam('is_ajax_load', null);
    $this->view->isajax = $isajax;
    $this->view->showtoptitle = $showtoptitle = Engine_Api::_()->sitepage()->showtoptitle($layout, $sitepage->page_id);

    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {
      $this->view->identity_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;

      //GET SEARCHING PARAMETERS
      $this->view->page = $page = $this->_getParam('page', 1);
      $this->view->search = $search = $this->_getParam('search');
      $this->view->search_text = $search = $this->_getParam('search_text');
      $this->view->role_id = $role_id = $this->_getParam('role_id', 0);
      $this->view->visibility = $selectbox = $this->_getParam('visibility');
      $this->view->member_encoded = $member_encoded = (int) $this->_getParam('member_encoded', 1);

      if (!empty($search)) {
        $values['search'] = $search;
      }

      if (!empty($selectbox)) {
        $values['orderby'] = $selectbox;
      }

      $values['page_id'] = $sitepage->page_id;
      $this->view->test  = $test = $membershipTable->getallsitepagemembersSelect($sitepage->page_id);
     // $this->view->inviteMembers  = $inviteMembers = $membershipTable->getinvitedsitepagemembersSelect($sitepage->page_id);

      if (!empty($role_id)) {
        $values['roles_id'] = $role_id > 0 ? array($role_id) : array(0);
      }

      $this->view->request_count = $membershipTable->getSitepagemembersPaginator($values, 'request');

      //MAKE PAGINATOR
      $currentPageNumber = $this->_getParam('page', 1);

      $this->view->paginator = $paginator = $membershipTable->getSitepagemembersPaginator($values);
      //if ($paginator->getTotalItemCount() == 0) return $this->setNoRender();
      $paginator->setItemCountPerPage(24)->setCurrentPageNumber($currentPageNumber);

      //ADD NUMBER OF POLLS IN TAB
      if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
        $this->_childCount = $paginator->getTotalItemCount();
      } else {
        if (empty($this->view->search) && empty($can_edit)) {
          return $this->setNoRender();
        }
      }
      
        //CHECK IF USER IS JOIN THE PAGE OR NOT.
        //$friendId = $viewer->membership()->getMembershipsOfIds();
        $select = $membershipTable->hasMembers($viewer_id, $sitepage->page_id);
        if (!empty($select)) {
          $this->view->hasMember = 1;
        }      
        
        $this->view->roleParamsArray = $rolesTable->rolesParams(array($sitepage->category_id), 0, $rolesParams, 1, $sitepage->page_id);
      
    } else {
      $this->view->show_content = false;
      $this->view->identity_temp = $this->view->identity;
      $values['page_id'] = $sitepage->page_id;
      $values['show_count'] = 1;

      if ($can_edit) {
        $this->view->request_count = $membershipTable->getSitepagemembersPaginator($values, 'request');

       $this->view->paginator = $paginator = $membershipTable->getSitepagemembersPaginator($values);


        if ($paginator->getTotalItemCount() > 0) {
          $this->_childCount = $paginator->getTotalItemCount();
        }
      } else {
        $this->view->request_count = $membershipTable->getSitepagemembersPaginator($values, 'request');
        $this->view->paginator = $paginator = $membershipTable->getSitepagemembersPaginator($values);

        if ($paginator->getTotalItemCount() == 0)
          return $this->setNoRender();

        if ($paginator->getTotalItemCount() > 0) {
          $this->_childCount = $paginator->getTotalItemCount();
        }
      }
    }
    $this->view->user_layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);

		if($sitepage->member_title && $sitepage->member_count > 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1) && $show_option){
				$this->getElement()->setTitle($sitepage->member_title);
		}
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}
