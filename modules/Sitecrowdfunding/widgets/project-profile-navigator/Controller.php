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
class Sitecrowdfunding_Widget_ProjectProfileNavigatorController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }
        //GET SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

        $project_id = $project->getIdentity();

        // Milestone
        $this->view->milestonesCount = Engine_Api::_()->getDbtable('milestones','sitecrowdfunding')->getMileStoneTotalCountByProjectId($project_id);

        // Organisation
        $this->view->externalorganizationsCount = Engine_Api::_()->getDbtable('organizations','sitecrowdfunding')->getOrganisationTotalCountByProjectId($project_id);
        $this->view->internalorganizationsCount = Engine_Api::_()->getDbtable('pages','sitecrowdfunding')->getPagesTotalCountbyProjectId($project_id);

        // Outcome and output
        $this->view->outcomesCount = Engine_Api::_()->getDbtable('outcomes','sitecrowdfunding')->getOutcomeTotalCountByProjectId($project_id);
        $this->view->outputCount = Engine_Api::_()->getDbtable('outputs','sitecrowdfunding')->getOutputTotalCountByProjectId($project_id);
        $this->view->goalsCount = Engine_Api::_()->getDbtable('goals','sitecrowdfunding')->getAllGoalsCountByProjectId($project_id);
        // Photos
        $album = $project->getSingletonAlbum();
        $paginator = $album->getCollectiblesPaginator();
        $this->view->photosCount = $paginator->getTotalItemCount();

        // Videos
        $moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
        $this->view->videoCount = Engine_Api::_()->$moduleName()->getTotalCount($project_id, 'sitevideo', 'videos');

        $db = Engine_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $this->view->additional_section = $additional_section = $select
            ->from('engine4_sitecrowdfunding_projects_additionalsection', '*')
            ->where('project_id = ?', $project_id)
            ->query()->fetchAll();

        // contact details
        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        $this->view->address = $address = $tableOtherinfo->getColumnValue($project->getIdentity(), 'contact_address');
        $this->view->phone = $phone = $tableOtherinfo->getColumnValue($project->getIdentity(), 'contact_phone');
        $this->view->email = $email = $tableOtherinfo->getColumnValue($project->getIdentity(), 'contact_email');

        // metrics
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

        $this->view->metric_id_array = $metric_id_array;

    }
}
