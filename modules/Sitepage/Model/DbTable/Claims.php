<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Claims.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Claims extends Engine_Db_Table {

  protected $_rowClass = 'Sitepage_Model_Claim';

	/**
   * Return status
   *
   * @param array params
   * @return status
   */	 
  public function getClaimStatus($params) {
  	
  	$select = $this->select()->from($this->info('name'), 'status');    
		if(isset($params['page_id']) && !empty($params['page_id'])) {
			$select = $select->where('page_id = ?', $params['page_id']);
		}
		if(isset($params['viewer_id']) && !empty($params['viewer_id'])) {
			$select = $select->where('user_id = ?', $params['viewer_id']);
		}
    return $this->fetchRow($select);
  }

	/**
   * Return viewer claim id
   *
   * @param int $viewer_id
   */	
	public function getViewerClaims($viewer_id) {

    $claim_id = 0;
    $claim_id = $this
              ->select()
              ->from($this->info('name'), array('claim_id'))
              ->where("user_id = ?", $viewer_id)
              ->order('creation_date')
              ->query()
              ->fetchColumn();
    return $claim_id;
	}
  
  /**
   * Gets claim pages 
   *
   * @param string $viewer_id
   * @param  Zend_Db_Table_Select
   */
  public function getMyClaimPages($viewer_id) {
  	
    //GET PAGE TABLE AND ITS NAME
    $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $tablePageName = $tablePage->info('name');
    $tableClaimName = $this->info('name');
    //SELECT
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($this->info('name'))
            ->joinInner($tablePageName, "$tableClaimName.page_id = $tablePageName.page_id", array('page_id', 'photo_id', 'title', 'owner_id'))
            ->where($tableClaimName . '.user_id = ?', $viewer_id)
            ->order('claim_id DESC');

    return Zend_Paginator::factory($select);
  }
}

?>