<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Subscribers.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Model_DbTable_Subscribers extends Engine_Db_Table {

    protected $_rowClass = "Sesnewsletter_Model_Subscriber";

    public function getResult($param = array()) {

        $tableName = $this->info('name');
        $select = $this->select()
                ->from($tableName)->order('subscriber_id DESC');
        if(!empty($param['type_id'])) {
            $select->where('type_id =?', $param['type_id']);
        }
        if(!empty($param['resource_id'])) {
            $select->where('resource_id =?', $param['resource_id']);
        }
        if(!empty($param['resource_type'])) {
            $select->where('resource_type =?', $param['resource_type']);
        }
        if(!empty($param['email'])) {
            $select->where('email =?', $param['email']);
        }
        if(isset($param['admin']) && !empty($param['admin'])) {
            $select->group('email');
        }
        if (isset($param['fetchAll'])) {
            $select->where('enabled =?', 1);
            return $this->fetchAll($select);
        }
        return Zend_Paginator::factory($select);
    }

    public function getTypeSubscribersEmails($param = array()) {

        $campaign = $param['campaign'];

        $table = Engine_Api::_()->getItemTable('user');
        $tableName = $table->info('name');

        //Other Members
        if($campaign->choose_member == '4') {

            $select = $table->select()
                ->from($table->info('name'), 'user_id')
                ->where('enabled = ?', true); // Do not email disabled members

            $level_ids = json_decode($campaign->member_levels);
            if (is_array($level_ids) && !empty($level_ids)) {
                $select->where('level_id IN (?)', $level_ids);
            }

            if(!empty($campaign->networks) && count(json_decode($campaign->networks)) > 0) {

                $resultsN = Engine_Api::_()->sesnewsletter()->networksJoinedMembers(json_decode($campaign->networks));
                $networkUserIds = array();
                if(count($resultsN) > 0) {
                    foreach($resultsN as $resultN) {
                        $networkUserIds[] = $resultN->user_id;
                    }
                    $select->where('user_id IN (?)', $networkUserIds);
                }
            }

            if(!empty($campaign->profile_types) && count(json_decode($campaign->profile_types)) > 0) {
                $resultsP = Engine_Api::_()->sesnewsletter()->profileTypesMembers(json_decode($campaign->profile_types));
                $profiletypeUserIds = array();
                if(count($resultsP) > 0) {
                    foreach($resultsP as $resultP) {
                        $profiletypeUserIds[] = $resultP->item_id;
                    }
                    $select->where('user_id IN (?)', $profiletypeUserIds);
                }
            }

            $results = $table->fetchAll($select);
            $userIds = array();
            foreach($results  as $result) {
                $userIds[] = $result->user_id;
            }
        }
        //Site Members
        else if($campaign->choose_member == '1') {
            $select = $table->select()
                ->from($table->info('name'), 'user_id')
                ->where('enabled = ?', true); // Do not email disabled members
            $results = $table->fetchAll($select);
            $userIds = array();
            foreach($results  as $result) {
                $userIds[] = $result->user_id;
            }
        }

        $emails = array();
        $tableName = $this->info('name');
        $select = $this->select()
                ->from($tableName)->order('subscriber_id DESC');
        if(!empty($param['type_id'])) {
            $select->where('type_id =?', $param['type_id']);
        }

        if($campaign->choose_member == '2') {
            $select->where('resource_id = ?', 0);
        }

        if(count($userIds)) {
            $select->where('resource_id IN (?)', $userIds);
        }

        $select->where('enabled =?', 1);
        $results = $this->fetchAll($select);
        foreach($results as $result) {
            $emails[] = $result->email;
        }
        return $emails;

    }

    public function isExist($email) {

        return $this->select()
                        ->from($this->info('name'), array('subscriber_id'))
                        ->where('email =?', $email)
                        ->query()
                        ->fetchColumn();
    }

    public function isExistType($email, $type_id) {

        return $this->select()
                        ->from($this->info('name'), array('subscriber_id'))
                        ->where('email =?', $email)
                        ->where('type_id =?', $type_id)
                        ->query()
                        ->fetchColumn();
    }
}
