<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PlaylistController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_PlaylistController extends Seaocore_Controller_Action_Standard {

    public function init() {
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) {
            return $this->_forward('notfound', 'error', 'core');
        }
        //SET THE SUBJECT
        if (0 !== ($playlist_id = (int) $this->_getParam('playlist_id')) &&
                null !== ($playlist = Engine_Api::_()->getItem('sitevideo_playlist', $playlist_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($playlist);
        } else if (0 !== ($playlist_map_id = (int) $this->_getParam('playlist_map_id')) &&
                null !== ($playlistMap = Engine_Api::_()->getItem('sitevideo_playlistmap', $playlist_map_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($playlistMap);
        }
    }

    //THIS ACTION USED TO CREATE A PLAYLIST
    public function createAction() {

        //Checking for "Playlist" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity())
            return $this->_forwardCustom('requireauth', 'error', 'core');

        $this->_helper->content
                //->setNoRender()
                ->setEnabled();
        $this->view->form = $form = new Sitevideo_Form_Playlist_Create();
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $sitevideoPlaylist = Zend_Registry::isRegistered('sitevideoPlaylist') ? Zend_Registry::get('sitevideoPlaylist') : null;
        if (empty($sitevideoPlaylist))
            return;

        $values = $form->getValues();
        $values['owner_id'] = $viewer->getIdentity();
        $values['owner_type'] = $viewer->getType();

        //validation for uniqueness of playlist title  , userwise 
        //started
        $validate = new Zend_Validate_Db_RecordExists(array('table' => Engine_Db_Table::getTablePrefix() . 'sitevideo_playlists',
            'field' => 'title'));
        $validate->getSelect()->where('owner_id = ?', $values['owner_id']);
        $validate->getSelect()->where('owner_type = ?', 'user');
        $result = $validate->isValid($values['title']);
        if ($result) {
            $form->getElement('title')->setErrors(array('This title already exists'));
            return;
        }
        //validation for uniqueness of playlist title  , userwise 
        //ended

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $playlist = Engine_Api::_()->getDbtable('playlists', 'sitevideo')->createRow();
            $playlist->setFromArray($values);
            $playlist->save();

            //Playlist thumbnail insertion
            //Started
            $photo = $form->getValue('photo');
            if (!empty($photo)) {
                $playlist->setPhoto($form->photo);
            }
            //Playlist thumbnail insertion
            //Ended
            $db->commit();
        } catch (Exception $ex) {
            
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage', 'tab' => 'playlist'), 'sitevideo_video_general', true);
    }

    /*
     * MY PLAYLIST PAGE ACTION
     */

    public function manageAction() {
        //Checking for "Playlist" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    /*
     * EDIT PLAYLIST
     */

    public function editAction() {
        //Checking for "Playlist" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        if (!$this->_helper->requireUser()->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireSubject()->isValid())
            return;

        //Find the subject model
        $playlist = Engine_Api::_()->core()->getSubject();
        if (!$playlist->canEdit())
            return $this->_forward('requireauth', 'error', 'core');
        $is_admin = $this->_getParam('admin', false);
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $this->view->playlist = $playlist;
        //INITIATE FORM OBJECT
        $this->view->form = $form = new Sitevideo_Form_Playlist_Edit();
        //SET THE FORM VALUES
        $form->getElement('title')->setValue($playlist->title);
        $form->getElement('description')->setValue($playlist->description);
        $form->getElement('privacy')->setValue($playlist->privacy);
        //CHECKING REQUEST FOR POST
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $sitevideoPlaylist = Zend_Registry::isRegistered('sitevideoPlaylist') ? Zend_Registry::get('sitevideoPlaylist') : null;
        if (empty($sitevideoPlaylist))
            return;
        $values = $form->getValues();
        //validation for uniqueness of playlist title  , userwise 
        //started
        $validate = new Zend_Validate_Db_RecordExists(array('table' => Engine_Db_Table::getTablePrefix() . 'sitevideo_playlists',
            'field' => 'title'));
        $validate->getSelect()->where('owner_id = ?', $playlist->owner_id);
        $validate->getSelect()->where('owner_type = ?', $playlist->owner_type);
        $validate->getSelect()->where('playlist_id <> ?', $playlist->playlist_id);
        $result = $validate->isValid($values['title']);
        if ($result) {
            $form->getElement('title')->setErrors(array('This title already exists.'));
            return;
        }
        //validation for uniqueness of playlist title  , userwise 
        //ended

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $playlist->setFromArray($values);
            $playlist->save();

            //Playlist thumbnail insertion
            $photo = $form->getValue('photo');
            if (!empty($photo)) {
                $playlist->setPhoto($form->photo);
            } 
            $db->commit();
        } catch (Exception $ex) {
            echo $ex;
        } 
        
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Playlist has been updated.');
        if ($is_admin)
            return $this->_forward('success', 'utility', 'core', array(
                        'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitevideo', 'controller' => 'manage-playlist'), 'admin_default', true),
                        'messages' => Array($this->view->message)
            ));
        return $this->_forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'browse'), 'sitevideo_playlist_general', true),
                    'messages' => Array($this->view->message)
        ));
    }

    //ACTION FOR DELETING VIDEO FROM PLAYLIST

    public function deleteAction() {
        //Checking for "Playlist" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireSubject()->isValid())
            return;
        if (!$this->_helper->requireUser()->isValid())
            return;

        //Find the subject model
        $playlist = Engine_Api::_()->core()->getSubject();
        if (!$playlist->canEdit())
            return $this->_forward('requireauth', 'error', 'core');

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $this->view->form = $form = new Sitevideo_Form_Playlist_Delete();
        if (!$playlist) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Playlist doesn't exists or not authorized to delete");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        $db = $playlist->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            // Delete all the mapped video with this playlist
            foreach ($playlist->getPlaylistAllMap() as $map)
                $map->delete();

            //Delete Playlist
            $playlist->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Playlist has been deleted.');
        return $this->_forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'browse'), 'sitevideo_playlist_general', true),
                    'messages' => Array($this->view->message)
        ));
    }

    /*
     * THIS ACTION USED TO PLAY ALL VIDEO'S OF PLAYLIST
     */

    public function playallAction() {

        //Checking for "Playlist" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        $playlist = Engine_Api::_()->core()->getSubject();
        //GET LOGGED IN USER INFORMATION
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if ($viewer_id != $playlist->owner_id && $playlist->privacy == 'private') {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    /*
     * THIS ACTION USED TO VIEW THE PLAYLIST
     */

    public function viewAction() {
        //Checking for "Playlist" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        $playlist = Engine_Api::_()->core()->getSubject();
        //GET LOGGED IN USER INFORMATION
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if ($viewer_id != $playlist->owner_id && $playlist->privacy == 'private') {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    /*
     * This action used to remove the video from playlist
     */

    public function removeVideoAction() {

        //Checking for "Playlist" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        if (!Engine_Api::_()->core()->hasSubject('sitevideo_playlistmap')) {
            return $this->setNoRender();
        }
        //FIND THE PLAYLIST MAP MODEL
        $playlistMap = Engine_Api::_()->core()->getSubject();
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $this->view->form = $form = new Sitevideo_Form_Playlistmap_Delete();
        if (!$playlistMap) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Playlist doesn't exists or not authorized to delete");
            return;
        }
        // CHECKING FOR POST REQUEST
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        $db = $playlistMap->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            //REDUCE THE VIDEO COUNT BY 1 FROM PLAYLIST TABLE WHEN VIDEO IS REMOVED FROM PLAYLIST
            $playlist = Engine_Api::_()->getItem('sitevideo_playlist', $playlistMap->playlist_id);
            if ($playlist) {
                $playlist->video_count = ($playlist->video_count) - 1;
                $playlist->save();
            }
            //DELETE THE VIDEO MAP RECORD
            $playlistMap->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video has been removed from playlist.');
        return $this->_forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view', 'playlist_id' => $playlist->playlist_id), 'sitevideo_playlist_view', true),
                    'messages' => Array($this->view->message)
        ));
    }

    /*
     * This action used to add a video into playlist
     */

    public function addToPlaylistAction() {

        $message = Zend_Registry::get('Zend_Translate')->_("Video is added to playlist.");
        $video_id = $this->_getParam('video_id');
        $playlist_id = $this->_getParam('playlist_id');
        $videoModel = Engine_Api::_()->getItem('sitevideo_video', $video_id);
        if ($videoModel && ($videoModel->checkType('embedcode') || $videoModel->checkType('instagram') || $videoModel->checkType('twitter') || $videoModel->checkType('pinterest')))
            $message = Zend_Registry::get('Zend_Translate')->_("Video is added to playlist.This video will not play automatically in a playlist.");
        $table = Engine_Api::_()->getDbtable('playlistmaps', 'sitevideo');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {

            $playlistMaps = new Sitevideo_Model_DbTable_Playlistmaps();
            // CHECKING FOR VIDEO EXISTS IN PLAYLIST
            $playlistMapModel = $playlistMaps->fetchRow($playlistMaps->select()
                            ->where('playlist_id = ?', $playlist_id)
                            ->where('video_id = ?', $video_id));
            //IF VIDEO DOES NOT EXISTS IN PLAYLIST THEN ADD VIDEO INTO PLAYLIST
            if (!$playlistMapModel) {
                $playlistmapRow = $table->createRow();
                $playlistmapRow->video_id = $video_id;
                $playlistmapRow->playlist_id = $playlist_id;
                $playlistmapRow->save();
                $playlist = $playlistmapRow->getPlaylistDetail();
                //ADD THE VIDEO COUNT BY 1 TO PLAYLIST TABLE WHEN VIDEO IS ADDED TO PLAYLIST
                if ($playlist) {
                    $playlist->video_count = ($playlist->video_count) + 1;
                    $playlist->save();
                }
            } else
                $message = Zend_Registry::get('Zend_Translate')->_("This video is already added to playlist.");
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $data = array();
        $data[] = array(
            'message' => $message,
        );
        return $this->_helper->json($data);
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }

    /*
     * THIS ACTION USED TO REMOVE A VIDEO FROM PLAYLIST
     */

    public function removeFromPlaylistAction() {

        $message = Zend_Registry::get('Zend_Translate')->_("Video has been removed from playlist.");
        //FETCHING THE VIDEO AND PLAYLIST ID
        $video_id = $this->_getParam('video_id');
        $playlist_id = $this->_getParam('playlist_id');
        $table = Engine_Api::_()->getDbtable('playlistmaps', 'sitevideo');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {

            $playlistMaps = new Sitevideo_Model_DbTable_Playlistmaps();
            //CHECKING FOR VIDEO EXISTENCE IN PLAYLIST
            $playlistMapModel = $playlistMaps->fetchRow($playlistMaps->select()
                            ->where('playlist_id = ?', $playlist_id)
                            ->where('video_id = ?', $video_id));
            //IF VIDEO EXISTS THEN REMOVE FROM PLAYLIST
            if ($playlistMapModel) {

                $playlist = $playlistMapModel->getPlaylistDetail();
                //REDUCE THE VIDEO COUNT BY 1 FROM PLAYLIST TABLE WHEN VIDEO IS REMOVED FROM PLAYLIST
                if ($playlist) {
                    $playlist->video_count = ($playlist->video_count) - 1;
                    $playlist->save();
                }
                $playlistMapModel->delete();
            } else
                $message = Zend_Registry::get('Zend_Translate')->_("This video is already removed from playlist.");
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $data = array();
        $data[] = array(
            'message' => $message,
        );
        return $this->_helper->json($data);
    }

    public function browseAction() {


        $this->_helper->content->setNoRender()->setEnabled();
    }

}
