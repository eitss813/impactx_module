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
class Sitecrowdfunding_OutputController extends Core_Controller_Action_Standard
{

    public function manageOutputAction() {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');

        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_editoutput';

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

        $this->view->outputs =  $output = Engine_Api::_()->getDbtable('outputs','sitecrowdfunding')->getAllOutputsByProjectId($project_id);

    }

    public function addOutputAction() {

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
        $this->view->TabActive = "sitecrowdfunding_dashboard_editoutput";
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Output(array('project_id'=> $project_id));
        $form->removeDecorator('title');
        $form->removeDecorator('description');

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            if (empty($values))
                return;

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_output');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $output = $table->createRow();

                $inputs = array(
                    'title' => $values['title'],
                    'description' => $values['description'],
                    'project_id' => $project_id,
                    'user_id' => $viewer_id
                );
                $output->setFromArray($inputs);
                $output->save();
                $db->commit();

            }catch (Exception $e){
                $db->rollBack();
                throw $e;
            }
            return $this->_helper->redirector->gotoRoute(array('controller' => 'output', 'action' => 'manage-output', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
        }

    }

    public function editOutputAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID AND OBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->output_id = $output_id = $this->_getParam('output_id', null);
        $this->view->output = $output = Engine_Api::_()->getItem('sitecrowdfunding_output', $output_id);
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
        $this->view->TabActive = "sitecrowdfunding_dashboard_editoutput";
        $form = new Sitecrowdfunding_Form_Project_Output(array('project_id'=> $project_id));
        $form->removeDecorator('title');
        $form->removeDecorator('description');
        $form->setTitle('Edit Output');
        $form->setDescription('Edit the Output details using below form.');
        $form->execute->setLabel('Save changes');
        $outputAsArray = $output->toArray();
        $form->populate($outputAsArray);

        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            if (empty($values))
                return;

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_output');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $outputModel = $output;

                $inputs = array(
                    'title' => $values['title'],
                    'description' => $values['description']
                );

                $outputModel->setFromArray($inputs);
                $outputModel->save();
                $db->commit();

            }catch (Exception $e){
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('controller' => 'output', 'action' => 'manage-output', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);

        }
    }

    public function deleteOutputAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->output_id = $output_id = $this->_getParam('output_id', null);

        // Send to view script if not POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if(!empty($output_id)){

            $item  = Engine_Api::_()->getItem('sitecrowdfunding_output', $output_id);

            $item->delete();

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Output has been remove successfully.'))
            ));

        }
    }

}