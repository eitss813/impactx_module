<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Timings.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Timings extends Engine_Db_Table {

	protected $_rowClass = "Sitepage_Model_Timing";
	public function getPageTimings($page_id)
	{
		$timings = array();
		$select = $this->select()->where('page_id = ?',$page_id);
		$row = $this->fetchAll($select);
		foreach ($row as $key => $value) {
			$timings[$value['day']] = 1;
			$timings[$value['day'].'start'] = $value['start'];
			$timings[$value['day'].'end'] = $value['end'];
		}
		return $timings;
	}
	public function deletePageTimings($page_id)
	{
		$this->delete(array(
			'page_id = ?' => $page_id
			));
	}
	public function getTimings($page_id, $day)
	{
		$time = array();
		$select = $this->select()->where('page_id = ?',$page_id)->where('day =?',$day);
		$row = $this->fetchRow($select);
		$time['start'] = $row['start'];
		$time['end'] = $row['end'];
		return $time;
	} 
}