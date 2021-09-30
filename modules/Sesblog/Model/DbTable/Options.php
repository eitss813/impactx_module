<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Options.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
class Sesblog_Model_DbTable_Options extends Engine_Db_Table {

  protected $_rowClass = 'Sesblog_Model_Option';
  protected $_name = 'sesblog_blog_fields_options';

  public function getOptionsLabel($option_id) {
    return $this->select()
                    ->from($this->info('name'), array('label'))
                    ->where('option_id = ?', $option_id)
                    ->query()
                    ->fetchColumn();
  }
	public function getAllOptions() {
    return $this->fetchAll($this->select()
                    ->from($this->info('name'), '*'));
							
  }
}
