<?php

class Impactx_Api_Core extends Core_Api_Abstract
{
    /*
     * This method is the copy from the Yndynamicform/Entries.php file, and I modified it according to my requirements.
     */
    function getTotalSubmittedEntries($form_id) {
        $table = Engine_Api::_()->getDbtable('entries', 'yndynamicform');
        $rName = $table->info('name');

        $select = $table->select()->from($rName)
            ->where('form_id = ?', $form_id)
            ->where('submission_status = \'submitted\' OR submission_status = \'preview\'')
            ->order('creation_date DESC');

        return count($table->fetchAll($select));
      
    }
    
    /*
     * This method is the copy from the Yndynamicform/Entries.php file, and I modified it according to my requirements.
     */
    public function getSubmittedEntries($form_id, $page_no)
    {
        $table = Engine_Api::_()->getDbtable('entries', 'yndynamicform');
        $rName = $table->info('name');

        $select = $table->select()->from($rName)
            ->where('form_id = ?', $form_id)
            ->where('submission_status = \'submitted\' OR submission_status = \'preview\'')
            ->order('creation_date DESC');

        // return $this->fetchAll($select);

         $paginator = Zend_Paginator::factory($select);

        if (!empty($page_no)) {
            $paginator->setCurrentPageNumber($page_no);
        }
        $paginator->setItemCountPerPage(5);
        return $paginator;
    }
    
    /*
     * This method is the copy from the Yndynamicform/Entries.php file, and I modified it according to my requirements.
     */
    public function getEntriesCountByFormId($form_id){
        $table = Engine_Api::_()->getDbtable('entries', 'yndynamicform');
        $rName = $table->info('name');

        $select = $table->select()->from($rName, 'entry_id');

        $select
            ->where("form_id = ?", $form_id)
            ->where('submission_status = \'submitted\' OR submission_status = \'preview\'');


        return $select->query()->fetchAll();

    }
    
    /*
     * This method is the copy from the Yndynamicform/Entries.php file, and I modified it according to my requirements.
     */
    public function metricsSuggestion($page_id, $text) {
        $metrics_array = Engine_Api::_()->getDbtable('metrics', 'sitepage')->getMetricsDataByOrganisationIdAndText($page_id, $text);
        
        $data = array();
        if( !empty($metrics_array) ) {
            foreach ($metrics_array as $metric) {
                
                $metric->metric_name = str_replace("'", "\'", $metric->metric_name);
                $metric->metric_description = str_replace("'", "\'", $metric->metric_description);
                
                $data[] = array(
                    'id' => $metric->metric_id,
                    'label' => str_replace("'", "\'", $metric->metric_name),
                    'metric_name' => str_replace("'", "\'", $metric->metric_name),
                    'metric_description' => str_replace("'", "\'", $metric->metric_description),
                    'metric_unit' => str_replace("'", "\'", $metric->metric_unit),
                    'metric_id' => str_replace("'", "\'", $metric->metric_id),
                    'photo' => ''
                );
            }
        }
        
        return $data;
    }
    
    public function getEntryIDByOwnerIdAndFormId($form_id, $owner_id){
        $table = Engine_Api::_()->getDbtable('entries', 'yndynamicform');
        $rName = $table->info('name');

        $select = $table->select()-> from($rName, 'entry_id');

        $select
            ->where("$rName.form_id = ?", $form_id)
            ->where("$rName.owner_id = ?", $owner_id);
        $select->order('entry_id DESC');

        return $select-> query()->fetchColumn();

    }
    
    public function getAllSubmittedEntries($form_id) {
        $table = Engine_Api::_()->getDbtable('entries', 'yndynamicform');
        $rName = $table->info('name');

        $select = $table->select()->from($rName)
            ->where('form_id = ?', $form_id)
            ->order('creation_date DESC');

        return $table->fetchAll($select);
    }
    
    /*
     * Update the formula values for the all metrics available in updated form field.
     */
    public function updateFormulaOnEditNumberField($params) {
        $option_id = $params['option_id'];
        
        $tempParams = array();
        $fieldMaps = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry')->getRowsMatching('option_id', $option_id);
        foreach ($fieldMaps as $item) {
            $field = $item->getChild();
            $values = $field->toArray();
            
            if( isset($values['type']) && !empty($values['type']) && ($values['type'] == 'float') ) {
                $tempParams[$values['field_id']] = ($params['field_id'] == $values['field_id'])? $params['label']: $values['label'];
            }
        }
        
        foreach ($fieldMaps as $item) {
            $field = $item->getChild();
            $values = $field->toArray();
            
            if( isset($values['type']) && !empty($values['type']) && ($values['type'] == 'metrics') ) {
                $own_formula_input = $own_actual_formula = $values['config']['own_formula_by_id'];
                if( !empty($tempParams) && !empty($own_actual_formula) && !empty($own_formula_input) ) {
                    foreach($tempParams as $field_id => $label) {
                        $own_formula_input = str_replace("field_id_" . $field_id, $label, $own_formula_input);
                        $own_actual_formula = str_replace("field_id_" . $field_id, '[' . $label . ']', $own_actual_formula);
                    }
                }
                
                
                $values['config']['own_formula_input'] = $own_formula_input;
                $values['config']['own_actual_formula'] = $own_actual_formula;
                
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->update('engine4_yndynamicform_entry_fields_meta', array(
                    'config' => json_encode($values['config']),
                ), array(
                    'field_id = ?' => $values['field_id'],
                ));
            }
        }
        
        return;
    }
    
    /*
     * Validate the metrics formula on number field delete
     */
    public function validateMetricsFormulaOnNumFieldDeletion($map) {
        $option_id = $map->option_id;
        $child_id = $map->child_id;
        
        $fieldMaps = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry')->getRowsMatching('option_id', $option_id);
        foreach ($fieldMaps as $item) {
            $field = $item->getChild();
            $values = $field->toArray();
            
            if( isset($values['type']) && !empty($values['type']) && ($values['type'] == 'metrics') && isset($values['config']['metric_aggregate_fields']) && !empty($values['config']['metric_aggregate_fields']) && in_array($child_id, $values['config']['metric_aggregate_fields']) )
                return true;
        }

        return false;
    }
}

