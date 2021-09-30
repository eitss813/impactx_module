<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Claims.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Model_DbTable_Claims extends Engine_Db_Table {
  protected $_rowClass = "Sesblog_Model_Claim"; 
  
  /**
   * Gets a paginator for sesblogs
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */
  public function getblogclaimsPaginator($params = array()) {
  
    $paginator = Zend_Paginator::factory($this->getblogclaimsSelect($params));
    if( !empty($params['page']) )
    $paginator->setCurrentPageNumber($params['page']);
    if( !empty($params['limit']) )
    $paginator->setItemCountPerPage($params['limit']);
    if( empty($params['limit']) ) {
      $page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.page', 10);
      $paginator->setItemCountPerPage($page);
    }
    return $paginator;
  }
  
  /**
   * Gets a select object for the user's sesblog entries
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Db_Table_Select
   */
  public function getblogclaimsSelect($params = array())
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $blogTableName = $this->info('name');
    $select = $this->select()->where('user_id =?', $viewer->getIdentity())->where('status =?', 0)->where('blog_id !=?', 0);
    return $select;
  }
  
  public function claimCount() {
     $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
     return $this->select()
     ->from($this->info('name'), 'claim_id')
     ->where('user_id =?', $viewerId)
     ->where('status =?', 0)->query()->fetchColumn();
  }
  
}
