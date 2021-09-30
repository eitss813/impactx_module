<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Profilemaps.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Model_DbTable_Profilemaps extends Engine_Db_Table {

  protected $_rowClass = "Sitemember_Model_Profilemap";
  
  public function getOptionIds($params = array()) {
    
		return $this->select()
					->from($this->info('name'), $params['fetchColumn'])
					->where('option_id = ?', $params['option_id'])
					->query()
					->fetchColumn();
  }
}