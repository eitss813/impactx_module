<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Pages.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Services extends Engine_Db_Table {

  protected $_rowClass = "Sitepage_Model_Service";

  /**
   * Return services which have this page_id 
   *
   * @param int page_id
   * @return Zend_Db_Table_Select
   */
  public function getPageService($page_id) {
    $select = $this->select()
    ->where('page_id = ?', $page_id);
    return $this->fetchAll($select)->toArray();
  }

  public function countPageServices($page_id) {
    $select = $this
    ->select()
    ->from($this->info('name'), array('count(*) as count'))
    ->where("page_id = ?", $page_id);

    return $select->query()->fetchColumn();
  }

  /**
   * Return service is existing or not.
   *
   * @return Zend_Db_Table_Select
   */
  public function checkService($title,$page_id) {

    //MAKE QUERY
    $hasService = $this->select()
    ->from($this->info('name'), array('title'))
    ->where('title=?',$title)
    ->where('page_id=?',$page_id)
    ->query()
    ->fetchColumn();

    //RETURN RESULTS
    return $hasService;
  }

  // get lising according to requerment
  public function getListing($params = array()) {

    $limit = 10;
    $table = Engine_Api::_()->getDbtable('services', 'sitepage');
    $sitepageTableName = $table->info('name');
    
    $columns = array('service_id','title','body','photo_id', 'duration','duration_type');
    $select = $table->select()
    ->from($sitepageTableName, $columns);

    if (isset($params['page_id']) && !empty($params['page_id'])) {
      $select->where($sitepageTableName . '.page_id = ?', $params['page_id']);
    }
    $select->group($sitepageTableName . '.service_id');
    $paginator = Zend_Paginator::factory($select);
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    $paginator->setItemCountPerPage($params['count']);
    return $paginator;
  }

  }