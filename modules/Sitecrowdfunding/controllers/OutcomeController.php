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
class Sitecrowdfunding_OutcomeController extends Core_Controller_Action_Standard
{

    public function manageOutcomeAction() {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');

        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_editoutcome';

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

        $this->view->outcomes =  $outcomes = Engine_Api::_()->getDbtable('outcomes','sitecrowdfunding')->getAllOutcomesByProjectId($project_id);

    }

    public function addOutcomeAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID AND OBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitecrowdfunding_project', null, "view")->isValid())
            return;

        //IF PROJECT IS NOT EXIST
        if (empty($project)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //SET PROJECT SUBJECT
        Engine_Api::_()->core()->setSubject($project);

        //SELECTED TAB
        $this->view->TabActive = "sitecrowdfunding_dashboard_editoutcome";
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Outcome(array('project_id'=> $project_id));
        $form->removeDecorator('title');
        $form->removeDecorator('description');

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            if (empty($values))
                return;

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_outcome');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $outcome = $table->createRow();

                $inputs = array(
                    'title' => $values['title'],
                    'description' => $values['description'],
                    'project_id' => $project_id,
                    'user_id' => $viewer_id
                );
                $outcome->setFromArray($inputs);
                $outcome->save();
                $db->commit();

            }catch (Exception $e){
                $db->rollBack();
                throw $e;
            }
            return $this->_helper->redirector->gotoRoute(array('controller' => 'outcome', 'action' => 'manage-outcome', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
        }

    }

    public function editOutcomeAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID AND OBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->outcome_id = $outcome_id = $this->_getParam('outcome_id', null);
        $this->view->outcome = $outcome = Engine_Api::_()->getItem('sitecrowdfunding_outcome', $outcome_id);
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

        //SELECTED TAB
        $this->view->TabActive = "sitecrowdfunding_dashboard_editoutcome";
        $form = new Sitecrowdfunding_Form_Project_Outcome(array('project_id'=> $project_id));
        $form->removeDecorator('title');
        $form->removeDecorator('description');
        $form->setTitle('Edit Outcome');
        $form->setDescription('Edit the Outcome details using below form.');
        $form->execute->setLabel('Save changes');
        $outcomeAsArray = $outcome->toArray();
        $form->populate($outcomeAsArray);

        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            if (empty($values))
                return;

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_outcome');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $outcomeModel = $outcome;

                $inputs = array(
                    'title' => $values['title'],
                    'description' => $values['description'],
                );

                $outcomeModel->setFromArray($inputs);
                $outcomeModel->save();
                $db->commit();

            }catch (Exception $e){
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('controller' => 'outcome', 'action' => 'manage-outcome', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);

        }
    }

    public function deleteOutcomeAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->outcome_id = $outcome_id = $this->_getParam('outcome_id', null);

        // Send to view script if not POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if(!empty($outcome_id)){

            $item  = Engine_Api::_()->getItem('sitecrowdfunding_outcome', $outcome_id);

            $item->delete();

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Outcome has been remove successfully.'))
            ));

        }
    }

    public function settingsAction()
    {
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // get details
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        // todo: Allow edit for project admins:
        $isProjectAdmin = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->checkLeader($project);

        // todo: Allow edit for organization admins: get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        $sitepage = Engine_Api::_()->getItem('sitepage_page',$parentOrganization['page_id']);
        $editPrivacyOrganization = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        if ($isProjectAdmin != 1 && empty($editPrivacyOrganization)) {
            if (!$this->_helper->requireAuth()->setAuthParams($project, $viewer, "edit")->isValid()) {
                return;
            }
        }


        // Bind Forms
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_OutcomeSettings(array(
            'project_id' => $project_id
        ));

        $form->populate(
            array(
                'no_of_jobs' => $project->no_of_jobs,
                'desire_desc' => $project->desire_desc,
                'help_desc' => $project->help_desc,
            )
        );

        //FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();

            if (empty($values))
                return;

            try {
                $project->no_of_jobs = $values['no_of_jobs'];
                $project->desire_desc = $values['desire_desc'];
                $project->help_desc = $values['help_desc'];
                $project->save();
            } catch (Exception $e) {

            }

        }

    }

}