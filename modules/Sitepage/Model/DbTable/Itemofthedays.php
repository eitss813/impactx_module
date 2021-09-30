<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Itemofthedays.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Itemofthedays extends Engine_Db_Table {

  protected $_rowClass = "Sitepage_Model_Itemoftheday";

  /**
   * Get list of page of the day items
   * @param array $params : contain ordering info
   * @param int $resource_id : item id
   * @param char $resource_type : item type
   */
  public function getItemOfDayList($params=array(), $resource_id, $resource_type) {

		//GET ITEM OF THE DAY TABLE NAME
    $itemofthedayName = $this->info('name');
	
		//GET ITEM TABLE INFO
    $itemTable = Engine_Api::_()->getItemTable($resource_type);
		$itemTableName = $itemTable->info('name');

		//MAKE QUERY
    $select = $this->select()
            ->setIntegrityCheck(false)  	
            ->from($itemofthedayName)
            ->join($itemTableName, $itemTableName . ".$resource_id = " . $itemofthedayName . '.resource_id')
						->where($itemofthedayName.".resource_type = ?", $resource_type);

    $select->order((!empty($params['order']) ? $params['order'] : 'start_date' ) . ' ' . (!empty($params['order_direction']) ? $params['order_direction'] : 'DESC' ));

		//RETURN RESULTS
    return $paginator = Zend_Paginator::factory($select);
  }
}
?>