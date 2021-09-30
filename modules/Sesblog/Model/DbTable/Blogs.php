<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Blogs.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Model_DbTable_Blogs extends Engine_Db_Table
{

  protected $_rowClass = "Sesblog_Model_Blog";

  public function packageBlogCount($packageId = null) {
    $count = $this->select()->from($this->info('name'), array("COUNT(blog_id)"))->where('package_id =?', $packageId);
    return $count->query()->fetchColumn();
  }
  
  /**
   * Gets a paginator for sesblogs
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */
  public function getSesblogsPaginator($params = array(), $customFields = array()) {
    $paginator = Zend_Paginator::factory($this->getSesblogsSelect($params, $customFields));
    if (!empty($params['page']))
      $paginator->setCurrentPageNumber($params['page']);
    if (!empty($params['limit']))
      $paginator->setItemCountPerPage($params['limit']);

    if (empty($params['limit'])) {
      $page = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.page', 10);
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
  public function getSesblogsSelect($params = array(), $customFields = array()) {
  
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewerId = $viewer->getIdentity();
    $tableLocationName = Engine_Api::_()->getDbtable('locations', 'sesbasic')->info('name');
    $blogTable = Engine_Api::_()->getDbtable('blogs', 'sesblog');
    $blogTableName = $blogTable->info('name');
    $select = $blogTable->select()
                      ->setIntegrityCheck(false)
                      ->from($blogTableName)
                      ->where($blogTableName.'.owner_id <> ?', 0);

    if (!empty($params['org_id']) && is_numeric($params['org_id']))
    $select->where($blogTableName . '.org_id = ?', $params['org_id']);

    if (!empty($params['project_id']) && is_numeric($params['project_id']))
    $select->where($blogTableName . '.project_id = ?', $params['project_id']);

    if (!empty($params['user_id']) && is_numeric($params['user_id']))
      $select->where($blogTableName . '.owner_id = ?', $params['user_id']);
      
    if (!empty($params['blog_id']) && is_numeric($params['blog_id']))
      $select->where($blogTableName . '.blog_id <> ?', $params['blog_id']);
      
    if (isset($params['parent_type']))
      $select->where($blogTableName . '.parent_type = ?', $params['parent_type']);

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User)
      $select->where($blogTableName . '.owner_id = ?', $params['user']->getIdentity());

    if (isset($params['show']) && $params['show'] == 2 && $viewer->getIdentity()) {
      $users = $viewer->membership()->getMembershipsOfIds();
      if ($users)
        $select->where($blogTableName . '.owner_id IN (?)', $users);
      else
        $select->where($blogTableName . '.owner_id IN (?)', 0);
    }
//     $sql = $select->__toString();
//     echo "$sql\n";
// die;    
    if (empty($params['miles']))
      $params['miles'] = 200;
      
    //Location Based search
    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('seslocation') && Engine_Api::_()->getApi('settings', 'core')->getSetting('seslocationenable', 1) && empty($params['lat']) && !empty($_COOKIE['sesbasic_location_data']) && $params['manage-widget'] != 1) {
      $params['location'] = $_COOKIE['sesbasic_location_data'];
      $params['lat'] = $_COOKIE['sesbasic_location_lat'];
      $params['lng'] = $_COOKIE['sesbasic_location_lng'];
      $params['miles'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seslocation.searchmiles', 50);
    }

    if (isset($params['lat']) && isset($params['miles']) && $params['miles'] != 0 && isset($params['lng']) && $params['lat'] != '' && $params['lng'] != '' && ((isset($params['location']) && $params['location'] != '' && strtolower($params['location']) != 'world'))) {
      $origLat = $params['lat'];
      $origLon = $params['lng'];
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.search.type', 1) == 1)
        $searchType = 3956;
      else
        $searchType = 6371;

      //This is the maximum distance (in miles) away from $origLat, $origLon in which to search
      $dist = $params['miles'];

      $asinSort = array('lat', 'lng', 'distance' => new Zend_Db_Expr(($searchType . " * 2 * ASIN(SQRT( POWER(SIN(($origLat - abs(lat))*pi()/180/2),2) + COS($origLat*pi()/180 )*COS(abs(lat)*pi()/180) *POWER(SIN(($origLon-lng)*pi()/180/2),2)))")));
      $select->joinLeft($tableLocationName, $tableLocationName . '.resource_id = ' . $blogTableName . '.blog_id AND ' . $tableLocationName . '.resource_type = "sesblog_blog" ', $asinSort);
      $select->where($tableLocationName . ".lng between ($origLon-$dist/abs(cos(radians($origLat))*69)) and ($origLon+$dist/abs(cos(radians($origLat))*69)) and " . $tableLocationName . ".lat between ($origLat-($dist/69)) and ($origLat+($dist/69))");
      $select->order('distance');
      $select->having("distance < $dist");
    } else {
      $select->joinLeft($tableLocationName, $tableLocationName . '.resource_id = ' . $blogTableName . '.blog_id AND ' . $tableLocationName . '.resource_type = "sesblog_blog" ', array('lat', 'lng'));
    }

    if (!empty($params['tag'])) {
      $tmName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
      $select->setIntegrityCheck(false)->joinLeft($tmName, "$tmName.resource_id = $blogTableName.blog_id")
        ->where($tmName . '.resource_type = ?', 'sesblog_blog')
        ->where($tmName . '.tag_id = ?', $params['tag']);
    }

    if (!empty($params['alphabet']) && $params['alphabet'] != 'all')
      $select->where($blogTableName . ".title LIKE ?", $params['alphabet'] . '%');

    $currentTime = date('Y-m-d H:i:s');
    if (isset($params['popularCol']) && !empty($params['popularCol'])) {
      if ($params['popularCol'] == 'week') {
        $endTime = date('Y-m-d H:i:s', strtotime("-1 week"));
        $select->where("DATE(" . $blogTableName . ".creation_date) between ('$endTime') and ('$currentTime')");
      } elseif ($params['popularCol'] == 'month') {
        $endTime = date('Y-m-d H:i:s', strtotime("-1 month"));
        $select->where("DATE(" . $blogTableName . ".creation_date) between ('$endTime') and ('$currentTime')");
      } else {
        $select = $select->order($blogTableName . '.' . $params['popularCol'] . ' DESC');
      }
    }

    if (isset($params['fixedData']) && !empty($params['fixedData']) && $params['fixedData'] != '')
      $select = $select->where($blogTableName . '.' . $params['fixedData'] . ' =?', 1);

    if (isset($params['featured']) && !empty($params['featured']))
      $select = $select->where($blogTableName . '.featured =?', 1);

    if (isset($params['verified']) && !empty($params['verified']))
      $select = $select->where($blogTableName . '.verified =?', 1);

    if (isset($params['sponsored']) && !empty($params['sponsored']))
      $select = $select->where($blogTableName . '.sponsored =?', 1);

    if (!empty($params['category_id']))
      $select = $select->where($blogTableName . '.category_id =?', $params['category_id']);

    if (!empty($params['subcat_id']))
      $select = $select->where($blogTableName . '.subcat_id =?', $params['subcat_id']);

    if (!empty($params['subsubcat_id']))
      $select = $select->where($blogTableName . '.subsubcat_id =?', $params['subsubcat_id']);

    if (isset($params['draft']))
      $select->where($blogTableName . '.draft = ?', $params['draft']);

    if (!empty($params['text']))
      $select->where($blogTableName . ".title LIKE ? OR " . $blogTableName . ".body LIKE ?", '%' . $params['text'] . '%');

    if (!empty($params['date']))
      $select->where("DATE_FORMAT(" . $blogTableName . ".creation_date, '%Y-%m-%d') = ?", date('Y-m-d', strtotime($params['date'])));

    if (!empty($params['start_date']))
      $select->where($blogTableName . ".creation_date = ?", date('Y-m-d', $params['start_date']));

    if (!empty($params['end_date']))
      $select->where($blogTableName . ".creation_date < ?", date('Y-m-d', $params['end_date']));

    if (!empty($params['visible']))
      $select->where($blogTableName . ".search = ?", $params['visible']);

    if (!isset($params['manage-widget'])) {
      $select->where($blogTableName . ".publish_date <= '$currentTime' OR " . $blogTableName . ".publish_date = ''");
      $select->where($blogTableName . '.is_approved = ?', (bool)1)->where($blogTableName . '.draft = ?', (bool)0)->where($blogTableName . '.search = ?', (bool)1);
			//check package query
			if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesblogpackage') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.enable.package', 1)){
				$order = Engine_Api::_()->getDbTable('orderspackages','sesblogpackage');
				$orderTableName = $order->info('name');
				$select->joinLeft($orderTableName, $orderTableName . '.orderspackage_id = ' . $blogTableName . '.orderspackage_id',null);
				$select->where($orderTableName . '.expiration_date  > "'.date("Y-m-d H:i:s").'" || expiration_date IS NULL || expiration_date = "0000-00-00 00:00:00"');
			}
    } else
      $select->where($blogTableName . '.owner_id = ?', $viewerId);


    if (isset($params['criteria'])) {
      if ($params['criteria'] == 1)
        $select->where($blogTableName . '.featured =?', '1');
      else if ($params['criteria'] == 2)
        $select->where($blogTableName . '.sponsored =?', '1');
      else if ($params['criteria'] == 3)
        $select->where($blogTableName . '.featured = 1 OR ' . $blogTableName . '.sponsored = 1');
      else if ($params['criteria'] == 4)
        $select->where($blogTableName . '.featured = 0 AND ' . $blogTableName . '.sponsored = 0');
      else if ($params['criteria'] == 6)
        $select->where($blogTableName . '.verified =?', '1');
    }


    if (isset($params['order']) && !empty($params['order'])) {
      if ($params['order'] == 'week') {
        $endTime = date('Y-m-d H:i:s', strtotime("-1 week"));
        $select->where("DATE(" . $blogTableName . ".creation_date) between ('$endTime') and ('$currentTime')");
      } elseif ($params['order'] == 'month') {
        $endTime = date('Y-m-d H:i:s', strtotime("-1 month"));
        $select->where("DATE(" . $blogTableName . ".creation_date) between ('$endTime') and ('$currentTime')");
      }
    }

    if (isset($params['widgetName']) && !empty($params['widgetName']) && $params['widgetName'] == 'Similar Blogs') {
      if (!empty($params['widgetName'])) {
        $select->where($blogTableName . '.category_id =?', $params['category_id']);
        $endTime = date('Y-m-d H:i:s', strtotime("-1 week"));
        $select->where("DATE(" . $blogTableName . ".creation_date) between ('$endTime') and ('$currentTime')");
        $select->order('featured DESC');
        $select->order('view_count DESC');
      }
    }

    if (isset($params['similar_blog']))
      $select->where($blogTableName . '.parent_id =?', $params['blog_id']);

    if (isset($customFields['has_photo']) && !empty($customFields['has_photo'])) {
      $select->where($blogTableName . '.photo_id != ?', "0");
    }

    if (isset($params['info'])) {
      switch ($params['info']) {
        case 'recently_created':
          $select->order($blogTableName . '.creation_date DESC');
          break;
        case 'most_viewed':
          $select->order($blogTableName . '.view_count DESC');
          break;
        case 'most_liked':
          $select->order($blogTableName . '.like_count DESC');
          break;
        case 'most_favourite':
          $select->order($blogTableName . '.favourite_count DESC');
          break;
        case 'most_commented':
          $select->order($blogTableName . '.comment_count DESC');
          break;
        case 'most_rated':
          $select->order($blogTableName . '.rating DESC');
          break;
        case 'random':
          $select->order('Rand()');
          break;
      }
    }
    if (!empty($params['getblog'])) {
      $select->where($blogTableName . ".title LIKE ? OR " . $blogTableName . ".body LIKE ?", '%' . $params['textSearch'] . '%')->where($blogTableName . ".search = ?", 1);
    }

    //don't show other module blogs
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.other.modulsesblogs', 1) && empty($params['resource_type'])) {
      $select->where($blogTableName . '.resource_type IS NULL')
        ->where($blogTableName . '.resource_id =?', 0);
    } else if (!empty($params['resource_type']) && !empty($params['resource_id'])) {
      $select->where($blogTableName . '.resource_type =?', $params['resource_type'])
        ->where($blogTableName . '.resource_id =?', $params['resource_id']);
    } else if (!empty($params['resource_type'])) {
      $select->where($blogTableName . '.resource_type =?', $params['resource_type']);
    }
    //don't show other module blogs
    
    //Network  Privacy work
    $networkSqlExecute = false;
    if ($viewerId) {
      $network_table = Engine_Api::_()->getDbTable('membership', 'network');
      $network_select = $network_table->select('resource_id')->where('user_id = ?', $viewerId);
      $network_id_query = $network_table->fetchAll($network_select);
      $network_id_query_count = count($network_id_query);
      $networkSql = '(';
      for ($i = 0; $i < $network_id_query_count; $i++) {
        $networkSql = $networkSql . "CONCAT(',',CONCAT(networks,',')) LIKE '%,". $network_id_query[$i]['resource_id'] .",%' || ";
      }
      $networkSql = trim($networkSql, '|| ') . ')';
      if ($networkSql != '()') {
        $networkSqlExecute = true;
        $networkSql = $networkSql . ' || networks IS NULL || networks = "" || ' . $blogTableName . '.owner_id =' . $viewerId;
        $select->where($networkSql);
      }
    }

    if (!$networkSqlExecute) {
      $networkUser = '';
      if ($viewerId)
        $networkUser = ' ||' . $blogTableName . '.owner_id =' . $viewerId . ' ';
      $select->where('networks IS NULL || networks = ""  ' . $networkUser);
    }
    //Network  Privacy work

    $select->order(!empty($params['orderby']) ? $params['orderby'] . ' DESC' : $blogTableName . '.creation_date DESC');
    if (isset($params['fetchAll'])) {
      if (!isset($params['rss'])) {
        if (empty($params['limit']))
          $select->limit(3);
        else
          $select->limit($params['limit']);
      }
      return $this->fetchAll($select);
    } else
      return $select;
  }

  public function getBlog($params = array())
  {

    $table = Engine_Api::_()->getDbtable('blogs', 'sesblog');
    $blogTableName = $table->info('name');
    $select = $table->select()
      ->where($blogTableName . '.draft = ?', 0)
      ->where($blogTableName . ".title LIKE ? OR " . $blogTableName . ".body LIKE ?", '%' . $params['text'] . '%')
      ->where($blogTableName . ".search = ?", 1)
      ->order('creation_date DESC');

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.other.modulsesblogs', 1) && empty($params['resource_type'])) {
      $select->where($blogTableName . '.resource_type IS NULL')->where($blogTableName . '.resource_id =?', 0);
    }

    return $this->fetchAll($select);
  }

  /**
   * Returns an array of dates where a given user created a sesblog entry
   *
   * @param User_Model_User user to calculate for
   * @return Array Dates
   */
  public function getArchiveList($spec)
  {

    if (!($spec instanceof User_Model_User))
      return null;

    $localeObject = Zend_Registry::get('Locale');
    if (!$localeObject)
      $localeObject = new Zend_Locale();

    $dates = $this->select()
      ->from($this, 'creation_date')
      ->where('owner_type = ?', 'user')
      ->where('owner_id = ?', $spec->getIdentity())
      ->where('draft = ?', 0)
      ->order('blog_id DESC')
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);

    $time = time();

    $archive_list = array();
    foreach ($dates as $date) {

      $date = strtotime($date);
      $ltime = localtime($date, true);
      $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
      $ltime["tm_year"] = $ltime["tm_year"] + 1900;

      // LESS THAN A YEAR AGO - MONTHS
      if ($date + 31536000 > $time) {
        $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, $ltime["tm_mon"] + 1, 1, $ltime["tm_year"]);
        $type = 'month';

        $dateObject = new Zend_Date($date);
        $format = $localeObject->getTranslation('yMMMM', 'dateitem', $localeObject);
        $label = $dateObject->toString($format, $localeObject);
      } // MORE THAN A YEAR AGO - YEARS
      else {
        $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"] + 1);
        $type = 'year';

        $dateObject = new Zend_Date($date);
        $format = $localeObject->getTranslation('yyyy', 'dateitem', $localeObject);
        if (!$format)
          $format = $localeObject->getTranslation('y', 'dateitem', $localeObject);

        $label = $dateObject->toString($format, $localeObject);
      }

      if (!isset($archive_list[$date_start])) {
        $archive_list[$date_start] = array(
          'type' => $type,
          'label' => $label,
          'date' => $date,
          'date_start' => $date_start,
          'date_end' => $date_end,
          'count' => 1
        );
      } else
        $archive_list[$date_start]['count']++;
    }
    return $archive_list;
  }

  public function getOfTheDayResults()
  {
    return $this->select()
      ->from($this->info('name'), 'blog_id')
      ->where('offtheday =?', 1)
      ->where('starttime <= DATE(NOW())')
      ->where('endtime >= DATE(NOW())')
      ->order('RAND()')
      ->query()
      ->fetchColumn();
  }

  public function checkCustomUrl($value = '', $blog_id = '')
  {
    $select = $this->select('blog_id')->where('custom_url = ?', $value);
    if ($blog_id)
      $select->where('blog_id !=?', $blog_id);
    return $select->query()->fetchColumn();
  }

  public function getBlogId($slug = null)
  {
    if ($slug) {
      $tableName = $this->info('name');
      $select = $this->select()
        ->from($tableName)
        ->where($tableName . '.custom_url = ?', $slug);
      $row = $this->fetchRow($select);
      if (empty($row)) {
        $blog_id = $slug;
      } else
        $blog_id = $row->blog_id;
      return $blog_id;
    }
    return '';
  }
  public function getBlogIdForScroll($allid, $categoryid)
  {
    $select = $this->select()
      ->from($this->info('name'),'blog_id')
      ->where('blog_id !=(?)', $allid);
    if(!empty($categoryid))
    {
      $select=$select->where('category_id = ?',$categoryid);
    }
    $select=$select->order('blog_id DESC')
    ->query()
    ->fetchAll();

    return $select;
  }
  
  public function getItemCount($params = array()) {
    
    $select = $this->select()->from($this->info('name'), 'count(*) AS total');
    
    if(isset($params['columnName']) && !empty($params['columnName']))
      $select = $select->where($params['columnName'].' =?', 1);
    
    return $select->query()->fetchColumn();
  }
}
