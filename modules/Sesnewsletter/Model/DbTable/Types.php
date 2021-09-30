<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Types.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Model_DbTable_Types extends Engine_Db_Table {

    protected $_rowClass = "Sesnewsletter_Model_Type";

    public function getResult($param = array()) {

        $tableName = $this->info('name');
        $select = $this->select()
                ->from($tableName)
                ->order('type_id DESC');
        if (isset($param['fetchAll'])) {
            $select->where('enabled =?', 1);
            return $this->fetchAll($select);
        }
        return Zend_Paginator::factory($select);
    }

    public function getEnabledTypes($param = array()) {

        $tableName = $this->info('name');
        $select = $this->select()
                ->from($tableName)
                ->where('enabled =?', 1);
        return $this->fetchAll($select);
    }

    public function getGuestuserTypes($param = array()) {

        $tableName = $this->info('name');
        $select = $this->select()
                ->from($tableName)
                ->where('enabled =?', 1)
                ->where('guestuser =?', 1);
        return $this->fetchAll($select);
    }

    public function getSignupuserTypes($param = array()) {

        $tableName = $this->info('name');
        $select = $this->select()
                ->from($tableName)
                ->where('enabled =?', 1)
                ->where('singupuser =?', 1)
                ->order('type_id DESC');
        return $this->fetchAll($select);
    }
}
