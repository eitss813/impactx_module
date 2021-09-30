<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TopicController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_TopicController extends Seaocore_Controller_Action_Standard {

  public function init() {

    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //PACKAGE BASE PRIYACY START
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagediscussion")) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdicreate');
        if (empty($isPageOwnerAllow)) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if (0 !== ($topic_id = (int) $this->_getParam('topic_id'))) {
        $topic = Engine_Api::_()->getItem('sitepage_topic', $topic_id);
        $page_id = $topic->page_id;
      }
      if (Engine_Api::_()->core()->hasSubject('sitepage_page') != null) {
        $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        if (!empty($sitepage))
          $page_id = $sitepage->page_id;
      }
    }

    if (Engine_Api::_()->core()->hasSubject())
      return;

    if (0 !== ($topic_id = (int) $this->_getParam('topic_id')) &&
            null !== ($topic = Engine_Api::_()->getItem('sitepage_topic', $topic_id))) {
      Engine_Api::_()->core()->setSubject($topic);
    } else if (0 !== ($page_id = (int) $this->_getParam('page_id')) &&
            null !== ($sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id))) {
      Engine_Api::_()->core()->setSubject($sitepage);
    }
  }

  //ACTION FOR SHOWING THE TOPIC
  public function indexAction() {

    //CHECK SITEPAGE SUBJECT IS VALID OR NOT
    if (!$this->_helper->requireSubject('sitepage_page')->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //GET SITEPAGE SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject();

    //SEND THE TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('topics', 'sitepage')->getPageTopics($sitepage->getIdentity());
    $paginator->setCurrentPageNumber($this->_getParam('page'));
  }

  //ACTION FOR VIEW THE TOPIC
  public function viewAction() {

    //CHECK SITEPAGE SUBJECT IS VALID OR NOT
    if (!$this->_helper->requireSubject('sitepage_topic')->isValid())
      return;
    
    //SEND THE TOPIC SUBJECT TO THE TPL
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

    //GET THE SITEPAGE ITEM 
    $this->view->sitepage = $sitepage = $topic->getParentSitepage();    

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if (empty(Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.0'))) {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    }    

  }

  //ACTION FOR CREATING THE TOPIC, POST 
  public function createAction() {

    //USER VALIDATION REQURIED
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK SITEPAGE SUBJECT IS VALID OR NOT
    if (!$this->_helper->requireSubject('sitepage_page')->isValid())
      return;

    //GET SITEPAGE SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //GET LOGGED USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //MAKE FORM
    $this->view->form = $form = new Sitepage_Form_Topic_Create();

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');
    $this->view->resource_type = $this->_getParam('resource_type', null);
    $this->view->resource_id = $this->_getParam('resource_id', 0);
    //CHECK METHOD / DATA
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['page_id'] = $page_id = $sitepage->getIdentity();

    $topicTable = Engine_Api::_()->getDbtable('topics', 'sitepage');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitepage');
    $postTable = Engine_Api::_()->getDbtable('posts', 'sitepage');

    //GET DB
    $db = $sitepage->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //CREATE TOPIC
      $values['resource_type'] = $this->_getParam('resource_type', null);
      $values['resource_id'] = $this->_getParam('resource_id', 0);
      $topic = $topicTable->createRow();
      $topic->setFromArray($values);
      $topic->view_count = 1;
      $topic->save();
      $values['topic_id'] = $topic->topic_id;

      //CREATE POST
      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      $topicWatchesTable->insert(array(
          'resource_id' => $sitepage->getIdentity(),
          'topic_id' => $topic->getIdentity(),
          'user_id' => $viewer->getIdentity(),
          'watch' => (bool) $values['watch'],
          'page_id' => $page_id,
      ));

      //START WORK WHEN ANY ONE CREATE DISCUSSION IN THE PAGE AND IF PAGE HAVE MANY MEMBER THEN ENTRY FOR ALL PAGE MEMEBR IS WATHCED.
      if(Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagemember')) {
      $paginator = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($sitepage->getIdentity(), '', '', 0);
				if(!empty($paginator)) {
					foreach($paginator as $result) {						
						$user_id = $result->user_id;
						$topic_id = $topic->getIdentity();

						$db = Engine_Db_Table::getDefaultAdapter();
						$db->query("INSERT IGNORE INTO `engine4_sitepage_topicwatches` (`resource_id`, `topic_id`, `user_id`, `watch`, `page_id`) VALUES ('$page_id', '$topic_id', '$user_id', '1', '$page_id');");
					}
				}
      }
      //END WORK WHEN ANY ONE CREATE DISCUSSION IN THE PAGE AND IF PAGE HAVE MANY MEMBER THEN ENTRY FOR ALL PAGE MEMEBR IS WATHCED.

      //ADD ACTIVITY
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $activityFeedType = null;
        if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
          $activityFeedType = 'sitepage_admin_topic_create';
        elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage))
          $activityFeedType = 'sitepage_topic_create';


        if ($activityFeedType) {
          $action = $activityApi->addActivity($viewer, $sitepage, $activityFeedType, null, array('child_id' => $topic->getIdentity()));
          Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
        }
        if ($action) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $topic);
          //SENDING ACTIVITY FEED TO FACEBOOK.
          $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
          if (!empty($enable_Facebooksefeed)) {
            $topiccreate_array = array();
            $topiccreate_array['type'] = 'sitepage_topic_create';
            $topiccreate_array['object'] = $topic;
            $topiccreate_array['description'] = $values['body'];
            Engine_Api::_()->facebooksefeed()->sendFacebookFeed($topiccreate_array);
          }
        }
        	//PAGE NOTE CREATE NOTIFICATION AND EMAIL WORK
