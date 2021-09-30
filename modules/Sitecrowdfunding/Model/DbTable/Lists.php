<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Lists.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Lists extends Engine_Db_Table {

    protected $_rowClass = 'Sitecrowdfunding_Model_List';

    public function


    getProjectsUserLead($user_id) {
        $table = Engine_Api::_()->getItemTable('sitecrowdfunding_list_item');
        $projectListItemTableName = $table->info('name');
        $projectListTableName = $this->info('name');
        $select = $this->select();
        $select = $select
                ->setIntegrityCheck(false)
                ->from($projectListTableName, array('owner_id'));

        $select->join($projectListItemTableName, "$projectListTableName.list_id = $projectListItemTableName.list_id   ", array());
        $select->where('child_id = ?', $user_id);
        $projectsList = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return implode(",", $projectsList);
    }

    public function getLeaders($list_id) {
        $table = Engine_Api::_()->getItemTable('sitecrowdfunding_list_item');
        $projectListItemTableName = $table->info('name');

        $select = $table->select()
                ->from($projectListItemTableName, array('child_id'));
        $select->where('list_id = ?', $list_id);
        $projectsList = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return implode(",", $projectsList);
    }

}