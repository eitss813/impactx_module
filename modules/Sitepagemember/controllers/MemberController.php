<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MemberController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_MemberController extends Seaocore_Controller_Action_Standard {

  protected $_TEMPDATEVALUE = 75633;
  //ACTION FOR MEMBER JOIN THE PAGE.
  public function joinAction() {

    //CHECK AUTH
    if( !$this->_helper->requireUser()->isValid() ) return;

    //SOMMTHBOX
    $this->_helper->layout->setLayout('default-simple');

    //MAKE FORM
    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$this->view->form = $form = new Sitepagemember_Form_Join();
    } else {
      $this->view->form = $form = new Sitepagemember_Form_SitemobileJoin();
    }

    //IF THE MODE IS APP MODE THEN
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      $this->view->sitemapPageHeaderTitle = "Join Page";
      $form->setTitle('');
    }
    
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		
		$page_id = $this->_getParam('page_id');
		$sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
		$owner = $sitepage->getOwner();
		$action_notification = array();
	  	$notificationSettings = Engine_Api::_()->getDbTable('membership', 'sitepage')->notificationSettings(array('user_id' => $sitepage->owner_id, 'page_id' => $page_id, 'columnName' => array('action_notification')));
	  	if($notificationSettings)
 	  	$action_notification = Zend_Json_Decoder::decode($notificationSettings);

    $pageMemberJoinType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.join.type', null);    
    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $page_id);
    $pageMemberJoinType = $pageMemberJoinType + $this->_TEMPDATEVALUE;
    $pageMemberJoinType = @md5($pageMemberJoinType);     
		
    //IF MEMBER IS ALREADY PART OF THE PAGE
    if(!empty($hasMembers)) {
      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have already sent a membership request.')),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }

    //PROCESS FORM
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )	{

      $pageMemberUnitType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.unit.type', null);
      
			//SET THE REQUEST AS HANDLED FOR NOTIFACTION.
			$friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
			if($action_notification && $action_notification['notificationjoin'] == 1) {
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $sitepage, 'sitepage_join');
			} elseif($action_notification && in_array($sitepage->owner_id, $friendId) && $action_notification['notificationjoin'] == 2) {
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $sitepage, 'sitepage_join');
			}
			
			//ADD ACTIVITY
			$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitepage, 'sitepage_join');
				if ( $action ) {
					Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $sitepage ) ;
				}
			Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action,true);
      if( $pageMemberJoinType == $pageMemberUnitType ) {
        //GET VALUE FROM THE FORM.
        $values = $this->getRequest()->getPost();

        $membersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
        $row = $membersTable->createRow();
        $row->resource_id = $page_id;
        $row->page_id = $page_id;
        $row->user_id = $viewer_id;
        //$row->action_notification = '["posted","created"]';

        //FOR CATEGORY WORK.
				if (isset($values['role_id'])) {
					$roleName = array();
					foreach($values['role_id'] as $role_id) {
						$roleName[] = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->getRoleName($role_id);
					}
					$roleTitle = json_encode($roleName);
					$roleIDs = json_encode($values['role_id']);
					if ($roleTitle && $roleIDs) {
            $row->title = $roleTitle;
            $row->role_id = $roleIDs;
          }
				}
				
        //FOR DATE WORK.
        if (!empty($values['year']) || !empty($values['month']) || !empty($values['day'])) {
          $member_date = $values['year'] . '-' . (int) $values['month'] . '-' . (int) $values['day'];
          $row->date = $member_date;
        }

        //IF MEMBER IS ALREADY FEATURED THEN AUTOMATICALLY FEATURED WHEN MEMBER JOIN ANY PAGE.
        $sitepagemember = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id);
        if(!empty($sitepagemember->featured) && $sitepagemember->featured == 1) {
          $row->featured = 1;
        }

        $row->save();

        //MEMBER COUNT INCREASE WHEN MEMBER JOIN THE PAGE.
        Engine_Api::_()->sitepage()->updateMemberCount($sitepage);
        $sitepage->save();

        //AUTOMATICALLY LIKE THE PAGE WHEN MEMBER JOIN THE PAGE.
        $autoLike = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'pagemember.automatically.like' , 0);
        if(!empty($autoLike)) {
          Engine_Api::_()->sitepage()->autoLike($page_id, 'sitepage_page');
        }
        
        //START DISCUSSION WORK WHEN MEMBER JOIN THE PAGE THEN ALL DISCUSSION IS WATCHABLE FOR JOINED MEMBERS.
        if(Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagediscussion')) {
					$results = Engine_Api::_()->getDbTable('topics', 'sitepage')->getPageTopics($page_id);
					if(!empty($results)) {
						foreach($results as $result) {
						
						$topic_id = $result->topic_id;
						
						$db = Engine_Db_Table::getDefaultAdapter();
						
						$db->query("INSERT IGNORE INTO `engine4_sitepage_topicwatches` (`resource_id`, `topic_id`, `user_id`, `watch`, `page_id`) VALUES ('$page_id', '$topic_id', '$viewer_id', '1', '$page_id');");
						}
					}
        }
        //END DISCUSSION WORK WHEN MEMBER JOIN THE PAGE THEN ALL DISCUSSION IS WATCHABLE FOR JOINED MEMBERS.
        
      }

      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You are now a member of this page.')),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }
  }

  //ACTION FOR LEAVE THE PAGE.
  public function leaveAction() {

    //CHECK AUTH
    if( !$this->_helper->requireUser()->isValid()) return;

		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		//GET PAGE ID.
		$page_id = $this->_getParam('page_id');
		$sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
		
    //MAKE FORM
    $this->view->form = $form = new Sitepagemember_Form_Member();
    $form->setTitle('Leave Page');
    $form->setDescription('Are you sure you want to leave this page?');
    $form->submit->setLabel('Leave Page');

     //IF THE MODE IS APP MODE THEN
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      $this->view->sitemapPageHeaderTitle = "Leave Page";
      $form->setTitle('');
    }
    
    //PROCESS FORM
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )	{

      if (!empty($page_id)) {

        //DELETE THE RESULT FORM THE TABLE.
        Engine_Api::_()->getDbtable('membership', 'sitepage')->delete(array('resource_id =?' => $page_id, 'user_id = ?' => $viewer_id));

        //DELETE ACTIVITY FEED OF JOIN PAGE ACCORDING TO USER ID.
        $action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?'  => 'sitepage_join', 'subject_id = ?' => $viewer_id, 'object_id = ?' => $page_id));
        $action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
        if (!empty($action)) {
					$action->delete();
        }

				//REMOVE THE NOTIFICATION.
				$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($sitepage->getOwner(), $sitepage, 'sitepage_join');
				if($notification) {
					$notification->delete();
				}
			
				//MEMBER COUNT DECREASE IN THE PAGE TABLE WHEN MEMBER LEAVE THE PAGE.
				$sitepage->member_count--;
				$sitepage->save();
      }

      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully left this page.')),
        'layout' => 'default-simple',
        
        'parentRefresh' => true,
      ));
    }
  }

  //ACTION FOR REQUEST PAGE.
  public function requestAction() {

    //CHECK AUTH
    if( !$this->_helper->requireUser()->isValid() ) return;

    //SOMMTHBOX
    $this->_helper->layout->setLayout('default-simple');
    
    //MAKE FORM
    $this->view->form = $form = new Sitepagemember_Form_Member();
    $form->setTitle('Request Page Membership');
    $form->setDescription('Would you like to request membership in this page?');
    $form->submit->setLabel('Send Request');
    
		//GET THE VIEWER ID.
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET THE PAGE ID.
		$page_id = $this->_getParam('page_id');
		$sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
		
		$pagetitle = $sitepage->title;
		$page_url = Engine_Api::_()->sitepage()->getPageUrl($page_id);
		
		$page_baseurl = ((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://":"http://") . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page_url), 'sitepage_entry_view', true);		
		$page_title_link = '<a href="' . $page_baseurl . '"  >' . $pagetitle . ' </a>';
			
    $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $page_id);
    $pageJoinType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.join.type', null);

    //IF MEMBER IS ALREADY PART OF THE PAGE
    if(!empty($hasMembers)) {
      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have already sent a membership request.')),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }

    //PROCESS FORM
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

			$sitepagemember = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id);
      $pagePhraseNum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.phrase.num', null);

      //GET MANAGE ADMIN AND SEND NOTIFICATIONS TO ALL MANAGE ADMINS.
			$manageadmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($page_id);
      if( $pageJoinType == $pagePhraseNum ) {			
        foreach($manageadmins as $manageadmin) {

          $user_subject = Engine_Api::_()->user()->getUser($manageadmin['user_id']);

          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitepage, 'sitepagemember_approve');

          //Email to all page admins.
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, 'SITEPAGEMEMBER_REQUEST_EMAIL', array(
              'page_title' => $pagetitle,
              'page_title_with_link' => $page_title_link,
              'object_link' => $page_baseurl,
              //'email' => $email,
              'queue' => true
          ));
        }

        $values = $this->getRequest()->getPost();

        $membersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');

        $row = $membersTable->createRow();
        $row->resource_id = $page_id;
        $row->page_id = $page_id;
        $row->user_id = $viewer_id;
        $row->active = 0;
        $row->resource_approved = 0;
        $row->user_approved = 0;

        if (!empty($sitepagemember->featured) && $sitepagemember->featured == 1) {
          $row->featured = 1;
        }

        $row->save();
      }
			return $this->_forwardCustom('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your page membership request has been sent successfully.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
    }
  }

  //ACTION FOR CANCEL MEMBER REQUEST.
  public function cancelAction() {

    //CHECK AUTH
    if( !$this->_helper->requireUser()->isValid() ) return;

		//GET PAGE ID.
		$page_id = $this->_getParam('page_id');
		$sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
		
		//GET VIEWER ID.
		$viewer = Engine_Api::_()->user()->getViewer();

    //MAKE FORM
    $this->view->form = $form = new Sitepagemember_Form_Member();
    $form->setTitle('Cancel Page Membership Request');
    $form->setDescription('Would you like to cancel your request for membership in this page?');
    $form->submit->setLabel('Cancel Request');

    //PROCESS FORM
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

			//REMOVE THE NOTIFICATION.
			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($sitepage->getOwner(), $sitepage, 'sitepagemember_approve');
			if( $notification ) {
				$notification->delete();
			}

			if (!empty($page_id)) {
				//DELETE THE RESULT FORM THE TABLE.
				Engine_Api::_()->getDbtable('membership', 'sitepage')->delete(array('resource_id =?' => $page_id, 'user_id =?' => $viewer->getIdentity()));
			}

      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Page membership request cancelled.')),
        'layout' => 'default-simple',
        
        'parentRefresh' => true,
      ));
    }
  }

  //RESPOND REQUEST.
  public function respondAction() {
  
    // CHECK AUTH
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();

    //GET THE SITEPAGE ID FROM THE URL
    $page_id = $this->_getParam('page_id');
    $param = $this->_getParam('param');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    $this->view->form = $form = new Sitepagemember_Form_Respond();
    if ($param != 'Invite') {
			$form->setTitle('Respond to Membership Request');
			//$form->setDescription('Respond to Membership Request.');
    } else {
			$form->setTitle('Respond to Membership Invitation');
			//$form->setDescription('Respond to Membership Invitation.');
    }

      // Process form
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }
  
  		//GET VALUE FROM THE FORM.
		$values = $this->getRequest()->getPost(); 

		if (isset($values['accept'])) {
		
// 			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($viewer, $sitepage, 'sitepagemember_invite');
// 			if( $notification ) {
// 				$notification->mitigated = true;
// 				$notification->save();
// 			}
			
      if (!empty($sitepage->member_approval)) {
				//ADD ACTIVITY.
				$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitepage, 'sitepage_join');
				if ( $action ) {
					Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $sitepage ) ;
				}
				Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action,true);
				Engine_Api::_()->getDbtable('membership', 'sitepage')->update(array('active'=>  '1', 'user_approved' => '1'), array('resource_id =?' => $page_id, 'user_id =?' => $viewer->getIdentity()));
				
				//MEMBER COUNT INCREASE WHEN MEMBER JOIN THE PAGE.
				Engine_Api::_()->sitepage()->updateMemberCount($sitepage);
				$sitepage->save();
			} 
			else {
				Engine_Api::_()->getDbtable('membership', 'sitepage')->update(array('active'=>  '0', 'user_approved' => '0', 'resource_approved' => '0'), array('resource_id =?' => $page_id, 'user_id =?' => $viewer->getIdentity()));
			
				//GET MANAGE ADMIN AND SEND NOTIFICATIONS TO ALL MANAGE ADMINS.
				$manageadmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($page_id);
				foreach($manageadmins as $manageadmin) {

					$user_subject = Engine_Api::_()->user()->getUser($manageadmin['user_id']);
                                        $row = Engine_Api::_()->getDbTable('membership', 'sitepage')->getRow($sitepage, $viewer);
					Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitepage, 'sitepagemember_approve', array('member_id' => $row->member_id));
				}
			}
		} 
		else {
			if (!empty($page_id)) {
				//DELETE THE RESULT FORM THE TABLE.
				Engine_Api::_()->getDbtable('membership', 'sitepage')->delete(array('resource_id =?' => $page_id, 'user_id =?' => $viewer->getIdentity()));
			}
		}
             $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($viewer, $sitepage, 'sitepagemember_invite');
 			if( $notification ) {
 				$notification->mitigated = true;
                                $notification->read = true;
 				$notification->save();
 			}

    $this->view->status = true;
    $this->view->error = false;
    if (isset($values['accept']) && !empty($automaticallyJoin) && !empty($sitepage->member_approval)) {
			$message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the page %s.');
    }  elseif (isset($values['accept']) && empty($automaticallyJoin) && empty($sitepage->member_approval)) {
			$message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invitation to join the page %s.');
    }  elseif (isset($values['accept']) && empty($automaticallyJoin) && !empty($sitepage->member_approval)) {
			$message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invitation to join the page %s.');
    } 
    else {
			$message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the page %s.');
    }
    
    $message = sprintf($message, $sitepage->__toString());
    $this->view->message = $message;

    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array($message),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }
  }
  
  //ACTION FOR ACCEPT PAGE MEMBER REQUEST.
  public function acceptAction() {

    //CHECK AUTH
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();

    //GET THE SITEPAGE ID FROM THE URL
    $page_id = $this->_getParam('page_id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    
    //MAKE FORM
    $this->view->form = $form = new Sitepagemember_Form_Member();
    $form->setTitle('Accept Page Invitation');
    $form->setDescription('Would you like to accept page invitation for this page?');
    $form->submit->setLabel('Accept Page Invitation');

    //PROCESS FORM
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }

		//SET THE REQUEST AS HANDLED
		$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($viewer, $sitepage, 'sitepagemember_invite');
		if( $notification ) {
			$notification->mitigated = true;
			$notification->save();
		}

		//GET VALUE FROM THE FORM.
		$values = $this->getRequest()->getPost();
		
		//ADD ACTIVITY
		$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitepage, 'sitepage_join');
		if ( $action ) {
       Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $sitepage ) ;
    }
		Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action,true);
		Engine_Api::_()->getDbtable('membership', 'sitepage')->update(array('active'=>  '1', 'user_approved' => '1'), array('resource_id =?' => $page_id, 'user_id =?' => $viewer->getIdentity()));
		
		//MEMBER COUNT INCREASE WHEN MEMBER JOIN THE PAGE.
		Engine_Api::_()->sitepage()->updateMemberCount($sitepage);
		$sitepage->save();
		
    $this->view->status = true;
    $this->view->error = false;

    $message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the page %s');
    $message = sprintf($message, $sitepage->__toString());
    $this->view->message = $message;

    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array($message),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }
  }

  //ACTION FOR REJECT PAGE MEMBER REQUEST.
  public function rejectAction() {

    //CHECK AUTH
    if( !$this->_helper->requireUser()->isValid() ) return;

    //PROCESS
    $viewer = Engine_Api::_()->user()->getViewer();
    
    //GET THE SITEPAGE ID FROM THE URL
    $page_id = $this->_getParam('page_id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //MAKE FORM
    $this->view->form = $form = new Sitepagemember_Form_Member();
    $form->setTitle('Reject Page Invitation');
    $form->setDescription('Would you like to reject the invitation for this page?');
    $form->submit->setLabel('Reject Page Invitation');
    
    //PROCESS FORM
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }

		if (!empty($page_id)) {
		
			//DELETE THE RESULT FORM THE TABLE.
			Engine_Api::_()->getDbtable('membership', 'sitepage')->delete(array('resource_id =?' => $page_id, 'user_id =?' => $viewer->getIdentity()));
		}
		
    $this->view->status = true;
    $this->view->error = false;
    $message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the page %s');
    $message = sprintf($message, $sitepage->__toString());
    $this->view->message = $message;

    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array($message),
        'layout' => 'default-simple',
        
        'parentRefresh' => true,
      ));
    }
  }
  
  //ACTION FOR THE INVITE MEMBER.
  public function inviteMembersAction() {

    if( !$this->_helper->requireUser()->isValid() ) return;

    //GET PAGE ID.
    $this->view->page_id = $page_id = $this->_getParam('page_id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
		$isPageAdmin = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $sitepage->getIdentity());
		
    $automaticallyJoin = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.automatically.addmember', 1);

    //PREPARE FORM
    $this->view->form = $form = new Sitepagemember_Form_InviteMembers(array(
        'page_id'=> $page_id
    ));

    $this->view->externalform = $externalform = new Sitepagemember_Form_InviteExternalMembers(array(
        'page_id' => $page_id
    ));

    $externalform->populate(array(
        'page_id' => $page_id,
        'message' => 'You are being invited to join our ImpactX and Join to the Organizations - '. $sitepage->getTitle()
    ));

    $externalform->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitepagemember', 'controller' => 'member' ,'action' => 'invite-external' , 'page_id' => $page_id ),'sitepagemember_extended', true));

      //IF THE MODE IS APP MODE THEN
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      $memberSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.automatically.addmember' , 1);
      if (!empty($memberSettings)) {
        $text = "Add People";
      }else{
        $text = "Invite People";
      }
      $this->view->sitemapPageHeaderTitle = $text;
      $form->setTitle('');
    }
    
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

      $user_id=null;

      if( $values['user_ids'] && strpos($values['user_ids'], '@') !== false && strpos($values['user_ids'], '.') !== false) {
          $userTable = Engine_Api::_()->getItemTable('user');
          $userTableName = $userTable->info('name');

          $user_id=null;

          if(strpos($values['user_ids'], ',') !== false){
              $getMail = explode(",", $values['toValues']);

              $members_ids =array();
              foreach ($getMail as $val) {
                  if(strpos($val, '@') !== false)
                      $user_id = $userTable->select()->from($userTableName, 'user_id')->where('email =?', $values['user_ids'])->query()->fetchColumn();
                  else {
                      $user_id = $val;
                  }
                  array_push($members_ids,$user_id);
              }

          } else{

              $user_id = $userTable->select()->from($userTableName, 'user_id')->where('email =?', $values['user_ids'])->query()->fetchColumn();
              $members_ids =  (array) $user_id;
              $memberEmail = $values['user_ids'] ? $values['user_ids'] :null;
          }
          array_unique($members_ids);
      }
      else {
          $members_ids = explode(",", $values['toValues']);

      }

      if(!empty($memberEmail) && !$user_id){
          //external members mail flow
          $message = 'You are being invited to join our ImpactX and Join to the Organizations - '. $sitepage->getTitle();
          if(!empty($memberEmail)){

              $viewer = Engine_Api::_()->user()->getViewer();
              $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
              $db = $inviteTable->getAdapter();
              $db->beginTransaction();

              try {
                  $emailsSent = $inviteTable->sendCustomORGInvites($viewer, $memberEmail, $message,1, $page_id, $memberEmail,1);
                  $db->commit();
              } catch( Exception $e ) {
                  $db->rollBack();
                  if( APPLICATION_ENV == 'development' ) {
                      throw $e;
                  }
              }

          }



      }

    //$members_ids = explode(",", $values['toValues']);
    
