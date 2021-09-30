<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_LikeController extends Seaocore_Controller_Action_Standard {

	//ACTION FOR LIKES
  public function globalLikesAction() {

		//GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GETTING THE VALUE OF RESOURCE ID AND RESOURCE TYPE
		$resource_id = $this->_getParam('resource_id');
		$resource_type = $this->_getParam('resource_type');
		
    if (empty($viewer_id)) {
      return;
    }

    //GET THE VALUE OF LIKE ID
		$like_id = $this->_getParam('like_id');
		$status = $this->_getParam('smoothbox', 1);
		$this->view->status = true;

    //GET LIKES.
    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
    $resource = Engine_Api::_()->getItem($resource_type, $resource_id);

    $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;

    //CHECK FOR LIKE ID
    if (empty($like_id)) {
      //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
      $like_id_temp = Engine_Api::_()->sitepage()->checkAvailability($resource_type, $resource_id);
      if (empty($like_id_temp[0]['like_id'])) {

        if (!empty($resource)) {

					//START PAGE MEMBER PLUGIN WORK.
					if ($resource_type == 'sitepage_page' && !empty(Engine_Api::_()->seaocore()->checkVersion($sitepageVersion, '4.2.9p3'))) {
						if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagemember')) {
							Engine_Api::_()->sitepagemember()->joinLeave($resource, 'Join');
						}
					Engine_Api::_()->sitepage()->itemCommentLike($resource, 'sitepage_contentlike');
					}
					//END PAGE MEMBER PLUGIN WORK.
					
          $like_id = $likeTable->addLike($resource, $viewer);
           if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
          Engine_Api::_()->sitelike()->setLikeFeed($viewer, $resource);
        }

        $notify_table = Engine_Api::_()->getDbtable('notifications', 'activity');
        $db = $likeTable->getAdapter();
        $db->beginTransaction();
        try {

          //CREATE THE NEW ROW IN TABLE
          if (!empty($getOwnerId) && $getOwnerId != $viewer_id) {

            $notifyData = $notify_table->createRow();
            $notifyData->user_id = $getOwnerId;
            $notifyData->subject_type = $viewer->getType();
            $notifyData->subject_id = $viewer_id;
            $notifyData->object_type = $object_type;
            $notifyData->object_id = $resource_id;
            $notifyData->type = 'liked';
            $notifyData->params = $resource->getShortType();
            $notifyData->date = date('Y-m-d h:i:s', time());
            $notifyData->save();
          }

          //PASS THE LIKE ID.
          $this->view->like_id = $like_id;
          $this->view->error_mess = 0;
          $db->commit();
        } catch (Exception $e) {

						$db->rollBack();
						throw $e;
        }
        $like_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Liked.');
      } else {
					$this->view->like_id = $like_id_temp[0]['like_id'];
					$this->view->error_mess = 1;
      }
    }
		else {
			if (!empty($resource)) {
			  
			  //START PAGE MEMBER PLUGIN WORK
				if ($resource_type == 'sitepage_page' && $sitepageVersion >= '4.2.9p3') {
										if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagemember')) {
				  Engine_Api::_()->sitepagemember()->joinLeave($resource, 'Leave');
				  }
				}
				//END PAGE MEMBER PLUGIN WORK				
				
				$likeTable->removeLike($resource, $viewer);
				  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
          Engine_Api::_()->sitelike()->removeLikeFeed($viewer, $resource);
			}
			$this->view->error_mess = 0;

			$like_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Unliked.');
    }
    if (empty($status)) {
      $this->_forwardCustom('success', 'utility', 'core', array(
              'smoothboxClose' => true,
              'parentRefresh' => true,
              'messages' => array($like_msg)
          )
      );
    }
  }

  //ACTION FOR NUMBER OF PAGES LIKES.
  public function likePagesAction() {

    //GETTING THE RESOURCE TYPE AND RESOURCE ID.
    $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
    $this->view->resource_id = $resource_id = $this->_getParam('resource_id');

		//CURRENT PAGE NUMBER FOR THE 'AJAX PAGINATION '.
    $page = $this->_getParam('page', 1);
    $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', 0);

		//SEARCHING VALUE WHICH SET BY AJAX SERCHING IN .TPL.
		$search = $this->_getParam('search', ''); 
		$this->view->call_status = $call_status = $this->_getParam('call_status');

    //GETTING THE USER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!empty($search)) {
      $this->view->search = $search;
    }

    //HERE FUNCTION CALL FROM THE CORE.PHP FILE OR THIS IS SHOW NUMBER OF FRIEND AND PEOPLE
    $fetch_sub = Engine_Api::_()->sitepage()->friendPublicLike($call_status, $resource_type, $resource_id, $user_id, $search);
    $check_object_result = $fetch_sub->getTotalItemCount();

		$this->view->user_obj = array();
    if (!empty($check_object_result)) {
      $this->view->user_obj = $fetch_sub;
    } else {
      $this->view->no_result_msg = $this->view->translate('No results were found.');
    }

    $fetch_sub->setCurrentPageNumber($page);
    $fetch_sub->setItemCountPerPage(50);

    //NUMBER OF PEOPLE WHICH LIKED THIS CONTENT.
    $this->view->public_count = Engine_Api::_()->sitepage()->numberOfLike('sitepage_page', $resource_id);

    //NUMBER OF MY FRIEND WHICH LIKED THIS CONTENT.
    $this->view->friend_count = Engine_Api::_()->sitepage()->friendNumberOfLike($resource_type, $resource_id);

