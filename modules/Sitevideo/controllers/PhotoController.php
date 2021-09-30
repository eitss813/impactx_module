<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_PhotoController extends Seaocore_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, "view")->isValid())
            return;

        //SET SUBJECT
        if (!Engine_Api::_()->core()->hasSubject()) {

            if (0 != ($photo_id = (int) $this->_getParam('photo_id')) &&
                    null != ($photo = Engine_Api::_()->getItem('sitevideo_photo', $photo_id))) {
                Engine_Api::_()->core()->setSubject($photo);
            } else if (0 != ($channel_id = (int) $this->_getParam('channel_id')) &&
                    null != ($channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id))) {
                Engine_Api::_()->core()->setSubject($channel);
            }
        }
        $this->_helper->requireUser->addActionRequires(array(
            'upload',
            'upload-photo',
            'edit',
        ));

        $this->_helper->requireSubject->setActionRequireTypes(array(
            'channel' => 'sitevideo_channel',
            'upload' => 'sitevideo_channel',
            'view' => 'sitevideo_photo',
            'edit' => 'sitevideo_photo',
        ));
    }

    //ACTION FOR UPLOAD PHOTO
    public function uploadAction() {
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET CHANNEL
        $this->view->channel_id = $channel_id = $this->_getParam('channel_id');

        $this->view->channel = $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        $this->view->can_edit = $channel->authorization()->isAllowed($viewer, "edit");

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("siteevideo_main");

        $this->view->allowed_upload_photo = $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($channel, $viewer, "photo");

        if (!$this->getRequest()->isPost()) {
            if (empty($this->view->allowed_upload_photo)) {
                return $this->_forwardCustom('requireauth', 'error', 'core');
            }
        }

        //AUTHORIZATION CHECK
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $this->view->allowed_upload_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
        //SELECTED TAB
        $this->view->TabActive = "photo";
        if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
            return $this->_forwardCustom('upload-photo', null, null, array('format' => 'json', 'channel_id' => (int) $channel->getIdentity()));
        }
        //GET ALBUM
        $album = $channel->getSingletonAlbum();

        //MAKE FORM
        $this->view->form = $form = new Sitevideo_Form_Photo_Upload();
        $form->file->setAttrib('data', array('channel_id' => $channel->getIdentity()));
        $this->view->tab_id = $content_id = $this->_getParam('content_id');
        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        //PROCESS
        $table = Engine_Api::_()->getItemTable('sitevideo_photo');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {

            $values = $form->getValues();
            $params = array(
                'channel_id' => $channel->getIdentity(),
                'user_id' => $viewer->getIdentity(),
            );

            //ADD ACTION AND ATTACHMENTS
            $count = count($values['file']);
            $api = Engine_Api::_()->getDbtable('actions', 'seaocore');
            $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $channel, Engine_Api::_()->sitevideo()->getActivtyFeedType($channel, 'sitevideo_photo_upload'), null, array('count' => count($values['file']), 'title' => $channel->title));
            $count = 0;
            foreach ($values['file'] as $photo_id) {
                $photo = Engine_Api::_()->getItem("sitevideo_photo", $photo_id);

                if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
                    continue;

                $photo->collection_id = $album->album_id;
                $photo->album_id = $album->album_id;
                $photo->save();
                if ($channel->file_id == 0) {
                    $channel->file_id = $photo->file_id;
                    $channel->save();
                }
                if ($action instanceof Activity_Model_Action && $count < 8) {
                    $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                }
                $count++;
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if ($this->view->can_edit) {
            return $this->_gotoRouteCustom(array('action' => 'editphotos', 'channel_id' => $album->channel_id), "sitevideo_albumspecific", true);
        } else {
            return $this->_gotoRouteCustom(array('channel_id' => $album->channel_id, 'slug' => $channel->getSlug(), 'tab' => $content_id), "sitevideo_entry_view", true);
        }
    }

    //ACTION FOR UPLOAD PHOTO
    public function uploadPhotoAction() {

        //GET CHANNEL
        $channel = Engine_Api::_()->getItem('sitevideo_channel', (int) $this->_getParam('channel_id'));

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
        $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitevideo');
        $db = $tablePhoto->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $photo_id = $channel->setPhoto($response['path'], array('setChannelMainPhoto' => false, 'return' => 'photo'))->photo_id;
            $this->view->status = true;
            // $this->view->name = $_FILES['Filedata']['name'];
            $this->view->photo_id = $photo_id;
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_($e->getMessage());
            return;
        }
        @unlink($response['path']);
    }

    //ACTION FOR PHOTO DELETE
    public function removeAction() {

        //GET PHOTO ID AND ITEM
        $photo_id = (int) $this->_getParam('photo_id');
        $photo = Engine_Api::_()->getItem('sitevideo_photo', $photo_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        $canEdit = $photo->canEdit();
        if (!$canEdit) {
            return;
        }

        //GET CHANNEL
        $channel = $photo->getParent('sitevideo_channel');

        $isajax = (int) $this->_getParam('isajax');
        if ($isajax) {
            $db = Engine_Api::_()->getDbTable('photos', 'sitevideo')->getAdapter();
            $db->beginTransaction();

            try {
                $photo->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }

        //MAKE FORM
        $this->view->form = $form = new Sitevideo_Form_Photo_Delete();

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            $form->populate($photo->toArray());
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = Engine_Api::_()->getDbTable('photos', 'sitevideo')->getAdapter();
        $db->beginTransaction();

        try {
            $photo->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forwardCustom('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted')),
                    'layout' => 'default-simple',
                    'parentRedirect' => $channel->getHref(),
                    'closeSmoothbox' => true,
        ));
    }

    //ACTION FOR VIEWING THE PHOTO
    public function viewAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET PHOTOS
        $this->view->image = $photo = Engine_Api::_()->core()->getSubject();
        //GET ALBUM DETAILS
        $this->view->album = $photo->getCollection();
        //GET SETTINGS
        $this->view->canEdit = $photo->canEdit();

        if (!$viewer || !$viewer_id || $photo->user_id != $viewer->getIdentity()) {
            $photo->view_count = new Zend_Db_Expr('view_count + 1');
            $photo->save();
        }

        $this->view->enablePinit = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.pinit', 0);
    }

}
