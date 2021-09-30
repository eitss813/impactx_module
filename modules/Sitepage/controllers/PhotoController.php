<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_PhotoController extends Seaocore_Controller_Action_Standard {

  public function init() {
    //HERE WE CHECKING THE SITEPAGE ALBUM IS ENABLED OR NOT
    $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
    if (!$sitepagealbumEnabled) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //CHECK SUBJECT IS EXIST OR NOT IF NOT EXIST THEN SET ACCORDING TO THE PAGE ID AND PHOTO ID
    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
              null !== ($photo = Engine_Api::_()->getItem('sitepage_photo', $photo_id))) {
        Engine_Api::_()->core()->setSubject($photo);
      } else if (0 !== ($page_id = (int) $this->_getParam('page_id')) &&
              null !== ($sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id))) {
        Engine_Api::_()->core()->setSubject($sitepage);
      }
    }

    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //PACKAGE BASE PRIYACY START    
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (!empty($sitepage)) {
        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagealbum")) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
          }
        } else {
          $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
          if (empty($isPageOwnerAllow)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
          }
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if (Engine_Api::_()->core()->hasSubject() != null) {
        $photo = Engine_Api::_()->core()->getSubject();
        $album = $photo->getCollection();
        $page_id = $album->page_id;
      }
    }
  }

  //ACTION FOR UPLOADING THE ALBUM
  public function uploadAlbumAction() {

    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			//GET THE FILEDATA IF FILEDATA IS THERE THEN CALL THE UPLOAD PHOTO ACTION
			if (isset($_GET['ul']))
				return $this->_forwardCustom('upload-photo', null, null, array('format' => 'json'));

      if(isset($_FILES['Filedata']))
        $_POST['file'] = $this->uploadPhotoAction();
			//GET VIEWER ID 
			$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

			//GET PAGE ID 
			$page_id = $this->_getParam('page_id');

			//GET NAVIGATION
			$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

			//GET SITEPAGE ITEM
			$this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

			//START MANAGE-ADMIN CHECK
			$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
			if (empty($isManageAdmin)) {
				$this->view->can_edit = $can_edit = 0;
			} else {
				$this->view->can_edit = $can_edit = 1;
			}

			$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
			if (empty($isManageAdmin) && empty($can_edit)) {
				return $this->_forwardCustom('requireauth', 'error', 'core');
			}
			//END MANAGE-ADMIN CHECK
			//SEND TAB ID TO THE TPL 
			$this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

			//GET FORM
			$this->view->form = $form = new Sitepage_Form_Photo_Album();

			//SEND ALBUM ID TO THE TPL
			$this->view->album_id = $this->_getParam('album_id');

			if (!$can_edit) {
				$form->removeElement('album');
				$form->removeElement('auth_tag');
				$form->removeElement('title');
				$form->removeElement('search');
				if (isset($_POST['default_album_id']))
					$this->view->album_id = $album_id = $_POST['default_album_id'];
			}

			//SET PAGE ID INTO THE FORM
			$form->page_id->setValue($page_id);

			//CHECK FORM VALIDATION
			if (!$this->getRequest()->isPost()) {
				if (null !== ($album_id = $this->_getParam('album_id'))) {
					$form->populate(array(
							'album' => $album_id
					));
				}
				return;
			}

			//CHECK FORM VALIDATION
			if (!$form->isValid($this->getRequest()->getPost())) {
				return;
			}

			//GET DB
			$db = Engine_Api::_()->getItemTable('sitepage_album')->getAdapter();
			$db->beginTransaction($db);
			try {
				//SAVE VALUES
				$values = $album = $form->saveValues();

				//UPDATE VALUES
				Engine_Api::_()->getDbtable('photos', 'sitepage')->update(array('album_id' => $album->getIdentity(), 'page_id' => $page_id, 'collection_id' => $album->getIdentity(),), array('page_id =?' => $page_id, 'album_id =?' => 0, 'collection_id =?' => 0, 'user_id =?' => $viewer_id));

				//COMMIT
				$db->commit();
			} catch (Exception $e) {
				$db->rollBack();
				throw $e;
			}

			//REDIRECTING
			if ($viewer_id == $sitepage->owner_id) {
				$this->_helper->redirector->gotoRoute(array('action' => 'edit-photos', 'page_id' => $page_id, 'album_id' => $album->getIdentity(), 'slug' => $album->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitepage_albumphoto_general', true);
			} else {
				$this->_helper->redirector->gotoRoute(array('action' => 'view', 'page_id' => $page_id, 'album_id' => $album->getIdentity(), 'slug' => $album->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitepage_albumphoto_general', true);
			}

    } 
    else {
			//CHECK THE VERSION OF THE CORE MODULE
			$coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
			$coreversion = $coremodule->version;
			Engine_API::_()->sitemobile()->setContentStorage();
			if (empty(Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.0'))) {
				$this->_helper->content->render();
			} else {
				$this->_helper->content
							//->setNoRender()
							->setEnabled()
				;
			}
			
			//GET VIEWER ID 
			$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

			//GET PAGE ID 
			$page_id = $this->_getParam('page_id');
			
			$this->view->album_id = $this->_getParam('album_id');
			$this->view->tab_selected_id = $this->_getParam('tab');
			
			//GET NAVIGATION
			$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

			//GET SITEPAGE ITEM
			$this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

			//START MANAGE-ADMIN CHECK

			$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
			if (empty($isManageAdmin)) {
				$this->view->can_edit = $can_edit = 0;
			} else {
				$this->view->can_edit = $can_edit = 1;
			}

			$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
			if (empty($isManageAdmin) && empty($can_edit)) {
				return $this->_forwardCustom('requireauth', 'error', 'core');
			}
			//END MANAGE-ADMIN CHECK
			//
			//GET FORM
			$this->view->form = $form = new Sitepage_Form_SitemobilePhoto_Album();

			if (!$can_edit) {
				$form->removeElement('album');
				$form->removeElement('auth_tag');
				$form->removeElement('title');
				$form->removeElement('search');
				if (isset($_POST['default_album_id']))
					$this->view->album_id = $album_id = $_POST['default_album_id'];
			}

			//SET PAGE ID INTO THE FORM
			$form->page_id->setValue($page_id);
      
      if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
      Zend_Registry::set('setFixedCreationHeaderTitle', str_replace(' New ', ' ', $form->getTitle()));
      Zend_Registry::set('setFixedCreationHeaderSubmit', 'Save');
      $this->view->form->setAttrib('id', 'form-upload');
      Zend_Registry::set('setFixedCreationFormId', '#form-upload');
      $this->view->form->removeElement('submit');
      $form->setTitle('');
    }

			//CHECK FORM VALIDATION
			if (!$this->getRequest()->isPost()) {
				$this->view->status = false;
				$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
				if (null !== ($album_id = $this->_getParam('album_id'))) {
					$form->populate(array(
							'album' => $album_id
					));
				}
				return;
			}

			//CHECK FORM VALIDATION
			if (!$form->isValid($this->getRequest()->getPost())) {
				return;
			}
			
	//upload photo action

			//GET SITEPAGE ITEM
			$sitepage = Engine_Api::_()->getItem('sitepage_page', (int) $this->_getParam('page_id'));

			$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
			if (empty($isManageAdmin)) {
				$can_edit = 0;
			} else {
				$can_edit = 1;
			}

			//START MANAGE-ADMIN CHECK
			if ($viewer_id != $sitepage->owner_id && empty($can_edit)) {
				$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
				if (empty($isManageAdmin)) {
					return $this->_forwardCustom('requireauth', 'error', 'core');
				}
			}
			//END MANAGE-ADMIN CHECK

			//CHECK MAX FILE SIZE
			if (!$this->_helper->requireUser()->checkRequire()) {
				$this->view->status = false;
				$this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
				return;
			}

			//GET FORM VALUES
			$values = $this->getRequest()->getPost();

			//GET DB
			$tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitepage');
			$db = $tablePhoto->getAdapter();
			$db->beginTransaction();

			//COUNT NO. OF PHOTOS (CHECK ATLEAST SINGLE PHOTO UPLOAD).
			$count = 0;
			foreach ($_FILES['Filedata']['name'] as $data) {
				if (!empty($data)) {
					$count = 1;
					break;
				}
			}
	//order of photos 
			$rows = $tablePhoto->fetchRow($tablePhoto->select()->from($tablePhoto->info('name'), 'order')->order('order DESC')->limit(1));
			$order = 0;
			if (!empty($rows)) {
				$order = $rows->order + 1;
			}
			try {
				if (!isset($_FILES['Filedata']) || !isset($_FILES['Filedata']['name']) || $count == 0) {
					$this->view->status = false;
					$form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
					return;
				}
        $values['file'] = array();
				foreach ($_FILES['Filedata']['name'] as $key => $uploadFile) {
					$params = array(
							'collection_id' => 0,
							'album_id' => 0,
							'page_id' => $sitepage->page_id,
							'user_id' => $viewer_id,
							'order' => $order,
					);

					$file = array('name' => $_FILES['Filedata']['name'][$key], 'tmp_name' => $_FILES['Filedata']['tmp_name'][$key], 'type' => $_FILES['Filedata']['type'][$key], 'size' => $_FILES['Filedata']['size'][$key], 'error' => $_FILES['Filedata']['error'][$key]);

					if (!is_uploaded_file($file['tmp_name'])) {
						continue;
					}
					$photoObj = $tablePhoto->createPhoto($params, $file);
					$photoObj ? $photo_id = $photoObj->photo_id : $photo_id = 0;
					$this->view->status = true;
					$this->view->name = $_FILES['Filedata']['name'][$key];
					$this->view->photo_id = $photo_id;
					$db->commit();
					$order++;
          $values['file'][] = $photoObj->photo_id;
				}
			} catch (Exception $e) {
				$db->rollBack();
				$this->view->status = false;
				$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
				return;
			}


			//Album action
			//GET DB
			$db = Engine_Api::_()->getItemTable('sitepage_album')->getAdapter();
			$db->beginTransaction($db);
			try {
				//SAVE VALUES
				$values = $album = $form->saveValues($values);

				//UPDATE VALUES
				Engine_Api::_()->getDbtable('photos', 'sitepage')->update(array('album_id' => $album->getIdentity(), 'page_id' => $page_id, 'collection_id' => $album->getIdentity(),), array('page_id =?' => $page_id, 'album_id =?' => 0, 'collection_id =?' => 0, 'user_id =?' => $viewer_id));

				//COMMIT
				$db->commit();
			} catch (Exception $e) {
				$db->rollBack();
				throw $e;
			}

      $this->_helper->redirector->gotoRoute(array('action' => 'view', 'page_id' => $page_id, 'album_id' => $album->getIdentity(), 'slug' => $album->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitepage_albumphoto_general', true);
    }
  }

  //ACTION FOR UPLOADING THE PHOTO
  public function uploadPhotoAction() {

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', (int) $this->_getParam('page_id'));

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    //START MANAGE-ADMIN CHECK
    if ($viewer_id != $sitepage->owner_id && empty($can_edit)) {
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
      if (empty($isManageAdmin)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //END MANAGE-ADMIN CHECK

    //CHECK MAX FILE SIZE
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    //CHECK FORM VALIDAION
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //GET FORM VALUES
    $values = $this->getRequest()->getPost();
    $response = Seaocore_Service_FancyUpload::upload();

    if (!empty($response['error'])) {
    	$this->view->status = false;
    	$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
    	return;
    }

    if (empty($response['path']) || empty(file_exists($response['path']))) {
    	$this->view->status = false;
    	$this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
    	return;
    }

    //GET DB
    $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitepage');
    $db = $tablePhoto->getAdapter();
    $db->beginTransaction();
    
    $rows = $tablePhoto->fetchRow($tablePhoto->select()->from($tablePhoto->info('name'), 'order')->order('order DESC')->limit(1));
    $order = 0;
    if (!empty($rows)) {
      $order = $rows->order + 1;
    }
    try {
      $params = array(
          'collection_id' => 0,
          'album_id' => 0,
          'page_id' => $sitepage->page_id,
          'user_id' => $viewer_id,
          'order' => $order,
      );
      $photoObj = $tablePhoto->createPhoto($params, $response['path']);
      $photoObj ? $photo_id = $photoObj->photo_id : $photo_id = 0;
      $this->view->status = true;
      // $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo_id;
      $db->commit();
      @unlink($response['path']);
      return $photo_id;
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_($e->getMessage());
      return;
    }
  }

  //ACTION FOR EDIT THE PHOTOS TITLE AND DISCRIPTION
  public function photoEditAction() {

    //GET PHOTO SUBJECT
    $photo = Engine_Api::_()->core()->getSubject();

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET PAGE ID
    $page_id = (int) $this->_getParam('page_id');

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id != $photo->user_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET PHOTO ID
    $photo_id = (int) $this->_getParam('photo_id');

    //GET ALBUM ID
    $album_id = (int) $this->_getParam('album_id');

    //EDIT PHOTO FORM
    $this->view->form = $form = new Sitepage_Form_Photo_Photoedit();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $form->populate($photo->toArray());
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $this->view->tab_selected_id = $this->_getParam('tab');

    //PROCESS
    $db = Engine_Api::_()->getDbtable('photos', 'sitepage')->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE VALUES
      $photo->setFromArray($form->getValues())->save();

      //GET FORM VALUES
      $values = $form->getValues();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved.')),
                'smoothboxClose' => 2,
                'parentRedirect' => $this->_helper->url->url(array('action' => 'view',
                    'photo_id' => $photo_id, 'page_id' => $page_id, 'album_id' => $album_id, 'tab' => $this->view->tab_selected_id
                        ), 'sitepage_imagephoto_specific', true),
                'parentRedirectTime' => '2',
            ));
  }

  //ACTION FOR DELETE THE PHOTOS
  public function removeAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET PHOTO ID
    $photo_id = (int) $this->_getParam('photo_id');

    //GET PAGE ID
    $page_id = (int) $this->_getParam('page_id');

    //GET ALBUM ID
    $album_id = (int) $this->_getParam('album_id');

    //GET TAB ID
    $tab_selected_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $page_id, Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0));

    //GET PHOTO ITEM
    $photo = Engine_Api::_()->getItem('sitepage_photo', $photo_id);

    //GET COLLECTION OF ALBUM
    $album = $photo->getCollection();

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN DELETE PHOTO
    if ($viewer_id != $photo->user_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET REQUEST ISAJAX OR NOT
    $isajax = (int) $this->_getParam('isajax');
    if ($isajax) {
      $photo->delete();
      return $this->_helper->json(array());
    }

    //GET PHOTO DELETE FORM
    $this->view->form = $form = new Sitepage_Form_Photo_Delete();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $form->populate($photo->toArray());
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET DB
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //DELETE PHOTO
      $photo->delete();

      //SET PAGE PHOTO PARAMS
      $paramsPhoto = array();
      $paramsPhoto['album_id'] = $album_id;
      $paramsPhoto['order'] = 'order ASC';

      //COUNT PHOTOS
      $count = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);

      if (empty($count)) {
        Engine_Api::_()->getItemTable('sitepage_album')->update(array('photo_id' => 0), array('album_id = ?' => $album_id, 'default_value=?' => 1));
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
        throw $e;
      }

    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted.')),
                'smoothboxClose' => 2,
                'parentRedirect' => $this->_helper->url->url(array('action' => 'view', 'page_id' => $page_id, 'album_id' => $album_id, 'slug' => $album->getSlug(), 'tab' => $tab_selected_id), 'sitepage_albumphoto_general', true),
                'parentRedirectTime' => '2',
            ));
  }

  //ACTION FOR VIEWS THE PHOTOS
  public function viewAction() {

    //GET REQUEST ISAJAX OR NOT
    $this->view->isajax = (int) $this->_getParam('isajax', 0);
    $this->view->sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');

    //SEND ALBUM ID TO THE TPL
    $this->view->album_id = (int) $this->_getParam('album_id');

    //SEND ALBUM ID TO THE TPL
    $photo_id = (int) $this->_getParam('photo_id');

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

    //CHECK SUBJECT IS THERE OR NOT
    if (Engine_Api::_()->core()->hasSubject() == null) {
      return $this->_forwardCustom('notfound', 'error', 'core');
    }

    //GET PHOTO SUBJECT
    $this->view->image = $photo = Engine_Api::_()->getItem('sitepage_photo', $photo_id);

    if (!$photo) {
      return $this->_forwardCustom('notfound', 'error', 'core');
    }

    //GET ALBUM INFORMATION
    $this->view->album = $album = $photo->getCollection();

    if (!$album) {
      return $this->_forwardCustom('notfound', 'error', 'core');
    }

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET SITEPAGE ITEM
    if (!empty($album)) {
      $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $album->page_id);
    }

    //START MANAGE-ADMIN CHECK
    if (!empty($sitepage)) {
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
      if (empty($isManageAdmin)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }

      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
      if (empty($isManageAdmin)) {
        $this->view->can_comment = 0;
      } else {
        $this->view->can_comment = 1;
      }

      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
      if (empty($isManageAdmin)) {
        $can_edit = 0;
      } else {
        $can_edit = 1;
      }
    }
    //END MANAGE-ADMIN CHECK

    if ($can_edit) {
      $this->view->canTag = 1;
      $this->view->canUntagGlobal = 1;
    } else {
      $this->view->canTag = $album->authorization()->isAllowed($viewer, 'tag');
      $this->view->canUntagGlobal = $album->isOwner($viewer);
    }

    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT AND DELETE PHOTO
    if ($viewer_id == $photo->user_id || $can_edit == 1) {
      $this->view->canDelete = 1;
      $this->view->canEdit = 1;
    } else {
      $this->view->canDelete = 0;
      $this->view->canEdit = 0;
    }

    if (!empty($viewer_id) && $viewer_id != $photo->user_id) {
      $this->view->report = $report = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.report', 1);
    }

    if (!empty($viewer_id)) {
      $this->view->share = $share = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.share', 1);
    }

    //INCREMENT VIEWS
    if (!$photo->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
      $photo->view_count++;
      ;
    }

		$this->view->allowFeatured = false;
		if (!empty($viewer_id) && $viewer->level_id == 1) {
			$auth = Engine_Api::_()->authorization()->context;
			$this->view->allowFeatured = $auth->isAllowed($sitepage, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($sitepage, 'registered', 'view') === 1 ? true : false;
		}
    //SAVE
    $photo->save();

    $this->view->showLightBox = Engine_Api::_()->sitepage()->canShowPhotoLightBox();
    $this->view->enablePinit = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.pinit', 0);
  }

  //ACTION FOR EDIT THE DESCRIPTION OF THE PHOTOS
  public function editDescriptionAction() {

    //GET TEXT
    $text = $this->_getParam('text_string');

    //GET PHOTO ITEM
    $photo = Engine_Api::_()->getItem('sitepage_photo', $this->_getParam('photo_id'));

    //GET DB
    $db = Engine_Api::_()->getDbtable('photos', 'sitepage')->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE VALUE
      $photo->description = $text;
      $photo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    exit();
  }

  //ACTION FOR ROTATE THE PHOTOS
  public function rotateAction() {

    //CHECK PHOTO SUBJECT IS OR NOT
    if (!$this->_helper->requireSubject('sitepage_photo')->isValid())
      return;

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    //GET PHOTO ITEM
    $photo = Engine_Api::_()->core()->getSubject('sitepage_photo');

    //GET ANGLE
    $angle = (int) $this->_getParam('angle', 90);
    if (!$angle || !($angle % 360)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid angle, must not be empty');
      return;
    }
    if (!in_array((int) $angle, array(90, 270))) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid angle, must be 90 or 270');
      return;
    }

    //GET FILE
    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }

    $tmpFile = $file->temporary();
    $image = Engine_Image::factory();
    $image->open($tmpFile)
            ->rotate($angle)
            ->write()
            ->destroy();

    //GET DB
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $photo->setPhoto($tmpFile);
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }

  //ACTION FOR FLIP THE PHOTOS
  public function flipAction() {

    //CHECK PHOTO SUBJECT IS OR NOT
    if (!$this->_helper->requireSubject('sitepage_photo')->isValid())
      return;

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    //GET PHOTO ITEM
    $photo = Engine_Api::_()->core()->getSubject('sitepage_photo');

    //GET DIRECTION
    $direction = $this->_getParam('direction');
    if (!in_array($direction, array('vertical', 'horizontal'))) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid direction');
      return;
    }

    //GET FILE
    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }

    $tmpFile = $file->temporary();
    $image = Engine_Image::factory();
    $image->open($tmpFile)
            ->flip($direction != 'vertical')
            ->write()
            ->destroy();

    //GET DB
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $photo->setPhoto($tmpFile);
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }

  //ACTION FOR DOWNLOAD THE PHOTOS
  public function downloadAction() {

    //GET PATH
    $path = urldecode($_GET['path']);
    $path = preg_replace('/\.{2,}/', '.', $path);
    $path = preg_replace('/[\/\\\\]+/', '/', $path);
    $path = trim($path, './\\');

    if (!Engine_Api::_()->seaocore()->isCdn()) {
      $pathArray = explode('?', $path);
      $path = $pathArray['0'];
      $pathRemoveArray = explode('/', $path);

      if ($pathRemoveArray['0'] != 'public') {
        unset($pathRemoveArray['0']);
      }
      $path = implode('/', $pathRemoveArray);
      $path = APPLICATION_PATH . '/' . $path;
    }
    $explodePath = explode("?", $path);
    $path = $explodePath['0'];
    if (ob_get_level()) {
      while (@ob_end_clean());
    }
    header("Content-Disposition: attachment; filename=" . @urlencode(basename($path)), true);
    header("Content-Transfer-Encoding: Binary", true);
    header("Content-Type: application/force-download", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Type: application/download", true);
    header("Content-Description: File Transfer", true);
    header("Content-Length: " . @filesize($path), true);
    flush();

    $fp = @fopen($path, "r");
    while (!@feof($fp)) {
      echo @fread($fp, 65536);
      flush();
    }
    @fclose($fp);

    exit();
  }

  public function makePageProfilePhotoAction() {
    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET PHOTO
    $photo = Engine_Api::_()->getItemByGuid($this->_getParam('photo'));

    if (!$photo || !($photo instanceof Core_Model_Item_Collectible) || empty($photo->photo_id)) {
      $this->_forwardCustom('requiresubject', 'error', 'core');
      return;
    }

    //MAKE FORM
    $this->view->photo = $photo;

    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
      //PROCESS
      $table = Engine_Api::_()->getItemTable('sitepage_page');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try {

        if ($sitepage->photo_id != $photo->file_id) {

          //ENSURE THUMB.ICON AND THUMB.PROFILE EXIST
          $newStorageFile = Engine_Api::_()->getItem('storage_file', $photo->file_id);
          $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
          if ($photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.profile')) {
            try {
              $tmpFile = $newStorageFile->temporary();
              $image = Engine_Image::factory();
              $image->open($tmpFile)
                      ->resize(200, 400)
                      ->write($tmpFile)
                      ->destroy();
              $iProfile = $filesTable->createFile($tmpFile, array(
                  'parent_type' => 'sitepage_page',
                  'parent_id' => $page_id,
                  'user_id' => $sitepage->owner_id,
                  'name' => basename($tmpFile),
                      ));
              $newStorageFile->bridge($iProfile, 'thumb.profile');
              @unlink($tmpFile);
            } catch (Exception $e) {
              echo $e;
              die();
            }
          }
          if ($photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.icon')) {
            try {
              $tmpFile = $newStorageFile->temporary();
              $image = Engine_Image::factory();
              $image->open($tmpFile);
              $size = min($image->height, $image->width);
              $x = ($image->width - $size) / 2;
              $y = ($image->height - $size) / 2;
              $image->resample($x, $y, $size, $size, 48, 48)
                      ->write($tmpFile)
                      ->destroy();
              $iSquare = $filesTable->createFile($tmpFile, array(
                  'parent_type' => 'sitepage_page',
                  'parent_id' => $page_id,
                  'user_id' => $sitepage->owner_id,
                  'name' => basename($tmpFile),
                      ));
              $newStorageFile->bridge($iSquare, 'thumb.icon');
              @unlink($tmpFile);
            } catch (Exception $e) {
              echo $e;
              die();
            }
          }

          //Set it
          $sitepage->photo_id = $photo->file_id;
          $sitepage->save();
          $db->commit();

          //INSERT ACTIVITY
          //@TODO MAYBE IT SHOULD READ "CHANGED THEIR PROFILE PHOTO" ?
          $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
          $activityFeedType = null;
          if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
            $activityFeedType = 'sitepage_admin_profile_photo';
          elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage))
            $activityFeedType = 'sitepage_profile_photo_update';


          if ($activityFeedType) {
            $action = $activityApi->addActivity(Engine_Api::_()->user()->getViewer(), $sitepage, $activityFeedType);
            Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
          }

          if ($action) {
            //WE HAVE TO ATTACH THE USER HIMSELF W/O ALBUM PLUGIN
            $activityApi
                    ->attachActivity($action, $photo);
          }
        }
      }

      //OTHERWISE IT'S PROBABLY A PROBLEM WITH THE DATABASE OR THE STORAGE SYSTEM (JUST THROW IT)
      catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your page profile photo has been successfully changed.')),
                  'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id)), 'sitepage_entry_view', true),
                  'smoothboxClose' => true,
              ));
    }
  }

  public function featuredAction() {

    $this->view->photo = $photo = Engine_Api::_()->getItem('sitepage_photo', $this->_getParam('photo_id', $this->_getParam('photo_id', null)));
    $photo->featured = !$photo->featured;
    $photo->save();
    exit(0);
  }

  //ACTION FOR ADDING PAGE OF THE DAY
  public function addPhotoOfDayAction() {


    //FORM GENERATION
    //$photo = Engine_Api::_()->core()->getSubject();
    $photo_id = $this->_getParam('photo_id');
    $form = $this->view->form = new Sitepagealbum_Form_ItemOfDayday();
    $form->setTitle('Make this Photo of the Day')
            ->setDescription('Select a start date and end date below.This photo will be displayed as "Photo of the Day" for this duration.If more than one photos of the day are found for a date then randomly one will be displayed.');

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();
      //$values["resource_id"] = $photo->getIdentity();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage');

        //FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $photo_id)->where('resource_type = ?', 'sitepage_photo');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $photo_id;
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
        $row->resource_type = 'sitepage_photo';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
                  'layout' => 'default-simple',
                  'smoothboxClose' => true,
              ));
    }
  }

  // ACTION FOR FEATURED PHOTOS CAROUSEL AFTER CLICK ON BUTTON 
  public function featuredPhotosCarouselAction() {
    //RETRIVE THE VALUE OF ITEM VISIBLE
    $this->view->itemsVisible = $limit = (int) $_GET['itemsVisible'];

    //RETRIVE THE VALUE OF NUMBER OF ROW
    $this->view->noOfRow = (int) $_GET['noOfRow'];
    //RETRIVE THE VALUE OF ITEM VISIBLE IN ONE ROW
    $this->view->inOneRow = (int) $_GET['inOneRow'];

    // Total Count Featured Photos
    $totalCount = (int) $_GET['totalItem'];

    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'] * $limit;

    if ($startindex > $totalCount) {
      $startindex = $totalCount - $limit;
    }
    if ($startindex < 0)
      $startindex = 0;

    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $direction = $_GET['direction'];
    $this->view->offset = $values['start_index'] = $startindex;
    $values['category_id'] = $_GET['category_id'];
    //GET Featured Photos with limit * 2
    $this->view->totalItemsInSlide = $values['limit'] = $limit * 2;
    $this->view->featuredPhotos = $featuredPhotos = Engine_Api::_()->sitepagealbum()->getFeaturedPhotos($values);

    //Pass the total number of result in tpl file
    $this->view->count = count($featuredPhotos);

    //Pass the direction of button in tpl file
    $this->view->direction = $direction;
    $this->view->showLightBox = Engine_Api::_()->sitepage()->canShowPhotoLightBox();
    if ($this->view->showLightBox) {
      $this->view->params = $params = array('type' => 'featured', 'count' => $totalCount);
    }
  }
  
  public function suggestAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      $data = null;
    } else {
    
      $page_id =  $this->_getParam('page_id');
      $values['page_id'] = $page_id;
      
      $data = array();
      if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))) ) {
        $values['search'] = $text;
      }
      
      $select = Engine_Api::_()->getDbtable('membership', 'sitepage')->getsitepagemembersSelect($values);

      foreach( $select->getTable()->fetchAll($select) as $friend ) {
        $data[] = array(
          'type'  => 'user',
          'id'    => $friend->getIdentity(),
          'guid'  => $friend->getGuid(),
          'label' => $friend->getTitle(),
          'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
          'url'   => $friend->getHref(),
        );
      }
    }

    if( $this->_getParam('sendNow', true) ) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }
}

?>