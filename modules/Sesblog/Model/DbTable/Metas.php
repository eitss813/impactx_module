<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Metas.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
class Sesblog_Model_DbTable_Metas extends Engine_Db_Table {

  protected $_name = 'sesblog_blog_fields_meta';
  protected $_rowClass = 'Sesblog_Model_Meta';
	
	public function getMetaData($options_id = null){
		if(!$options_id)
			return null;
		$tableName = $this->info('name');
		$mapTableName = Engine_Api::_()->getDbTable('maps','sesblog')->info('name');
		$select = $this->select()->from($tableName)
							->setIntegrityCheck(false)
							->join($mapTableName, $mapTableName . '.child_id = ' . $tableName . '.field_id', null)
							->order($mapTableName.'.order')
							->where($mapTableName.'.option_id =?',$options_id);
		return $this->fetchAll($select);
	}
		
  public function profileFieldId() {
    return $this->select()
                    ->from($this->info('name'), array('field_id'))
                    ->where('alias = ?', 'profile_type')
                    ->where('type = ?', 'profile_type')
                    ->query()
                    ->fetchColumn();
  }
  
	public function reviewProfileFieldId() {
	  $table = Engine_Api::_()->fields()->getTable('sesblog_review', 'metas');
		return $table->select()
		->from($table->info('name'), array('field_id'))
		->where('alias = ?', 'profile_type')
		->where('type = ?', 'profile_type')
		->query()
		->fetchColumn();
	}

}
