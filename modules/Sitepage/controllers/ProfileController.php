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
class Sitepage_ProfileController extends Seaocore_Controller_Action_Standard {

  //ACTION FOR SENDING A MESSGE TO PAGE OWNER
  public function messageOwnerAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET PAGE ID AND PAGE OBJECT
    $page_id = $this->_getParam("page_id");
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //PAGE OWNER CAN'T SEND MESSAGE TO HIMSELF
    if ($viewer_id == $sitepage->owner_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //FORM GENERATION
    $this->view->form = $form = new Messages_Form_Compose();
    $form->setTitle('Contact Page Owner');
    $form->setDescription('Create your message with the form given below. Your message will be sent to the admins of this Page.');
    $form->removeElement('to');

    //GET ADMINS ID FOR SENDING MESSAGE
    $manageAdminData = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($page_id);
    $manageAdminData = $manageAdminData->toArray();
    $ids = '';
    if (!empty($manageAdminData)) {
      foreach ($manageAdminData as $key => $user_ids) {
        $user_id = $user_ids['user_id'];
        if ($viewer_id != $user_id) {
          $ids = $ids . $user_id . ',';
        }
      }
    }
    $ids = trim($ids, ',');
    $form->toValues->setValue($ids);

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      $values = $this->getRequest()->getPost();

      $form->populate($values);

      $is_error = 0;
      if (empty($values['title'])) {
        $is_error = 1;
      }

      //SENDING MESSAGE
      if ($is_error == 1) {
        $error = $this->view->translate('Subject is required field !');
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      $recipients = preg_split('/[,. ]+/', $values['toValues']);

      //LIMIT RECIPIENTS IF IT IS NOT A SPECIAL LIST OF MEMBERS
      $recipients = array_slice($recipients, 0, 1000);

      //CLEAN THE RECIPIENTS FOR REPEATING IDS
      //THIS CAN HAPPEN IF RECIPIENTS IS SELECTED AND THEN A FRIEND LIST IS SELECTED
      $recipients = array_unique($recipients);

      $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);

      $sitepage_title = $sitepage->title;
      $page_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id)), 'sitepage_entry_view') . ">$sitepage_title</a>";

      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
              $viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->view->translate("This message corresponds to the Page:") . $page_title_with_link
      );

      foreach ($recipientsUsers as $user) {
        if ($user->getIdentity() == $viewer->getIdentity()) {
          continue;
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                $user, $viewer, $conversation, 'message_new'
        );
      }

      //INCREMENT MESSAGES COUNTER
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      $db->commit();

      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => false,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
              ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR TELL TO THE FRIEND FOR THIS PAGE
  public function tellAFriendAction() {

    //DEFAULT LAYOUT
    $sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
    if ($sitemobile && !Engine_Api::_()->sitemobile()->checkMode('mobile-mode'))
			$this->_helper->layout->setLayout('default-simple');

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewr_id = $viewer->getIdentity();

    //GET PAGE ID AND PAGE OBJECT
    $page_id = $this->_getParam('page_id', $this->_getParam('id', null));
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if (empty($sitepage))
      return $this->_forwardCustom('notfound', 'error', 'core');
    //AUTHORIZATION CHECK FOR TELL A FRIEND
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'tfriend');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //FORM GENERATION
    $this->view->form = $form = new Sitepage_Form_TellAFriend();
 
    if (!empty($viewr_id)) {
      $value['sender_email'] = $viewer->email;
      $value['sender_name'] = $viewer->displayname;
      $form->populate($value);
    }
    
    //IF THE MODE IS APP MODE THEN
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
      Zend_Registry::set('setFixedCreationFormBack', 'Back');
      Zend_Registry::set('setFixedCreationHeaderTitle', Zend_Registry::get('Zend_Translate')->_('Tell a friend'));
      Zend_Registry::set('setFixedCreationHeaderSubmit', Zend_Registry::get('Zend_Translate')->_('Send'));
      $this->view->form->setAttrib('id', 'tellAFriendFrom');
      Zend_Registry::set('setFixedCreationFormId', '#tellAFriendFrom');
      $this->view->form->removeElement('sitepage_send');
      $this->view->form->removeElement('sitepage_cancel');
      $form->setTitle('');
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();

      //EDPLODES EMAIL IDS
      $reciver_ids = explode(',', $values['sitepage_reciver_emails']);

      if (!empty($values['sitepage_send_me'])) {
        $reciver_ids[] = $values['sitepage_sender_email'];
      }
      $sender_email = $values['sitepage_sender_email'];

      //CHECK VALID EMAIL ID FORMITE
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);

      if (!$validator->isValid($sender_email)) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
        return;
      }
      foreach ($reciver_ids as $reciver_id) {
        $reciver_id = trim($reciver_id, ' ');
        if (!$validator->isValid($reciver_id)) {
          $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
          return;
        }
      }
      $sender = $values['sitepage_sender_name'];
      $message = $values['sitepage_message'];
      $heading = ucfirst($sitepage->getTitle());
      $host = $_SERVER['HTTP_HOST'];
      if($host == 'stage.impactx.co'){
        $host = $host . '/network/';
      }else{
        $host = $host . '/net/';
      }
      foreach ($reciver_ids as $reciver_id) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_id, 'SITEPAGE_TELLAFRIEND_EMAIL', array(
            'host' => $host,
            'sender_name' => $sender,
            'page_title' => $heading,
            'message' => '<div>' . $message . '</div>',
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()),
            'sender_email' => $sender_email,
            'queue' => false
        ));
      }
      if ($sitemobile && Engine_Api::_()->sitemobile()->checkMode('mobile-mode'))
				$this->_forwardCustom('success', 'utility', 'core', array(          
          'parentRedirect' => $sitepage->getHref(),          
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.'))
      ));
		  else	
      $this->_forwardCustom('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => false,
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.'))
      ));
    }
  }

  //ACTION FOR PRINTING THE PAGE
  public function printAction() {
    $this->_helper->layout->setLayout('default-simple');
    //GET PAGE ID AND PAGE OBJECT
    $this->view->page_id = $page_id = $this->_getParam('page_id', $this->_getParam('id', null));
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if (empty($sitepage))
      return $this->_forwardCustom('notfound', 'error', 'core');
    //AUTHORIZATION CHECK FOR PRINTING
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'print');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    if ($sitepage->category_id != 0)
      $this->view->category = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($sitepage->category_id);

    if ($sitepage->subcategory_id != 0)
      $this->view->subcategory = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($sitepage->subcategory_id);

    if ($sitepage->subsubcategory_id != 0)
      $this->view->subsubcategory = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($sitepage->subsubcategory_id);
    
    
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitepage/View/Helper', 'Sitepage_View_Helper');
     $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitepage);
  }

  //ACTION FOR WRITE BOX AT VIEW PROFILE PAGE
  public function displayAction() {

    //GET THE TEXT STRING
    $text = $this->_getParam('text_string');

    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    $writesTable = Engine_Api::_()->getDbtable('writes', 'sitepage')->setWriteContent($page_id, $text);
    exit();
  }

  public function contactDetailAction() {

    //GET PAGE ID
    $page_id = $this->_getParam("page_id");

    //GET SITEPAGE ITEM    
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //GET PHONE
    $phone = $this->_getParam('phone');

    //GET EMAIL
    $email = $this->_getParam('email');

    //GET WEBSITE
    $website = $this->_getParam('website');


    $phone = $this->view->string()->stripTags($phone);
    $email = $this->view->string()->stripTags($email);
    $website = $this->view->string()->stripTags($website);
   

    //SAVE DETAILS
    $sitepage->phone = $phone;
    $sitepage->email = $email;
    $sitepage->website = $website;
    $sitepage->save();
  }

  public function getCoverPhotoAction() {

    //GET PAGE ID
    $page_id = $this->_getParam("page_id");
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    //START MANAGE-ADMIN CHECK
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    $onlyMemberWithPhoto = $this->_getParam("onlyMemberWithPhoto", 1);
    if (empty($sitepage->page_cover)) {
      $this->view->show_member = $show_member = $this->_getParam("show_member", 0);
      if ($show_member) {
        $this->view->members = $members = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($sitepage->page_id, null,null,$onlyMemberWithPhoto);
        $this->view->membersCount = $members->getTotalItemCount();
        $this->view->membersCountView = $this->_getParam("memberCount", 8);
      }
      return;
    }
    $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitepage');
    $album = $tableAlbum->getSpecialAlbum($sitepage, 'cover');
    //$otherinfo = Engine_Api::_()->getDbtable('otherinfo', 'sitepage')->getOtherinfo($page_id);
    $this->view->photo = $photo = Engine_Api::_()->getItem('sitepage_photo', $sitepage->page_cover);

    $this->view->coverTop = 0;
    $this->view->coverLeft = 0;
    if ($album->cover_params && isset($album->cover_params['top'])) {
      $this->view->coverTop = $album->cover_params['top'];
    }
  }
  
  //ACTION FOR Email Me FOR THIS PAGE
  public function emailMeAction() {

    //DEFAULT LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewr_id = $viewer->getIdentity();

    //GET PAGE ID AND PAGE OBJECT
    $this->view->page_id  = $page_id = $this->_getParam('page_id', $this->_getParam('id', null));
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if (empty($sitepage))
      return $this->_forwardCustom('notfound', 'error', 'core');
      
    //AUTHORIZATION CHECK FOR TELL A FRIEND
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'tfriend');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //FORM GENERATION
    $this->view->form = $form = new Sitepage_Form_EmailMe();

    if (!empty($viewr_id)) {
      $value['sender_email'] = $viewer->email;
      $value['sender_name'] = $viewer->displayname;
      $form->populate($value);
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();
      $reciver_ids = array('0'=> $sitepage->email); 
     
      if (!empty($values['sitepage_send_me'])) {
        $reciver_ids[] = $values['sitepage_sender_email'];
      }
      $sender_email = $values['sitepage_sender_email'];

      //CHECK VALID EMAIL ID FORMITE
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);

      if (!$validator->isValid($sender_email)) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
        return;
      }
       foreach ($reciver_ids as $reciver_id) {
         if (!$validator->isValid($reciver_id)) {
           $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
           return;
         }
       }
      $sender = $values['sitepage_sender_name'];
      $message = $values['sitepage_message'];
      $heading = ucfirst($sitepage->getTitle());
        $host = $_SERVER['HTTP_HOST'];
        if($host == 'stage.impactx.co'){
            $host = $host . '/network/';
        }else{
            $host = $host . '/net/';
        }
      foreach ($reciver_ids as $reciver_id) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITEPAGE_EMAILME_EMAIL', array(
            'host' => $host,
            'sender_name' => $sender,
            'page_title' => $heading,
            'message' => '<div>' . $message . '</div>',
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()),
            'sender_email' => $sender_email,
            'queue' => true
        ));
      }
      $this->_forwardCustom('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => false,
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to page owner has been sent successfully.'))
      ));
    }
  }

  public function uploadCoverPhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //LAYOUT
    $this->_helper->layout->setLayout('default-simple');
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    //PAGE ID
    $page_id = $this->_getParam('page_id');

    $special = $this->_getParam('special', 'cover');
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //CHECK FORM VALIDATION
    $file='';
    $notNeedToCreate=false;
    $photo_id = $this->_getParam('photo_id');
    if ($photo_id) {
      $photo = Engine_Api::_()->getItem('sitepage_photo', $photo_id);
      $album = Engine_Api::_()->getItem('sitepage_album', $photo->album_id);
      if ($album && $album->type == 'cover') {
        $notNeedToCreate = true;
      }
      if ($photo->file_id && !$notNeedToCreate)
        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo->file_id);
    }


		//PROCESS
		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();
		try {
			//CREATE PHOTO
			$tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitepage');
			if (!$notNeedToCreate) {
				$photo = $tablePhoto->createRow();
				$photo->setFromArray(array(
						'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
						'page_id' => $page_id
				));
				$photo->save();
				if ($file) {
					$photo->setPhoto($file);
				} else {
					$photo->setPhoto($_FILES['Filedata'],true);
				}


				$tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitepage');
				$album = $tableAlbum->getSpecialAlbum($sitepage, $special);

				$tablePhotoName = $tablePhoto->info('name');
				$photoSelect = $tablePhoto->select()->from($tablePhotoName, 'order')->where('album_id = ?', $album->album_id)->order('order DESC')->limit(1);
				$photo_rowinfo = $tablePhoto->fetchRow($photoSelect);
				$photo->collection_id = $album->album_id;
				$photo->album_id = $album->album_id;
				$order = 0;
				if (!empty($photo_rowinfo)) {
					$order = $photo_rowinfo->order + 1;
				}
				$photo->order = $order;
				$photo->save();
			}

			$album->cover_params = $this->_getParam('position', array('top' => '0', 'left' => 0));
			$album->save();
			if (!$album->photo_id) {
				$album->photo_id = $photo->file_id;
				$album->save();
			}
			$sitepage->page_cover = $photo->photo_id;
			$sitepage->save();
			//ADD ACTIVITY
			$viewer = Engine_Api::_()->user()->getViewer();
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$activityFeedType = null;
			if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
				$activityFeedType = 'sitepage_admin_cover_update';
			elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage))
				$activityFeedType = 'sitepage_cover_update';


			if ($activityFeedType) {
				$action = $activityApi->addActivity($viewer, $sitepage, $activityFeedType);
			}
			if ($action) {
				Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
				if ($photo)
					Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
			}

			$this->view->status = true;
			$db->commit();
      return $this->_redirectCustom($sitepage->getHref());
		} catch (Exception $e) {
			$db->rollBack();
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
			return;
		}
  }

  public function removeCoverPhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    $page_id = $this->_getParam('page_id');
    if ($this->getRequest()->isPost()) {
      $special = $this->_getParam('special', 'cover');
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      $sitepage->page_cover = 0;
      $sitepage->save();
      $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitepage');
      $album = $tableAlbum->getSpecialAlbum($sitepage, $special);
      $album->cover_params = array('top' => '0', 'left' => 0);
      $album->save();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
  }

  public function getAlbumsPhotosAction() {
    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;
    $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
    if (!$sitepagealbumEnabled) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //GET PAGE ID
    $page_id = $this->_getParam("page_id");
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    ////START MANAGE-ADMIN CHECK
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($can_edit))
      return;
    //FETCH ALBUMS
    $this->view->recentAdded = $recentAdded = $this->_getParam("recent", false);
    $this->view->album_id = $album_id = $this->_getParam("album_id");
    if ($album_id) {
      $this->view->album = $album = Engine_Api::_()->getItem('sitepage_album', $album_id);
      $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
      $paginator->setItemCountPerPage(10000);
    } elseif ($recentAdded) {
      $paginator = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotos(array('page_id' => $page_id, 'orderby' => 'photo_id DESC', 'start' => 0, 'end' => 100));
    } else {
      $paramsAlbum['page_id'] = $page_id;
      $paginator = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbums($paramsAlbum);
    }
    $this->view->paginator = $paginator;
  }
  //show services
  public function showServicesAction() {
      //GET PAGE ID
      $this->view->page_id = $page_id = $this->_getParam('page_id');

      //GET SITEPAGE ITEM
      $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

      $servicePrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'service');
      if (empty($servicePrivacy)) {
        return $this->_forward('notfound', 'error', 'core');
      }

      $this->view->services=Engine_Api::_()->getDbTable('services', 'sitepage')->getListing(array('page_id'=>$page_id));

    }

    public function orgAction() {
        $res = "{'name':'val'}";
        return json_encode($res);
    }
}