// 				$sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
// 				if ($sitepageVersion >= '4.3.0p1') {
          if(!empty($action)) {
						Engine_Api::_()->sitepage()->sendNotificationEmail($topic, $action, 'sitepagediscussion_create', 'SITEPAGEDISCUSSION_CREATENOTIFICATION_EMAIL', 'Pageevent Invite');
						
						$isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $page_id);
						if (!empty($isPageAdmins)) {

							//NOTIFICATION FOR ALL FOLLWERS.
							Engine_Api::_()->sitepage()->sendNotificationToFollowers($topic, $action, 'sitepagediscussion_create');
						}
					}
				//}
      }

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECT TO THE TOPIC VIEW PAGE
    return $this->_redirectCustom($topic->getHref(array('tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab'))), array('prependBase' => false));
  }

  //ACTION FOR SENDING THE POST 
  public function postAction() {

    //USER VALIDATION REQURIED
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitepage_topic')->isValid())
      return;

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GET TOPIC SUBJECT
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

    //GET SITEPAGE SUBJECT ADN PAGE ID
    $this->view->sitepage = $sitepage = $topic->getParentSitepage();
    $page_id = $sitepage->page_id;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    if ($topic->closed) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('This topic is closed for posting.');
      return;
    }
    //MAKE FORM
    $this->view->form = $form = new Sitepage_Form_Post_Create();

    $quote_id = $this->getRequest()->getParam('quote_id');
    if( !empty($quote_id) ) {
      $quote = Engine_Api::_()->getItem('sitepage_post', $quote_id);
      if($quote->user_id == 0) {
          $owner_name = Zend_Registry::get('Zend_Translate')->_('Deleted Member');
      } else {
          $owner_name = $quote->getOwner()->__toString();
      }

			$form->body->setValue("<blockquote><strong>" . $this->view->translate('%1$s said:', $owner_name) . "</strong><br />" . $quote->body . "</blockquote><br />");

    }

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    //PROCESS
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET TOPIC OWNER
    $topicOwner = $topic->getOwner();

    //SELF CREATED TOPIC OR NOT
    $isOwnTopic = $viewer->isSelf($topicOwner);

    //GET POST TABLE
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitepage');

    //GET FORM VALUES
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['page_id'] = $sitepage->getIdentity();
    $values['topic_id'] = $topic->getIdentity();

    $watch = (bool) $values['watch'];
    $isWatching = $topicWatchesTable->isWatching($sitepage->getIdentity(), $topic->getIdentity(), $viewer->getIdentity());

    //GET DB
    $db = $sitepage->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //CREATE POST
      $post = Engine_Api::_()->getDbtable('posts', 'sitepage')->createRow();
      $post->setFromArray($values);
      $post->save();

      //WATCH
      if (false === $isWatching) {
        $topicWatchesTable->insert(array(
            'resource_id' => $sitepage->getIdentity(),
            'topic_id' => $topic->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'watch' => (bool) $watch,
            'page_id' => $values['page_id'],
        ));
      } else if ($watch != $isWatching) {
        $topicWatchesTable->update(array(
            'watch' => (bool) $watch,
            'page_id' => $values['page_id'],
                ), array(
            'resource_id = ?' => $sitepage->getIdentity(),
            'topic_id = ?' => $topic->getIdentity(),
            'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      //ADD ACTIVITY
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $activityFeedType = null;
        if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
          $activityFeedType = 'sitepage_admin_topic_reply';
        elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage))
          $activityFeedType = 'sitepage_topic_reply';


  //      if ($activityFeedType) {
  //        $action = $activityApi->addActivity($viewer, $sitepage, $activityFeedType);
  //        Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
  //      }
        //ACTIVITY      
        if ($activityFeedType) {
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitepage, $activityFeedType, null, array('child_id' => $topic->getIdentity()));
          Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
          if (!empty($action))
            $action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);

          //SENDING ACTIVITY FEED TO FACEBOOK.
          $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
          if (!empty($enable_Facebooksefeed)) {
            $topicreply_array = array();
            $topicreply_array['type'] = 'sitepage_topic_reply';
            $topicreply_array['object'] = $topic;
            $topicreply_array['description'] = $values['body'];
            Engine_Api::_()->facebooksefeed()->sendFacebookFeed($topicreply_array);
          }
        }
      }
      //NOTIFICATIONS
      $notifyUserIds = $topicWatchesTable->getNotifyUserIds($values);

      foreach (Engine_Api::_()->getItemTable('user')->find($notifyUserIds) as $notifyUser) {
        //DON'T NOTIFY SELF
        if ($notifyUser->isSelf($viewer)) {
          continue;
        }

        if ($notifyUser->isSelf($topicOwner)) {
          $type = 'sitepage_discussion_response';
        } else {
          $type = 'sitepage_discussion_reply';
        }

        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($notifyUser, $viewer, $topic, $type, array(
            'message' => $this->view->BBCode($post->body),
        ));
      }

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECT TO THE POST PAGE
    $this->_redirectCustom($post);
  }

  //ACTION FOR STICKY THE TOPIC 
  public function stickyAction() {

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitepage_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject('sitepage_topic');

    //START MANAGE-ADMIN CHECK
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $topic->page_id);
    $can_edit = $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

    //CHECKING WHETHER THE USER HAVE THE PERMISSION OR NOT.
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET DB
    $db = $topic->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE STICKY
      $topic->sticky = ( null === $this->_getParam('sticky') ? !$topic->sticky : (bool) $this->_getParam('sticky') );
      $topic->save();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    //REDIRECT TO THE TOPIC VIEW PAGE
    $this->_redirectCustom($topic);
  }

  //ACTION FOR CLOSE THE TOPIC 
  public function closeAction() {

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitepage_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $topic->page_id);

    //START MANAGE-ADMIN CHECK
    $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET DB
    $db = $topic->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE TOPIC CLOSED
      $topic->closed = ( null === $this->_getParam('closed') ? !$topic->closed : (bool) $this->_getParam('closed') );
      $topic->save();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    //REDIRECT TO THE TOPIC VIEW PAGE
    $this->_redirectCustom($topic);
  }

  //ACTION FOR RENAME THE TOPIC 
  public function renameAction() {

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitepage_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $topic->page_id);

    //START MANAGE-ADMIN CHECK
    $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //MAKE FORM
    $this->view->form = $form = new Sitepage_Form_Topic_Rename();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $form->title->setValue(htmlspecialchars_decode($topic->title));
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET DB
    $db = $topic->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE TOPIC TITLE
      $topic->title = htmlspecialchars($form->getValue('title'));
      ;
      $topic->save();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECTING
    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected topic has been successfully renamed.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
  }

  //ACTION FOR DELETE THE TOPIC 
  public function deleteAction() {

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitepage_topic')->isValid())
      return;

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //START MANAGE-ADMIN CHECK
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $topic->page_id);
    $can_edit = $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //MAKE FORM
    $this->view->form = $form = new Sitepage_Form_Topic_Delete();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET DB
    $db = $topic->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //GET PAGE ID
      $page_id = $topic->page_id;

      //DELETE TOPIC
      $topic->delete();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECTING
    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected topic has been deleted.')),
                'layout' => 'default-simple',
                'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $this->view->tab_selected_id), 'sitepage_entry_view'),
            ));
  }

  //ACTION FOR WATCH THE TOPIC 
  public function watchAction() {

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $topic->page_id);

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET WATCH PARAM
    $watch = $this->_getParam('watch', true);

    //GET TOPIC WATCH TABLE
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitepage');

    //GET DB
    $db = $topicWatchesTable->getAdapter();
    $db->beginTransaction();
    try {
      $isWatching = $topicWatchesTable->isWatching($sitepage->getIdentity(), $topic->getIdentity(), $viewer->getIdentity());
      if (false === $isWatching) {
        $topicWatchesTable->insert(array(
            'resource_id' => $sitepage->getIdentity(),
            'topic_id' => $topic->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'watch' => (bool) $watch,
            'page_id' => $topic->page_id,
        ));
      } else {
        $topicWatchesTable->update(array(
            'watch' => (bool) $watch,
            'page_id' => $topic->page_id,
                ), array(
            'resource_id = ?' => $sitepage->getIdentity(),
            'topic_id = ?' => $topic->getIdentity(),
            'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirectCustom($topic);
  }

}

?>