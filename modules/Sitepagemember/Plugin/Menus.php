<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_Plugin_Menus {

  public function canViewMembers() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.member.show.menu', 1)) {
      return false;
    }
    $userstable = Engine_Api::_()->getDbtable('users', 'user');
    $userstableName = $userstable->info('name');
    
    $memberTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
    $membershipTableName = $memberTable->info('name');
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');
    $select = $memberTable->select()
                    ->setIntegrityCheck(false)
                    ->from($pageTableName, array('COUNT(*) as count'))
                    ->join($membershipTableName, $membershipTableName . '.resource_id = ' . $pageTableName . '.page_id', array(''))
                    ->join($userstableName, $userstableName . '.user_id = ' . $membershipTableName . '.user_id', array(''))
                    ->where($membershipTableName .'.active = ?', 1)
                    ->where($pageTableName . '.closed = ?', '0')
                    ->where($pageTableName . '.approved = ?', '1')
                    ->where($pageTableName . '.search = ?', '1')
                    ->where($pageTableName . '.declined = ?', '0')
                    ->where($pageTableName . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    } 
    $row = $select->query()->fetchColumn();
    //$count = count($row);
    if (empty($row)) {
      return false;
    }
    return true;
  }
  
  public function onMenuInitialize_SitepageGutterJoin($row) {

    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    if (empty($sitepage->member_approval)) {
			return false;
    }
    if($sitepage->join_enable == 0) {
        return false;
    }
    
    $sitepagememberMenuActive = Zend_Registry::isRegistered('sitepagememberMenuActive') ? Zend_Registry::get('sitepagememberMenuActive') : null;
    if( empty($sitepagememberMenuActive) ) {
      return false;
    }
    
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (empty($viewer_id)) { 
			return false;
    }
    if ($viewer_id == $sitepage->owner_id) {
			return false;
    }
    
    // PACKAGE BASE PRIYACY START
    $allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    if (empty($allowPage)) {
			return false;
		}
    // PACKAGE BASE PRIYACY END

    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id);

    if (!empty($hasMembers)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitepageGutterLeave($row) {

    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (empty($viewer_id)) { 
			return false;
    }
    if ($viewer_id == $sitepage->owner_id) {
			return false;
    }
    // PACKAGE BASE PRIYACY START
    $allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    if (empty($allowPage)) {
			return false;
		}
    // PACKAGE BASE PRIYACY END
    
    $sitepagememberMenuActive = Zend_Registry::isRegistered('sitepagememberMenuActive') ? Zend_Registry::get('sitepagememberMenuActive') : null;
    if( empty($sitepagememberMenuActive) ) {
      return false;
    }
    
    $isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer_id, $sitepage->getIdentity());
    if (!empty($isPageAdmins)) {
			return false;
    }

		$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id, $params = "Leave");
		
    if (empty($hasMembers)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitepageGutterRequest($row) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    if (!empty($sitepage->member_approval)) {
			return false;
    }
    
    $sitepagememberMenuActive = Zend_Registry::isRegistered('sitepagememberMenuActive') ? Zend_Registry::get('sitepagememberMenuActive') : null;
    if( empty($sitepagememberMenuActive) ) {
      return false;
    }
    if (empty($viewer_id)) { 
			return false;
    }
    if ($viewer_id == $sitepage->owner_id) {
			return false;
    }
    
    // PACKAGE BASE PRIYACY START
    $allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    if (empty($allowPage)) {
			return false;
		}
    // PACKAGE BASE PRIYACY END

		$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id);
    if (!empty($hasMembers)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitepageGutterCancel($row) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    if (!empty($sitepage->member_approval)) {
			return false;
    }
    
    $sitepagememberMenuActive = Zend_Registry::isRegistered('sitepagememberMenuActive') ? Zend_Registry::get('sitepagememberMenuActive') : null;
    if( empty($sitepagememberMenuActive) ) {
      return false;
    }
    if (empty($viewer_id)) { 
			return false;
    }
    if ($viewer_id == $sitepage->owner_id) {
			return false;
    }

    // PACKAGE BASE PRIYACY START
    $allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    if (empty($allowPage)) {
			return false;
		}
    // PACKAGE BASE PRIYACY END

		$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id, $params = 'Cancel');
    if (empty($hasMembers)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitepageGutterInvite($row) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return false;
    }
    
    $sitepagememberMenuActive = Zend_Registry::isRegistered('sitepagememberMenuActive') ? Zend_Registry::get('sitepagememberMenuActive') : null;
    if( empty($sitepagememberMenuActive) ) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    
		//CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
    $pageAdmin = Engine_Api::_()->getDbTable('manageadmins', 'sitepage')->isPageAdmins($viewer_id, $sitepage->getIdentity());
    if (!empty($pageAdmin)) {
			return false;
    }
    
    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id, $params = 'Invite');
    if (empty($hasMembers)) {
      return false;
    }

    // PACKAGE BASE PRIYACY START
    $allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    if (empty($allowPage)) {
			return false;
		}
    // PACKAGE BASE PRIYACY END

    //START MANAGE-ADMIN CHECK
    if (!empty($sitepage->member_invite)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitepageGutterInvitePageadmin($row) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return false;
    }
    
    $sitepagememberMenuActive = Zend_Registry::isRegistered('sitepagememberMenuActive') ? Zend_Registry::get('sitepagememberMenuActive') : null;
    if( empty($sitepagememberMenuActive) ) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    
		//CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
    $pageAdmin = Engine_Api::_()->getDbTable('manageadmins', 'sitepage')->isPageAdmins($viewer_id, $sitepage->getIdentity());
    if (empty($pageAdmin)) {
			return false;
    }
    
    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id, $params = 'Invite');
    if (empty($hasMembers)) {
      return false;
    }
    
    // PACKAGE BASE PRIYACY START
    $allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    if (empty($allowPage)) {
			return false;
		}
    // PACKAGE BASE PRIYACY END
    
    //START MANAGE-ADMIN CHECK
    if (!empty($sitepage->member_invite)) {
			$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
			if (empty($isManageAdmin)) {
				return false;
			}
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }

  public function onMenuInitialize_SitepageGutterRespondinvite($row) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    if ($viewer_id == $sitepage->owner_id) {
			return false;
    }
    
    $sitepagememberMenuActive = Zend_Registry::isRegistered('sitepagememberMenuActive') ? Zend_Registry::get('sitepagememberMenuActive') : null;
    if( empty($sitepagememberMenuActive) ) {
      return false;
    }
    $automaticallyJoin = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.automatically.addmember', 1);
    if (empty($automaticallyJoin) && empty($sitepage->member_approval)) {
			return false;
    }
    // PACKAGE BASE PRIYACY START
    $allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    if (empty($allowPage)) {
			return false;
		}
    // PACKAGE BASE PRIYACY END
		
		$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id, $params = 'Accept');
    if (empty($hasMembers)) {
      return false;
    }

    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id, $params = 'Reject');
    if (empty($hasMembers)  || empty($hasMembers->resource_approved)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    return $params;
  }
  
	public function onMenuInitialize_SitepageGutterManageJoinedMembers($row) {

		//GETTING THE VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$joinePages = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($viewer->getIdentity(), 'pageJoin');
    if ($joinePages->getTotalItemCount() == 0) {
      return false;
    }
    
		$coreContentTable = Engine_Api::_()->getDbtable('content', 'core');
		$coreContentTableName = $coreContentTable->info('name');

		$select = new Zend_Db_Select($coreContentTable->getAdapter());
		$select->from($coreContentTableName, 'content_id')->where("name = ?", 'sitepage.profile-joined-sitepage');
		$data = $select->query()->fetchColumn();
		if(empty($data)) {
			return false;
		}

		if ($viewer->getIdentity()) {
			//Return EDIT LINK
			return array(
				'label' => $row->label,
				'route' => 'user_profile',
				'params' => array(
					'id' => $viewer->getIdentity(),
					'tab' => $data
				)
			);
		}
	}
	
	public function onMenuInitialize_SitepageGutterRespondmemberinvite($row) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return false;
    }

    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    if ($viewer_id == $sitepage->owner_id) {
			return false;
    }
    
    $sitepagememberMenuActive = Zend_Registry::isRegistered('sitepagememberMenuActive') ? Zend_Registry::get('sitepagememberMenuActive') : null;
    if( empty($sitepagememberMenuActive) ) {
      return false;
    }
    
    $automaticallyJoin = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.automatically.addmember', 1);
    if (!empty($automaticallyJoin) && !empty($sitepage->member_approval)) {
			return false;
    }
    
    // PACKAGE BASE PRIYACY START
    $allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    if (empty($allowPage)) {
			return false;
		}
    // PACKAGE BASE PRIYACY END
		
		$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id, $params = 'Accept');
    if (empty($hasMembers)) {
      return false;
    }

    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id, $params = 'Reject');
    if (empty($hasMembers)  || empty($hasMembers->resource_approved)) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $sitepage->getIdentity();
    $params['params']['param'] = 'Invite';
    return $params;
  }

    public function sitepageGutterNotificationSettings($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
            return false;
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT SUBJECT
        $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

        $row = Engine_Api::_()->getDbTable('membership', 'sitepage')->getRow($sitepage, $viewer);

        if (!$row)
            return false;
        if(!$row->active || !$row->user_approved)
				  return false;
        return array(
            'class' => 'buttonlink icon_sitepage_notification smoothbox',
            'route' => "sitepagemember_approve",
            'action' => 'notification-settings',
            'params' => array(
                'member_id' => $row->member_id,
								'action' => 'notification-settings'
            ),
        );
    }
}