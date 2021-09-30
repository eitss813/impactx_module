<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Posts.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Posts extends Engine_Db_Table {

  protected $_rowClass = 'Sitepage_Model_Post';

	/**
   * Gets all post for topic
   *
   * @param int $page_id
   * @param int $topic_id 
   * @return Zend_Db_Table_Select
   */		  
  public function getPost($page_id, $topic_id, $order) {
    $select = $this->select()
            ->where('page_id = ?', $page_id)
            ->where('topic_id = ?', $topic_id);

   if($order == 1) {
			$select->order('creation_date DESC');
   } else {
      $select->order('creation_date ASC');
   }
            
    return Zend_Paginator::factory($select);        
  }
  
}

?>