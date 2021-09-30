<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_DashboardController extends Core_Controller_Action_Standard {

    //ACTION FOR CHANING THE PHOTO
    public function changePhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $sitevideoDashboard = Zend_Registry::isRegistered('sitevideoDashboard') ? Zend_Registry::get('sitevideoDashboard') : null;
        if (empty($sitevideoDashboard))
            return;

        //GET EVENT ID
        $this->view->channel_id = $channel_id = $this->_getParam('channel_id');

        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT ITEM
        $this->view->channel = $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);

        //IF THERE IS NO SITEEVENT.
        if (empty($channel)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        Engine_Api::_()->core()->setSubject($channel);

        //SELECTED TAB
        $this->view->TabActive = "profilepicture";

        //CAN EDIT OR NOT
        if (!$this->_helper->requireAuth()->setAuthParams($channel, $viewer, "edit")->isValid()) {
            return;
        }
        //GET FORM
        $this->view->form = $form = new Sitevideo_Form_ChangePhoto();
        //CHECK FORM VALIDATION
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //CHECK FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //UPLOAD PHOTO
        if ($form->Filedata->getValue() !== null) {
            //GET DB
            $db = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getAdapter();
            $db->beginTransaction();
            //PROCESS
            try {
                //SET PHOTO
                $channel->setPhoto($form->Filedata);
                $db->commit();
            } catch (Engine_Image_Adapter_Exception $e) {
                $db->rollBack();
                $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $channel, Engine_Api::_()->sitevideo()->getActivtyFeedType($channel, 'sitevideo_change_photo'));
            $file_id = Engine_Api::_()->getDbtable('photos', 'sitevideo')->getPhotoId($channel_id, $channel->file_id);
            $photo = Engine_Api::_()->getItem('sitevideo_photo', $file_id);
            if ($action != null) {
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
            }
        } else if ($form->getValue('coordinates') !== '') {
            $storage = Engine_Api::_()->storage();
            $iProfile = $storage->get($channel->file_id, 'thumb.profile');
            $iSquare = $storage->get($channel->file_id, 'thumb.icon');
            $pName = $iProfile->getStorageService()->temporary($iProfile);
            $iName = dirname($pName) . '/nis_' . basename($pName);
            list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));
            $image = Engine_Image::factory();
            $image->open($pName)
                    ->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
                    ->write($iName)
                    ->destroy();
            $iSquare->store($iName);
            @unlink($iName);
        }



        if (!empty($channel->file_id)) {
            $photoTable = Engine_Api::_()->getItemTable('sitevideo_photo');
            $order = $photoTable->select()
                    ->from($photoTable->info('name'), array('order'))
                    ->where('channel_id = ?', $channel->channel_id)
                    ->group('photo_id')
                    ->order('order ASC')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();

            $photoTable->update(array('order' => $order - 1), array('file_id = ?' => $channel->file_id));
        }
        $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
    }

    //ACTION FOR REMOVE THE PHOTO
    public function removePhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $sitevideoDashboard = Zend_Registry::isRegistered('sitevideoDashboard') ? Zend_Registry::get('sitevideoDashboard') : null;
        if (empty($sitevideoDashboard))
            return;

        //GET EVENT ID
        $channel_id = $this->_getParam('channel_id');

        //GET EVENT ITEM
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        $viewer = Engine_Api::_()->user()->getViewer();

        //CAN EDIT OR NOT
        if (!$this->_helper->requireAuth()->setAuthParams($channel, $viewer, "edit")->isValid()) {
            return;
        }

        //GET FILE ID
        $file_id = Engine_Api::_()->getDbtable('photos', 'sitevideo')->getPhotoId($channel_id, $channel->file_id);

        //DELETE PHOTO
        if (!empty($file_id)) {
            $photo = Engine_Api::_()->getItem('sitevideo_photo', $file_id);
            $photo->delete();
        }

        //SET PHOTO ID TO ZERO
        $channel->file_id = 0;
        $channel->save();

        return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'channel_id' => $channel_id), "sitevideo_dashboard", true);
    }

    //ACTION FOR META DETAIL INFORMATION
    public function metaDetailAction() {

        //ONLY LOGGED IN USER CAN ADD OVERVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT ID AND OBJECT
        $channel_id = $this->_getParam('channel_id');

        $this->view->channel = $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);

        Engine_Api::_()->core()->setSubject($channel);

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.metakeyword', 1)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$channel->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitevideo_channel', "metakeyword")) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->TabActive = "metadetails";

        //SET FORM
        $this->view->form = $form = new Sitevideo_Form_Metainfo();

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitevideo');

        //POPULATE FORM
        $value['keywords'] = $tableOtherinfo->getColumnValue($channel_id, 'keywords');

        $form->populate($value);

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET FORM VALUES
            $values = $form->getValues();
            $tableOtherinfo->update(array('keywords' => $values['keywords']), array('channel_id = ?' => $channel_id));

            //SHOW SUCCESS MESSAGE
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
    }

    public function videoEditAction() {
        //ONLY LOGGED IN USER CAN ADD VIDEO
        if (!$this->_helper->requireUser()->isValid())
            return;

        $sitevideoDashboard = Zend_Registry::isRegistered('sitevideoDashboard') ? Zend_Registry::get('sitevideoDashboard') : null;
        if (empty($sitevideoDashboard))
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer)
            return $this->_forward('notfound', 'error', 'core');
        //GET EVENT ID AND OBJECT
        $channel_id = $this->_getParam('channel_id');
        $this->view->channel = $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);

        if ($channel->owner_id != $viewer->getIdentity()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$channel->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $allowed_upload_video_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');

        if (empty($allowed_upload_video_video)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        Engine_Api::_()->core()->setSubject($channel);
        $params = array();
        $params['owner_id'] = $viewerId = $viewer->getIdentity();
        $params['channel_id'] = $channel->channel_id;
        $this->view->form = $form = new Sitevideo_Form_Dashboard_Video(array('item' => $params['channel_id']));
        $db = $channel->getTable()->getAdapter();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET FORM VALUES
            $values = $form->getValues();
            $videoIds = explode(',', $values['video_id']);
            // Find all the video of this channel
            $videoMapTable = Engine_Api::_()->getDbtable('videomaps', 'sitevideo');
            $mappedVideos = $videoMapTable->findVideoMaps(array('channel_id' => $channel->channel_id));
            $existingVideoIds = array();
            //start db transaction
            $db->beginTransaction();
            foreach ($mappedVideos as $map) {
                if (!in_array($map->video_id, $videoIds)) {
                    $videoModel = Engine_Api::_()->getItem('sitevideo_video', $map->video_id);
                    $map->delete();
                    $channel->videos_count = $channel->videos_count - 1;
                    $channel->save();
                    if (!empty($videoModel->main_channel_id) && $videoModel->main_channel_id == $channel->channel_id) {
                        $videoModel->main_channel_id = null;
                        $videoModel->save();
                    }
                } else {
                    $existingVideoIds [] = $map->video_id;
                }
            }
            $mapNewVideos = array_diff($videoIds, $existingVideoIds);
            foreach ($mapNewVideos as $video) {
                if (empty($video))
                    continue;
                $videoMapTable = Engine_Api::_()->getDbtable('videomaps', 'sitevideo');
                $videomap = $videoMapTable->createRow();
                $videomap->channel_id = $channel->channel_id;
                $videomap->video_id = $video;
                $videomap->owner_type = 'user';
                $videomap->owner_id = $viewerId;
                $videomap->save();
                $channel->videos_count = $channel->videos_count + 1;
                $channel->save();
            }
            $db->commit();
            return $this->_helper->redirector->gotoRoute(array('action' => 'video-edit', 'channel_id' => $channel->channel_id), 'sitevideo_dashboard', true);
        }
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
    }

    public function myVideosAction() {
        //ONLY LOGGED IN USER CAN ADD OVERVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        $sitevideoDashboard = Zend_Registry::isRegistered('sitevideoDashboard') ? Zend_Registry::get('sitevideoDashboard') : null;
        if (empty($sitevideoDashboard))
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer)
            return $this->_forward('notfound', 'error', 'core');
        //GET EVENT ID AND OBJECT
        $channel_id = $this->_getParam('channel_id');
        $addMyVideo = $this->_getParam('addMyVideo');
        if (!$channel_id)
            return $this->_forward('notfound', 'error', 'core');
        $this->view->videoIds = json_decode($this->_getParam('existingVideos'));
        $this->view->type = $type = $this->_getParam('type');
        $params['owner_id'] = $viewer->getIdentity();
        $params['excludeVideoOwner'] = ($addMyVideo == 0) ? $params['owner_id'] : 0;
        $this->view->channel = $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        switch ($type) {
            case 'favourited' :
                $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getFavouriteVideoPaginator($params);
                break;
            case 'liked' :
                $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getLikedVideoPaginator($params);
                break;
            case 'rated' :
                $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getRatedVideoPaginator($params);
                break;
            case 'uploaded' :
                $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getUploadedVideoPaginator($params);
                break;
            default :
                $paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getFavouriteVideoPaginator($params);
        }
        $this->view->paginator = $paginator;
    }


    public function youtubeVideosAction() {
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer)
            return $this->_forward('notfound', 'error', 'core');
        //GET EVENT ID AND OBJECT
        $channel_id = $this->_getParam('channel_id');
        $this->view->channel = $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);

        $this->view->form = $form = new Sitevideo_Form_YoutubeChannel();
        $params = array();
        $viewerId = $viewer->getIdentity();
        $params['channel_id'] = $channel->channel_id;
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());
        if (!empty($channel->youtube_channel_id)) {
            $form->populate(array(
                'youtube_channel_id' => $channel->youtube_channel_id,
                'pending_video' => $channel->pending_video,
                'youtube_channel_url' => 'https://www.youtube.com/channel/' . $channel->youtube_channel_id
            ));
        }
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            if (isset($values['youtube_channel_id']) && !empty($values['youtube_channel_id'])) {
                $channel->youtube_channel_id = $values['youtube_channel_id'];
                if (!empty($values['pending_video'])) {
                    $pendingVideos = $values['pending_video'] ;
                    if(!empty($channel->pending_video)){
                        $pendingVideos = ($channel->pending_video).",".$pendingVideos;
                    }
                    $channel->pending_video = $pendingVideos;
                    $channel->save();
                    Engine_Api::_()->getDbtable('jobs', 'core')->addJob('Sitevideo_Upload', array(
                        'channel_id' => $channel->channel_id,
                    ));
                }
                $channel->save();
            }
        }
    }

}

