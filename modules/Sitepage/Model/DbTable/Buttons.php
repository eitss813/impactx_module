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
class Sitepage_Model_DbTable_Buttons extends Engine_Db_Table {

	protected $_rowClass = "Sitepage_Model_Button";
	public function getPageButton($page_id)
	{
		$select = $this->select()->where('page_id = ?',$page_id);
		$row = $this->fetchRow($select);
		if (!empty($row) || !is_null($row)) {
			return $row;
		} else {
			return false;
		}
	}
	public function hasButton($page_id)
	{
		$select = $this->select()->where('page_id = ?',$page_id);
		$row = $this->fetchRow($select);
		if (!empty($row) || !is_null($row)) {
			return true;
		} else {
			return false;
		}
	}
	public function deletePageButton($page_id)
	{
		$this->delete(array(
			'page_id = ?' => $page_id
			));
	} 
}