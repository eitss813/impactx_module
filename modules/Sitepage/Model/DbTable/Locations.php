<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locations.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Locations extends Engine_Db_Table {

  protected $_rowClass = "Sitepage_Model_Location";

  /**
   * Get location
   *
   * @param array $params
   * @return object
   */
  public function getLocation($params=array()) {

    $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);
    if ($locationFieldEnable) {
   
      $locationName = $this->info('name');

      $select = $this->select();
      if (isset($params['id']) && isset($params['mapshow']) && $params['mapshow'] == 'Map Tab') {
        $select->where('page_id = ?', $params['id']);
        if (!empty($params['mainlocationId'])) {
        $select->where('location_id <> ?', $params['mainlocationId']);
        }
        
        $select->order('location_id DESC');
        return Zend_Paginator::factory($select);
      }
      elseif (isset($params['id'])) {
        $select->where('page_id = ?', $params['id']);
        if (isset($params['location_id']) && !empty($params['location_id'])) {
					$select->where('location_id = ?', $params['location_id']);
        }
        return $this->fetchRow($select);
      } 


      if (isset($params['page_ids'])) {

        $idsStr = (string) ( is_array($params['page_ids']) ? "'" . join("', '", $params['page_ids']) . "'" : $params['page_ids'] );

        $select->where('page_id IN (?)', new Zend_Db_Expr($idsStr));
        return $this->fetchAll($select);
      }
    }
  }
  
  public function getLocationId ($page_id, $location) {

		$locationName = $this->info('name');
		$select = $this->select()->from($locationName, 'location_id');
		$location_id = $select->where('page_id = ?', $page_id)->where('location = ?', $location)->query()
												->fetchColumn();
		return $location_id;

  }

    public function getPartnerLocationsByPageId($id){

        $partnerPagesTable = Engine_Api::_()->getDbtable('partners', 'sitepage');
        $partnerPagesTableName = $partnerPagesTable->info('name');

        $locationName = $this->info('name');
        $page_select = $this->select()
            ->from($locationName, '*')
            ->join($partnerPagesTableName, "$partnerPagesTableName.partner_page_id = $locationName.page_id", null)
            ->where("$partnerPagesTableName.page_id in (?) ", $id);

        return $this->fetchAll($page_select);
    }

}