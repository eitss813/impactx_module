<?php

class Impactx_MemberController extends Seaocore_Controller_Action_Standard
{
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
    
    
    // echo $pageMemberJoinType ."\n " . $pageMemberUnitType;die;


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
}
