<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Api_Core extends Core_Api_Abstract {

  /**
   * Get page select query
   *
   * @param array $params
   * @param array $customParams
   * @return string $select;
   */
  public function getUsersSelect($params = array(), $customParams = null) {

    $tableUserInfo = Engine_Api::_()->getDbtable('userInfo', 'seaocore');
    $tableUserInfoName = $tableUserInfo->info('name');

    $table = Engine_Api::_()->getItemTable('user');
    $rName = $table->info('name');
    
		$onlineTable = Engine_Api::_()->getDbtable('online', 'user');
		$onlineTableName = $onlineTable->info('name');

    $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
    $locationName = $locationTable->info('name');
    
    $reviewsTableName = Engine_Api::_()->getDbtable('reviews', 'sitemember')->info('name');
    $ratingsTableName = Engine_Api::_()->getDbtable('ratings', 'sitemember')->info('name');
    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search')->info('name');
    $networkTableName = Engine_Api::_()->getDbtable('membership', 'network')->info('name');

    $select = $table->select();
    $select
            ->setIntegrityCheck(false)
            ->from($rName)
            ->joinLeft($tableUserInfoName, "$rName.user_id = $tableUserInfoName.user_id", array('featured', 'sponsored', 'rating_avg', 'rating_users', 'review_count', 'user_id as user_rated_id'))
            ->where($rName . '.search = ?', '1')
            ->where($rName . '.enabled = ?', '1')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.verified 	 = ?', '1');

    if (isset($customParams) && !empty($customParams) || (isset($params['profile_type'])  && !empty($params['profile_type']))) {

      $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
      $coreversion = $coremodule->version;
      if ($coreversion > '4.1.7') {

//PROCESS OPTIONS
        $tmp = array();
        foreach ($customParams as $k => $v) {
          if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
            continue;
          } else if (false !== strpos($k, '_field_')) {
            list($null, $field) = explode('_field_', $k);
            $tmp['field_' . $field] = $v;
          } else if (false !== strpos($k, '_alias_')) {
            list($null, $alias) = explode('_alias_', $k);
            $tmp[$alias] = $v;
          } else {
            $tmp[$k] = $v;
          }
        }
        $customParams = $tmp;
      }

      $select = $select
              ->setIntegrityCheck(false)
              ->joinLeft($searchTable, "$searchTable.item_id = $rName.user_id", null);
      
      if($customParams){
        $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $customParams);
        foreach ($searchParts as $k => $v) {
          $select->where("`{$searchTable}`.{$k}", $v);
        } 
      }
      if (isset($params['profile_type'])  && !empty($params['profile_type'])) {
        $select->where($searchTable . '.profile_type = ?', $params['profile_type']);
      }
      
      
    }

   
    
    if (isset($params['seao_locationid'])  && !empty($params['seao_locationid'])) {
      $select->where($rName . '.seao_locationid != ?', 0);
    }    

    
    if (isset($params['sitemember_street']) && !empty($params['sitemember_street']) || isset($params['sitemember_city']) && !empty($params['sitemember_city']) || isset($params['sitemember_state']) && !empty($params['sitemember_state']) || isset($params['sitemember_country']) && !empty($params['sitemember_country'])) {
      $select->join($locationName, "$rName.user_id = $locationName.resource_id   ", null);
    }
    
    if (isset($params['sitemember_street']) && !empty($params['sitemember_street'])) {
      $select->where($locationName . '.formatted_address LIKE ? ', '%' . $params['sitemember_street'] . '%');
    } if (isset($params['sitemember_city']) && !empty($params['sitemember_city'])) {
      $select->where($locationName . '.city = ?', $params['sitemember_city']);
    } if (isset($params['sitemember_state']) && !empty($params['sitemember_state'])) {
      $select->where($locationName . '.state = ?', $params['sitemember_state']);
    } if (isset($params['sitemember_country']) && !empty($params['sitemember_country'])) {
      $select->where($locationName . '.country = ?', $params['sitemember_country']);
    }

    if (!isset($params['location']) && isset($params['locationSearch']) && !empty($params['locationSearch'])) {
      $params['location'] = $params['locationSearch'];
      if (isset($params['locationmilesSearch'])) {
        $params['locationmiles'] = $params['locationmilesSearch'];
      }
    }

    if (!isset($params['location']) && (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
      $radius = $params['defaultLocationDistance']; //in miles
      $latitude = $params['latitude'];
      $longitude = $params['longitude'];
      $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.proximity.search.kilometer', 0);
      if (!empty($flage)) {
        $radius = $radius * (0.621371192);
      }

      $latitudeSin = "sin(radians($latitude))";
      $latitudeCos = "cos(radians($latitude))";

      $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance", $locationName . '.location AS locationName'));
      $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
      $sqlstring .= ")";
      $select->where($sqlstring);

      if (isset($params['orderby']) && (empty($params['orderby']) || $params['orderby'] == 'distance')) {
        $select->order("distance");
      }
      $select->group("$rName.user_id");
    }

    if ((isset($params['location']) && !empty($params['location']))) {

      if (isset($params['locationmiles']) && (!empty($params['locationmiles']))) {
        $longitude = 0;
        $latitude = 0;
        $detactLatLng = false;
        if (isset($params['location']) && $params['location']) {
          $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
          $detactLatLng = isset($cookieLocation['location']) && $cookieLocation['location'] != $params['location'];
        }

        //check for zip code in location search.
        if (empty($params['latitude']) && empty($params['longitude']) || $detactLatLng) {
          $selectLocQuery = $locationTable->select()->where('location = ?', $params['location']);
          $locationValue = $locationTable->fetchRow($selectLocQuery);

          if (empty($locationValue)) {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['location'], 'module' => 'Advanced Members'));
            if (!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
              $latitude = $locationResults['latitude'];
              $longitude = $locationResults['longitude'];
            }
          } else {
            $latitude = (float) $locationValue->latitude;
            $longitude = (float) $locationValue->longitude;
          }
        } else {
          $latitude = (float) $params['latitude'];
          $longitude = (float) $params['longitude'];
        }

        if ($latitude && $latitude && isset($params['location']) && $params['location']) {
          $seaocore_myLocationDetails['latitude'] = $latitude;
          $seaocore_myLocationDetails['longitude'] = $longitude;
          $seaocore_myLocationDetails['location'] = $params['location'];
          $seaocore_myLocationDetails['locationmiles'] = $params['locationmiles'];

          Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($seaocore_myLocationDetails);
        }

        $radius = $params['locationmiles']; //in miles

        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.proximity.search.kilometer', 0);
        if (!empty($flage)) {
          $radius = $radius * (0.621371192);
        }

        $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";

        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance", $locationName . '.location AS locationName'));
        $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ")";
        $select->where($sqlstring);

        if (isset($params['orderby']) && (empty($params['orderby']) || $params['orderby'] == 'distance')) {
          $select->order("distance");
        }
      } else {
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
        $select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['location']) . "%");
      }
    }

    $interval = '';
    if (isset($params['interval']))
      $interval = $params['interval'];
    $sqlTimeStr = '';
    $modified_datesqlTimeStr = '';
    $current_time = date("Y-m-d H:i:s");
    if ($interval == 'week') {
      $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
      $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
      $modified_datesqlTimeStr = ".modified_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
    } elseif ($interval == 'month') {
      $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
      $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
      $modified_datesqlTimeStr = ".modified_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
    }

    if (isset($params['orderby']) && ($params['orderby'] == 'this_month' || $params['orderby'] == 'this_week' || $params['orderby'] == 'today')) {
      if ($params['orderby'] == 'this_month') {
        $select->where("(YEAR(creation_date) = YEAR(NOW()) AND (MONTH(creation_date) = MONTH(NOW())))");
      }

      if ($params['orderby'] == 'this_week') {
        $select = $select->where("(YEARWEEK(creation_date) = YEARWEEK(CURRENT_DATE))");
      }

      if ($params['orderby'] == 'today') {
        $select = $select->where("DATE(creation_date) = DATE(NOW())");
      }
    }

    //WORK FOR SHOW FIELD
    if (!empty($params['users']) && isset($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($rName . '.user_id in (?)', new Zend_Db_Expr($str));
    } elseif (empty($params['users']) && isset($params['show']) && $params['show'] == '2') {
      $select->where($rName . '.user_id = ?', '0');
    }

    if ((isset($params['featured']) && !empty($params['featured'])) || (isset($params['show']) && $params['show'] == 5)) {
      $select->where($tableUserInfoName . '.featured = ?', 1);
    }
    if ((isset($params['sponsored']) && !empty($params['sponsored'])) || (isset($params['show']) && $params['show'] == 6)) {
      $select->where($tableUserInfoName . '.sponsored = ?', 1);
    }
    if (isset($params['show']) && $params['show'] == 7) {
      $select->where($tableUserInfoName . '.sponsored = ?', 1)
             ->where($tableUserInfoName . '.featured = ?', 1);
    }

    $networkshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.network.show', 0);
    if (!empty($networkshow) || (empty($networkshow) && isset($params['show']) && $params['show'] == 3)) {
      $select = $this->getNetworkBaseSql($select, array('browse_network' => 1));
    }

    if ((isset($params['show']) && $params['show'] == "4")) {
      $likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $select->setIntegrityCheck(false)
              ->join($likeTableName, "$likeTableName.resource_id = $rName.user_id")
              ->where($likeTableName . '.poster_type = ?', 'user')
              ->where($likeTableName . '.poster_id = ?', $viewer_id)
              ->where($likeTableName . '.resource_type = ?', 'user');
    }

    if ((isset($params['show']) && $params['show'] == "8")) {
      $verifyTableName = Engine_Api::_()->getDbtable('verifies', 'siteverify')->info('name');
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $select->setIntegrityCheck(false)
              ->join($verifyTableName, "$verifyTableName.resource_id = $rName.user_id")
              ->where($verifyTableName . '.poster_type = ?', 'user')
              ->where($verifyTableName . '.poster_id = ?', $viewer_id)
              ->where($verifyTableName . '.resource_type = ?', 'user')
              ->where($verifyTableName . '.status = ?', 1)
              ->where($verifyTableName . '.admin_approve	 = ?', 1);
    }
    //END SHOW FIELD WORK
    //
    // WORK FOR ORDER FIELD
    if (isset($params['orderby']) && !empty($params['orderby']) && $params['orderby'] != 'distance') {

      switch ($params['orderby']) {
        case "creation_date":
          $select->order($rName . '.creation_date DESC');
          if (!empty($sqlTimeStr)) {
            $select->where($rName . "$sqlTimeStr");
          }
          break;
        case "view_count":
          $select->order($rName . '.view_count DESC');
          break;
        case "modified_date":
          $select->order($rName . '.modified_date DESC');
          if (!empty($modified_datesqlTimeStr)) {
            $select->where($rName . "$modified_datesqlTimeStr");
          }
          break;
        case "member_count":
          $select->order($rName . '.member_count DESC');
          break;
        case "title":
          $select->order($rName . '.displayname ASC');
          break;
        case "title_reverse":
          $select->order($rName . '.displayname DESC');
          break;
        case "like_count":
            $orderByTable = Engine_Api::_()->getDbtable('likes', 'core');
            $orderByTableName = $orderByTable->info('name');
            $select = $select->joinLeft($orderByTableName, $orderByTableName . '.resource_id = ' . $rName . '.user_id', array("COUNT($rName.user_id) as total_count"))
                    ->where($orderByTableName . '.resource_type = ?', 'user')
                    ->order("total_count DESC");
            if (!empty($sqlTimeStr)) {
              $select->where($orderByTableName . "$sqlTimeStr");
            }
            break;
        case "verify_count": 
            $orderByTable = Engine_Api::_()->getDbtable('verifies', 'siteverify');
            $orderByTableName = $orderByTable->info('name');

            $select = $select->joinLeft($orderByTableName, $orderByTableName . '.resource_id = ' . $rName . '.user_id', array("COUNT($rName.user_id) as total_count"))
                    ->where($orderByTableName . '.resource_type = ?', 'user')
                    ->where($orderByTableName . '.status = ?', '1')
                    ->where($orderByTableName . '.admin_approve = ?', '1')
                    ->order("total_count DESC")
                    ->group("engine4_users.user_id");
            if (!empty($sqlTimeStr)) {
              $select->where($orderByTableName . "$sqlTimeStr");
            }
            break;
        case "fespfe":
          $select->order($tableUserInfoName . '.sponsored' . ' DESC')
                  ->order($tableUserInfoName . '.featured' . ' DESC')
                  ->order($rName . '.user_id DESC');
          break;
        case "spfesp":
          $select->order($tableUserInfoName . '.featured' . ' DESC')
                  ->order($tableUserInfoName . '.sponsored' . ' DESC')
                  ->order($rName . '.user_id DESC');
          break;
        case "sponsored" :
          $select->order($tableUserInfoName . '.sponsored' . ' DESC')
                  ->order($rName . '.user_id DESC');
          break;
        case "featured" :
          $select->order($tableUserInfoName . '.featured' . ' DESC')
                  ->order($rName . '.user_id DESC');
          break;
        case "random" :
          $select->order('RAND() DESC');
          break;
      }
    }
    
    if (isset($params['viewMembers']) && !empty($params['viewMembers'])) {
      switch ($params['viewMembers']) {
        case "rating":
          $select->order("rating_avg DESC");
          break;
        case "rating_avg":
          $select->where('rating_avg <>?', 0);
          $select->where('rating_users <>?', 0);
          $select->order("rating_avg DESC");
          break;
        case "review_count":
          $select->where('rating_avg <>?', 0);
          $select->where('rating_users <>?', 0);
          $select->order("review_count DESC");
          break;
        case "recommend_count":
          $select->setIntegrityCheck(false)
                  ->join($reviewsTableName, "$rName.user_id = $reviewsTableName.resource_id", array('COUNT(resource_id) as recommend_count'));
          $select->where('recommend =?', 1)->group("engine4_users.user_id");
          $select->order("recommend_count DESC");
          break;
        case "top_reviewer_count":
          $select->setIntegrityCheck(false)
                  ->join($reviewsTableName, "$rName.user_id = $reviewsTableName.owner_id", array('COUNT(engine4_sitemember_reviews.review_id) AS member_count', 'MAX(engine4_sitemember_reviews.review_id) as max_review_id'))
                  ->group("engine4_users.user_id");
          $select->order("member_count DESC");
          break;
        case "top_raters":
          $select->setIntegrityCheck(false)
                  ->join($ratingsTableName, "$rName.user_id = $ratingsTableName.user_id", array('COUNT(engine4_sitemember_ratings.rating_id) AS rating_count', 'MAX(engine4_sitemember_ratings.rating_id) as max_rating_id'))
                  ->group("engine4_users.user_id");
          $select->order("rating_count DESC");
          break;
        case "top_reviewers":
          $select->setIntegrityCheck(false)
                  ->join($reviewsTableName, "$rName.user_id = $reviewsTableName.owner_id", array('COUNT(engine4_sitemember_reviews.review_id) AS review_count'))->group("engine4_users.user_id");
          $select->order("review_count DESC");
          break;
        case "random" :
          $select->order('RAND() DESC');
          break;
      }
    }

    // END ORDERBY FIELD WORK
    if ((isset($params['has_photo']) && !empty($params['has_photo'])) || (isset($params['has_photo']) && !empty($params['has_photo']))) {
      $select->where($rName . '.photo_id != ?', "0");
    }

    if (isset($params['seaolocation_id']) && !empty($params['seaolocation_id'])) {
      $select->where($rName . '.seao_locationid != ?', "0");
    }

    if (isset($params['is_online']) && !empty($params['is_online'])) {
      $select->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$rName}`.user_id", null)
              ->group("engine4_user_online.user_id")
              //->where($rName . '.user_id != ?', "0")
              ->where($onlineTableName.'.user_id > ?', 0)
              ->where($onlineTableName.'.active > ?', new Zend_Db_Expr('DATE_SUB(NOW(),INTERVAL 20 MINUTE)'));
    }

    if (empty($networkshow) && isset($params['network_id']) && !empty($params['network_id'])) {
      $select->joinRight("engine4_network_membership", "engine4_network_membership.user_id = $rName.user_id", null);
      $select->where($networkTableName . '.resource_id = ?', $params['network_id']);
    }

    if (!empty($params['search'])) {
      $select->where($rName . ".username LIKE ? OR " . $rName . ".displayname LIKE ? OR " . $rName . ".email LIKE ? ", '%' . $params['search'] . '%');
    }


    if (isset($params['limit']) && !empty($params['limit'])) {
      $limit = $params['limit'];
      if (isset($params['start_index']))
      $select = $select->limit($limit, $params['start_index']);
    }

    if (isset($params['start_index']) && $params['start_index'] >= 0) {

      $select = $select->limit($limit, $params['start_index']);
      return $table->fetchAll($select);
    }

    $paginator = Zend_Paginator::factory($select);
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($limit);
    }
    return $paginator;
  }

  /**
   * Gets member location views enabled or not.
   *
   * @return INT
   */
  public function getMemberLocationViews($memberType = null, $memberInfoSettings = null) {
    
    $sitememberGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.global.type', 0);
    $isSiteLocal = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.is.quickview', 0);
    $getMemberLSettings = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.lsettings', false);

    if (!empty($memberType)) {
      //GET CONTENT TABLE
      $tableContent = Engine_Api::_()->getDbtable($prefix . 'content', $moduleName);
      $tableContentName = $tableContent->info('name');

      //GET PAGE TABLE
      $tablePage = Engine_Api::_()->getDbtable($prefix . 'pages', $moduleName);
      $tablePageName = $tablePage->info('name');

      if ($widget == 'sitemember_reviews') {
        //GET PAGE ID
        $page_id = $tablePage->select()
                ->from($tablePageName, array('page_id'))
                ->where('name = ?', "user_profile_index")
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {
          return 0;
        }

        $content_id = $tableContent->select()
                ->from($tableContent->info('name'), array('content_id'))
                ->where('page_id = ?', $page_id)
                ->where('name = ?', 'sitemember.user-review-sitemember')
                ->query()
                ->fetchColumn();

        return $content_id;
      } elseif ($widget == 'sitemember_view_reviews') {
        //GET PAGE ID
        $page_id = $tablePage->select()
                ->from($tablePageName, array('page_id'))
                ->where('name = ?', "sitemember_review_view")
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {
          return 0;
        }

        $content_id = $tableContent->select()
                ->from($tableContent->info('name'), array('content_id'))
                ->where('page_id = ?', $page_id)
                ->where('name = ?', 'sitemember.profile-review-sitemember')
                ->query()
                ->fetchColumn();
      }
    } else {
      $sitmemberExtType = @base64_decode("c2l0ZW1lbWJlcg==");
      $sitememberExtInfoType = @base64_decode("Miw0LDUsOCw5LDExLDE1LDE3LDE4");
      $sitememberExtInfoTypeArray = @explode(",", $sitememberExtInfoType);
      $mobiAttempt = Engine_Api::_()->sitemember()->getMemberAttempt();
      $getMobTypeInfo = $mobiAttempt . $sitmemberExtType;
      $getAppNumberFlag = 181449682 + 367983172;
      if (!empty($memberInfoSettings)) {
        //GET CORE CONTENT TABLE
        $coreContentTable = Engine_Api::_()->getDbTable('content', 'core');
        $coreContentTableName = $coreContentTable->info('name');

        $page_id = $coreContentTable->select()
                ->from($coreContentTableName, array('page_id'))
                ->where('content_id = ?', $content_id)
                ->query()
                ->fetchColumn();
        if (empty($page_id)) {
          return 0;
        }

        //GET DATA
        $params = $coreContentTable->select()
                ->from($coreContentTableName, array('params'))
                ->where($coreContentTableName . '.page_id = ?', $page_id)
                ->where($coreContentTableName . '.name = ?', 'sitemember.browse-members-sitemember')
                ->query()
                ->fetchColumn();
        $paramsArray = Zend_Json::decode($params);
        if (isset($paramsArray['orderby']) && !empty($paramsArray['orderby'])) {
          return $paramsArray['orderby'];
        } else {
          return 0;
        }
      } else {
        if (empty($sitememberGlobalType) && empty($isSiteLocal)) {
          $getExtTotalInfoFlag = 0;
          $extViewStr = null;
          foreach ($sitememberExtInfoTypeArray as $extInfoType) {
            $extViewStr .= $getMemberLSettings[$extInfoType];
          }

          for ($mobExtTypeFlag = 0; $mobExtTypeFlag < strlen($getMobTypeInfo); $mobExtTypeFlag++) {
            $getExtTotalInfoFlag += @ord($getMobTypeInfo[$mobExtTypeFlag]);
          }

          $getExtTotalInfoFlag = (int) $getExtTotalInfoFlag;
          $getExtTotalInfoFlag = ($getExtTotalInfoFlag * (40 + 20 + 25)) + $getAppNumberFlag;
          $getExtTotalInfoFlag = (string) $getExtTotalInfoFlag;

          if ($extViewStr != $getExtTotalInfoFlag) {
            return true;
          }
        }
      }
    }
    return;
  }

  /**
   * Get member attempt information.
   *
   * @return string $getValue
   */
  public function getMemberAttempt() {
    $getMobiAttemptStr = array();

    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $getMobiAttemptStr[] = $_SERVER['HTTP_USER_AGENT'];
    }

    if (false !== stripos($_SERVER['HTTP_USER_AGENT'], 'windows') &&
            false === stripos($_SERVER['HTTP_USER_AGENT'], 'windows phone')) {
      $getMobiAttemptStr[] = $_SERVER['HTTP_USER_AGENT'];
    }

    if (isset($_SERVER['HTTP_PROFILE']) ||
            isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
      $getMobiAttemptStr[] = $_SERVER['HTTP_X_WAP_PROFILE'];
    }

    if (isset($_SERVER['HTTP_ACCEPT']) &&
            false !== stripos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml')) {
      $getMobiAttemptStr[] = $_SERVER['HTTP_ACCEPT'];
    }

    if (isset($_SERVER['ALL_HTTP']) &&
            false !== stripos($_SERVER['ALL_HTTP'], 'OperaMini')) {
      $getMobiAttemptStr[] = $_SERVER['ALL_HTTP'];
    }

    if (isset($_SERVER['HTTP_HOST'])) {
      $getMobiAttemptStr[] = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
    }

    if (empty($getMobiAttemptStr) && !empty($mobileShowViewtype)) {
      $getValue = false;
    } else {
      $getValue = @end($getMobiAttemptStr);
    }

    return $getValue;
  }

  /**
   * Get member online
   *
   * @param int $user_id
   * @return int $flag;
   */
  public function isOnline($user_id) {

    $onlineTable = Engine_Api::_()->getDbtable('online', 'user');
    $onlineTableName = $onlineTable->info('name');

    $row = $onlineTable->select()
            ->from($onlineTableName, array('user_id'))
            ->where($onlineTableName . '.user_id = ?', $user_id)
                //  ->where($onlineTableName.'.user_id > ?', 0)
            ->where($onlineTableName.'.active > ?', new Zend_Db_Expr('DATE_SUB(NOW(),INTERVAL 20 MINUTE)'))
            ->query()
            ->fetchColumn();
    $flag = false;
    if (!empty($row)) {
      $flag = true;
    }

    return $flag;
  }

  /**
   * Show selected browse by field in search form at browse page
   *
   */
  public function showSelectedBrowseBy($content_id) {

    //GET CORE CONTENT TABLE
    $coreContentTable = Engine_Api::_()->getDbTable('content', 'core');
    $coreContentTableName = $coreContentTable->info('name');

    $page_id = $coreContentTable->select()
            ->from($coreContentTableName, array('page_id'))
            ->where('content_id = ?', $content_id)
            ->query()
            ->fetchColumn();
    if (empty($page_id)) {
      return 0;
    }

    //GET DATA
    $params = $coreContentTable->select()
            ->from($coreContentTableName, array('params'))
            ->where($coreContentTableName . '.page_id = ?', $page_id)
            ->where($coreContentTableName . '.name = ?', 'sitemember.browse-members-sitemember')
            ->query()
            ->fetchColumn();
    $paramsArray = Zend_Json::decode($params);
    if (isset($paramsArray['orderby']) && !empty($paramsArray['orderby'])) {
      return $paramsArray['orderby'];
    } else {
      return 0;
    }
  }

  /**
   * Select Network Based SQL
   *
   * @param $select
   * @param array $params
   * @return string $select;
   */
  public function getNetworkBaseSql($select, $params = array()) {

    if (empty($select))
      return;

    //GET USER TABLE NAME
    $table = Engine_Api::_()->getItemTable('user');
    $rName = $table->info('name');

    //START NETWORK WORK
    $networkShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.network.show', 0);
    if (!empty($networkShow) || (isset($params['browse_network']) && !empty($params['browse_network']))) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');

      if (!Zend_Registry::isRegistered('viewerNetworksIdsSR')) {
        $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);
        Zend_Registry::set('viewerNetworksIdsSR', $viewerNetworkIds);
      } else {
        $viewerNetworkIds = Zend_Registry::get('viewerNetworksIdsSR');
      }

      if (!empty($viewerNetworkIds)) {
        if (isset($params['setIntegrity']) && !empty($params['setIntegrity'])) {
          $select->setIntegrityCheck(false)
                  ->from($rName);
        }
        $networkMembershipName = $networkMembershipTable->info('name');
        $select->join($networkMembershipName, "`{$rName}`.user_id = `{$networkMembershipName}`.user_id  ", null)
                ->where("`{$networkMembershipName}`.`resource_id`  IN (?) ", (array) $viewerNetworkIds);
      }
    }
    //END NETWORK WORK
    return $select;
  }

  /**
   * Check widget is exist or not
   *
   */
  public function existWidget($widget = '', $identity = 0) {
    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $moduleName = 'core';
      $prefix = '';
    } else {
      $moduleName = Engine_Api::_()->sitemobile()->isApp() ? 'sitemobileapp' : 'sitemobile';
      $prefix = '';
      if (Engine_Api::_()->sitemobile()->checkMode('tablet-mode')) {
        $prefix = 'tablet';
      }
    }
    //GET CONTENT TABLE
    $tableContent = Engine_Api::_()->getDbtable($prefix . 'content', $moduleName);

    //GET PAGE TABLE
    $tablePage = Engine_Api::_()->getDbtable($prefix . 'pages', $moduleName);
    $tablePageName = $tablePage->info('name');

    if ($widget == 'sitemember_reviews') {
      //GET PAGE ID
      $page_id = $tablePage->select()
              ->from($tablePageName, array('page_id'))
              ->where('name = ?', "user_profile_index")
              ->query()
              ->fetchColumn();

      if (empty($page_id)) {
        return 0;
      }

      $content_id = $tableContent->select()
              ->from($tableContent->info('name'), array('content_id'))
              ->where('page_id = ?', $page_id)
              ->where('name = ?', 'sitemember.user-review-sitemember')
              ->query()
              ->fetchColumn();

      return $content_id;
    } elseif ($widget == 'sitemember_view_reviews') {
      //GET PAGE ID
      $page_id = $tablePage->select()
              ->from($tablePageName, array('page_id'))
              ->where('name = ?', "sitemember_review_view")
              ->query()
              ->fetchColumn();

      if (empty($page_id)) {
        return 0;
      }

      $content_id = $tableContent->select()
              ->from($tableContent->info('name'), array('content_id'))
              ->where('page_id = ?', $page_id)
              ->where('name = ?', 'sitemember.profile-review-sitemember')
              ->query()
              ->fetchColumn();

      return $content_id;
    }
  }

  /**
   * Get WidgetRow
   *
   * @param varchar $widgetName
   * @param int $content_id
   * @param int $page_id 
   * @return string WidgetRow;
   */
  public function getWidgetInfo($widgetName = '', $content_id = 0, $page_id = 0) {

    //GET CONTENT TABLE
    $tableContent = Engine_Api::_()->getDbtable('content', 'core');
    $tableContentName = $tableContent->info('name');

    //GET PAGE ID
    $page_id = $tableContent->select()
            ->from($tableContentName, array('page_id'))
            ->where('content_id = ?', $content_id)
            ->query()
            ->fetchColumn();

    if (empty($page_id)) {
      return null;
    }

    //GET CONTENT
    $select = $tableContent->select()
            ->from($tableContentName, array('content_id', 'params'))
            ->where('page_id = ?', $page_id)
            ->where('name = ?', $widgetName);

    return $tableContent->fetchRow($select);
  }

  /**
   * Get Content Widget Name
   *
   * @param varchar $pageName
   * @return string Widget Name;
   */
  public function getContentWidgetName($pageName = null) {

    $tableNamePages = Engine_Api::_()->getDbtable('pages', 'core');
    $page_id = $tableNamePages->select()
            ->from($tableNamePages->info('name'), array('page_id'))
            ->where('name =?', $pageName)
            ->query()
            ->fetchColumn();

    $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
    $name = $tableNameContent->select()
            ->from($tableNameContent->info('name'), array('name'))
            ->where('page_id =?', $page_id)
            ->where('name in (?)', array('sitemember.search-sitemember', 'sitemember.searchbox-sitemember'))
            ->query()
            ->fetchColumn();

    return $name;
  }

  /**
   * Select Friends SQL
   *
   * @param array $params
   * @return friends sql
   */
  public function getFriendsSelect($params = array()) {
    $tableUser = Engine_Api::_()->getDbtable('users', 'user');
    $tableUserName = $tableUser->info('name');
    $tableMemberShipUser = Engine_Api::_()->getDbtable('membership', 'user');
    $tableMemberShipUserName = $tableMemberShipUser->info('name');
    $select = $tableMemberShipUser->select();
    $select
            ->setIntegrityCheck(false)
            ->from($tableMemberShipUserName)
            ->join($tableUserName, "$tableMemberShipUserName.resource_id = $tableUserName.user_id", array('displayname', 'member_count'))
            ->where("$tableMemberShipUserName.user_id =?", $params['user_id'])
            ->where("$tableMemberShipUserName.active =?", 1);

    if (isset($params['search']) && !empty($params['search'])) {
      $select->where("$tableUserName.displayname LIKE ?", '%' . $params['search'] . '%');
    }

    return $select;
  }

}