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
class Sitepagemember_Widget_SitemobileProfileSitepagemembersController extends Engine_Content_Widget_Abstract {

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
		//START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
    $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitepagemember');

    $this->view->show_option = $show_option = $this->_getParam('show_option', 1);
    $this->view->roles_id = $roles_id = $this->_getParam('roles_id', null);
    $rolesParams = array();
    if ($show_option == 0) {
      $othersRoles = in_array('0', $roles_id);
      $pageAdminRoles = in_array('pageadminRole', $roles_id);

      if ($othersRoles) {
        unset($role_id[array_search('0', $role_id)]);
      }

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

    $this->view->roleParamsArray = $roleParamsArray = $rolesTable->rolesParams(array($sitepage->category_id), 0, $rolesParams, 1);
    $memberJoinType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.join.type', null);
    $sitepagememberProfile = Zend_Registry::isRegistered('sitepagememberProfile') ? Zend_Registry::get('sitepagememberProfile') : null;
    if (empty($sitepagememberProfile)) {
      return $this->setNoRender();
    }

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    $pageMemberPhraseNum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.phrase.num', null);

    //GET PAGE ID
    $this->view->page_id = $page_id = $sitepage->page_id;

    if ($memberJoinType != $pageMemberPhraseNum) {
      return $this->setNoRender();
    }

    //CHECK IF USER IS JOIN THE PAGE OR NOT.
    $friendId = $viewer->membership()->getMembershipsOfIds();
    $select = $membershipTable->hasMembers($viewer_id, $sitepage->page_id);
    if (!empty($select)) {
      $this->view->hasMember = 1;
    }

    //TOTAL members
    $memberCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepage', 'membership');

    $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');


    if (empty($memberCount) && empty($can_edit)) {
      return $this->setNoRender();
    }
    $values = array();
    //END MANAGE-ADMIN CHECK
    if (!empty($rolesParams)) {
      $values['roles_id'] = $rolesParams;
    }


		//GET SEARCHING PARAMETERS
		$this->view->role_id = $role_id = $this->_getParam('role_id', 0);
		$this->view->member_encoded = $member_encoded = (int) $this->_getParam('member_encoded', 1);
    $this->view->visibility = $selectbox = $this->_getParam('visibility', 'join_date');
		$values['page_id'] = $page_id;

		if (!empty($selectbox)) {
			$values['orderby'] = $selectbox;
		}

		if (!empty($role_id)) {
			$values['roles_id'] = $role_id > 0 ? array($role_id) : array(0);
		}

		$this->view->request_count = $membershipTable->getSitepagemembersPaginator($values, 'request');
		$this->view->paginator = $paginator = $membershipTable->getSitepagemembersPaginator($values);
		$this->view->paginator->setItemCountPerPage(10)->setCurrentPageNumber($this->_getParam('page', 1));
		$this->_childCount = $paginator->getTotalItemCount();
		if($sitepage->member_title && $sitepage->member_count > 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1) && $show_option){
				$this->getElement()->setTitle($sitepage->member_title);
		}
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}