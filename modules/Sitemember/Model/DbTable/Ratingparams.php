<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ratingparams.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Model_DbTable_Ratingparams extends Engine_Db_Table {

  protected $_rowClass = 'Sitemember_Model_Ratingparam';

  /**
   * Review parameters
   *
   * @param Array $profiletypeIdsArray
   * @param Varchar $resource_type
   * @return Review parameters
   */
  public function memberParams($profiletypeIdsArray = array(), $resource_type = null) {

    if (empty($profiletypeIdsArray)) {
      return null;
    }

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('ratingparam_id', 'ratingparam_name'))
            ->where("profiletype_id IN (?)", (array) $profiletypeIdsArray)
            ->order("profiletype_id");

    if (!empty($resource_type)) {
      $select->where("resource_type =?", $resource_type);
    }

    //RETURN RESULTS
    return $this->fetchAll($select);
  }

}