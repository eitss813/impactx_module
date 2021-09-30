<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AlbumController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_AlbumController extends Core_Controller_Action_Standard {

    //ACTION FOR EDIT PHOTO
    public function editphotosAction() {
        //LOGGEND IN USER CAN EDIT PHOTO
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID AND OBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $change_url = $this->_getParam('change_url', 0);
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, "view")->isValid())
            return;

        //IF PROJECT IS NOT EXIST
        if (empty($project)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //SET PROJECT SUBJECT
        Engine_Api::_()->core()->setSubject($project);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            $allowPhotoUpload = Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "photo") ? 1 : 0;
        } else { 
            $allowPhotoUpload = Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "photo");
        }
        if (empty($allowPhotoUpload)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //AUTHORIZATION CHECK
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
            return;
        }

        //SELECTED TAB
        $this->view->TabActive = "photo";

        //PREPARE DATA
        $this->view->album = $album = $project->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());
        $this->view->count = count($paginator);
//MAKE FORM
        $this->view->form = $form = new Sitecrowdfunding_Form_Album_Photos();
        foreach ($paginator as $photo) {
            $subform = new Sitecrowdfunding_Form_Photo_SubEdit(array('elementsBelongTo' => $photo->getGuid()));
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

        $table = Engine_Api::_()->getDbTable('albums', 'sitecrowdfunding');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $values = $form->getValues();
            if (!empty($values['cover']) && $project->photo_id != $values['cover']) {

                $album->photo_id = $values['cover'];
                $album->save();
                $project->photo_id = $values['cover'];
                $project->save();
                $project->updateAllCoverPhotos();
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

            if (!empty($project->photo_id)) {
                $photoTable = Engine_Api::_()->getItemTable('sitecrowdfunding_photo');
                $order = $photoTable->select()
                        ->from($photoTable->info('name'), array('order'))
                        ->where('project_id = ?', $project->project_id)
                        ->group('photo_id')
                        ->order('order ASC')
                        ->limit(1)
                        ->query()
                        ->fetchColumn();

                $photoTable->update(array('order' => $order - 1), array('file_id = ?' => $project->photo_id));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if (empty($change_url)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'project_id' => $album->project_id), "sitecrowdfunding_albumspecific", true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'project_id' => $album->project_id), "sitecrowdfunding_dashboard", true);
        }
    }

    public function orderAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!$this->_helper->requireSubject('sitecrowdfunding_project')->isValid())
            return;

        $subject = Engine_Api::_()->core()->getSubject();

        $order = $this->_getParam('order');
        if (!$order) {
            $this->view->status = false;
            return;
        }

        $album = $subject->getSingletonAlbum();

        // Get a list of all photos in this album, by order
        $photoTable = Engine_Api::_()->getItemTable('sitecrowdfunding_photo');
        $currentOrder = $photoTable->select()
                ->from($photoTable, 'photo_id')
                ->where('album_id = ?', $album->getIdentity())
                ->where('project_id = ?', $subject->getIdentity())
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
