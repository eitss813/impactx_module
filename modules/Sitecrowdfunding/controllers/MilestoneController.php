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
class Sitecrowdfunding_MilestoneController extends Core_Controller_Action_Standard
{

    public function manageMilestoneAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_editmilestone';
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

        $this->view->statusLabels = array("yettostart" => "Yet to start", "inprogress" => "In Progress", 'completed'=> 'Completed');

        $this->view->milestones =  $milestones = Engine_Api::_()->getDbtable('milestones','sitecrowdfunding')->getAllMilestonesByProjectId($project_id);

    }

    public function addMilestoneAction(){


        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID AND OBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id');
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
        $this->view->TabActive = "sitecrowdfunding_dashboard_editmilestone";
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Milestone(array('project_id'=> $project_id));
        $form->removeDecorator('title');
        $form->removeDecorator('description');

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            //START DATE AND END DATE ARE REQUIRED
            if (empty($values['starttime'])) {
                $error = $this->view->translate('Please enter Start Date - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $startDate = date('Y-m-d H:i:s', strtotime($values['starttime']));

            if(!empty($values['endtime'])){
                $endDate = date('Y-m-d H:i:s', strtotime($values['endtime']));
            }else{
                $endDate = null;
            }

            if (empty($values))
                return;

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_milestone');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $milestone = $table->createRow();

                $file_id = null;
                if (!empty($values['photo'])) {
                    $file_id =  $milestone->setLogo($form->photo);
                }

                $inputs = array(
                    'title' => $values['title'],
                    'description' => $values['description'],
                    'question' => $values['question'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status'=> $values['status'],
                    'project_id' => $project_id,
                    'logo' => $file_id
                );
                $milestone->setFromArray($inputs);
                $milestone->save();
                $db->commit();
            }catch (Exception $e){
                $db->rollBack();
                throw $e;
            }
            return $this->_helper->redirector->gotoRoute(array('controller' => 'milestone', 'action' => 'manage-milestone', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
        }
    }

    public function editMilestoneAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID AND OBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->milestone_id = $milestone_id = $this->_getParam('milestone_id', null);
        $this->view->milestone = $milestone = Engine_Api::_()->getItem('sitecrowdfunding_milestone', $milestone_id);
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
        $this->view->TabActive = "sitecrowdfunding_dashboard_editmilestone";
        $form = new Sitecrowdfunding_Form_Project_Milestone(array('project_id'=> $project_id));
        $form->removeDecorator('title');
        $form->removeDecorator('description');
        $form->setTitle('Edit Milestone');
        $form->setDescription('Edit the milestone details using below form.');
        $form->execute->setLabel('Save changes');
        $milestoneAsArray = $milestone->toArray();
        $form->populate($milestoneAsArray);

        if(!empty($milestone->start_date)){
            $form->populate(
                array(
                    'starttime' => date('Y-m-d',strtotime($milestone->start_date))
                )
            );
        }
        if(!empty($milestone->end_date)){
            $form->populate(
                array(
                    'endtime' => date('Y-m-d',strtotime($milestone->end_date))
                )
            );
        }


        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            $startDate = date('Y-m-d H:i:s', strtotime($values['starttime']));

            if(!empty($values['endtime'])){
                $endDate = date('Y-m-d H:i:s', strtotime($values['endtime']));
            }else{
                $endDate = null;
            }



            if (empty($values))
                return;

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_milestone');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $milestoneModel = $milestone;

                $file_id = $milestoneModel->logo;
                if (!empty($values['photo'])) {
                    $file_id =  $milestone->setLogo($form->photo);
                }

                $inputs = array(
                    'title' => $values['title'],
                    'description' => $values['description'],
                    'question' => $values['question'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status'=> $values['status'],
                    'logo' => $file_id
                );
                $milestoneModel->setFromArray($inputs);
                $milestoneModel->save();
                $db->commit();
            }catch (Exception $e){
                $db->rollBack();
                throw $e;
            }
            return $this->_helper->redirector->gotoRoute(array('controller' => 'milestone', 'action' => 'manage-milestone', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
        }
    }

    public function deleteAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->milestone_id = $milestone_id = $this->_getParam('milestone_id', null);

        // Send to view script if not POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if(!empty($milestone_id)){

            $item  = Engine_Api::_()->getItem('sitecrowdfunding_milestone', $milestone_id);

            $item->delete();

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Milestone has been remove successfully.'))
            ));

        }

    }


}