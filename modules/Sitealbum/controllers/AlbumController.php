<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AlbumController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_AlbumController extends Seaocore_Controller_Action_Standard {

    protected $_set;

    public function init() {

        $this->_set = 0;
        if ($this->_getParam('set')) {
            $album_id = Engine_Api::_()->sitealbum()->getEncodeToDecode($this->_getParam('set'));
            if (Engine_Api::_()->getItem('album', $album_id))
                $this->_set = 1;
        }

        if (!$this->_set && !$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid())
            return;
        $hidePhotoUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.hide.photourl', null);
        if (empty($hidePhotoUrl)) {
            return;
        }

        if (0 !== ($album_id = (int) $this->_getParam('album_id')) &&
                null !== ($album = Engine_Api::_()->getItem('album', $album_id))) {
            Engine_Api::_()->core()->setSubject($album);
        }
    }

    //ACTION FOR ALBUM VIEW
    public function viewAction() {

        if (!$this->_helper->requireSubject('album')->isValid())
            return;

        $this->view->album = $album = Engine_Api::_()->core()->getSubject();

        $viewer = Engine_Api::_()->user()->getViewer();
        $album_id = $album->getIdentity();
        $sitealbum_password_protected = isset($_COOKIE["sitealbum_password_protected_$album_id"]) ? $_COOKIE["sitealbum_password_protected_$album_id"] : 0;
        if(isset($album->password) && !empty($album->password) && $album->owner_id != $viewer->getIdentity() && !$sitealbum_password_protected) {
         return $this->_forward('requireauth', 'error', 'sitealbum');
        }
        
        //Meta Keyword work
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $keywords = "";

        $categoryname = Engine_Api::_()->getDbtable('categories', 'sitealbum')->getCategoryName($album->category_id);
        if (isset($categoryname) && !empty($categoryname)) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $categoryname;
        }

        $subcategoryname = Engine_Api::_()->getDbtable('categories', 'sitealbum')->getCategoryName($album->subcategory_id);
        if (isset($subcategoryname) && !empty($subcategoryname)) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $subcategoryname;
        }

        if (isset($album->tag) && !empty($album->tag)) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $album->tag;
        }

        if (isset($album->location) && !empty($album->location)) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $album->location;
        }
        $siteinfo['keywords'] = trim($keywords, ',');
        $view->layout()->siteinfo = $siteinfo;


        if (!$album->isViewableByNetwork()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$this->_set && !$this->_helper->requireAuth()->setAuthParams($album, null, 'view')->isValid())
            return;

        $sitealbumType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.viewertype', null);
        if (empty($sitealbumType)) {
            return;
        }

        $getLightBox = Zend_Registry::isRegistered('sitealbum_getlightbox') ? Zend_Registry::get('sitealbum_getlightbox') : null;
        if (empty($getLightBox)) {
            return;
        }

        $this->_helper->content->setEnabled();

        //START: "SUGGEST TO FRIENDS" LINK WORK
        //HERE WE ARE DELETE THIS ALBUM SUGGESTION IF VIEWER HAVE
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion')) {
            Engine_Api::_()->sitealbum()->deleteSuggestion('album', $album->getIdentity(), 'album_suggestion');
        }
        if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
            Zend_Registry::set('setFixedCreationFormBack', 'Back');
        }
    }

    //ACTION FOR MAKING FEATURED ALBUM
    public function featuredAction() {
        if (!$this->_helper->requireSubject('album')->isValid())
            return;

        $this->view->album = $album = Engine_Api::_()->core()->getSubject();
        $album->featured = !$album->featured;
        $album->save();
        exit(0);
    }

    //ACTION FOR ADDING ALBUM OF THE DAY
    public function addAlbumOfDayAction() {

        //FORM GENERATION
        $album = Engine_Api::_()->core()->getSubject();

        // CHECK FOR ONLY ADMIN CAN ADD ALBUM OF THE DAY
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $allowView = $addAlbumOfTheDay = false;
        if (!empty($viewer_id) && $viewer->level_id == 1) {
            $addAlbumOfTheDay = true;
            $auth = Engine_Api::_()->authorization()->context;
            $allowView = $auth->isAllowed($album, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($album, 'registered', 'view') === 1 ? true : false;
        }

        if (!$addAlbumOfTheDay || !$allowView) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $form = $this->view->form = new Sitealbum_Form_ItemOfDayday();
        $form->setTitle('Album of the Day')
                ->setDescription('Select a start and end date to display your album as “Album of the Day”. You can select various albums to display them randomly in ‘Album Of the Day’ widget.');

        //CHECK POST
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $featuredAlbumType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.featuredalbum', null);
            if (empty($featuredAlbumType)) {
                return;
            }

            //GET FORM VALUES
            $values = $form->getValues();
            $values["resource_id"] = $album->getIdentity();
            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $table = Engine_Api::_()->getDbtable('itemofthedays', 'sitealbum');
                $row = $table->getItem('album', $values["resource_id"]);
                if (empty($row)) {
                    $row = $table->createRow();
                }
                $values = array_merge($values, array('resource_type' => 'album'));

                if ($values['start_date'] > $values['end_date'])
                    $values['end_date'] = $values['start_date'];
                $row->setFromArray($values);
                $row->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_forward('success', 'utility', 'core', array(
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
                        'layout' => 'default-simple',
                        'smoothboxClose' => true,
            ));
        }
    }

    //ACTION FOR MANAGE PHOTO
    public function editphotosAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!$this->_helper->requireSubject('album')->isValid())
            return;

        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            return;

        // Render
        $this->_helper->content->setEnabled();

        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitealbum_main');

        // Hack navigation
        foreach ($navigation->getPages() as $page) {
            if ($page->route != 'sitealbum_general' || $page->action != 'manage')
                continue;
            $page->active = true;
        }

        // Prepare data
        $this->view->album = $album = Engine_Api::_()->core()->getSubject();
        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array('album' => $album, 'order' => ''));

        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(5);

        // Get albums
        $albumTable = Engine_Api::_()->getItemTable('album');
        $myAlbums = $albumTable->select()
                ->from($albumTable, array('album_id', 'title', 'type'))
                ->where('owner_type = ?', 'user')
                ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
                ->query()
                ->fetchAll();

        $albumOptions = array('' => '');
        foreach ($myAlbums as $myAlbum) {

            if (($album->getIdentity() == $myAlbum['album_id']) || ($myAlbum['type'] != null))
                continue;
            $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
        }

        if (count($albumOptions) == 1) {
            $albumOptions = array();
        }

        // Make form
        $this->view->form = $form = new Sitealbum_Form_Album_Photos();

        foreach ($paginator as $photo) {
            $subform = new Sitealbum_Form_Album_EditPhoto(array('elementsBelongTo' => $photo->getGuid()));
            $subform->populate($photo->toArray());
            $form->addSubForm($subform, $photo->getGuid());
            $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
            if (empty($albumOptions)) {
                $subform->removeElement('move');
            } else {
                $subform->move->setMultiOptions($albumOptions);
            }
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();



            if (!empty($values['cover'])) {
                $album->photo_id = $values['cover'];
                $album->save();
            }

            // Process
            foreach ($paginator as $photo) {
                $subform = $form->getSubForm($photo->getGuid());
                $values = $subform->getValues();

                if (isset($_POST['changedate']) && isset($photo->date_taken) && isset($_POST['year-' . $photo->getIdentity()]) && isset($_POST['month-' . $photo->getIdentity()]) && isset($_POST['day-' . $photo->getIdentity()])) {
                    $date[$photo->getIdentity()] = $_POST['year-' . $photo->getIdentity()] . '-' . $_POST['month-' . $photo->getIdentity()] . '-' . $_POST['day-' . $photo->getIdentity()];
                    $photo->date_taken = $date[$photo->getIdentity()];
                    $photo->save();
                }
                $values = $values[$photo->getGuid()];
                unset($values['photo_id']);
                if (isset($values['delete']) && $values['delete'] == '1') {
                    $photo->delete();
                } else if (!empty($values['move'])) {
                    $nextPhoto = $photo->getNextPhoto();

//          $old_album_id = $photo->album_id;
                    $photo->album_id = $values['move'];
                    $photo->save();



                    $viewer = Engine_Api::_()->user()->getViewer();
                    if (($viewer->level_id == 1) && !$photo->getOwner()->isSelf($viewer)) {
                        $photo->owner_id = $viewer->getIdentity();
                        $photo->save();
                    }

                    // Change album cover if necessary
                    if (($nextPhoto instanceof Sitealbum_Model_Photo) &&
                            (int) $album->photo_id == (int) $photo->getIdentity()) {
                        $album->photo_id = $nextPhoto->getIdentity();
                        $album->save();
                    }

                    // Changes photos_count
                    $album->photos_count = $album->photos_count - 1;
                    $album->save();
                    $movingIntoAlbum = Engine_Api::_()->getItem('album', $values['move']);
                    $movingIntoAlbum->photos_count = $movingIntoAlbum->photos_count + 1;
                    $movingIntoAlbum->save();

                    // Remove activity attachments for this photo
                    Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($photo);
                } else {
                    $photo->setFromArray($values);
                    $photo->save();
                }
            }

            $select = $photoTable->getPhotoSelect(array('album_id' => $album->album_id));
            $photoResults = $photoTable->fetchAll($select);
            foreach ($photoResults as $photo) {

                if (isset($photo->location) && $album->location == $photo->location) {
                    continue;
                }

                if ($album->location) {
                    $seaoLocationId = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($album->location, '', 'album_photo', $photo->photo_id);
                    if (isset($photo->seao_locationid) && isset($photo->location)) {
                        $photo->seao_locationid = $seaoLocationId;
                        $photo->location = $album->location;
                        $photo->save();
                    }
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'slug' => $album->getSlug(), 'album_id' => $album->album_id), 'sitealbum_entry_view', true);
    }

    //ACTION FOR ORDERING PHOTOS BY DRAG AND DROP
    public function orderAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject('album')->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            return;

        $album = Engine_Api::_()->core()->getSubject();

        $order = $this->_getParam('order');
        if (!$order || !is_array($order)) {
            $this->view->status = false;
            return;
        }

        // Get a list of all photos in this album, by order
        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        $currentOrder = $photoTable->select()
                ->from($photoTable, 'photo_id')
                ->where('album_id = ?', $album->getIdentity())
                ->order('order ASC')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN)
        ;

        // Find the starting point?
        $start = null;
        $end = null;
        for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
            if (in_array($currentOrder[$i], $order)) {
                $start = $i;
                $end = $i + count($order);
                break;
            }
        }

        if (null === $start || null === $end) {
            $this->view->status = false;
            return;
        }
        $photo_id = 0;
        for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
            if ($i >= $start && $i <= $end) {
                if (isset($order[$i - $start]))
                    $photo_id = $order[$i - $start];
            } else {
                if (isset($currentOrder[$i]))
                    $photo_id = $currentOrder[$i];
            }
            $photoTable->update(array(
                'order' => $i,
                    ), array(
                'photo_id = ?' => $photo_id,
            ));
        }

        $this->view->status = true;
    }

    //ACTION FOR EDITING ALBUM
    public function editAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject('album')->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            return;

        // Render
        $this->_helper->content->setEnabled();

        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitealbum_main');

        // Hack navigation
        foreach ($navigation->getPages() as $page) {
            if ($page->route != 'sitealbum_general' || $page->action != 'manage')
                continue;
            $page->active = true;
        }

        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitealbum')->defaultProfileId();

        // Prepare data
        $album = Engine_Api::_()->core()->getSubject();
        $this->view->category_id = $album->category_id;
        $this->view->subcategory_id = $album->subcategory_id;

        //GET PROFILE MAPPING ID
        $categoryIds = array();
        $categoryIds[] = $this->view->category_id;
        $categoryIds[] = $this->view->subcategory_id;
        $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'sitealbum')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));

        // Make form
        $this->view->form = $form = new Sitealbum_Form_Album_Edit(array('defaultProfileId' => $defaultProfileId, 'item' => $album));

        if (!$this->getRequest()->isPost()) {
            $form->populate($album->toArray());

            //prepare tags
            $sitealbumTags = $album->tags()->getTagMaps();
            $tagString = '';

            foreach ($sitealbumTags as $tagmap) {
                if ($tagString != '')
                    $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();
            }

            if (isset($form->tags))
                $form->tags->setValue($tagString);

            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $role) {
                if (1 === $auth->isAllowed($album, $role, 'view') && isset($form->auth_view)) {
                    $form->auth_view->setValue($role);
                }
                if (1 === $auth->isAllowed($album, $role, 'comment') && isset($form->auth_comment)) {
                    $form->auth_comment->setValue($role);
                }
                if (1 === $auth->isAllowed($album, $role, 'tag') && isset($form->auth_tag)) {
                    $form->auth_tag->setValue($role);
                }
            }

            //NETWORK BASE ALBUM
            if (Engine_Api::_()->sitealbum()->albumBaseNetworkEnable()) {
                if (!empty($album->networks_privacy)) {
                    $form->networks_privacy->setValue(explode(',', $album->networks_privacy));
                } else {
                    $form->networks_privacy->setValue(array(0));
                }
            }

            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }

        // Process
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();
            
            if (!isset($values['password'])) {
                $values['password'] =  '';
            } elseif(isset($values['password']) && $values['password']) {
                $values['search'] = 0;
            }
            
            //NETWORK BASE ALBUM
            if (Engine_Api::_()->sitealbum()->albumBaseNetworkEnable()) {
                if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                    if (in_array(0, $values['networks_privacy'])) {
                        $values['networks_privacy'] = new Zend_Db_Expr('NULL');
                        $form->networks_privacy->setValue(array(0));
                    } else {
                        $values['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
                    }
                }
            }

            //SAVE TAGS
            $tags = '';
            if (isset($values['tags'])) {
                $tags = preg_split('/[,]+/', $values['tags']);
                $tags = array_filter(array_map("trim", $tags));
            }

            //GET VIEWER
            $viewer = Engine_Api::_()->user()->getViewer();

            if ($tags)
                $album->tags()->setTagMaps($viewer, $tags);

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1)) {
                //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                $customfieldform = $form->getSubForm('fields');
                $customfieldform->setItem($album);
                $customfieldform->saveValues();
                if ($customfieldform->getElement('submit')) {
                    $customfieldform->removeElement('submit');
                }

                //IF MAPPING HAS BEEN CHANGED OF CATEGORY THEN DELETE CORRESPONDENCE DATA FROM VALUES AND SEARCH TABLE
                if (isset($values['category_id']) && !empty($values['category_id'])) {
                    $categoryIds = array();
                    $categoryIds[] = $album->category_id;
                    $categoryIds[] = $album->subcategory_id;
                    $album->profile_type = Engine_Api::_()->getDbtable('categories', 'sitealbum')->getProfileType(array('categoryIds' => $categoryIds, 'category_id' => 0));
                    if ($album->profile_type != $previous_profile_type) {

                        $fieldvalueTable = Engine_Api::_()->fields()->getTable('album', 'values');
                        $fieldvalueTable->delete(array('item_id = ?' => $album->album_id));

                        Engine_Api::_()->fields()->getTable('album', 'search')->delete(array(
                            'item_id = ?' => $album->album_id,
                        ));

                        if (!empty($album->profile_type) && !empty($previous_profile_type)) {
                            //PUT NEW PROFILE TYPE
                            $fieldvalueTable->insert(array(
                                'item_id' => $album->album_id,
                                'field_id' => $defaultProfileId,
                                'index' => 0,
                                'value' => $album->profile_type,
                            ));
                        }
                    }
                    $album->save();
                }
            }
            $album->setFromArray($values);
            $album->save();

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

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($album) as $action) {
                $actionTable->resetActivityBindings($action);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'slug' => $album->getSlug(), 'album_id' => $album->album_id), 'sitealbum_entry_view', true);
    }

    //ACTION FOR DELETING ALBUM
    public function deleteAction() {

        $album = Engine_Api::_()->getItem('album', $this->getRequest()->getParam('album_id'));

        if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'delete')->isValid())
            return;

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');

        $this->view->form = $form = new Sitealbum_Form_Album_Delete();

        if (!$album) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Album doesn't exists or not authorized to delete");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = $album->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $album->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Album has been deleted.');
        return $this->_forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitealbum_general', true),
                    'messages' => Array($this->view->message)
        ));
    }

    public function composeUploadAction() {
        if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
            $this->_redirect('login');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
            return;
        }

        $response = Seaocore_Service_FancyUpload::upload();
        if (!empty($response['error'])) {
            $this->view->status = false;
            return;
        }

        if (empty($response['path']) || ( $response['type'] != 'html4' && empty(file_exists($response['path'])) ) ) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No File');
            return;
        }

        // Get album
        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getDbtable('albums', 'sitealbum');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $type = $this->_getParam('type', 'wall', 'comment');

            if (empty($type))
                $type = 'wall';

            $album = $table->getSpecialAlbum($viewer, $type);

            $photoTable = Engine_Api::_()->getDbtable('photos', 'sitealbum');
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
            ));
            $photo->save();
            if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                Engine_Api::_()->sitemobile()->autoRotationImage($response['path']);
            }
            $photo->setPhoto($response['path']);

            if ($type == 'message') {
                $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
            }

            $photo->order = $photo->photo_id;
            $photo->album_id = $album->album_id;
            $photo->save();

            if (!$album->photo_id) {
                $album->photo_id = $photo->getIdentity();
                $album->save();
            }

            if ($type != 'message') {
                // Authorizations
                $auth = Engine_Api::_()->authorization()->context;
                $auth->setAllowed($photo, 'everyone', 'view', true);
                $auth->setAllowed($photo, 'everyone', 'comment', true);
            }

            $db->commit();
            $this->view->clear_cache = true;
            $this->view->status = true;
            $this->view->photo_id = $photo->photo_id;
            $this->view->album_id = $album->album_id;
            $this->view->src = $photo->getPhotoUrl();
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Photo saved successfully.');

            if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') && !Engine_Api::_()->hasModuleBootstrap('mobi')) {
                //Attach photo preview on status box (Activity Feed)
                $requesttype = $this->_getParam('feedphoto', false);
                if ($requesttype) {
                    echo '<img src="' . $photo->getPhotoUrl() . '" id="compose-photo-preview-image" class="compose-preview-image"><div id="advfeed-photo"><input type="hidden" name="attachment[photo_id]" value="' . $photo->photo_id . '"><input type="hidden" name="attachment[type]" value="photo"></div>';
                    exit();
                }
            }
        } catch (Exception $e) {
            $db->rollBack();
            //throw $e;
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_($e->getMessage());
        }
        @unlink($response['path']);
    }
    

}
