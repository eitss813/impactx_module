<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_StatusController extends Core_Controller_Action_Standard
{
    public function editStatusAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_editstatus';
        //IF THERE IS NO PROJECT.
        if (empty($project)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        if (!$project->isOpen()) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        Engine_Api::_()->core()->setSubject($project);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Status(array('project_id' => $project_id));

        $this->view->showform = false;

        if($project->state === 'draft' || $project->state === 'rejected'){
            $this->view->showform = true;
        }

        $form->populate(array(
            'state' => $project->state,
            'is_fund_raisable' => $project->is_fund_raisable
        ));

        $this->view->adminnotes = $adminnotes = Engine_Api::_()->getDbTable('adminnotes','sitecrowdfunding')->getAllAdminNotesByProjectId($project_id,0);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();
            if (empty($values))
                return;

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $projectModel = $project;
                $inputs = array(
                    'state' => $values['state'],
                    'is_fund_raisable' => $values['is_fund_raisable'],
                );
                if($project->start_date == null){
                    $project->start_date = date('Y-m-d H:i:s');
                }
                $projectModel->setFromArray($inputs);
                $projectModel->save();
                $db->commit();
            }catch (Exception $e){
                $db->rollBack();
                throw $e;
            }
            return $this->_helper->redirector->gotoRoute(array('controller' => 'status', 'action' => 'edit-status', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
        }
        
        
    }

    public function submitAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //IF THERE IS NO PROJECT.
        if (empty($project)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        if (!$project->isOpen()) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        Engine_Api::_()->core()->setSubject($project);

        // Send to view script if not POST
        if (!$this->getRequest()->isPost()) {
            return;
        }


        $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $projectModel = $project;
            $inputs = array(
                'state'  => 'submitted',
                'start_date' => date('Y-m-d H:i:s')
            );
            $projectModel->setFromArray($inputs);
            $projectModel->save();
            $db->commit();

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Submitted for approval.'))
            ));

        }catch (Exception $e){
            $db->rollBack();
            throw $e;
        }

    }

    public function submitFundingAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //IF THERE IS NO PROJECT.
        if (empty($project)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        if (!$project->isOpen()) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        Engine_Api::_()->core()->setSubject($project);

        // Send to view script if not POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $projectModel = $project;
            $inputs = array(
                'funding_state'  => 'submitted',
            );

            $projectModel->setFromArray($inputs);
            $projectModel->save();
            $db->commit();

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Submitted for funding approval.'))
            ));

        }catch (Exception $e){
            $db->rollBack();
            throw $e;

        }

    }

    public function viewNotesAction(){

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->is_funding = $is_funding = $this->_getParam('is_funding');
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        //IF THERE IS NO PROJECT.
        if (empty($project)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        if (!$project->isOpen()) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        Engine_Api::_()->core()->setSubject($project);

        $this->view->adminnotes = $adminnotes = Engine_Api::_()->getDbTable('adminnotes','sitecrowdfunding')->getAllAdminNotesByProjectId($project_id, $is_funding);
    }
}