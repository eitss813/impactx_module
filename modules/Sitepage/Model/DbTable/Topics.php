<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topics.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Topics extends Engine_Db_Table {

  protected $_rowClass = 'Sitepage_Model_Topic';

  /**
   * Return page topics
   *
   * @param int $page_id
   * @return Zend_Db_Table_Select
   */
  public function getPageTopics($page_id, $params=array()) {

    $select = $this->select()
            ->where('page_id = ?', $page_id)
            ->order('sticky DESC')
            ->order('modified_date DESC');
    if (isset($params['resource_type']) && $params['resource_type'])
      $select->where('resource_type = ?', $params['resource_type']);
    if (isset($params['resource_id']) && $params['resource_id'])
      $select->where('resource_id = ?', $params['resource_id']);

    return Zend_Paginator::factory($select);
  }

}

?>