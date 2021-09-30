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
class Sitecrowdfunding_GoalsController extends Core_Controller_Action_Standard
{

    public function manageGoalsAction() {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');

        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_editgoals';

       /* $goals_ids = array(
            1 => "End poverty in all its forms everywhere",
            2 => "End hunger, achieve food security and improved nutrition and promote sustainable agriculture",
            3 => "Ensure healthy lives and promote well-being for all at all ages",
            4 => "Ensure inclusive and equitable quality education and promote lifelong learning opportunities for all",
            5 => "Achieve gender equality and empower all women and girls",
            6 => "Ensure availability and sustainable management of water and sanitation for all",
            7 => "Ensure access to affordable, reliable, sustainable and modern energy for all",
            8 => "Promote sustained, inclusive and sustainable economic growth, full and productive employment and decent work for all",
            9 => "Build resilient infrastructure, promote inclusive and sustainable industrialization and foster innovation",
            10 => "Reduce inequality within and among countries",
            11 => "Make cities and human settlements inclusive, safe, resilient and sustainable",
            12 => "Ensure sustainable consumption and production patterns",
            13 => "Take urgent action to combat climate change and its impacts",
            14 => "Conserve and sustainably use the oceans, seas and marine resources for sustainable development",
            15 => "Protect, restore and promote sustainable use of terrestrial ecosystems, sustainably manage forests, combat desertification, and halt and reverse land degradation and halt biodiversity loss",
            16 => "Promote peaceful and inclusive societies for sustainable development, provide access to justice for all and build effective, accountable and inclusive institutions at all levels",
            17 => "Strengthen the means of implementation and revitalize the global partnership for sustainable development"
        );*/
        $goals_ids = Engine_Api::_()->getDbTable('sdggoals','sitecrowdfunding')->getSDGGoals();

        $targets = Engine_Api::_()->getDbTable('sdgtargets','sitecrowdfunding')->getSDGTargetsWithActualIDS();

        $this->view->goals_ids = $goals_ids;
        $this->view->target_ids = $targets;

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

        $this->view->goals =  $goals = Engine_Api::_()->getDbtable('goals','sitecrowdfunding')->getAllGoalsByProjectId($project_id);

    }

    public function addGoalAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID AND OBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $this->view->layoutType = $layoutType = $this->_getParam('layoutType');

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
        $this->view->TabActive = "sitecrowdfunding_dashboard_editgoals";
        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Goals(array('project_id'=> $project_id));
        $form->removeDecorator('title');
        $form->removeDecorator('description');

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            if (empty($values))
                return;

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_goal');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $goal = $table->createRow();

                $string = $values['sdg_target_id'];
                $str_arr = explode ("-", $string);

                $inputs = array(
                    'sdg_goal_id' => $values['sdg_goal_id'],
                    'sdg_target_id' => $str_arr[1],
                    //'targets' => $values['targets'],
                    'project_id' => $project_id,
                );
                $goal->setFromArray($inputs);
                $goal->save();
                $db->commit();

            }catch (Exception $e){
                $db->rollBack();
                throw $e;
            }

            if($layoutType == 'projectStepsCreate'){
                return $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRefresh' => true,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Goal has been added successfully.'))
                ));
            }else{
                return $this->_helper->redirector->gotoRoute(array('controller' => 'goals', 'action' => 'manage-goals', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
            }
        }

    }

    public function editGoalAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID AND OBJECT
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->goal_id = $goal_id = $this->_getParam('goal_id', null);
        $this->view->goal = $goal = Engine_Api::_()->getItem('sitecrowdfunding_goal', $goal_id);
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $this->view->layoutType = $layoutType = $this->_getParam('layoutType');

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
        $this->view->TabActive = "sitecrowdfunding_dashboard_editgoals";
        $form = new Sitecrowdfunding_Form_Project_Goals(array('project_id'=> $project_id));
        $form->removeDecorator('title');
        $form->removeDecorator('description');
        //$form->setTitle('Edit Output');
        //$form->setDescription('Edit the Output details using below form.');
        $form->execute->setLabel('Save changes');
        $goalAsArray = $goal->toArray();
        $form->populate($goalAsArray);

        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            if (empty($values))
                return;

            $string = $values['sdg_target_id'];
            $str_arr = explode ("-", $string);

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_goal');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $goalModel = $goal;

                $inputs = array(
                    'sdg_goal_id' => $values['sdg_goal_id'],
                    'sdg_target_id' => $str_arr[1]
                );

                $goalModel->setFromArray($inputs);
                $goalModel->save();
                $db->commit();

            }catch (Exception $e){
                $db->rollBack();
                throw $e;
            }

            if($layoutType == 'projectStepsCreate'){
                return $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRefresh' => true,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Goal has been edited successfully.'))
                ));
            }else {
                return $this->_helper->redirector->gotoRoute(array('controller' => 'goals', 'action' => 'manage-goals', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
            }

        }
    }

    public function deleteGoalAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->goal_id = $goal_id = $this->_getParam('goal_id', null);

        // Send to view script if not POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if(!empty($goal_id)){

            $item  = Engine_Api::_()->getItem('sitecrowdfunding_goal', $goal_id);

            $item->delete();

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Goal has been remove successfully.'))
            ));

        }
    }

}