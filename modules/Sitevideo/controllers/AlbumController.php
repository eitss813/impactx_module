<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AlbumController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_AlbumController extends Core_Controller_Action_Standard {

    //ACTION FOR EDIT PHOTO
    public function editphotosAction() {
        //LOGGEND IN USER CAN EDIT PHOTO
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET CHANNEL ID AND OBJECT
        $this->view->channel_id = $channel_id = $this->_getParam('channel_id');
        $change_url = $this->_getParam('change_url', 0);
        $this->view->channel = $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, "view")->isValid())
            return;

        //IF CHANNEL IS NOT EXIST
        if (empty($channel)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //SET CHANNEL SUBJECT
        Engine_Api::_()->core()->setSubject($channel);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $this->view->allowed_upload_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');

        if (!$this->_helper->requireAuth()->setAuthParams($channel, $viewer, "edit")->isValid()) {
            return;
        }

        //SELECTED TAB
        $this->view->TabActive = "photo";

        //PREPARE DATA
        $this->view->album = $album = $channel->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());
        $this->view->count = count($paginator);

        $this->view->upload_photo = 0;
        $this->view->upload_photo = $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($channel, $viewer, "photo");
        if (empty($allowed_upload_photo)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //MAKE FORM
        $this->view->form = $form = new Sitevideo_Form_Album_Photos();
        foreach ($paginator as $photo) {
            $subform = new Sitevideo_Form_Photo_SubEdit(array('elementsBelongTo' => $photo->getGuid()));
            $subform->populate($photo->toArray());
            $form->addSubForm($subform, $photo->getGuid());
            $form->cover->addMultiOption($photo->file_id, $photo->file_id);
        }

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = Engine_Api::_()->getDbTable('albums', 'sitevideo');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $values = $form->getValues();
            if (!empty($values['cover']) && $channel->file_id != $values['cover']) {

                $album->photo_id = $values['cover'];
                $album->save();

                $channel->file_id = $values['cover'];
                $channel->save();
                $channel->updateAllCoverPhotos();
            }



            //PROCESS
            foreach ($paginator as $photo) {

                $subform = $form->getSubForm($photo->getGuid());
                $values = $subform->getValues();
                $values = $values[$photo->getGuid()];

                if (isset($values['delete']) && $values['delete'] == '1') {
                    $photo->delete();
                } else {
                    $photo->setFromArray($values);
                    $photo->save();
                }
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

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if (empty($change_url)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'channel_id' => $album->channel_id), "sitevideo_albumspecific", true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'channel_id' => $album->channel_id), "sitevideo_dashboard", true);
        }
    }

    public function orderAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!$this->_helper->requireSubject('sitevideo_channel')->isValid())
            return;

        $subject = Engine_Api::_()->core()->getSubject();

        $order = $this->_getParam('order');
        if (!$order) {
            $this->view->status = false;
            return;
        }

        $album = $subject->getSingletonAlbum();

        // Get a list of all photos in this album, by order
        $photoTable = Engine_Api::_()->getItemTable('sitevideo_photo');
        $currentOrder = $photoTable->select()
                ->from($photoTable, 'photo_id')
                ->where('album_id = ?', $album->getIdentity())
                ->where('channel_id = ?', $subject->getIdentity())
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

        for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
            if ($i >= $start && $i <= $end) {
                $photo_id = $order[$i - $start];
            } else {
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

}
