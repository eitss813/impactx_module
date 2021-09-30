<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Api_Core extends Core_Api_Abstract {

    protected $_table;


    public function getItemTableClass($type) {

        // Generate item table class manually
        $module = 'Sitevideo';
        $class = $module . '_Model_DbTable_' . self::typeToClassSuffix($type, $module);
        if (substr($class, -1, 1) === 'y' && substr($class, -3) !== 'way') {
            $class = substr($class, 0, -1) . 'ies';
        } else if (substr($class, -1, 1) !== 's') {
            $class .= 's';
        }
        return $class;
    }

    public function removeMapLink($string) {

        if (!empty($string)) {
            $reqStartMapPaterrn = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . 'maps.google.com/?q=';
            $reqEndPatern = '>map</a>]';
            $positionMapStart = strpos($string, $reqStartMapPaterrn);
            if ($positionMapStart !== false) {
                $reqStartPatern = "<a";
                $positionStart = strpos($string, $reqStartPatern, ($positionMapStart - 10));
                $positionEnd = strpos($string, $reqEndPatern, $positionStart);
                if ($positionStart < $positionMapStart && $positionMapStart < $positionEnd)
                    $string = substr_replace($string, "", $positionStart - 1, ($positionEnd + 10) - $positionStart);
            }
        }

        return $string;
    }

    /**
     * Used to inflect item types to class suffix.
     * 
     * @param string $type
     * @param string $module
     * @return string
     */
    static public function typeToClassSuffix($type, $module) {

        $parts = explode('_', $type);
        if (count($parts) > 1 && ($parts[0] === strtolower($module) || $parts[0] === strtolower('sitevideo_channel'))) {
            array_shift($parts);
        }
        $partial = str_replace(' ', '', ucwords(join(' ', $parts)));
        return $partial;
    }

  public function enableStreamVideo()
  {
    if(!Engine_Api::_()->hasModuleBootstrap('sitevod')) {
      return false;
    }
    $result = Engine_Api::_()->sitevod()->hasReady();
    return $result['status'];
  }
  
  public function allowConvertToStreamVideo()
  {
    return $this->enableStreamVideo() && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.convert.stream', true);

    if( !$allow ) {
      return false;
    }
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $allowedSources_level = $permissionsTable->getAllowed('video', Engine_Api::_()->user()->getViewer()->level_id, 'source');

    return $allow && in_array(7, $allowedSources_level);
  }

  /**
     * Check lightbox is enable or not for videos show
     * @return bool
     */
    public function showLightBoxVideo() {

        $session = new Zend_Session_Namespace('mobile');
        if (isset($session->mobile) && $session->mobile)
            return false;

        return SEA_SITEVIDEO_LIGHTBOX;
    }

    public function getPrevVideo($current_video, $params = array()) {
        return $this->getVideos($current_video, $params, -1);
    }

    public function getNextVideo($current_video, $params = array()) {
        return $this->getVideos($current_video, $params, 1);
    }

    public function getVideos($collectible, $params = array(), $direction = null) {
        if (!isset($params['offset']) || empty($params['offset']))
            $index = $this->getCollectibleIndex($collectible, $params);
        else
            $index = $params['offset'];


        $index = $index + (int) $direction;

        $select = $this->getCollectibleSql($collectible, $params);

        // Check index bounds
        $count = $params['count'];
        if ($index >= $count) {
            $index -= $count;
        } else if ($index < 0) {
            $index += $count;
        }

        $select->limit(1, (int) $index);

        $rowset = $this->_table->fetchAll($select);
        if (null === $rowset) {
            // @todo throw?
            return null;
        }
        $row = $rowset->current();
        return Engine_Api::_()->getItem($collectible->getType(), $row->video_id);
    }

    /**
     * get the current video index
     */
    public function getCollectibleIndex($collectible, $params = array()) {
        $select = $this->getCollectibleSql($collectible, $params);

        $i = 0;
        $index = 0;
        if (isset($params['count']) && !empty($params['count'])) {
            $select->limit($params['count']);
        }
        $rows = $this->_table->fetchAll($select);
        $totalCount = $rows->count();
        foreach ($rows as $row) {
            if ($row->getIdentity() == $collectible->getIdentity()) {
                $index = $i;
                break;
            }
            $i++;
        }
        return $index;
    }

    /**
     * get the current video index sql
     */
    public function getCollectibleSql($collectible, $params = array()) {
        $collectibleType = $collectible->getType();
        $this->_table = $table = Engine_Api::_()->getItemTable($collectibleType);
        $tableName = $table->info('name');
        $col = current($table->info("primary"));
        $metaDataInfo = $table->info('metadata');
        if (isset($metaDataInfo['user_id'])) {
            $owner_id = 'user_id';
        } else {
            $owner_id = 'owner_id';
        }
        $select = $table->select()
                ->from($tableName, $col);
        $select
                ->where('search = ?', true)
                ->where('status = ?', 1);

        $type = $params['type'];
        // Page video
        if ($collectibleType == 'sitepagevideo_video') {
            $owner_id = 'page_id';
            if (!in_array($type, array('same-poster', 'same-tags', 'also-liked')) && strstr($params['subject_guid'], 'sitepage_page_')) {
                $select
                        ->where($tableName . '.page_id = ?', $collectible->page_id);
            }
            // Page profile Page
            if ($type == 'page-profile') {
                if (!empty($params['search_text'])) {
                    $select->where($tableName . ".title LIKE ? OR " . $tableName . ".description LIKE ?", '%' . $params['search_text'] . '%');
                }

                if (!empty($params['my_video'])) {
                    $select->where("$tableName .owner_id = ?", $collectible->owner_id);
                }
                if (empty($params['browse'])) {
                    $select->order("$tableName.highlighted DESC");
                } elseif ($params['browse'] == 'highlighted') {
                    $select->where($tableName . '.highlighted = ?', 1);
                } elseif ($params['browse'] == 'featured') {
                    $select->where($tableName . '.featured = ?', 1);
                } else {
                    $browse = $params['browse'];
                    if ($browse != 'creation_date')
                        $select->order("$tableName.$browse DESC");
                }
                $select->order("$tableName.video_id DESC");
                return $select;
            }
        } /* Business Video */ elseif ($collectibleType == 'sitebusinessvideo_video') {
            $owner_id = 'business_id';
            if (!in_array($type, array('same-poster', 'same-tags', 'also-liked')) && strstr($params['subject_guid'], 'sitebusiness_business_')) {
                $select
                        ->where($tableName . '.business_id = ?', $collectible->business_id);
            }
            // Business profile Page
            if ($type == 'business-profile') {
                if (!empty($params['search_text'])) {
                    $select->where($tableName . ".title LIKE ? OR " . $tableName . ".description LIKE ?", '%' . $params['search_text'] . '%');
                }

                if (!empty($params['my_video'])) {
                    $select->where("$tableName .owner_id = ?", $collectible->owner_id);
                }
                if (empty($params['browse'])) {
                    $select->order("$tableName.highlighted DESC");
                } elseif ($params['browse'] == 'highlighted') {
                    $select->where($tableName . '.highlighted = ?', 1);
                } elseif ($params['browse'] == 'featured') {
                    $select->where($tableName . '.featured = ?', 1);
                } else {
                    $browse = $params['browse'];
                    if ($browse != 'creation_date')
                        $select->order("$tableName.$browse DESC");
                }
                $select->order("$tableName.video_id DESC");
                return $select;
            }
        }/* Directory Group Video */ else if ($collectibleType == 'sitegroupvideo_video') {
            $owner_id = 'group_id';
            if (!in_array($type, array('same-poster', 'same-tags', 'also-liked')) && strstr($params['subject_guid'], 'sitegroup_group_')) {
                $select
                        ->where($tableName . '.group_id = ?', $collectible->group_id);
            }
            // Page profile Page
            if ($type == 'group-profile') {
                if (!empty($params['search_text'])) {
                    $select->where($tableName . ".title LIKE ? OR " . $tableName . ".description LIKE ?", '%' . $params['search_text'] . '%');
                }

                if (!empty($params['my_video'])) {
                    $select->where("$tableName .owner_id = ?", $collectible->owner_id);
                }
                if (empty($params['browse'])) {
                    $select->order("$tableName.highlighted DESC");
                } elseif ($params['browse'] == 'highlighted') {
                    $select->where($tableName . '.highlighted = ?', 1);
                } elseif ($params['browse'] == 'featured') {
                    $select->where($tableName . '.featured = ?', 1);
                } else {
                    $browse = $params['browse'];
                    if ($browse != 'creation_date')
                        $select->order("$tableName.$browse DESC");
                }
                $select->order("$tableName.video_id DESC");
                return $select;
            }
        }/* Directory Store Video */ else if ($collectibleType == 'sitestorevideo_video') {
            $owner_id = 'store_id';
            if (!in_array($type, array('same-poster', 'same-tags', 'also-liked')) && strstr($params['subject_guid'], 'sitestore_store_')) {
                $select
                        ->where($tableName . '.store_id = ?', $collectible->store_id);
            }
            // Page profile Page
            if ($type == 'store-profile') {
                if (!empty($params['search_text'])) {
                    $select->where($tableName . ".title LIKE ? OR " . $tableName . ".description LIKE ?", '%' . $params['search_text'] . '%');
                }

                if (!empty($params['my_video'])) {
                    $select->where("$tableName .owner_id = ?", $collectible->owner_id);
                }
                if (empty($params['browse'])) {
                    $select->order("$tableName.highlighted DESC");
                } elseif ($params['browse'] == 'highlighted') {
                    $select->where($tableName . '.highlighted = ?', 1);
                } elseif ($params['browse'] == 'featured') {
                    $select->where($tableName . '.featured = ?', 1);
                } else {
                    $browse = $params['browse'];
                    if ($browse != 'creation_date')
                        $select->order("$tableName.$browse DESC");
                }
                $select->order("$tableName.video_id DESC");
                return $select;
            }
        } /* Ynvideo Video Playlist */ elseif (strstr($params['subject_guid'], 'ynvideo_playlist_')) {
            $subject_guid = $params['subject_guid'];
            if (!empty($subject_guid))
                $subject = Engine_Api::_()->getItemByGuid($subject_guid);

            $playlistAssocTbl = Engine_Api::_()->getDbTable('playlistassoc', 'ynvideo');
            $playlistAssocTblName = $playlistAssocTbl->info('name');
            $select->setIntegrityCheck(false);
            $select->join($playlistAssocTblName, "$playlistAssocTblName.video_id = $tableName.video_id")
                    ->where("$playlistAssocTblName.playlist_id = ?", $subject->getIdentity());
            $select->order("$tableName.creation_date DESC");
            return $select;
        }

        switch ($type) {
            case 'most-viewed':
                $select->order($tableName . '.view_count DESC');
                break;
            case 'recent':
                $select->order("$tableName.video_id DESC");
                break;
            case 'modified':
                $select->order("$tableName.modified_date DESC");
                break;
            case 'my-favorite':
            case 'profile-favorite':
                $favoriteTable = Engine_Api::_()->getDbTable('favorites', 'ynvideo');
                $favoriteTableName = $favoriteTable->info('name');

                $select->setIntegrityCheck(false)
                        ->join($favoriteTableName, $favoriteTableName . ".video_id = " . $tableName . ".video_id");

                if ($type == 'my-favorite') {
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $select->where("$favoriteTableName.user_id = ?", $viewer->getIdentity());
                } else if ($type == 'profile-favorite') {
                    $subject_guid = $params['subject_guid'];
                    if (!empty($subject_guid))
                        $subject = Engine_Api::_()->getItemByGuid($subject_guid);

                    $select->where("$favoriteTableName.user_id = ?", $subject->getIdentity());
                }
                $select->order("$tableName.creation_date DESC");
                break;
            case 'profile-listing':
                $subject_guid = $params['subject_guid'];
                if (!empty($subject_guid))
                    $subject = Engine_Api::_()->getItemByGuid($subject_guid);

                $listvideoTable = Engine_Api::_()->getDbTable('clasfvideos', 'list');
                $listvideoTableName = $listvideoTable->info('name');
                $select = $select
                        ->setIntegrityCheck(false)
                        ->join($listvideoTableName, $tableName . '.video_id = ' . $listvideoTableName . '.video_id', array())
                        ->group($tableName . '.video_id')
                        ->where($listvideoTableName . '.listing_id  = ?', $subject->getIdentity());

                break;
            case 'profile-recipe':
                $subject_guid = $params['subject_guid'];
                if (!empty($subject_guid))
                    $subject = Engine_Api::_()->getItemByGuid($subject_guid);
                $listvideoTable = Engine_Api::_()->getDbTable('clasfvideos', 'recipe');
                $listvideoTableName = $listvideoTable->info('name');
                $select = $select
                        ->setIntegrityCheck(false)
                        ->join($listvideoTableName, $tableName . '.video_id = ' . $listvideoTableName . '.video_id', array())
                        ->group($tableName . '.video_id')
                        ->where($listvideoTableName . '.recipe_id  = ?', $subject->getIdentity());

                break;
            case 'profile-sitereview':
                $subject_guid = $params['subject_guid'];
                if (!empty($subject_guid))
                    $subject = Engine_Api::_()->getItemByGuid($subject_guid);
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereview.show.video', 1)) {
                    $listvideoTable = Engine_Api::_()->getDbTable('clasfvideos', 'sitereview');
                    $listvideoTableName = $listvideoTable->info('name');
                    $select = $select
                            ->setIntegrityCheck(false)
                            ->join($listvideoTableName, $tableName . '.video_id = ' . $listvideoTableName . '.video_id', array())
                            ->group($tableName . '.video_id')
                            ->where($listvideoTableName . '.listing_id  = ?', $subject->getIdentity());
                } else {
                    $select->where($tableName . '.listing_id  = ?', $subject->getIdentity());
                }
                break;
            case 'profile-sitestoreproduct':
                $subject_guid = $params['subject_guid'];
                if (!empty($subject_guid))
                    $subject = Engine_Api::_()->getItemByGuid($subject_guid);
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.video', 1)) {
                    $listvideoTable = Engine_Api::_()->getDbTable('clasfvideos', 'sitestoreproduct');
                    $listvideoTableName = $listvideoTable->info('name');
                    $select = $select
                            ->setIntegrityCheck(false)
                            ->join($listvideoTableName, $tableName . '.video_id = ' . $listvideoTableName . '.video_id', array())
                            ->group($tableName . '.video_id')
                            ->where($listvideoTableName . '.product_id  = ?', $subject->getIdentity());
                } else {
                    $select->where($tableName . '.product_id  = ?', $subject->getIdentity());
                }
                break;
            case 'my-watch-later':
                $viewer = Engine_Api::_()->user()->getViewer();
                $watchLaterTbl = Engine_Api::_()->getDbTable('watchlaters', 'ynvideo');
                $watchLaterTblName = $watchLaterTbl->info('name');
                $select->setIntegrityCheck(false)
                        ->join($watchLaterTblName, $watchLaterTblName . ".video_id = " . $tableName . ".video_id", "$watchLaterTblName.watched")
                        ->order(array("$watchLaterTblName.watched ASC", "$watchLaterTblName.creation_date DESC"))
                        ->where("$watchLaterTblName.user_id = ?", $viewer->getIdentity());
                break;
            case 'most-liked':
                if (!empty($params['duration']) && $params['duration'] != 'overall') {
                    if ($params['duration'] == 1) {
                        $start_date = date('Y-m-d');
                    } else {
                        $params['duration'] = $params['duration'] - 1;
                        //DATE IS CONVERT IN TO SECONDS
                        $duration_seconds = $params['duration'] * 24 * 60 * 60;
                        $seconds_enddate = time();
                        $seconds_startdate = $seconds_enddate - $duration_seconds;
                        //START DATE CAN GET FROM THE DATE FUNCTION AND MINUS RESULT
                        $start_date = date('Y-m-d', $seconds_startdate);
                    }
                    $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
                    $likeTableName = $likeTable->info('name');
                    $select->setIntegrityCheck(false)
                            ->join($likeTableName, "($likeTableName .resource_id =  $tableName.video_id AND  $likeTableName.resource_type = '$collectibleType' AND $likeTableName.creation_date >= '$start_date')", array());
                }

                $select->order($tableName . '.like_count DESC');
                break;
            case 'most-commented':
                $select->order($tableName . '.comment_count DESC');
                break;
            case 'top-rated':
                $select->order($tableName . '.rating DESC');
                break;
            case 'most-favorite':
                $select->order($tableName . '.favorite_count DESC');
                break;
            case 'featured':
                $select->where($tableName . '.featured = ?', 1);
                break;
            case 'highlight':
                $select->where($tableName . '.highlighted = ?', 1);
                break;
            case 'same-categories':
                $select->where($tableName . '.category_id = ?', $collectible->category_id);
                break;
            case 'also-liked':
                $subject_guid = $params['subject_guid'];
                if (!empty($subject_guid))
                    $subject = Engine_Api::_()->getItemByGuid($subject_guid);
                $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
                $likesTableName = $likesTable->info('name');
                $select->distinct(true)
                        ->joinLeft($likesTableName, $likesTableName . '.resource_id=video_id', null)
                        ->joinLeft($likesTableName . ' as l2', $likesTableName . '.poster_id=l2.poster_id', null)
                        ->where($likesTableName . '.poster_type = ?', 'user')
                        ->where('l2.poster_type = ?', 'user')
                        ->where($likesTableName . '.resource_type = ?', $subject->getType())
                        ->where('l2.resource_type = ?', $subject->getType())
                        ->where($likesTableName . '.resource_id != ?', $subject->getIdentity())
                        ->where('l2.resource_id = ?', $subject->getIdentity())
                        ->where('search = ?', true)
                        ->where('video_id != ?', $subject->getIdentity());
                break;
            case 'same-tags':
                $tagMapsTable = Engine_Api::_()->getDbtable('tagMaps', 'core');
                $tagsTable = Engine_Api::_()->getDbtable('tags', 'core');
                $subject_guid = $params['subject_guid'];
                if (!empty($subject_guid))
                    $subject = Engine_Api::_()->getItemByGuid($subject_guid);
                // Get tags
                $tags = $tagMapsTable->select()
                        ->from($tagMapsTable, 'tag_id')
                        ->where('resource_type = ?', $subject->getType())
                        ->where('resource_id = ?', $subject->getIdentity())
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);

                // Get other with same tags
                if (!empty($tags)) {
                    $select
                            ->distinct(true)
                            ->joinLeft($tagMapsTable->info('name'), 'resource_id = video_id', null)
                            ->where('resource_type = ?', $subject->getType())
                            ->where('resource_id != ?', $subject->getIdentity())
                            ->where('tag_id IN(?)', $tags);
                }
                break;
            case 'same-poster':
                $subject_guid = $params['subject_guid'];
                if (!empty($subject_guid))
                    $subject = Engine_Api::_()->getItemByGuid($subject_guid);
                $select->where($tableName . '.video_id != ?', $subject->getIdentity())
                        ->where("$tableName .$owner_id = ?", $subject->$owner_id);
                break;
            case 'subject_recent':
                $subject_guid = $params['subject_guid'];
                if (!empty($subject_guid))
                    $subject = Engine_Api::_()->getItemByGuid($subject_guid);

                $select->where('parent_type = ?', $subject->getType())
                        ->where('parent_id = ?', $subject->getIdentity());
                $select->order("$tableName.creation_date DESC");
                break;

            default :
                $select
                        ->where("$tableName .$owner_id = ?", $collectible->$owner_id);
                $select->order($tableName . '.video_id DESC');
                break;
        }

        if (in_array($collectibleType, array('sitepagevideo_video', 'sitebusinessvideo_video'))) {
            $select->order($tableName . '.video_id DESC');
        }
        return $select;
    }

    public function isVideoTypeValid($collectible = null, $params = array(), $direction = null) {
        if (!empty($collectible) && (!isset($params['offset']) || empty($params['offset'])))
            $index = $this->getCollectibleIndex($collectible, $params);
        else
            $index = $params['offset'];

        $sitadvsearchExtType = @base64_decode("c2l0ZXZpZGVv");
        $addDefaultFirstFlag = (int) @base64_decode("NjUz");
        $addDefaultSecondFlag = (int) @base64_decode("NTYzOTg3NjIw");
        $siteadvsearchExtInfoType = @base64_decode("MSwzLDQsOCwxMCwxMSwxNCwxNiwxOA==");
        if (!empty($collectible)) {
            $index = $index + (int) $direction;

            $select = $this->getCollectibleSql($collectible, $params);

            // Check index bounds
            $count = $params['count'];
            if ($index >= $count) {
                $index -= $count;
            } else if ($index < 0) {
                $index += $count;
            }

            $select->limit(1, (int) $index);

            $rowset = $this->_table->fetchAll($select);
            if (null === $rowset) {
                // @todo throw?
                return null;
            }
            $row = $rowset->current();
            return Engine_Api::_()->getItem($collectible->getType(), $row->video_id);
        } else {
            $getMemberLSettings = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.lsettings', false);
            $mobiAttempt = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
            $getMobTypeInfo = $mobiAttempt . $sitadvsearchExtType;

            if (!empty($params)) {
                // Not logged in
                $viewer = Engine_Api::_()->user()->getViewer();
                if (!$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false)) {
                    return false;
                }
                // Get setting?
                $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
                if (Authorization_Api_Core::LEVEL_DISALLOW === $permission) {
                    return false;
                }
                $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
                if ($messageAuth == 'none') {
                    return false;
                } else if ($messageAuth == 'friends') {
                    // Get data
                    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
                    if (!$direction) {
                        //one way
                        $friendship_status = $viewer->membership()->getRow($subject);
                    } else
                        $friendship_status = $subject->membership()->getRow($viewer);

                    if (!$friendship_status || $friendship_status->active == 0) {
                        return false;
                    }
                }
                return true;
            } else {
                $siteadvsearchExtInfoTypeArray = @explode(",", $siteadvsearchExtInfoType);
                $extViewStr = null;
                foreach ($siteadvsearchExtInfoTypeArray as $extInfoType) {
                    $extViewStr .= $getMemberLSettings[$extInfoType];
                }

                for ($mobExtTypeFlag = 0; $mobExtTypeFlag < strlen($getMobTypeInfo); $mobExtTypeFlag++) {
                    $getExtTotalInfoFlag += @ord($getMobTypeInfo[$mobExtTypeFlag]);
                }
                $getExtTotalInfoFlag = (int) $getExtTotalInfoFlag;
                $getExtTotalInfoFlag = ($getExtTotalInfoFlag * ($addDefaultFirstFlag)) + $addDefaultSecondFlag;
                $getExtTotalInfoFlag = (string) $getExtTotalInfoFlag;
                if (!empty($extViewStr) && !empty($getExtTotalInfoFlag) && ($extViewStr == $getExtTotalInfoFlag)) {
                    return true;
                } else {
                    if ($mobiAttempt == 'localhost' || strpos($mobiAttempt, '192.168.') != false || strpos($mobiAttempt, '127.0.') != false) {
                        return true;
                    } else {
                        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitevideo.viewtypeinfo.settings', 0);
                        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitevideo.viewtypeinfo.type', 0);
                        return false;
                    }
                }
            }

            if (!empty($direction)) {
                $viewer = Engine_Api::_()->user()->getViewer();
                $table = Engine_Api::_()->getDBTable($param['tableName'], 'sitevideo');
                $tableName = $table->info('name');
                $column_name = $param['columnName'];
                //MAKE QUERY
                $select = $table->select()
                        ->from($tableName, array('COUNT(*) AS count'));
                if (!empty($param['columnName'])) {
                    $select->where("$column_name = ?", $viewer->getIdentity());
                }
                if (!empty($param['resourceType'])) {
                    $select->where("resource_type = ?", $param['resourceType']);
                }
                $totalCount = $select->query()->fetchColumn();
                return $totalCount;
            }
        }
    }

    public function getCountTotal($collectible, $params = array()) {
        $select = $this->getCollectibleSql($collectible, $params);
        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }

        $rows = $this->_table->fetchAll($select);
        $totalCount = $rows->count();
        return $totalCount;
    }

    public function canSendUserMessage($subject) {

        // Not logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false)) {
            return false;
        }
        // Get setting?
        $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
        if (Authorization_Api_Core::LEVEL_DISALLOW === $permission) {
            return false;
        }
        $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
        if ($messageAuth == 'none') {
            return false;
        } else if ($messageAuth == 'friends') {
            // Get data
            $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
            if (!$direction) {
                //one way
                $friendship_status = $viewer->membership()->getRow($subject);
            } else
                $friendship_status = $subject->membership()->getRow($viewer);

            if (!$friendship_status || $friendship_status->active == 0) {
                return false;
            }
        }
        return true;
    }

    public function isLessThan417ChannelModule() {
        $channelModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitevideo');
        $channelModuleVersion = $channelModule->version;
        if ($channelModuleVersion < '4.1.7') {
            return true;
        } else {
            return false;
        }
    }

    public function deleteSuggestion($entity, $entity_id, $notifications_type) {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
        if (!empty($is_moduleEnabled)) {
            $suggestion_table = Engine_Api::_()->getItemTable('suggestion');
            $suggestion_table_name = $suggestion_table->info('name');
            $suggestion_select = $suggestion_table->select()
                    ->from($suggestion_table_name, array('suggestion_id'))
                    ->where('owner_id = ?', $viewer_id)
                    ->where('entity = ?', $entity)
                    ->where('entity_id = ?', $entity_id);
            $suggestion_array = $suggestion_select->query()->fetchAll();
            if (!empty($suggestion_array)) {
                foreach ($suggestion_array as $sugg_id) {
                    Engine_Api::_()->getItem('suggestion', $sugg_id['suggestion_id'])->delete();
                    Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_id = ?' => $sugg_id['suggestion_id'], 'type = ?' => $notifications_type));
                }
            }
        }
        return;
    }

    public function isModulesSupport() {
        $modArray = array(
            'advancedactivity' => '4.8.10p7',
            'communityad' => '4.8.10p1',
            'facebookse' => '4.8.10p2',
            'facebooksefeed' => '4.8.10p1',
            'nestedcomment' => '4.8.10p3',
            'siteadvsearch' => '4.8.10p2',
            'sitealbum' => '4.8.10p8',
            'sitecontentcoverphoto' => '4.8.10p4',
            'sitehomepagevideo' => '4.8.10p1',
            'sitelike' => '4.8.10p2',
            'sitemenu' => '4.8.10p1',
            'sitemobile' => '4.8.10p3',
            'siteusercoverphoto' => '4.8.10p4',
            'suggestion' => '4.8.10p2',
            'captivate' => '4.8.10'
        );
        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
            if (!empty($isModEnabled)) {
                $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
                $isModSupport = $this->checkVersion($getModVersion->version, $value);
                if (!$isModSupport) {
                    $finalModules[] = $getModVersion->title;
                }
            }
        }
        return $finalModules;
    }

    /**
     * Set Orders of Videos
     * @return bool
     */
    public function setVideosOrder($channel_id) {

        $videoTable = Engine_Api::_()->getItemTable('sitevideo_video');
        $channel_id_Col = "main_channel_id";
        if ($this->isLessThan417ChannelModule()) {
            $channel_id_Col = "collection_id";
        }
        $conutOrder = $videoTable->select()
                ->from($videoTable, 'video_id')
                ->where("$channel_id_Col = ?", $channel_id)
                ->where('`order` = ?', 0)
                ->order('order ASC')
                ->limit(2)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
        $count = count($conutOrder);
        if ($count <= 1)
            return;

        $currentOrder = $videoTable->select()
                ->from($videoTable, 'video_id')
                ->where("$channel_id_Col = ?", $channel_id)
                ->order('order ASC')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
        for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
            $video_id = $currentOrder[$i];
            $videoTable->update(array(
                'order' => $i,
                    ), array(
                'video_id = ?' => $video_id,
            ));
        }
    }

    /**
     * Set Meta Titles
     *
     * @param array $params
     */
    public function setMetaTitles($params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $titles = $siteinfo['title'];

        if (isset($params['subcategoryname']) && !empty($params['subcategoryname'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['subcategoryname'];
        }

        if (isset($params['categoryname']) && !empty($params['categoryname'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['categoryname'];
        }

        if (isset($params['default_title'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['default_title'];
        }
        if (isset($params['dashboard'])) {
            if (isset($params['channel_type_title'])) {
                if (!empty($titles))
                    $titles .= ' - ';
                $titles .= $params['channel_type_title'];
            }

            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['dashboard'];
        }
        $siteinfo['title'] = $titles;
        $view->layout()->siteinfo = $siteinfo;
    }

    /**
     * Set Meta Titles
     *
     * @param array $params
     */
    public function setMetaDescriptionsBrowse($params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $descriptions = '';
        if (isset($params['description'])) {
            if (!empty($descriptions))
                $descriptions .= ' - ';
            $descriptions .= $params['description'];
        }

        $siteinfo['description'] = $descriptions;
        $view->layout()->siteinfo = $siteinfo;
    }

    /**
     * Set Meta Keywords
     *
     * @param array $params
     */
    public function setMetaKeywords($params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $keywords = "";

        if (isset($params['subcategoryname_keywords']) && !empty($params['subcategoryname_keywords'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['subcategoryname_keywords'];
        }

        if (isset($params['categoryname_keywords']) && !empty($params['categoryname_keywords'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['categoryname_keywords'];
        }

        if (isset($params['location']) && !empty($params['location'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['location'];
        }

        if (isset($params['tag']) && !empty($params['tag'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['tag'];
        }

        if (isset($params['search'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['search'];
        }

        if (isset($params['keywords'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['keywords'];
        }

        if (isset($params['channel_type_title'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['channel_type_title'];
        }

        $siteinfo['keywords'] = $keywords;
        $view->layout()->siteinfo = $siteinfo;
    }

    /**
     * Get sitevideo tags created by users
     * @param int $owner_id : sitevideo owner id
     * @param int $total_tags : number tags to show
     */
    public function getVideoTags($owner_id = 0, $total_tags = 100, $count_only = 0, $params = array()) {
        //GET DOCUMENT TABLE
        $tableSitevideo = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $tableSitevideoName = $tableSitevideo->info('name');
        //MAKE QUERY
        $select = $tableSitevideo->select()
                ->setIntegrityCheck(false)
                ->from($tableSitevideoName, array("video_id"))
                ->where($tableSitevideoName . ".search = ?", 1);
        if ($params['videotype'] == 0) {
            $select->order("view_count DESC");
        }
        if (!empty($owner_id)) {
            $select->where($tableSitevideoName . '.owner_id = ?', $owner_id);
        }

        $select->distinct(true);

        $videoIds = $select
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        if (empty($videoIds)) {
            return;
        }

        $tableTagMaps = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $tableTagMapsName = $tableTagMaps->info('name');
        //GET TAG TABLE NAME
        $tableTags = 'engine4_core_tags';
        $videoTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $videoTableName = $videoTable->info('name');

        $select = $tableTagMaps->select()
                ->setIntegrityCheck(false)
                ->from($tableTagMapsName, array("COUNT($tableTagMapsName.resource_id) AS Frequency", 'resource_id'))
                ->joinInner($tableTags, "$tableTags.tag_id = $tableTagMapsName.tag_id", array('text', 'tag_id'));
        if ($params['videotype'] == 0) {
            $select->joinInner($videoTableName, "$videoTableName.video_id = $tableTagMapsName.resource_id", array('view_count'));
        }
        $select->where($tableTagMapsName . '.resource_type = ?', 'video');
        $select->where($tableTagMapsName . '.resource_id IN(?)', (array) $videoIds);
        if ($params['videotype'] == 0) {
            $select->order("view_count DESC");
        }
        $select->group("$tableTags.text");


        /*
          //MAKE QUERY
          $select = $tableTagMaps->select()
          ->setIntegrityCheck(false)
          ->from($tableTagMapsName, array("COUNT($tableTagMapsName.resource_id) AS Frequency"))
          ->joinInner($tableTags, "$tableTags.tag_id = $tableTagMapsName.tag_id", array('text', 'tag_id'))
          ->where($tableTagMapsName . '.resource_type = ?', 'video')
          ->where($tableTagMapsName . '.resource_id IN(?)', (array) $videoIds)
          ->group("$tableTags.text");
         */
        if ($params['videotype'] == 0) {

            //  $select->order('DESC');
        }

        if (isset($params['orderingType']) && !empty($params['orderingType']))
            $select->order("$tableTags.text");
        else
            $select->order("Frequency DESC");

        if (!empty($total_tags)) {
            $select = $select->limit($total_tags);
        }
        $select = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getNetworkBaseSql($select);
        if (!empty($count_only)) {
            $total_results = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            return Count($total_results);
        }
        //RETURN RESULTS
        return $select->query()->fetchAll();
    }

    /**
     * Get sitevideo tags created by users
     * @param int $owner_id : sitevideo owner id
     * @param int $total_tags : number tags to show
     */
    public function getTags($owner_id = 0, $total_tags = 100, $count_only = 0, $params = array()) {

        //GET DOCUMENT TABLE
        $tableSitevideo = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $tableSitevideoName = $tableSitevideo->info('name');

        //MAKE QUERY
        $select = $tableSitevideo->select()
                ->setIntegrityCheck(false)
                ->from($tableSitevideoName, array("channel_id", 'view_count'))
                ->where($tableSitevideoName . ".search = ?", 1);

        if (!empty($owner_id)) {
            $select->where($tableSitevideoName . '.owner_id = ?', $owner_id);
        }

        $select->distinct(true);

        $channelIds = $select
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        if (empty($channelIds)) {
            return;
        }

        $tableTagMaps = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $tableTagMapsName = $tableTagMaps->info('name');

        $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $channelTableName = $channelTable->info('name');


        //GET TAG TABLE NAME
        $tableTags = 'engine4_core_tags';

        //MAKE QUERY
        $select = $tableTagMaps->select()
                ->setIntegrityCheck(false)
                ->from($tableTagMapsName, array("COUNT($tableTagMapsName.resource_id) AS Frequency", 'resource_id'))
                ->joinInner($tableTags, "$tableTags.tag_id = $tableTagMapsName.tag_id", array('text', 'tag_id'));
        if ($params['channeltype'] == 0) {
            $select->joinInner($channelTableName, "$channelTableName.channel_id = $tableTagMapsName.resource_id", array('view_count'));
        }
        $select->where($tableTagMapsName . '.resource_type = ?', 'sitevideo_channel');
        $select->where($tableTagMapsName . '.resource_id IN(?)', (array) $channelIds);
        if ($params['channeltype'] == 0) {
            $select->order("view_count DESC");
        }
        $select->group("$tableTags.text");

        if (isset($params['orderingType']) && !empty($params['orderingType']))
            $select->order("$tableTags.text");
        else
            $select->order("Frequency DESC");

        if (!empty($total_tags)) {
            $select = $select->limit($total_tags);
        }
        $select = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getNetworkBaseSql($select);

        if (!empty($count_only)) {
            $total_results = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            return Count($total_results);
        }

        //RETURN RESULTS
        return $select->query()->fetchAll();
    }

    public function getFieldsStructureSearch($spec, $parent_field_id = null, $parent_option_id = null, $showGlobal = true, $profileTypeIds = array()) {

        $fieldsApi = Engine_Api::_()->getApi('core', 'fields');



        $type = $fieldsApi->getFieldType($spec);

        $structure = array();
        foreach ($fieldsApi->getFieldsMaps($type)->getRowsMatching('field_id', (int) $parent_field_id) as $map) {   
            // Skip maps that don't match parent_option_id (if provided)
            if (null !== $parent_option_id && $map->option_id != $parent_option_id) {
                continue;
            }

            //FETCHING THE FIELDS WHICH BELONGS TO SOME SPECIFIC LISTNIG TYPE
            if ($parent_field_id == 1 && !empty($profileTypeIds) && !in_array($map->option_id, $profileTypeIds)) {
                continue;
            }

            // Get child field
            $field = $fieldsApi->getFieldsMeta($type)->getRowMatching('field_id', $map->child_id);
            if (empty($field)) {
                continue;
            }

            // Add to structure
            if ($field->search) {
                $structure[$map->getKey()] = $map;
            }

            // Get children
            if ($field->canHaveDependents()) {
                $structure += $this->getFieldsStructureSearch($spec, $map->child_id, null, $showGlobal, $profileTypeIds);
            }
        }

        return $structure;
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
                ->where($coreContentTableName . '.name = ?', 'sitevideo.browse-channels-sitevideo')
                ->query()
                ->fetchColumn();

        $paramsArray = !empty($params) ? Zend_Json::decode($params) : array();

        if (isset($paramsArray['orderby']) && !empty($paramsArray['orderby'])) {
            return $paramsArray['orderby'];
        } else {
            return 0;
        }
    }

    public function showSelectedVideoBrowseBy($content_id) {

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
                ->where($coreContentTableName . '.name = ?', 'sitevideo.browse-videos-sitevideo')
                ->query()
                ->fetchColumn();

        $paramsArray = !empty($params) ? Zend_Json::decode($params) : array();

        if (isset($paramsArray['orderby']) && !empty($paramsArray['orderby'])) {
            return $paramsArray['orderby'];
        } else {
            return 0;
        }
    }

    /**
     * Channel base network enable
     *
     * @return bool
     */
    public function channelBaseNetworkEnable() {

        return (bool) ( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.networks.type', 0) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.network', 0) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.default.show', 0)));
    }

    /**
     * Video base network enable
     *
     * @return bool
     */
    public function videoBaseNetworkEnable() {

        return (bool) ( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.networks.type', 0) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.network', 0) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.default.show', 0)));
    }

    // CONVERT THE DECODE STRING INTO ENCODE
    public function getDecodeToEncode($string = null) {
        set_time_limit(0);
        $encodeString = '';

        if (!empty($string)) {
            $startIndex = 11;
            $CodeArray = array("x4b1e4ty6u", "bl42iz50sq", "pr9v41c19a", "ddr5b8fi7s", "lc44rdya6c", "o5or323c54", "xazefrda4p", "54er65ee9t", "8ig5f2a6da", "kkgh5j9x8c", "ttd3s2a16b", "5r3ec7w46z", "0d1a4f7af3", "sx4b8jxxde", "hf5blof8ic", "4a6ez5t81f", "3yf5fc3o12", "sd56hgde4f", "d5ghi82el9");

            $time = time();
            $timeLn = Engine_String::strlen($time);
            $last2DigtTime = substr($time, $timeLn - 2, 2);
            $sI1 = (int) ($last2DigtTime / 10);
            $sI2 = $last2DigtTime % 10;
            $Index = $sI1 + $sI2;

            $codeString = $CodeArray[$Index];
            $startIndex+=$Index % 10;
            $lenght = Engine_String::strlen($string);
            for ($i = 0; $i < $lenght; $i++) {
                $code = uniqid(rand(), true);
                $encodeString.= substr($code, 0, $startIndex);
                $encodeString.=$string{$i};
                $startIndex++;
            }
            $code = uniqid(rand(), true);
            $appendEnd = substr($code, 5, $startIndex);
            $prepandStart = substr($code, 20, 10);
            $encodeString = $prepandStart . $codeString . $encodeString . $appendEnd;
        }

        return $encodeString;
    }

    // CONVERT THE ENCODE STRING INTO DECODE
    public function getEncodeToDecode($string) {
        $decodeString = '';

        if (!empty($string)) {
            $startIndex = 11;
            $CodeArray = array("x4b1e4ty6u", "bl42iz50sq", "pr9v41c19a", "ddr5b8fi7s", "lc44rdya6c", "o5or323c54", "xazefrda4p", "54er65ee9t", "8ig5f2a6da", "kkgh5j9x8c", "ttd3s2a16b", "5r3ec7w46z", "0d1a4f7af3", "sx4b8jxxde", "hf5blof8ic", "4a6ez5t81f", "3yf5fc3o12", "sd56hgde4f", "d5ghi82el9");
            $string = substr($string, 10, (Engine_String::strlen($string) - 10));
            $codeString = substr($string, 0, 10);

            $Index = array_search($codeString, $CodeArray);
            $string = substr($string, 10, Engine_String::strlen($string) - 10);
            $startIndex+=$Index % 10;

            $string = substr($string, 0, (Engine_String::strlen($string) - $startIndex));

            $lenght = Engine_String::strlen($string);
            $j = 1;
            for ($i = $startIndex; $i < $lenght;
            ) {
                $j++;
                $decodeString.= $string{$i};
                $i = $i + $startIndex + $j;
            }
        }
        return $decodeString;
    }

    // handle video upload
    public function createVideo($params, $file, $values) {
        if ($file instanceof Storage_Model_File) {
            $params['file_id'] = $file->getIdentity();
        } else {
            if (Engine_Api::_()->sitevideo()->allowConvertToStreamVideo()) {
              return  $this->createStreamVideo($params, $file);
            }
            // create video item
            $video = Engine_Api::_()->getDbtable('videos', 'sitevideo')->createRow();
            $file_ext = pathinfo($file);
            $file_ext = $file_ext['extension'];
            $video->code = $file_ext;
            $video->synchronized = 1;
            $video->save();

            // Channel video in temporary storage object for ffmpeg to handle
            $storage = Engine_Api::_()->getItemTable('storage_file');
            $storageObject = $storage->createFile($file, array(
                'parent_id' => $video->getIdentity(),
                'parent_type' => $video->getType(),
                'user_id' => $video->owner_id,
            ));

            // Remove temporary file
            @unlink($file);

            $video->file_id = $storageObject->file_id;
            $video->save();
            // Add to jobs
            if (strtolower($file_ext)=='mp4' || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.html5', true)) {
                Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitevideo_encode', array(
                    'video_id' => $video->getIdentity(),
                    'type' => 'mp4',
                ));
            } else {
                Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitevideo_encode', array(
                    'video_id' => $video->getIdentity(),
                    'type' => 'flv',
                ));
            }
        }

        return $video;
    }
    
      // handle video upload
  public function createStreamVideo($params, $file)
  {
    // create video item
    $video = Engine_Api::_()->getDbtable('videos', 'sitevideo')->createRow();
    $file_ext = pathinfo($file);
    $video->code = $file_ext['extension'];
    $video->owner_id = $params['owner_id'];
    $video->synchronized = 1;
    $video->type = 'stream';
    $video->status = 0;
    $video->save();
    $storageObject = Engine_Api::_()->sitevod()->createStream(array(
      'parent_id' => $video->getIdentity(),
      'parent_type' => $video->getType(),
      'user_id' => $video->owner_id,
      ), $file);
    $video->file_id = $storageObject->file_id;
    $video->save();
    return $video;
  }

  // delete video
    public function deleteVideo($video) {

        $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $channelTableName = $channelTable->info('name');

        $videoMapTable = Engine_Api::_()->getDbtable('videomaps', 'sitevideo');
        $videoMapTableName = $videoMapTable->info('name');

        $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sitevideo');
        $playlistTableName = $playlistTable->info("name");

        $playlistmapTable = Engine_Api::_()->getDbtable('playlistmaps', 'sitevideo');
        $playlistmapTableName = $playlistmapTable->info("name");

        $db = Engine_Db_Table::getDefaultAdapter();

        // check to make sure the video did not fail, if it did we wont have files to remove
        if ($video->status == 1) {
            // delete storage files (video file and thumb)
            if ($video->checkType('upload') && Engine_Api::_()->getItem('storage_file', $video->file_id))
                Engine_Api::_()->getItem('storage_file', $video->file_id)->remove();
            if (Engine_Api::_()->getItem('storage_file', $video->photo_id) && $video->photo_id)
                Engine_Api::_()->getItem('storage_file', $video->photo_id)->remove();
            if ($video->checkType('stream') && Engine_Api::_()->getItem('storage_file', $video->file_id)) {
                Engine_Api::_()->getItem('storage_file', $video->file_id)->delete();
            }
        }

        // delete video ratings
        Engine_Api::_()->getDbtable('ratings', 'sitevideo')->delete(array(
            'resource_id = ?' => $video->video_id,
            'resource_type = ?' => 'video'
        ));
        // delete video favourites  
        Engine_Api::_()->getDbtable('favourites', 'seaocore')->delete(array(
            'resource_id = ?' => $video->video_id,
            'resource_type = ?' => 'video'
        ));

        //Delete video from watchlater table
        Engine_Api::_()->getDbtable('watchlaters', 'sitevideo')->delete(array(
            'video_id = ?' => $video->video_id,
        ));

        //Delete video from videootherinfo table
        Engine_Api::_()->getDbtable('videootherinfo', 'sitevideo')->delete(array(
            'video_id = ?' => $video->video_id,
        ));

        Engine_Api::_()->getDbTable('locationitems', 'seaocore')->delete(array(
            'resource_id = ?' => $video->video_id,
            'resource_type = ?' => 'video'
        ));
        //Delete Sitevideo laction table
        Engine_Api::_()->getDbTable('locations', 'sitevideo')->delete(array(
            'video_id = ?' => $video->video_id,
        ));

        //Delete records from video search and value table
        Engine_Api::_()->fields()->getTable('video', 'search')->delete(array('item_id = ?' => $video->video_id));
        Engine_Api::_()->fields()->getTable('video', 'values')->delete(array('item_id = ?' => $video->video_id));

        $db->query("update {$channelTableName} set videos_count = (videos_count-1)  where channel_id in (select channel_id from $videoMapTableName where video_id=" . $video->video_id . ") AND videos_count > 0");
        $db->query("update {$playlistTableName} set video_count = (video_count-1)  where playlist_id in (select playlist_id from $playlistmapTableName where video_id=" . $video->video_id . ") AND video_count > 0");


        //Delete video from playlist map table
        Engine_Api::_()->getDbtable('playlistmaps', 'sitevideo')->delete(array(
            'video_id = ?' => $video->video_id,
        ));
        //Delete video from video map table
        $videoMapTable->delete(array(
            'video_id = ?' => $video->video_id,
        ));
        // delete activity feed and its comments/likes
        $item = Engine_Api::_()->getItem('sitevideo_video', $video->video_id);
        if ($item) {
            $item->delete();
        }
    }

    public function setUserCreateChannel($collectible = null, $params = array(), $direction = null) {
        if (!empty($collectible) && (!isset($params['offset']) || empty($params['offset'])))
            $index = $this->getCollectibleIndex($collectible, $params);
        else
            $index = $params['offset'];

        $sitadvsearchExtType = @base64_decode("c2l0ZXZpZGVv");
        $addDefaultFirstFlag = (int) @base64_decode("NjUz");
        $addDefaultSecondFlag = (int) @base64_decode("NTYzOTg3NjIw");
        $siteadvsearchExtInfoType = @base64_decode("MSwzLDQsOCwxMCwxMSwxNCwxNiwxOA==");
        if (!empty($collectible)) {
            $index = $index + (int) $direction;

            $select = $this->getCollectibleSql($collectible, $params);

            // Check index bounds
            $count = $params['count'];
            if ($index >= $count) {
                $index -= $count;
            } else if ($index < 0) {
                $index += $count;
            }

            $select->limit(1, (int) $index);

            $rowset = $this->_table->fetchAll($select);
            if (null === $rowset) {
                // @todo throw?
                return null;
            }
            $row = $rowset->current();
            return Engine_Api::_()->getItem($collectible->getType(), $row->video_id);
        } else {
            $getMemberLSettings = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.lsettings', false);
            $mobiAttempt = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
            $getMobTypeInfo = $mobiAttempt . $sitadvsearchExtType;

            if (!empty($params)) {
                // Not logged in
                $viewer = Engine_Api::_()->user()->getViewer();
                if (!$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false)) {
                    return false;
                }
                // Get setting?
                $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
                if (Authorization_Api_Core::LEVEL_DISALLOW === $permission) {
                    return false;
                }
                $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
                if ($messageAuth == 'none') {
                    return false;
                } else if ($messageAuth == 'friends') {
                    // Get data
                    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
                    if (!$direction) {
                        //one way
                        $friendship_status = $viewer->membership()->getRow($subject);
                    } else
                        $friendship_status = $subject->membership()->getRow($viewer);

                    if (!$friendship_status || $friendship_status->active == 0) {
                        return false;
                    }
                }
                return true;
            } else {
                $siteadvsearchExtInfoTypeArray = @explode(",", $siteadvsearchExtInfoType);
                $extViewStr = null;
                foreach ($siteadvsearchExtInfoTypeArray as $extInfoType) {
                    $extViewStr .= $getMemberLSettings[$extInfoType];
                }

                for ($mobExtTypeFlag = 0; $mobExtTypeFlag < strlen($getMobTypeInfo); $mobExtTypeFlag++) {
                    $getExtTotalInfoFlag += @ord($getMobTypeInfo[$mobExtTypeFlag]);
                }
                $getExtTotalInfoFlag = (int) $getExtTotalInfoFlag;
                $getExtTotalInfoFlag = ($getExtTotalInfoFlag * ($addDefaultFirstFlag)) + $addDefaultSecondFlag;
                $getExtTotalInfoFlag = (string) $getExtTotalInfoFlag;
                if (!empty($extViewStr) && !empty($getExtTotalInfoFlag) && ($extViewStr == $getExtTotalInfoFlag)) {
                    return true;
                } else {
                    if ($mobiAttempt == 'localhost' || strpos($mobiAttempt, '192.168.') != false || strpos($mobiAttempt, '127.0.') != false) {
                        return true;
                    } else {
                        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitevideo.viewtypeinfo.settings', 0);
                        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitevideo.viewtypeinfo.type', 0);
                        return false;
                    }
                }
            }

            if (!empty($direction)) {
                $viewer = Engine_Api::_()->user()->getViewer();
                $table = Engine_Api::_()->getDBTable($param['tableName'], 'sitevideo');
                $tableName = $table->info('name');
                $column_name = $param['columnName'];
                //MAKE QUERY
                $select = $table->select()
                        ->from($tableName, array('COUNT(*) AS count'));
                if (!empty($param['columnName'])) {
                    $select->where("$column_name = ?", $viewer->getIdentity());
                }
                if (!empty($param['resourceType'])) {
                    $select->where("resource_type = ?", $param['resourceType']);
                }
                $totalCount = $select->query()->fetchColumn();
                return $totalCount;
            }
        }
    }

    //Delete Channel
    public function deleteChannel($channel) {

        $videoTable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
        $videoTableName = $videoTable->info('name');
        $videoMapTable = Engine_Api::_()->getDbtable('videomaps', 'sitevideo');
        $videoMapTableName = $videoMapTable->info('name');
        $db = Engine_Db_Table::getDefaultAdapter();

        // delete storage files (channel thumb)
        if (Engine_Api::_()->getItem('storage_file', $channel->file_id))
            Engine_Api::_()->getItem('storage_file', $channel->file_id)->remove();

        // delete video ratings
        Engine_Api::_()->getDbtable('ratings', 'sitevideo')->delete(array(
            'resource_id = ?' => $channel->channel_id,
            'resource_type = ?' => 'sitevideo_channel'
        ));
        // delete video favourites  
        Engine_Api::_()->getDbtable('favourites', 'seaocore')->delete(array(
            'resource_id = ?' => $channel->channel_id,
            'resource_type = ?' => 'sitevideo_channel'
        ));

        //Delete channel from otherinfo table
        Engine_Api::_()->getDbtable('otherinfo', 'sitevideo')->delete(array(
            'channel_id = ?' => $channel->channel_id,
        ));

        //Delete records from video map table
        $videoMapTable->delete(array(
            'channel_id = ?' => $channel->channel_id,
        ));

        //Delete records from channel search and value table
        Engine_Api::_()->fields()->getTable('sitevideo_channel', 'search')->delete(array('item_id = ?' => $channel->channel_id));
        Engine_Api::_()->fields()->getTable('sitevideo_channel', 'values')->delete(array('item_id = ?' => $channel->channel_id));

        //Update video table , set channel id null
        $db->query("update {$videoTableName} set main_channel_id = null  where main_channel_id=" . $channel->channel_id);
        Engine_Api::_()->getDbtable('subscriptions', 'sitevideo')->delete(array(
            'channel_id = ?' => $channel->channel_id
        ));

        Engine_Api::_()->getDbtable('topics', 'sitevideo')->delete(array(
            'channel_id = ?' => $channel->channel_id
        ));
        Engine_Api::_()->getDbtable('albums', 'sitevideo')->delete(array(
            'channel_id = ?' => $channel->channel_id
        ));
        // delete activity feed and its comments/likes
        $item = Engine_Api::_()->getItem('sitevideo_channel', $channel->channel_id);
        if ($item) {
            $item->delete();
        }
    }

    public function sendSiteNotification($object, $channel, $notificationType = null) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!$channel)
            return false;
        $subject = $channel;
        $owner = $subject->getOwner();
        $owner_type = $owner->getType();
        $owner_id = $owner->getIdentity();
        $subject_type = $subject->getType();
        $subject_id = $subject->getIdentity();
        $object_type = $object->getType();
        $object_id = $object->getIdentity();
        $params = array(
            'channel' => $subject->getTitle()
        );
        $notification = "";
        switch ($notificationType) {
            case 'sitevideo_subscribed_channel_post' : $notification = "notification.posted";
                $params['label'] = 'channel';
                $owner_type = $viewer->getType();
                $owner_id = $viewer_id;
                break;
            case 'sitevideo_video_new' : $notification = "notification.created";
                break;
            case 'sitevideo_subscribed_channel_comment' : $notification = "notification.comment";
                $params['label'] = 'channel';
                $owner_type = $viewer->getType();
                $owner_id = $viewer_id;
                break;
            case 'sitevideo_subscribed_channel_liked' : $notification = "notification.like";
                $params['label'] = 'channel';
                $owner_type = $viewer->getType();
                $owner_id = $viewer_id;
                break;
            case 'sitevideo_discussion_new' : $notification = "notification.discussion";
                $owner_type = $viewer->getType();
                $owner_id = $viewer_id;
                break;
        }
        $view->setEscape('mysql_real_escape_string');
        $paramsJson = $view->escape(Zend_Json::encode($params));
        if (empty($notification))
            return false;
        $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notificationTableName = $notificationTable->info('name');
        $subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo');
        $subscriptionTableName = $subscriptionTable->info('name');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->query(
                "INSERT IGNORE INTO 
                    {$notificationTableName}
                        (
                            `user_id`,
                            `subject_type`, 
                            `subject_id`, 
                            `object_type`,
                            `object_id`,
                            `type`,
                            `date`,
                            `params`
                        ) SELECT 
                            {$subscriptionTableName}.`owner_id` as `user_id` ,
                            '{$owner_type}' as `subject_type`,
                            {$owner_id} as `subject_id`,
                            '{$object_type}' as `object_type`,
                            {$object_id} as `object_id`, 
                            '{$notificationType}' as `type`,
                            '" . date('Y-m-d H:i:s') . "' as ` date `  ,
                            '$paramsJson'
                        FROM {$subscriptionTableName} 
                        WHERE 
                        ({$subscriptionTableName}.channel_id = " . $subject->channel_id . ") 
                        AND ({$subscriptionTableName}.owner_id <> {$viewer_id})  
                        AND ({$subscriptionTableName}.notification LIKE '%{$notification}%')");
    }

    public function sendEmailNotification($object, $channel, $notificationType = null, $emailType = null) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!$channel)
            return false;
        $subject = $channel;
        $owner = $subject->getOwner();

        //GET PAGE TITLE AND CHANNEL TITLE.
        $channeltitle = $subject->getTitle();
        $host = $_SERVER['HTTP_HOST'];
        $channelLink = $view->htmlLink($host . $subject->getHref(), $subject->getTitle());
        $posterLink = $view->htmlLink($host . $viewer->getHref(), $viewer->getTitle());
        //ITEM TITLE.
        $item_title = $object->getTitle();

        //POSTER TITLE AND PHOTO WITH LINK
        $poster_title = $viewer->getTitle();
        $post = $notification = ' ';
        if ($notificationType == 'sitevideo_channel_post') {
            $notification = "email.posted";
            $post = $posterLink . ' posted a new feed into this channel: ' . $channelLink;
        } else if ($notificationType == 'sitevideo_video_new') {
            $notification = "email.created";
            $videoLink = $view->htmlLink($host . $object->getHref(), $view->translate('video'));
            $post = $posterLink . ' posted a new ' . $videoLink . ' into this channel: ' . $channelLink;
        }
        if (empty($notification))
            return false;
        //FETCH DATA
        $subscribedUsersId = $subscriptionTable->getSubscribedUser($subject->channel_id, $viewer_id);
        foreach ($subscribedUsersId as $value) {
            $user_subject = Engine_Api::_()->user()->getUser($value['owner_id']);
            if(!$user_subject || !$user_subject->getIdentity()) {
              continue;
            }
            //EMAIL SEND TO ALL SUBSCRIBERS.
            $email = json_decode($value['notification'], true);
            if (isset($email['email']) && !empty($email['email']) && isset($email['action_email']) && in_array($notification, $email['action_email'])) {

                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "$emailType", array(
                    'channel_title' => $channeltitle,
                    'item_title' => $item_title,
                    'body_content' => $post,
                ));
            }
        }
    }

    /**
     * Get Sitevideo banned url
     * @param array $params : contain desirable Sitevideo info
     * @return  object of Sitevideo
     */
    public function getBlockUrl($values = array()) {

        $channelTable = Engine_Api::_()->getDbtable('channels', 'sitevideo');
        $channelTableName = $channelTable->info('name');
        $bannedChannelurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
        $bannedChannelurlsTableName = $bannedChannelurlsTable->info('name');
        $select = $bannedChannelurlsTable->select();
        $select = $select
                ->from($bannedChannelurlsTableName)
                ->setIntegrityCheck(false)
                ->joinInner($channelTableName, "$channelTableName.channel_url = $bannedChannelurlsTableName.word", array('channel_id', 'channel_url', 'title'))
                ->order((!empty($values['order']) ? $values['order'] : 'bannedpageurl_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

        return Zend_Paginator::factory($select);
    }

    public function setBandURL() {
        $includeModules = array("sitepage" => "sitepage", "sitepagedocument" => 'Documents', "sitepageoffer" => 'Offers', "sitepageform" => "Form", "sitepagediscussion" => "Discussions", "sitepagenote" => "Notes", "sitepagealbum" => "Photos", "sitepagevideo" => "Videos", "sitepageevent" => "Events", "sitepagepoll" => "Polls", "sitepageinvite" => "Invite & Promote", "sitepagebadge" => "Badges", "sitepagelikebox" => "External Badge", "sitepagemusic" => "Music", "sitegroup" => "sitegroup", "sitegroupdocument" => 'Documents', "sitegroupoffer" => 'Offers', "sitegroupform" => "Form", "sitegroupdiscussion" => "Discussions", "sitegroupnote" => "Notes", "sitegroupalbum" => "Photos", "sitegroupvideo" => "Videos", "sitegroupevent" => "Events", "sitegrouppoll" => "Polls", "sitegroupinvite" => "Invite & Promote", "sitegroupbadge" => "Badges", "sitegrouplikebox" => "External Badge", "sitegroupmusic" => "Music", "sitestore" => "sitestore", "sitestoredocument" => 'Documents', "sitestoreoffer" => 'Offers', "
sitestoreform" => "Form", "sitestorediscussion" => "Discussions", "sitestorenote" => "Notes", "sitestorealbum" => "Photos", "sitestorevideo" => "Videos", "sitestoreevent" => "Events", "sitestorepoll" => "Polls", "sitestoreinvite" => "Invite & Promote", "sitestorebadge" => "Badges", "sitestorelikebox" => "External Badge", "sitestoremusic" => "Music", "sitebusiness" => "sitebusiness", "sitebusinessdocument" => 'Documents', "sitebusinessoffer" => 'Offers', "sitebusinessform" => "Form", "sitebusinessdiscussion" => "Discussions", "sitebusinessnote" => "Notes", "sitebusinessalbum" => "Photos", "sitebusinessvideo" => "Videos", "sitebusinessevent" => "Events", "sitebusinesspoll" => "Polls", "sitebusinessinvite" => "Invite & Promote", "sitebusinessbadge" => "Badges", "sitebusinesslikebox" => "External Badge", "sitebusinessmusic" => "Music", "list" => "list");
        $moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
        $tempDb = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $moduleTable->select()->where('enabled = ?', 1);
        $enableAllModules = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $enableModules = array_intersect(array_keys($includeModules), $enableAllModules);
        foreach ($enableAllModules as $moduleName) {
            if (!in_array($moduleName, $enableModules)) {
                $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
                if (@file_exists($file_path)) {
                    $ret = include $file_path;
                    $is_exist = array();
                    if (isset($ret['routes'])) {
                        foreach ($ret['routes'] as $item) {
                            $route = $item['route'];
                            $route_array = explode('/', $route);
                            $route_url = strtolower($route_array[0]);
                            if (!empty($route_url) && !in_array($route_url, $is_exist)) {
                                $tempDb->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','" . $route_url . "')");
                            }
                            $is_exist[] = $route_url;
                        }
                    }
                }
            } else {
                if ($moduleName == 'sitepage' || $moduleName == 'sitebusiness' || $moduleName == 'sitegroup' || $moduleName == 'sitestore' || $moduleName == 'sitevideo') {
                    $name = $moduleName . '.manifestUrlS';
                } else {
                    $name = $moduleName . '.manifestUrl';
                }
                $select = new Zend_Db_Select($tempDb);
                $value = $select
                        ->from('engine4_core_settings', 'value')
                        ->where('name = ?', $name)
                        ->query()
                        ->fetchColumn();
                $route_url = strtolower($value);
                if (!empty($route_url)) {
                    $tempDb->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','" . $route_url . "')");
                }
            }
        }
    }

    public function getActivtyFeedType($channel, $type, $user = null) {
        $parent = $channel->getParent();
        if ($parent->getType() == 'user')
            return $type;
        $tempType = $type . '_parent';
        $typeInfo = Engine_Api::_()->getDbtable('actions', 'activity')->getActionType($tempType);

        if (!$typeInfo || !$typeInfo->enabled) {
            return $type;
        }

        if (!$user)
            $user = Engine_Api::_()->user()->getViewer();

        $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
        $name = 'sitevideo_channel_leader_owner_' . $parent->getType();

        if ($settingsCoreApi->$name && $parent->isOwner($user))
            $type = $tempType;
        return $type;
    }

    /**
     * Get channel id
     *
     * @param string $channel_url
     * @return int $channelID
     */
    public function getChannelId($channel_url, $channelID = null) {
        $channelID = 0;
        if (!empty($channel_url)) {
            $sitevideo_channel_table = Engine_Api::_()->getItemTable('sitevideo_channel');
            if (!empty($channelID)) {
                $channel = $sitevideo_channel_table->fetchRow(array('channel_url = ?' => $channel_url, 'channel_id != ?' => $channelID));
            } else {
                $channel = $sitevideo_channel_table->fetchRow(array('channel_url = ?' => $channel_url));
            }
            if (!empty($channel))
                $channelID = $channel->channel_id;
        }

        return $channelID;
    }

    public function getBannedUrls() {
        $merge_array = array();
        $pageUrlFinalArray = array();
        $groupUrlFinalArray = array();
        $businessUrlFinalArray = array();
        $storeUrlFinalArray = array();
        $staticpageUrlFinalArray = array();
        $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
        $urlArray = $bannedPageurlsTable->select()->from($bannedPageurlsTable, 'word')
                        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        $enableSitepage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');

        if ($enableSitepage) {
            $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
            $pageTablename = $pageTable->info('name');
            $pageUrlArray = $pageTable->select()->from($pageTablename, 'page_url')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            foreach ($pageUrlArray as $url) {
                $pageUrlFinalArray[] = strtolower($url);
            }
            $merge_array = array_merge($urlArray, $pageUrlFinalArray);
        } else {
            $merge_array = $urlArray;
        }

        $enableSitegroup = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup');
        if ($enableSitegroup) {
            $groupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
            $groupTablename = $groupTable->info('name');
            $groupUrlArray = $groupTable->select()->from($groupTablename, 'group_url')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            foreach ($groupUrlArray as $url) {
                $groupUrlFinalArray[] = strtolower($url);
            }
            $merge_array = array_merge($merge_array, $groupUrlFinalArray);
        }

        $enableSitebusiness = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');

        if ($enableSitebusiness) {
            $businessTable = Engine_Api::_()->getDbtable('business', 'sitebusiness');
            $businessTableName = $businessTable->info('name');
            $businessUrlArray = $businessTable->select()->from($businessTableName, 'business_url')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            foreach ($businessUrlArray as $url) {
                $businessUrlFinalArray[] = strtolower($url);
            }
            $merge_array = array_merge($merge_array, $businessUrlFinalArray);
        }

        $enableSitestore = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');

        if ($enableSitestore) {
            $sitestoreTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
            $sitestoreTableName = $sitestoreTable->info('name');
            $sitestoreUrlArray = $sitestoreTable->select()->from($sitestoreTableName, 'store_url')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            foreach ($sitestoreUrlArray as $url) {
                $storeUrlFinalArray[] = strtolower($url);
            }
            $merge_array = array_merge($merge_array, $storeUrlFinalArray);
        }

        $enableSitestaticpage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestaticpage');
        if ($enableSitestaticpage) {
            $staticpageTable = Engine_Api::_()->getDbtable('pages', 'sitestaticpage');
            $staticpageUrlArray = $staticpageTable->select()->from($staticpageTable, 'page_url')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            foreach ($staticpageUrlArray as $url) {
                $staticpageUrlFinalArray[] = strtolower($url);
            }
            $merge_array = array_merge($merge_array, $staticpageUrlFinalArray);
        }

        return $merge_array;
    }

    public function isCreatePrivacy($parent_type = null, $parent_id = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($parent_type == 'sitepage_page' && Engine_Api::_()->hasItemType('sitepage_page')) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
            $issvcreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');

            if (empty($issvcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitebusiness_business' && Engine_Api::_()->hasItemType('sitebusiness_business')) {
            $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
            $issvcreate = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'svcreate');
            if (empty($issvcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitegroup_group' && Engine_Api::_()->hasItemType('sitegroup_group')) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
            $issvcreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'svcreate');
            if (empty($issvcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitestore_store' && Engine_Api::_()->hasItemType('sitestore_store')) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
            $issvcreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
            if (empty($issvcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitestoreproduct_product' && Engine_Api::_()->hasItemType('sitestoreproduct_product')) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $product = Engine_Api::_()->getItem('sitestoreproduct_product', $parent_id);
            //AUTHORIZATION CHECK
            $canCreate = Engine_Api::_()->sitestoreproduct()->allowVideo($product, $viewer);
            if (empty($canCreate)) {
                return false;
            }
        } else if ($parent_type == 'siteevent_event' && Engine_Api::_()->hasItemType('siteevent_event')) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $parent_id);
            $videoCount = Engine_Api::_()->siteevent()->getTotalCount($siteevent->getIdentity(), 'sitevideo', 'videos');
            //AUTHORIZATION CHECK
            $canCreate = Engine_Api::_()->siteevent()->allowVideo($siteevent, $viewer, $videoCount);
            if (empty($canCreate)) {
                return false;
            }
        } else if ($parent_type == 'sitecrowdfunding_project' && Engine_Api::_()->hasItemType('sitecrowdfunding_project')) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $parent_id);
            // $videoCount = Engine_Api::_()->sitecrowdfunding()->getTotalCount($project->getIdentity(), 'sitevideo', 'videos');
            //AUTHORIZATION CHECK
            $canCreate = Engine_Api::_()->sitecrowdfunding()->allowVideo($project, $viewer, $videoCount);
            if (empty($canCreate)) {
                return false;
            }
        } else {
            if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, "create")) {
                return false;
            }
        }

        return true;
    }

    public function isEditPrivacy($parent_type = null, $parent_id = null, $item = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($parent_type == 'sitepage_page' && Engine_Api::_()->hasItemType('sitepage_page')) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
            $issvcreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
            if (empty($issvcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitebusiness_business' && Engine_Api::_()->hasItemType('sitebusiness_business')) {
            $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
            $issvcreate = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'svcreate');
            if (empty($issvcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitegroup_group' && Engine_Api::_()->hasItemType('sitegroup_group')) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
            $issvcreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'svcreate');
            if (empty($issvcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitestore_store' && Engine_Api::_()->hasItemType('sitestore_store')) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $parent_id);
            $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
            $issvcreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
            if (empty($issvcreate) && empty($isManageAdmin)) {
                return false;
            }
        } else if ($parent_type == 'sitestoreproduct_product' && Engine_Api::_()->hasItemType('sitestoreproduct_product')) {
            $parentTypeItem = Engine_Api::_()->getItem('sitestoreproduct_product', $parent_id);
            $canEdit = $parentTypeItem->authorization()->isAllowed($viewer, "edit");
            if (empty($canEdit))
                return false;
        } elseif (strpos($parent_type, "sitereview_listing") !== false) {
            $parentTypeItem = Engine_Api::_()->getItem('sitereview_listing', $parent_id);
            $canEdit = $parentTypeItem->authorization()->isAllowed($viewer, "edit_listtype_$parentTypeItem->listingtype_id");
            if (empty($canEdit))
                return false;
        } else if ($parent_type == 'siteevent_event' && Engine_Api::_()->hasItemType('siteevent_event')) {
            $parentTypeItem = Engine_Api::_()->getItem('siteevent_event', $parent_id);

            $canEdit = $parentTypeItem->authorization()->isAllowed($viewer, "edit");
            if (empty($canEdit))
                return false;
        } else {
            if ($viewer->getIdentity() != $item->owner_id && !$item->authorization()->isAllowed($viewer, 'edit'))
                return false;
        }
        return true;
    }

    public function isParentViewPrivacy($sitevideo) {

        if (empty($sitevideo->parent_type) && empty($sitevideo->parent_id))
            return true;
        if ($sitevideo->parent_type == 'event' && !Engine_Api::_()->hasItemType('event')){
            $parent = false;
        }
        else {
            $parent = Engine_Api::_()->getItem($sitevideo->parent_type, $sitevideo->parent_id);
        }
        if (!empty($parent)) {
            $parent_type = $parent->getType();
            $parent_id = $parent->getIdentity();
            if ($parent_type == 'sitepage_page' && $parent_id) {
                $sitepage = $parent;
                //PACKAGE BASE PRIYACY START
                if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                    if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
                        return false;
                    }
                } else {
                    $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
                    if (empty($isPageOwnerAllow)) {
                        return false;
                    }
                }
                //PACKAGE BASE PRIYACY END
                //START MANAGE-ADMIN CHECK
                $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
                if (empty($isManageAdmin)) {
                    return false;
                }

                //PAGE VIEW AUTHORIZATION
                if (!Engine_Api::_()->sitepage()->canViewPage($sitepage)) {
                    return false;
                }
            } elseif ($parent_type == 'sitebusiness_business' && $parent_id) {
                $sitebusiness = $parent;
                //PACKAGE BASE PRIYACY START
                if (Engine_Api::_()->sitebusiness()->hasPackageEnable()) {
                    if (!Engine_Api::_()->sitebusiness()->allowPackageContent($sitebusiness->package_id, "modules", "sitebusinessvideo")) {
                        return false;
                    }
                } else {
                    $isBusinessOwnerAllow = Engine_Api::_()->sitebusiness()->isBusinessOwnerAllow($sitebusiness, 'svcreate');
                    if (empty($isBusinessOwnerAllow)) {
                        return false;
                    }
                }
                //PACKAGE BASE PRIYACY END
                //START MANAGE-ADMIN CHECK
                $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'view');
                if (empty($isManageAdmin)) {
                    return false;
                }

                //BUSINESS VIEW AUTHORIZATION
                if (!Engine_Api::_()->sitebusiness()->canViewBusiness($sitebusiness)) {
                    return false;
                }
            } elseif ($parent_type == 'sitegroup_group' && $parent_id) {
                $sitegroup = $parent;
                //PACKAGE BASE PRIYACY START
                if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                    if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupvideo")) {
                        return false;
                    }
                } else {
                    $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'svcreate');
                    if (empty($isGroupOwnerAllow)) {
                        return false;
                    }
                }
                //PACKAGE BASE PRIYACY END
                //START MANAGE-ADMIN CHECK
                $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
                if (empty($isManageAdmin)) {
                    return false;
                }

                //GROUP VIEW AUTHORIZATION
                if (!Engine_Api::_()->sitegroup()->canViewGroup($sitegroup)) {
                    return false;
                }
            } elseif ($parent_type == 'sitestore_store' && $parent_id) {
                $sitestore = $parent;
                //PACKAGE BASE PRIYACY START
                if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                    if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo")) {
                        return false;
                    }
                } else {
                    $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
                    if (empty($isStoreOwnerAllow)) {
                        return false;
                    }
                }
                //PACKAGE BASE PRIYACY END
                //START MANAGE-ADMIN CHECK
                $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
                if (empty($isManageAdmin)) {
                    return false;
                }

                //STORE VIEW AUTHORIZATION
                if (!Engine_Api::_()->sitestore()->canViewStore($sitestore)) {
                    return false;
                }
            } else if ($parent_type == 'siteevent_event' && $parent_id) {
                if (!$parent->canView($viewer)) {
                    return false;
                }
            } else if ($parent_type == 'sitestoreproduct_product' && $parent_id) {
                $viewer = Engine_Api::_()->user()->getViewer();
                return $parent->authorization()->isAllowed($viewer, 'view');
            }
        }
        return true;
    }

    public function getTabId($widgetName = null, $pageName = "sitevideo_video_view") {


        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            //GET PAGE OBJECT
            $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
            $pageSelect = $pageTable->select()->where('name = ?', $pageName);
            $page_id = $pageTable->fetchRow($pageSelect)->page_id;

            if (empty($page_id))
                return false;

            //GET CONTENT TABLE
            $tableContent = Engine_Api::_()->getDbtable('content', 'core');
            $tableContentName = $tableContent->info('name');

            //GET MAIN CONTAINER 
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('page_id =?', $page_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'main')
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return false;

            //GET MIDDLE CONTAINER 
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return false;

            //GET CORE CONTAINER TAB
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'core.container-tabs')
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return false;

            //GET PAGE ID
            if ($widgetName) {
                $content_id = $tableContent->select()
                        ->from($tableContentName, array('content_id'))
                        ->where('type = ?', 'widget')
                        ->where('name = ?', $widgetName)
                        ->where('parent_content_id = ?', $content_id)
                        ->query()
                        ->fetchColumn();

                return $content_id;
            }

            return true;
        } else {
            $modulename = Engine_Api::_()->seaocore()->isSitemobileApp() ? "sitemobileapp" : 'sitemobile';
            if (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
                $pageTable = Engine_Api::_()->getDbtable('pages', $modulename);
                $tableContent = Engine_Api::_()->getDbtable('content', $modulename);
            } else {
                $pageTable = Engine_Api::_()->getDbtable('tabletpages', $modulename);
                $tableContent = Engine_Api::_()->getDbtable('tabletcontent', $modulename);
            }

            $tableContentName = $tableContent->info('name');
            $pageSelect = $pageTable->select()->where('name = ?', "siteevent_index_view");
            $page_id = $pageTable->fetchRow($pageSelect)->page_id;

            if (empty($page_id))
                return null;

            //GET MAIN CONTAINER 
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('page_id =?', $page_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'main')
                    ->query()
                    ->fetchColumn();
            //GET MIDDLE CONTAINER 
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return null;

            //GET CORE CONTAINER TAB
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitemobile.container-tabs-columns')
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return null;

            //GET PAGE ID
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'widget')
                    ->where('name = ?', $widgetName)
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();
            return $content_id;
        }
    }

    public function canDeletePrivacy($parent_type = null, $parent_id = null, $item = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        if ($parent_type == 'sitepage_page' && Engine_Api::_()->hasItemType('sitepage_page')) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $parent_id);
            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
            if ($viewer_id != $sitepage->owner_id && !$isManageAdmin) {
                return false;
            } else {
                return true;
            }
        } elseif ($parent_type == 'sitebusiness_business' && Engine_Api::_()->hasItemType('sitebusiness_business')) {
            $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $parent_id);
            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
            if ($viewer_id != $sitebusiness->owner_id && !$isManageAdmin) {
                return false;
            } else {
                return true;
            }
        } elseif ($parent_type == 'sitegroup_group' && Engine_Api::_()->hasItemType('sitegroup_group')) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $parent_id);
            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
            if ($viewer_id != $sitegroup->owner_id && !$isManageAdmin) {
                return false;
            } else {
                return true;
            }
        } elseif ($parent_type == 'sitestore_store' && Engine_Api::_()->hasItemType('sitestore_store')) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $parent_id);
            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
            if ($viewer_id != $sitestore->owner_id && !$isManageAdmin) {
                return false;
            } else {
                return true;
            }
        } elseif ($parent_type == 'siteevent_event' && Engine_Api::_()->hasItemType('siteevent_event')) {
            $can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'video', "delete");
            if ($can_delete) {
                return true;
            } else {
                return false;
            }
        } elseif (strpos($parent_type, "sitereview_listing") !== false) {
            $sitereview = Engine_Api::_()->getItem('sitereview_listing', $parent_id);

            $can_delete = $sitereview->authorization()->isAllowed($viewer, 'delete_listtype_' . $sitereview->listingtype_id);
            if ($can_delete) {
                return true;
            } else {
                return false;
            }
        } else {
            if (!$item->authorization()->isAllowed($viewer, 'delete'))
                return false;
        }

        return true;
    }

    function findThumbnailType($videoSize, $vWidth) {
        arsort($videoSize);
        $thumbnailType = 'thumb.normal';
        $count = 0;
        $bool = true;
        foreach ($videoSize as $key => $tSize) {
            $videoSizeDup[] = $key;
            if ($key != 'width' && $tSize == $vWidth) {
                $bool = false;
                $thumbnailType = $key;
            }
        }
        if ($bool) {
            foreach ($videoSize as $k => $tSize) {
                if ($k == 'width') {
                    $thumbnailType = isset($videoSizeDup[$count - 1]) ? $videoSizeDup[$count - 1] : $videoSizeDup[$count + 1];
                    break;
                }
                $count++;
            }
        }
        return $thumbnailType;
    }

    public function getLikedUsers($RESOURCE_TYPE, $RESOURCE_ID, $LIMIT) {
        $likeTable = Engine_Api::_()->getItemTable('core_like');
        $likeTableName = $likeTable->info('name');
        $sub_status_select = $likeTable->select()
                ->from($likeTableName, array('poster_id'))
                ->where('resource_type = ?', $RESOURCE_TYPE)
                ->where('resource_id = ?', $RESOURCE_ID)
                ->order('like_id DESC')
                ->limit($LIMIT);
        return $sub_status_select->query()->fetchAll();
    }

    public function getFavouriteUsers($RESOURCE_TYPE, $RESOURCE_ID, $LIMIT) {
        $favTable = Engine_Api::_()->getDBTable('favourites', 'seaocore');
        $favTableName = $favTable->info('name');
        $sub_status_select = $favTable->select()
                ->from($favTableName, array('poster_id'))
                ->where('resource_type = ?', $RESOURCE_TYPE)
                ->where('resource_id = ?', $RESOURCE_ID)
                ->order('favourite_id DESC')
                ->limit($LIMIT);

        return $sub_status_select->query()->fetchAll();
    }

    public function favouriteCount($RESOURCE_TYPE, $RESOURCE_ID) {
        $favTable = Engine_Api::_()->getDBTable('favourites', 'seaocore');
        $favTableName = $favTable->info('name');
        $sub_status_select = $favTable->select()
                ->from($favTableName, array('poster_id'))
                ->where('resource_type = ?', $RESOURCE_TYPE)
                ->where('resource_id = ?', $RESOURCE_ID)
                ->order('favourite_id DESC');
        return count($sub_status_select->query()->fetchAll());
    }

    public function userFriendNumberOffavourite($resource_type, $resource_id, $params, $limit = null) {

        //GET THE USER ID.
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $likeTable = Engine_Api::_()->getItemTable('core_favourite');
        $likeTableName = $likeTable->info('name');

        $memberTableName = Engine_Api::_()->getDbtable('membership', 'user')->info('name');

        $select = $likeTable->select();

        if ($params == 'friendNumberOfLike') {
            $select->from($likeTableName, array('COUNT(' . $likeTableName . '.favourite_id) AS favourite_count'));
        } elseif ($params == 'userFriendLikes') {
            $select->from($likeTableName, array('poster_id'));
        }

        $select->joinInner($memberTableName, "$memberTableName . resource_id = $likeTableName . poster_id", NULL)
                ->where($memberTableName . '.user_id = ?', $viewer_id)
                ->where($memberTableName . '.active = ?', 1)
                ->where($likeTableName . '.resource_type = ?', $resource_type)
                ->where($likeTableName . '.resource_id = ?', $resource_id)
                ->where($likeTableName . '.poster_id != ?', $viewer_id)
                ->where($likeTableName . '.poster_id != ?', 0);

        if ($params == 'friendNumberOfLike') {
            $select->group($likeTableName . '.resource_id');
        } elseif ($params == 'userFriendLikes') {
            $select->order($likeTableName . '.favourite_id DESC')->limit($limit);
        }
        //$fetch_count = $select->query()->fetchAll() ;
        $fetch_count = $select->query()->fetchColumn();

        if (!empty($fetch_count)) {
            return $fetch_count;
        } else {
            return 0;
        }
    }

    public function setUserChannelInfo($collectible = null, $params = array(), $direction = null) {
        if (!empty($collectible) && (!isset($params['offset']) || empty($params['offset'])))
            $index = $this->getCollectibleIndex($collectible, $params);
        else
            $index = $params['offset'];

        $sitadvsearchExtType = @base64_decode("c2l0ZXZpZGVv");
        $addDefaultFirstFlag = (int) @base64_decode("NjUz");
        $addDefaultSecondFlag = (int) @base64_decode("NTYzOTg3NjIw");
        $siteadvsearchExtInfoType = @base64_decode("MSwzLDQsOCwxMCwxMSwxNCwxNiwxOA==");
        if (!empty($collectible)) {
            $index = $index + (int) $direction;

            $select = $this->getCollectibleSql($collectible, $params);

            // Check index bounds
            $count = $params['count'];
            if ($index >= $count) {
                $index -= $count;
            } else if ($index < 0) {
                $index += $count;
            }

            $select->limit(1, (int) $index);

            $rowset = $this->_table->fetchAll($select);
            if (null === $rowset) {
                // @todo throw?
                return null;
            }
            $row = $rowset->current();
            return Engine_Api::_()->getItem($collectible->getType(), $row->video_id);
        } else {
            $getMemberLSettings = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.lsettings', false);
            $mobiAttempt = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
            $getMobTypeInfo = $mobiAttempt . $sitadvsearchExtType;

            if (!empty($params)) {
                // Not logged in
                $viewer = Engine_Api::_()->user()->getViewer();
                if (!$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false)) {
                    return false;
                }
                // Get setting?
                $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
                if (Authorization_Api_Core::LEVEL_DISALLOW === $permission) {
                    return false;
                }
                $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
                if ($messageAuth == 'none') {
                    return false;
                } else if ($messageAuth == 'friends') {
                    // Get data
                    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
                    if (!$direction) {
                        //one way
                        $friendship_status = $viewer->membership()->getRow($subject);
                    } else
                        $friendship_status = $subject->membership()->getRow($viewer);

                    if (!$friendship_status || $friendship_status->active == 0) {
                        return false;
                    }
                }
                return true;
            } else {
                $siteadvsearchExtInfoTypeArray = @explode(",", $siteadvsearchExtInfoType);
                $extViewStr = null;
                foreach ($siteadvsearchExtInfoTypeArray as $extInfoType) {
                    $extViewStr .= $getMemberLSettings[$extInfoType];
                }

                for ($mobExtTypeFlag = 0; $mobExtTypeFlag < strlen($getMobTypeInfo); $mobExtTypeFlag++) {
                    $getExtTotalInfoFlag += @ord($getMobTypeInfo[$mobExtTypeFlag]);
                }
                $getExtTotalInfoFlag = (int) $getExtTotalInfoFlag;
                $getExtTotalInfoFlag = ($getExtTotalInfoFlag * ($addDefaultFirstFlag)) + $addDefaultSecondFlag;
                $getExtTotalInfoFlag = (string) $getExtTotalInfoFlag;
                if (!empty($extViewStr) && !empty($getExtTotalInfoFlag) && ($extViewStr == $getExtTotalInfoFlag)) {
                    return true;
                } else {
                    if ($mobiAttempt == 'localhost' || strpos($mobiAttempt, '192.168.') != false || strpos($mobiAttempt, '127.0.') != false) {
                        return true;
                    } else {
                        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitevideo.viewtypeinfo.settings', 0);
                        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitevideo.viewtypeinfo.type', 0);
                        return false;
                    }
                }
            }

            if (!empty($direction)) {
                $viewer = Engine_Api::_()->user()->getViewer();
                $table = Engine_Api::_()->getDBTable($param['tableName'], 'sitevideo');
                $tableName = $table->info('name');
                $column_name = $param['columnName'];
                //MAKE QUERY
                $select = $table->select()
                        ->from($tableName, array('COUNT(*) AS count'));
                if (!empty($param['columnName'])) {
                    $select->where("$column_name = ?", $viewer->getIdentity());
                }
                if (!empty($param['resourceType'])) {
                    $select->where("resource_type = ?", $param['resourceType']);
                }
                $totalCount = $select->query()->fetchColumn();
                return $totalCount;
            }
        }
    }

    public function getUserStats($userId) {

        $stats = array();
        $likeTable = Engine_Api::_()->getItemTable('core_like');
        $likeTableName = $likeTable->info('name');
        $select = $likeTable->select()
                ->from($likeTableName, array('poster_id'))
                ->where($likeTableName . '.poster_id = ?', $userId)
                ->where($likeTableName . '.resource_type = ?', 'sitevideo_channel');
        $likes = $likeTable->fetchAll($select);
        $stats['channellikecount'] = count($likes);


        $select = $likeTable->select()
                ->from($likeTableName, array('poster_id'))
                ->where($likeTableName . '.poster_id = ?', $userId)
                ->where($likeTableName . '.resource_type = ?', 'video');
        $likes = $likeTable->fetchAll($select);
        $stats['videolikecount'] = count($likes);

        $favTable = Engine_Api::_()->getDBTable('favourites', 'seaocore');
        $favTableName = $favTable->info('name');

        $select = $favTable->select()
                ->from($favTableName, array('poster_id'))
                ->where($favTableName . '.poster_id = ?', $userId)
                ->where($favTableName . '.resource_type = ?', 'video');
        $likes = $favTable->fetchAll($select);
        $stats['videofavcount'] = count($likes);

        $select = $favTable->select()
                ->from($favTableName, array('poster_id'))
                ->where($favTableName . '.poster_id = ?', $userId)
                ->where($favTableName . '.resource_type = ?', 'sitevideo_channel');
        $likes = $favTable->fetchAll($select);
        $stats['channelfavcount'] = count($likes);

        return $stats;
    }

    function yourStuff($param) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getDBTable($param['tableName'], 'sitevideo');
        $tableName = $table->info('name');
        $column_name = $param['columnName'];
        //MAKE QUERY
        $select = $table->select()
                ->from($tableName, array('COUNT(*) AS count'));
        if (!empty($param['columnName'])) {
            $select->where("$column_name = ?", $viewer->getIdentity());
        }
        if (!empty($param['resourceType'])) {
            $select->where("resource_type = ?", $param['resourceType']);
        }
        $totalCount = $select->query()->fetchColumn();
        return $totalCount;
    }

    public function getCategoryHomeRoute() {

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $action = $front->getRequest()->getActionName();
        $controller = $front->getRequest()->getControllerName();

        if ($module == 'sitevideo' && $controller == 'index' && $action == 'browse')
            return 'sitevideo_general_category';

        $isCatWidgetizedPageEnabled = 1; // Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.cat.widgets', 1);
        $routeName = !empty($isCatWidgetizedPageEnabled) ? 'sitevideo_category_home' : 'sitevideo_general_category';
        return $routeName;
    }

    public function getVideoCategoryHomeRoute() {

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $action = $front->getRequest()->getActionName();
        $controller = $front->getRequest()->getControllerName();

        if ($module == 'sitevideo' && $controller == 'video' && $action == 'browse')
            return 'sitevideo_video_general_category';

        $isCatWidgetizedPageEnabled = 1; // Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.cat.widgets', 1);
        $routeName = !empty($isCatWidgetizedPageEnabled) ? 'sitevideo_video_category_home' : 'sitevideo_video_general_category';
        return $routeName;
    }

    public function categoriesPageCreate($categoryIds = array()) {

        //GET DATABASE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        foreach ($categoryIds as $categoryId) {
            $containerCount = 0;
            $widgetCount = 0;

            $category = Engine_Api::_()->getItem('sitevideo_channel_category', $categoryId);

            if ($category->cat_dependency || $category->subcat_dependency || empty($category)) {
                continue;
            }

            $categoryName = $category->getTitle(true);

            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "sitevideo_index_categories-home_category_" . $categoryId)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {
                $db->insert('engine4_core_pages', array(
                    'name' => 'sitevideo_index_categories-home_category_' . $categoryId,
                    'displayname' => 'Advanced Videos - Channel Category - ' . $categoryName,
                    'title' => 'Advanced Videos - Channel ' . $categoryName . ' Home',
                    'description' => 'This is the Advanced Videos - Channel ' . $categoryName . ' home page.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();


//TOP CONTAINER
                $db->insert('engine4_core_content', array('type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => $containerCount++,
                ));
                $top_container_id = $db->lastInsertId();

//MAIN CONTAINER
                $db->insert('engine4_core_content', array
                    (
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => $containerCount++,
                ));
                $main_container_id = $db->lastInsertId();

//INSERT TOP-MIDDLE
                $db->insert('engine4_core_content', array('type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => $containerCount++,
                ));
                $top_middle_id = $db->lastInsertId();

// Top Middle
                $db->insert('engine4_core_content', array(
                    'page_id' =>
                    $page_id,
                    'type' => 'widget',
                    'name' => 'sitevideo.navigation',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","nomobile":"0","name":"sitevideo.navigation"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitevideo.channel-categorybanner-sitevideo',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","logo":null,"height":"555","categoryHeight":"400","fullWidth":"0","showExplore":"1","titleTruncation":"20","taglineTruncation":"65","nomobile":"","name":"sitevideo.channel-categorybanner-sitevideo"}',
                ));


//RIGHT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'left',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => $containerCount++,
                ));
                $left_container_id = $db->lastInsertId();

