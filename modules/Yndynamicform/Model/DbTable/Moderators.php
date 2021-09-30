<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/10/2016
 * Time: 9:05 AM
 */
class Yndynamicform_Model_DbTable_Moderators extends Engine_Db_Table
{
    protected $_rowClass = 'Yndynamicform_Model_Moderator';

    public function getModerator($id)
    {
        $select = $this -> select()
                        -> where('moderator_id = ?', $id)
                        -> limit(1);
        return $this -> fetchRow($select);
    }

    public function getModerators($params)
    {
        $table = Engine_Api::_()->getDbtable('moderators', 'yndynamicform');
        $rName = $table->info('name');

        $select = $this -> select() -> from($rName) -> setIntegrityCheck(true);

        // Form id
        if (!empty($params['form_id'])) {
            $select->where("$rName.form_id = ?", $params['form_id']);
        }

        //
        if (!empty($params['moderators'])) {
            $select->where("$rName.moderator_id IN ?", $params['moderators']);
        }

        $select->order("$rName.creation_date ASC");

        return $table->fetchAll($select);
    }

    public function getModeratedForms(User_Model_User $user)
    {
        if ($user instanceof User_Model_User) {
            return $this->select()
                -> from($this -> info('name'), 'form_id')
                -> where('moderator_id = ?', $user -> getIdentity())
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
        } else {
            return null;
        }
    }
}