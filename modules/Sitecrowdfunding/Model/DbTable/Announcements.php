<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Announcements.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Announcements extends Engine_Db_Table {

    protected $_name = 'sitecrowdfunding_announcements';
    protected $_rowClass = 'Sitecrowdfunding_Model_Announcement';

    public function announcements($project_id, $showExpired = 0, $limit, $fetchColumns = array()) {

        $announcementTableName = $this->info('name');

        $select = $this->select();
        
        if (!empty($fetchColumns)) {
            $select->from($announcementTableName, $fetchColumns);
        }
        $select->where('project_id = ?', $project_id);
        
        if (empty($showExpired)) {
            $select->where($announcementTableName . '.status = ?', 1)
                    ->where($announcementTableName . '. startdate <= ?', date('y-m-d'))
                    ->where($announcementTableName . '. expirydate >= ?', date('y-m-d'));
        }
        $select->order($announcementTableName . '.creation_date DESC');

        if (!empty($limit)) {
            $select->limit($limit);
        }

        return $this->fetchAll($select);
    }

}