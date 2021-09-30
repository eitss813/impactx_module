<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_MetricsController extends Seaocore_Content_Widget_Abstract
{

    public function indexAction()
    {

        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $this->view->project_id = $project_id = $project->getIdentity();

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->params = $params = $request->getParams();

        // if no page_id, then dont render anything
        if (!$project_id) {
            return $this->setNoRender();
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
            $this->view->ajaxUrlPath = 'widget/index/mod/sitecrowdfunding/name/metrics';
            $metric_page_no = $params['metric_page_no'];
            if(!$metric_page_no || $metric_page_no==null) {
                $metric_page_no = 1;
            }
            $this->view->metrics = $metrics = Engine_Api::_()->getDbtable('metrics', 'sitepage')->getProjectVisibilityMetricsDataByIdPaginator($metric_id_array,$metric_page_no);
            $this->view->metric_page_no = $metric_page_no;
        }else{
            return $this->setNoRender();
        }

    }
}
