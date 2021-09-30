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
class Sitecrowdfunding_FundingController extends Core_Controller_Action_Standard {

    public function editFundingAction(){

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        $this->view->layoutType = $layoutType = $this->_getParam('layoutType');

        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_editfunding';
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

        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Funding();
        $form->removeDecorator('title');

        $this->view->disableCalBtn = false;

        $form->removeDecorator('description');

        if($project->funding_start_date !== null && $project->funding_end_date !== null){
            $form->populate(array(
                'starttime' => date('Y-m-d',strtotime($project->funding_start_date)),
                'endtime' => date('Y-m-d',strtotime($project->funding_end_date)),
            ));
        }

        $form->populate(array(
            'starttime' => date('Y-m-d',strtotime($project->funding_start_date))

        ));


        if($project->payment_action_label !== null){
            $form->getElement('payment_action_label')->setValue($project->payment_action_label);
        }

        if($project->payment_is_tax_deductible !== null){
            $form->getElement('payment_is_tax_deductible')->setValue($project->payment_is_tax_deductible);
        }

        if($project->payment_tax_deductible_label !== null){
            $form->getElement('payment_tax_deductible_label')->setValue($project->payment_tax_deductible_label);
        }

        if($project->goal_amount !== null){
            $form->getElement('goal_amount')->setValue($project->goal_amount);
        }

        if($project->invested_amount !== null){
            $form->getElement('invested_amount')->setValue($project->invested_amount);
        }


        if (!$this->getRequest()->isPost() ) {
            $form->getElement('is_fund_raisable')->setValue($project->is_fund_raisable);
        }



        //END MANAGE-ADMIN CHECK
        //GET REQUEST IS AJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //SET PROJECT SUBJECT
        $this->view->rewards = Engine_Api::_()->getDbtable('rewards', 'sitecrowdfunding')->getRewards($project_id, 0);

        $tableReward = Engine_Api::_()->getDbtable('rewards', 'Sitecrowdfunding');
        $this->view->rewardCount = 0;
        $this->view->rewardCount = $tableReward->select()->from($tableReward->info('name'), array("count(*)"))->where('project_id = ?', $project_id)->query()->fetchColumn();



        if ($this->getRequest()->isPost() ) {

            $values = $form->getValues();
            $flag  = $values['is_fund_raisable'] || $values['is_fund_raisable'] =='1' ? true : false;
            $values['is_fund_raisable'] = $_POST['is_fund_raisable'];
            $values['invested_amount'] = $_POST['invested_amount'];
            $values['goal_amount'] = $_POST['goal_amount'];
            if($_POST['endtime'] && $_POST['endtime']['date']) {
                $eedate = 	$_POST['endtime']['date'].' '.$_POST['endtime']['hour'].':'.$_POST['endtime']['minute'].':00';

                $values['endtime'] =     $eedate;
            }

            if($_POST['starttime'] && $_POST['starttime']['date']) {
                $eedate = 	$_POST['starttime']['date'].' '.$_POST['starttime']['hour'].':'.$_POST['starttime']['minute'].':00';
                $values['starttime'] =     $eedate;
            }

            if($flag && $form->isValid($this->getRequest()->getPost())) {

                if (!empty($values['starttime']) && !empty($values['endtime']))
                {

                    $startDate = date('Y-m-d H:i:s', strtotime($values['starttime']));
                    $endDate = date('Y-m-d H:i:s', strtotime($values['endtime']));
                    $startDate2 =  date('Y-m-d', strtotime($startDate));
                    $endDate2 = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m', strtotime($endDate)), date('d', strtotime($endDate)), date('Y', strtotime($endDate))));

                }
                else if(!empty($values['starttime'])) {
                    $startDate = date('Y-m-d H:i:s', strtotime($values['starttime']));
                    $startDate2 =  date('Y-m-d', strtotime($startDate));

                }
                else{
                    $startDate2= null;
                    $endDate2= null;

                }

                if (empty($values))
                    return;

                // remove comma from amount fields to fix decimal issue
                $values['goal_amount'] = str_replace( ',', '', $values['goal_amount'] );
                $values['invested_amount'] = str_replace( ',', '', $values['invested_amount'] );

                $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
                $db = $table->getAdapter();
                $db->beginTransaction();
                try {
                    $projectModel = $project;

                    $inputs = array(
                        'funding_start_date' => $startDate2,
                        'funding_end_date' => $endDate2,
                        'goal_amount'=> $values['goal_amount'],
                        'invested_amount'=> $values['invested_amount'],
                        'is_fund_raisable'=> $values['is_fund_raisable'],
                        'payment_action_label' => $_POST['payment_action_label'],
                        'payment_is_tax_deductible' => $_POST['payment_is_tax_deductible'],
                        'payment_tax_deductible_label' => $_POST['payment_tax_deductible_label']
                    );

                    $projectModel->setFromArray($inputs);
                    $projectModel->save();
                    $db->commit();
                }catch (Exception $e){
                    $db->rollBack();
                    throw $e;
                }

                if($layoutType == 'fundingDetails'){
                    return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Updated Successfully'))
                    ));
                }else{
                    return $this->_helper->redirector->gotoRoute(array('controller' => 'funding', 'action' => 'edit-funding', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
                }
            }
            elseif (!$flag) {
                if (!empty($values['starttime']) && !empty($values['endtime']))
                {
                    $startDate = date('Y-m-d H:i:s', strtotime($values['starttime']));
                    $endDate = date('Y-m-d H:i:s', strtotime($values['endtime']));
                    $startDate2 =  date('Y-m-d', strtotime($startDate));
                    $endDate2 = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m', strtotime($endDate)), date('d', strtotime($endDate)), date('Y', strtotime($endDate))));

                }
                else if(!empty($values['starttime'])) {
                    $startDate = date('Y-m-d H:i:s', strtotime($values['starttime']));
                    $startDate2 =  date('Y-m-d', strtotime($startDate));

                }
                else{
                    $startDate2= null;
                    $endDate2= null;

                }

                if (empty($values))
                    return;

                // remove comma from amount fields to fix decimal issue
                $values['goal_amount'] = str_replace( ',', '', $values['goal_amount'] );
                $values['invested_amount'] = str_replace( ',', '', $values['invested_amount'] );

                $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
                $db = $table->getAdapter();
                $db->beginTransaction();
                try {
                    $projectModel = $project;

                    $inputs = array(
                        'funding_start_date' => $startDate2,
                        'funding_end_date' => $endDate2,
                        'goal_amount'=> $values['goal_amount'],
                        'invested_amount'=> $values['invested_amount'],
                        'is_fund_raisable'=> $values['is_fund_raisable'],
                        'payment_action_label' => $_POST['payment_action_label'],
                        'payment_is_tax_deductible' => $_POST['payment_is_tax_deductible'],
                        'payment_tax_deductible_label' => $_POST['payment_tax_deductible_label']
                    );

                    $projectModel->setFromArray($inputs);
                    $projectModel->save();
                    $db->commit();
                }catch (Exception $e){
                    $db->rollBack();
                    throw $e;
                }

                if($layoutType == 'fundingDetails'){
                    return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Updated Successfully'))
                    ));
                }else{
                    return $this->_helper->redirector->gotoRoute(array('controller' => 'funding', 'action' => 'edit-funding', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
                }
            }



        }

    }

