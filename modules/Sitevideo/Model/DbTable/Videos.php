<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Videos.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Videos extends Engine_Db_Table {

    protected $_name = 'video_videos';
    protected $_rowClass = 'Sitevideo_Model_Video';
    protected $_serializedColumns = array('networks_privacy');

    public function getVideoSelect(array $params, $customParams = array()) {

        $videoTableName = $this->info('name');

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitevideo');

        $locationTableName = $locationTable->info('name');
        $select = $this->select()->from($videoTableName, '*');
        $ownerTableName = $videoTableName;
        if (isset($customParams)) {
            //GET SEARCH TABLE
            $searchTable = Engine_Api::_()->fields()->getTable('video', 'search')->info('name');
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

            $select = $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($searchTable, "$searchTable.item_id = $videoTableName.video_id", null);

            $searchParts = Engine_Api::_()->fields()->getSearchQuery('video', $customParams);
            foreach ($searchParts as $k => $v) {
                $select->where("`{$searchTable}`.{$k}", $v);
            }
        }

        if (isset($params['video_city']) && !empty($params['video_city']) && strstr(',', $params['video_city'])) {
            $video_city = explode(',', $params['video_city']);
            $params['video_city'] = $video_city[0];
        }

        if (isset($params['video_street']) && !empty($params['video_street']) || isset($params['video_city']) && !empty($params['video_city']) || isset($params['video_state']) && !empty($params['video_state']) || isset($params['video_country']) && !empty($params['video_country'])) {
            $select->join($locationTableName, "$videoTableName.video_id = $locationTableName.video_id   ", null);
        }

        if (isset($params['video_street']) && !empty($params['video_street'])) {
            $select->where($locationTableName . '.address   LIKE ? ', '%' . $params['video_street'] . '%');
        } if (isset($params['video_city']) && !empty($params['video_city'])) {
            $select->where($locationTableName . '.city = ?', $params['video_city']);
        } if (isset($params['video_state']) && !empty($params['video_state'])) {
            $select->where($locationTableName . '.state = ?', $params['video_state']);
        } if (isset($params['video_country']) && !empty($params['video_country'])) {
            $select->where($locationTableName . '.country = ?', $params['video_country']);
        }

        if (!isset($params['location']) && isset($params['locationSearch']) && !empty($params['locationSearch'])) {
            $params['location'] = $params['locationSearch'];

            if (isset($params['locationmilesSearch'])) {
                $params['locationmiles'] = $params['locationmilesSearch'];
            }
        }

        $addGroupBy = 1;
        if (!isset($params['location']) && (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitevideo');
            $locationTableName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationTableName, "$videoTableName.video_id = $locationTableName.video_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);

            if (isset($params['orderby']) && $params['orderby'] == "distance") {
                $select->order("distance");
            }

            $select->group("$videoTableName.video_id");
            $addGroupBy = 0;
        }

        if ((isset($params['location']) && !empty($params['location'])) || (!empty($params['Latitude']) && !empty($params['Longitude']))) {
            $enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.proximitysearch', 1);
            $longitude = 0;
            $latitude = 0;
            $detactLatLng = false;
            if (isset($params['location']) && $params['location']) {
                $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
                $detactLatLng = isset($cookieLocation['location']) && $cookieLocation['location'] != $params['location'];
            }
            if ((isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable))) || $detactLatLng) {

                if ($params['location']) {
                    $selectLocQuery = $locationTable->select()->where('location = ?', $params['location']);
                    $locationValue = $locationTable->fetchRow($selectLocQuery);
                }

                //check for zip code in location search.
                if ((empty($params['Latitude']) && empty($params['Longitude'])) || $detactLatLng) {
                    if (empty($locationValue)) {

                        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['location'], 'module' => 'Advanced Videos'));
                        if (!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
                            $latitude = $locationResults['latitude'];
                            $longitude = $locationResults['longitude'];
                        }
                    } else {
                        $latitude = (float) $locationValue->latitude;
                        $longitude = (float) $locationValue->longitude;
                    }
                } else {
                    $latitude = (float) $params['Latitude'];
                    $longitude = (float) $params['Longitude'];
                }
                if ($latitude && $latitude && isset($params['location']) && $params['location']) {
                    $seaocore_myLocationDetails['latitude'] = $latitude;
                    $seaocore_myLocationDetails['longitude'] = $longitude;
                    $seaocore_myLocationDetails['location'] = $params['location'];
                    $seaocore_myLocationDetails['locationmiles'] = $params['locationmiles'];

                    Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($seaocore_myLocationDetails);
                }
            }

            if (isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable)) && $latitude && $longitude) {
                $radius = $params['locationmiles'];

                $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.proximity.search.kilometer', 0);
                if (!empty($flage)) {
                    $radius = $radius * (0.621371192);
                }

                //$latitudeRadians = deg2rad($latitude);
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";
                $select->join($locationTableName, "$videoTableName.video_id = $locationTableName.video_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance"));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);

                if (isset($params['orderby']) && $params['orderby'] == "distance") {
                    $select->order("distance");
                }
            } else {
                $select->join($locationTableName, "$videoTableName.video_id = $locationTableName.video_id", null);
                $select->where("`{$locationTableName}`.formatted_address LIKE ? or `{$locationTableName}`.location LIKE ? or `{$locationTableName}`.city LIKE ? or `{$locationTableName}`.state LIKE ?", "%" . $params['location'] . "%");
            }
        }

        if (!empty($params['users'])) {
            $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
            $select->where($videoTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
        }

        if (empty($params['users']) && isset($params['view_view']) && $params['view_view'] == '1') {
            $select->where($videoTableName . '.owner_id = ?', '0');
        }
        if (isset($params['synchronized'])) {
            $select->where($videoTableName . '.synchronized = ?', $params['synchronized']);
        }
        if (!empty($params['category_id'])) {
            $select->where($videoTableName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($videoTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }
        if (!empty($params['subsubcategory_id'])) {
            $select->where($videoTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }
        //GET TAGMAP TABLE NAME
        $tagMapTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
        $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
        $isTagIdSearch = false;
        if (isset($params['tag_id']) && !empty($params['tag_id'])) {
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $videoTableName.video_id", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'))
                    ->where($tagMapTableName . '.resource_type = ?', 'video')
                    ->where($tagMapTableName . '.tag_id = ?', $params['tag_id']);
            $isTagIdSearch = true;
        }
        if (isset($params['search']) && !empty($params['search'])) {

            if ($isTagIdSearch == false) {
                $select
                        ->setIntegrityCheck(false)
                        ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $videoTableName.video_id and " . $tagMapTableName . ".resource_type = 'video'", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'));
            }
            $select->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id", array());
            $select->where("lower($videoTableName.title) LIKE ? OR lower($videoTableName.description) LIKE ? OR lower($tagName.text) LIKE ? ", '%' . strtolower($params['search']) . '%');
            $select->group("$videoTableName.video_id");
        }

        if (isset($params['channel_id']) && !empty($params['channel_id'])) {
            $videoMapTable = Engine_Api::_()->getDbtable('videomaps', 'sitevideo');
            $videoMapTableName = $videoMapTable->info('name');
            $select->joinRight($videoMapTableName, $videoMapTableName . ".video_id=" . $videoTableName . ".video_id", null);
            $select->where("$videoMapTableName.channel_id = ? ", $params['channel_id']);
            $ownerTableName = $videoMapTableName;
        }

        if (isset($params['videoType']) && !empty($params['videoType']) && $params['videoType'] != 'user' && $params['videoType'] != 'All') {
            $select->where("$videoTableName.parent_type = ?", $params['videoType']);
        } else if (isset($params['videoType']) && $params['videoType'] == 'user' && $params['videoType'] != 'All') {
            $select->where("$videoTableName.parent_type is NULL");
        }

        if (isset($params['parent_type']) && !empty($params['parent_type'])) {
            $select->where("$videoTableName.parent_type = ?", $params['parent_type']);
        }

        if (isset($params['parent_id']) && !empty($params['parent_id'])) {
            $select->where("$videoTableName.parent_id = ?", $params['parent_id']);
        }

        if (!empty($params['owner_id']))
            $select->where("$ownerTableName.owner_id = ?", $params['owner_id']);

        if (isset($params['type']) && $params['type'] == 'browse') {
            $select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
        }

        if (!isset($params['orderby']) && !isset($params['custom_order']))
            $select->order("$ownerTableName.creation_date DESC");

        if (isset($params['filter']) && !empty($params['filter'])) {
            switch ($params['filter']) {
                case "view_count":
                    $select->where($videoTableName . '.view_count>0');
                    break;
                case "comment_count":
                    $select->where($videoTableName . '.comment_count>0');
                    break;
                case 'like_count':
                    $select->where($videoTableName . '.like_count>0');
                    break;
                case 'rating':
                    $select->where($videoTableName . '.rating >0');
                    break;
                case 'favourite_count' :
                    $select->where($videoTableName . '.favourite_count >0');
                    break;
                case 'featured' :
                    $select->where($videoTableName . '.featured = 1');
                    break;
            }
        }

        if (isset($params['orderby']) && !empty($params['orderby']) && !isset($params['custom_order'])) {
            switch ($params['orderby']) {
                case "creation_date":
                    $select->order($ownerTableName . '.creation_date DESC');
                    break;
                case "creationDateAsc":
                    $select->order($ownerTableName . '.creation_date ASC');
                    break;
                case "modified_date":
                    $select->order($ownerTableName . '.modified_date DESC');
                    break;
                case "view_count":
                    $select->order($videoTableName . '.view_count DESC');
                    break;
                case "comment_count":
                    $select->order($videoTableName . '.comment_count DESC');
                    break;
                case 'like_count':
                    $select->order($videoTableName . '.like_count  DESC');
                    break;
                case 'rating':
                    $select->order($videoTableName . '.rating  DESC');
                    break;
                case 'favourite_count':
                    $select->order($videoTableName . '.favourite_count  DESC');
                    break;
                case 'title':
                    $select->order($videoTableName . '.title ASC');
                    break;
                case 'title_reverse':
                    $select->order($videoTableName . '.title DESC');
                    break;
                case 'featured':
                    $select->order($videoTableName . '.featured DESC');
                    $select->order($videoTableName . '.creation_date ASC');
                    break;
                case 'sponsored':
                    $select->order($videoTableName . '.sponsored DESC');
                    $select->order($videoTableName . '.creation_date ASC');
                    break;
                case 'sponsoredFeatured':
                    $select->order($videoTableName . '.sponsored DESC');
                    $select->order($videoTableName . '.featured DESC');
                    $select->order($videoTableName . '.creation_date ASC');
                    break;
                case 'featuredSponsored':
                    $select->order($videoTableName . '.featured DESC');
                    $select->order($videoTableName . '.sponsored DESC');
                    $select->order($videoTableName . '.creation_date ASC');
                    break;
                case 'random' :
                    $select->order('RAND()');
                    break;
                default:
                    $select->order($ownerTableName . '.modified_date DESC');
                    break;
            }
        }
        if (isset($params['custom_order'])) {
            $ord = implode($params['custom_order'], ',');
            $select->order("FIELD({$ord})");
        }
        if (isset($params['selectLimit']) && !empty($params['selectLimit']) && isset($params['start_index']) && $params['start_index'] >= 0) {
            $select->limit($params['selectLimit'], $params['start_index']);
        } else if (isset($params['selectLimit']) && !empty($params['selectLimit'])) {
            $select->limit($params['selectLimit']);
        }
        return $select;
    }

    public function getVideos($params) {
        $select = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoSelect($params);
        return $this->fetchAll($select);
    }

    public function getVideoPaginator(array $params, $customParams = array()) {
        $select = $this->getVideoSelect($params, $customParams);
        return Zend_Paginator::factory($select);
    }

    public function getUploadedVideoSelect(array $params) {
        $videoTableName = $this->info('name');
        $select = $this->select()->from($videoTableName, '*');
        $ownerTableName = $videoTableName;

        if (!empty($params['owner_id']))
            $select->where("$ownerTableName.owner_id = ?", $params['owner_id']);
        if (isset($params['order']) && $params['order'] == 'random') {
            $select->order('RAND()');
        } else {
            $select->order("$ownerTableName.creation_date DESC");
        }
        return $select;
    }

    public function getUploadedVideoPaginator(array $params) {
        return Zend_Paginator::factory($this->getUploadedVideoSelect($params));
    }

    public function getLikedVideoSelect(array $params) {

        $videoTableName = $this->info('name');
        $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
        $likesTableName = $likesTable->info('name');
        $select = $this->select()->from($videoTableName, '*');
        $select->joinLeft($likesTableName, $likesTableName . ".resource_id=" . $videoTableName . ".video_id", null);
        $select->where('resource_type = ?', 'video');
        if (!empty($params['owner_id']))
            $select->where('poster_id = ?', $params['owner_id']);
        if (isset($params['order']) && $params['order'] == 'random') {
            $select->order('RAND()');
        } else {
            $select->order("$likesTableName.creation_date DESC");
        }
        if (isset($params['excludeVideoOwner']) && !empty($params['excludeVideoOwner'])) {
            $select->where("$videoTableName.owner_id <> ?", $params['excludeVideoOwner']);
        }
        if (isset($params['search']) && !empty($params['search'])) {
            $select->where("lower($videoTableName.title) like ? ", '%' . strtolower($params['search']) . '%');
        }
        return $select;
    }

    public function getLikedVideoPaginator(array $params) {
        return Zend_Paginator::factory($this->getLikedVideoSelect($params));
    }

    public function getFavouriteVideoSelect(array $params) {

        $videoTableName = $this->info('name');
        $favouritesTable = Engine_Api::_()->getDbtable('favourites', 'seaocore');
        $favouritesTableName = $favouritesTable->info('name');
        $select = $this->select()->from($videoTableName, '*');
        $select->joinLeft($favouritesTableName, $favouritesTableName . ".resource_id=" . $videoTableName . ".video_id", null);
        $select->where('resource_type = ?', 'video');
        if (!empty($params['owner_id']))
            $select->where('poster_id = ?', $params['owner_id']);
        if (isset($params['order']) && $params['order'] == 'random') {
            $select->order('RAND()');
        } else {
            $select->order("$favouritesTableName.creation_date DESC");
        }
        if (isset($params['excludeVideoOwner']) && !empty($params['excludeVideoOwner'])) {
            $select->where("$videoTableName.owner_id <> ?", $params['excludeVideoOwner']);
        }
        if (isset($params['search']) && !empty($params['search'])) {
            $select->where("lower($videoTableName.title) like ? ", '%' . strtolower($params['search']) . '%');
        }
        return $select;
    }

    public function getFavouriteVideoPaginator(array $params) {
        return Zend_Paginator::factory($this->getFavouriteVideoSelect($params));
    }

    public function getRatedVideoSelect(array $params) {
        $videoTableName = $this->info('name');
        $ratingsTable = Engine_Api::_()->getDbtable('ratings', 'sitevideo');
        $ratingsTableName = $ratingsTable->info('name');
        $select = $this->select()->from($videoTableName, '*');
        $select->joinLeft($ratingsTableName, $ratingsTableName . ".resource_id=" . $videoTableName . ".video_id", null);
        $select->where('resource_type = ?', 'video');
        if (!empty($params['owner_id']))
            $select->where('user_id = ?', $params['owner_id']);

        if (isset($params['order']) && $params['order'] == 'random') {
            $select->order('RAND()');
        } else {
            $select->order("$ratingsTableName.rating_id DESC");
        }
        if (isset($params['excludeVideoOwner']) && !empty($params['excludeVideoOwner'])) {
            $select->where("$videoTableName.owner_id <> ?", $params['excludeVideoOwner']);
        }
        if (isset($params['search']) && !empty($params['search'])) {
            $select->where("lower($videoTableName.title) like ? ", '%' . strtolower($params['search']) . '%');
        }
        return $select;
    }

    public function getRatedVideoPaginator(array $params) {
        return Zend_Paginator::factory($this->getRatedVideoSelect($params));
    }

    //RETURN WATCHLATER RESULT SET IN PAGINATION
    public function getWatchlaterPaginator(array $params) {
        return Zend_Paginator::factory($this->getWatchlaterSelect($params));
    }

    //MAKE A QUERY TO SELECT THE WATCHLATER ON GIVEN PARAMETER
    public function getWatchlaterSelect(array $params) {

        $videosTableName = $this->info('name');
        $watchlatersTable = Engine_Api::_()->getDbtable('watchlaters', 'sitevideo');
        $watchlatersTableName = $watchlatersTable->info('name');
        $select = $this->select()->from($videosTableName, '*');
        $select->joinRight($watchlatersTableName, "$videosTableName.video_id=$watchlatersTableName.video_id", null);
        if (!empty($params['owner_id']))
            $select->where("$watchlatersTableName.owner_id = ?", $params['owner_id']);

        if (isset($params['watchlaterOrder']) && $params['watchlaterOrder'] == 'random') {
            $select->order('RAND()');
        } else {
            $select->order("$watchlatersTableName.creation_date");
        }
        if (isset($params['itemCountPerPage']) && !empty($params['itemCountPerPage'])) {
            $select->limit($params['itemCountPerPage']);
        }
        if (isset($params['search']) && !empty($params['search'])) {
            $select->where("lower($videosTableName.title) like ? ", '%' . strtolower($params['search']) . '%');
            $select->group("$watchlatersTableName.watchlater_id");
        }
        //RETURN QUERY
        return $select;
    }

    /**
     * Get paginator of videos
     *
     * @param array $params
     * @return Zend_Paginator;
     */
    Public function videoBySettings($params = array()) {
        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitevideo');

        $locationTableName = $locationTable->info('name');
        $videoTableName = $this->info('name');
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        //MAKE TIMING STRING
        $sqlTimeStr = '';
        $interval = '';
        if (isset($params['interval']) && !empty($params['interval'])) {
            $interval = $params['interval'];
            $current_time = date("Y-m-d H:i:s");
            if ($interval == 'week') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
            } elseif ($interval == 'month') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
            }
        }

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($videoTableName, array('main_channel_id', 'video_id', 'title', 'file_id', 'owner_id', 'view_count', 'comment_count', 'rating', 'seao_locationid', 'location', 'like_count', 'description', 'creation_date', 'photo_id', 'duration', 'featured', 'sponsored'));
        if (isset($params['owner_id']) && !empty($params['owner_id'])) {
            $select->where($videoTableName . '.owner_id = ?', $params['owner_id'])
                    ->order($videoTableName . ".video_id DESC");
        }
        if (isset($params['video_ids']) && !empty($params['video_ids'])) {
            $select->where($videoTableName . '.video_id IN(?)', $params['video_ids']);
        }
        if (isset($params['video_city']) && !empty($params['video_city']) && strstr(',', $params['video_city'])) {
            $video_city = explode(',', $params['video_city']);
            $params['video_city'] = $video_city[0];
        }

        if (isset($params['video_street']) && !empty($params['video_street']) || isset($params['video_city']) && !empty($params['video_city']) || isset($params['video_state']) && !empty($params['video_state']) || isset($params['video_country']) && !empty($params['video_country'])) {
            $select->join($locationTableName, "$videoTableName.video_id = $locationTableName.video_id   ", null);
        }

        if (isset($params['video_street']) && !empty($params['video_street'])) {
            $select->where($locationTableName . '.address   LIKE ? ', '%' . $params['video_street'] . '%');
        } if (isset($params['video_city']) && !empty($params['video_city'])) {
            $select->where($locationTableName . '.city = ?', $params['video_city']);
        } if (isset($params['video_state']) && !empty($params['video_state'])) {
            $select->where($locationTableName . '.state = ?', $params['video_state']);
        } if (isset($params['video_country']) && !empty($params['video_country'])) {
            $select->where($locationTableName . '.country = ?', $params['video_country']);
        }

        if (!isset($params['location']) && isset($params['locationSearch']) && !empty($params['locationSearch'])) {
            $params['location'] = $params['locationSearch'];

            if (isset($params['locationmilesSearch'])) {
                $params['locationmiles'] = $params['locationmilesSearch'];
            }
        }

        $addGroupBy = 1;
        if (!isset($params['location']) && (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitevideo');
            $locationTableName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationTableName, "$videoTableName.video_id = $locationTableName.video_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);

            if (isset($params['orderby']) && $params['orderby'] == "distance") {
                $select->order("distance");
            }

            $select->group("$videoTableName.video_id");
            $addGroupBy = 0;
        }

        if ((isset($params['location']) && !empty($params['location'])) || (!empty($params['Latitude']) && !empty($params['Longitude']))) {
            $enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.proximitysearch', 1);
            $longitude = 0;
            $latitude = 0;
            $detactLatLng = false;
            if (isset($params['location']) && $params['location']) {
                $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
                $detactLatLng = isset($cookieLocation['location']) && $cookieLocation['location'] != $params['location'];
            }
            if ((isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable))) || $detactLatLng) {

                if ($params['location']) {
                    $selectLocQuery = $locationTable->select()->where('location = ?', $params['location']);
                    $locationValue = $locationTable->fetchRow($selectLocQuery);
                }

                //check for zip code in location search.
                if ((empty($params['Latitude']) && empty($params['Longitude'])) || $detactLatLng) {
                    if (empty($locationValue)) {

                        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['location'], 'module' => 'Advanced Videos'));
                        if (!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
                            $latitude = $locationResults['latitude'];
                            $longitude = $locationResults['longitude'];
                        }
                    } else {
                        $latitude = (float) $locationValue->latitude;
                        $longitude = (float) $locationValue->longitude;
                    }
                } else {
                    $latitude = (float) $params['Latitude'];
                    $longitude = (float) $params['Longitude'];
                }
                if ($latitude && $latitude && isset($params['location']) && $params['location']) {
                    $seaocore_myLocationDetails['latitude'] = $latitude;
                    $seaocore_myLocationDetails['longitude'] = $longitude;
                    $seaocore_myLocationDetails['location'] = $params['location'];
                    $seaocore_myLocationDetails['locationmiles'] = $params['locationmiles'];

                    Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($seaocore_myLocationDetails);
                }
            }

            if (isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable)) && $latitude && $longitude) {
                $radius = $params['locationmiles'];

                $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.proximity.search.kilometer', 0);
                if (!empty($flage)) {
                    $radius = $radius * (0.621371192);
                }

                //$latitudeRadians = deg2rad($latitude);
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";
                $select->join($locationTableName, "$videoTableName.video_id = $locationTableName.video_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance"));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);

                if (isset($params['orderby']) && $params['orderby'] == "distance") {
                    $select->order("distance");
                }
            } else {
                $select->join($locationTableName, "$videoTableName.video_id = $locationTableName.video_id", null);
                $select->where("`{$locationTableName}`.formatted_address LIKE ? or `{$locationTableName}`.location LIKE ? or `{$locationTableName}`.city LIKE ? or `{$locationTableName}`.state LIKE ?", "%" . $params['location'] . "%");
            }
        }

        if (!empty($params['category_id'])) {
            $select->where($videoTableName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($videoTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }
        if (!empty($params['subsubcategory_id'])) {
            $select->where($videoTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }
        if (isset($params['orderby']) && !empty($params['orderby'])) {
            switch ($params['orderby']) {
                case 'modified_date':
                    $select->order($videoTableName . '.modified_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($videoTableName . "$sqlTimeStr");
                    }
                    break;
                case 'creation_date':
                    $select->order($videoTableName . '.creation_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($videoTableName . "$sqlTimeStr");
                    }
                    break;
                case 'like_count':
                case 'likesvideos':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
                        $popularityTableName = $popularityTable->info('name');

                        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $videoTableName . '.video_id', array("COUNT($videoTableName.video_id) as total_count"))
                                ->where($popularityTableName . '.resource_type = ?', 'video')
                                ->order("total_count DESC");

                        $select->where($popularityTableName . "$sqlTimeStr");
                    } else {
                        $select->order($videoTableName . '.like_count DESC');
                    }

                    break;
                case 'view_count':
                    $select->order($videoTableName . '.view_count DESC');
                    break;
                case 'comment_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
                        $popularityTableName = $popularityTable->info('name');

                        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $videoTableName . '.video_id', array("COUNT($videoTableName.video_id) as total_count"))
                                ->where($popularityTableName . '.resource_type = ?', 'video')
                                ->order("total_count DESC");


                        $select->where($popularityTableName . "$sqlTimeStr");
                    } else {
                        $select->order($videoTableName . '.comment_count DESC');
                    }
                    break;
                case 'rating':
                    $select->order($videoTableName . '.rating DESC');
                    break;
                case 'featured':
                    $select->where($videoTableName . '.featured = ?', 1);
                    if (isset($params['orderBy']) && $params['orderBy'] == 'creation_date') {
                        $select->order('creation_date DESC');
                    } else {
                        $select->order('Rand()');
                    }
                    break;
                case 'random':
                    $select->order('Rand()');
                    break;
            }
        }

        if (isset($params['orderby']) && !empty($params['orderby']) && $params['orderby'] != 'creation_date' && $params['orderby'] != 'modified_date' && $params['orderby'] != 'random' && $params['orderby'] != 'featured') {
            $select->order($videoTableName . ".video_id DESC");
        }
        if (isset($params['showVideo']) && !empty($params['showVideo'])) {
            switch ($params['showVideo']) {
                case 'featured' :
                    $select->where("$videoTableName.featured = ?", 1);
                    break;
                case 'sponsored' :
                    $select->where($videoTableName . '.sponsored = ?', '1');
                    break;
                case 'featuredSponsored' :
                    $select->where("$videoTableName.sponsored = 1 OR $videoTableName.featured = 1");
                    break;
            }
        }
        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where($videoTableName . '.featured = ?', 1);
        }
        if (isset($params['videoType']) && !empty($params['videoType']) && $params['videoType'] != 'user' && $params['videoType'] != 'All') {
            $select->where("$videoTableName.parent_type = ?", $params['videoType']);
        } else if (isset($params['videoType']) && $params['videoType'] == 'user' && $params['videoType'] != 'All') {
            $select->where("$videoTableName.parent_type is NULL");
        }

        if (isset($params['parent_type']) && !empty($params['parent_type'])) {
            $select->where("$videoTableName.parent_type = ?", $params['parent_type']);
        }

        if (isset($params['parent_id']) && !empty($params['parent_id'])) {
            $select->where("$videoTableName.parent_id = ?", $params['parent_id']);
        }

        if (!empty($params['users'])) {
            $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
            $select->where($videoTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
        }

        if (empty($params['users']) && isset($params['view_view']) && $params['view_view'] == '1') {
            $select->where($videoTableName . '.owner_id = ?', '0');
        }

        //Start Network work
        $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
        //End Network work

        $select->group("$videoTableName.video_id");


        if (isset($params['limit']) && !empty($params['limit'])) {
            $limit = $params['limit'];
        }

        if (isset($params['start_index']) && $params['start_index'] >= 0) {
            $select = $select->limit($limit, $params['start_index']);
            return $this->fetchAll($select);
        }

        return Zend_Paginator::factory($select);
    }

    /**
     * get the video next/ previoues
     */
    public function getVideo($collectible, $params = array(), $direction) {

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
        $rowset = $this->fetchAll($select);
        if (null === $rowset) {
            // @todo throw?
            return null;
        }
        $row = $rowset->current();
        return Engine_Api::_()->getItem('sitevideo_video', $row->video_id);
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

        $rows = $this->fetchAll($select);
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

    public function getCollectibleSql($collectible, $params = array()) {

        $parentTable = Engine_Api::_()->getItemTable('sitevideo_channel');
        $parentName = $parentTable->info('name');

        $tableName = $this->info('name');

        $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
        $locationName = $locationTable->info('name');

        $col = current($this->info("primary"));
        $select = $this->select()
                ->from($tableName, $col);
        if (!Engine_Api::_()->sitevideo()->isLessThan417ChannelModule()) {
            $select->join($parentName, $parentName . '.channel_id=' . $tableName . '.main_channel_id', null);
        } else {
            $select->join($parentName, $parentName . '.channel_id=' . $tableName . '.collection_id', null);
        }

        $select->where('search = ?', true)
                ->setIntegrityCheck(false);

        //MAKE TIMING STRING
        $sqlTimeStr = '';
        $interval = '';
        if (isset($params['interval']) && !empty($params['interval'])) {
            $interval = $params['interval'];
            $current_time = date("Y-m-d H:i:s");
            if ($interval == 'week') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
            } elseif ($interval == 'month') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
            }
        }

        if ((isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationName, "$tableName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);
//      $select->order("distance");
//      $select->group("$tableName.video_id");
        }

        $type = $params['type'];

        if ($params['type'] == 'strip_view') {
            $type = 'view_count';
        } elseif ($params['type'] == 'strip_like') {
            $type = 'like_count';
        } elseif ($params['type'] == 'strip_comment') {
            $type = 'comment_count';
        } elseif ($params['type'] == 'strip_rating') {
            $type = 'rating';
        } elseif ($params['type'] == 'strip_modified') {
            $type = 'modified_date';
        } elseif ($params['type'] == 'strip_creation') {
            $type = 'creation_date';
        } elseif ($params['type'] == 'strip_random') {
            $type = 'random';
        }

        switch ($type) {
            case 'featured':
                $select
                        ->where($tableName . '.featured = ?', 1);
                break;
            case 'tagged':

                if (!isset($params['owner_id']) || empty($params['owner_id']))
                    break;
                $owner_id = $params['owner_id'];

                $this->_table = $tableTagmaps = Engine_Api::_()->getDbtable('tagMaps', 'core');
                $tableTagmapsName = $tableTagmaps->info('name');

                $select = $tableTagmaps->select()
                        ->setIntegrityCheck(false)
                        ->from($tableTagmapsName, array('resource_id', 'creation_date'))
                        ->join($tableName, $tableTagmapsName . '.resource_id=' . $tableName . '.video_id', array('video_id'));

                if (!Engine_Api::_()->sitevideo()->isLessThan417ChannelModule()) {
                    $select->join($parentName, $parentName . '.channel_id=' . $tableName . '.main_channel_id', array());
                } else {
                    $select->join($parentName, $parentName . '.channel_id=' . $tableName . '.collection_id', array());
                }
                $select->where($tableTagmapsName . '.resource_type = ?', "sitevideo_video")
                        ->where($tableName . '.	owner_id <> ?', $owner_id)
                        ->where($parentName . '.	owner_id <> ?', $owner_id)
                        ->where($tableTagmapsName . '.tag_id = ?', $owner_id)
                        ->where($tableTagmapsName . '.tag_type = ?', "user")
                        ->where('search = ?', true)
                        ->order($tableTagmapsName . '.creation_date DESC');

                if (!empty($params['category_id'])) {
                    $select->where($parentName . '.category_id = ?', $params['category_id']);
                }
                if (!empty($params['subcategory_id'])) {
                    $select->where($parentName . '.subcategory_id = ?', $params['subcategory_id']);
                }
                if (!empty($params['subsubcategory_id'])) {
                    $select->where($parentName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
                }


                return $select;
                break;
            case 'random':
                $select->order('Rand()');
                break;
            case 'yourvideos':
                $select->where($parentName . '.owner_id =? ', $params['owner_id']);
                break;
            case 'creation_date':
                $select->order($tableName . '.creation_date DESC');
                if (($interval == 'week') || ($interval == 'month')) {
                    $select->where($tableName . "$sqlTimeStr");
                }
                break;
            case 'like_count':
            case 'likesvideos':
                if (($interval == 'week') || ($interval == 'month')) {
                    $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
                    $popularityTableName = $popularityTable->info('name');

                    $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $tableName . '.video_id', array("COUNT($tableName.video_id) as total_count"))
                            ->where($popularityTableName . '.resource_type = ?', 'sitevideo_video')
                            ->order("total_count DESC");

                    $select->where($popularityTableName . "$sqlTimeStr");
                } else {
                    $select->order($tableName . '.like_count DESC');
                }

                break;
            case 'comment_count':
                if (($interval == 'week') || ($interval == 'month')) {
                    $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
                    $popularityTableName = $popularityTable->info('name');

                    $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $tableName . '.video_id', array("COUNT($tableName.video_id) as total_count"))
                            ->where($popularityTableName . '.resource_type = ?', 'sitevideo_video')
                            ->order("total_count DESC");

                    $select->where($popularityTableName . "$sqlTimeStr");
                } else {
                    $select->order($tableName . '.comment_count DESC');
                }
                break;
            default :
                $select->order($tableName . ".$type DESC");
                break;
        }

        if (!empty($params['category_id'])) {
            $select->where($parentName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($parentName . '.subcategory_id = ?', $params['subcategory_id']);
        }
        if (!empty($params['subsubcategory_id'])) {
            $select->where($parentName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }
        if (!empty($params['featured'])) {
            $select->where($tableName . '.featured = ?', 1);
        }

        if ($type != "modified_date" && $type != "creation_date" && $type != "random")
            $select->order($tableName . '.video_id DESC');

        $select->group("$tableName.video_id");

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.specialchannel', 0))
            $select->where($parentName . '.type IS NULL');

        $select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));

        return $select;
    }

    /**
     * Get total channels of particular category / subcategoty 
     *
     * @param array $params
     * @return int $totalVideos;
     */
    public function getVideosCount($params = array()) {

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('COUNT(*) AS count'));

        if (isset($params['foruser']) && !empty($params['foruser'])) {
            $select->where('search = ?', 1);
        }
        if (isset($params['synchronized'])) {
            $select->where('synchronized = ?', $params['synchronized']);
        }
        if (isset($params['type']) && !empty($params['type'])) {
            $select->where('type in (?)', $params['type']);
        }
        if (!empty($params['columnName']) && !empty($params['category_id'])) {
            $column_name = $params['columnName'];
            $select->where("$column_name = ?", $params['category_id']);
        }

        $totalVideos = $select->query()->fetchColumn();

        //RETURN VIDEO COUNT
        return $totalVideos;
    }

    public function getVideosCountByParentType($params = array()) {

        //MAKE QUERY
        $select = $this->select()
            ->from($this->info('name'), array('COUNT(*) AS count'));

        if (isset($params['parent_type']) && !empty($params['parent_type'])) {
            $select->where('parent_type = ?', $params['parent_type']);
        }

        if (isset($params['parent_id']) && !empty($params['parent_id'])) {
            $select->where('parent_id = ?', $params['parent_id']);
        }

        $totalVideos = $select->query()->fetchColumn();

        //RETURN VIDEO COUNT
        return $totalVideos;
    }

    /**
     * Return channelids which have this category and this mapping
     *
     * @param int category_id
     * @return array $channelIds
     */
    public function getMappedSitevideo($category_id) {

        //RETURN IF CATEGORY ID IS NULL
        if (empty($category_id)) {
            return null;
        }

        //MAKE QUERY
        $videoIds = $this->select()
                ->from($this->info('name'), 'video_id')
                ->where("category_id = $category_id OR subcategory_id = $category_id OR subsubcategory_id = $category_id")
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        return $videoIds;
    }

    /**
     * Return channels which have this category and this mapping
     *
     * @param int category_id
     * @return Zend_Db_Table_Select
     */
    public function getCategoryList($params = array()) {

        //RETURN IF CATEGORY ID IS NULL
        if (empty($params['category_id'])) {
            return null;
        }

        //MAKE QUERY
        $categoty_type = $params['categoty_type'];
        return $this->select()
                        ->from($this->info('name'), 'main_channel_id')
                        ->where("$categoty_type = ?", $params['category_id'])
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
    }

    /**
     * Get pages to add as item of the day
     * @param string $title : search text
     * @param int $limit : result limit
     */
    public function getDayItems($title, $limit = 10) {

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('video_id', 'owner_id', 'title', 'photo_id', 'main_channel_id'))
                ->where('lower(title)  LIKE ? ', '%' . strtolower($title) . '%')
                ->where('search = ?', '1')
                ->order('title ASC')
                ->limit($limit);

        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    public function addPrivacyVideosSQl($select, $tableName = null) {

        $privacybase = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.privacybase', 0);

        if (empty($privacybase))
            return $select;

        $column = $tableName ? "$tableName.video_id" : "video_id";

        return $select->where("$column IN(?)", $this->getOnlyViewableVideosId());
    }

    public function getOnlyViewableVideosId() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $channels_ids = array();
        $cache = Zend_Registry::get('Zend_Cache');
        $cacheName = 'video_ids_user_id_' . $viewer->getIdentity();

        $data = APPLICATION_ENV == 'development' ? ( Zend_Registry::isRegistered($cacheName) ? Zend_Registry::get($cacheName) : null ) : $cache->load($cacheName);
        if ($data && is_array($data)) {
            $video_ids = $data;
        } else {
            set_time_limit(0);
            $table = Engine_Api::_()->getItemTable('sitevideo_video');
            $video_select = $table->select()
                    ->where('search = ?', true)
                    ->order('video_id DESC');

            // Create new array filtering out private channels
            $i = 0;
            foreach ($video_select->getTable()->fetchAll($video_select) as $video) {
                if ($video->isOwner($viewer) || Engine_Api::_()->authorization()->isAllowed($video, $viewer, 'view')) {
                    $video_ids[$i++] = $video->video_id;
                }
            }

            // Try to save to cache
            if (empty($video_ids))
                $video_ids = array(0);

            if (APPLICATION_ENV == 'development') {
                Zend_Registry::set($cacheName, $video_ids);
            } else {
                $cache->save($video_ids, $cacheName);
            }
        }

        return $video_ids;
    }

    public function getNetworkBaseSql($select, $params = array()) {
        $select = $this->addPrivacyVideosSQl($select, $this->info('name'));
        if (empty($select))
            return;

        $sitevideo_tableName = $this->info('name');

        //START NETWORK WORK
        $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.network', 0);

        if (!empty($enableNetwork) || (isset($params['browse_network']) && !empty($params['browse_network']))) {

            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer = Engine_Api::_()->user()->getViewer();
            $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
            if (!Zend_Registry::isRegistered('viewerNetworksIdsVideo')) {
                $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);
                Zend_Registry::set('viewerNetworksIdsVideo', $viewerNetworkIds);
            } else {
                $viewerNetworkIds = Zend_Registry::get('viewerNetworksIdsVideo');
            }
            if (!Engine_Api::_()->sitevideo()->videoBaseNetworkEnable()) {
                $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

                if (!empty($viewerNetwork)) {
                    if (isset($params['setIntegrity']) && !empty($params['setIntegrity'])) {
                        $select->setIntegrityCheck(false)
                                ->from($sitevideo_tableName);
                    }
                    $networkMembershipName = $networkMembershipTable->info('name');
                    $select
                            ->join($networkMembershipName, "`{$sitevideo_tableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                            ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                            ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity());
                    if (!isset($params['not_groupBy']) || empty($params['not_groupBy'])) {
                        $select->group($sitevideo_tableName . ".video_id");
                    }
                    if (isset($params['extension_group']) && !empty($params['extension_group'])) {
                        $select->group($params['extension_group']);
                    }
                }
            } else {
                $viewerNetwork = $networkMembershipTable->getMembershipsOfInfo($viewer);
                $str = array();
                $columnName = "`{$sitevideo_tableName}`.networks_privacy";
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
        }
        return $select;
    }

    public function createDefaultVideos() {
        $select = $this->select()->from($this->info('name'), array('COUNT(*) AS count'));
        $totalCount = $select->query()->fetchColumn();
        if ($totalCount <= 25) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $viewer = Engine_Api::_()->user()->getViewer();
            $ownerId = $viewer->getIdentity();
            $ownerType = $viewer->getType();
            $channel_category_table = Engine_Api::_()->getItemTable('sitevideo_video_category');
            $category = $channel_category_table->fetchRow(array('category_name=?' => 'Others'));
            $categoryId = 0;
            if ($category)
                $categoryId = $category->category_id;

            $videos_table = Engine_Api::_()->getDbTable('videos', 'sitevideo');
            $max_video_id = $videos_table->select()
                    ->from($videos_table->info('name'), 'max(video_id) as max_video_id')
                    ->query()
                    ->fetchColumn();

            $db->query("
                INSERT IGNORE INTO `engine4_video_videos`(`title`, `description`, `search`, `owner_type`, `owner_id`, `creation_date`, `modified_date`, `type`, `category_id`, `status`, `featured`,`sponsored`, `code`, `duration`,`main_channel_id`)
                VALUES
                ('NEW 2015 Peugeot 508 Sedan','The Peugeot 508 is a large family car launched in 2010 by French automaker Peugeot, and followed by the 508 SW, an estate version, in March 2011.It replaces the Peugeot 407, as well as the larger Peugeot 607, for which no more direct replacement is scheduled. It shares its platform and most engine options with the second-generation Citroën C5: the two cars are produced alongside one another at the companys Rennes Plant, and in Wuhan, China for sales inside China.The Peugeot 508 got several international awards like the Car of the Year 2011 in Spain (award in 2012), Next Green Car Best Large Family car 2011 for  being spacious and well equipped, also noting that it represented excellent build quality and has the best fuel economy in its class  (Peugeot 508 1.6 e-HDi 109g CO2/km) or also Auto Zeitung Best imported family car 2011',1,'$ownerType',$ownerId,now(),now(),'youtube',$categoryId,1,1,1,'aM6iprJa-oc',85,24),
                ('The Joy of Matte Painting','We get old-school artsy, Corey channels his inner artiste, and we uncover matte paintings inner workings.',1,'$ownerType',$ownerId,now(),now(),'vimeo',$categoryId,1,1,1,'153413474',292,197),
                ('EAT','3 guys, 44 days, 11 countries, 18 flights, 38 thousand miles, an exploding volcano, 2 cameras and almost a terabyte of footage... all to turn 3 ambitious linear concepts based on movement, learning and food ....into 3 beautiful and hopefully compelling short films',1,'$ownerType',$ownerId,now(),now(),'vimeo',$categoryId,1,1,1,'27243869',61,194),
                ('Gold Poker Tour','Gold Poker Tour is a fictitious jingle mixing the poker and luxury codes.',1,'$ownerType',$ownerId,now(),now(),'vimeo',$categoryId,1,1,1,'82883181',5,108),
                ('SHARKS','Andy Brandy Casagrande IV aka (ABC) is an Emmy Award winning wildlife cinematographer, field producer & television presenter specializing in blue-chip wildlife documentaries around the world. From King Cobras & Killer Whales to Great Whites Sharks & Polar Bears, Andys innovative cinematography & unorthodox camera techniques are helping revolutionize the way the world sees & perceives wildlife. From super-slow motion & thermal-infrared to night-vision & remote-controlled spy-cams, Andy shoots with the most advanced camera technologies on the planet and continues to push the boundaries of wildlife filmmaking to shed new light & perspective into the hidden lives of the planets most feared & misunderstood predators.',1,'$ownerType',$ownerId,now(),now(),'vimeo',$categoryId,1,1,1,'60475481',32,166),
                ('DIY Spider Trax Dolly Track','This is a track that I made for my spider trax dolly. You can attach it to a tri-pod or K-pod and it can be made with simple tools like a few wrenches and a saw.',1,'$ownerType',$ownerId,now(),now(),'vimeo',$categoryId,1,1,1,'13949623',171,176),
                ('How to Spatchcock a Chicken','This is a great way to reduce the cooking time of a regular roast chicken. It is the perfect family dinner or works well for dinner parties.',1,'$ownerType',$ownerId,now(),now(),'vimeo',$categoryId,1,1,1,'138061328',83,180),
                ('Lofoten Eternal Lights','An amazing 14 days touring the Lofoten islands, fromTromson to Reine, more than 2,300 km, a truly unforgettable life experience.An adventure shared with my great friend Jesus Hermana. Together, we enjoyed many of the beautiful spots of Lofoten and its magic northern lights, the Aurora Borealis.I virtually planned the entire trip with the help of Photopills app, which is a crucial tool for me. It helps me predict everything, the movement of the Sun, the Moon and the Milky Way, together with many other useful tools that make planning and shooting my ideas much easier.For the movement of the camera, I used the Mslider dolly. A unique tool to produce great camera movements in a simple way.The equipment used was composed by 3 cameras, the Canon 5D Mark III, the Canon 6D and the Canon 5Dsr, together with several Canon lenses and the Mslider dolly.Special thanks to Canon Spain for lending me the superb Canon 5Dsr. I really enjoyed its high performance and quality.',1,'$ownerType',$ownerId,now(),now(),'vimeo',$categoryId,1,1,1,'154819915',202,229),
                ('Tyler Frasca Santa Cruz','We recently made it down to the loam capital of California for a rip with Tyler Frasca. The return of true winter to Santa Cruz has made for stellar riding conditions.',1,'$ownerType',$ownerId,now(),now(),'vimeo',$categoryId,1,1,1,'152338973',88,231)
               ");
            $select = $videos_table->select()
                    ->from($videos_table->info('name'), 'video_id');

            if ($max_video_id) {
                $select->where('video_id > (?)', $max_video_id);
            }

            $select->order('video_id ASC')
                    ->limit(10);
            $videos = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            if (!empty($videos)) {

                $db->query("INSERT IGNORE INTO `engine4_sitevideo_videootherinfo` (`video_id`,`tagline1`, `tagline2`, `tagline_description`, `url`) VALUES
('$videos[0]', 'By French automaker Peugeot', ' Launched in 2010', 'The Peugeot 508 is a large family car launched in 2010 by French automaker Peugeot, and followed by the 508 SW, an estate version, in March 2011.It replaces the Peugeot 407, as well as the larger Peugeot 607.', NULL),
('$videos[1]', 'First matte painting shot was made in 1907', 'First digital matte shot was created by painter Chris Evans', 'A matte painting is a painted representation of a landscape, set, or distant location that allows filmmakers to create the illusion of an environment that is nonexistent in real life or would otherwise be too expensive or impossible to build or visit. Historically, matte painters and film technicians have used various techniques to combine a matte-painted image with live-action footage. ', NULL),
('$videos[2]', 'Owner‎: ‎Niall & Faith MacArthur', 'Founded‎: ‎1996', '3 guys, 44 days, 11 countries, 18 flights, 38 thousand miles, an exploding volcano, 2 cameras and almost a terabyte of footage... all to turn 3 ambitious linear concepts based on movement, learning and food ....into 3 beautiful and hopefully compelling short films.', NULL),
('$videos[3]', 'Casino: The Mirage, Paradise, Nevada ', 'Total Prize Pool: $3,074,900', 'Gold Poker Tour is a fictitious jingle mixing the poker and luxury codes.', NULL),
('$videos[4]', ' Field Producer: Andy Brandy Casagrande IV', 'Wildlife Documentaries', 'Andy Brandy Casagrande IV aka (ABC) is an Emmy Award winning wildlife cinematographer, field producer & television presenter specializing in blue-chip wildlife documentaries around the world. From King Cobras & Killer Whales to Great Whites Sharks & Polar Bears, Andy''s innovative cinematography & unorthodox camera techniques are helping revolutionize the way the world sees & perceives wildlife. From super-slow motion & thermal-infrared to night-vision & remote-controlled spy-cams, Andy shoots with the most advanced camera technologies on the planet and continues to push the boundaries of wildlife filmmaking to shed new light & perspective into the hidden lives of the planet''s most feared & misunderstood predators.', NULL),
('$videos[5]', 'By Jarrod ', 'Just Basl Productions', 'This is a track that I made for my spider trax dolly. You can attach it to a tri-pod or K-pod and it can be made with simple tools like a few wrenches and a saw.', NULL),
('$videos[6]', 'By Jessica', 'Directed by Jeremy Keith', 'This is a great way to reduce the cooking time of a regular roast chicken. It is the perfect family dinner or works well for dinner parties.\n', NULL),
('$videos[7]', 'A short film', 'Finding motivation', 'This short video is made to fight the feeling of frustration that comes over when one see a work finished. ', NULL),
('$videos[8]', 'In Aurora Borealis', 'Used the Mslider dolly', 'The movement of the Sun, the Moon and the Milky Way, together with many other useful tools that make planning and shooting much easier.', NULL),
('$videos[9]', 'From Kitsbow', 'State California', 'Santa Cruz is known for its moderate climate, the natural beauty of its coastline, redwood forests, alternative community lifestyles, and socially liberal leanings.', NULL);");
            }

            foreach ($videos as $video) {
                Engine_Api::_()->sitevideo()->autoLike($video, 'video');
            }

            $arr = array();
            $arr[24] = 'Motors-TV';
            $arr[71] = 'comedy-central';
            $arr[108] = 'poker';
            $arr[166] = 'nat-geo-wild';
            $arr[176] = 'diy-network';
            $arr[180] = 'health-flavors';
            $arr[194] = 'hgtv';
            $arr[197] = 'ovation';
            $arr[229] = 'cameras-and-techniques';
            $arr[231] = 'nature';

            $select = $this->select()->from($this->info('name'), '*');
            if ($max_video_id) {
                $select->where('video_id > (?)', $max_video_id);
            }
            $select
                    ->limit(10);
            $videos = $this->fetchAll($select);

            foreach ($videos as $video) {

                // CREATE AUTH STUFF HERE
                $values = array();
                $values['auth_view'] = 'everyone';
                $values['auth_comment'] = 'everyone';
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
                }

                $thumbnail = $this->handleThumbnail($video->type, $video->code);
                $temVideo = $video->saveVideoThumbnail($thumbnail);
                if (isset($arr[$video->main_channel_id])) {
                    $channel_url = $arr[$video->main_channel_id];
                    $channel_id = Engine_Api::_()->sitevideo()->getChannelId($channel_url);
                    $video->main_channel_id = $channel_id;
                } else
                    $video->main_channel_id = null;

                $video->synchronized = 1;
                $video->save();
                $video->addVideomap();
            }
        } else {
            $select = $this->select()->from($this->info('name'), '*')->order('Rand()')->limit(10);
            $videos = $this->fetchAll($select);
            foreach ($videos as $video) {
                $video->featured = 1;
                $video->sponsored = 1;
                $video->save();
            }
        }
    }

    // handles thumbnails
    public function handleThumbnail($type, $code = null) {
        switch ($type) {

            //youtube
            case "youtube":
                $thumbnail = "";
                $thumbnailSize = array('maxresdefault', 'sddefault', 'hqdefault', 'mqdefault', 'default');
                foreach ($thumbnailSize as $size) {
                    $thumbnailUrl = "https://i.ytimg.com/vi/$code/$size.jpg";
                    $data = @file_get_contents($thumbnailUrl);
                    if ($data && is_string($data)) {
                        $thumbnail = $thumbnailUrl;
                        break;
                    }
                }
                return $thumbnail;
            //vimeo
            case "vimeo":
                $thumbnail = "";
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                if (isset($data->video->thumbnail_large))
                    $thumbnail = $data->video->thumbnail_large;
                else if (isset($data->video->thumbnail_medium))
                    $thumbnail = $data->video->thumbnail_medium;
                else if (isset($data->video->thumbnail_small))
                    $thumbnail = $data->video->thumbnail_small;

                return $thumbnail;
            //dailymotion
            case "dailymotion":
                $thumbnail = "";
                $thumbnailUrl = 'https://api.dailymotion.com/video/' . $code . '?fields=thumbnail_small_url,thumbnail_large_url,thumbnail_medium_url';
                $json_thumbnail = file_get_contents($thumbnailUrl);
                if ($json_thumbnail) {
                    $thumbnails = json_decode($json_thumbnail);
                    if (isset($thumbnails->thumbnail_large_url))
                        $thumbnail = $thumbnails->thumbnail_large_url;
                    else if (isset($thumbnails->thumbnail_medium_url)) {
                        $thumbnail = $thumbnails->thumbnail_medium_url;
                    } else if (isset($thumbnails->thumbnail_small_url)) {
                        $thumbnail = $thumbnails->thumbnail_small_url;
                    }
                }
                return $thumbnail;
        }
    }

}
