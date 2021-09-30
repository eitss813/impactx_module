<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_IndexController extends Seaocore_Controller_Action_Standard {

    //ACTION FOR HOME PAGE
    public function indexAction() {

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
            return;
        }

        $getLightBox = Zend_Registry::isRegistered('sitealbum_getajaxview') ? Zend_Registry::get('sitealbum_getajaxview') : null;
        if (empty($getLightBox)) {
            return;
        }
        //OPEN TAB IN NEW PAGE
        if ($this->renderWidgetCustom())
            return;
        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION FOR BROWSE PAGE 
    public function browseAction() {

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
            return;
        }

        //GET PAGE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitealbum_index_browse");
        $pageObject = $pageTable->fetchRow($pageSelect);

        $params = array();
        $album_type_title = '';
        if (!empty($pageObject->title)) {
            $params['default_title'] = $title = $pageObject->title;
        } else {
            $params['default_title'] = $title = Zend_Registry::get('Zend_Translate')->_('Browse Albums');
        }

        //GET ALBUM CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitealbum');
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $category_id = $request->getParam('category_id', null);

        if (!empty($category_id)) {
            if ($album_type_title)
                $params['album_type_title'] = $title = $album_type_title;
            $meta_title = $tableCategory->getCategory($category_id)->meta_title;
            if (empty($meta_title)) {
                $params['categoryname'] = Engine_Api::_()->getItem('album_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname'] = $meta_title;
            }
            $meta_description = $tableCategory->getCategory($category_id)->meta_description;
            if (!empty($meta_description))
                $params['description'] = $meta_description;

            $meta_keywords = $tableCategory->getCategory($category_id)->meta_keywords;
            if (empty($meta_keywords)) {
                $params['categoryname_keywords'] = Engine_Api::_()->getItem('album_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname_keywords'] = $meta_keywords;
            }

            $subcategory_id = $request->getParam('subcategory_id', null);
            if (!empty($subcategory_id)) {
                $meta_title = $tableCategory->getCategory($subcategory_id)->meta_title;
                if (empty($meta_title)) {
                    $params['subcategoryname'] = Engine_Api::_()->getItem('album_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname'] = $meta_title;
                }

                $meta_description = $tableCategory->getCategory($subcategory_id)->meta_description;
                if (!empty($meta_description))
                    $params['description'] = $meta_description;

                $meta_keywords = $tableCategory->getCategory($subcategory_id)->meta_keywords;
                if (empty($meta_keywords)) {
                    $params['subcategoryname_keywords'] = Engine_Api::_()->getItem('album_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname_keywords'] = $meta_keywords;
                }
            }
        }

        //SET META TITLE
        Engine_Api::_()->sitealbum()->setMetaTitles($params);

        //SET META TITLE
        Engine_Api::_()->sitealbum()->setMetaDescriptionsBrowse($params);

        //GET LOCATION
        if (isset($_GET['location']) && !empty($_GET['location'])) {
            $params['location'] = $_GET['location'];
        }

        //GET TAG
        if (isset($_GET['tag']) && !empty($_GET['tag'])) {
            $params['tag'] = $_GET['tag'];
        }

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $params['search'] = $_GET['search'];
        }

        //GET ALBUMS TITLE
        $params['album_type_title'] = $this->view->translate('Albums');

        //SET META KEYWORDS
        Engine_Api::_()->sitealbum()->setMetaKeywords($params);

        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION FOR PINBOARD VIEW
    public function pinboardAction() {

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
            return;
        }

        //GET PAGE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitealbum_index_pinboard");
        $pageObject = $pageTable->fetchRow($pageSelect);

        $params = array();
        $album_type_title = '';
        if (!empty($pageObject->title)) {
            $params['default_title'] = $title = $pageObject->title;
        } else {
            $params['default_title'] = $title = Zend_Registry::get('Zend_Translate')->_('Albums Pinboard');
        }

        //GET ALBUM CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitealbum');
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $category_id = $request->getParam('category_id', null);

        if (!empty($category_id)) {
            if ($album_type_title)
                $params['album_type_title'] = $title = $album_type_title;
            $meta_title = $tableCategory->getCategory($category_id)->meta_title;
            if (empty($meta_title)) {
                $params['categoryname'] = Engine_Api::_()->getItem('album_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname'] = $meta_title;
            }
            $meta_description = $tableCategory->getCategory($category_id)->meta_description;
            if (!empty($meta_description))
                $params['description'] = $meta_description;

            $meta_keywords = $tableCategory->getCategory($category_id)->meta_keywords;
            if (empty($meta_keywords)) {
                $params['categoryname_keywords'] = Engine_Api::_()->getItem('album_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname_keywords'] = $meta_keywords;
            }

            $subcategory_id = $request->getParam('subcategory_id', null);

            if (!empty($subcategory_id)) {
                $meta_title = $tableCategory->getCategory($subcategory_id)->meta_title;
                if (empty($meta_title)) {
                    $params['subcategoryname'] = Engine_Api::_()->getItem('album_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname'] = $meta_title;
                }

                $meta_description = $tableCategory->getCategory($subcategory_id)->meta_description;
                if (!empty($meta_description))
                    $params['description'] = $meta_description;

                $meta_keywords = $tableCategory->getCategory($subcategory_id)->meta_keywords;
                if (empty($meta_keywords)) {
                    $params['subcategoryname_keywords'] = Engine_Api::_()->getItem('album_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname_keywords'] = $meta_keywords;
                }
            }
        }

        //SET META TITLE
        Engine_Api::_()->sitealbum()->setMetaTitles($params);

        //SET META TITLE
        Engine_Api::_()->sitealbum()->setMetaDescriptionsBrowse($params);

        //GET LOCATION
        if (isset($_GET['location']) && !empty($_GET['location'])) {
            $params['location'] = $_GET['location'];
        }

        //GET TAG
        if (isset($_GET['tag']) && !empty($_GET['tag'])) {
            $params['tag'] = $_GET['tag'];
        }

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $params['search'] = $_GET['search'];
        }

        //GET ALBUMS TITLE
        $params['album_type_title'] = $this->view->translate('Albums');

        //SET META KEYWORDS
        Engine_Api::_()->sitealbum()->setMetaKeywords($params);

        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION FOR MY ALBUM PAGE
    public function manageAction() {

        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    //ACTION FOR BROWSE LOCATION PAGES.
    public function mapAction() {

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
            return;
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum')) {
            $this->_helper->content->setNoRender()->setEnabled();
        } else {
            return $this->_forward('notfound', 'error', 'core');
        }
    }

    //ACTION FOR UNHIDE THE PHOTO
    public function unhidePhotoAction() {

        // CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //UNHIDE PHOTO FORM
        $this->view->form = $form = new Sitealbum_Form_Unhidephoto();

        //CHECK FORM VALIDAITON
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            Engine_Api::_()->getItemTable('album_photo')->update(array('photo_hide' => 0), array('owner_id = ?' => $this->_getParam('user_id', null), 'photo_hide = ?' => 1));

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'format' => 'smoothbox',
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your photos have been restored.'))
            ));
        }
    }

    public function taggedUserAction() {

        if (0 == ($album_id = (int) $this->_getParam('album_id')) || null == ($album = Engine_Api::_()->getItem('album', $album_id))) {
            return $this->_forward('notfound', 'error', 'core');
        }

        if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'view')->isValid()) {
            return;
        }

        $this->view->insideAlbum = $inSideAlbum = Engine_Api::_()->sitealbum()->getTaggedUser($album_id);
        $this->view->viewer = $this->view->viewer();
    }

    public function youAndOwnerPhotosAction() {

        // Check auth
        if (!$this->_helper->requireUser()->isValid())
            return;

        if (0 == ($owner_id = (int) $this->_getParam('owner_id')) || null == ($owner = Engine_Api::_()->getItem('user', $owner_id))) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->owner = $owner;

        $this->view->youAndOwner = $youAndOwner = Engine_Api::_()->getDbTable('photos', 'sitealbum')->getTaggedYouAndOwnerPhotos($viewer_id, $owner_id);
        $youAndOwner->setItemCountPerPage(100);
        $youAndOwner->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->showLightBox = Engine_Api::_()->sitealbum()->showLightBoxPhoto();
    }

    // ACTION FOR FEATURED PHOTOS CAROUSEL AFTER CLICK ON BUTTON 
    public function featuredPhotosCarouselAction() {

        //RETURN THE OBJECT OF LIMIT PER PAGE FROM CORE SETTING TABLE
        $this->view->sponserdSitealbumsCount = $limit_sitealbum = $_GET['curnt_limit'];
        $limit_sitealbum_horizontal = $limit_sitealbum * 2;
        $values = array();
        $values = $this->_getAllParams();

        //GET COUNT
        $totalCount = $_GET['total'];
        //RETRIVE THE VALUE OF START INDEX
        $startindex = $_GET['startindex'];
        if ($startindex > $totalCount) {
            $startindex = $totalCount - $limit_sitealbum;
        }
        if ($startindex < 0) {
            $startindex = 0;
        }
        $this->view->photoInfo = $param['photoInfo'] = $this->_getParam('photoInfo', array("photoTitle", "ownerName", "viewCount", "likeCount", "commentCount"));
        //RETRIVE THE VALUE OF BUTTON DIRECTION
        $this->view->direction = $_GET['direction'];
        $values['start_index'] = $startindex;
        $this->view->totalItemsInSlide = $values['limit'] = $limit_sitealbum_horizontal;

        $values['orderby'] = $this->_getParam('orderby', 'comment_count');
        $values['featured'] = $this->_getParam('featured', 0);
        $values['category_id'] = $this->_getParam('category_id');
        $values['subcategory_id'] = $this->_getParam('subcategory_id');

        $this->view->photos = Engine_Api::_()->getDbTable('photos', 'sitealbum')->photoBySettings($values);
        $this->view->count = count($this->view->photos);
        $this->view->vertical = $_GET['vertical'];
        $this->view->photoTitleTruncation = $this->_getParam('photoTitleTruncation', 22);
        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
        $this->view->blockHeight = $this->_getParam('blockHeight', 250);
        $this->view->photoWidth = $param['blockWidth'] = $this->_getParam('photoWidth', 200);
        $this->view->photoHeight = $param['blockWidth'] = $this->_getParam('photoHeight', 200);
        $this->view->normalPhotoWidth = $this->_getParam('normalPhotoWidth', 375);
        $this->view->photo_type = $this->_getParam('photo_type');
        $this->view->sitealbum_last_photoid = $this->_getParam('sitealbum_last_photoid');
        $this->view->params = $this->_getParam('params', array());
        $this->view->showLightBox = $this->_getParam('showLightBox', 1);
    }

    //ACTION FOR ADD PHOTOS 
    public function uploadAction() {

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            if (isset($_GET['ul'])) {
                return $this->_forward('upload-photo', null, null, array('format' => 'json'));
            }
       
            if (isset($_FILES['Filedata']))
                $_POST['file'] = $this->uploadPhotoAction();

            if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid()) {
                return;
            }
            // Render
            $this->_helper->content->setEnabled();

            //GET DEFAULT PROFILE TYPE ID
            $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitealbum')->defaultProfileId();

            // Get form
            $this->view->form = $form = new Sitealbum_Form_Album(array('defaultProfileId' => $defaultProfileId));
            if (!$this->getRequest()->isPost()) {
                if (null !== ($album_id = $this->_getParam('album_id'))) {
                    $form->populate(array(
                        'album' => $album_id
                    ));
                }
                $this->renderScript('index/upload-album.tpl');
                return;
            }

            if (!$form->isValid($this->getRequest()->getPost())) {
                $this->renderScript('index/upload-album.tpl');
                return;
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $album = $form->saveValues();
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 1)) {
                    //ADDING TAGS
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $keywords = '';
                    $tags = $form->getValue('tags');
                    if (!empty($tags)) {
                        $tags = preg_split('/[,]+/', $tags);
                        $tags = array_filter(array_map("trim", $tags));
                        $album->tags()->addTagMaps($viewer, $tags);

                        foreach ($tags as $tag) {
                            $keywords .= " $tag";
                        }
                    }
                }

                if ($album->category_id) {
                    $categoryIds[] = $album->category_id;
                    $categoryIds[] = $album->subcategory_id;
                    $album->profile_type = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));
                }
                //SAVE LOCATION
                $location = $form->getValue('sitealbum_location');
                if (!empty($location) && $album) {
                    $seaoLocationId = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($location, '', $album->getType(), $album->getIdentity());
                    $album->seao_locationid = $seaoLocationId;
                    $album->location = $location;
                    if (Engine_Api::_()->hasModuleBootstrap('sitetagcheckin') && isset($form->dataParams) && !empty($form->dataParams) && empty($album->password)) {
                        $data_params = array();
                        parse_str($form->getValue('dataParams'), $data_params);
                        Engine_Api::_()->sitealbum()->saveInCheckinTable($data_params, $location, $album);
                    }
                }

                $album->save();

                //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                $customfieldform = $form->getSubForm('fields');
                $customfieldform->setItem($album);
                $customfieldform->saveValues();

                //COMMIT
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            //UPDATE KEYWORDS IN SEARCH TABLE
            if (!empty($keywords)) {
                Engine_Api::_()->getDbTable('search', 'core')->update(array('keywords' => $keywords), array('type = ?' => 'album', 'id = ?' => $album->album_id));
            }

            $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'album_id' => $album->album_id), 'sitealbum_specific', true);
        } else {//Mobile site code
            if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid())
                return;

            // Render
            $this->_helper->content->setEnabled();

            //GET DEFAULT PROFILE TYPE ID
            $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitealbum')->defaultProfileId();

            // Get form
            $this->view->form = $form = new Sitealbum_Form_Album(array('defaultProfileId' => $defaultProfileId));

            if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
                Zend_Registry::set('setFixedCreationForm', true);
                Zend_Registry::set('setFixedCreationHeaderTitle', 'Add Photos');
                Zend_Registry::set('setFixedCreationHeaderSubmit', 'Save');
                $this->view->form->setAttrib('id', 'form-upload');
                Zend_Registry::set('setFixedCreationFormId', '#form-upload');
                $this->view->form->removeElement('submit');
                $form->setTitle('');
            }
            if (!$this->getRequest()->isPost()) {
                if (null !== ($album_id = $this->_getParam('album_id'))) {
                    $form->populate(array(
                        'album' => $album_id
                    ));
                }
                return;
            }

            //possible errors
            $this->view->clear_cache = true;
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }
            if (!$this->_helper->requireUser()->checkRequire()) {
                $this->view->status = false;
                $form->addError(Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).'));
                return;
            }

            $db = Engine_Api::_()->getDbtable('photos', 'sitealbum')->getAdapter();
            $db->beginTransaction();

            //COUNT NO. OF PHOTOS (CHECK ATLEAST SINGLE PHOTO UPLOAD).
            $count = 0;
            foreach ($_FILES['Filedata']['name'] as $data) {
                if (!empty($data)) {
                    $count = 1;
                    break;
                }
            }

            try {
                if (!isset($_FILES['Filedata']) || !isset($_FILES['Filedata']['name']) || $count == 0) {
                    $this->view->status = false;
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
                    return;
                }
                $values = $form->getValues();

                $viewer = Engine_Api::_()->user()->getViewer();
                $values['file'] = array();
                $photoTable = Engine_Api::_()->getDbtable('photos', 'sitealbum');
                foreach ($_FILES['Filedata']['name'] as $key => $uploadFile) {
                    $file = array('name' => $_FILES['Filedata']['name'][$key], 'tmp_name' => $_FILES['Filedata']['tmp_name'][$key], 'type' => $_FILES['Filedata']['type'][$key], 'size' => $_FILES['Filedata']['size'][$key], 'error' => $_FILES['Filedata']['error'][$key]);

                    if (!is_uploaded_file($file['tmp_name'])) {
                        continue;
                    }
                    Engine_Api::_()->sitemobile()->autoRotationImage($file);
                    $photo = $photoTable->createRow();
                    $photo->setFromArray(array(
                        'owner_type' => 'user',
                        'owner_id' => $viewer->getIdentity()
                    ));
                    $photo->save();
                    $photo->order = $photo->photo_id;
                    $photo->setPhoto($file);
                    $photo->save();
                    $values['file'][] = $photo->photo_id;
                }

                if (count($values['file']) < 1) {
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
                    return;
                }
                $db->commit();
            } catch (Album_Model_Exception $e) {
                $db->rollBack();
                throw $e;
                return;
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
                return;
            }
            $db = Engine_Api::_()->getItemTable('album')->getAdapter();
            $db->beginTransaction();

            try {
                $set_cover = false;

                $params = Array();
                if ((empty($values['owner_type'])) || (empty($values['owner_id']))) {
                    $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
                    $params['owner_type'] = 'user';
                } else {
                    $params['owner_id'] = $values['owner_id'];
                    $params['owner_type'] = $values['owner_type'];
                    throw new Zend_Exception("Non-user album owners not yet implemented");
                }

                if (($values['album'] == 0)) {
                    $params['title'] = $values['title'];
                    if (empty($params['title'])) {
                        $params['title'] = "Untitled Album";
                    }
                    $params['category_id'] = (int) @$values['category_id'];
                    $params['description'] = $values['description'];
                    $params['search'] = $values['search'];
                    $generateFeed = true;
                    if (isset($values['password']) && !empty($values['password'])) {
                        $params['search'] = 0;
                        $generateFeed = false;
                    }
                    $album = Engine_Api::_()->getDbtable('albums', 'sitealbum')->createRow();
                    $album->setFromArray($params);

                    $album->save();

                    $set_cover = true;

                    // CREATE AUTH STUFF HERE
                    $auth = Engine_Api::_()->authorization()->context;
                    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                    if (empty($values['auth_view'])) {
                        $values['auth_view'] = key($form->auth_view->options);
                        if (empty($values['auth_view'])) {
                            $values['auth_view'] = 'everyone';
                        }
                    }
                    if (empty($values['auth_comment'])) {
                        $values['auth_comment'] = key($form->auth_comment->options);
                        if (empty($values['auth_comment'])) {
                            $values['auth_comment'] = 'owner_member';
                        }
                    }
                    if (empty($values['auth_tag'])) {
                        $values['auth_tag'] = key($form->auth_tag->options);
                        if (empty($values['auth_tag'])) {
                            $values['auth_tag'] = 'owner_member';
                        }
                    }

                    $viewMax = array_search($values['auth_view'], $roles);
                    $commentMax = array_search($values['auth_comment'], $roles);
                    $tagMax = array_search($values['auth_tag'], $roles);

                    foreach ($roles as $i => $role) {
                        $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
                        $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
                        $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
                    }
                } else {
                    if (!isset($album)) {
                        $album = Engine_Api::_()->getItem('album', $values['album']);
                    }
                }

                // Add action and attachments
                $api = Engine_Api::_()->getDbtable('actions', 'activity');
                if ($generateFeed) {

                    if (count($values['file']) == 1) {
                        $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_single_photo_upload', null);
                    } else {
                        $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_photo_new', null, array('count' => count($values['file'])));
                    }
                }

                // Do other stuff
                $count = 0;
                foreach ($values['file'] as $photo_id) {
                    $photo = Engine_Api::_()->getItem("album_photo", $photo_id);
                    if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
                        continue;

                    if ($set_cover) {
                        $album->photo_id = $photo_id;
                        $album->save();
                        $set_cover = false;
                    }

                    $photo->album_id = $album->album_id;
                    $photo->order = $photo_id;
                    $photo->save();

                    if ($generateFeed && $action instanceof Activity_Model_Action && $count < 8) {
                        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                    }
                    $count++;
                }

                //Added
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 1)) {
                    //ADDING TAGS
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $keywords = '';
                    $tags = $form->getValue('tags');
                    if (!empty($tags)) {
                        $tags = preg_split('/[,]+/', $tags);
                        $tags = array_filter(array_map("trim", $tags));
                        $album->tags()->addTagMaps($viewer, $tags);

                        foreach ($tags as $tag) {
                            $keywords .= " $tag";
                        }
                    }
                }

                $categoryIds[] = $album->category_id;
                $categoryIds[] = $album->subcategory_id;
                $album->profile_type = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));

                //SAVE LOCATION
                $location = $form->getValue('sitealbum_location');
                if (!empty($location)) {
                    $seaoLocationId = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($location, '', $album->getType(), $album->getIdentity());
                    $album->seao_locationid = $seaoLocationId;
                    $album->location = $location;
                    if (Engine_Api::_()->hasModuleBootstrap('sitetagcheckin') && isset($form->dataParams) && empty($album->password)) {
                        $data_params = array();
                        parse_str($form->getValue('dataParams'), $data_params);
                        Engine_Api::_()->sitealbum()->saveInCheckinTable($data_params, $location, $album);
                    }
                }
                $album->save();
                //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                $customfieldform = $form->getSubForm('fields');
                $customfieldform->setItem($album);
                $customfieldform->saveValues();
                //Added
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            //UPDATE KEYWORDS IN SEARCH TABLE
            if (!empty($keywords)) {
                Engine_Api::_()->getDbTable('search', 'core')->update(array('keywords' => $keywords), array('type = ?' => 'album', 'id = ?' => $album->album_id));
            }

            return $this->_forward('success', 'utility', 'core', array(
                        'redirect' => $this->_helper->url->url(array('action' => 'view', 'album_id' => $album->album_id), 'album_specific', true),
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Album has been created successfully.')),
            ));
        }
    }

    //ACTION FOR UPLOADING PHOTOS BY FANCY UPLOADER
    public function uploadPhotoAction() {

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid())
            return;

        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

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


        $db = Engine_Api::_()->getDbtable('photos', 'sitealbum')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();

            $photoTable = Engine_Api::_()->getDbtable('photos', 'sitealbum');
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
            ));
            $photo->save();

            $photo->order = $photo->photo_id;
            $photo->setPhoto($response['path']);
            $photo->save();
            $this->view->status = true;
            $this->view->name = $response['name'];
            $this->view->photo_id = $photo->photo_id;
            $this->view->src = $photo->getPhotoUrl();

            $exif_date = date("Y-m-d h:m:s");
            if (isset($exif_data['DateTime'])) {
                if (function_exists('exif_read_data')) {
                    $exif_data = exif_read_data($response['path']);
                    $exif_date = $exif_data['DateTime'];
                }
            }
            $this->view->currentDate = date("Y-m-d h:m:s");
            $this->view->dateTime = $exif_date;
            $this->view->datePhoto = date("Y-m-d", strtotime($exif_date));
            $db->commit();
            return $photo->photo_id;
        } catch (Album_Model_Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = $this->view->translate($e->getMessage());
            throw $e;
            return;
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_($e->getMessage());
            throw $e;
            return;
        }
        @unlink($response['path']);
    }

    //ACTION TO GET SUB-CATEGORY
    public function subCategoryAction() {

        //GET CATEGORY ID
        $category_id_temp = $this->_getParam('category_id_temp');
        $showAllCategories = $this->_getParam('showAllCategories', 1);

        //INTIALIZE ARRAY
        $this->view->subcats = $data = array();

        //RETURN IF CATEGORY ID IS EMPTY
        if (empty($category_id_temp))
            return;

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitealbum');

        //GET CATEGORY
        $category = $tableCategory->getCategory($category_id_temp);
        if (!empty($category->category_name)) {
            $categoryName = Engine_Api::_()->getItem('album_category', $category_id_temp)->getCategorySlug();
        }
        //GET SUB-CATEGORY
        $subCategories = $tableCategory->getSubCategories(array('category_id' => $category_id_temp, 'fetchColumns' => array('category_id', 'category_name', 'category_slug'), 'havingAlbums' => $showAllCategories));

        foreach ($subCategories as $subCategory) {
            $content_array = array();
            $content_array['category_name'] = $this->view->translate($subCategory->category_name);
            $content_array['category_id'] = $subCategory->category_id;
            $content_array['categoryname_temp'] = $categoryName;
            $content_array['category_slug'] = $subCategory->getCategorySlug();
            $data[] = $content_array;
        }

        $this->view->subcats = $data;
    }

    //ACTION TO SAVE RATING AND SEND DATA ARRAY
    public function rateAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $allowRating = Engine_Api::_()->authorization()->getPermission($level_id, 'album', 'rate');


        $rating = $this->_getParam('rating');
        $subject_id = $this->_getParam('subject_id');
        $subject_type = $this->_getParam('subject_type');

        if ((empty($viewer_id) || empty($allowRating)) || (!$subject_id && !$subject_type))
            return $this->_forward('requireauth', 'error', 'core');

        $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitealbum');

        $db = $ratingTable->getAdapter();
        $db->beginTransaction();

        try {
            $ratingTable->setRating($subject_id, $subject_type, $rating);

            $subject = Engine_Api::_()->getItem($subject_type, $subject_id);
            $subject->rating = $ratingTable->getRating($subject_id, $subject_type);
            $subject->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $total = $ratingTable->ratingCount(array('resource_id' => $subject_id, 'resource_type' => $subject_type));

        $data = array();
        $data[] = array(
            'total' => $total,
            'rating' => $rating,
        );
        return $this->_helper->json($data);
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }

    //ACTON FOR CATEGORIES PAGE
    public function categoriesAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
            return;
        }

        $siteinfo = $this->view->layout()->siteinfo;
        $titles = $siteinfo['title'];
        $keywords = $siteinfo['keywords'];
        $album_type_title = 'Albums';
        if (!empty($titles))
            $titles .= ' - ';
        $titles .= $album_type_title;
        $siteinfo['title'] = $titles;

        if (!empty($keywords))
            $keywords .= ' - ';
        $keywords .= $album_type_title;
        $siteinfo['keywords'] = $keywords;
        $this->view->layout()->siteinfo = $siteinfo;


        $this->_helper->content
                ->setNoRender()
                ->setEnabled();
    }

    //ACTION FOR GETTING THE AUTOSUGGESTED ALBUMS BASED ON SEARCHING
    public function getSearchAlbumsAction() {

        //GET ALBUMS AND MAKE ARRAY
        $usersitealbums = Engine_Api::_()->getDbtable('albums', 'sitealbum')->getDayItems($this->_getParam('text'), $this->_getParam('limit', 10));
        $data = array();
        $mode = $this->_getParam('struct');
        $count = count($usersitealbums);
        if ($mode == 'text') {
            $i = 0;
            foreach ($usersitealbums as $usersitealbum) {
                $sitealbum_url = $this->view->url(array('slug' => $usersitealbum->getSlug(), 'album_id' => $usersitealbum->album_id), "sitealbum_entry_view", true);
                $i++;
                $content_photo = $this->view->itemPhoto($usersitealbum, 'thumb.normal');
                $data[] = array(
                    'id' => $usersitealbum->album_id,
                    'label' => $usersitealbum->title,
                    'photo' => $content_photo,
                    'sitealbum_url' => $sitealbum_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        } else {
            $i = 0;
            foreach ($usersitealbums as $usersitealbum) {
                $sitealbum_url = $this->view->url(array('slug' => $usersitealbum->getSlug(), 'album_id' => $usersitealbum->album_id), "sitealbum_entry_view", true);
                $content_photo = $this->view->itemPhoto($usersitealbum, 'thumb.normal');
                $i++;
                $data[] = array(
                    'id' => $usersitealbum->album_id,
                    'label' => $usersitealbum->title,
                    'photo' => $content_photo,
                    'sitealbum_url' => $sitealbum_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        }
        if (!empty($data) && $i >= 1) {
            if ($data[--$i]['count'] == $count) {
                $data[$count]['id'] = 'stopevent';
                $data[$count]['label'] = $this->_getParam('text');
                $data[$count]['sitealbum_url'] = 'seeMoreLink';
                $data[$count]['total_count'] = $count;
            }
        }
        return $this->_helper->json($data);
    }

    // ACTION FOR EDIT ALBUM LOCATION
    public function editLocationAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'edit')->isValid())
            return;

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET PAGE ID, PAGE OBJECT AND THEN CHECK PAGE VALIDATION
        $resource = Engine_Api::_()->getItemByGuid($this->_getParam('subject'));
        $resource_type = $resource->getType();
        if ($resource_type == 'album') {
            $id = 'album_id';
            $itemTable = Engine_Api::_()->getDbtable('albums', 'sitealbum');
        } else {
            $id = 'photo_id';
            $itemTable = Engine_Api::_()->getDbtable('photos', 'sitealbum');
        }

        $resource_id = $resource->getIdentity();
        $this->view->form = $form = new Sitealbum_Form_Address(array('item' => $resource));

        //POPULATE FORM
        if (!$this->getRequest()->isPost()) {
            $form->populate($resource->toArray());
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            $values = $form->getValues();
            $resource->location = $values['location'];
            if (empty($values['location'])) {
                //DELETE THE RESULT FORM THE TABLE.
                Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $resource_id, 'resource_type = ?' => $resource_type));
                $resource->seao_locationid = '0';
            }
            $resource->save();
            unset($values['submit']);

            if (!empty($values['location'])) {

                //DELETE THE RESULT FORM THE TABLE.
                Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $resource_id, 'resource_type = ?' => $resource_type));

                $seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($values['location'], '', $resource_type, $resource_id);

                //group table entry of location id.
                $itemTable->update(array('seao_locationid' => $seaoLocation), array("$id =?" => $resource_id));
            }

            if (Engine_Api::_()->hasModuleBootstrap('sitetagcheckin') && isset($form->dataParams)) {
                $data_params = array();
                parse_str($form->getValue('dataParams'), $data_params);
                Engine_Api::_()->sitealbum()->saveInCheckinTable($data_params, $values['location'], $resource);
            }

            $db->commit();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 500,
                'parentRefresh' => 300,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Album location has been modified successfully.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR CONSTRUCT TAG CLOUD
    public function tagscloudAction() {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 0)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
            return;
        }
        $this->_helper->content
                ->setContentName('sitealbum_index_tagscloud')
                ->setNoRender()
                ->setEnabled();
    }

    //CREATED FOR MOBILE SITE & APP
    public function photosAction() {
        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
            return;
        }
        //OPEN TAB IN NEW PAGE
        if ($this->renderWidgetCustom())
            return;
        $this->_helper->content
                ->setNoRender()
                ->setEnabled();
    }

    //DELETING THE PHOTOS WHEN CANCEL ANY PHOTO
    public function cancelPhotosAction() {

        $photo_ids = explode(" ", trim($this->_getParam('photo_ids', $this->_getParam('photo_id'))));

        if (!$photo_ids) {
            return false;
        }
        foreach ($photo_ids as $photo_id) {
            if (Engine_Api::_()->hasItem('album_photo', $photo_id))
                Engine_Api::_()->getItem('album_photo', $photo_id)->delete();
        }

        $albumPhotoTable = Engine_Api::_()->getItemTable('album_photo');
        $albumPhotoTableName = $albumPhotoTable->info('name');

        $photos = $albumPhotoTable->select()->from($albumPhotoTableName, 'photo_id')
                ->where('album_id =?', 0)
                ->query()
                ->fetchAll();

        foreach ($photos as $photo) {
            if (Engine_Api::_()->hasItem('album_photo', $photo))
                Engine_Api::_()->getItem('album_photo', $photo)->delete();
        }
    }

    public function checkPasswordProtectionAction() {
        $album_id = (int) $this->_getParam('album_id');
        $album = Engine_Api::_()->getItem('album', $album_id);
        $password = $this->_getParam('password');
        $checkPasswordProtection = $album->checkPasswordProtection($password);
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        if (!$checkPasswordProtection) {
            setcookie("sitealbum_password_protected_$album_id", $checkPasswordProtection, time() + 60 * 60 * 24 * 30, $view->url(array(), 'default', true));
            echo Zend_Json::encode(array('status' => 0));
        } else {
            setcookie("sitealbum_password_protected_$album_id", $checkPasswordProtection, time() + 60 * 60 * 24 * 30, $view->url(array(), 'default', true));
            echo Zend_Json::encode(array('status' => 1));
        }
        exit();
    }

}
