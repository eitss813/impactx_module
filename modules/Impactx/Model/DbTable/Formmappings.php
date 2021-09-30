<?php

class Impactx_Model_DbTable_Formmappings extends Core_Model_Item_DbTable_Abstract
{
    protected $_rowClass = "Impactx_Model_Formmapping";

    public function getMappingInfoByRoleId($role_id){
        $info = $this->select()
        ->from($this)
        ->where('role_id = ?', $role_id)
        ->limit(1)
        ->query()
        ->fetchAll();

        return !empty($info)? $info[0]: '';

    }

    public function isRoleForm($formId){
        $result = $this->select()
        ->from($this)
        ->where('form_id = ?', $formId)
        ->limit(1)
        ->query()
        ->fetchColumn();

        return !empty($result)? $result[0]: '';
    }

    // get All form id
    public function getAllMappingFormId(){
        $results = $this->select()
        ->from($this,'form_id')
        ->query()
        ->fetchAll();
        $formids = [];
        foreach($results as $result){
            $formids[] = $result['form_id'];
        }

        return !empty($formids)? $formids: array();

    }
// get All form id
public function getAllMappingFormRole(){
    $results = $this->select()
    ->from($this,'role_id',)
    ->query()
    ->fetchAll();
    $roleids = [];
    foreach($results as $result){
        $roleids[] = $result['role_id'];
    }

    return !empty($roleids)? $roleids: array();

}
    // has role associate with form
    public function formHasNoFiled($role_id){
        $info = $this->getMappingInfoByRoleId($role_id);
        $optionTable = Engine_Api::_()->fields()->getTable('yndynamicform_entry', 'maps');
        $optionTableName = $optionTable -> info('name');

        $result = $optionTable->select()
        ->from($optionTableName,'option_id')
        ->where('option_id = ?', $info['option_id'])
        ->query()
        ->fetchColumn();
   
         return !$result? true : FALSE;


    }

}
