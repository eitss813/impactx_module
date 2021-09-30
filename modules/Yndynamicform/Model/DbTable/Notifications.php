<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/3/2016
 * Time: 4:59 PM
 */
class Yndynamicform_Model_DbTable_Notifications extends Engine_Db_Table
{
    protected $_rowClass = 'Yndynamicform_Model_Notification';

    public function getNotifications($params)
    {
        $table = Engine_Api::_()->getDbtable('notifications', 'yndynamicform');
        $rName = $table->info('name');

        $select = $this -> select() -> from($rName) -> setIntegrityCheck(true);

        // Form id
        if (!empty($params['form_id'])) {
            $select->where("$rName.form_id = ?", $params['form_id']);
        }

        // Title
        if (!empty($params['name'])) {
            $select->where("$rName.name LIKE ?", "%{$params['name']}%");
        }

        // Isenable ?
        if (isset($params['status']) && is_numeric($params['status'])) {
            $select->where($rName . '.enable = ?', $params['status']);
        }

        $select->order("$rName.order ASC");

        return $table->fetchAll($select);
    }
}