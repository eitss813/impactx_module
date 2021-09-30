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
class Sitepage_Model_DbTable_Pages extends Engine_Db_Table {

  protected $_rowClass = "Sitepage_Model_Page";

  public function getPageNamesById($ids){
      if(!empty($ids) && count($ids)){
          $page_select = $this->select()
              ->from($this->info('name'), array('title','page_id'))
              ->where('page_id IN (?)', $ids);
          return $this->fetchAll($page_select);
      }else{
          return array();
      }
  }

    public function getPageDetailsWithInitiativesCountForPageIds($ids){
        $initiativesTable = Engine_Api::_()->getDbtable('initiatives', 'sitepage');
        $initiativesTableName = $initiativesTable->info('name');

        $pagesTableName = $this->info('name');
        if(!empty($ids) && count($ids)){
            $page_select = $this->select()
                ->setIntegrityCheck(false)
                ->from($pagesTableName, array('title','page_id','initiatives_count' => new Zend_Db_Expr("COUNT($initiativesTableName.initiative_id)")))
                ->joinLeft($initiativesTableName , "$initiativesTableName.page_id = $pagesTableName.page_id",null)
                ->group("$pagesTableName.page_id")
                ->where("$pagesTableName.page_id IN (?)", $ids);

            return $this->fetchAll($page_select);
        }else{
            return array();
        }
    }

    public function getPageDetailsWithProjectsCountForPageIds($ids){
        $currentDate = date('Y-m-d H:i:s');

        $pageProjectsTable = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
        $pageProjectsTableName = $pageProjectsTable->info('name');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $projectTable->info('name');

        $pagesTableName = $this->info('name');
        if(!empty($ids) && count($ids)){
            $page_select = $this->select()
                ->setIntegrityCheck(false)
                ->from($pagesTableName, array('title','page_id','projects_count' => new Zend_Db_Expr("COUNT(distinct $pageProjectsTableName.project_id)")))
                ->joinLeft($pageProjectsTableName , "$pageProjectsTableName.page_id = $pagesTableName.page_id",null)
                ->joinLeft($projectTableName , "$projectTableName.project_id = $pageProjectsTableName.project_id",null)
                ->group("$pagesTableName.page_id")
                ->where("$pagesTableName.page_id IN (?)", $ids)
                ->where("$projectTableName.state = ?", 'published')
                ->where("$projectTableName.approved = ?", 1)
                ->where("$projectTableName.start_date <= '$currentDate'");

            return $this->fetchAll($page_select);
        }else{
            return array();
        }
    }
    public function getPageDetailsWithProjectsCustomCountForPageIds($ids){
        $currentDate = date('Y-m-d H:i:s');

        $pageProjectsTable = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
        $pageProjectsTableName = $pageProjectsTable->info('name');

        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $projectTable->info('name');

        $pagesTableName = $this->info('name');
        if(!empty($ids) && count($ids)){
            $page_select = $this->select()
                ->setIntegrityCheck(false)
                ->from($pagesTableName, array('title','page_id','projects_count' => new Zend_Db_Expr("COUNT(distinct $pageProjectsTableName.project_id)")))
                ->joinLeft($pageProjectsTableName , "$pageProjectsTableName.page_id = $pagesTableName.page_id",null)
                ->joinLeft($projectTableName , "$projectTableName.project_id = $pageProjectsTableName.project_id",null)
                ->group("$pagesTableName.page_id")
                ->where("$pagesTableName.page_id =?", $ids)
                ->where("$projectTableName.state = ?", 'published')
                ->where("$projectTableName.approved = ?", 1)
                ->where("$projectTableName.start_date <= '$currentDate'");

            return $this->fetchAll($page_select);
        }else{
            return array();
        }
    }
  public function getOnlyViewablePagesId() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $pages_ids = array();
    $cache = Zend_Registry::get('Zend_Cache');
    $cacheName = 'sitepage_ids_user_id_' . $viewer->getIdentity();
    $data = APPLICATION_ENV == 'development' ? ( Zend_Registry::isRegistered($cacheName) ? Zend_Registry::get($cacheName) : null ) : $cache->load($cacheName);
    if ($data && is_array($data)) {
      $pages_ids = $data;
    } else {
      set_time_limit(0);
      $tableName = $this->info('name');
      $page_select = $this->select()
              ->from($this->info('name'), array('page_id', 'owner_id', 'title', 'photo_id'))
              ->where("{$tableName}.search = ?", 1)
              ->where("{$tableName}.closed = ?", '0')
              ->where("{$tableName}.approved = ?", '1')
              ->where("{$tableName}.declined = ?", '0')
              ->where("{$tableName}.draft = ?", '1');
      if (Engine_Api::_()->sitepage()->hasPackageEnable())
        $page_select->where("{$tableName}.expiration_date  > ?", date("Y-m-d H:i:s"));

      // Create new array filtering out private albums
      $i = 0;
      foreach ($this->fetchAll($page_select) as $page) {
        if (Engine_Api::_()->authorization()->isAllowed($page, $viewer, 'view')) {
          $pages_ids[$i++] = $page->page_id;
        }
      }

      // Try to save to cache
      if (empty($pages_ids))
        $pages_ids = array(0);

      if (APPLICATION_ENV == 'development') {
        Zend_Registry::set($cacheName, $pages_ids);
      } else {
        $cache->save($pages_ids, $cacheName);
      }
    }