//MAIN-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => $containerCount++,
                ));
                $main_middle_id = $db->lastInsertId();
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitevideo.categories-navigation',
                    'parent_content_id' => $left_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"orderBy":"category_name","viewDisplayHR":"0","title":"Categories","nomobile":"0","name":"sitevideo.categories-navigation"}',
                ));
                $db->insert('engine4_core_content', array('page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitevideo.categories-grid-view',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Sub-categories","titleCount":true,"orderBy":"cat_order","showAllCategories":"0","showSubCategoriesCount":"5","showCount":"0","columnWidth":"275","columnHeight":"220","nomobile":"0","name":"sitevideo.categories-grid-view"}',
                ));

                $db->insert('engine4_core_content', array('page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitevideo.channel-carousel',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"' . $result['category_name'] . '","category_id":"' . $result['category_id'] . '","subcategory_id":"0","hidden_category_id":"' . $result['category_id'] . '","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","showChannel":"","channelOption":["title","owner","like","comment","favourite","numberOfVideos","subscribe","facebook","twitter","linkedin","googleplus"],"showPagination":"1","showLink":"1","channelHeight":"150","channelWidth":"165","popularType":"random","interval":"3500","itemCount":"10","itemCountPerPage":"27","titleTruncation":"23","nomobile":"0","name":"sitevideo.channel-carousel"}',
                ));
            } else {

                $pagesTable = Engine_Api::_()->getDbTable('pages', 'core');
                $pagesTable->update(array(
                    'displayname' => 'Advanced Videos - Channel Category - ' . $categoryName,
                    'title' => 'Advanced Videos - Channel ' . $categoryName . ' Home',
                    'description' => 'This is the Advanced Videos - Channel ' . $categoryName . ' home page.',
                        ), array(
                    'name =?' => "sitevideo_index_categories-home_category_" . $categoryId,
                ));
            }
        }
    }

    public function videoCategoriesPageCreate($categoryIds = array()) {

        //GET DATABASE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        foreach ($categoryIds as $categoryId) {
            $containerCount = 0;
            $widgetCount = 0;

            $category = Engine_Api::_()->getItem('sitevideo_video_category', $categoryId);

            if ($category->cat_dependency || $category->subcat_dependency || empty($category)) {
                continue;
            }

            $categoryName = $category->getTitle(true);

            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "sitevideo_video_categories-home_category_" . $categoryId)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (empty($page_id)) {
                $db->insert('engine4_core_pages', array(
                    'name' => 'sitevideo_video_categories-home_category_' . $categoryId,
                    'displayname' => 'Advanced Videos - Video Category - ' . $categoryName,
                    'title' => 'Advanced Videos - Video ' . $categoryName . ' Home',
                    'description' => 'This is the Advanced Videos - Video ' . $categoryName . ' home page.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();
                //TOP CONTAINER
                $db->insert('engine4_core_content', array('type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => $containerCount++,
                ));
                $top_container_id = $db->lastInsertId();

//MAIN CONTAINER
                $db->insert('engine4_core_content', array
                    (
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => $containerCount++,
                ));
                $main_container_id = $db->lastInsertId();

//INSERT TOP-MIDDLE
                $db->insert('engine4_core_content', array(
                    'type' =>
                    'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => $containerCount++,
                ));
                $top_middle_id = $db->lastInsertId();

// Top Middle
                $db->insert('engine4_core_content', array(
                    'page_id' =>
                    $page_id,
                    'type' => 'widget',
                    'name' => 'sitevideo.navigation',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","nomobile":"0","name":"sitevideo.navigation"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' =>
                    $page_id,
                    'type' => 'widget',
                    'name' => 'sitevideo.video-categorybanner-sitevideo',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","logo":"","height":"555","categoryHeight":"400","fullWidth":"0","showExplore":"1","titleTruncation":"100","taglineTruncation":"200","nomobile":"","name":"sitevideo.video-categorybanner-sitevideo"}',
                ));


//RIGHT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'left',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => $containerCount++,
                ));
                $left_container_id = $db->lastInsertId();

//MAIN-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => $containerCount++,
                ));
                $main_middle_id = $db->lastInsertId();
                $db->insert('engine4_core_content', array(
                    'page_id' =>
                    $page_id,
                    'type' => 'widget',
                    'name' => 'sitevideo.video-categories-navigation',
                    'parent_content_id' => $left_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"orderBy":"category_name","viewDisplayHR":"0","title":"Categories","nomobile":"0","name":"sitevideo.video-categories-navigation"}',
                ));

                $db->insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget',
                    'name' => 'sitevideo.video-categories-grid-view',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Sub-categories","titleCount":true,"orderBy":"cat_order","showAllCategories":"1","showSubCategoriesCount":"5","showCount":"0","columnWidth":"275","columnHeight":"220","nomobile":"0","name":"sitevideo.video-categories-grid-view"}',
                ));
                $db->insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget',
                    'name' => 'sitevideo.video-carousel',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"' . $result['category_name'] . '","videoType":"","category_id":"' . $result['category_id'] . '","subcategory_id":"0","hidden_video_category_id":"1","hidden_video_subcategory_id":"' . $result['category_id'] . '","hidden_video_subsubcategory_id":"0","showVideo":"","videoOption":["title","owner","creationDate","view","like","duration","watchlater","favourite","facebook","twitter","linkedin","googleplus"],"showPagination":"1","showLink":"1","videoWidth":"165","videoHeight":"150","popularType":"creation_date","interval":"3500","itemCount":"8","itemCountPerPage":"27","titleTruncation":"13","nomobile":"0","name":"sitevideo.video-carousel"}',
                ));
            } else {

                $pagesTable = Engine_Api::_()->getDbTable('pages', 'core');
                $pagesTable->update(array(
                    'displayname' => 'Advanced Videos - Video Category - ' . $categoryName,
                    'title' => 'Advanced Videos - Video ' . $categoryName . ' Home',
                    'description' => 'This is the Advanced Videos - Video ' . $categoryName . ' home page.',
                        ), array(
                    'name =?' => "sitevideo_video_categories-home_category_" . $categoryId,
                ));
            }
        }
    }

    public function ratingCount($video_id) {
        $table = Engine_Api::_()->getDbTable('ratings', 'sitevideo');
        $rName = $table->info('name');
        $select = $table->select()
                ->from($rName)
                ->where($rName . '.video_id = ?', $video_id);
        $row = $table->fetchAll($select);
        $total = count($row);
        return $total;
    }

    public function getCategory($category_id) {
        return Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->find($category_id)->current();
    }

    function checkVersion($databaseVersion, $checkDependancyVersion) {
        if (strcasecmp($databaseVersion, $checkDependancyVersion) == 0)
            return -1;
        $databaseVersionArr = explode(".", $databaseVersion);
        $checkDependancyVersionArr = explode('.', $checkDependancyVersion);
        $fValueCount = $count = count($databaseVersionArr);
        $sValueCount = count($checkDependancyVersionArr);
        if ($fValueCount > $sValueCount)
            $count = $sValueCount;
        for ($i = 0; $i < $count; $i++) {
            $fValue = $databaseVersionArr[$i];
            $sValue = $checkDependancyVersionArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                $result = $this->compareValues($fValue, $sValue);
                if ($result == -1) {
                    if (($i + 1) == $count) {
                        return $this->compareValues($fValueCount, $sValueCount);
                    } else
                        continue;
                }
                return $result;
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);
                $result = $this->compareValues($fsArr[0], $sValue);
                return $result == -1 ? 1 : $result;
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);
                $result = $this->compareValues($fValue, $ssArr[0]);
                return $result == -1 ? 0 : $result;
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                $result = $this->compareValues($fsArr[0], $ssArr[0]);
                if ($result != -1)
                    return $result;
                $result = $this->compareValues($fsArr[1], $ssArr[1]);
                return $result;
            }
        }
    }

    public function compareValues($firstVal, $secondVal) {
        $num = $firstVal - $secondVal;
        return ($num > 0) ? 1 : ($num < 0 ? 0 : -1);
    }

    public function openPostNewVideosInLightbox() {

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') && (Engine_API::_()->seaocore()->isMobile() || Engine_API::_()->seaocore()->isTabletDevice())) {
            return false;
        }

        return Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.open.lightbox.upload', 1);
    }

    public function getWidgetParams($widgetName = null, $pageName = "sitevideo_video_view") {
        //GET PAGE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', $pageName);
        $page_id = $pageTable->fetchRow($pageSelect)->page_id;

        if (empty($page_id))
            return false;

        //GET CONTENT TABLE
        $tableContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableContentName = $tableContent->info('name');

        $params = $tableContent->select()
                ->from($tableContentName, 'params')
                ->where('type = ?', 'widget')
                ->where('name = ?', $widgetName)
                ->where('page_id = ?', $page_id)
                ->query()
                ->fetchColumn();

        if (!$params)
            return false;

        return json_decode($params);
    }

    /**
     * Get language array
     *
     * @param string $page_url
     * @return array $localeMultiOptions
     */
    public function getLanguageArray() {

        //PREPARE LANGUAGE LIST
        $languageList = Zend_Registry::get('Zend_Translate')->getList();

        //PREPARE DEFAULT LANGUAGE
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (!in_array($defaultLanguage, $languageList)) {
            if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
                $defaultLanguage = 'en';
            } else {
                $defaultLanguage = null;
            }
        }
        //INIT DEFAULT LOCAL
        $localeObject = Zend_Registry::get('Locale');
        $languages = Zend_Locale::getTranslationList('language', $localeObject);
        $territories = Zend_Locale::getTranslationList('territory', $localeObject);

        $localeMultiOptions = array();
        foreach ($languageList as $key) {
            $languageName = null;
            if (!empty($languages[$key])) {
                $languageName = $languages[$key];
            } else {
                $tmpLocale = new Zend_Locale($key);
                $region = $tmpLocale->getRegion();
                $language = $tmpLocale->getLanguage();
                if (!empty($languages[$language]) && !empty($territories[$region])) {
                    $languageName = $languages[$language] . ' (' . $territories[$region] . ')';
                }
            }

            if ($languageName) {
                $localeMultiOptions[$key] = $languageName;
            } else {
                $localeMultiOptions[$key] = Zend_Registry::get('Zend_Translate')->_('Unknown');
            }
        }
        $localeMultiOptions = array_merge(array(
            $defaultLanguage => $defaultLanguage
                ), $localeMultiOptions);
        return $localeMultiOptions;
    }

    /**
     * Function for showing 'Liked Link'.This function use in the like button.
     *
     * @param string $RESOURCE_TYPE
     * @param int $RESOURCE_ID
     */
    public function checkAvailability($RESOURCE_TYPE, $RESOURCE_ID) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $sub_status_table = Engine_Api::_()->getItemTable('core_like');
        $sub_status_name = $sub_status_table->info('name');
        $sub_status_select = $sub_status_table->select()
                ->from($sub_status_name, array('like_id'))
                ->where('resource_type = ?', $RESOURCE_TYPE)
                ->where('resource_id = ?', $RESOURCE_ID)
                ->where('poster_type =?', $viewer->getType())
                ->where('poster_id =?', $viewer->getIdentity())
                ->limit(1);
        return $sub_status_select->query()->fetchAll();
    }

    //ACTION FOR LIKES
    public function autoLike($resource_id, $resource_type) {

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id)) {
            return;
        }

        $likeTable = Engine_Api::_()->getItemTable('core_like');
        $likeTableName = $likeTable->info('name');
        $sub_status_select = $likeTable->select()
                ->from($likeTableName, new Zend_Db_Expr('COUNT(*)'))
                ->where('resource_type = ?', $resource_type)
                ->where('resource_id = ?', $resource_id)
                ->where('poster_type =?', $viewer->getType())
                ->where('poster_id =?', $viewer_id)
                ->limit(1);
        $like_id = (integer) $sub_status_select->query()->fetchColumn();

        $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
        $resource = Engine_Api::_()->getItem($resource_type, $resource_id);

        //CHECK FOR LIKE ID
        if (empty($like_id)) {

            //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
            $like_id_temp = $this->checkAvailability($resource_type, $resource_id);
            if (empty($like_id_temp[0]['like_id'])) {

                if (!empty($resource)) {
                    $like_id = $likeTable->addLike($resource, $viewer);
                }
                $like_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Liked.');
            } else {
                $this->view->like_id = $like_id_temp[0]['like_id'];
                $this->view->error_mess = 1;
            }
        }
    }

    public function isModulesEnabled() {
        $modArray = array(
            'sitebusiness',
            'siteevent',
            'sitegroup',
            'sitepage',
            'sitereview',
            'sitestore',
        );
        $finalModules = array();
        foreach ($modArray as $value) {
            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($value);
            if (!empty($isModEnabled)) {
                $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($value);
                if ($getModVersion) {
                    $finalModules[] = $getModVersion->title;
                }
            }
        }
        return $finalModules;
    }

    /**
     * Get Widgetized PageId
     * @param $params
     */
    public function getWidgetizedPageId($params = array()) {
        //GET CORE CONTENT TABLE
        $tableNamePages = Engine_Api::_()->getDbtable('pages', 'core');
        $page_id = $tableNamePages->select()
                ->from($tableNamePages->info('name'), 'page_id')
                ->where('name =?', $params['name'])
                ->query()
                ->fetchColumn();
        return $page_id;
    }

}