    public function externalFundingAction(){

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');
        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_externalfunding';
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

        $this->view->externalfunding = $externalfunding =  Engine_Api::_()->getDbtable('externalfundings','sitecrowdfunding')->getAllExternalFunding($project_id);

    }

    public function addExternalFundingAction(){
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->project_id = $project_id = $this->_getParam('project_id');

        $this->view->layoutType = $layoutType = $this->_getParam('layoutType');

        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        //SHOW THE TAB ACTIVE IN DASHBOARD
        $this->view->activeItem = 'sitecrowdfunding_dashboard_externalfunding';
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

        $this->view->form = $form = new Sitecrowdfunding_Form_Project_ExternalFunding();
        $form->removeDecorator('title');
        $form->removeDecorator('description');

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (empty($values))
            return;

        $organization_id = null;
        $organization_name = null;
        if($values['resource_type'] === 'organization'){

            if($values['is_organisation_listed']=='yes'){
                $organization_id = $values['organization_id'];

                if (strpos($organization_id, 'internal') !== false) {
                    $organization_id = str_replace('internal', '', $organization_id);
                    $is_external = false;
                }else{
                    $organization_id = str_replace('external', '', $organization_id);
                    $is_external = true;
                }
            }

            if($values['is_organisation_listed']=='no'){
                $organization_name = $values['organization_name'];
                $is_external = true;
            }

        }else{
            $is_external = false;
        }


        if($values['resource_type']=== 'organization'){
            if($values['is_organisation_listed']=='yes'){
                if(empty($organization_id)){
                    $error = $this->view->translate('Please select organization - it is required.');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);
                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                }
            }
            if($values['is_organisation_listed']=='no'){
                if(empty($organization_name)){
                    $error = $this->view->translate('Please enter organization name - it is required.');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);
                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                }
            }
        }else {
            if(empty($values['member_id'])){
                $error = $this->view->translate('Please select member - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }
        }

        try{
            $fundingDate = date('Y-m-d H:i:s', strtotime($values['funding_date']));
            $fundingDate =  date('Y-m-d', strtotime($fundingDate));

            // remove comma from amount fields to fix decimal issue
            $values['funding_amount'] = str_replace( ',', '', $values['funding_amount'] );

            $table = Engine_Api::_()->getDbtable('externalfundings', 'sitecrowdfunding');
            $fundingRow = $table->createRow();
            $fundingRow->project_id = $project_id;
            $fundingRow->resource_type = $values['resource_type'];
            if($values['resource_type']=== 'organization'){
                if($values['is_organisation_listed']=='yes') {
                    $fundingRow->resource_id = $organization_id;
                    $fundingRow->resource_name = null;
                }
                if($values['is_organisation_listed']=='no') {
                    $fundingRow->resource_id = null;
                    $fundingRow->resource_name = $organization_name;
                }
            }else{
                $fundingRow->resource_id = $values['member_id'];
            }
            $fundingRow->funding_amount = $values['funding_amount'];
            $fundingRow->notes = $values['notes'];
            $fundingRow->is_external = $is_external;
            $fundingRow->funding_date = $fundingDate;
            $fundingRow->save();
        }catch (Exception $e){
            throw $e;
        }

        if($layoutType == 'fundingDetails'){
            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Added External Funding Successfully'))
            ));
        }else{
            return $this->_helper->redirector->gotoRoute(array('action' => 'external-funding', 'controller' => 'funding', 'project_id' => $project_id), "sitecrowdfunding_extended", true);
        }


    }

    public function deleteExternalFundingAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->externalfunding_id = $externalfunding_id = $this->_getParam('externalfunding_id', null);

        // Send to view script if not POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if(!empty($externalfunding_id)){

            $item  = Engine_Api::_()->getItem('sitecrowdfunding_externalfunding', $externalfunding_id);

            $item->delete();

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('External funding has been remove successfully.'))
            ));

        }

    }

    public function editExternalFundingAction(){

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PROJECT ID AND OBJECT
        $this->view->layoutType = $layoutType = $this->_getParam('layoutType');
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->externalfunding_id = $externalfunding_id = $this->_getParam('externalfunding_id', null);
        $this->view->externalfunding = $externalfunding = Engine_Api::_()->getItem('sitecrowdfunding_externalfunding', $externalfunding_id);
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
        $this->view->TabActive = "sitecrowdfunding_dashboard_externalfunding";
        $form = new Sitecrowdfunding_Form_Project_ExternalFunding(array('project_id'=> $project_id));
        $form->removeDecorator('title');
        $form->removeDecorator('description');
        $form->execute->setLabel('Save changes');
        $externalfundingAsArray = $externalfunding->toArray();
        $form->populate($externalfundingAsArray);

        $org_id = null;
        $organization_name = null;
        $mem_id = null;
        if($externalfunding['resource_type'] === 'organization' && $externalfunding['is_external'] === 1){
            $org_id = 'external'.$externalfunding['resource_id'];
        }
        else if($externalfunding['resource_type'] === 'organization' && $externalfunding['is_external'] === 1){
            $org_id = 'internal'.$externalfunding['resource_id'];
        }

        $organization_name = $externalfunding['resource_name'];
        $is_organisation_listed = null;
        if(empty($organization_name)){
            $is_organisation_listed = 'yes';
        }else{
            $is_organisation_listed = 'no';
        }

        $form->populate(
            array(
                'is_organisation_listed' => $is_organisation_listed,
                'organization_id' => $org_id,
                'organization_name' => $organization_name,
                'funding_date' => date('Y-m-d',strtotime($externalfunding->funding_date)),
            )
        );

        $this->view->selectedType = $externalfunding['resource_type'];


        $this->view->form = $form;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            if (empty($values))
                return;

            $organization_id = null;
            $organization_name = null;
            if($values['resource_type'] === 'organization'){

                if($values['is_organisation_listed']=='yes') {
                    $organization_id = $values['organization_id'];

                    if (strpos($organization_id, 'internal') !== false) {
                        $organization_id = str_replace('internal', '', $organization_id);
                        $is_external = false;
                    } else {
                        $organization_id = str_replace('external', '', $organization_id);
                        $is_external = true;
                    }
                }

                if($values['is_organisation_listed']=='no'){
                    $organization_name = $values['organization_name'];
                    $is_external = true;
                }

            }else{
                $is_external = false;
            }

            if($values['resource_type']=== 'organization'){
                if($values['is_organisation_listed']=='yes'){
                    if(empty($organization_id)){
                        $error = $this->view->translate('Please select organization - it is required.');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }
                }
                if($values['is_organisation_listed']=='no'){
                    if(empty($organization_name)){
                        $error = $this->view->translate('Please enter organization name - it is required.');
                        $error = Zend_Registry::get('Zend_Translate')->_($error);
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error);
                        return;
                    }
                }
            }else {
                if(empty($values['member_id'])){
                    $error = $this->view->translate('Please select member - it is required.');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);
                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                }
            }

            // remove comma from amount fields to fix decimal issue
            $values['funding_amount'] = str_replace( ',', '', $values['funding_amount'] );

            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_externalfunding');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $externalfundingModel = $externalfunding;

                $inputs = array(
                    'resource_type' => $values['resource_type'],
                    'resource_id' => null,
                    'resource_name' => null,
                    'is_external'=> $is_external,
                    'funding_amount' => $values['funding_amount'],
                    'funding_date' => date('Y-m-d', strtotime($values['funding_date'])),
                    'notes' => $values['notes']
                );

                if($values['resource_type']=== 'organization'){
                    if($values['is_organisation_listed']=='yes') {
                        $inputs['resource_id'] = $organization_id;
                        $inputs['resource_name'] = null;
                    }
                    if($values['is_organisation_listed']=='no') {
                        $inputs['resource_id'] = null;
                        $inputs['resource_name'] = $organization_name;
                    }
                }else{
                    $inputs['resource_id'] = $values['member_id'];
                }

                $externalfundingModel->setFromArray($inputs);
                $externalfundingModel->save();
                $db->commit();
            }catch (Exception $e){
                $db->rollBack();
                throw $e;
            }

            if($layoutType == 'fundingDetails'){
                return $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRefresh' => true,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Edit External Funding Successfully'))
                ));
            }else{
                return $this->_helper->redirector->gotoRoute(array('controller' => 'funding', 'action' => 'external-funding', 'project_id' => $project_id), 'sitecrowdfunding_extended', true);
            }
        }

    }
}