    return $pages_ids;
  }

  public function addPrivacyPagesSQl($select, $tableName = null) {
    $privacybase = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.privacybase', 0);
    if (empty($privacybase))
      return $select;

    $column = $tableName ? "$tableName.page_id" : "page_id";

    return $select->where("$column IN(?)", $this->getOnlyViewablePagesId());
  }

  /**
   * Get pages to add as item of the day
   * @param string $title : search text
   * @param int $limit : result limit
   */
  public function getDayItems($title, $limit = 10, $category_id = null) {

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('page_id', 'owner_id', 'title', 'photo_id'));

    if (!empty($category_id)) {
      $select->where('category_id = ?', $category_id);
    }

    $select->where($this->info('name') . ".title LIKE ? OR " . $this->info('name') . ".location LIKE ? ", '%' . $title . '%')
            ->where('closed = ?', '0')
            ->where('declined = ?', '0')
            ->where('approved = ?', '1')
            ->where('draft = ?', '1')
            ->order('title ASC')
            ->limit($limit);

    //FETCH RESULTS
    return $this->fetchAll($select);
  }

  public function getPagesSelectSql($params = array()) {
    $tableName = $this->info('name');
    $select = $this->select()
            ->where("{$tableName}.search = ?", 1)
            ->where("{$tableName}.closed = ?", '0')
            ->where("{$tableName}.approved = ?", '1')
            ->where("{$tableName}.declined = ?", '0')
            ->where("{$tableName}.draft = ?", '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable())
      $select->where("{$tableName}.expiration_date  > ?", date("Y-m-d H:i:s"));

    if (isset($params['limit']) && !empty($params['limit']))
      $select->limit($params['limit']);
    return $select;
  }

  public function getTagCloud($limit = 100, $category_id, $count_only = 0) {

    $tableTagmaps = 'engine4_core_tagmaps';
    $tableTags = 'engine4_core_tags';

    $tableSitepages = $this->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($tableSitepages, array(''))
            ->joinInner($tableTagmaps, "$tableSitepages.page_id = $tableTagmaps.resource_id", array('COUNT(engine4_core_tagmaps.resource_id) AS Frequency'))
            ->joinInner($tableTags, "$tableTags.tag_id = $tableTagmaps.tag_id", array('text', 'tag_id'))
            ->where($tableSitepages . '.approved = ?', "1");
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1);

    if ($stusShow == 0) {
      $select = $select->where($tableSitepages . '.closed = ?', "0");
    }

    if (Engine_Api::_()->sitepage()->hasPackageEnable())
      $select->where($tableSitepages . '.expiration_date  > ?', date("Y-m-d H:i:s"));

    $select->where($tableSitepages . ".search = ?", 1);
    $select = $select->where($tableSitepages . '.draft = ?', "1")
            ->where($tableSitepages . '.declined = ?', '0')
            ->where($tableTagmaps . '.resource_type = ?', 'sitepage_page')
            ->group("$tableTags.text")
            ->order("Frequency DESC");

    if (!empty($category_id)) {
      $select = $select->where($tableSitepages . '.	category_id =?', $category_id);
    }

    //Start Network work
    $select = $this->getNetworkBaseSql($select);
    //End Network work

    if (!empty($count_only)) {
      $total_results = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      return Count($total_results);
    }

    $select = $select->limit($limit);

    return $select->query()->fetchAll();
  }

  /**
   * Return pages which have this category and this mapping
   *
   * @param int category_id
   * @param int profile_type
   * @return Zend_Db_Table_Select
   */
  public function getCategoryPage($category_id, $profile_type) {
    $select = $this->select()
            ->from($this->info('name'), 'page_id')
            ->where('category_id = ?', $category_id)
            ->where('profile_type != ?', $profile_type);
    return $this->fetchAll($select)->toArray();
  }

  /**
   * Return pages which can user have to choice to claim
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
  public function getSuggestClaimPage($params) {
    //SELECT
    $select = $this->select()
            ->from($this->info('name'), array('page_id', 'title', 'userclaim', 'photo_id', 'owner_id'))
            ->where('approved = ?', '1')
            ->where('declined = ?', '0')
            ->where('draft = ?', '1');

    if (isset($params['page_id']) && !empty($params['page_id'])) {
      $select = $select->where('page_id = ?', $params['page_id']);
    }

    if (isset($params['viewer_id']) && !empty($params['viewer_id'])) {
      $select = $select->where('owner_id != ?', $params['viewer_id']);
    }

    if (isset($params['title']) && !empty($params['title'])) {
      $select = $select->where('title LIKE ? ', '%' . $params['title'] . '%');
    }

    if (isset($params['limit']) && !empty($params['limit'])) {
      $select = $select->limit($params['limit']);
    }

    if (isset($params['orderby']) && !empty($params['orderby'])) {
      $select = $select->order($params['orderby']);
    }
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1);
    if ($stusShow == 0) {
      $select = $select
              ->where('closed = ?', '0');
    }
    if (Engine_Api::_()->sitepage()->hasPackageEnable())
      $select->where('expiration_date  > ?', date("Y-m-d H:i:s"));

    //FETCH
    return $this->fetchAll($select);
  }

  /**
   * Get Popular location base on city and state
   *
   */
  public function getPopularLocation($items_count, $category_id) {
    $limit = $items_count;
    $pageName = $this->info('name');
    $locationTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
    $locationName = $locationTable->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($pageName, null)
            ->join($locationName, "$pageName.page_id = $locationName.page_id", array("city", "count(city) as count_location", "state", "count(state) as count_location_state"))
            ->group("city")
            ->group("state")
            ->order("count_location DESC")
            ->limit($limit);
    if (!empty($category_id)) {
      $select = $select->where($pageName . '.	category_id =?', $category_id);
    }
    $select->where($pageName . '.approved = ?', '1')
            ->where($pageName . '.declined = ?', '0')
            ->where($pageName . '.draft = ?', '1');
    $select->where($pageName . ".search = ?", 1);
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1);
    if ($stusShow == 0) {
      $select = $select
              ->where($pageName . '.closed = ?', '0');
    }
    if (Engine_Api::_()->sitepage()->hasPackageEnable())
      $select->where($pageName . '.expiration_date  > ?', date("Y-m-d H:i:s"));

    //Start Network work
    $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
    //End Network work

    return $this->fetchAll($select);
  }

  /**
   * Get Arcive Pages
   *
   * @param int $user_id
   * @return object
   */
  public function getArchiveSitepage($user_id = null) {

    $rName = $this->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($rName, array('creation_date'))
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.declined = ?', '0')
            ->where($rName . '.draft = ?', '1');
    $select->where($rName . ".search = ?", 1);

    if (Engine_Api::_()->sitepage()->hasPackageEnable())
      $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));

    if (!empty($user_id)) {
      $select->where($rName . '.owner_id = ?', $user_id);
    }

    //Start Network work
    $select = $this->getNetworkBaseSql($select);
    //END NETWORK WORK
    return $this->fetchAll($select);
  }

    /**
     * Get Pages relative to page owner
     *
     * @param int $page_id
     * @param int $owner_id
     * @return objects
     */
    public function getUserPages($user_id) {

        $rName = $this->info('name');

        $select = $this->select()->from($rName, array('page_id'))
            ->where($rName . '.owner_id =?', $user_id)
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.declined = ?', '0');
        $select->order("creation_date DESC");

        return $this->fetchALL($select);
    }

    public function getUserPageIds($user_id) {

        $rName = $this->info('name');

        $select = $this->select()->from($rName, array('page_id'))
            ->where($rName . '.owner_id =?', $user_id)
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.declined = ?', '0');
        $select->order("creation_date DESC");

        $ids = array();
        $pages_ids = $this->fetchALL($select);
        foreach ($pages_ids as $id) {
            $ids[] = $id->page_id;
        }
        return $ids;

    }

    public function getUserAdminPages($user_id) {

        $pagemanageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');

        if(count($this->getUserPageIds($user_id)) <= 0 ){
            $select = $pagemanageadminsTable->select()
                ->where('user_id = ?', $user_id);
        }else{
            $select = $pagemanageadminsTable->select()
                ->where('user_id = ?', $user_id)
                ->where('page_id not in (?)', $this->getUserPageIds($user_id));
        }

        return $pagemanageadminsTable->fetchAll($select);
    }

    public function getUserPagesAndAdminPages($user_id) {

        $streamTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $db = $streamTable->getAdapter();
        $union = new Zend_Db_Select($db);

        // user pages
        $rName = $this->info('name');
        $select1 = $this->select()->from($rName, array('page_id'))
            ->setIntegrityCheck(false)
            ->where($rName . '.owner_id =?', $user_id)
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.declined = ?', '0')
            ->order("creation_date DESC");

        // admin pages
        $pagemanageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $pagemanageadminsTableName = $pagemanageadminsTable->info('name');
        $select2 = $pagemanageadminsTable->select()->from($pagemanageadminsTableName, array('page_id'))
            ->setIntegrityCheck(false)
            ->where("$pagemanageadminsTableName.user_id = ?", $user_id);

        // union select queries
        $select = $union->union(array('('.$select1->__toString().')','('.$select2->__toString().')'));

        $select = $union->order('page_id DESC')->limit(3);

        $page_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        return $page_ids;

    }

    public function getUserPagesAndAdminPagesCount($user_id) {

        $streamTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $db = $streamTable->getAdapter();
        $union = new Zend_Db_Select($db);

        // user pages
        $rName = $this->info('name');
        $select1 = $this->select()->from($rName, array('page_id'))
            ->setIntegrityCheck(false)
            ->where($rName . '.owner_id =?', $user_id)
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.declined = ?', '0')
            ->order("creation_date DESC");

        // admin pages
        $pagemanageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $pagemanageadminsTableName = $pagemanageadminsTable->info('name');
        $select2 = $pagemanageadminsTable->select()->from($pagemanageadminsTableName, array('page_id'))
            ->setIntegrityCheck(false)
            ->where("$pagemanageadminsTableName.user_id = ?", $user_id);

        // union select queries
        $select = $union->union(array('('.$select1->__toString().')','('.$select2->__toString().')'));

        $page_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        return count($page_ids);

    }

    /**
   * Get Pages relative to page owner
   *
   * @param int $page_id
   * @param int $owner_id
   * @return objects
   */
  public function userPage($params = array()) {

    $rName = $this->info('name');
    $select = $this->select()
            ->from($rName, array('page_id', 'title', 'photo_id', 'page_url', 'owner_id', 'view_count', 'like_count'))
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.declined = ?', '0')
            ->where($rName . '.draft = ?', '1')
            ->where($rName . ".search = ?", 1);
    
    if (isset($params['page_id']) && !empty($params['page_id'])) {
      $select = $select->where($rName . '.	page_id !=?', $params['page_id']);
    }    
    
    if (isset($params['owner_id']) && !empty($params['owner_id'])) {
      $select = $select->where($rName . '.	owner_id =?', $params['owner_id']);
    }    

    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select = $select->where($rName . '.	category_id =?', $params['category_id']);
    }
    if (isset($params['featured']) && ($params['featured'] == '1')) {
      $select = $select->where($rName . '.	featured =?', '0');
    } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
      $select = $select->where($rName . '.	featured =?', '1');
    }

    if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
      $select = $select->where($rName . '.	sponsored =?', '0');
    } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
      $select = $select->where($rName . '.	sponsored =?', '1');
    }
    if (Engine_Api::_()->sitepage()->hasPackageEnable())
      $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));

    if(isset($params['popularity']) && !empty($params['popularity']))
        $select->order($params['popularity'] ." DESC");
    
    $select->order("creation_date DESC");

    //Start Network work
    $select = $this->getNetworkBaseSql($select, array('setIntegrity' => 1));
    //End Network work
    $select = $select->limit($params['totalpages']);

    return $this->fetchALL($select);
  }

  /**
   * Get Pages for links
   *
   * @param int $page_id
   * @param int $viewer_id
   * @return objects
   */
  public function getPages($page_id, $viewer_id) {

    $sitepageName = $this->info('name');
    $select = $this->select()
            ->where($sitepageName . '.page_id <> ?', $page_id)
            ->where($sitepageName . '.owner_id  =?', $viewer_id)
            ->where('NOT EXISTS (SELECT `page_id` FROM `engine4_sitepage_favourites` WHERE `page_id_for`=' . $page_id . ' AND `page_id` = ' . $sitepageName . '.`page_id`) ');

    return $this->fetchALL($select)->toArray();
  }

