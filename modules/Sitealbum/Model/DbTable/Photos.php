<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photos.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Model_DbTable_Photos extends Core_Model_Item_DbTable_Abstract {

    protected $_name = 'album_photos';
    protected $_rowClass = 'Sitealbum_Model_Photo';

    public function getPhotoSelect(array $params) {

        $select = $this->select();

        if (!empty($params['album']) && $params['album'] instanceof Sitealbum_Model_Album) {
            $select->where('album_id = ?', $params['album']->getIdentity());
        } else if (!empty($params['album_id']) && is_numeric($params['album_id'])) {
            $select->where('album_id = ?', $params['album_id']);
        }

        if (!isset($params['order']) || empty($params['order'])) {
            $select->order('order ASC');
        } else if (is_string($params['order'])) {
            $select->order($params['order']);
        }

        return $select;
    }

    public function getPhotoPaginator(array $params) {
        return Zend_Paginator::factory($this->getPhotoSelect($params));
    }

    /**
     * Get paginator of photos
     *
     * @param array $params
     * @return Zend_Paginator;
     */
    Public function photoBySettings($params = array()) {

        $parentTable = Engine_Api::_()->getItemTable('album');
        $parentTableName = $parentTable->info('name');

        $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
        $locationName = $locationTable->info('name');

        $photoTableName = $this->info('name');
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
                ->from($photoTableName, array('album_id', 'photo_id', 'title', 'file_id', 'owner_id', 'view_count', 'comment_count', 'rating', 'seao_locationid', 'location', 'like_count', 'description', 'creation_date'));

        $select->where($parentTableName . '.search = ?', true);

        if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
            $select->joinLeft($parentTableName, $parentTableName . '.album_id=' . $photoTableName . '.album_id', null);
        } else {
            $select->joinLeft($parentTableName, $parentTableName . '.album_id=' . $photoTableName . '.collection_id', null);
        }

        if (isset($params['tab']) && isset($params['owner_id']) && $params['tab'] == 'likesphotos') {
            $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
            $popularityTableName = $popularityTable->info('name');

            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $photoTableName . '.photo_id', null)
                    ->where($popularityTableName . '.poster_id = ?', $params['owner_id']);
        }

        if (isset($params['owner_id']) && !empty($params['owner_id'])) {
            $select->where($parentTableName . '.owner_id = ?', $params['owner_id'])
                    ->order($photoTableName . ".photo_id DESC");
        }

        if (!isset($params['location']) && (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationName, "$photoTableName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);
//      $select->order("distance");
//      $select->group("$photoTableName.photo_id");
        }

        if (!empty($params['category_id'])) {
            $select->where($parentTableName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($parentTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['orderby']) && !empty($params['orderby'])) {
            switch ($params['orderby']) {

                case 'modified_date':
                    $select->order($photoTableName . '.modified_date DESC');
                    break;
                case 'creation_date':
                    $select->order($photoTableName . '.creation_date DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($photoTableName . "$sqlTimeStr");
                    }
                    break;
                case 'date_taken' :
                    $select->order($photoTableName . '.date_taken DESC');
                    if (($interval == 'week') || ($interval == 'month')) {
                        $select->where($photoTableName . "$sqlTimeStr");
                    }
                    break;
                case 'like_count':
                case 'likesphotos':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
                        $popularityTableName = $popularityTable->info('name');

                        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $photoTableName . '.photo_id', array("COUNT($photoTableName.photo_id) as total_count"))
                                ->where($popularityTableName . '.resource_type = ?', 'album_photo')
                                ->order("total_count DESC");

                        $select->where($popularityTableName . "$sqlTimeStr");
                    } else {
                        $select->where($photoTableName . '.like_count != ?', 0);
                        $select->order($photoTableName . '.like_count DESC');
                    }

                    break;
                case 'view_count':
                    $select->where($photoTableName . '.view_count != ?', 0);
                    $select->order($photoTableName . '.view_count DESC');
                    break;
                case 'comment_count':
                    if (($interval == 'week') || ($interval == 'month')) {
                        $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
                        $popularityTableName = $popularityTable->info('name');

                        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $photoTableName . '.photo_id', array("COUNT($photoTableName.photo_id) as total_count"))
                                ->where($popularityTableName . '.resource_type = ?', 'album_photo')
                                ->order("total_count DESC");


                        $select->where($popularityTableName . "$sqlTimeStr");
                    } else {
                        $select->where($photoTableName . '.comment_count != ?', 0);
                        $select->order($photoTableName . '.comment_count DESC');
                    }
                    break;
                case 'rating':
                    $select->where($photoTableName . '.rating != ?', 0);
                    $select->order($photoTableName . '.rating DESC');
                    break;
                case 'featured':
                    $select->where($photoTableName . '.featured = ?', 1);
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
            $select->order($photoTableName . ".photo_id DESC");
        }

        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where($photoTableName . '.featured = ?', 1);
        }

        $select->group("$photoTableName.photo_id");

        if (!empty($params['ownerObject'])) {
            $ownerObject = $params['ownerObject'];
            $viewer = Engine_Api::_()->user()->getViewer();
            if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1) && $viewer->getIdentity() != $ownerObject->getIdentity()) {
                $select->where('type IS NULL');
            }
        } elseif (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
            $select->where('type IS NULL');

        //$select = Engine_Api::_()->sitealbum()->addPrivacyAlbumsSQl($select, $photoTableName);
        $select = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));

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
     * Get paginator of photos
     *
     * @param array $params
     * @return Zend_Paginator;
     */
    Public function photoBySearching($params = array()) {
        $parentTable = Engine_Api::_()->getItemTable('album');
        $parentTableName = $parentTable->info('name');

        $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
        $locationName = $locationTable->info('name');
        //GET TAGMAP TABLE NAME
        $tagMapTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');

        $photoTableName = $this->info('name');
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($photoTableName, array('album_id', 'photo_id', 'title', 'file_id', 'owner_id', 'view_count', 'comment_count', 'rating', 'seao_locationid', 'location', 'like_count', 'description', 'creation_date'));

        $select->where($parentTableName . '.search = ?', true);

        if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
            $select->joinLeft($parentTableName, $parentTableName . '.album_id=' . $photoTableName . '.album_id', null);
        } else {
            $select->joinLeft($parentTableName, $parentTableName . '.album_id=' . $photoTableName . '.collection_id', null);
        }

        if (isset($params['album_city']) && !empty($params['album_city']) && strstr(',', $params['album_city'])) {
            $album_city = explode(',', $params['album_city']);
            $params['album_city'] = $album_city[0];
        }

        if (isset($params['album_street']) && !empty($params['album_street'])) {
            $select->join($locationName, "$photoTableName.seao_locationid = $locationName.locationitem_id");
            $select->where($locationName . '.formatted_address LIKE ? ', '%' . $params['album_street'] . '%');
        } if (isset($params['album_city']) && !empty($params['album_city'])) {
            $select->join($locationName, "$photoTableName.seao_locationid = $locationName.locationitem_id");
            $select->where($locationName . '.city = ?', $params['album_city']);
        } if (isset($params['album_state']) && !empty($params['album_state'])) {
            $select->join($locationName, "$photoTableName.seao_locationid = $locationName.locationitem_id");
            $select->where($locationName . '.state = ?', $params['album_state']);
        } if (isset($params['album_country']) && !empty($params['album_country'])) {
            $select->join($locationName, "$photoTableName.seao_locationid = $locationName.locationitem_id");
            $select->where($locationName . '.country = ?', $params['album_country']);
        }
        if (!isset($params['location']) && (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationName, "$photoTableName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);
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
                if (empty($params['Latitude']) && empty($params['Longitude']) || $detactLatLng) {

                    if ($params['location']) {
                        $selectLocQuery = $locationTable->select()->where('location = ?', $params['location']);
                        $locationValue = $locationTable->fetchRow($selectLocQuery);
                    }

                    if (empty($locationValue)) {
                        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['location'], 'module' => 'Advanced Photo Albums'));
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

                $radius = $params['locationmiles']; //in miles

                $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.proximity.search.kilometer', 0);
                if (!empty($flage)) {
                    $radius = $radius * (0.621371192);
                }
                //$latitudeRadians = deg2rad($latitude);
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";
                $select->join($locationName, "$photoTableName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance", 'locationitem_id', 'resource_type', 'resource_id', 'formatted_address', 'country', 'state', 'zipcode', 'city', 'address', 'location as seao_location'));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);
            } else {
                $select->join($locationName, "$photoTableName.seao_locationid = $locationName.locationitem_id", array('locationitem_id', 'resource_type', 'resource_id', 'latitude', 'longitude', 'formatted_address', 'country', 'state', 'zipcode', 'city', 'address', 'location as seao_location'));
                $select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['location']) . "%");
            }
        } elseif (empty($params['album_street']) && empty($params['album_city']) && empty($params['album_state']) && empty($params['album_country']) && (!isset($params['notLocationPage']) && empty($params['notLocationPage']))) {
            $select->joinLeft($locationName, "$photoTableName.seao_locationid = $locationName.locationitem_id", array('locationitem_id', 'resource_type', 'resource_id', 'latitude', 'longitude', 'formatted_address', 'country', 'state', 'zipcode', 'city', 'address', 'location as seao_location'));
        }

        if (!empty($params['tag_id'])) {
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $photoTableName.photo_id", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'))
                    ->where($tagMapTableName . '.resource_type = ?', 'album_photo')
                    ->where($tagMapTableName . '.tag_id = ?', $params['tag_id']);
        }
        if (!empty($params['category_id'])) {
            $select->where($parentTableName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($parentTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['orderby']) && !empty($params['orderby'])) {
            switch ($params['orderby']) {
                case 'taken_date' :
                    $select->order($photoTableName . '.date_taken DESC');
                    break;
                case 'featuredTakenBy' :
                    $select->order($photoTableName . '.featured DESC');
                    $select->order('date_taken DESC');
                    break;
                case 'modified_date':
                    $select->order($photoTableName . '.modified_date DESC');
                    break;
                case 'creation_date':
                    $select->order($photoTableName . '.creation_date DESC');
                    break;
                case 'like_count':
                case 'likesphotos':
                    $select->where($photoTableName . '.like_count != ?', 0);
                    $select->order($photoTableName . '.like_count DESC');
                    break;
                case 'view_count':
                    $select->where($photoTableName . '.view_count != ?', 0);
                    $select->order($photoTableName . '.view_count DESC');
                    break;
                case 'comment_count':
                    $select->where($photoTableName . '.comment_count != ?', 0);
                    $select->order($photoTableName . '.comment_count DESC');
                    break;
                case 'rating':
                    $select->where($photoTableName . '.rating != ?', 0);
                    $select->order($photoTableName . '.rating DESC');
                    break;
                case 'featured':
                 //   $select->where($photoTableName . '.featured = ?', 1);
                    $select->order($photoTableName . '.featured DESC');
                    $select->order('creation_date DESC');
                    break;
                case 'random':
                    $select->order('Rand()');
                    break;
            }
        }
        if (!empty($params['search'])) {

            $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $photoTableName.photo_id and " . $tagMapTableName . ".resource_type = 'album_photo'", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'))
                    ->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id", array());

            $select->where($photoTableName . ".title LIKE ? OR " . $photoTableName . ".description LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['search'] . '%');
        }
        $select->group("$photoTableName.photo_id");
        if (!empty($params['ownerObject'])) {
            $ownerObject = $params['ownerObject'];
            $viewer = Engine_Api::_()->user()->getViewer();
            if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1) && $viewer->getIdentity() != $ownerObject->getIdentity()) {
                $select->where('type IS NULL');
            }
        } elseif (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
            $select->where('type IS NULL');

        //$select = Engine_Api::_()->sitealbum()->addPrivacyAlbumsSQl($select, $photoTableName);
        $select = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));

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
     * get the tagged photos both you and owner
     */
    public function getTaggedYouAndOwnerPhotos($viewer_id, $owner_id, $limit = null) {

        $tableTagmaps = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $tableTagmapsName = $tableTagmaps->info('name');

        $nestedSelect = $tableTagmaps->select()
                ->from($tableTagmapsName, array('resource_id'))
                ->where($tableTagmapsName . '.resource_type = ?', "album_photo")
                ->where($tableTagmapsName . '.tag_type = ?', "user")
                ->where($tableTagmapsName . '.tag_id = ?', $viewer_id);
        $viewerTagged = $tableTagmaps->fetchAll($nestedSelect);
        $resource_id = array();
        foreach ($viewerTagged as $value)
            $resource_id[] = $value->resource_id;
        // Not any tagged photo
        if (empty($resource_id))
            return;

        $parentTable = Engine_Api::_()->getItemTable('album');
        $parentTableName = $parentTable->info('name');

        $photoTableName = $this->info('name');

        $select = $tableTagmaps->select()
                ->from($tableTagmapsName, array('resource_id'))
                ->join($photoTableName, $tableTagmapsName . '.resource_id=' . $photoTableName . '.photo_id', array());
        if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
            $select->join($parentTableName, $parentTableName . '.album_id=' . $photoTableName . '.album_id', array());
        } else {
            $select->join($parentTableName, $parentTableName . '.album_id=' . $photoTableName . '.collection_id', array());
        }

        $select->where($tableTagmapsName . '.resource_type = ?', "album_photo")
                ->where($tableTagmapsName . '.resource_id in (?)', new Zend_Db_Expr("'" . join("', '", $resource_id) . "'"))
                ->where($tableTagmapsName . '.tag_id = ?', $owner_id)
                ->where('search = ?', true);

        //$select = Engine_Api::_()->sitealbum()->addPrivacyAlbumsSQl($select, $photoTableName);
        $select = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getNetworkBaseSql($select);

        if (!empty($limit)) {
            $select->limit($limit);
            $select->order('RAND()');
        }

        return Zend_Paginator::factory($select);
    }

    /**
     * get the tagged photos both you and owner
     */
    public function getTaggedInOthersPhotos($params = array()) {

        $parentTable = Engine_Api::_()->getItemTable('album');
        $parentName = $parentTable->info('name');

        $photoTableName = $this->info('name');

        $tableTagmaps = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $tableTagmapsName = $tableTagmaps->info('name');

        $select = $tableTagmaps->select()
                ->from($tableTagmapsName, array('resource_id', 'creation_date'))
                ->join($photoTableName, $tableTagmapsName . '.resource_id=' . $photoTableName . '.photo_id', array());

        if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
            $select->join($parentName, $parentName . '.album_id=' . $photoTableName . '.album_id', array());
        } else {
            $select->join($parentName, $parentName . '.album_id=' . $photoTableName . '.collection_id', array());
        }

        if (!empty($params['category_id'])) {
            $select->where($parentName . '.category_id = ?', $params['category_id']);
        }

        if (!empty($params['subcategory_id'])) {
            $select->where($parentName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        $select->where($tableTagmapsName . '.resource_type = ?', "album_photo")
                ->where($photoTableName . '.	owner_id <> ?', $params['owner_id'])
                ->where($parentName . '.	owner_id <> ?', $params['owner_id'])
                ->where($tableTagmapsName . '.tag_id = ?', $params['owner_id'])
                ->where($tableTagmapsName . '.tag_type = ?', "user")
                ->where('search = ?', true)
                ->order($tableTagmapsName . '.creation_date DESC');

        return Zend_Paginator::factory($select);
    }

    /**
     * get the friends photos
     */
    public function getFriendsPhotos($friend_ids, $params) {

        $parentTable = Engine_Api::_()->getItemTable('album');
        $parentTableName = $parentTable->info('name');

        $photoTableName = $this->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($photoTableName, array('album_id', 'photo_id', 'file_id', 'title', 'owner_id', 'like_count', 'view_count', 'comment_count', 'rating', 'location', 'seao_locationid', 'creation_date'));
        if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
            $select->joinLeft($parentTableName, $parentTableName . '.album_id=' . $photoTableName . '.album_id', null);
        } else {
            $select->joinLeft($parentTableName, $parentTableName . '.album_id=' . $photoTableName . '.collection_id', null);
        }

        $select
                ->where($parentTableName . '.search = ?', true)
                ->where($parentTableName . '.owner_id in (?)', new Zend_Db_Expr("'" . join("', '", $friend_ids) . "'"))
                ->limit($params['itemCountPhoto'])
                ->order('Rand()');

        if (!empty($params['category_id'])) {
            $select->where($parentTableName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($parentTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where($photoTableName . '.featured = ?', 1);
        }

        $select->group("$photoTableName.photo_id");

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
            $select->where($parentTableName . '.type IS NULL');
        //$select = Engine_Api::_()->sitealbum()->addPrivacyAlbumsSQl($select, $photoTableName);
        $select = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
        return $this->fetchAll($select);
    }

    /**
     * get the Profile Strip photos
     */
    public function getProfileStripPhotos($params) {

        $parentTable = Engine_Api::_()->getItemTable('album');
        $parentTableName = $parentTable->info('name');

        $photoTableName = $this->info('name');

        $select = $this->select()
                ->from($photoTableName, array('album_id', 'photo_id', 'file_id', 'title'))
                ->where($photoTableName . '.owner_id = ?', $params['owner_id'])
                ->where($photoTableName . '.photo_hide = ?', 0)
                ->where('search = ?', true);
        if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
            $select->joinLeft($parentTableName, $parentTableName . '.album_id=' . $photoTableName . '.album_id', null);
        } else {
            $select->joinLeft($parentTableName, $parentTableName . '.album_id=' . $photoTableName . '.collection_id', null);
        }

        if (!empty($params['category_id'])) {
            $select->where($parentTableName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($parentTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['orderby']) && !empty($params['orderby'])) {
            switch ($params['orderby']) {
                case "creation_date":
                    $select->order($photoTableName . '.creation_date DESC');
                    break;
                case "modified_date":
                    $select->order($photoTableName . '.modified_date DESC');
                    break;
                case "view_count":
                    $select->order($photoTableName . '.view_count DESC');
                    break;
                case "comment_count":
                    $select->order($photoTableName . '.comment_count DESC');
                    break;
                case 'like_count':
                case 'likesphotos':
                    $select->order($photoTableName . '.like_count  DESC');
                    break;
                case 'rating':
                    $select->order($photoTableName . '.rating  DESC');
                    break;
                default:
                    $select->order($photoTableName . '.modified_date DESC');
                    break;
            }
        }

        if (isset($params['orderby']) && !empty($params['orderby']) && $params['orderby'] != 'creation_date' && $params['orderby'] != 'modified_date' && $params['orderby'] != 'random') {
            $select->order($photoTableName . ".photo_id DESC");
        }

        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where($photoTableName . '.featured = ?', 1);
        }

        $select->group("$photoTableName.photo_id");

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
            $select->where($parentTableName . '.type IS NULL');
        $select->limit($params['itemCountPerPage']);

        //$select = Engine_Api::_()->sitealbum()->addPrivacyAlbumsSQl($select, $photoTableName);
        $select = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));

        return Zend_Paginator::factory($select);
    }

    /**
     * get the photo next/ previoues
     */
    public function getPhoto($collectible, $params = array(), $direction) {

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
        return Engine_Api::_()->getItem('album_photo', $row->photo_id);
    }

    /**
     * get the current photo index
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

        $parentTable = Engine_Api::_()->getItemTable('album');
        $parentName = $parentTable->info('name');

        $tableName = $this->info('name');

        $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
        $locationName = $locationTable->info('name');

        $col = current($this->info("primary"));
        $select = $this->select()
                ->from($tableName, $col);
        if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
            $select->join($parentName, $parentName . '.album_id=' . $tableName . '.album_id', null);
        } else {
            $select->join($parentName, $parentName . '.album_id=' . $tableName . '.collection_id', null);
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
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.proximity.search.kilometer', 0);
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
//      $select->group("$tableName.photo_id");
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
                        ->join($tableName, $tableTagmapsName . '.resource_id=' . $tableName . '.photo_id', array('photo_id'));

                if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
                    $select->join($parentName, $parentName . '.album_id=' . $tableName . '.album_id', array());
                } else {
                    $select->join($parentName, $parentName . '.album_id=' . $tableName . '.collection_id', array());
                }
                $select->where($tableTagmapsName . '.resource_type = ?', "album_photo")
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

                return $select;
                break;
            case 'random':
                $select->order('Rand()');
                break;
            case 'yourphotos':
                $select->where($parentName . '.owner_id =? ', $params['owner_id']);
                break;
            case 'creation_date':
                $select->order($tableName . '.creation_date DESC');
                if (($interval == 'week') || ($interval == 'month')) {
                    $select->where($tableName . "$sqlTimeStr");
                }
                break;
            case 'like_count':
            case 'likesphotos':
                if (($interval == 'week') || ($interval == 'month')) {
                    $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
                    $popularityTableName = $popularityTable->info('name');

                    $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $tableName . '.photo_id', array("COUNT($tableName.photo_id) as total_count"))
                            ->where($popularityTableName . '.resource_type = ?', 'album_photo')
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

                    $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $tableName . '.photo_id', array("COUNT($tableName.photo_id) as total_count"))
                            ->where($popularityTableName . '.resource_type = ?', 'album_photo')
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

        if (!empty($params['featured'])) {
            $select->where($tableName . '.featured = ?', 1);
        }

        if ($params['type'] == 'strip_view' || $params['type'] == 'strip_like' || $params['type'] == 'strip_comment' || $params['type'] == 'strip_rating' || $params['type'] == 'strip_creation' || $params['type'] == 'strip_modified' || $params['type'] == 'strip_random') {
//      if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
//        $select->where($parentName . '.type IS NULL');
            if (!empty($params['owner_id'])) {
                $select->where($tableName . '.owner_id = ?', $params['owner_id'])
                        ->where($tableName . '.photo_hide = ?', 0);
            }
        }

        if ($type != "modified_date" && $type != "creation_date" && $type != "random")
            $select->order($tableName . '.photo_id DESC');

        $select->group("$tableName.photo_id");

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
            $select->where($parentName . '.type IS NULL');

        $select = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
        return $select;
    }

    /**
     * Get total photos
     *
     * @param array $params
     * @return int $totalPhotos;
     */
    public function getPhotosCount($params = array()) {

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('COUNT(*) AS count'));

        $totalPhotos = $select->query()->fetchColumn();

        //RETURN PHOTO COUNT
        return $totalPhotos;
    }

}
