<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Parameters.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
class Sesblog_Model_DbTable_Parameters extends Engine_Db_Table {

  protected $_rowClass = 'Sesblog_Model_Parameter';
	protected $_name  = 'sesblog_parameters';
	function getParameterResult($params = array()){
		if (isset($params['column_name']))
      $columnName = $params['column_name'];
    else
      $columnName = '*';
    $select = $this->select()
            ->from($this->info('name'), $columnName);
    if (isset($params['category_id']))
      $select = $select->where('category_id = ?', $params['category_id']);   
      return $select->query()->fetchAll();
	}
	
}