//     //FIND OUT THE TITLE OF LIKE.
//     if ($resource_type == 'member') {
//       $this->view->like_title = Engine_Api::_()->getItem('user', $resource_id)->displayname;
//     } else {
//       $this->view->like_title = Engine_Api::_()->getItem($resource_type, $resource_id)->title;
//     }
      }


	//FUNCTION FOR MY LIKES.
  public function mylikesAction() {

		//USER VALDIATION
		if (!$this->_helper->requireUser()->isValid())
		return;

		$viewer = Engine_Api::_()->user()->getViewer();

		//PAGE OFFER IS INSTALLED OR NOT
		$this->view->sitepageOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');

		//PAGE-RATING IS ENABLED OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
		$this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);
		//GET NAVIGATION
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main', array(), 'sitepage_main');
    $defaultOrder = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.ajax.layouts.oder', 1);
    $ShowViewArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.ajax.widgets.layout', array("0" => "1", "1" => "2", "2" => "3"));

    $this->view->list_view = 0;
    $this->view->grid_view = 0;
    $this->view->map_view = 0;
    $this->view->defaultView = -1;
    if (in_array("1", $ShowViewArray)) {
      $this->view->list_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 1)
        $this->view->defaultView = 0;
    }
    if (in_array("2", $ShowViewArray)) {
      $this->view->grid_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 2)
        $this->view->defaultView = 1;
    }
    if (in_array("3", $ShowViewArray)) {
      $this->view->map_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 3)
        $this->view->defaultView = 2;
    }

    if ($this->view->defaultView == -1) {
      return $this->setNoRender();
    }
    $customFieldValues = array();
    $values = array();

		//GETTING THE CURRENT USER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $coreLikeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $coreLikeTableName = $coreLikeTable->info( 'name' ) ;
    $moduleTable = Engine_Api::_()->getItemTable( 'sitepage_page' ) ;
    $moduleTableName = $moduleTable->info( 'name' ) ;
		$stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1);

    $like_select = $moduleTable->select()
            ->setIntegrityCheck( false )
						->from( $coreLikeTableName, null)
						->join($moduleTableName, "$coreLikeTableName.resource_id = $moduleTableName.page_id")
						->where( $coreLikeTableName . '.resource_type = ?' , 'sitepage_page' )
						->where($moduleTableName . '.approved = ?', '1')
						->where($moduleTableName . '.declined = ?', '0')
						->where($moduleTableName . '.draft = ?', '1')
						->where($moduleTableName . ".search = ?", 1)
						->where( $coreLikeTableName . '.poster_id = ?' , $user_id );
		if ($stusShow == 0) {
			$like_select = $like_select->where($moduleTableName . '.closed = ?', '0');
		}    
    
    $paginator = Zend_Paginator::factory( $like_select ) ;
    $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));

    //$items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.page', 10);
    $paginator->setItemCountPerPage(10);

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitepage()->enableLocation();
    $this->view->flageSponsored = 0;

    if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {
      $ids = array();
      $sponsored = array();
      foreach ($paginator as $sitepage) {
        $id = $sitepage->getIdentity();
        $ids[] = $id;
        $sitepage_temp[$id] = $sitepage;
      }
      $values['page_ids'] = $ids;
      $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($values);

      foreach ($locations as $location) {
        if ($sitepage_temp[$location->page_id]->sponsored) {
          $this->view->flageSponsored = 1;
          break;
        }
      }
      $this->view->sitepage = $sitepage_temp;
    } else {
      $this->view->enableLocation = 0;
    }

    if(Engine_Api::_()->seaocore()->isSitemobileApp()) {  
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $this->view->page = $this->_getParam('page', 1);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalCount) /10);
    
    if (!$isappajax) {
      $this->_helper->content/*->setNoRender()*/->setEnabled();
    }
  }

	//FUNCTION FOR MY JOINED PAGES.
  public function myJoinedAction() {

		//USER VALDIATION
		if (!$this->_helper->requireUser()->isValid())
			return;
		
		//GET NAVIGATION
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main', array(), 'sitepage_main');
		
		//GET VIEWER
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($user_id, 'pageJoin', 0);

    $this->view->paginator->setCurrentPageNumber(1);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    //PAGE-RATING IS ENABLE OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
    
    if(Engine_Api::_()->seaocore()->isSitemobileApp()) {  
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $this->view->page = $this->_getParam('page', 1);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($this->view->totalCount) /10);

    if (!$isappajax) {
      $this->_helper->content/*->setNoRender()*/->setEnabled();
    }
  }

	//ACTION FOR SENDING UPDATES TO THE USERS WHO LIKED THIS PAGE
  public function sendUpdateAction() {

    $multi = 'member';
    $multi_ids = '';
    $page_id = $resource_id = $this->_getParam("page_id");
    $resource_type = 'sitepage_page'; 
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sendupdate');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $this->view->self_liked = 0;
    if (empty($sitepage->like_count)) {
      return;
    } elseif ($sitepage->like_count == 1) {
      $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
      $likeTableName = $likeTable->info('name');
      $select = $likeTable->select()
              ->from($likeTableName, 'like_id')
              ->where('resource_type = ?', 'sitepage_page')
              ->where('poster_id = ?', $viewer->getIdentity())
              ->where('resource_id = ?', $page_id)
              ->limit(1);
      $likeData = $likeTable->fetchRow($select);
      if (!empty($likeData)) {
        $this->view->self_liked = 1;
        return;
      }
    }

    $this->view->form = $form = new Messages_Form_Compose();
    $form->removeElement('to');
    $form->setDescription('Create the update to be sent to people who like your Page.');

    if ($sitepage->like_count == 1) {
      $tip_msg = $this->view->translate("<div class='tip'><span>1 person likes this Page.</span></div>");
    } else {
      $tip_msg = "<div class='tip'><span>". $this->view->translate('%s people like this Page.', $sitepage->like_count). "</span></div>";
    }

    $form->addElement('dummy', 'tip_msg', array(
            'label' => $tip_msg,
            'ignore' => true,
    ));
    $form->getElement('tip_msg')->getDecorator('label')->setOptions(array('placement', 'APPEND', 'escape' => false));

    $friends = Engine_Api::_()->user()->getViewer()->membership()->getMembers();

    $data = array();
    foreach ($friends as $friend) {
      $friend_photo = $this->view->itemPhoto($friend, 'thumb.icon');
      $data[] = array('label' => $friend->getTitle(), 'id' => $friend->getIdentity(), 'photo' => $friend_photo);
    }
    $data = Zend_Json::encode($data);
    $this->view->friends = $data;

		//LOGING FOR HANDLING MULTIPLE RECIPIENTS
    if (!empty($multi)) {
      $user_id = $viewer->getIdentity();
      $sub_status_table = Engine_Api::_()->getItemTable('core_like');
      $sub_status_name = $sub_status_table->info('name');
      $user_table = Engine_Api::_()->getItemTable('user');
      $user_Name = $user_table->info('name');

      $sub_status_select = $user_table->select()
              ->setIntegrityCheck(false)
              ->from($sub_status_name, array('poster_id'))
              ->joinInner($user_Name, "$user_Name . user_id = $sub_status_name . poster_id", null)
              ->where($sub_status_name . '.resource_type = ?', $resource_type)
              ->where($sub_status_name . '.resource_id = ?', $resource_id)
              ->where($sub_status_name . '.poster_id != ?', $user_id);
      $members_ids = $sub_status_select->query()->fetchAll();
      foreach ($members_ids as $member_id) {
        $multi_ids .= ',' . $member_id['poster_id'];
      }
      $multi_ids = ltrim($multi_ids, ",");
      if ($multi_ids) {
        $this->view->multi = $multi;
        $this->view->multi_name = $viewer->getTitle();
        $this->view->multi_ids = $multi_ids;
        $form->toValues->setValue($multi_ids);
      }
    }

    //ASSIGN THE COMPOSING STUFF
    $composePartials = array();
    foreach (Zend_Registry::get('Engine_Manifest') as $data) {
      if (empty($data['composer']))
        continue;
      foreach ($data['composer'] as $type => $config) {
        $composePartials[] = $config['script'];
      }
    }
    $this->view->composePartials = $composePartials;


    //FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //PROCESS
    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      //TRY ATTACHEMENT GETTING STUFF
      $attachment = null;
      $attachmentData = $this->getRequest()->getParam('attachment');
      if (!empty($attachmentData) && !empty($attachmentData['type'])) {
        $type = $attachmentData['type'];
        $config = null;
        foreach (Zend_Registry::get('Engine_Manifest') as $data) {
          if (!empty($data['composer'][$type])) {
            $config = $data['composer'][$type];
          }
        }
        if ($config) {
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach' . ucfirst($type);
          $attachment = $plugin->$method($attachmentData);
          $parent = $attachment->getParent();
          if ($parent->getType() === 'user') {
            $attachment->search = 0;
            $attachment->save();
          } else {
            $parent->search = 0;
            $parent->save();
          }
        }
      }

      $viewer = Engine_Api::_()->user()->getViewer();
      $values = $this->getRequest()->getPost();
      $recipients = preg_split('/[,. ]+/', $values['toValues']);

			//LIMIT RECIPIENTS IF IT IS NOT A SPECIAL LIST OF MEMBERS
      if (empty($multi)) {
        $recipients = array_slice($recipients, 0, 1000000);
			}

			//CLEAN THE RECIPIENTS FOR REPEATING IDS
      $recipients = array_unique($recipients);

      $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);

      $page_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepage->page_id)), 'sitepage_entry_view') . ">$sitepage->title</a>";

      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
              $viewer, $recipients, $values['title'], $this->view->translate("This is an update for the page:" . $page_title_with_link) . "<br />" . $values['body'], $attachment
      );
      foreach ($recipientsUsers as $user) {
        if ($user->getIdentity() == $viewer->getIdentity()) {
          continue;
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
            $user, $viewer, $conversation, 'message_new'
        );
      }

      //INCREMENT MESSAGE COUNTER
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      $db->commit();

      $this->_forwardCustom('success', 'utility', 'core', array(
              'smoothboxClose' => 500,
              'parentRefresh' => false,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('Update was sent successfully to people who like your Page.'))
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }
}