//  public function sitepageselect($page_id) {
//    $sitepageselect = $this->select()->where('page_id =?', $page_id);
//    return $userPages = $this->fetchALL($sitepageselect);
//  }

  /**
   * Get Discussed Pages
   *
   * @return all discussed pages
   */
  public function getDiscussedPage($params = array()) {
    $sitepage_tableName = $this->info('name');
    $topic_tableName = Engine_Api::_()->getDbTable('topics', 'sitepage')->info('name');
    $select = $this->select()->setIntegrityCheck(false)
            ->from($sitepage_tableName, array('page_id', 'title', 'photo_id', 'page_url', 'owner_id'))
            ->join($topic_tableName, $topic_tableName . '.page_id = ' . $sitepage_tableName . '.page_id', array('count(*) as counttopics', '(sum(post_count) - count(*) ) as total_count'))
            ->where($sitepage_tableName . '.closed = ?', '0')
            ->where($sitepage_tableName . '.approved = ?', '1')
            ->where($sitepage_tableName . '.draft = ?', '1')
            ->where($topic_tableName . '.post_count > ?', '1')
            ->group($topic_tableName . '.page_id');
    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select = $select->where($sitepage_tableName . '.	category_id =?', $params['category_id']);
    }
    if (isset($params['featured']) && ($params['featured'] == '1')) {
      $select = $select->where($sitepage_tableName . '.	featured =?', '0');
    } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
      $select = $select->where($sitepage_tableName . '.	featured =?', '1');
    }

    if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
      $select = $select->where($sitepage_tableName . '.	sponsored =?', '0');
    } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
      $select = $select->where($sitepage_tableName . '.	sponsored =?', '1');
    }
    $select->order('total_count DESC')
            ->order('counttopics DESC')
            ->limit($params['totalpages']);
    if (Engine_Api::_()->sitepage()->hasPackageEnable())
      $select->where($sitepage_tableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    $select->where($sitepage_tableName . ".search = ?", 1);

    //START NETWORK WORK
    $select = $this->getNetworkBaseSql($select);
    //END NETWORK WORK
    return $this->fetchALL($select);
  }

  /**
   * Get pages based on category
   * @param string $title : search text
   * @param int $category_id : category id
   * @param char $popularity : result sorting based on views, reviews, likes, comments
   * @param char $interval : time interval
   * @param string $sqlTimeStr : Time durating string for where clause 
   */
  public function pagesByCategory($category_id, $popularity, $interval, $sqlTimeStr, $totalPages) {
    $groupBy = 1;
    $pageTableName = $this->info('name');

    if ($interval == 'overall' || $popularity == 'view_count') {
      $groupBy = 0;
      $select = $this->select()
              ->from($pageTableName, array('page_id', 'title', 'photo_id', 'page_url', 'owner_id', "$popularity AS populirityCount"))
              ->where($pageTableName . '.category_id = ?', $category_id)
              ->where($pageTableName . '.closed = ?', '0')
              ->where($pageTableName . '.approved = ?', '1')
              ->where($pageTableName . '.declined = ?', '0')
              ->where($pageTableName . '.draft = ?', '1')
              ->where($pageTableName . '.search = ?', '1')
              ->order("$popularity DESC")
              ->order("creation_date DESC")
              ->limit($totalPages);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    } elseif ($popularity == 'review_count' && $interval != 'overall') {

      $popularityTable = Engine_Api::_()->getDbtable('reviews', 'sitepagereview');
      $popularityTableName = $popularityTable->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($pageTableName, array('page_id', 'title', 'photo_id', 'page_url', 'owner_id', "$popularity AS populirityCount"))
              ->joinLeft($popularityTableName, $popularityTableName . '.page_id = ' . $pageTableName . '.page_id', array("COUNT(review_id) as total_count"))
              ->where($pageTableName . '.category_id = ?', $category_id)
              ->where($pageTableName . '.closed = ?', '0')
              ->where($pageTableName . '.approved = ?', '1')
              ->where($pageTableName . '.declined = ?', '0')
              ->where($pageTableName . '.draft = ?', '1')
              ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
              ->group($pageTableName . '.page_id')
              ->order("total_count DESC")
              ->order($pageTableName . ".creation_date DESC")
              ->limit($totalPages);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    } elseif ($popularity == 'member_count' && $interval != 'overall') {

      $popularityTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
      $popularityTableName = $popularityTable->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($pageTableName, array('page_id', 'title', 'photo_id', 'page_url', 'owner_id', "$popularity AS populirityCount"))
              ->joinLeft($popularityTableName, $popularityTableName . '.page_id = ' . $pageTableName . '.page_id', array("COUNT(member_id) as total_count"))
              ->where($pageTableName . '.category_id = ?', $category_id)
              ->where($pageTableName . '.closed = ?', '0')
              ->where($pageTableName . '.approved = ?', '1')
              ->where($pageTableName . '.declined = ?', '0')
              ->where($pageTableName . '.draft = ?', '1')
              ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
              ->group($pageTableName . '.page_id')
              ->order("total_count DESC")
              ->order($pageTableName . ".creation_date DESC")
              ->limit($totalPages);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    } elseif ($popularity != 'view_count' && $popularity != 'review_count' && $popularity != 'member_count' && $interval != 'overall') {

      if ($popularity == 'like_count') {
        $popularityType = 'like';
      } else {
        $popularityType = 'comment';
      }

      $id = $popularityType . "_id";

      $popularityTable = Engine_Api::_()->getDbtable("$popularityType" . "s", 'core');
      $popularityTableName = $popularityTable->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($pageTableName, array('page_id', 'title', 'photo_id', 'page_url', 'owner_id', "$popularity AS populirityCount"))
              ->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $pageTableName . '.page_id', array("COUNT($id) as total_count"))
              ->where($pageTableName . '.category_id = ?', $category_id)
              ->where($pageTableName . '.closed = ?', '0')
              ->where($pageTableName . '.approved = ?', '1')
              ->where($pageTableName . '.declined = ?', '0')
              ->where($pageTableName . '.draft = ?', '1')
              ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
              ->group($pageTableName . '.page_id')
              ->order("total_count DESC")
              ->order($pageTableName . ".creation_date DESC")
              ->limit($totalPages);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    }
    //Start Network work
    $select = $this->getNetworkBaseSql($select, array('not_groupBy' => $groupBy));
    //End Network work

    return $this->fetchAll($select);
  }

  public function getItemOfDay() {

    //$sitepageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $sitepageTableName = $this->info('name');

    $itemofthedaytable = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage');
    $itemofthedayName = $itemofthedaytable->info('name');

    $select = $this->select();
    $select = $select->setIntegrityCheck(false)
            ->from($sitepageTableName, array('page_id', 'title', 'photo_id', 'page_url', 'owner_id'))
            ->join($itemofthedayName, $sitepageTableName . ".page_id = " . $itemofthedayName . '.resource_id', array('start_date'))
            ->where($sitepageTableName . '.closed = ?', '0')
            ->where($sitepageTableName . '.declined = ?', '0')
            ->where($sitepageTableName . '.approved = ?', '1')
            ->where($sitepageTableName . '.draft = ?', '1')
            ->where($itemofthedayName . '.resource_type=?', 'sitepage_page')
            ->where($itemofthedayName . '.start_date <=?', date('Y-m-d'))
            ->where($itemofthedayName . '.end_date >=?', date('Y-m-d'))
            ->order('RAND()');
    return $this->fetchRow($select);
  }

  public function getNetworkBaseSql($select, $params = array()) {
    if (empty($select))
      return;
    $sitepage_tableName = $this->info('name');
    //START NETWORK WORK
    $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.network', 0);
    if (!empty($enableNetwork) || (isset($params['browse_network']) && !empty($params['browse_network']))) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      if (!Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable()) {
        $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

        if (!empty($viewerNetwork)) {
          if (isset($params['setIntegrity']) && !empty($params['setIntegrity'])) {
            $select->setIntegrityCheck(false)
                    ->from($sitepage_tableName);
          }
          $networkMembershipName = $networkMembershipTable->info('name');
          $select
                  ->join($networkMembershipName, "`{$sitepage_tableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                  ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                  ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity());
          if (!isset($params['not_groupBy']) || empty($params['not_groupBy'])) {
            $select->group($sitepage_tableName . ".page_id");
          }
          if (isset($params['extension_group']) && !empty($params['extension_group'])) {
            $select->group($params['extension_group']);
          }
        }
      } else {
        $viewerNetwork = $networkMembershipTable->getMembershipsOfInfo($viewer);
        $str = array();
        $columnName = "`{$sitepage_tableName}`.networks_privacy";
        foreach ($viewerNetwork as $networkvalue) {
          $network_id = $networkvalue->resource_id;
          $str[] = "'" . $network_id . "'";
          $str[] = "'" . $network_id . ",%'";
          $str[] = "'%," . $network_id . ",%'";
          $str[] = "'%," . $network_id . "'";
        }
        if (!empty($str)) {
          $likeNetworkVale = (string) ( join(" or $columnName  LIKE ", $str) );
          $select->where($columnName . ' LIKE ' . $likeNetworkVale . ' or ' . $columnName . " IS NULL");
        } else {
          $select->where($columnName . " IS NULL");
        }
      }
      //END NETWORK WORK
    } else {
      $select = $this->addPrivacyPagesSQl($select, $this->info('name'));
    }
    
    return $select;
  }

  public function countUserPages($user_id) {
    $count = 0;
    $select = $this
            ->select()
            ->from($this->info('name'), array('count(*) as count'))
            ->where("owner_id = ?", $user_id);

    return $select->query()->fetchColumn();
  }

  /**
   * Return page is existing or not.
   *
   * @return Zend_Db_Table_Select
   */
  public function checkPage() {

    //MAKE QUERY
    $hasPage = $this->select()
            ->from($this->info('name'), array('page_id'))
            ->query()
            ->fetchColumn();

    //RETURN RESULTS
    return $hasPage;
  }

  // get lising according to requerment
  public function getListing($sitepagetype = '', $params = array()) {

    $limit = 10;
    $table = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $sitepageTableName = $table->info('name');
    $coreTable = Engine_Api::_()->getDbtable('likes', 'core');
    $coreName = $coreTable->info('name');
    
    $columns = array('page_id', 'title','photo_id', 'page_url', 'owner_id','view_count', 'like_count', 'comment_count', 'follow_count', 'category_id', 'sponsored', 'featured');
    
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
			$columns[]="review_count";
      $columns[]="rating";
		}
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
			$columns[]="member_count";
			$columns[]="member_title";
		}    
    
    $select = $table->select()
            ->from($sitepageTableName, $columns)
            ->where($sitepageTableName . '.closed = ?', '0')
            ->where($sitepageTableName . '.approved = ?', '1')
            ->where($sitepageTableName . '.draft = ?', '1')
            ->where($sitepageTableName . ".search = ?", 1);

    //$select = $this->expirySQL($select);

    if (isset($params['page_id']) && !empty($params['page_id'])) {
      $select->where($sitepageTableName . '.page_id != ?', $params['page_id']);
    }

    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select->where($sitepageTableName . '.category_id = ?', $params['category_id']);
    }

    if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
      $select->where($sitepageTableName . '.subcategory_id = ?', $params['subcategory_id']);
    }

    if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
      $select->where($sitepageTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
    }

    if (isset($params['popularity']) && !empty($params['popularity'])) {
      $select->order($params['popularity'] . " DESC");
    }

    if (isset($params['featured']) && !empty($params['featured']) || $sitepagetype == 'featured') {
      $select->where("$sitepageTableName.featured = ?", 1);
    }

    if (isset($params['sponsored']) && !empty($params['sponsored']) || $sitepagetype == 'sponsored') {
      $select = $select->where($sitepageTableName . '.sponsored = ?', '1');
    }

    //Start Network work
    $select = $table->getNetworkBaseSql($select, array('setIntegrity' => 1));
    //End Network work

    if ($sitepagetype == 'most_popular') {
      $select = $select->order($sitepageTableName . '.view_count DESC');
    }

    if ($sitepagetype == 'most_reviews' || $sitepagetype == 'most_reviewed') {
      $select = $select->order($sitepageTableName . '.review_count DESC');
    }

    if (isset($params['similar_items_order']) && !empty($params['similar_items_order'])) {
      if (isset($params['ratingType']) && !empty($params['ratingType']) && $params['ratingType'] != 'rating_both') {
        $ratingType = $params['ratingType'];
        $select->order($sitepageTableName . ".$ratingType DESC");
      } else {
        $select->order($sitepageTableName . '.rating_avg DESC');
      }
      $select->order('RAND()');
    } else {
      $select->order($sitepageTableName . '.page_id DESC');
    }

    if (isset($params['limit']) && !empty($params['limit'])) {
      $limit = $params['limit'];
    }

    $select->group($sitepageTableName . '.page_id');

    if (isset($params['start_index']) && $params['start_index'] >= 0) {
      $select = $select->limit($limit, $params['start_index']);
      return $table->fetchAll($select);
    } else {

      $paginator = Zend_Paginator::factory($select);
      if (!empty($params['page'])) {
        $paginator->setCurrentPageNumber($params['page']);
      }

      if (!empty($params['limit'])) {
        $paginator->setItemCountPerPage($limit);
      }

      return $paginator;
    }
  }
  
    /**
     * Get Pages listing according to requerment
     *
     * @param string $sitepagetype
     * @param array $params
     * @return objects
     */
    public function getListings($sitepagetype, $params = array(), $interval = NULL, $sqlTimeStr = NULL, $columnsArray = array()) {

        $limit = 10;
        $tempNum = 63542;
        $rName = $this->info('name');

        if(empty($columnsArray) || (!empty($columnsArray) && Count($columnsArray) <= 0)) {
            $columnsArray = array('page_id', 'title', 'page_url', 'body', 'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'modified_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'closed', 'email', 'website', 'phone', 'package_id', 'follow_count','show_name');
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                $columnsArray[] = 'member_count';
            }
            $columnsArray[] = 'member_title';
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge'))
                $columnsArray[] = 'badge_id';

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer'))
                $columnsArray[] = 'offer';

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
                $columnsArray[] = 'review_count';
                $columnsArray[] = 'rating';
            }             
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
            $tempGetHost = $tempMemberLsetting = 0;
            $getHost = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
            $memberLsetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.lsettings', null);
            for ($check = 0; $check < strlen($getHost); $check++) {
                $tempGetHost += @ord($getHost[$check]);
            }

            for ($check = 0; $check < strlen($memberLsetting); $check++) {
                $tempMemberLsetting += @ord($memberLsetting[$check]);
            }
            $tempPageMemberValues = $tempGetHost + $tempMemberLsetting + $tempNum;
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagemember.join.type', $tempPageMemberValues);
        }

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($rName, $columnsArray)
                ->where($rName . '.closed = ?', '0')
                ->where($rName . '.approved = ?', '1')
                ->where($rName . '.declined = ?', '0')
                ->where($rName . '.draft = ?', '1')
                ->where($rName . ".search = ?", 1);
        if (Engine_Api::_()->sitepage()->hasPackageEnable())
            $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));

        //Start Network work
        $select = $this->getNetworkBaseSql($select, array());
        //End Network work
        if ($sitepagetype == 'Most Viewed') {
            $select = $select->where($rName . '.view_count <> ?', '0')->order($rName . '.view_count DESC');
        } elseif ($sitepagetype == 'Most Viewed List') {
            $select = $select->where($rName . '.view_count <> ?', '0');
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if ($interval != 'overall') {
                $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
            }
            $select->order($rName . '.view_count DESC');

            if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }
        } elseif ($sitepagetype == 'Recently Posted List') {
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            $select = $select->order($rName . '.creation_date DESC');
            if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }
        } elseif ($sitepagetype == 'Random List') {
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            $select->order('RAND() DESC ');
            if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }
        } elseif ($sitepagetype == 'Most Commented') {
            $select = $select->where($rName . '.comment_count <> ?', '0');
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if ($interval != 'overall') {
                $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
            }
            $select->order($rName . '.comment_count DESC');
            if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }
        } elseif ($sitepagetype == 'Top Rated') {
            $select = $select->where($rName . '.rating <> ?', '0')->order($rName . '.rating DESC');
            $limit = $params['itemCount'];
        } elseif ($sitepagetype == 'Recently Posted') {
            $select = $select->order($rName . '.creation_date DESC');
        } elseif ($sitepagetype == 'Featured') {
            $select = $select->where($rName . '.featured = ?', '1');
        } elseif ($sitepagetype == 'Sponosred') {
            $select = $select->where($rName . '.sponsored = ?', '1');
        } elseif ($sitepagetype == 'Sponsored Sitepage') {
            $select = $select->where($rName . '.sponsored = ?', '1');
            if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }
        } elseif ($sitepagetype == 'Total Sponsored Sitepage') {
            $select = $select->where($rName . '.sponsored = ?', '1');
        } elseif ($sitepagetype == 'Sponsored Sitepage AJAX') {
            $select = $select->where($rName . '.sponsored = ?', '1');
            if (isset($params['totalpages'])) {
                $limit = (int) $params['totalpages'] * 2;
            }
        } elseif ($sitepagetype == 'Featured Slideshow') {
            $select = $select->where($rName . '.featured = ?', '1');
            if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }
        } elseif ($sitepagetype == 'Sponosred Slideshow') {
            $select = $select->where($rName . '.sponsored = ?', '1');
            if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }
        } elseif ($sitepagetype == 'Most Joined') {
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if (isset($params['totalpages'])) {
                $limit = (int) $params['totalpages'];
            }
            $select->order($rName . '.member_count DESC');
        } elseif ($sitepagetype == 'Most Active Pages') {
            if (isset($params['active_pages'])) {
                if ($params['active_pages'] == 'member_count') {
                    $select->order($rName . '.member_count DESC');
                } elseif ($params['active_pages'] == 'comment_count') {
                    $select->order($rName . '.comment_count DESC');
                } elseif ($params['active_pages'] == 'like_count') {
                    $select->order($rName . '.like_count DESC');
                } elseif ($params['active_pages'] == 'view_count') {
                    $select->order($rName . '.view_count DESC');
                }
            }
        } elseif ($sitepagetype == 'Most Followers') {
            $select = $select->where($rName . '.follow_count <> ?', '0');
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if ($interval != 'overall') {
                $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
            }
            $select->order($rName . '.follow_count DESC');

            if (isset($params['totalpages'])) {
                $limit = (int) $params['totalpages'];
            }
        } elseif ($sitepagetype == 'Most Likes') {
            $select = $select->where($rName . '.like_count <> ?', '0');
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if ($interval != 'overall') {
                $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
            }
            $select->order($rName . '.like_count DESC');

            if (isset($params['totalpages'])) {
                $limit = (int) $params['totalpages'];
            }
        } elseif ($sitepagetype == 'Pin Board') {
            if (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['locationmiles']) && $params['locationmiles']) {
                $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
                $locationName = $locationsTable->info('name');
                $radius = $params['locationmiles']; //in miles
                $latitude = $params['latitude'];
                $longitude = $params['longitude'];
                $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
                if (!empty($flage)) {
                    $radius = $radius * (0.621371192);
                }
                //$latitudeRadians = deg2rad($latitude);
								$latitudeSin = "sin(radians($latitude))";
								$latitudeCos = "cos(radians($latitude))";

                $select->join($locationName, "$rName.page_id = $locationName.page_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance", $locationName . '.location AS locationName'));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);
                $select->order("distance");
                $select->group("$rName.page_id");
            }

            $popularity = $params['popularity'];
            $interval = $params['interval'];
            //MAKE TIMING STRING
            $sqlTimeStr = '';
            $current_time = date("Y-m-d H:i:s");
            if ($interval == 'week') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
            } elseif ($interval == 'month') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
            }

            if ($interval != 'overall' && $popularity == 'like_count') {

                $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
                $popularityTableName = $popularityTable->info('name');

                $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.page_id', array("COUNT(like_id) as total_count"))
                        ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                        ->order("total_count DESC");
            } elseif ($interval != 'overall' && $popularity == 'follow_count') {

                $popularityTable = Engine_Api::_()->getDbtable('follows', 'seaocore');
                $popularityTableName = $popularityTable->info('name');

                $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.page_id', array("COUNT(follow_id) as total_count"))
                        ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                        ->order("total_count DESC");
            } elseif ($interval != 'overall' && $popularity == 'member_count') {

                if ($interval == 'week') {
                    $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                    $sqlTimeStr = ".join_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
                } elseif ($interval == 'month') {
                    $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                    $sqlTimeStr = ".join_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
                }
                $popularityTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
                $popularityTableName = $popularityTable->info('name');

                $select = $select->join($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.page_id', array("COUNT(member_id) as total_count"))
                        ->where($popularityTableName . $sqlTimeStr)
                        ->where($popularityTableName . ".active =?", 1)
                        ->group($popularityTableName . '.resource_id')
                        ->order("total_count DESC");
            } elseif ($interval != 'overall' && $popularity == 'comment_count') {

                $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
                $popularityTableName = $popularityTable->info('name');

                $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.page_id', array("COUNT(comment_id) as total_count"))
                        ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                        ->order("total_count DESC");
            } else {

                if (isset($popularity) && !empty($popularity)) {
                    $select->order("$rName.$popularity DESC");
                }

                if (isset($params['featured']) && ($params['featured'] == '1')) {
                    $select = $select->where($rName . '.	featured =?', '1');
                }
                
                if (isset($params['random']) && ($params['random'] == '1')) {
                    $select =  $select->order('RAND() DESC ');
                }

                if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                    $select = $select->where($rName . '.	sponsored =?', '1');
                }
                if ($interval != 'overall') {
                    $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
                }
            }

            if (isset($params['totalpages'])) {
                $limit = (int) $params['totalpages'];
            }
        } elseif ($sitepagetype == 'Random') {
            $select->order('RAND() DESC ');
        } else if ($sitepagetype == 'Featured Slideshow') {
            $select->order('RAND() DESC ');
        } else if ($sitepagetype == 'Sponosred Slideshow') {
            $select->order('RAND() DESC ');
        } else {
            $select->order($rName . '.page_id DESC');
        }

        if (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance']) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
            $locationName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
           // $latitudeRadians = deg2rad($latitude);
						$latitudeSin = "sin(radians($latitude))";
						$latitudeCos = "cos(radians($latitude))";

            $select->join($locationName, "$rName.page_id = $locationName.page_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance", $locationName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);
            $select->order("distance");
           
        }
				$select->group("$rName.page_id");
        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select = $select->where($rName . '.	category_id =?', $params['category_id']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $limit = $params['limit'];
        }

        if (($sitepagetype == 'Sponsored Sitepage AJAX' || $sitepagetype == 'Sponsored Sitepage' ) && !empty($params['start_index'])) {
            $select = $select->limit($limit, $params['start_index']);
        } else {
            if ($sitepagetype != 'Total Sponsored Sitepage') {
                $select = $select->limit($limit);
            }
        }
        if (isset($params['paginator']) && !empty($params['paginator'])) {
            return $paginator = Zend_Paginator::factory($select);
        }

        return $this->fetchALL($select);
    }

    public function getListingsCount($sitepagetype, $params = array(), $interval = NULL, $sqlTimeStr = NULL, $columnsArray = array()) {

        //$limit = 10;
        $tempNum = 63542;
        $rName = $this->info('name');

        if(empty($columnsArray) || (!empty($columnsArray) && Count($columnsArray) <= 0)) {
            $columnsArray = array('page_id', 'title', 'page_url', 'body', 'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'modified_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'closed', 'email', 'website', 'phone', 'package_id', 'follow_count','show_name');
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                $columnsArray[] = 'member_count';
            }
            $columnsArray[] = 'member_title';
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge'))
                $columnsArray[] = 'badge_id';

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer'))
                $columnsArray[] = 'offer';

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
                $columnsArray[] = 'review_count';
                $columnsArray[] = 'rating';
            }
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
            $tempGetHost = $tempMemberLsetting = 0;
            $getHost = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
            $memberLsetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.lsettings', null);
            for ($check = 0; $check < strlen($getHost); $check++) {
                $tempGetHost += @ord($getHost[$check]);
            }

            for ($check = 0; $check < strlen($memberLsetting); $check++) {
                $tempMemberLsetting += @ord($memberLsetting[$check]);
            }
            $tempPageMemberValues = $tempGetHost + $tempMemberLsetting + $tempNum;
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagemember.join.type', $tempPageMemberValues);
        }

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($rName, $columnsArray)
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.declined = ?', '0')
            ->where($rName . '.draft = ?', '1')
            ->where($rName . ".search = ?", 1);
        if (Engine_Api::_()->sitepage()->hasPackageEnable())
            $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));

        //Start Network work
        $select = $this->getNetworkBaseSql($select, array());
        //End Network work
        if ($sitepagetype == 'Most Viewed') {
            $select = $select->where($rName . '.view_count <> ?', '0')->order($rName . '.view_count DESC');
        } elseif ($sitepagetype == 'Most Viewed List') {
            $select = $select->where($rName . '.view_count <> ?', '0');
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if ($interval != 'overall') {
                $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
            }
            $select->order($rName . '.view_count DESC');

            /*if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }*/
        } elseif ($sitepagetype == 'Recently Posted List') {
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            $select = $select->order($rName . '.creation_date DESC');
            /*if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }*/
        } elseif ($sitepagetype == 'Random List') {
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            $select->order('RAND() DESC ');
            /*if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }*/
        } elseif ($sitepagetype == 'Most Commented') {
            $select = $select->where($rName . '.comment_count <> ?', '0');
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if ($interval != 'overall') {
                $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
            }
            $select->order($rName . '.comment_count DESC');
            /*if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }*/
        } elseif ($sitepagetype == 'Top Rated') {
            $select = $select->where($rName . '.rating <> ?', '0')->order($rName . '.rating DESC');
            //$limit = $params['itemCount'];
        } elseif ($sitepagetype == 'Recently Posted') {
            $select = $select->order($rName . '.creation_date DESC');
        } elseif ($sitepagetype == 'Featured') {
            $select = $select->where($rName . '.featured = ?', '1');
        } elseif ($sitepagetype == 'Sponosred') {
            $select = $select->where($rName . '.sponsored = ?', '1');
        } elseif ($sitepagetype == 'Sponsored Sitepage') {
            $select = $select->where($rName . '.sponsored = ?', '1');
            /*if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }*/
        } elseif ($sitepagetype == 'Total Sponsored Sitepage') {
            $select = $select->where($rName . '.sponsored = ?', '1');
        } elseif ($sitepagetype == 'Sponsored Sitepage AJAX') {
            $select = $select->where($rName . '.sponsored = ?', '1');
            /*if (isset($params['totalpages'])) {
                $limit = (int) $params['totalpages'] * 2;
            }*/
        } elseif ($sitepagetype == 'Featured Slideshow') {
            $select = $select->where($rName . '.featured = ?', '1');
            /*if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }*/
        } elseif ($sitepagetype == 'Sponosred Slideshow') {
            $select = $select->where($rName . '.sponsored = ?', '1');
            /*if (isset($params['totalpages'])) {
                $limit = $params['totalpages'];
            }*/
        } elseif ($sitepagetype == 'Most Joined') {
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            /*if (isset($params['totalpages'])) {
                $limit = (int) $params['totalpages'];
            }*/
            $select->order($rName . '.member_count DESC');
        } elseif ($sitepagetype == 'Most Active Pages') {
            if (isset($params['active_pages'])) {
                if ($params['active_pages'] == 'member_count') {
                    $select->order($rName . '.member_count DESC');
                } elseif ($params['active_pages'] == 'comment_count') {
                    $select->order($rName . '.comment_count DESC');
                } elseif ($params['active_pages'] == 'like_count') {
                    $select->order($rName . '.like_count DESC');
                } elseif ($params['active_pages'] == 'view_count') {
                    $select->order($rName . '.view_count DESC');
                }
            }
        } elseif ($sitepagetype == 'Most Followers') {
            $select = $select->where($rName . '.follow_count <> ?', '0');
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if ($interval != 'overall') {
                $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
            }
            $select->order($rName . '.follow_count DESC');

            if (isset($params['totalpages'])) {
                $limit = (int) $params['totalpages'];
            }
        } elseif ($sitepagetype == 'Most Likes') {
            $select = $select->where($rName . '.like_count <> ?', '0');
            if (isset($params['featured']) && ($params['featured'] == '1')) {
                $select = $select->where($rName . '.	featured =?', '0');
            } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
                $select = $select->where($rName . '.	featured =?', '1');
            }

            if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                $select = $select->where($rName . '.	sponsored =?', '0');
            } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
                $select = $select->where($rName . '.	sponsored =?', '1');
            }
            if ($interval != 'overall') {
                $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
            }
            $select->order($rName . '.like_count DESC');

            /*if (isset($params['totalpages'])) {
                $limit = (int) $params['totalpages'];
            }*/
        } elseif ($sitepagetype == 'Pin Board') {
            if (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['locationmiles']) && $params['locationmiles']) {
                $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
                $locationName = $locationsTable->info('name');
                $radius = $params['locationmiles']; //in miles
                $latitude = $params['latitude'];
                $longitude = $params['longitude'];
                $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
                if (!empty($flage)) {
                    $radius = $radius * (0.621371192);
                }
                //$latitudeRadians = deg2rad($latitude);
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";

                $select->join($locationName, "$rName.page_id = $locationName.page_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance", $locationName . '.location AS locationName'));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);
                $select->order("distance");
                $select->group("$rName.page_id");
            }

            $popularity = $params['popularity'];
            $interval = $params['interval'];
            //MAKE TIMING STRING
            $sqlTimeStr = '';
            $current_time = date("Y-m-d H:i:s");
            if ($interval == 'week') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
            } elseif ($interval == 'month') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
            }

            if ($interval != 'overall' && $popularity == 'like_count') {

                $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
                $popularityTableName = $popularityTable->info('name');

                $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.page_id', array("COUNT(like_id) as total_count"))
                    ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                    ->order("total_count DESC");
            } elseif ($interval != 'overall' && $popularity == 'follow_count') {

                $popularityTable = Engine_Api::_()->getDbtable('follows', 'seaocore');
                $popularityTableName = $popularityTable->info('name');

                $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.page_id', array("COUNT(follow_id) as total_count"))
                    ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                    ->order("total_count DESC");
            } elseif ($interval != 'overall' && $popularity == 'member_count') {

                if ($interval == 'week') {
                    $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                    $sqlTimeStr = ".join_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
                } elseif ($interval == 'month') {
                    $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                    $sqlTimeStr = ".join_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
                }
                $popularityTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
                $popularityTableName = $popularityTable->info('name');

                $select = $select->join($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.page_id', array("COUNT(member_id) as total_count"))
                    ->where($popularityTableName . $sqlTimeStr)
                    ->where($popularityTableName . ".active =?", 1)
                    ->group($popularityTableName . '.resource_id')
                    ->order("total_count DESC");
            } elseif ($interval != 'overall' && $popularity == 'comment_count') {

                $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
                $popularityTableName = $popularityTable->info('name');

                $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.page_id', array("COUNT(comment_id) as total_count"))
                    ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                    ->order("total_count DESC");
            } else {

                if (isset($popularity) && !empty($popularity)) {
                    $select->order("$rName.$popularity DESC");
                }

                if (isset($params['featured']) && ($params['featured'] == '1')) {
                    $select = $select->where($rName . '.	featured =?', '1');
                }

                if (isset($params['random']) && ($params['random'] == '1')) {
                    $select =  $select->order('RAND() DESC ');
                }

                if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
                    $select = $select->where($rName . '.	sponsored =?', '1');
                }
                if ($interval != 'overall') {
                    $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
                }
            }

            /*if (isset($params['totalpages'])) {
                $limit = (int) $params['totalpages'];
            }*/
        } elseif ($sitepagetype == 'Random') {
            $select->order('RAND() DESC ');
        } else if ($sitepagetype == 'Featured Slideshow') {
            $select->order('RAND() DESC ');
        } else if ($sitepagetype == 'Sponosred Slideshow') {
            $select->order('RAND() DESC ');
        } else {
            $select->order($rName . '.page_id DESC');
        }

        if (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance']) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
            $locationName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            // $latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationName, "$rName.page_id = $locationName.page_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance", $locationName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);
            $select->order("distance");

        }
        $select->group("$rName.page_id");
        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select = $select->where($rName . '.	category_id =?', $params['category_id']);
        }

        /*
        if (isset($params['limit']) && !empty($params['limit'])) {
            $limit = $params['limit'];
        }

        if (($sitepagetype == 'Sponsored Sitepage AJAX' || $sitepagetype == 'Sponsored Sitepage' ) && !empty($params['start_index'])) {
            $select = $select->limit($limit, $params['start_index']);
        } else {
            if ($sitepagetype != 'Total Sponsored Sitepage') {
                $select = $select->limit($limit);
            }
        }
        */
        $paginator = Zend_Paginator::factory($select);

        return $paginator->getTotalItemCount();
    }

  /**
   * Return Location Base Pages
   * 
   * @return $pages
   */
  public function getLocationBaseContents($params = array()) {

    if (empty($params['search']))
      return;
    $limit = 5;
    if (isset($params['limit']) && !empty($params['limit'])) {
      $limit = $params['limit'];
    } else {
      $limit = 5;
    }
    $select = $this->getPagesSelectSql(array("limit" => $limit));
    //Start Network work
    $select = $this->getNetworkBaseSql($select);
    //End Network work
    $pageName = $this->info('name');
    $locationTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
    $locationName = $locationTable->info('name');
    $select
            ->setIntegrityCheck(false)
            ->from($pageName, array('title', 'page_id', 'location', 'photo_id', 'category_id'))
            ->join($locationName, "$pageName.page_id = $locationName.page_id", array("latitude", "longitude", "formatted_address"));

    if (isset($params['search']) && !empty($params['search'])) {
      $select->where("`{$pageName}`.title LIKE ? or `{$pageName}`.location LIKE ? or `{$locationName}`.city LIKE ?", "%" . $params['search'] . "%");
    }

    if (isset($params['resource_id']) && !empty($params['resource_id'])) {
      $select->where($locationName . '.page_id not in (?)', new Zend_Db_Expr(trim($params['resource_id'], ',')));
    }

    $select->order('creation_date DESC');

    return $this->fetchAll($select);
  }

  /* Return Location Base Pages
   * 
   * @return $pages
   */

  public function getPreviousLocationBaseContents($params = array()) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->getPagesSelectSql(array("limit" => 5));

    //START NETWORK WORK
    $select = $this->getNetworkBaseSql($select);
    //END NETWORK WORK
    //GET PAGE TABLE NAME
    $pageTableName = $this->info('name');

    //LOCATION TABLE NAME
    $locationTableName = Engine_Api::_()->getDbtable('locations', 'sitepage')->info('name');

    //GET ADD LOCATION TABLE NAME
    $addlocationsTableName = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin')->info('name');

    $select = $select
            ->setIntegrityCheck(false)
            ->from($pageTableName, array('title', 'page_id', 'location', 'photo_id', 'category_id'))
            ->join($addlocationsTableName, "$addlocationsTableName.object_id = $pageTableName.page_id", null)
            ->join($locationTableName, "$locationTableName.location_id = $addlocationsTableName.location_id", array("latitude", "longitude", "formatted_address"))
            ->where("$addlocationsTableName.object_type =?", "sitepage_page")
            ->where("$addlocationsTableName.owner_id =?", $viewer_id)
            ->group("$addlocationsTableName.object_id")
            ->order("$pageTableName.creation_date DESC");

    return $this->fetchAll($select);
  }

