<?php

class Impactx_Widget_MetricsProfileController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $this->view->metric_id = $metric_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('metric_id');

        $this->view->metric_details = $metric_details = Engine_Api::_()->getItem('sitepage_metric', $metric_id);

        if (!Engine_Api::_()->core()->hasSubject('sitepage_metric')) {
            Engine_Api::_()->core()->setSubject($metric_details);
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        // get field_id
        $field_ids = array();
        foreach (Engine_Api::_()->fields()->getFieldsMeta('yndynamicform_entry') as $field) {
            if ($field->type == 'metrics') {
                $fieldMeta = Engine_Api::_()->fields()->getField($field->field_id, 'yndynamicform_entry');
                if ($fieldMeta->config['selected_metric_id'] == $metric_id) {
                    $field_ids[] = $field->field_id;
                }
            }
        }

        if ($field_ids) {
            $entryTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
            $entryTableName = $entryTable->info('name');

            $valuesTableName = 'engine4_yndynamicform_entry_fields_values';

            $projectSelect = $entryTable->select()
                    ->setIntegrityCheck(false)
                    ->from($entryTableName, array("$entryTableName.form_id", "$entryTableName.project_id", "$entryTableName.entry_id", "$valuesTableName.value"))
                    ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id", array("$valuesTableName.value", "$valuesTableName.field_id"))
                    ->where("$valuesTableName.field_id in (?)", $field_ids)
                    ->where("$entryTableName.publish=1")
                    ->where("$entryTableName.project_id IS NOT NULL")
                    ->where("$entryTableName.user_id IS NULL");

            $userSelect = $entryTable->select()
                    ->setIntegrityCheck(false)
                    ->from($entryTableName, array("$entryTableName.form_id", "$entryTableName.user_id", "$entryTableName.entry_id", "$valuesTableName.value"))
                    ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id", array("$valuesTableName.value", "$valuesTableName.field_id"))
                    ->where("$valuesTableName.field_id in (?)", $field_ids)
                    ->where("$entryTableName.publish=1")
                    ->where("$entryTableName.project_id IS NULL")
                    ->where("$entryTableName.user_id IS NOT NULL");

            $project_paginator = $entryTable->fetchAll($projectSelect);
            $user_paginator = $entryTable->fetchAll($userSelect);


            // get total aggregate value
            $project_aggregate_value = $entryTable->select()
                    ->setIntegrityCheck(false)
                    ->from($entryTableName, array("SUM($valuesTableName.value) as project_aggregate"))
                    ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id")
                    ->where("$valuesTableName.field_id in (?)", $field_ids)
                    ->where("$entryTableName.publish=1")
                    ->where("$entryTableName.project_id IS NOT NULL")
                    ->where("$entryTableName.user_id IS NULL")
                    ->query()
                    ->fetchColumn();

            $user_aggregate_value = $entryTable->select()
                    ->setIntegrityCheck(false)
                    ->from($entryTableName, array("SUM($valuesTableName.value) as user_aggregate"))
                    ->join($valuesTableName, "$entryTableName.entry_id = $valuesTableName.item_id")
                    ->where("$valuesTableName.field_id in (?)", $field_ids)
                    ->where("$entryTableName.publish=1")
                    ->where("$entryTableName.project_id IS NULL")
                    ->where("$entryTableName.user_id IS NOT NULL")
                    ->query()
                    ->fetchColumn();

            $this->view->project_aggregate_value = $project_aggregate_value;
            $this->view->user_aggregate_value = $user_aggregate_value;
            $this->view->totalAggregateValue = (int) $project_aggregate_value + (int) $user_aggregate_value;

            /*
              $project_paginator = Zend_Paginator::factory($projectSelect);
              $user_paginator = Zend_Paginator::factory($userSelect);

              if (!empty($params['projects_page_no'])) {
              $project_paginator->setCurrentPageNumber($params['projects_page_no']);
              $this->view->projects_page_no = $projects_page_no = $params['projects_page_no'];
              }elseif (!empty($params['users_page_no'])) {
              $user_paginator->setCurrentPageNumber($params['users_page_no']);
              $this->view->users_page_no = $user_page_no = $params['users_page_no'];
              } else {
              $project_paginator->setCurrentPageNumber(1);
              $user_paginator->setCurrentPageNumber(1);
              $this->view->projects_page_no = $projects_page_no = 1;
              $this->view->users_page_no = $user_page_no = 1;
              }

              $project_paginator->setItemCountPerPage(1);
              $user_paginator->setItemCountPerPage(1);
             */

            $this->view->project_entries = $project_paginator;
            $this->view->user_entries = $user_paginator;
        }
    }

}

?>