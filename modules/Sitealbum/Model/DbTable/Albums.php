<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Albums.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Model_DbTable_Albums extends Core_Model_Item_DbTable_Abstract {

  protected $_name = 'album_albums';
  protected $_rowClass = 'Sitealbum_Model_Album';

  public function getSpecialAlbum(User_Model_User $user, $type) {

    if (!in_array($type, array('wall', 'profile', 'message', 'blog', 'comment'))) {
      throw new Album_Model_Exception('Unknown special album type');
    }
    $select = $this->select()
            ->where('owner_type = ?', $user->getType())
            ->where('owner_id = ?', $user->getIdentity())
            ->where('type = ?', $type)
            ->order('album_id ASC')
            ->limit(1);

    $album = $this->fetchRow($select);

    // Create wall photos album if it doesn't exist yet
    if (null === $album) {
      $translate = Zend_Registry::get('Zend_Translate');

      $album = $this->createRow();
      $album->owner_type = 'user';
      $album->owner_id = $user->getIdentity();
      $album->title = $translate->_(ucfirst($type) . ' Photos');
      $album->type = $type;

      if ($type == 'message' || !Engine_Api::_()->getApi('settings', 'core')->getSetting('album.searchable', 0)) {
        $album->search = 0;
      } else {
        $album->search = 1;
      }

      $album->save();

      // Authorizations
      if ($type != 'message') {
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($album, 'everyone', 'view', true);
        $auth->setAllowed($album, 'everyone', 'comment', true);
      }
    }

    return $album;
  }

  /**
   * Get total albums of particular category / subcategoty 
   *
   * @param array $params
   * @return int $totalAlbums;
   */
  public function getAlbumsCount($params = array()) {

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('COUNT(*) AS count'));

    if (isset($params['foruser']) && !empty($params['foruser'])) {
      $select->where('search = ?', 1);
    }

    if (!empty($params['columnName']) && !empty($params['category_id'])) {
      $column_name = $params['columnName'];
      $select->where("$column_name = ?", $params['category_id']);
    }

    $totalAlbums = $select->query()->fetchColumn();

    //RETURN ALBUM COUNT
    return $totalAlbums;
  }

  /**
   * Get albums of current viewing user
   * @param obj $user
   * @param array $params
   * @return string $select;
   */
  public function getUserAlbums($user, $params = array()) {

    $select = $this->select()->where("owner_type = ?", "user")->where("owner_id = ?", $user->user_id)->where('search = ?', 1)->order('album_id DESC');

    if (!empty($params['category_id']) && is_numeric($params['category_id'])) {
      $select->where('category_id = ?', $params['category_id']);
    }

    if (!empty($params['subcategory_id']) && is_numeric($params['subcategory_id'])) {
      $select->where('subcategory_id = ?', $params['subcategory_id']);
    }

    if (!isset($params['defaultAlbumsShow']) && empty($params['defaultAlbumsShow']) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
      $select->where('type IS NULL');

    if (isset($params['fetchAll']) && !empty($params['fetchAll'])) {
      return $this->fetchAll($select);
    } else {
      return $select;
    }
  }

  public function getAlbumPaginator($options = array(), $customParams = null) {
    return Zend_Paginator::factory($this->getAlbumSelect($options,$customParams));
  }

    /**
     * Get album select query
     *
     * @param array $params
     * @param array $customParams
     * @return string $select;
     */
    public function getAlbumSelect($params = array(), $customParams = null) {

        //GET ALBUM TABLE
        $albumTableName = $this->info('name');

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
        $locationName = $locationTable->info('name');

        //GET SEARCH TABLE
        $searchTable = Engine_Api::_()->fields()->getTable('album', 'search')->info('name');

        //GET TAGMAP TABLE NAME
        $tagMapTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');

        $select = $this->select();
        $select
                ->setIntegrityCheck(false)
                ->from($albumTableName, array('album_id', 'photo_id', 'title', 'description', 'owner_id', 'view_count', 'comment_count', 'rating', 'seao_locationid', 'location', 'like_count', 'creation_date', 'category_id', 'photos_count'));

        if (!isset($params['albumType'])) {
            $select->where($albumTableName . '.search = ?', '1');
        }

        if (isset($customParams)) {

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
                    ->joinLeft($searchTable, "$searchTable.item_id = $albumTableName.album_id", null);

            $searchParts = Engine_Api::_()->fields()->getSearchQuery('album', $customParams);
            foreach ($searchParts as $k => $v) {
                $select->where("`{$searchTable}`.{$k}", $v);
            }
        }

        if (isset($params['album_city']) && !empty($params['album_city']) && strstr(',', $params['album_city'])) {
            $album_city = explode(',', $params['album_city']);
            $params['album_city'] = $album_city[0];
        }

        if (isset($params['album_street']) && !empty($params['album_street']) || isset($params['album_city']) && !empty($params['album_city']) || isset($params['album_state']) && !empty($params['album_state']) || isset($params['album_country']) && !empty($params['album_country'])) {
            $select->join($locationName, "$albumTableName.seao_locationid = $locationName.locationitem_id");
        }
        if (isset($params['album_street']) && !empty($params['album_street'])) {
            $select->where($locationName . '.formatted_address LIKE ? ', '%' . $params['album_street'] . '%');
        } if (isset($params['album_city']) && !empty($params['album_city'])) {

            $select->where($locationName . '.city = ?', $params['album_city']);
        } if (isset($params['album_state']) && !empty($params['album_state'])) {

            $select->where($locationName . '.state = ?', $params['album_state']);
        } if (isset($params['album_country']) && !empty($params['album_country'])) {

            $select->where($locationName . '.country = ?', $params['album_country']);
        }

        if (!isset($params['location']) && isset($params['locationSearch']) && !empty($params['locationSearch'])) {
            $params['location'] = $params['locationSearch'];

            if (isset($params['locationmilesSearch'])) {
                $params['locationmiles'] = $params['locationmilesSearch'];
            }
        } elseif (!isset($params['location']) && isset($params['album_location']) && !empty($params['album_location'])) {
            $params['location'] = $params['album_location'];
        }

        if (!isset($params['location']) && (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationName, "$albumTableName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
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
                $select->join($locationName, "$albumTableName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);
            } else {
                $select->join($locationName, "$albumTableName.seao_locationid = $locationName.locationitem_id");
                $select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['location']) . "%");
            }
        } elseif (empty($params['album_street']) && empty($params['album_city']) && empty($params['album_state']) && empty($params['album_country']) && (!isset($params['notLocationPage']) && empty($params['notLocationPage']))) {
            $select->joinLeft($locationName, "$albumTableName.seao_locationid = $locationName.locationitem_id");
        }

        if (!empty($params['tag_id'])) {
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $albumTableName.album_id", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'))
                    ->where($tagMapTableName . '.resource_type = ?', 'album')
                    ->where($tagMapTableName . '.tag_id = ?', $params['tag_id']);
        }

        if (!empty($params['category_id'])) {
            $select->where($albumTableName . '.category_id = ?', $params['category_id']);
        }
        if (!empty($params['subcategory_id'])) {
            $select->where($albumTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (!empty($params['users']) && isset($params['users'])) {
            $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
            $select->where($albumTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
        }

        if (!isset($params['orderby']) && empty($params['orderby'])) {
            $params['orderby'] = 'creation_date';
        }

        if (!empty($params['ignoreAlbum'])) {
            $select->where($albumTableName . '.photos_count > ?', 0);
        }

        if (isset($params['orderby']) && !empty($params['orderby'])) {
            switch ($params['orderby']) {
                case "creation_date":
                    $select->order($albumTableName . '.creation_date DESC');
                    break;
                case "modified_date":
                    $select->order($albumTableName . '.modified_date DESC');
                    break;
                case "view_count":
                    $select->order($albumTableName . '.view_count DESC');
                    break;
                case "comment_count":
                    $select->order($albumTableName . '.comment_count DESC');
                    break;
                case 'like_count':
                    $select->order($albumTableName . '.like_count  DESC');
                    break;
                case 'photos_count':
                    $select->order($albumTableName . '.photos_count  DESC');
                    break;
                case 'rating':
                    $select->order($albumTableName . '.rating  DESC');
                    break;
                case 'title':
                    $select->order('title ASC');
                    break;
                case 'title_reverse':
                    $select->order('title DESC');
                    break;
                case 'featured':
                    $select->order('featured DESC');
                    break;
                default:
                    $select->order($albumTableName . '.modified_date DESC');
                    break;
            }
        }

        if (isset($params['orderby']) && !empty($params['orderby']) && $params['orderby'] != 'creation_date' && $params['orderby'] != 'modified_date' && $params['orderby'] != 'random') {
            $select->order($albumTableName . ".album_id DESC");
        }

        if (!empty($params['owner']) &&
                $params['owner'] instanceof Core_Model_Item_Abstract) {
            $select
                    ->where("$albumTableName.owner_type = ?", $params['owner']->getType())
                    ->where("$albumTableName.owner_id = ?", $params['owner']->getIdentity());
        }


        if (!empty($params['search'])) {

            $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $albumTableName.album_id and " . $tagMapTableName . ".resource_type = 'album'", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName . '.tag_id'))
                    ->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id", array());

            $select->where($albumTableName . ".title LIKE ? OR " . $albumTableName . ".description LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['search'] . '%');
        }
        $select->group($albumTableName . '.album_id');
       // echo $select;
      //  die;
        if (!isset($params['defaultAlbumsShow']) && empty($params['defaultAlbumsShow']) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
            $select->where('type IS NULL');

        //Network Based Work
        //$select = Engine_Api::_()->sitealbum()->addPrivacyAlbumsSQl($select);
        $select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));

        return $select;
    }

  /**
   * Get paginator of albums
   *
   * @param array $params
   * @return Zend_Paginator;
   */
  Public function albumBySettings($params = array()) {

    $albumTableName = $this->info('name');

    $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
    $locationName = $locationTable->info('name');

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
            ->from($albumTableName, array('album_id', 'photo_id', 'title', 'description', 'owner_id', 'view_count', 'comment_count', 'rating', 'seao_locationid', 'location', 'like_count', 'creation_date', 'category_id', 'photos_count'))
            ->where($albumTableName . '.search = ?', true);

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

      $select->join($locationName, "$albumTableName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
      $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
      $sqlstring .= ")";
      $select->where($sqlstring);
    }

    if (!empty($params['category_id'])) {
      $select->where($albumTableName . '.category_id = ?', $params['category_id']);
    }
    if (!empty($params['subcategory_id'])) {
      $select->where($albumTableName . '.subcategory_id = ?', $params['subcategory_id']);
    }

    if (isset($params['orderby']) && !empty($params['orderby'])) {
      switch ($params['orderby']) {
        case 'modified_date':
          $select->order($albumTableName . '.modified_date DESC');
          break;
        case 'creation_date':
          $select->order($albumTableName . '.creation_date DESC');
          if (($interval == 'week') || ($interval == 'month')) {
            $select->where($albumTableName . "$sqlTimeStr");
          }
          break;
        case 'like_count':
          if (($interval == 'week') || ($interval == 'month')) {
            $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
            $popularityTableName = $popularityTable->info('name');
            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $albumTableName . '.album_id', array("COUNT(album_id) as total_count"))
                    ->where($popularityTableName . '.resource_type = ?', 'album')
                    ->order("total_count DESC");

            $select->where($popularityTableName . "$sqlTimeStr");
          } else {
            $select->where($albumTableName . '.like_count != ?', 0);
            $select->order($albumTableName . '.like_count DESC');
          }
          break;
        case 'view_count':
          $select->where($albumTableName . '.view_count != ?', 0);
          $select->order($albumTableName . '.view_count DESC');
          break;
        case 'comment_count':
          if (($interval == 'week') || ($interval == 'month')) {
            $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
            $popularityTableName = $popularityTable->info('name');

            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $albumTableName . '.album_id', array("COUNT(album_id) as total_count"))
                    ->where($popularityTableName . '.resource_type = ?', 'album')
                    ->order("total_count DESC");

            $select->where($popularityTableName . "$sqlTimeStr");
          } else {
            $select->where($albumTableName . '.comment_count != ?', 0);
            $select->order($albumTableName . '.comment_count DESC');
          }
          break;
        case 'rating':
					$select->where($albumTableName . '.rating != ?', 0);
          $select->order($albumTableName . '.rating DESC');
          break;
        case 'photos_count':
          $select->order($albumTableName . '.photos_count DESC');
          break;
        case 'featured':
          $select->where($albumTableName . '.featured = ?', 1);
          if(isset($params['orderBy']) && $params['orderBy'] == 'creation_date') {
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
      $select->order($albumTableName . ".album_id DESC");
    }

    if (isset($params['featured']) && !empty($params['featured'])) {
      $select->where($albumTableName . '.featured = ?', 1);
    }

    $select->group("$albumTableName.album_id");

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
      $select->where('type IS NULL');

    $select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));

    return Zend_Paginator::factory($select);
  }

  /**
   * Get pages to add as item of the day
   * @param string $title : search text
   * @param int $limit : result limit
   */
  public function getDayItems($title, $limit = 10) {

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('album_id', 'owner_id', 'title', 'photo_id'))
            ->where('title  LIKE ? ', '%' . $title . '%')
            ->where('search = ?', '1')
            ->order('title ASC')
            ->limit($limit);

    //RETURN RESULTS
    return $this->fetchAll($select);
  }

  /**
   * Return albumids which have this category and this mapping
   *
   * @param int category_id
   * @return array $albumIds
   */
  public function getMappedSitealbum($category_id) {

    //RETURN IF CATEGORY ID IS NULL
    if (empty($category_id)) {
      return null;
    }

    //MAKE QUERY
    $albumIds = $this->select()
            ->from($this->info('name'), 'album_id')
            ->where("category_id = $category_id OR subcategory_id = $category_id")
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);

    return $albumIds;
  }

  /**
   * Return albums which have this category and this mapping
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
    $categoty_type = $params['category_type'];
    return $this->select()
                    ->from($this->info('name'), 'album_id')
                    ->where("$categoty_type = ?", $params['category_id'])
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
  }

  /**
   * Get Popular location base on city and state
   *
   */
  public function getPopularLocation($params = null) {

    //GET SITEALBUM TABLE NAME
    $sitealbumTableName = $this->info('name');

    //GET LOCATION TABLE
    $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
    $locationTableName = $locationTable->info('name');

    //MAKE QUERY
    $seaolocationIds = $this->select()
            ->setIntegrityCheck(false)
            ->from($sitealbumTableName, array("seao_locationid"))
            ->where($sitealbumTableName . '.search = ?', '1')
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);

    if (empty($seaolocationIds)) {
      return;
    }

    $lselect = $locationTable->select()
            ->setIntegrityCheck(false)
            ->from($locationTableName, array("city", "count(city) as count_location", "state", "count(state) as count_location_state"))
            ->where("$locationTableName.locationitem_id IN (?)", $seaolocationIds)
            ->group("city")
            ->group("state")
            ->order("count_location DESC");

    if (isset($params['limit']) && !empty($params['limit'])) {
      $lselect->limit($params['limit']);
    }

    //RETURN RESULTS
    return $locationTable->fetchAll($lselect);
  }

  public function getNetworkBaseSql($select, $params = array()) {

    if (empty($select))
      return;

    $sitealbum_tableName = $this->info('name');

    //START NETWORK WORK
    $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.network', 0);
    if (!empty($enableNetwork) || (isset($params['browse_network']) && !empty($params['browse_network']))) {

      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      if (!Engine_Api::_()->sitealbum()->albumBaseNetworkEnable()) {
        $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

        if (!empty($viewerNetwork)) {
          $select->setIntegrityCheck(false);
          $networkMembershipName = $networkMembershipTable->info('name');
          $select
                  ->join($networkMembershipName, "`{$sitealbum_tableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                  ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                  ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity());
          if (!isset($params['not_groupBy']) || empty($params['not_groupBy'])) {
            $select->group($sitealbum_tableName . ".album_id");
          }
          if (isset($params['extension_group']) && !empty($params['extension_group'])) {
            $select->group($params['extension_group']);
          }
        }
      } else {
        $viewerNetwork = $networkMembershipTable->getMembershipsOfInfo($viewer);
        $str = array();
        $columnName = "`{$sitealbum_tableName}`.networks_privacy";
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
    
     $select = Engine_Api::_()->sitealbum()->addPrivacyAlbumsSQl($select, $this->info('name'));
     
    return $select;
  }

  /**
   * get the friends photo Albums
   */
  public function getFriendsPhotoAlbums($friend_ids, $params = array()) {

    $parentTableName = $this->info('name');

    $table = Engine_Api::_()->getItemTable('album_photo');

    $albumSelect = $this->select()
            ->setIntegrityCheck(false)
            ->from($parentTableName, array('album_id', 'photo_id', 'title', 'description', 'owner_id', 'view_count', 'comment_count', 'rating', 'seao_locationid', 'location', 'like_count', 'creation_date', 'category_id', 'photos_count'))
            ->where('owner_id in (?)', new Zend_Db_Expr("'" . join("', '", $friend_ids) . "'"))
            ->where('search = ?', true)
            ->limit($params['itemCountAlbum'])
            ->order('Rand()');

    if (!empty($params['category_id'])) {
      $albumSelect->where($parentTableName . '.category_id = ?', $params['category_id']);
    }
    if (!empty($params['subcategory_id'])) {
      $albumSelect->where($parentTableName . '.subcategory_id = ?', $params['subcategory_id']);
    }

    if (isset($params['featured']) && !empty($params['featured'])) {
      $albumSelect->where($parentTableName . '.featured = ?', 1);
    }

    $albumSelect->group("$parentTableName.album_id");

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
      $albumSelect->where('type IS NULL');
    // $albumSelect = $this->addPrivacyAlbumsSQl($albumSelect);
    $albumSelect = $this->getNetworkBaseSql($albumSelect, array('browse_network' => (isset($params['view_view']) && $params['view_view'] == "3")));
    $albums = $this->fetchAll($albumSelect);

    $collection = array();
    foreach ($albums as $value) {
      $photoSelect = $table->select()
              ->where('owner_id in (?)', new Zend_Db_Expr("'" . join("', '", $friend_ids) . "'"));
      if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
        $photoSelect->where('album_id = ?', $value->album_id);
      } else {
        $photoSelect->where('collection_id = ?', $value->album_id);
      }
      $photoSelect->limit($params['itemCountPhoto'])
              ->order('Rand()');
      $photos = $this->fetchAll($photoSelect);
      $count = count($photos);
      if ($count) {
        $collection[$value->album_id]['photo'] = $photos;
        $collection[$value->album_id]['album'] = $value;
      }
    }
    return $collection;
  }
}