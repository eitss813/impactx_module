<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Reviewmetas.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Model_DbTable_Reviewmetas extends Engine_Db_Table {

  protected $_name = 'sesblog_review_fields_meta';
  protected $_rowClass = 'Sesblog_Model_Reviewmeta';

  public function profileFieldId() {

    return $this->select()
                    ->from($this->info('name'), array('field_id'))
                    ->where('alias = ?', 'profile_type')
                    ->where('type = ?', 'profile_type')
                    ->query()
                    ->fetchColumn();
  }
  
}
