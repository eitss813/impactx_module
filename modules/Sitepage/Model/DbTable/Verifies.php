<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Verifies.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_Model_DbTable_Verifies extends Engine_Db_Table {

  protected $_name = 'sitepage_verifies';
  protected $_rowClass = 'Sitepage_Model_Verify';

  public function hasVerify($resource_id) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->select()->from($this->info('name'), array('admin_approve', 'verify_id'))
            ->where('resource_type = ?', 'page')
            ->where('resource_id = ?', $resource_id)
            ->where('poster_id = ?', $viewer_id);
    return $this->fetchRow($select);
  }

  public function getVerifyCount($resource_id) {
    return $this->select()
                    ->from($this->info('name'), array('COUNT(verify_id) as verifyCount'))
                    ->where('status = ?', 1)
                    ->where('resource_type = ?', 'page')
                    ->where('resource_id = ?', $resource_id)
                    ->where('admin_approve = ?', 1)
                    ->query()
                    ->fetchColumn();
  }

  public function getVerifyPaginator($params = array()) {
    $paginator = Zend_Paginator::factory($this->getVerifySelect($params));
    $paginator->setItemCountPerPage(20);
    return $paginator;
  }

  public function getVerifySelect($params = array()) {

    $select = $this->select()
            ->order('verify_id DESC');

    if (isset($params['admin_approve'])) {
      $select->where('admin_approve = ?', $params['admin_approve']);
    }
    if (isset($params['resource_id'])) {
      $select->where("resource_id =?", $params['resource_id']);
    }
    if (isset($params['status'])) {
      $select->where("status =?", $params['status']);
    }

    return $select;
  }
  
  public function getMostVerified($params = array("resource_type" => "user")) {
    $select = $this->select()
            ->from($this->info('name'), array('verify_id', 'resource_type', 'resource_id', 'COUNT(resource_id) as verifyCount'))
            ->where("status =?", 1)
            ->where('admin_approve = ?', 1);

    if (isset($params['resource_type']) && !empty($params['resource_type']))
      $select->where("resource_type =?", $params['resource_type']);
    
    if (isset($params['limit']) && !empty($params['limit']))
      $select->limit($params['limit']);
    
    $select->group('resource_id');
    $select->order('verifyCount DESC');
    
    $queryArray = $select->query()->fetchAll();
    $queryArray = !empty($queryArray)? $queryArray: array();
    return $queryArray;
  }


}