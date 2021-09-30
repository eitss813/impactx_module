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
class Sitepage_InitiativesController extends Core_Controller_Action_Standard
{

    public function getInitiativesAction() {

        $page_id = $this->_getParam('page_id');
        $initiative_id = $this->_getParam('initiative_id',null);

        $initiativeOptions = array();

        // if only page_id is passed
        if(!empty($page_id) && empty($initiative_id)){
            $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageId($page_id);
            if (count($initiatives) > 0) {
                foreach ($initiatives as $initiative) {
                    $data = array();
                    $data['value'] = $initiative['initiative_id'];
                    $data['text'] = $initiative['title'];
                    $initiativeOptions[] = $data;
                }
            }
        }

        // if both page_id and initiative_id is passed
        if(!empty($page_id) && !empty($initiative_id)){
            $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);
            $project_galleries = preg_split('/[,]+/', $initiative['sections']);
            $project_galleries = array_filter(array_map("trim", $project_galleries));
            if (count($project_galleries) > 0) {
                foreach ($project_galleries as $project_gallery) {
                    $data = array();
                    $data['value'] = $project_gallery;
                    $data['text'] = $project_gallery;
                    $initiativeOptions[] = $data;
                }
                $data = array();
                $data['value'] = 'OTHER';
                $data['text'] = 'Other';
                $initiativeOptions[] = $data;
            }
        }

