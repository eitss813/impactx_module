<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Api_Core extends Core_Api_Abstract {

  protected $_table;

  public function getItemTableClass($type) {

    // Generate item table class manually
    $module = 'Sitealbum';
    $class = $module . '_Model_DbTable_' . self::typeToClassSuffix($type, $module);
    if (substr($class, -1, 1) === 'y' && substr($class, -3) !== 'way') {
      $class = substr($class, 0, -1) . 'ies';
    } else if (substr($class, -1, 1) !== 's') {
      $class .= 's';
    }
    return $class;
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
    if (count($parts) > 1 && ($parts[0] === strtolower($module) || $parts[0] === strtolower('album'))) {
      array_shift($parts);
    }
    $partial = str_replace(' ', '', ucwords(join(' ', $parts)));
    return $partial;
  }

  /**
   * Check lightbox is enable or not for photos show
   * @return bool
   */
  public function showLightBoxPhoto() {

    $session = new Zend_Session_Namespace('mobile');
    if (isset($session->mobile) && $session->mobile)
      return false;

    return SEA_SITEALBUM_LIGHTBOX;
  }

  /**
   * Get Photo lightbox Url
   * $photo : object
   * $params : array
   * @return url
   */
  public function getLightBoxPhotoHref($photo, $params = array()) {

    if (!$this->isLessThan417AlbumModule()) {
      $params = array_merge(array(
          'route' => 'sitealbum_extended',
          'reset' => true,
        //  'controller' => 'photo',
          'action' => 'light-box-view',
          'album_id' => $photo->album_id,
          'photo_id' => $photo->photo_id,
              ), $params);
    } else {
      $params = array_merge(array(
          'route' => 'sitealbum_extended',
          'reset' => true,
         // 'controller' => 'photo',
          'action' => 'light-box-view',
          'album_id' => $photo->collection_id,
          'photo_id' => $photo->photo_id,
              ), $params);
    }
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  public function getOnlyViewableAlbumsId() { 
       
    $viewer = Engine_Api::_()->user()->getViewer();
    $albums_ids = array();
    $cache = Zend_Registry::get('Zend_Cache');
    $cacheName = 'album_ids_user_id_' . $viewer->getIdentity();

    $data = APPLICATION_ENV == 'development' ? ( Zend_Registry::isRegistered($cacheName) ? Zend_Registry::get($cacheName) : null ) : $cache->load($cacheName);
    if ($data && is_array($data)) {
      $albums_ids = $data;
    } else {
      set_time_limit(0);
      $resource_type = 'album';
      $resource_id = 'album_id';
      $owner_id = 'owner_id';
      $action = 'view';
      $authorizationAllowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
      $authorizationAllowTableName = $authorizationAllowTable->info('name');
      $resourceTable = Engine_Api::_()->getItemTable($resource_type);
      $resourceTableName = $resourceTable->info('name');
      $db = $authorizationAllowTable->getAdapter();
      $union = new Zend_Db_Select($db);
      // Target info
      $select = $authorizationAllowTable->select()
        ->from($authorizationAllowTable->info('name'), 'resource_id')
        ->where('resource_type = ?', $resource_type)
        ->where('action = ?', $action)
      ;
      $select->setIntegrityCheck(false)
        ->join($resourceTableName, "$authorizationAllowTableName.resource_id =$resourceTableName.$resource_id", array());
      //   ->where("$resourceTable.$owner_id IN(?)", (array) $category_ids);
      $friendsIds = array();
      $networkUserIds = array();
      $userFriendMemberIds = array();
      $viewer = Engine_Api::_()->user()->getViewer();
      $registeredPrivacy = array(
        'everyone',
        'registered',
        'owner_network',
        'owner_member_member',
        'owner_member',
      );
      if( !$viewer->getIdentity() ) {
        $registeredPrivacy = array(
          'everyone',
        );
      } else {
        $friendsIds = $viewer->membership()->getMembersIds();
        $netMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
        $networkIds = $netMembershipTable->getMembershipsOfIds($viewer);
        if( $networkIds ) {
          $networkUserIds = array_diff($netMembershipTable
              ->select()
              ->from($netMembershipTable->info('name'), 'user_id')
              ->where('resource_id IN(?)', $networkIds)
              ->where('active = ?', true)
              ->group('user_id')
              ->query()
              ->fetchAll(Zend_Db::FETCH_COLUMN), array($viewer->getIdentity()));
        }
        if( $friendsIds ) {
          $userMembershipTable = Engine_Api::_()->getDbtable('membership', 'user');
          $userFriendMemberIds = $userMembershipTable
            ->select()
            ->from($userMembershipTable->info('name'), 'user_id')
            ->where('resource_id IN(?)', $friendsIds)
            ->where('active = ?', true)
            ->group('user_id')
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
        }
      }
      foreach( $registeredPrivacy as $type ) {
        $userIds = array();
        if( !in_array($type, array('registered', 'everyone')) ) {
          if( $type === 'owner_member' ) {
            $userIds = $friendsIds;
          } else if( $type === 'owner' ) {
            $userIds = array($viewer->getIdentity());
          } else if( $type === 'owner_member_member' ) {
            $userIds = $userFriendMemberIds;
          } else if( $type === 'owner_network' ) {
            $userIds = $networkUserIds;
          }
          if( empty($userIds) ) {
            continue;
          }
        }
        $selectSubject = clone $select;
        // Add subject to main query
        $selectSubject
          ->where($authorizationAllowTableName . '.role = ?', $type);
        if( $userIds ) {
          $selectSubject->where("$resourceTableName.$owner_id IN(?)", $userIds);
        }
        $union->union(array('(' . $selectSubject->__toString() . ')')); // (string) not work before PHP 5.2.0
      }
      $union->order('resource_id DESC');
      $results = $db->fetchAll($union);
      $ids = array();
      foreach( $results as $row ) {
        $ids[] = $row['resource_id'];
      }
      if( $viewer->getIdentity() ) {
        $ownerAlbumIds = $resourceTable
          ->select()
          ->from($resourceTableName, $resource_id)
          ->where("$owner_id = ?", $viewer->getIdentity())
          ->query()
          ->fetchAll(Zend_Db::FETCH_COLUMN);
        $ids = array_merge($ids, $ownerAlbumIds);
      }
      $albums_ids = array_unique($ids);
//      $table = Engine_Api::_()->getItemTable('album');
//      $album_select = $table->select()
//              ->where('search = ?', true)
//              ->order('album_id DESC');
//
//      if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
//        $album_select->where('type IS NULL');
//      // Create new array filtering out private albums
//      $i = 0;
//      foreach ($album_select->getTable()->fetchAll($album_select) as $album) {
//        if ($album->isOwner($viewer) || Engine_Api::_()->authorization()->isAllowed($album, $viewer, 'view')) {
//          $albums_ids[$i++] = $album->album_id;
//        }
//      }
      // Try to save to cache
      if( empty($albums_ids) )
        $albums_ids = array(0);

      if( APPLICATION_ENV == 'development' ) {
        Zend_Registry::set($cacheName, $albums_ids);
      } else {
        $cache->save($albums_ids, $cacheName);
      }
    }

    return $albums_ids;
  }

  public function addPrivacyAlbumsSQl($select, $tableName = null) {

    $privacybase = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.privacybase', 0);
    if (empty($privacybase))
      return $select;

    $column = $tableName ? "$tableName.album_id" : "album_id";

    return $select->where("$column IN(?)", $this->getOnlyViewableAlbumsId());
  }

  /**
   * get the tagged member in album
   * @params : $album_id
   * @return object
   */
  public function getTaggedUser($album_id) {

    $tableTagmaps = 'engine4_core_tagmaps';
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $userTable->info('name');
    $parentTable = Engine_Api::_()->getItemTable('album');
    $parentTableName = $parentTable->info('name');
    $table = Engine_Api::_()->getItemTable('album_photo');
    $tableName = $table->info('name');

    $select = $parentTable->select()
            ->setIntegrityCheck(false)
            ->from($parentTableName, array());
    if (!$this->isLessThan417AlbumModule()) {
      $select->join($tableName, $tableName . '.album_id=' . $parentTableName . '.album_id', array());
    } else {
      $select->join($tableName, $tableName . '.collection_id=' . $parentTableName . '.album_id', array());
    }
    $select->join($tableTagmaps, $tableTagmaps . '.resource_id=' . $tableName . '.photo_id', array('count(tag_id) as number_of_tag'))
            ->join($userTableName, $tableTagmaps . '.tag_id=' . $userTableName . '.user_id', array($userTableName . '.user_id'))
            ->where($parentTableName . '.album_id = ?', $album_id)
            ->where($tableTagmaps . '.resource_type = ?', "album_photo")
            ->where($tableTagmaps . '.tag_type = ?', "user")
            ->group($tableTagmaps . ".tag_id")
            ->order('number_of_tag DESC');
    return $table->fetchAll($select);
  }

  /**
   * get the previous photo
   */
  public function getPrevPhoto($current_photo, $params = array()) {
    if (!isset($params['type'])) {
      if (!$this->isLessThan417AlbumModule()) {
        return $current_photo->getPreviousPhoto();
      } else {
        return $current_photo->getPrevCollectible();
      }
    } else {
      return Engine_Api::_()->getDbTable('photos', 'sitealbum')->getPhoto($current_photo, $params, -1);
    }
  }

  /**
   * get the next photo
   */
  public function getNextPhoto($current_photo, $params = array()) {
    if (!isset($params['type'])) {
      if (!$this->isLessThan417AlbumModule()) {
        return $current_photo->getNextPhoto();
      } else {
        return $current_photo->getNextCollectible();
      }
    } else {
      return Engine_Api::_()->getDbTable('photos', 'sitealbum')->getPhoto($current_photo, $params, 1);
    }
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
      }
      else
        $friendship_status = $subject->membership()->getRow($viewer);

      if (!$friendship_status || $friendship_status->active == 0) {
        return false;
      }
    }
    return true;
  }

  public function isLessThan417AlbumModule() {
    $albumModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitealbum');
    $albumModuleVersion = $albumModule->version;
    if ($albumModuleVersion < '4.1.7') {
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
        'facebookse' => '4.1.7',
        'suggestion' => '4.1.7p1',
        'sitecontentcoverphoto' => '4.8.5'
    );
    $finalModules = array();
    foreach ($modArray as $key => $value) {
      $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
      if (!empty($isModEnabled)) {
        $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
        $isModSupport = $this->checkVersion($getModVersion->version, $value);
        if (empty($isModSupport)) {
          $finalModules[] = $getModVersion->title;
        }
      }
    }
    return $finalModules;
  }
    public function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }
  /**
   * Set Orders of Photos
   * @return bool
   */
  public function setPhotosOrder($album_id) {

    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    $album_id_Col = "album_id";
    if ($this->isLessThan417AlbumModule()) {
      $album_id_Col = "collection_id";
    }
    $conutOrder = $photoTable->select()
            ->from($photoTable, 'photo_id')
            ->where("$album_id_Col = ?", $album_id)
            ->where('`order` = ?', 0)
            ->order('order ASC')
            ->limit(2)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
    $count = count($conutOrder);
    if ($count <= 1)
      return;

    $currentOrder = $photoTable->select()
            ->from($photoTable, 'photo_id')
            ->where("$album_id_Col = ?", $album_id)
            ->order('order ASC')
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      $photo_id = $currentOrder[$i];
      $photoTable->update(array(
          'order' => $i,
              ), array(
          'photo_id = ?' => $photo_id,
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

    if (isset($params['album_type_title'])) {
      if (!empty($keywords))
        $keywords .= ', ';
      $keywords .= $params['album_type_title'];
    }

    $siteinfo['keywords'] = $keywords;
    $view->layout()->siteinfo = $siteinfo;
  }

  /**
   * Get sitealbum tags created by users
   * @param int $owner_id : sitealbum owner id
   * @param int $total_tags : number tags to show
   */
  public function getTags($owner_id = 0, $total_tags = 100, $count_only = 0, $params = array()) {

    //GET DOCUMENT TABLE
    $tableSitealbum = Engine_Api::_()->getDbtable('albums', 'sitealbum');
    $tableSitealbumName = $tableSitealbum->info('name');

    //MAKE QUERY
    $select = $tableSitealbum->select()
            ->setIntegrityCheck(false)
            ->from($tableSitealbumName, array("album_id"))
            ->where($tableSitealbumName . ".search = ?", 1);
    if (!empty($owner_id)) {
      $select->where($tableSitealbumName . '.owner_id = ?', $owner_id);
    }

    $select->distinct(true);

    $albumIds = $select
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);

    if (empty($albumIds)) {
      return;
    }

    $tableTagMaps = Engine_Api::_()->getDbtable('tagMaps', 'core');
    $tableTagMapsName = $tableTagMaps->info('name');

    //GET TAG TABLE NAME
    $tableTags = 'engine4_core_tags';

    //MAKE QUERY
    $select = $tableTagMaps->select()
            ->setIntegrityCheck(false)
            ->from($tableTagMapsName, array("COUNT($tableTagMapsName.resource_id) AS Frequency"))
            ->joinInner($tableTags, "$tableTags.tag_id = $tableTagMapsName.tag_id", array('text', 'tag_id'))
            ->where($tableTagMapsName . '.resource_type = ?', 'album')
            ->where($tableTagMapsName . '.resource_id IN(?)', (array) $albumIds)
            ->group("$tableTags.text");

    if (isset($params['orderingType']) && !empty($params['orderingType']))
      $select->order("$tableTags.text");
    else
      $select->order("Frequency DESC");

    if (!empty($total_tags)) {
      $select = $select->limit($total_tags);
    }

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
            ->where($coreContentTableName . '.name = ?', 'sitealbum.browse-albums-sitealbum')
            ->query()
            ->fetchColumn();

    $paramsArray = $params ? Zend_Json::decode($params) : array();

    if (isset($paramsArray['orderby']) && !empty($paramsArray['orderby'])) {
      return $paramsArray['orderby'];
    } else {
      return 0;
    }
  }

  /**
   * Album base network enable
   *
   * @return bool
   */
  public function albumBaseNetworkEnable() {
    return (bool) ( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.networks.type', 0) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.network', 0) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.default.show', 0)));
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

  public function saveInCheckinTable($checkin_params, $location, $getItem, $useNoPhoto = 0) {

    $notification_location = "";
    //GET RESOURCE TYPE
    $resource_type = $getItem->getType();

    //GET RESOURCE ID
    $resource_id = $getItem->getIdentity();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    //GET GUID
    $resource_guid = isset($checkin_params['resource_guid']) ? $checkin_params['resource_guid'] : 0;
    //GET TYPE
    $type = 'place';
    $viewer = Engine_Api::_()->user()->getViewer();
    //GET VIEWER ID
    $viewer_id = $viewer->getIdentity();
    //GET ADD LOCATION TABLE
    $tableAddLocation = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');
    //GET LOCATION ID
    $location_id = 0;
    if (!empty($location) && $type != 'just_use') {
      $location_id = $tableAddLocation->getLocationId($location);
    }

    //IF EMPTY LOCATION THE SET CHECKIN PARAM NULL
    if (empty($location))
      $checkin_params = null;

    //ACTION IDENTITY
    $actionIdentity = 0;

    //GET ACTION TABLE
    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $actionTableName = $actionTable->info('name');
    //GET ATTACHMENT TABLE
    $attachmentsTable = Engine_Api::_()->getDbtable('attachments', 'activity');
    $attachmentsTableName = $attachmentsTable->info('name');

    //SELECT ACTION IDS
    $select = $actionTable->select()->from($actionTable->info('name'))
            ->where('type =?', 'sitetagcheckin_location')
            ->where('object_type =?', $resource_type)
            ->where('object_id =?', $resource_id)
            ->where('subject_id =?', $viewer_id);

    $action = $actionTable->fetchRow($select);

    if (!isset($checkin_params['label']))
      $checkin_params['label'] = '';

    //SET PARAMS
    $params = array('checkin' => $checkin_params);

    //GET PHOTO ITEM
    $photo = Engine_Api::_()->getItem($resource_type, $resource_id);

    //IF ACTION IS EXISTIN THEN DELETE
    if (!empty($action)) {
      $action->delete();
    }

    //GET PREFIX
    if (isset($checkin_params['prefixadd'])) {
      $prefixadd = strtolower($checkin_params['prefixadd']);
    } else {
      $prefixadd = $view->translate('at');
    }
    $is_mobile = Engine_Api::_()->seaocore()->isMobile();
    //SEND FEED
    //if (!empty($location)) {
    //SELECT ACTION IDS
    if ($resource_type == 'album') {
      $select = $actionTable->select()->from($actionTable->info('name'))
              ->where("$actionTableName.object_id =?", $resource_id)
              ->where("$actionTableName.object_type =?", $resource_type)
              ->order("$actionTableName.action_id DESC")
      ;
    }
    $res = $actionTable->fetchRow($select);

    if (empty($res)) {
      if (!empty($location)) {
        $action = $actionTable->addActivity($viewer, $getItem, 'sitetagcheckin_location', null, array('prefixadd' => $prefixadd, 'checkin' => $checkin_params));
        if ($action) {
          $actionTable
                  ->attachActivity($action, $getItem);
        }
        $actionIdentity = $action->getIdentity();

        //IF EMPTY RESOURCE ID THEN SET LOCATION
        if (empty($resource_guid)) {

//          if (isset($checkin_params['vicinity'])) {
//            if (isset($checkin_params['name']) && $checkin_params['name'] && $checkin_params['name'] != $checkin_params['vicinity']) {
//              $checkin_params['label'] = $checkin_params['name'] . ', ' . $checkin_params['vicinity'];
//            } else {
//              $checkin_params['label'] = $checkin_params['vicinity'];
//            }
//          }
          $checkin_params['label'] = $checkin_params['vicinity'];
          if (!$is_mobile) {
            $notification_location = $view->htmlLink($view->url(array('guid' => $action->getGuid(), 'format' => 'smoothbox'), 'sitetagcheckin_viewmap', true), $checkin_params['label'], array('class' => 'smoothbox'));
          } else {
            $notification_location = $view->htmlLink($view->url(array('guid' => $action->getGuid()), 'sitetagcheckin_viewmap', true), $checkin_params['label']);
          }
        }
        $action->params = array_merge((array) $action->params, array('location' => $notification_location));
        $actionIdentity = $action->save();

        if ($resource_type == 'album_photo') {
          $content = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.content', 'everyone');
          if (!Engine_Api::_()->getApi('PhotoInLightbox', 'seaocore')->isLessThan417AlbumModule()) {
            $getAlbum = $photo->getAlbum();
            $objectParent = $getAlbum->getParent();
          } else {
            $getAlbum = $photo->getCollection();
            $objectParent = $getAlbum->getParent();
          }

          // Network
          if (in_array($content, array('everyone', 'networks'))) {
            if ($getAlbum instanceof User_Model_User && Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'network', 'view')) {
              $networkTable = Engine_Api::_()->getDbtable('membership', 'network');
              $ids = $networkTable->getMembershipsOfIds($getAlbum);
              $ids = array_unique($ids);
              foreach ($ids as $id) {
                Engine_Api::_()->sitetagcheckin()->insertPrivacyInStream('network', $id, $action);
              }
            } elseif ($objectParent instanceof User_Model_User && Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'owner_network', 'view')) {
              $networkTable = Engine_Api::_()->getDbtable('membership', 'network');
              $ids = $networkTable->getMembershipsOfIds($objectParent);
              $ids = array_unique($ids);
              foreach ($ids as $id) {
                Engine_Api::_()->sitetagcheckin()->insertPrivacyInStream('network', $id, $action);
              }
            }
          }

          // Members
          if ($getAlbum instanceof User_Model_User) {
            if (Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'member', 'view')) {
              Engine_Api::_()->sitetagcheckin()->insertPrivacyInStream('members', $getAlbum->getIdentity(), $action);
            }
          } else if ($objectParent instanceof User_Model_User) {
            // Note: technically we shouldn't do owner_member, however some things are using it
            if (Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'owner_member', 'view') ||
                    Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'parent_member', 'view')) {
              Engine_Api::_()->sitetagcheckin()->insertPrivacyInStream('members', $objectParent->getIdentity(), $action);
            }
          }

          // Registered
          if ($content == 'everyone' &&
                  Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'registered', 'view')) {
            Engine_Api::_()->sitetagcheckin()->insertPrivacyInStream('registered', 0, $action);
          }

          // Everyone
          if ($content == 'everyone' &&
                  Engine_Api::_()->authorization()->context->isAllowed($getAlbum, 'everyone', 'view')) {
            Engine_Api::_()->sitetagcheckin()->insertPrivacyInStream('everyone', 0, $action);
          }
        }
      }
    } else {
      //IF EMPTY RESOURCE ID THEN SET LOCATION
      if (empty($resource_guid)) {
        if ($type == 'just_use') {
          $notification_location = $checkin_params['label'];
        } else {
          if (isset($checkin_params['vicinity'])) {
            if (isset($checkin_params['name']) && $checkin_params['name'] && $checkin_params['name'] != $checkin_params['vicinity']) {
              $checkin_params['label'] = $checkin_params['name'] . ', ' . $checkin_params['vicinity'];
            } else {
              $checkin_params['label'] = $checkin_params['vicinity'];
            }
          }

          if (!$is_mobile) {
            $notification_location = $view->htmlLink($view->url(array('guid' => "activity_action_" . $res->action_id, 'format' => 'smoothbox'), 'sitetagcheckin_viewmap', true), $checkin_params['label'], array('class' => 'smoothbox'));
          } else {
            $notification_location = $view->htmlLink($view->url(array('guid' => "activity_action_" . $res->action_id), 'sitetagcheckin_viewmap', true), $checkin_params['label'], array());
          }
        }
      }
      $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
      if ($res->type == 'album_photo_new') {
        $res->type = "sitetagcheckin_album_photo_new";
        $streamTable->update(array('type' => "sitetagcheckin_album_photo_new"), array('action_id = ?' => $res->action_id));
      } elseif ($res->type == 'sitetagcheckin_album_photo_new') {
        if (empty($location)) {
          $res->type = "album_photo_new";
          $streamTable->update(array('type' => "album_photo_new"), array('action_id = ?' => $res->action_id));
        }
      }
      if (!empty($location)) {
        $res->params = array_merge((array) $res->params, array('prefixadd' => $prefixadd, 'location' => $notification_location, 'checkin' => $checkin_params));
      } else {
        $res->params = (array) $res->params;
      }
      $res->save();
      $actionIdentity = $res->action_id;
    }
    //}

    $checkinContentArray = array(
        'location_id' => $location_id,
        'type' => 'tagging',
        'item_id' => $actionIdentity,
        'item_type' => 'activity_action',
        'params' => $checkin_params,
        'event_date' => date('Y-m-d H:i:s'),
        'owner_id' => $viewer_id
    );
    $content_array = array(
        'resource_id' => $resource_id,
        'resource_type' => $resource_type,
        'object_id' => $resource_id,
        'object_type' => $resource_type,
        'action_id' => $actionIdentity,
    );

    //SAVE LOCATION
    $addLocation = $tableAddLocation->saveLocation(array_merge($content_array, $checkinContentArray));

    //FOR ALBUM TAGGING
    if (($resource_type == 'album' || $resource_type == 'advalbum_album') && !$useNoPhoto) {
      switch ($resource_type) {
        case 'album':
          $changeResourceType = 'album_photo';
          break;
        case 'advalbum_album':
          $changeResourceType = 'advalbum_photo';
          break;
      }

      //GET PHOTO TABLE
      $photoTable = Engine_Api::_()->getItemTable($changeResourceType);

      //GET COLUMN
      $col = current($photoTable->info("primary"));

      //SELECT 
      $select = $photoTable->select()->from($photoTable->info('name'), $col)->where('album_id =?', $resource_id);

      //GET ROWS
      $rows = $photoTable->fetchAll($select);

      //GET OBJECT IDS
      $objectIds = $tableAddLocation->getObjectIds($changeResourceType);

      //MAKE ARRAY OF RESOURC IDS
      $objectIdsArray = array();
      foreach ($objectIds as $key => $value) {
        $objectIdsArray[] = $value;
      }

      //SAVE LOCATION FOR RESOURCE IDS
      foreach ($rows as $key => $value) {
        if (in_array($value->photo_id, $objectIdsArray))
          continue;
        $content = array(
            'resource_id' => $value->photo_id,
            'resource_type' => $changeResourceType,
            'object_id' => $value->photo_id,
            'object_type' => $changeResourceType,
            'action_id' => '-' . $addLocation->action_id,
        );
        $tableAddLocation->saveLocation(array_merge($content, $checkinContentArray));
      }
    }

    //SEND NOTIFICATION WHEN THERE IS ANY TAGGED USER IN THE PHOTO
    $existingTagMaps = Engine_Api::_()->getDbtable('tags', 'core')->getTagMaps($getItem);
    $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    foreach ($existingTagMaps as $tagmap) {
      if ($tagmap->tag_id != $viewer_id) {
        $ownerObj = Engine_Api::_()->getItem('user', $tagmap->tag_id);
        $notificationTable->addNotification($ownerObj, $viewer, $getItem, "sitetagcheckin_tagged_location", array("location" => $notification_location, "label" => "photo"));
      }
    }
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
    
    public function openAddNewPhotosInLightbox() {
        return Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.open.lightbox.upload', 1);
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