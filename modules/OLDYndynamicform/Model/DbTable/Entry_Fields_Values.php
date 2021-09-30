<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/23/2016
 * Time: 5:36 PM
 */
class Yndynamicform_Model_DbTable_Entry_Fields_Values extends Engine_Db_Table
{

    protected $_rowClass = 'Yndynamicform_Model_Entry_Fields_Values';
    protected $_name = 'yndynamicform_entry_fields_values';

    public function getOptionsLabel($item_id) {
        return $this->select()
            ->from($this->info('name'), array('*'))
            ->where('item_id = ?', $item_id)
            ->query()
            ->fetchColumn();
    }
}

?>