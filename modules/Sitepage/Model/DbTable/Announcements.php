<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Announcements.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Announcements extends Engine_Db_Table {

  protected $_name = 'sitepage_announcements';
  protected $_rowClass = 'Sitepage_Model_Announcement';

  public function announcements($params = array(), $fetchColumns = array()) {
  
    $announcementTableName = $this->info('name');
    
    $select = $this->select();
    
    if (!empty($fetchColumns)) {
        $select->from($announcementTableName, $fetchColumns);
    }    
    
    if(isset($params['page_id']) && !empty($params['page_id'])) {
        $select->where('page_id = ?', $params['page_id']);
    }   
    
    if (isset($params['hideExpired']) && !empty($params['hideExpired'])) {
			$select->where($announcementTableName . '.status = ?', 1)
			      ->where($announcementTableName . '. startdate <= ?', date('y-m-d'))
            ->where($announcementTableName . '. expirydate >= ?', date('y-m-d'));    
    }
    
    if (isset($params['limit']) && !empty($params['limit'])) {
        $select->limit($params['limit']);
    }
    
    $select->order($announcementTableName . '.creation_date DESC');
    
    return $this->fetchAll($select);
  }
}