        $this->view->initiatives = $initiativeOptions;

    }

    public function listAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->page_id = $page_id = $this->_getParam('page_id');

        //GET PROJECT ITEM
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //IF THERE IS NO PAGE.
        if (empty($sitepage)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        Engine_Api::_()->core()->setSubject($sitepage);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->initiatives = $outcomes = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageId($page_id);

    }

    public function createAction()
    {

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID AND OBJECT
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //IF PROJECT IS NOT EXIST
        if (empty($sitepage)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //SET PROJECT SUBJECT
        Engine_Api::_()->core()->setSubject($sitepage);

        // get initiative count
        $initiativesCount = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getInitiativesCountByPageId($page_id);

        //SELECTED TAB
        $this->view->form = $form = new Sitepage_Form_Initiative(array('page_id' => $page_id));
        $form->removeDecorator('description');
        $form->removeElement('initiative_order');
        $form->setTitle('Add Initiatives');

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            // metrics
            $metrics = array();
            $total_metrics_added = $_POST['metrics_counter'];
            $metric_name_array = $_POST['metric_name'];
            $metric_value_array = $_POST['metric_value'];
            $metric_unit_array = $_POST['metric_unit'];
            $selected_metric_id_array = $_POST['selected_metric_id'];

            // questions
            $questions = array();
            $total_questions_added = $_POST['questions_counter'];
            $question_titles = $_POST['question_title'];
            $question_hints = $_POST['question_hint'];
            $question_fieldtypes = $_POST['question_fieldtype'];

            // loop metric
            for ($i = 0; $i < $total_metrics_added; $i++) {
                $name = $metric_name_array[$i];
                $value = $metric_value_array[$i];
                $unit = $metric_unit_array[$i];
                $selected_metric_id = $selected_metric_id_array[$i];

                // if metric length=1 then user can fill any one field
                if ($total_metrics_added == 1) {

                    if (
                        // if any only one filled
                    (
                        (!empty($name) && empty($value) && empty($unit)) ||
                        (empty($name) && !empty($value) && empty($unit)) ||
                        (empty($name) && empty($value) && !empty($unit))
                    )
                    ) {
                        $error = $this->view->translate('Please fill all name, unit and value in metrics');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }

                } else {

                    if (empty($name) || empty($value) || empty($unit)) {
                        $error = $this->view->translate('Please fill all name , unit and value in metrics');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }

                }

                // if all above satisfy
                if(!empty($name) && !empty($value) && !empty($unit)){
                    $metrics[] = array(
                        "metric_name" => $name,
                        "metric_value" => $value,
                        "metric_unit" => $unit,
                        "selected_metric_id" => $selected_metric_id
                    );
                }

            }

            // loop questions
            for ($i = 0; $i < $total_questions_added; $i++) {
                $questiontitle = $question_titles[$i];
                $questionhint = $question_hints[$i];
                $questionfieldtype = $question_fieldtypes[$i];

                // if question length=1 then user can fill any one field
                if ($total_questions_added == 1) {

                    if (
                        // if both fields is empty
                        (!empty($questiontitle) && empty($questionhint) && empty($questionfieldtype) ) ||
                        (empty($questiontitle) && !empty($questionhint) && empty($questionfieldtype) ) ||
                        (empty($questiontitle) && empty($questionhint) && !empty($questionfieldtype) ) ||
                        // if one field is empty
                        (!empty($questiontitle) && !empty($questionhint) && empty($questionfieldtype) ) ||
                        (empty($questiontitle) && !empty($questionhint) && !empty($questionfieldtype) ) ||
                        (!empty($questiontitle) && empty($questionhint) && !empty($questionfieldtype) )
                    ) {
                        $error = $this->view->translate('Please fill fields in questions');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }

                } else {

                    if (empty($questiontitle) || empty($questionhint) || empty($questionfieldtype)) {
                        $error = $this->view->translate('Please fill all fields in questions');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }

                }

                // if all above satisfy
                if(!empty($questiontitle) && !empty($questionhint) && !empty($questionfieldtype)){
                    $questions[] = array(
                        "question_title" => $questiontitle,
                        "question_hint" => $questionhint ,
                        "question_fieldtype" => $questionfieldtype
                    );
                }

            }

            if (empty($values))
                return;

            if (isset($values['sections']) && !empty($values['sections'])) {
                $sections = preg_split('/[,]+/', $values['sections']);
                $sections = array_filter(array_map("trim", $sections));

                /*if (count($sections) >= 5) {
                    $error = $this->view->translate('Project galleries can be added upto 4');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);
                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                }*/
            }

            $initiativeTable = Engine_Api::_()->getItemTable('sitepage_initiative');
            $initiativeMetricTable = Engine_Api::_()->getItemTable('sitepage_initiativemetric');
            $metricTable = Engine_Api::_()->getItemTable('sitepage_metric');
            $initiativeQuestionTable = Engine_Api::_()->getItemTable('sitepage_initiativequestion');

            $db = $initiativeTable->getAdapter();
            $db->beginTransaction();
            try {

                $initiatives = $initiativeTable->createRow();

                // get file
                $file_id = null;
                if (!empty($values['logo'])) {
                    $file_id = $initiatives->setLogo($form->logo);
                }



//                $projects  = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsByPageIdAndInitiativesId($page_id,$initiative_id,null);
//                $projectsIds  = [];
//                foreach ($projects as $project) {
//                    array_push($projectsIds,$project['project_id']);
//                }
//
//                echo "hiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii";
//                print_r($projectsIds);
//
//                $data = array('payment_action_label' => $_POST['payment_action_label'] );
//                $where = $db->quoteInto('project_id IN (?)', $projectsIds );
//                $db->update( "engine4_sitecrowdfunding_projects", $data, $where );

                $inputs = array(
                    'title' => $values['title'],
                    'about' => $values['about'],
                    'back_story' => $values['back_story'],
                    'sections' => $values['sections'],
                    'logo' => $file_id,
                    'page_id' => $page_id,
                    'user_id' => $viewer_id,
                    'payment_action_label' => $_POST['payment_action_label'],
                    'payment_is_tax_deductible' => $_POST['payment_is_tax_deductible'],
                    'payment_tax_deductible_label' => $_POST['payment_tax_deductible_label']
                );

                $initiatives->setFromArray($inputs);
                $initiatives->save();


                // set initiative count
                $initiativeModal = Engine_Api::_()->getItem('sitepage_initiative', $initiatives->initiative_id);
                $initiativeModal->initiative_order = $initiativesCount + 1;
                $initiativeModal->save();

                // save initiatives metrics
                foreach ($metrics as $metric) {

                    // save in metrics table
                    if(!$metric["selected_metric_id"]) {
                        $metricData = array(
                            'metric_name' => $metric['metric_name'],
                            'metric_unit' => $metric['metric_unit'],
                            'page_id' => $page_id,
                            'user_id' => $viewer_id
                        );
                        $metricRow = $metricTable->createRow();
                        $metricRow->setFromArray($metricData);
                        $metricRow->save();

                        $id= $metricRow->metric_id;

                        $auth = Engine_Api::_()->authorization()->context;

                        $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                        foreach ($roles as $i => $role) {
                            $auth->setAllowed($metricRow, $role, "view", 1);
                            $auth->setAllowed($metricRow, $role, "comment", 1);
                        }

                        $roles = array('leader', 'parent_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                        foreach ($roles as $i => $role) {
                            $auth->setAllowed($metricRow, $role, "topic", 1);
                            $auth->setAllowed($metricRow, $role, "post", 1);
                        }

                    }else{
                        $id = $metric["selected_metric_id"];
                    }

                    // save in initiative-metrics
                    $initiativeMetricsInputs = array(
                        'initiativemetric_name' => $metric['metric_name'],
                        'initiativemetric_value' => $metric['metric_value'],
                        'initiativemetric_unit' => $metric['metric_unit'],
                        'initiative_id' => $initiatives->initiative_id,
                        'metric_id' => $id,
                        'page_id' => $page_id,
                        'user_id' => $viewer_id
                    );

                    $initiativeMetric = $initiativeMetricTable->createRow();
                    $initiativeMetric->setFromArray($initiativeMetricsInputs);
                    $initiativeMetric->save();
                }

                // save initiatives questions
                foreach ($questions as $question) {
                    $questionInputs = array(
                        'initiativequestion_title' => $question['question_title'],
                        'initiativequestion_hint' => $question['question_hint'],
                        'initiativequestion_fieldtype' => $question['question_fieldtype'],
                        'initiative_id' => $initiatives->initiative_id,
                        'page_id' => $page_id,
                        'user_id' => $viewer_id
                    );

                    $initiativeQuestion = $initiativeQuestionTable->createRow();
                    $initiativeQuestion->setFromArray($questionInputs);
                    $initiativeQuestion->save();
                }

                $db->commit();

            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('controller' => 'initiatives', 'action' => 'list', 'page_id' => $page_id), 'sitepage_initiatives', true);

        }

    }

    public function editAction()
    {

        if (!$this->_helper->requireUser()->isValid())
            return;
        $db = Engine_Db_Table::getDefaultAdapter();
        //GET PROJECT ID AND OBJECT
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->initiative_id = $initiative_id = $this->_getParam('initiative_id', null);
        $this->view->initiative = $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);
        $this->view->initiativeMetric = $initiativeMetric = Engine_Api::_()->getItemTable('sitepage_initiativemetric')->getAllInitiativesMetricById($page_id, $initiative_id);
        $this->view->initiativeQuestions = $initiativeQuestions = Engine_Api::_()->getItemTable('sitepage_initiativequestion')->getAllInitiativesQuestionsById($page_id, $initiative_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //IF PROJECT IS NOT EXIST
        if (empty($page_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //SET PROJECT SUBJECT
        Engine_Api::_()->core()->setSubject($sitepage);

        //SELECTED TAB
        $form = new Sitepage_Form_Initiative(array('page_id' => $page_id, 'initiative_id' => $initiative_id));
        $form->removeDecorator('description');
        $form->setTitle('Edit Initiatives');

        // If logo is added dont show the add file option.
        if (!empty($initiative['logo'])) {
            $form->removeElement('logo');
        }

        $initiativeAsArray = $initiative->toArray();
        $form->populate($initiativeAsArray);

        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            $metrics = array();
            $total_metrics_added = $_POST['metrics_counter'];
            $metric_id_array = $_POST['metric_id'];
            $metric_name_array = $_POST['metric_name'];
            $metric_unit_array = $_POST['metric_unit'];
            $metric_value_array = $_POST['metric_value'];
            $selected_metric_id_array = $_POST['selected_metric_id'];

            // questions
            $questions = array();
            $total_questions_added = $_POST['questions_counter'];
            $question_ids = $_POST['question_id'];
            $question_titles = $_POST['question_title'];
            $question_hints = $_POST['question_hint'];
            $question_fieldtypes = $_POST['question_fieldtype'];

            $already_created_question_ids = array();

            // loop metric
            for ($i = 0; $i < $total_metrics_added; $i++) {
                $id  = $metric_id_array[$i];
                $name = $metric_name_array[$i];
                $value = $metric_value_array[$i];
                $unit = $metric_unit_array[$i];
                $selected_metric_id = $selected_metric_id_array[$i];

                // if metric length=1 then user can fill any one field
                if ($total_metrics_added == 1) {

                    if (
                        // if any only one filled
                    (
                        (!empty($name) && empty($value) && empty($unit)) ||
                        (empty($name) && !empty($value) && empty($unit)) ||
                        (empty($name) && empty($value) && !empty($unit))
                    )
                    ) {
                        $error = $this->view->translate('Please fill all name, unit and value in metrics');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }

                } else {

                    if (empty($name) || empty($value) || empty($unit)) {
                        $error = $this->view->translate('Please fill all name , unit and value in metrics');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }

                }

                // if all above satisfy
                if(!empty($name) && !empty($value)){
                    $metrics[] = array(
                        "metric_id" => $id,
                        "metric_name" => $name,
                        "metric_value" => $value,
                        "metric_unit" => $unit,
                        "selected_metric_id" => $selected_metric_id
                    );
                }

            }

            // loop questions
            for ($i = 0; $i < $total_questions_added; $i++) {
                $questionid = $question_ids[$i];
                $questiontitle = $question_titles[$i];
                $questionhint = $question_hints[$i];
                $questionfieldtype = $question_fieldtypes[$i];

                // insert the created question_ids, for prevent from deleting those
                if(!empty($questionid)){
                    $already_created_question_ids[] = $questionid;
                }

                // if question length=1 then user can fill any one field
                if ($total_questions_added == 1) {

                    if (
                        // if both fields is empty
                        (!empty($questiontitle) && empty($questionhint) && empty($questionfieldtype) ) ||
                        (empty($questiontitle) && !empty($questionhint) && empty($questionfieldtype) ) ||
                        (empty($questiontitle) && empty($questionhint) && !empty($questionfieldtype) ) ||
                        // if one field is empty
                        (!empty($questiontitle) && !empty($questionhint) && empty($questionfieldtype) ) ||
                        (empty($questiontitle) && !empty($questionhint) && !empty($questionfieldtype) ) ||
                        (!empty($questiontitle) && empty($questionhint) && !empty($questionfieldtype) )
                    ) {
                        $error = $this->view->translate('Please fill fields in questions');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }

                } else {

                    if (empty($questiontitle) || empty($questionhint) || empty($questionfieldtype)) {
                        $error = $this->view->translate('Please fill all fields in questions');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }

                }

                // if all above satisfy
                if(!empty($questiontitle) && !empty($questionhint) && !empty($questionfieldtype)){
                    $questions[] = array(
                        "question_id" => $questionid,
                        "question_title" => $questiontitle,
                        "question_hint" => $questionhint ,
                        "question_fieldtype" => $questionfieldtype
                    );
                }

            }

            if (empty($values))
                return;

            if (isset($values['sections']) && !empty($values['sections'])) {
                $sections = preg_split('/[,]+/', $values['sections']);
                $sections = array_filter(array_map("trim", $sections));

                /*if (count($sections) >= 5) {
                    $error = $this->view->translate('Project galleries can be added upto 4');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);
                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                }*/
            }

            $initiativeTable = Engine_Api::_()->getItemTable('sitepage_initiative');
            $initiativeMetricTable = Engine_Api::_()->getItemTable('sitepage_initiativemetric');
            $metricTable = Engine_Api::_()->getItemTable('sitepage_metric');
            $initiativeQuestionTable = Engine_Api::_()->getItemTable('sitepage_initiativequestion');
            $db = $initiativeTable->getAdapter();
            $db->beginTransaction();
            try {
                $initiativeModel = $initiative;

                $file_id = $initiativeModel->logo;
                if (!empty($values['logo'])) {
                    $file_id = $initiative->setLogo($form->logo);
                }

                $projects  = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsByPageIdAndInitiativesId($page_id,$initiative_id,null);
                $projectsIds  = [];
                foreach ($projects as $project) {
                    array_push($projectsIds,$project['project_id']);
                }



                /*
                if( $_POST['payment_action_label'] && $projectsIds && count($projectsIds) > 0) {
                   $data = array('payment_action_label' => $_POST['payment_action_label'] );
                   $where = $db->quoteInto('project_id IN (?)', $projectsIds );
                   $db->update( "engine4_sitecrowdfunding_projects", $data, $where );
               }
               */

                $inputs = array(
                    'title' => $values['title'],
                    'about' => $values['about'],
                    'sections' => $values['sections'],
                    'back_story' => $values['back_story'],
                    'updated_date' => new Zend_Db_Expr('NOW()'),
                    'initiative_order' => $values['initiative_order'],
                    'logo' => $file_id,
                    'payment_action_label' => $_POST['payment_action_label'],
                    'payment_is_tax_deductible' => $_POST['payment_is_tax_deductible'],
                    'payment_tax_deductible_label' => $_POST['payment_tax_deductible_label']
                );



                $initiativeModel->setFromArray($inputs);
                $initiativeModel->save();

                // save initiatives metrics
                $initiativeMetricTable->delete(array('initiative_id = ?' => $initiative_id, 'page_id =? ' => $page_id));
                foreach ($metrics as $metric) {

                    // update metric table
                    if($metric['metric_id'] && !$metric["selected_metric_id"]){
                        $id = $metric['metric_id'];
                    }elseif (!$metric['metric_id'] && !$metric["selected_metric_id"]){
                        $metricData = array(
                            'metric_name' => $metric['metric_name'],
                            'metric_unit' => $metric['metric_unit'],
                            'page_id' => $page_id,
                            'user_id' => $viewer_id
                        );
                        $metricRow = $metricTable->createRow();
                        $metricRow->setFromArray($metricData);
                        $metricRow->save();

                        $id= $metricRow->metric_id;
                    }else{
                        $id = $metric["selected_metric_id"];
                    }


                    // reinsert initiative-metric id
                    $metricsInputs = array(
                        'initiativemetric_name' => $metric['metric_name'],
                        'initiativemetric_value' => $metric['metric_value'],
                        'initiativemetric_unit' => $metric['metric_unit'],
                        'initiative_id' => $initiative_id,
                        'metric_id' => $id,
                        'updated_date' => new Zend_Db_Expr('NOW()'),
                        'page_id' => $page_id,
                        'user_id' => $viewer_id
                    );
                    $initiativeMetric = $initiativeMetricTable->createRow();
                    $initiativeMetric->setFromArray($metricsInputs);
                    $initiativeMetric->save();
                }

                // check if already created question there when saving, except those delete other questions
                if(count($already_created_question_ids) > 0 ){
                    $initiativeQuestionTable->delete(array('initiative_id = ?' => $initiative_id, 'page_id =? ' => $page_id , 'initiativequestion_id NOT IN ('.join(',', $already_created_question_ids).')'));
                }

                // check if created question not there  when saving , delete all questions
                if(count($already_created_question_ids) <= 0 ){
                    $initiativeQuestionTable->delete(array('initiative_id = ?' => $initiative_id, 'page_id =? ' => $page_id ));
                }

                // insert only not added questions, instead of adding all
                foreach ($questions as $question) {
                    if(empty($question['question_id'])){
                        $questionInputs = array(
                            'initiativequestion_title' => $question['question_title'],
                            'initiativequestion_hint' => $question['question_hint'],
                            'initiativequestion_fieldtype' => $question['question_fieldtype'],
                            'initiative_id' => $initiative_id,
                            'updated_date' => new Zend_Db_Expr('NOW()'),
                            'page_id' => $page_id,
                            'user_id' => $viewer_id
                        );
                        $initiativeQuestion = $initiativeQuestionTable->createRow();
                        $initiativeQuestion->setFromArray($questionInputs);
                        $initiativeQuestion->save();
                    }
                }

                $db->commit();

            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('controller' => 'initiatives', 'action' => 'list', 'page_id' => $page_id), 'sitepage_initiatives', true);

        }
    }

    public function deleteAction()
    {

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->initiative_id = $initiative_id = $this->_getParam('initiative_id', null);
        $this->view->page_id = $page_id = $this->_getParam('page_id');

        // Send to view script if not POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!empty($initiative_id)) {

            $item = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);
            $item->delete();

            // delete metrics
            $initiativeMetricTable = Engine_Api::_()->getItemTable('sitepage_initiativemetric');
            $initiativeMetricTable->delete(array('initiative_id = ?' => $initiative_id, 'page_id =? ' => $page_id));

            // delete questions
            $initiativeQuestionsTable = Engine_Api::_()->getItemTable('sitepage_initiativequestion');
            $initiativeQuestionsTable->delete(array('initiative_id = ?' => $initiative_id, 'page_id =? ' => $page_id));

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Initiative has been remove successfully.'))
            ));

        }
    }

    public function landingPageAction()
    {

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->initiative_id = $initiative_id = $this->_getParam('initiative_id');

        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->initiative = $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);

        $this->view->metric_page_no = $metric_page_no = $this->_getParam('metric_page_no',null);
        if($metric_page_no && $metric_page_no!=null) {
            $metric_page_no  = $metric_page_no;
        }
        else {
            $metric_page_no = 0;
        }
        $this->view->initiativeMetrics = Engine_Api::_()->getItemTable('sitepage_initiativemetric')->getAllInitiativesMetricByIdPage($page_id,$initiative_id,$metric_page_no);





        if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
            Engine_Api::_()->core()->setSubject($sitepage);
        }

        $this->view->cover_params = array('top' => 0, 'left' => 0);
        if (!empty($initiative['cover_params'])) {
            $this->view->cover_params = json_decode($initiative['cover_params']);
        }

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        // get photo
        $this->view->photo = $photo = Engine_Api::_()->getItem('sitepage_photo', $sitepage->page_cover);

        // get project galleries
        $project_galleries = preg_split('/[,]+/', $initiative['sections']);
        $this->view->project_galleries = $project_galleries = array_filter(array_map("trim", $project_galleries));

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        // set subject if params
        if (empty($params['tab_link'])) {
            if(count($project_galleries) > 0 ){
                $params['tab_link'] = 'project_galleries';
            }else{
                $params['tab_link'] = 'browse_projects';
            }
        } else if (isset($params['tab_link']) && !empty($params['tab_link'])) {
            $params['tab_link'] = $params['tab_link'];
        } else {
            if(count($project_galleries) > 0 ){
                $params['tab_link'] = 'project_galleries';
            }else{
                $params['tab_link'] = 'browse_projects';
            }
        }

        $params['page_id'] = $page_id;
        $params['initiative'] = $initiative_id;
        if (empty($params['initiative_galleries'])) {
            $params['initiative_galleries'] = null;
        } else if (isset($params['initiative_galleries']) && !empty($params['initiative_galleries'])) {
            $params['initiative_galleries'] = $params['initiative_galleries'];
        } else {
            $params['initiative_galleries'] = null;
        }

        // Follow Button
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        $this->view->resource_id = $resource_id = $sitepage->getIdentity();
        $this->view->resource_type = $resource_type = $sitepage->getType();
        $this->view->isFollow = $isFollow = $subject->follows()->isFollow($viewer);

        $this->view->params = $params;

        // Common params
        $this->view->gridViewWidth = $params['gridViewWidth'] = $this->_getParam('gridViewWidth', 296);
        $this->view->gridViewHeight = $params['gridViewHeight'] = $this->_getParam('gridViewHeight', 510);
        $this->view->projectOption = $this->_getParam('projectOption');
        if (empty($this->view->projectOption) || !is_array($this->view->projectOption)) {
            $this->view->projectOption = $params['projectOption'] = array('title', 'owner', 'backer', 'like', 'facebook', 'twitter', 'linkedin', 'googleplus');
        }

        $this->view->truncationLocation = 30;
        $this->view->titleTruncationGridView = 25;
        $this->view->titleTruncationListView = 55;
        $this->view->descriptionTruncation = 175;

        // Locations
        if ($params['tab_link'] == 'project_locations') {

            // Make form
            $this->view->locationForm = $locationForm = new Sitecrowdfunding_Form_ProjectLocationFilter();



            // set params
            $project_location_prams = array();
            $project_location_prams['page_id'] = $page_id;
            $project_location_prams['initiative'] = $initiative_id;
            if ($request->getParam('page')){
                $page_no = $request->getParam('page');
            } else{
                $page_no = $this->_getParam('page', 1);
            }
            $project_location_prams['page'] = $page_no;
            $project_location_prams['location_only_projects'] = true;

            // populate form
            $locationForm->populate($_POST);

            if (isset($_POST['search_enabled'])) {
                $values = $locationForm->getValues();

                //set the latitude and longitude based on location chosen custome code
                if($values['projectlocation'] && $values['projectlocation'] != 'Type search' && $values['projectlocation'] != 'Select Location' ) {
                    $locationsDetail = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding')->getInitiativeProjectsByLocationSelect($page_id,$initiative_id);
                    foreach ($locationsDetail as $val) {
                        if($val->location == $values['projectlocation']) {
                            $valss = '{"location" :"'.$val->location.'",
                        "latitude" :"'.(float)$val->latitude.'",
                        "longitude":"'.(float)$val->longitude.'"}';
                            $projectlocationOption[$val->location] = $val->location;
                        }
                    }
                    $values['customLocation'] = $values['projectlocation'];
                    $values['customLocationParams'] = $valss;
                }

                $project_location_prams['search'] = $values['search_str'];
                $project_location_prams['customLocationMiles'] = $values['customLocationMiles'];
                if (isset($values['customLocationParams']) && $values['customLocationParams']) {
                    if (is_string($values['customLocationParams'])) {
                        $locationParams = Zend_Json_Decoder::decode($values['customLocationParams']);
                        $project_location_prams = array_merge($project_location_prams, $locationParams);
                    }
                }
            }

            // get location based projects
            $result = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding')->getInitiativeProjectsSelect($project_location_prams);
            $paginator = Zend_Paginator::factory($result);
            $list_paginator = $result->query()->fetchAll();
            $paginator->setItemCountPerPage(8);
            $paginator->setCurrentPageNumber($page_no);
            $this->view->paginator = $paginator;
            $this->view->projectsCount = $paginator->getTotalItemCount();

            // get its lat and lng for plotting in map
            if (count($list_paginator) > 0) {

                $ids = array();
                foreach ($list_paginator as $project) {
                    $id = $project['project_id'];
                    $ids[] = $id;
                    $project_temp[$id] =  Engine_Api::_()->getItem('sitecrowdfunding_project', $id);
                }
                $locationParams =  array();
                $locationParams['project_ids'] = $ids;
                $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding')->getLocation($locationParams);
                $this->view->list = $project_temp;
            }

            $this->view->isViewMoreButton = false;
        }

        // Browse Projects
        if($params['tab_link'] == 'browse_projects'){

            /************************************* SEARCH ***********************************/
            //FORM CREATION
            $this->view->whatWhereWithinmile = $params['whatWhereWithinmile'] = $this->_getParam('whatWhereWithinmile', 0);
            $this->view->advancedSearch = $params['advancedSearch'] = $this->_getParam('advancedSearch', 0);
            $this->view->showAllCategories = $params['showAllCategories'] = $this->_getParam('showAllCategories', 1);
            $this->view->locationDetection = $params['locationDetection'] = $this->_getParam('locationDetection', 0);

            $widgetSettings = array(
                'viewType' => 'vertical',
                'whatWhereWithinmile' => $this->view->whatWhereWithinmile,
                'advancedSearch' => $this->view->advancedSearch,
                'showAllCategories' => $this->view->showAllCategories,
                'locationDetection' => $this->view->locationDetection,
            );

            $this->view->searchForm = $searchForm = new Sitecrowdfunding_Form_Search_ProjectSearch(array('widgetSettings' => $widgetSettings));
            $searchForm->removeDecorator('title');
            $searchForm->removeElement('location');
            $searchForm->removeElement('locationmiles');
            $searchForm->removeElement('project_street');
            $searchForm->removeElement('project_city');
            $searchForm->removeElement('project_state');
            $searchForm->removeElement('project_country');
            $searchForm->removeElement('orderby');

            // hide the initiative project galleries
            if(count($project_galleries) <= 0 ){
                $searchForm->removeElement('initiative_galleries');
            }

            /*$orderBy = $request->getParam('orderby', null);
            if (empty($orderBy) && !empty($this->view->identity) ) {
                $order = Engine_Api::_()->sitecrowdfunding()->showSelectedProjectBrowseBy($this->view->identity);
                if (isset($searchForm->orderby))
                    $searchForm->orderby->setValue("$order");
            }else {
                $params['orderby'] = $orderBy;
            }*/

            if (!isset($params['category_id']))
                $params['category_id'] = 0;

            if (!isset($params['subcategory_id']))
                $params['subcategory_id'] = 0;

            if (!isset($params['subsubcategory_id']))
                $params['subsubcategory_id'] = 0;

            $this->view->category_id = $category_id = $params['category_id'];
            $this->view->subcategory_id = $subcategory_id = $params['subcategory_id'];
            $this->view->subsubcategory_id = $subsubcategory_id = $params['subsubcategory_id'];
            $this->view->categoryInSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitecrowdfunding_project', 'category_id');

            if (!isset($params['profile_type']) && !empty($this->view->category_id) && !empty($this->view->categoryInSearchForm)) {
                $categoryIds = array();
                $categoryIds[] = $this->view->category_id;
                $categoryIds[] = $this->view->subcategory_id;
                $categoryIds[] = $this->view->subsubcategory_id;

                $profile_type = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getProfileType($categoryIds);
                if (!empty($profile_type)) {
                    $params['profile_type'] = $profile_type;
                }
            }

            if (!empty($params))
                $searchForm->populate($params);

            //SHOW PROFILE FIELDS ON DOME READY
            if (!empty($this->view->categoryInSearchForm) && !empty($this->view->categoryInSearchForm->display) && !empty($category_id)) {
                $categoryIds = array();
                $categoryIds[] = $category_id;
                $categoryIds[] = $subcategory_id;
                $categoryIds[] = $subsubcategory_id;
            }


            $categories = Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getCategories();
            $categories_slug[0] = "";
            if (count($categories) != 0) {
                foreach ($categories as $category) {
                    $categories_slug[$category->category_id] = $category->getCategorySlug();
                }
            }
            $this->view->categories_slug = $categories_slug;

            /****************************************** PROJECTS *******************************************/

            //TO SHOW ONLY GRID VIEW IN THE MOBILE VIEW
            $this->view->isSiteMobileView = Engine_Api::_()->sitecrowdfunding()->isSiteMobileMode();

            $params['projectType'] = $contentType = $request->getParam('projectType', null);
            if (empty($contentType)) {
                $params['projectType'] = $this->_getParam('projectType', 'All');
            }
            $this->view->projectType = $params['projectType'];

            $params['selectProjects'] = $this->_getParam('selectProjects', 'all');

            $this->view->viewType = $params['viewType'] = $this->_getParam('viewType');
            $this->view->defaultViewType = $params['defaultViewType'] = $this->_getParam('defaultViewType', 'gridView');

            if (!empty($params['viewType']) && !in_array($params['defaultViewType'], $params['viewType']))
                $this->view->defaultViewType = $params['defaultViewType'] = $params['viewType'][0];
            if (empty($this->view->viewType))
                $this->view->viewType = $params['viewType'] = array($params['defaultViewType']);

            if (!isset($params['viewFormat']))
                $this->view->viewFormat = $params['viewFormat'] = $params['defaultViewType'];
            else
                $this->view->viewFormat = $params['viewFormat'];
            $this->view->gridViewCountPerPage = $gridViewCountPerPage = $params['gridItemCountPerPage'] = $this->_getParam('gridItemCountPerPage', 8);
            $this->view->listViewCountPerPage = $listViewCountPerPage = $params['listItemCountPerPage'] = $this->_getParam('listItemCountPerPage', 8);
            //$this->view->orderby = $orderby = $params['orderby'] = $this->_getParam('orderby', 'startDate');
            $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);

            if (isset($params['page']) && !empty($params['page']))
                $page = $params['page'];
            else
                $page = $this->_getParam('page', 1);

            $widgetSettings = array(
                'locationDetection' => $this->view->detactLocation,
            );

            //FORM GENERATION
            $searchForm = new Sitecrowdfunding_Form_Search_ProjectSearch(array('widgetSettings' => $widgetSettings));
            if (!empty($params)) {
                $searchForm->populate($params);
            }
            $this->view->formValues = $searchForm->getValues();
            $params = array_merge($params, $searchForm->getValues());

            if (!$this->view->detactLocation)
                $param['location'] = "";
            $this->view->latitude = $param['latitude'] = 0;
            $this->view->longitude = $param['longitude'] = 0;
            $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            if ($this->view->detactLocation) {
                $this->view->detactLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1);
            }
            if ($this->view->detactLocation) {
                $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
                $this->view->latitude = $params['latitude'] = $cookieLocation['latitude'];
                $this->view->longitude = $params['longitude'] = $cookieLocation['longitude'];
                if (empty($request->getParams()['location']))
                    $this->view->location = $params['location'] = '';
            }

            //CUSTOM FIELD WORK
            $customFieldValues = array();
            $customFieldValues = array_intersect_key($params, $searchForm->getFieldElements());

            /*$params['orderby'] = $orderBy = $request->getParam('orderby', null);
            if (empty($orderBy)) {
                if ($orderby == 'startDate')
                    $params['orderby'] = 'startDate';
                else
                    $params['orderby'] = $orderby;
            }*/

            $this->view->params = $params;
            $this->view->message = 'Nobody has started a project yet.';

            if ((isset($params['search']) && !empty($params['search'])) || (isset($params['category_id']) && !empty($params['category_id'])) || (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) || (isset($params['tag_id']) && !empty($params['tag_id'])) || (isset($params['location']) && !empty($params['location'])))
                $this->view->message = 'Nobody has started a project with that criteria.';

            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $this->view->viewerId = $viewer->getIdentity();
            if (!empty($viewer_id)) {
                $level_id = Engine_Api::_()->user()->getViewer()->level_id;
            } else {
                $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
            }
            $this->view->can_upload_project = $allow_upload_project = false;
            $this->view->isViewMoreButton = false;
            $this->view->showViewMore = true;
            $projectDbTables = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
            $paginatorGridView = $this->view->paginatorGridView = $projectDbTables->getProjectPaginator($params, $customFieldValues);
            $paginatorListView = $this->view->paginatorListView = $paginatorGridView;
            //$projectDbTables->getProjectPaginator($params, $customFieldValues);
            $paginatorMapView = $this->view->paginatorMapView = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectLocationSelect($params);
            if (count($this->view->viewType) == 2) {
                if ($gridViewCountPerPage > $listViewCountPerPage) {
                    $this->view->gPaginator = $paginatorListView;
                } else {
                    $this->view->gPaginator = $paginatorGridView;
                }
            } else {
                if ($this->view->viewType[0] == 'gridView') {
                    $this->view->gPaginator = $paginatorGridView;
                } else {
                    $this->view->gPaginator = $paginatorListView;
                }
            }
            $paginatorGridView->setItemCountPerPage($gridViewCountPerPage);
            $paginatorListView->setItemCountPerPage($listViewCountPerPage);

            $paginatorGridView->setCurrentPageNumber($page);
            $paginatorListView->setCurrentPageNumber($page);
            $this->view->countPage = $this->view->totalCount = $paginatorListView->getTotalItemCount();

        }

    }

    public function saveCroppedImageAction()
    {
        if (empty($_POST) || !isset($_POST['initiative_id'])) {
            return false;
        }

        $values = $_POST;

        if (empty($values)) {
            return;
        }

        $initiative_id = $values['initiative_id'];
        $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);

        $coordinatesInput = $values['coordinates'];
        $photo_id = $values['photo_id'];

        $storage = Engine_Api::_()->storage();
        $iMain = $storage->get($photo_id, 'thumb.main');
        $iCover = $storage->get($photo_id, 'thumb.cover');

        if (empty($iCover)) {
            $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
            $params = array(
                'parent_type' => 'sitepage_initiative',
                'parent_id' => $initiative_id,
                'user_id' => $initiative->user_id,
                'name' => $iMain->name,
            );

            $iCover = $filesTable->createFile($iMain->storage_path, $params);
            $iMain->bridge($iCover, 'thumb.cover');
        }

        $pName = $iMain->getStorageService()->temporary($iMain);
        $iName = dirname($pName) . '/nis_' . basename($pName);
        list($x, $y, $w, $h) = explode(':', $coordinatesInput);
        $image = Engine_Image::factory();
        $image->open($pName)
            ->resample($x + .1, $y + .1, $w - .1, $h - .1, $w, $h)
            ->write($iName)
            ->destroy();
        $iCover->store($iName);
        @unlink($iName);

        // remove cover params
        $initiative->cover_params = null;
        $initiative->save();

        return true;
    }

    public function getCoverPhotoAction()
    {

        //GET PAGE ID
        $page_id = $this->_getParam("page_id");
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

        //START MANAGE-ADMIN CHECK
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->initiative_id = $initiative_id = $this->_getParam('initiative_id', null);

        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->initiative = $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        $this->view->coverTop = 0;
        $this->view->coverLeft = 0;
        $coverParams = $initiative['cover_params'];
        if (isset($coverParams)) {
            $coverParams = json_decode($coverParams);
            $this->view->coverTop = $coverParams->top;
        }

    }

    public function resetPositionCoverPhotoAction()
    {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PAGE ID
        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->initiative_id = $initiative_id = $this->_getParam('initiative_id', null);

        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->initiative = $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        ////START MANAGE-ADMIN CHECK
        $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($can_edit))
            return;

        $cover_params = $this->_getParam('position', array('top' => '0', 'left' => 0));
        $initiative->cover_params = json_encode($cover_params);
        $initiative->save();
    }

    public function uploadCoverPhotoAction()
    {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //LAYOUT
        $this->_helper->layout->setLayout('default-simple');
        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        //PAGE ID
        $page_id = $this->_getParam('page_id');
        $initiative_id = $this->_getParam('initiative_id');

        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET FORM
        $this->view->form = $form = new Sitepage_Form_InitiativeCover();

        //CHECK FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //UPLOAD PHOTO
        if ($form->Filedata->getValue() !== null) {

            //PROCESS
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $file_id = null;
                $file_id = $initiative->setLogo($form->Filedata, true);
                $initiative->logo = $file_id;
                $initiative->cover_params = $this->_getParam('position', array('top' => '0', 'left' => 0));
                $initiative->save();
                $db->commit();

                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 10,
                    'parentRefresh' => 10,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
                ));

            } catch (Exception $e) {
                $db->rollBack();
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
                return;
            }
        }
    }

    public function removeCoverPhotoAction()
    {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //PAGE ID
        $page_id = $this->_getParam('page_id');
        $initiative_id = $this->_getParam('initiative_id', null);

        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);

        if ($this->getRequest()->isPost()) {
            $initiative->logo = null;
            $initiative->cover_params = array('top' => '0', 'left' => 0);
            $initiative->save();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    public function repositionCoverPhotoAction()
    {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->page_id = $page_id = $this->_getParam('page_id');
        $this->view->initiative_id = $initiative_id = $this->_getParam('initiative_id', null);

        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $this->view->initiative = $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    }
}