//    if(empty($values['user_ids']) && empty($values['toValues'])) {
//        $form->addError('Please complete this field - It is requried.');
//        return;
//    }
   if(empty($values['toValues'])) {
    // $form->addError('Please complete this field - It is requried.');
    //  return;
   }

//   if(empty($values['toValues'])) {
//      $form->addError('This is an invalid user name. Please select a valid user name from the autosuggest.');
//      return;
//   }

    $membersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
    
		if (!empty($members_ids)) {
		
			foreach($members_ids as $members_id) {

				$row = $membersTable->createRow();
				$row->resource_id = $page_id;
				$row->page_id = $page_id;
				$row->user_id = $members_id;
				$row->resource_approved = 1;
				
				if($members_id != 0) {
                    if (!empty($automaticallyJoin) && !empty($sitepage->member_approval)) {

                        $row->active = 1;
                        $row->user_approved = 1;
                        $row->save();
                        //MEMBER COUNT INCREASE WHEN MEMBER JOIN THE PAGE.
                        Engine_Api::_()->sitepage()->updateMemberCount($sitepage);
                        $sitepage->save();
                    } elseif (!empty($automaticallyJoin) && empty($sitepage->member_approval) && !empty($isPageAdmin)) {
                        $row->active = 1;
                        $row->user_approved = 1;
                        $row->save();
                        //MEMBER COUNT INCREASE WHEN MEMBER JOIN THE PAGE.
                        Engine_Api::_()->sitepage()->updateMemberCount($sitepage);
                        $sitepage->save();
                    }
                    else {
                        $row->active = 0;
                        //$row->resource_approved = 0;
                        $row->user_approved = 0;
                    }

                    if (isset($values['role_id'])) {
                        $roleName = array();
                        foreach($values['role_id'] as $role_id) {
                            $roleName[] = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->getRoleName($role_id);
                        }
                        $roleTitle = json_encode($roleName);
                        $roleIDs = json_encode($values['role_id']);
                        if ($roleTitle && $roleIDs) {
                            $row->title = $roleTitle;
                            $row->role_id = $roleIDs;
                        }
                    }

                    $row->save();

                    /// Send email start
                            //GET PAGE TITLE
                            $pagetitle = $sitepage->title;
                            //PAGE URL
                            $page_url = Engine_Api::_()->sitepage()->getPageUrl($sitepage->page_id);
                            //GET PAGE URL
                            $page_baseurl = 'http://' . $_SERVER['HTTP_HOST'] .
                                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page_url), 'sitepage_entry_view', true);

                            //MAKING PAGE TITLE LINK
                            $page_title_link = '<a href="' . $page_baseurl . '"  >' . $pagetitle . ' </a>';
                            $userTable = Engine_Api::_()->getItemTable('user');
                            $userTableName = $userTable->info('name');
                            // check if email is present in user table
                            $email =  $userTable->select()->from($userTableName, 'email')->where('user_id =?', $members_id)->query()->fetchColumn();

                            //EMAIL THAT GOES TO NEW ORG MEMBER
                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'SITEPAGEMEMBER_APPROVE_EMAIL', array(
                                'page_title' => $pagetitle,
                                'page_title_with_link' => $page_title_link,
                                'object_link' => $page_baseurl,
                                'email' => $email,
                                'queue' => true
                            ));

                    ///Send  email end


                    if (empty($automaticallyJoin)) {
                        $user_subject = Engine_Api::_()->user()->getUser($members_id);
                        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitepage, 'sitepagemember_invite');
                    }
                    else {
                        $user_subject = Engine_Api::_()->user()->getUser($members_id);
                        //SET THE REQUEST AS HANDLED FOR NOTIFACTION.
                        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitepage, 'sitepage_addmember');

                        //ADD ACTIVITY
                        $action=Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user_subject, $sitepage, 'sitepage_join');
                        if ( $action ) {
                            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $sitepage ) ;
                        }
                        Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action,true);
                    }
                }
			}
		}
		
    return $this->_forwardCustom('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected members have been successfully added to this page.')),
      'layout' => 'default-simple',
      
      'parentRefresh' => true,
    ));
  }

    public function inviteExternalAction(){

        if( !$this->getRequest()->isPost() ) {
            return;
        }

        $values = $_POST;

        if(!empty($values['recipients'])){

            $viewer = Engine_Api::_()->user()->getViewer();
            $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
            $db = $inviteTable->getAdapter();
            $db->beginTransaction();

            try {
                $emailsSent = $inviteTable->sendCustomORGInvites($viewer, $values['recipients'], @$values['message'],$values['friendship'], $values['page_id'], $values['recipient_name'],$values['role_id']);
                $db->commit();
            } catch( Exception $e ) {
                $db->rollBack();
                if( APPLICATION_ENV == 'development' ) {
                    throw $e;
                }
            }

            return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully Invited.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
        }

    }


    //ACTION FOR THE INVITE MEMBER.
  public function inviteAction() {

    if( !$this->_helper->requireUser()->isValid() ) return;

    //GET PAGE ID.
    $page_id = $this->_getParam('page_id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    
    $automaticallyJoin = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.automatically.addmember', 1);
    
    //PREPARE DATA
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->friends = $friends = $viewer->membership()->getMembers();

    $hasMembers_viewer = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer->getIdentity(), $sitepage->getIdentity());

    //PREPARE FORM
    $this->view->form = $form = new Sitepagemember_Form_Invite();

    $count = 0;
    foreach( $friends as $friend ) {
    
			$friend_id = $friend->getIdentity();

			$hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($friend_id, $page_id);

			if(!empty($hasMembers)) {
				continue;
		  }
      //if( $sitepage->membership()->isMember($friend, null) ) continue;
      $multiOptions[$friend->getIdentity()] = $friend->getTitle();
      $count++;
    }
    sort($multiOptions);
    $form->users->addMultiOptions($multiOptions);
    $this->view->count = $count;

    // throw notice if count = 0
    if( $count == 0 ) {
      return $this->_forwardCustom('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have currently no friends to invite.')),
      'layout' => 'default-simple',
      'parentRefresh' => true,
      ));
    }

    //NOT POSTING
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $usersIds = $form->getValue('users');
		foreach( $friends as $friend ) {
		
			if( !in_array($friend->getIdentity(), $usersIds) ) {
				continue;
			}

			//GET VALUE FROM THE FORM.
			$values = $this->getRequest()->getPost();
			$membersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
			$row = $membersTable->createRow();
			$row->resource_id = $page_id;
			$row->page_id = $page_id;
			$row->user_id = $friend->getIdentity();
			$row->resource_approved = 1;
			
			if (!empty($automaticallyJoin) && !empty($sitepage->member_approval)) {
				$row->active = 1;
				$row->user_approved = 1;

				//MEMBER COUNT INCREASE WHEN MEMBER JOIN THE PAGE.
				Engine_Api::_()->sitepage()->updateMemberCount($sitepage);
				$sitepage->save();
			} else {
				$row->active = 0;
				if(!empty($automaticallyJoin) && empty($sitepage->member_approval)) {
					$row->resource_approved = 0;
				}
				$row->user_approved = 0;
			}
			
			$row->save();
			
      if (empty($automaticallyJoin)) {
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($friend, $viewer, $sitepage, 'sitepagemember_invite');
			} elseif (!empty($automaticallyJoin) && empty($sitepage->member_approval)) {
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($friend, $viewer, $sitepage, 'sitepagemember_invite');
			}
		}
    if (!empty($automaticallyJoin) && !empty($sitepage->member_approval)) {
			$messages = Zend_Registry::get('Zend_Translate')->_('Members have been successfully added.');
    } else {
			$messages = Zend_Registry::get('Zend_Translate')->_('Members have been successfully invited.');
    }
    return $this->_forwardCustom('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_($messages)),
      'layout' => 'default-simple',
      'parentRefresh' => true,
    ));
  }
  
  //ACTION FOR JOINED MORE PAGES BY MEMBERS.
  public function getMoreJoinedPagesAction() {
  
		$page_title = $this->_getParam('text', null); 
		$user_id = $this->_getParam('user_id', null);
    $data = array();

    $joinPage = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($user_id, 'memberOfDay');

    $pageIds = array();

    foreach( $joinPage as $joinPages ) {
      $pageIds[] = $joinPages['page_id'];
    }

    $morepages = Engine_Api::_()->sitepagemember()->getMorePage($pageIds, $page_title);

    foreach ($morepages as $morepage) {
			$page_photo = $this->view->itemPhoto($morepage, 'thumb.icon');
      $data[] = array(
				'id' => $morepage->page_id,
				'label' => $morepage->title,
				'photo' => $page_photo
      );
    }

    return $this->_helper->json($data);
  }
  
  //ACTION FOR JOINED MERE PAGES.
  public function joinedMorePagesAction() {
  
    $this->view->user_id = $user_id = $this->_getParam('user_id');
    
    //SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitepagemember_Form_JoinedMorePages();

    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    //$form->getElement('title')->setLabel('Enter the name of the page which you want to join.');

    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

    //PROCESS FORM
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )	{

			//GET VALUE FROM THE FORM.
			$values = $this->getRequest()->getPost();
			$page_id = $values['page_id'];
			$sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
			$owner = $sitepage->getOwner();

			//SET THE REQUEST AS HANDLED FOR NOTIFACTION.
			Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $sitepage, 'sitepage_join');
			
			//ADD ACTIVITY
			$action=Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitepage, 'sitepage_join');
		  	if ( $action ) {
				Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity( $action , $sitepage ) ;
			}
	    Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action,true);
			$membersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
			$row = $membersTable->createRow();
			$row->resource_id = $page_id;
			$row->page_id = $page_id;
			$row->user_id = $viewer_id;
			
			//IF MEMBER IS ALREADY FEATURED THEN AUTOMATICALLY FEATURED WHEN MEMBER JOIN THE ANY PAGE.
			$sitepagemember = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id);
			if(!empty($sitepagemember->featured) && $sitepagemember->featured == 1) {
				$row->featured = 1;
			}

			$row->save();

      //MEMBER COUNT INCREASE WHEN MEMBER JOIN THE PAGE.
			Engine_Api::_()->sitepage()->updateMemberCount($sitepage);
			$sitepage->save();

		  //AUTOMATICALLY LIKE THE PAGE WHEN MEMBER JOIN THE PAGE.
		  $autoLike = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'pagemember.automatically.like' , 0);
		  if(!empty($autoLike)) {
				Engine_Api::_()->sitepage()->autoLike($page_id, 'sitepage_page');
      }

      return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You are now a member of this page.')),
        'layout' => 'default-simple',
        'parentRefresh' => true,
      ));
    }
  }

  public function requestMemberAction() {
    $this->view->notification = $notification = $this->_getParam('notification');
  }
}