//    /**
//    * Return Count Location Base Pages
//    * 
//    * @return $pages
//    */
// 	public function getLocationCount() {
// 	
// 	  $pageTableName = $this->info('name');
// 		$select = $this->select()->from($pageTableName, array('location'))
// 						->where($pageTableName . '.approved = 1')
// 						->where($pageTableName . '.draft = ?', '1')
// 						->where($pageTableName . '.location != ?', '')
// 						->where($pageTableName . '.closed = ?', '0');
// 		return $select->query()->fetchColumn();
// 	}

  /**
   * Return pages which have this category and this mapping
   *
   * @param int category_id
   * @return Zend_Db_Table_Select
   */
  public function getCategorySitepage($category_id) {

    //RETURN IF CATEGORY ID IS NULL
    if (empty($category_id)) {
      return null;
    }

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), 'page_id')
            ->where('category_id = ?', $category_id);

    //GET DATA
    $categoryData = $this->fetchAll($select);

    if (!empty($categoryData)) {
      return $categoryData->toArray();
    }

    return null;
  }

  public function getPageName($page_id) {
    $select = $this->select()
            ->from($this->info('name'), 'title')
            ->where('page_id = ?', $page_id);
    return $select->query()->fetchColumn();
  }

  public function getPageId($owner_id) {
    $select = $this->select()
            ->from($this->info('name'), 'page_id')
            ->where('owner_id = ?', $owner_id);
    return $select->query()->fetchAll();
  }

  public function getsubPageids($page_id) {

    $select = $this->select()
            ->from($this->info('name'), 'page_id')
            ->where('subpage = ?', '1')
            ->where('parent_id = ?', $page_id);
    return $select->query()->fetchAll();
  }

  public function getPageObject($page_id) {
    $select = $this->select()
            ->from($this->info('name'), array('page_id', 'title', 'page_url'))
            ->where("page_id IN ($page_id)");
    return $this->fetchAll($select);
  }

  public function getLikeCounts($params = array()) {

    //GETTING THE CURRENT USER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $coreLikeTable = Engine_Api::_()->getItemTable('core_like');
    $coreLikeTableName = $coreLikeTable->info('name');
    $moduleTable = Engine_Api::_()->getItemTable('sitepage_page');
    $moduleTableName = $moduleTable->info('name');

    $like_select = $moduleTable->select()
            ->setIntegrityCheck(false)
            ->from($coreLikeTableName, null)
            ->join($moduleTableName, "$coreLikeTableName.resource_id = $moduleTableName.page_id", array("COUNT(page_id) as likeCount"))
            ->where($coreLikeTableName . '.resource_type = ?', 'sitepage_page')
            ->where($moduleTableName . '.approved = ?', '1')
            ->where($moduleTableName . '.declined = ?', '0')
            ->where($moduleTableName . '.draft = ?', '1')
            ->where($moduleTableName . ".search = ?", 1)
            ->where($coreLikeTableName . '.poster_id = ?', $user_id);
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1) == 0) {
      $like_select = $like_select->where($moduleTableName . '.closed = ?', '0');
    }

    return $like_select->query()->fetchColumn();
  }
  public function getPagesCount($id, $column_name, $foruser = null) {

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('COUNT(*) AS count'));

    if (!empty($foruser)) {
      $select->where('closed = ?', 0)
              ->where('approved = ?', 1)
              ->where('draft = ?', 0)
              ->where('search = ?', 1);
      $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
    }

    if (!empty($column_name) && !empty($id)) {
            $select->where("$column_name = ?", $id);
        }

        $totalPages = $select->query()->fetchColumn();

        //RETURN EVENTS COUNT
        return $totalPages;
  }

  public function getCategoryList($category_id, $categoryType) {

        //RETURN IF CATEGORY ID IS NULL
        if (empty($category_id)) {
            return null;
        }

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), 'page_id')
                ->where("$categoryType = ?", $category_id);

        //GET DATA
        return $this->fetchAll($select);
    }


    public function getFollowingPages($user_id){

        $pageName = $this->info('name');
        $followTable = Engine_Api::_()->getDbtable('follows', 'seaocore');
        $followTableName = $followTable->info('name');

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($pageName)
            ->join($followTableName, "$pageName.page_id = $followTableName.resource_id")
            // should show not owned, approved and not expired pages
            ->where($pageName . '.owner_id != ?', $user_id)
            ->where($pageName . '.closed = ?', '0')
         //   ->where($pageName . '.approved = ?', '1')
            ->where($pageName . '.declined = ?', '0')
            ->where($pageName . '.draft = ?', '1')
            ->where($pageName . '.expiration_date  > ?', date("Y-m-d H:i:s"))
            // and followed
            ->where($followTableName . '.resource_type = ?', "sitepage_page")
            ->where($followTableName . '.poster_id = ?', $user_id);

        return $this->fetchAll($select);
    }

    public function getJoinedPages($user_id){

        $pageName = $this->info('name');
        $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
        $membershipTableName = $membershipTable->info('name');

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($pageName)
            ->join($membershipTableName, "$pageName.page_id = $membershipTableName.resource_id ")
            // should show not owned, approved and not expired pages
            ->where($pageName . '.owner_id != ?', $user_id)
            ->where($pageName . '.closed = ?', '0')
          //  ->where($pageName . '.approved = ?', '1')
            ->where($pageName . '.declined = ?', '0')
            ->where($pageName . '.draft = ?', '1')
            ->where($pageName . '.expiration_date  > ?', date("Y-m-d H:i:s"))
            // and joined
            ->where($membershipTableName . '.user_id = ?', $user_id)
            ->where($membershipTableName . '.active = ?', '1')
            ->where($membershipTableName . '.resource_approved = ?', '1')
            ->where($membershipTableName . '.user_approved = ?', '1');

        return $this->fetchAll($select);
    }

    public function pagesIAdmin($user_id){
        $manageTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $manageTableName = $manageTable->info('name');
        $result = $manageTable
            ->select()
            ->from($manageTableName,array('page_id'))
            ->where('user_id = ?', $user_id )
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);

        if(!empty($result)){
            $select = $this->select()
                ->where('page_id IN (?)', $result)
                // should show not owned, approved and not expired pages
                ->where('owner_id != ?', $user_id)
                ->where('closed = ?', '0')
           //     ->where('approved = ?', '1')
                ->where('declined = ?', '0')
                ->where('draft = ?', '1')
                ->where('expiration_date  > ?', date("Y-m-d H:i:s"));
            return $this->fetchAll($select);
        }

        return null;
    }

    public function getPageProjectsByParamsPaginator($params = array()) {
        $paginator = Zend_Paginator::factory($this->getPageProjectsByParamsSelect($params));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        $paginator->setItemCountPerPage(5);
        return $paginator;

    }
    public function getPageProjectsByParamsSelect($params = array()) {


        //GET USER TABLE NAME
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

        //GET PROJECT TABLE
        $tableProject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTableName = $tableProject->info('name');

        //MAKE QUERY
        $select = $tableProject->select()
            ->setIntegrityCheck(false)
            ->from($projectTableName)
            ->joinLeft($tableUserName, "$projectTableName.owner_id = $tableUserName.user_id", array('username','displayname'))
            ->group("$projectTableName.project_id");

        // filter by project ids
        if (!empty($params['project_ids'])) {
            $select->where("$projectTableName.project_id IN (?)", $params['project_ids']);
        }

        // project filter
        if (!empty($params['project_id'])) {
            $select->where($projectTableName . '.project_id = ? ', $params['project_id']);
        }
//      //project filter

        // filter by project name , if project_id is not passed
        if ( empty($params['project_id']) && !empty($params['project_name']) ) {
            $select->where($projectTableName . '.title LIKE ?', '%' .trim($params['project_name']) . '%');
        }

        // owner based filter
        if (!empty($params['user_id'])) {
            $select->where($projectTableName . '.owner_id = ? ', $params['user_id']);
        }
        if (!empty($params['project_order'])) {
            $select->where($projectTableName . '.project_order = ? ', $params['project_order']);
        }
        // filter by user_name if user_id is not passed
        if (!empty($params['user_name']) && empty($params['user_id'])) {
            $select->where($tableUserName . '.displayname  LIKE ?', '%' . trim($params['user_name']) . '%');
        }

        // Project Status
        if (!empty($params['project_status']) && !is_array($params['project_status']) ) {
            $project_status = "";
            switch ($params['project_status']) {
                case "1" :
                    $project_status = 'draft';
                    break;
                case "2" :
                    $project_status = "published";
                    break;
                case "3" :
                    $project_status = "successful";
                    break;
                case "4" :
                    $project_status = "failed";
                    break;
                case "5" :
                    $project_status = "submitted";
                    break;
                case "6" :
                    $project_status = "rejected";
                    break;
            }
            if (!empty($project_status)) {
                $select->where($projectTableName . '.state = ? ', $project_status);
            }
        }
        if (!empty($params['project_status']) && is_array($params['project_status']) && count($params['project_status']) > 0 ) {
            $select->where($projectTableName . '.state in (?) ', $params['project_status']);
        }

        // Funding Status
        if (!empty($params['funding_status'])) {
            $funding_status = "";
            switch ($params['funding_status']) {
                case "1" :
                    $funding_status = 'draft';
                    break;
                case "2" :
                    $funding_status = "published";
                    break;
                case "3" :
                    $funding_status = "successful";
                    break;
                case "4" :
                    $funding_status = "failed";
                    break;
                case "5" :
                    $funding_status = "submitted";
                    break;
                case "6" :
                    $funding_status = "rejected";
                    break;
            }
            if (!empty($funding_status)) {
                $select->where($projectTableName . '.is_fund_raisable = ? ', 1);
                $select->where($projectTableName . '.funding_state = ? ', $funding_status);
            }
        }

        // Published/ Unpublished
        if (!empty($params['is_published_yn'])) {
            $liveProjectStatus = array("published");
            if($params['is_published_yn'] === '1'){
                $select->where("$projectTableName.state NOT IN (?)", $liveProjectStatus);
            }
            if($params['is_published_yn'] === '2'){
                $select->where("$projectTableName.state IN (?)", $liveProjectStatus);
            }
        }

        // Funding Enabled / Disabled
        if (!empty($params['is_funding_enabled_yn'])) {
            if($params['is_funding_enabled_yn'] === '2'){
                $select->where($projectTableName . '.is_fund_raisable = ? ', 1);
                $select->where($projectTableName . '.funding_approved = ? ', 1);
            }
            if($params['is_funding_enabled_yn'] === '1'){
                $select->where($projectTableName . '.is_fund_raisable = ? ', 1);
                $select->where($projectTableName . '.funding_approved = ? ', 0);
            }
        }

        // is_payment_edit
        if (!empty($params['is_payment_edit'])) {
            $params['is_payment_edit'] --;
            $select->where($projectTableName . '.is_fund_raisable = ? ', 1);
            $select->where($projectTableName . '.is_payment_details_editable = ? ', $params['is_payment_edit']);
        }

        // goal min and max value
        if (!empty($params['goal_amount_min']) && empty($params['goal_amount_max'])) {
            $goal_amt_min_value = str_replace( ',', '', $params['goal_amount_min']);
            $select
                ->where($projectTableName. '.is_fund_raisable = ?',1)
                ->where($projectTableName . '.goal_amount >= ? ', $goal_amt_min_value);
        }
        if (empty($params['goal_amount_min']) && !empty($params['goal_amount_max'])) {
            $goal_amt_max_value = str_replace( ',', '', $params['goal_amount_max']);
            $select
                ->where($projectTableName. '.is_fund_raisable = ?',1)
                ->where($projectTableName . '.goal_amount <= ? ', $goal_amt_max_value);
        }
        if (!empty($params['goal_amount_min']) && !empty($params['goal_amount_max'])) {
            $goal_amt_min_value = str_replace( ',', '', $params['goal_amount_min']);
            $goal_amt_max_value = str_replace( ',', '', $params['goal_amount_max']);
            $select
                ->where($projectTableName. '.is_fund_raisable = ?',1)
                ->where($projectTableName . '.goal_amount >= ? ', $goal_amt_min_value)
                ->where($projectTableName . '.goal_amount <= ? ', $goal_amt_max_value);

        }


        // Sort
        if (!empty($params['sort_field'])) {

            $sort_direction = $params['sort_direction'];

            switch ($params['sort_field']) {
                case "project_name" :
                    $select->order("$projectTableName.title $sort_direction");
                    break;
                case "owner" :
                    $select->order("$tableUserName.displayname $sort_direction");
                    break;
                case "project_order" :
                    $select->order("$projectTableName.project_order $sort_direction");
                    break;
                case "project_status" :
                    $select->order("$projectTableName.state $sort_direction");
                    break;
                case "funding_status" :
                    $select->order("$projectTableName.funding_state $sort_direction");
                    break;
                case "goal_amount" :
                    $select->order("$projectTableName.goal_amount $sort_direction");
                    break;
                case "date" :
                    $select->order("$projectTableName.creation_date $sort_direction");
                    break;
                default:
                    $select->order("$projectTableName.creation_date DESC");
            }

        } else {
           // $select->order(new Zend_Db_Expr("-project_order DESC"));
            $select->order("$projectTableName.creation_date DESC");
        }

        return $select;

    }

}