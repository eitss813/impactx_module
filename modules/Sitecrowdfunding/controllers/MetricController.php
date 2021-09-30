<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProjectController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_MetricController extends Seaocore_Controller_Action_Standard
{

    protected $_hasPackageEnable;

    public function init()
    {
        //SET THE SUBJECT
        if (0 !== ($project_id = (int)$this->_getParam('project_id')) && null !== ($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($project);
            Engine_Api::_()->sitecrowdfunding()->setPaymentFlag($project_id);
        }
        $this->_hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
    }

    // edit initiative answers
    public function listMetricAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->params = $params = $request->getParams();

        /****** get project details ****/
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }


        $entryTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
        $entryTableName = $entryTable->info('name');

        $valuesTableName = 'engine4_yndynamicform_entry_fields_values';

        // get metric fields
        $field_ids = array();
        foreach (Engine_Api::_()->fields()->getFieldsMeta('yndynamicform_entry') as $field) {
            if ($field->type == 'metrics') {
                $field_ids[] = $field->field_id;
            }
        }

        $projectSelect = $entryTable->select()
            ->setIntegrityCheck(false)
            ->from($entryTableName, array("$valuesTableName.field_id", "$valuesTableName.value"))
            ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id", array("$valuesTableName.value", "$valuesTableName.field_id"))
            ->where("$valuesTableName.field_id in (?)", $field_ids)
            ->where("$entryTableName.project_id IS NOT NULL")
            ->where("$entryTableName.project_id = ?",$project_id)
            ->where("$entryTableName.user_id IS NULL");

        $project_paginator = $entryTable->fetchALL($projectSelect);

        $metric_id_array = array();

        foreach($project_paginator as $project){
            $fieldMeta = Engine_Api::_()->fields()->getField($project->field_id, 'yndynamicform_entry');
            $metric_id = $fieldMeta->config['selected_metric_id'];
            if($metric_id){
                $metric_id_array[] = $metric_id;
            }
        }

        if(count($metric_id_array)){
            $this->view->metrics = $metrics = Engine_Api::_()->getDbtable('metrics', 'sitepage')->getMetricsDataById($metric_id_array);
        }

    }

    public function visibleMetricAction(){

        if (empty($_POST) || !isset($_POST['metric_id'])) {
            return false;
        }

        $values = $_POST;

        if(empty($values)){
            return;
        }
        $metric_id = $values['metric_id'];

        $metric = Engine_Api::_()->getItem('sitepage_metric', $metric_id);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            if ($metric->project_side_visibility) {
                $metric->project_side_visibility = 0;
            }
            else {
                $metric->project_side_visibility = 1;
            }
            $metric->save();
            $db->commit();

        }catch (Exception $e){
            $db->rollBack();
            throw $e;
        }

        return true;
    }

}
