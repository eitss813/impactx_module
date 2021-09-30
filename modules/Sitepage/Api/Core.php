<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010notIncludeSelf-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Api_Core extends Core_Api_Abstract
{

  protected $_privacy = array();  // $_privacy['guid']['user_id']['privacy_type']=$privacy;

  /**
   * Privacy settings base on level and package
   *
   * @param object $sitepage
   * @param string $privacy_type
   * @return int $is_manage_admin
   */

  public function isManageAdmin($sitepage, $privacy_type)
  {
    if( empty($sitepage) || empty($privacy_type) )
      return 0;
    $page_id = $sitepage->page_id;
    //page is declined then not edit page
    if( !empty($sitepage->declined) && ($privacy_type == "edit") ) {
      return 0;
    }

    if( $privacy_type == "view" && !$this->canViewPage($sitepage) ) {
      return 0;
    }

    if( $this->hasPackageEnable() ) {

      $packageInclude = array("tellafriend" => "tfriend", "print" => "print", "overview" => "overview", "map" => "map", "insights" => "insight", "service" => "service", /* "layout" => "layout", */ "contact_details" => "contact", "profile" => "profile", "sendupdate" => "sendupdate", "twitter" => "twitter");
      $packageOwnerModules = array("sitepageoffer" => "offer", "sitepageform" => "form", "sitepageinvite" => "invite", "sitepagebadge" => "badge", "sitepagelikebox" => "likebox", "sitepagemember" => "smecreate");
      $subModules = array("sitepagedocument" => "sdcreate", "sitepagenote" => "sncreate", "sitepagepoll" => "splcreate", "sitepageevent" => "secreate", "sitepagevideo" => "svcreate", "sitepagealbum" => "spcreate", "sitepagemusic" => "smcreate", "sitecrowdfunding" => "sprcreate", "sitemusic" => "samcreate");

      // $packageSubModule = $this->getEnableSubModules();
      //non sub modules
      $search_Key = array_search($privacy_type, $packageInclude);
      if( !empty($search_Key) ) {
        return $this->allowPackageContent($sitepage->package_id, $search_Key);
      }

      //owner base submodules
      $packageOwnerSubModule = @array_search($privacy_type, $packageOwnerModules);
      if( !empty($packageOwnerSubModule) ) {
        return $this->allowPackageContent($sitepage->package_id, "modules", $packageOwnerSubModule);
      }

      //owner base and also depeanded on viewer
      $subModule = @array_search($privacy_type, $subModules);
      if( !empty($subModule) ) {
        if( !$this->allowPackageContent($sitepage->package_id, "modules", $subModule) )
          return 0;
      }
    }else {

      $levelInclude = array("tellafriend" => "tfriend", "print" => "print", "overview" => "overview", "map" => "map", "insights" => "insight", "service" => "service", /* "layout" => "layout", */ "contact_details" => "contact", "profile" => "profile", "sendupdate" => "sendupdate", "twitter" => "twitter");
      $levelOwnerModules = array("sitepageoffer" => "offer", "sitepageform" => "form", "sitepageinvite" => "invite", "sitepagebadge" => "badge", "sitepagelikebox" => "likebox", "sitepagemember" => "smecreate");
      $levelPageBaseSubModule = array("sitepagedocument" => "sdcreate", "sitepagenote" => "sncreate", "sitepagepoll" => "splcreate", "sitepageevent" => "secreate", "sitepagevideo" => "svcreate", "sitepagealbum" => "spcreate", "sitepagemusic" => "smcreate");
      //non sub modules
      $search_Key = array_search($privacy_type, $levelInclude);
      if( !empty($search_Key) ) {
        $page_owner = Engine_Api::_()->getItem('user', $sitepage->owner_id);
        $can_edit = $this->getManageAdminPrivacyCache('sitepage_page', "level_" . $page_owner->level_id, $privacy_type);
        if( $can_edit == -1 ) {
          $can_edit = Engine_Api::_()->authorization()->getPermission($page_owner->level_id, 'sitepage_page', $privacy_type); //Engine_Api::_()->authorization()->isAllowed($sitepage, $page_owner, $privacy_type);
          $this->setManageAdminPrivacyCache('sitepage_page', "level_" . $page_owner->level_id, $privacy_type, $can_edit);
        }
        if( empty($can_edit) ) {
          return 0;
        } else {
          return 1;
        }
      }

      //owner base submodules
      $levelsubModule = @array_search($privacy_type, $levelOwnerModules);
      if( !empty($levelsubModule) ) {
        $page_owner = Engine_Api::_()->getItem('user', $sitepage->owner_id);
        $can_edit = $this->getManageAdminPrivacyCache('sitepage_page', "level_" . $page_owner->level_id, $privacy_type);
        if( $can_edit == -1 ) {
          $can_edit = Engine_Api::_()->authorization()->getPermission($page_owner->level_id, 'sitepage_page', $privacy_type);
          $this->setManageAdminPrivacyCache('sitepage_page', "level_" . $page_owner->level_id, $privacy_type, $can_edit);
        }
        if( empty($can_edit) ) {
          return 0;
        } else {
          return 1;
        }
      }

      //owner base and also depeanded on viewer
      $levelsubModule = @array_search($privacy_type, $levelPageBaseSubModule);
      if( !empty($levelsubModule) ) {
        $page_owner = Engine_Api::_()->getItem('user', $sitepage->owner_id);
        $can_edit = $this->getManageAdminPrivacyCache('sitepage_page', "level_" . $page_owner->level_id, $privacy_type);
        if( $can_edit == -1 ) {
          $can_edit = Engine_Api::_()->authorization()->getPermission($page_owner->level_id, 'sitepage_page', $privacy_type);
          $this->setManageAdminPrivacyCache('sitepage_page', "level_" . $page_owner->level_id, $privacy_type, $can_edit);
        }
        if( empty($can_edit) ) {
          return 0;
        }
      }
    }
    $existManageAdmin = 0;
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $manageAdminEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1);
    if( !empty($viewer_id) && !empty($manageAdminEnable) ) {
      $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
      $manageadminTableName = $manageadminTable->info('name');
      $select = $manageadminTable->select()
        ->from($manageadminTableName, 'manageadmin_id')
        ->where('user_id = ?', $viewer_id)
        ->where('page_id = ?', $page_id)
        ->limit(1);
      $row = $manageadminTable->fetchAll($select)->toArray();
      if( !empty($row[0]['manageadmin_id']) ) {
        $existManageAdmin = 1;
      } else {
        $existManageAdmin = 0;
      }
    }

    $is_manage_admin = 1;
    if( $existManageAdmin == 0 || $viewer_id == $sitepage->owner_id ) {
      if( empty($viewer_id) ) {
        $viewer = null;
        $viewer_id = 0;
      }
      $viewer_guid = 'user' . "_" . $viewer_id;
      $can_edit = $this->getManageAdminPrivacyCache($sitepage->getGuid(), $viewer_guid, $privacy_type);
      if( $can_edit == -1 ) {
        $can_edit = Engine_Api::_()->authorization()->isAllowed($sitepage, $viewer, $privacy_type);
        $this->setManageAdminPrivacyCache($sitepage->getGuid(), $viewer_guid, $privacy_type, $can_edit);
      }
      if( empty($can_edit) ) {
        $is_manage_admin = 0;
      }
    } elseif( $existManageAdmin == 1 && $viewer_id != $sitepage->owner_id ) {
      $page_owner = Engine_Api::_()->getItem('user', $sitepage->owner_id);
      $can_edit = $this->getManageAdminPrivacyCache($sitepage->getGuid(), $page_owner->getGuid(), $privacy_type);
      if( $can_edit == -1 ) {
        $can_edit = Engine_Api::_()->authorization()->isAllowed($sitepage, $page_owner, $privacy_type);
        $this->setManageAdminPrivacyCache($sitepage->getGuid(), $page_owner->getGuid(), $privacy_type, $can_edit);
      }
      if( empty($can_edit) ) {
        $is_manage_admin = 0;
      }
    }

    if( $privacy_type == "view" && $is_manage_admin ) {
      $is_manage_admin = $sitepage->isViewableByNetwork();
    }

    return $is_manage_admin;
  }

  public function setManageAdminPrivacyCache($index, $member_index, $privacy_type, $privacy)
  {
    return $this->_privacy[$index][$member_index][$privacy_type] = $privacy;
  }

  public function getManageAdminPrivacyCache($index, $member_index, $privacy_type)
  {
    //  print_r($this->_privacy);
    if( isset($this->_privacy[$index][$member_index][$privacy_type]) ) {
      return $this->_privacy[$index][$member_index][$privacy_type];
    } else {
      return -1;
    }
  }

  /**
   * viewer is page owner or page admin
   *
   * @param object $sitepage
   * @return bool $isPageOwnerFlage
   */
  public function isPageOwner($sitepage, $user = null)
  {
    if( empty($user) )
      $user = Engine_Api::_()->user()->getViewer();

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $isPageOwnerFlage = false;
    if( empty($viewer_id) )
      return $isPageOwnerFlage;
    if( $sitepage->owner_id == $viewer_id )
      return true;

    $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
    $manageadminTableName = $manageadminTable->info('name');
    $select = $manageadminTable->select()
      ->from($manageadminTableName, 'manageadmin_id')
      ->where('user_id = ?', $viewer_id)
      ->where('page_id = ?', $sitepage->page_id);
    $row = $manageadminTable->fetchRow($select);
    if( !empty($row) )
      $isPageOwnerFlage = true;

    return $isPageOwnerFlage;
  }

  /**
   * allow to page owner
   *
   * @param object $sitepage
   * @param string $privacy_type
   * @return bool $canDo
   */
  public function isPageOwnerAllow($sitepage, $privacy_type)
  {
    if( empty($sitepage) )
      return;
    $page_owner = Engine_Api::_()->getItem('user', $sitepage->owner_id);
    return (bool) $canDo = Engine_Api::_()->authorization()->getPermission($page_owner->level_id, 'sitepage_page', $privacy_type);
  }

  public function setDisabledType()
  {
    $modArray = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.mod.settings', 0));
    $modArrayType = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.mod.types', 0));
    if( !empty($modArray) ) {
      foreach( $modArray as $modName ) {
        $newModArray[] = strrev($modName);
      }
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.mod.settings', serialize($newModArray));
    }
    if( !empty($modArrayType) ) {
      foreach( $modArrayType as $modNameType ) {
        $newModArrayType[] = strrev($modNameType);
      }
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.mod.types', serialize($newModArrayType));
    }
  }

  /**
   * Get page id
   *
   * @param string $page_url
   * @return int $pageID
   */
  public function getPageId($page_url, $pageId = null)
  {
    $pageID = 0;
    if( !empty($page_url) ) {
      $sitepage_table = Engine_Api::_()->getItemTable('sitepage_page');
      $select = $sitepage_table->select()
        ->from($sitepage_table->info('name'), 'page_id')
        ->where('page_url = ?', $page_url);

      if( !empty($pageId) ) {
        $select->where('page_id != ?', $pageId);
      }

      $pageID = $select->limit(1)
        ->query()
        ->fetchColumn();
    }

    return $pageID;
  }

  /**
   * Get page url
   *
   * @param int $page_id
   * @return string $pageUrl
   */
  public function getPageUrl($page_id)
  {

    $pageUrl = 0;
    if( !empty($page_id) ) {
      $sitepage_table = Engine_Api::_()->getItemTable('sitepage_page');
      $pageUrl = $sitepage_table->select()
        ->from($sitepage_table->info('name'), 'page_url')
        ->where('page_id = ?', $page_id)
        ->limit(1)
        ->query()
        ->fetchColumn();
    }
    return $pageUrl;
  }

  /**
   * Get page list
   *
   * @param array $params
   * @param array $customParams
   * @return array $paginator;
   */
  public function getSitepagesPaginator($params = array(), $customParams = null)
  {

    $paginator = Zend_Paginator::factory($this->getSitepagesSelect($params, $customParams));
    if( !empty($params['page']) ) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if( !empty($params['limit']) ) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  /**
   * Get page select query
   *
   * @param array $params
   * @param array $customParams
   * @return string $select;
   */
  public function getSitepagesSelect($params = array(), $customParams = null)
  {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $table = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $rName = $table->info('name');

    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    if( !empty($moduleEnabled) ) {
      $membertable = Engine_Api::_()->getDbtable('membership', 'sitepage');
      $membertableName = $membertable->info('name');
    }

    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'search')->info('name');

    $locationTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
    $locationName = $locationTable->info('name');
    $select = $table->select()->setIntegrityCheck(false);

    if( isset($params['browse_page']) && !empty($params['browse_page']) ) {
      $columnsArray = array('page_id', 'title', 'page_url', 'body', 'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'modified_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'closed', 'email', 'website', 'phone', 'package_id', 'follow_count','show_name');

      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') ) {
        $columnsArray = array_merge(array('member_count'), $columnsArray);
        $columnsArray = array_merge(array('member_title'), $columnsArray);
        $columnsArray = array_merge(array('member_approval'), $columnsArray);
      }

      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge') )
        $columnsArray[] = 'badge_id';

      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer') )
        $columnsArray[] = 'offer';

      if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') ) {
        $columnsArray[] = 'review_count';
        $columnsArray[] = 'rating';
      }

      $select = $select->from($rName, $columnsArray);
    } else {
      $select = $select->from($rName);
    }

    //filtering with page id
    if(isset($params['selected_page_ids'])){
        if(!empty($params['selected_page_ids'])){
            $select = $select
                ->where('page_id IN (?)', $params['selected_page_ids']);
        }else{
            //if params were set and empty data means we should not return any data selecting -1 data
            $select = $select
                ->where('page_id IN (?)', array(-1));
        }
    }

    if( isset($params['type']) && !empty($params['type']) && $params['type'] != 'manage' ) {
      if( $params['type'] == 'browse' || $params['type'] == 'home' ) {
        $select = $select
         // ->where($rName . '.approved = ?', '1')
          ->where($rName . '.declined = ?', '0')
          ->where($rName . '.draft = ?', '1');

        if( !empty($moduleEnabled) && isset($params['type_location']) && $params['type_location'] != 'browseLocation' && $params['type_location'] != 'browsePage' && $params['type_location'] == 'profilebrowsePage' ) {
          $select = $select->join($membertableName, "$membertableName.page_id = $rName.page_id", array('user_id AS page_owner_id'));
          if( !empty($values['adminpages']) ) {
            $select = $select->where($membertableName . '.user_id = ?', $params['user_id']);
          }
          if( isset($params['onlymember']) && !empty($params['onlymember']) ) {
            $select = $select->where($rName . '.page_id IN (?)', (array) $params['onlymember']);
          }
          $select = $select->where($membertableName . '.active = ?', 1);
        }

        $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1);
        if( $stusShow == 0 ) {
          $select = $select
            ->where($rName . '.closed = ?', '0');
        }
        if( $this->hasPackageEnable() )
          $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      } elseif( $params['type'] == 'browse_home_zero' ) {
        $select = $select
          ->where($rName . '.closed = ?', '0')
       //   ->where($rName . '.approved = ?', '1')
          ->where($rName . '.declined = ?', '0')
          ->where($rName . '.draft = ?', '1');
        if( $this->hasPackageEnable() )
          $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
      $select->where($rName . ".search = ?", 1);
    }
    if( isset($customParams) ) {

      $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
      $coreversion = $coremodule->version;
      if( Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.7') == 1 ) {

        //PROCESS OPTIONS
        $tmp = array();
        foreach( $customParams as $k => $v ) {
          if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
            continue;
          } else if( false !== strpos($k, '_field_') ) {
            list($null, $field) = explode('_field_', $k);
            $tmp['field_' . $field] = $v;
          } else if( false !== strpos($k, '_alias_') ) {
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
        ->joinLeft($searchTable, "$searchTable.item_id = $rName.page_id", null);

      $searchParts = Engine_Api::_()->fields()->getSearchQuery('sitepage_page', $customParams);
      foreach( $searchParts as $k => $v ) {
        //$v = str_replace("%2C%20",", ",$v);
        $select->where("`{$searchTable}`.{$k}", $v);
      }
    }

    if( isset($params['sitepage_price']) && !empty($params['sitepage_price']) ) {

      if( (!empty($params['sitepage_price']['min']) && !empty($params['sitepage_price']['max']) ) ) {

        if( $params['sitepage_price']['max'] < $params['sitepage_price']['min'] ) {
          $min = $params['sitepage_price']['max'];
          $max = $params['sitepage_price']['min'];
        } else {
          $min = $params['sitepage_price']['min'];
          $max = $params['sitepage_price']['max'];
        }

        $select = $select->where($rName . '.price >= ?', $this->getPriceWithCurrency($min))->where($rName . '.price <= ?', $this->getPriceWithCurrency($max, 1, 1));
      }

      if( (empty($params['sitepage_price']['min']) && !empty($params['sitepage_price']['max']) ) ) {
        $select = $select->where($rName . '.price <= ?', $this->getPriceWithCurrency($params['sitepage_price']['max'], 1, 1));
      }

      if( (!empty($params['sitepage_price']['min']) && empty($param['sitepage_price']['max']) ) ) {
        $select = $select->where($rName . '.price >= ?', $this->getPriceWithCurrency($params['sitepage_price']['min'], 1, 1));
      }
    }

    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer') && isset($params['offer_type']) && !empty($params['offer_type']) ) {
      $offerTable = Engine_Api::_()->getDbtable('offers', 'sitepageoffer');
      $offerTableName = $offerTable->info('name');
      $today = date("Y-m-d H:i:s");
      $select->setIntegrityCheck(false)
        ->join($offerTableName, "$offerTableName.page_id = $rName.page_id", array(''))
        ->where("$offerTableName.end_settings = 0 OR ($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$today')");

      if( $params['offer_type'] == 'hot' ) {
        $select->where("$offerTableName.hotoffer = 1");
      } elseif( $params['offer_type'] == 'featured' ) {
        $select->where("$offerTableName.sticky = 1");
      }
    }

//     if (isset($params['sitepage_postalcode']) && empty($params['sitepage_postalcode']) && isset($params['locationmiles']) && empty($params['locationmiles'])) {
    //check for stret , city etc in location search.
    if( isset($params['sitepage_street']) && !empty($params['sitepage_street']) ) {
      $select->join($locationName, "$rName.page_id = $locationName.page_id   ", null);
      $select->where($locationName . '.address   LIKE ? ', '%' . $params['sitepage_street'] . '%');
    } if( isset($params['sitepage_city']) && !empty($params['sitepage_city']) ) {
      $select->join($locationName, "$rName.page_id = $locationName.page_id   ", null);
      $select->where($locationName . '.city = ?', $params['sitepage_city']);
    } if( isset($params['sitepage_state']) && !empty($params['sitepage_state']) ) {
      $select->join($locationName, "$rName.page_id = $locationName.page_id   ", null);
      $select->where($locationName . '.state = ?', $params['sitepage_state']);
    } if( isset($params['sitepage_country']) && !empty($params['sitepage_country']) ) {
      $select->join($locationName, "$rName.page_id = $locationName.page_id   ", null);
      $select->where($locationName . '.country = ?', $params['sitepage_country']);
    }
// 		} else {
// 			$select->join($locationName, "$rName.page_id = $locationName.page_id   ", null);
// 			$select->where($locationName . '.zipcode = ?', $params['sitepage_postalcode']);
// 		}

    if( !isset($params['sitepage_location']) && isset($params['locationSearch']) && !empty($params['locationSearch']) ) {
      $params['sitepage_location'] = $params['locationSearch'];

      if( isset($params['locationmilesSearch']) ) {
        $params['locationmiles'] = $params['locationmilesSearch'];
      }
    }

    if( (isset($params['sitepage_location']) && !empty($params['sitepage_location'])) || (!empty($params['formatted_address'])) ) {
      $enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximitysearch', 1);
      if( isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable)) ) {
        $longitude = 0;
        $latitude = 0;


        //check for zip code in location search.
        if( empty($params['Latitude']) && empty($params['Longitude']) ) {
          $selectLocQuery = $locationTable->select()->where('location = ?', $params['sitepage_location']);
          $locationValue = $locationTable->fetchRow($selectLocQuery);

          if( empty($locationValue) ) {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['sitepage_location'], 'module' => 'Directory / Pages'));
            if( !empty($locationResults['latitude']) && !empty($locationResults['longitude']) ) {
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

        $radius = $params['locationmiles']; //in miles

        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
        if( !empty($flage) ) {
          $radius = $radius * (0.621371192);
        }

        //Start We have follow this code from event plugin.
        // $latitudeRadians = deg2rad($latitude);
        $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";

        $select->join($locationName, "$rName.page_id = $locationName.page_id   ", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
        $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ")";
        $select->where($sqlstring);
        $select->order("distance");
      } else {
// 				if ($params['sitepage_postalcode'] == 'postalCode') {
// 					$select->join($locationName, "$rName.page_id = $locationName.page_id", null);
// 					$select->where("`{$locationName}`.formatted_address LIKE ? ", "%" . $params['formatted_address'] . "%");
// 				}
// 				else {
        $select->join($locationName, "$rName.page_id = $locationName.page_id", null);
        $select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['sitepage_location']) . "%");
        //}
      }
    } elseif( (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagegeolocation') && isset($params['has_currentlocation']) && !empty($params['has_currentlocation']) && !empty($params['latitude']) && !empty($params['longitude']) ) {
      $radius = Engine_Api::_()->getApi('settings', 'core')->getSetting('sgl.geolocation.range', 100); // in miles
      $latitude = $params['latitude'];
      $longitude = $params['longitude'];
      $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
      if( !empty($flage) ) {
        $radius = $radius * (0.621371192);
      }
      //  $latitudeRadians = deg2rad($latitude);
      $latitudeSin = "sin(radians($latitude))";
      $latitudeCos = "cos(radians($latitude))";
      $select->join($locationName, "$rName.page_id = $locationName.page_id   ", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
      $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
      $sqlstring .= ")";
      $select->where($sqlstring);
      $select->order("distance");
    }

    //Start Network work
    if( !empty($params['type']) ) {
      if( $params['type'] == 'browse' || $params['type'] == 'home' || $params['type'] == 'browse_home_zero' ) {

        $select = $table->getNetworkBaseSql($select, array('browse_network' => (isset($params['show']) && $params['show'] == "3")));
      }
    }
    //End Network work

    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    if( isset($params['type_location']) && $params['type_location'] != 'profilebrowsePage' ) {
      if( !empty($params['user_id']) && is_numeric($params['user_id']) ) {
        $select->where($rName . '.owner_id = ?', $params['user_id']);
      }
    }

    if( !empty($params['user']) && $params['user'] instanceof User_Model_User ) {
      $select->where($rName . '.owner_id = ?', $params['user']->getIdentity());
    }

    if( (isset($params['show']) && $params['show'] == "4" ) ) {
      $likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $select->setIntegrityCheck(false)
        ->join($likeTableName, "$likeTableName.resource_id = $rName.page_id")
        ->where($likeTableName . '.poster_type = ?', 'user')
        ->where($likeTableName . '.poster_id = ?', $viewer_id)
        ->where($likeTableName . '.resource_type = ?', 'sitepage_page');
    }

    if( (isset($params['show']) && $params['show'] == "5" ) ) {
      $select->where($rName . '.featured = ?', 1);
    }

    if( !empty($params['users']) ) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($rName . '.owner_id in (?)', new Zend_Db_Expr($str));
    }

    if( empty($params['users']) && isset($params['show']) && $params['show'] == '2' ) {
      $select->where($rName . '.owner_id = ?', '0');
    }
    if( !empty($params['tag']) ) {
      $select
        ->setIntegrityCheck(false)
        ->joinLeft($tmName, "$tmName.resource_id = $rName.page_id")
        ->where($tmName . '.resource_type = ?', 'sitepage_page')
        ->where($tmName . '.tag_id = ?', $params['tag']);
    }

// 		if ($params['widget'] == 'locationsearch') {
// 			$select->where($rName . ".location != ?", '');
// 		}

    if( isset($params['adminpages']) ) {
      $str = (string) ( is_array($params['adminpages']) ? "'" . join("', '", $params['adminpages']) . "'" : $params['adminpages'] );
      $select->where($rName . '.page_id in (?)', new Zend_Db_Expr($str));
    }

    if( isset($params['adminjoinedpages']) ) {
      $str = (string) ( is_array($params['adminjoinedpages']) ? "'" . join("', '", $params['adminjoinedpages']) . "'" : $params['adminjoinedpages'] );
      $select->where($rName . '.page_id in (?)', new Zend_Db_Expr($str));
    }

    if( isset($params['notIncludeSelfPages']) && !empty($params['notIncludeSelfPages']) ) {
      $select->where($rName . '.owner_id != ?', $params['notIncludeSelfPages']);
    }

    if( !empty($params['category']) ) {
      $select->where($rName . '.category_id = ?', $params['category']);
    }

    if( !empty($params['category_id']) ) {
      $select->where($rName . '.category_id = ?', $params['category_id']);
    }

    if( (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge') ) {
      if( !empty($params['badge_id']) ) {
        $select->where($rName . '.badge_id = ?', $params['badge_id']);
      }
    }

    if( !empty($params['profile_type']) ) {
      $select->where($rName . '.profile_type = ?', $params['profile_type']);
    }

    if( !empty($params['subcategory']) ) {
      $select->where($rName . '.subcategory_id = ?', $params['subcategory']);
    }

    if( !empty($params['subcategory_id']) ) {
      $select->where($rName . '.subcategory_id = ?', $params['subcategory_id']);
    }

    if( !empty($params['subsubcategory']) ) {
      $select->where($rName . '.subsubcategory_id = ?', $params['subsubcategory']);
    }

    if( !empty($params['subsubcategory_id']) ) {
      $select->where($rName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
    }

    if( isset($params['closed']) && $params['closed'] != "" ) {
      $select->where($rName . '.closed = ?', $params['closed']);
    }

    //Could we use the search indexer for this?
    if( !empty($params['search']) ) {

      $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
      $select
        ->setIntegrityCheck(false)
        ->joinLeft($tmName, "$tmName.resource_id = $rName.page_id and " . $tmName . ".resource_type = 'sitepage_page'", null)
        ->joinLeft($tagName, "$tagName.tag_id = $tmName.tag_id", array($tagName . ".text"));
      //$params['search'] = str_replace("%20"," ",$params['search']);
      $select->where($rName . ".title LIKE ? OR " . $rName . ".body LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['search'] . '%');
    }

    if( !empty($params['start_date']) ) {
      $select->where($rName . ".creation_date > ?", date('Y-m-d', $params['start_date']));
    }

    if( !empty($params['end_date']) ) {
      $select->where($rName . ".creation_date < ?", date('Y-m-d', $params['end_date']));
    }

    if( !empty($params['has_photo']) ) {
      $select->where($rName . ".photo_id > ?", 0);
    }

    if( !empty($params['has_review']) ) {
      $select->where($rName . ".review_count > ?", 0);
    }

    if( (isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] != 'all' && $_GET['alphabeticsearch'] != '@' ) ) {
      $select->where($rName . ".title LIKE ?", $_GET['alphabeticsearch'] . '%');
    } elseif( isset($_GET['alphabeticsearch']) && ($_GET['alphabeticsearch'] == 'all' && $_GET['alphabeticsearch'] != '@') ) {
//      $select->group($rName . '.page_id');
//      return $select;
    } elseif( isset($_GET['alphabeticsearch']) && ($_GET['alphabeticsearch'] == '@' && $_GET['alphabeticsearch'] != 'all') ) {
      $select->where($rName . ".title REGEXP '^[0-9]'");
    }
    $select->group($rName . '.page_id');

    if( !empty($params['type']) && empty($params['orderby']) ) {
      if( $params['type'] == 'browse' || $params['type'] == 'manage' ) {
        $order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.browseorder', 1);
        switch( $order ) {
          case "1":
            $select->order($rName . '.creation_date DESC');
            break;
          case "2":
            $select->order($rName . '.view_count DESC');
            break;
          case "3":
            $select->order($rName . '.title');
            break;
          case "4":
            $select->order($rName . '.sponsored' . ' DESC');
            break;
          case "5":
            $select->order($rName . '.featured' . ' DESC');
            break;
          case "6":
            $select->order($rName . '.sponsored' . ' DESC');
            $select->order($rName . '.featured' . ' DESC');
            break;
          case "7":
            $select->order($rName . '.featured' . ' DESC');
            $select->order($rName . '.sponsored' . ' DESC');
            break;
        }
      }
    } else {
      if( !empty($params['orderby']) && $params['orderby'] == "title" ) {
        $select->order($rName . '.' . $params['orderby']);
      } elseif( isset($params['orderby']) && !empty($params['orderby']) )
        $select->order($rName . '.' . $params['orderby'] . ' DESC');
    }
    $select->order($rName . '.creation_date DESC'); //echo $select;die;

      // should show approved and not expired pages
    $select->where('closed = ?', '0')
     // ->where('approved = ?', '1')
      ->where('declined = ?', '0')
      ->where('draft = ?', '1')
      ->where('expiration_date  > ?', date("Y-m-d H:i:s"));

    return $select;
  }

  public function getPackageAuthInfo($modulename)
  {
    $sitepageModSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.mod.settings', 0);
    $sitepageModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.mod.types', 0);
    $modSecondArray = $modFirstArray = array();
    if( !empty($sitepageModSetting) ) {
      $modFirstArray = unserialize($sitepageModSetting);
    }
    if( !empty($sitepageModType) ) {
      $modSecondArray = unserialize($sitepageModType);
    }
    $modArray = array_merge($modFirstArray, $modSecondArray);
    return in_array(strrev($modulename), $modArray);
  }

  /**
   * Get Page View Link
   *
   * @param int $page_id
   * @param int $owner_id
   * @param string $slug
   * @return link
   */
  public function getHref($page_id, $owner_id, $slug = null)
  {

    $page_url = Engine_Api::_()->sitepage()->getPageUrl($page_id);
    $params = array_merge(array('page_url' => $page_url));
    $urlO = Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, 'sitepage_entry_view', true);

    //SITEPAGEURL WORK START
    $sitepageUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageurl');
    if( !empty($sitepageUrlEnabled) ) {
      $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manifestUrlS', "pageitem");
      $banneUrlArray = Engine_Api::_()->sitepage()->getBannedPageUrls();
      $page_likes = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.likelimit.forurlblock', "5");
      $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.change.url', 1);

      $sitepageObject = Engine_Api::_()->getItem('sitepage_page', Engine_Api::_()->sitepage()->getPageId($page_url));
      $replaceStr = str_replace("/" . $routeStartS . "/", "/", $urlO);
      if( (!empty($change_url)) && ($sitepageObject->like_count >= $page_likes) && !in_array($page_url, $banneUrlArray) && !empty($sitepageObject) ) {
        $urlO = $replaceStr;
      }
    }
    return $urlO;
  }

//

  /**
   * GET LINK OF PHOTO VIEW PAGE
   *
   * @param object $image
   * @param array $params
   * @return link
   */
  public function getHreflink($image, $params = array())
  {
    $params = array_merge(array(
      'route' => 'sitepage_imagephoto_specific',
      'reset' => true,
      'controller' => 'photo',
      'action' => 'view',
      'photo_id' => $image->getIdentity(),
      'album_id' => $image->collection_id,
      ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, $reset);
  }

  /**
   * Get Truncation String
   *
   * @param string $string
   * @param int $length
   * @return string $string
   */
  public function truncation($string, $length = null)
  {

    if( empty($length) ) {
      $length = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 16);
    }

    $string = strip_tags($string);
    return $string = Engine_String::strlen($string) > $length ? Engine_String::substr($string, 0, ($length - 3)) . '...' : $string;
  }

  public function setModPackageInfo($modulename, $mod = null)
  {
    $modPackageInfo = $this->isEnabledModPackage($mod);
    $modArray = array();
    if( !empty($modPackageInfo) ) {
      $modArray = unserialize($modPackageInfo);
      if( !empty($modArray) ) {
        $inArray1 = in_array($modulename, $modArray);
        $inArray2 = in_array(strrev($modulename), $modArray);
      }
    }

    if( !empty($inArray1) && empty($inArray2) ) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.mod.settings', 'a:0:{}');
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.mod.types', 'a:0:{}');
      Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitepage.edit.package');
      $modArray = array();
    }
    if( !empty($inArray2) && empty($inArray1) ) {
      return;
    }

    $modArray[] = strrev($modulename);
    $arrayName = 0;
    $mod ? $arrayName = strrev('sepyt.dom.egapetis') : $arrayName = strrev('sgnittes.dom.egapetis');
    Engine_Api::_()->getApi('settings', 'core')->setSetting($arrayName, serialize($modArray));
    return;
  }

  /**
   * Check location is enable
   *
   * @param array $params
   * @return int $check
   */
  public function enableLocation($params = array())
  {
      $check = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.location', 1);

      if( !empty($check) ) {
        $check = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);
      }

    return $check;
  }

  /**
   * THIS FUNCTION SHOW PEOPLE LIKES OR FRIEND LIKES.
   *
   * @param string $call_status
   * @param string $resource_type
   * @param int $resource_id
   * @param int $user_id
   * @param int $search
   * @return ALL RESULTS
   */
  public function friendPublicLike($call_status, $resource_type, $resource_id, $user_id, $search)
  {

    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $likeTableName = $likeTable->info('name');
    $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
    $membershipTableName = $membershipTable->info('name');
    $userTable = Engine_Api::_()->getItemTable('user');
    $userTableName = $userTable->info('name');

    $sub_status_select = $userTable->select()
      ->setIntegrityCheck(false)
      ->from($likeTableName, array('poster_id'))
      ->where($likeTableName . '.resource_type = ?', $resource_type)
      ->where($likeTableName . '.resource_id = ?', $resource_id)
      ->where($likeTableName . '.poster_id != ?', 0)
      ->where($userTableName . '.displayname LIKE ?', '%' . $search . '%')
      ->order('	like_id DESC');
    if( $call_status == 'friend' ) {

      $sub_status_select->joinInner($membershipTableName, "$membershipTableName . user_id = $likeTableName . poster_id", NULL)
        ->joinInner($userTableName, "$userTableName . user_id = $membershipTableName . user_id")
        ->where($membershipTableName . '.resource_id = ?', $user_id)
        ->where($membershipTableName . '.active = ?', 1)
        ->where($likeTableName . '.poster_id != ?', $user_id);
    } else if( $call_status == 'public' ) {

      $sub_status_select->joinInner($userTableName, "$userTableName . user_id = $likeTableName . poster_id");
    }
    return Zend_Paginator::factory($sub_status_select);
  }

  /**
   * number of page like
   *
   * @param string $RESOURCE_TYPE
   * @param int $RESOURCE_ID
   * @param int $LIMIT
   */
  public function pageLike($RESOURCE_TYPE, $RESOURCE_ID, $LIMIT)
  {

    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $likeTableName = $likeTable->info('name');
    $select = $likeTable->select()
      ->from($likeTableName, array('poster_id'))
      ->where('resource_type = ?', $RESOURCE_TYPE)
      ->where('resource_id = ?', $RESOURCE_ID)
      ->order('like_id DESC')
      ->limit($LIMIT);
    $fetch_sub = $select->query()->fetchAll();
    return $fetch_sub;
  }

  /**
   * Function for showing 'Liked Link'.This function use in the like button.
   *
   * @param string $RESOURCE_TYPE
   * @param int $RESOURCE_ID
   */
  public function checkAvailability($RESOURCE_TYPE, $RESOURCE_ID)
  {

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

  /**
   * Check number of like by friend
   *
   * @param string  $RESOURCE_TYPE
   * @param int  $RESOURCE_ID
   */
  public function friendNumberOfLike($RESOURCE_TYPE, $RESOURCE_ID)
  {

    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $likeTableName = $likeTable->info('name');
    $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
    $membershipTableName = $membershipTable->info('name');
    $select = $likeTable->select()
      ->from($likeTableName, array('COUNT(' . $likeTableName . '.like_id) AS like_count'))
      ->joinInner($membershipTableName, "$membershipTableName . user_id = $likeTableName . poster_id", NULL)
      //->joinInner($userName, "$userName.user_id = $likeTableName.poster_id", NULL)
      ->where($membershipTableName . '.resource_id = ?', $user_id)
      ->where($membershipTableName . '.active = ?', 1)
      ->where($likeTableName . '.resource_type = ?', $RESOURCE_TYPE)
      ->where($likeTableName . '.resource_id = ?', $RESOURCE_ID)
      ->where($likeTableName . '.poster_id != ?', $user_id)
      ->where($likeTableName . '.poster_id != ?', 0)
      ->group($likeTableName . '.resource_id');
    $fetch_count = $select->query()->fetchAll();
    if( !empty($fetch_count) ) {
      return $fetch_count[0]['like_count'];
    } else {
      return 0;
    }
  }

  /**
   * Check number of like
   *
   * @param string $RESOURCE_TYPE
   * @param int  $RESOURCE_ID
   */
  public function numberOfLike($RESOURCE_TYPE, $RESOURCE_ID)
  {

    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $likeTableName = $likeTable->info('name');
    $select = $likeTable->select()
      ->from($likeTableName, array('COUNT(' . $likeTableName . '.like_id) AS like_count'))
      ->where('resource_type = ?', $RESOURCE_TYPE)
      ->where('resource_id = ?', $RESOURCE_ID)
      ->where('poster_id != ?', 0)
      ->group('resource_id');
    $fetch_count = $select->query()->fetchAll();
    if( !empty($fetch_count) ) {
      return $fetch_count[0]['like_count'];
    } else {
      return 0;
    }
  }

  public function getEnabledSubModules()
  {

    $coreTable = Engine_Api::_()->getDbtable('modules', 'core');
    ///////////////////START FOR INRAGRATION WORK WITH OTHER PLUGIN./////////
    $sitepageintegrationEnabled = $coreTable->isModuleEnabled('sitepageintegration');
    if( !empty($sitepageintegrationEnabled) ) {
      $mixSettingsResults = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getIntegrationItems(null, $isDashboard = true);
    }
    ///////////////////END FOR INRAGRATION WORK WITH OTHER PLUGIN./////////


    if( $coreTable->isModuleEnabled('sitepagealbum') ||
      $coreTable->isModuleEnabled('sitepagevideo') ||
      $coreTable->isModuleEnabled('sitepagepoll') ||
      $coreTable->isModuleEnabled('sitepagenote') ||
      $coreTable->isModuleEnabled('sitepageevent') ||
      $coreTable->isModuleEnabled('sitepagedocument') ||
      $coreTable->isModuleEnabled('sitepagereviews') ||
      $coreTable->isModuleEnabled('sitepagediscussion') ||
      $coreTable->isModuleEnabled('sitepageoffer') ||
      $coreTable->isModuleEnabled('sitepageform') ||
      $coreTable->isModuleEnabled('sitepageinvite') ||
      $coreTable->isModuleEnabled('sitepagemember') ||
      $coreTable->isModuleEnabled('sitepagemusic') ||
      (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ||
      !empty($mixSettingsResults) || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ||
      (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ||
      (Engine_Api::_()->hasModuleBootstrap('sitemusic') && Engine_Api::_()->getDbtable('modules', 'sitemusic')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ||
      (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration') && Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
      return $page_redirect = 1;
    } else {
      return $page_redirect = 0;
    }
  }

  //Package Related Functions

  /**
   * Get List of enabled submodule for package
   *
   */
  public function getEnableSubModules($tempPackages = null)
  {

    $enableSubModules = array();

    $includeModules = array("sitepagedocument" => 'Documents', "sitepageoffer" => 'Offers', "sitepageform" => "Form", "sitepagediscussion" => "Discussions", "sitepagenote" => "Notes", "sitepagealbum" => "Photos", "sitepagevideo" => "Videos", "sitepageevent" => "Events", "sitepagepoll" => "Polls", "sitepageinvite" => "Invite & Promote", "sitepagebadge" => "Badges", "sitepagelikebox" => "External Badge", "sitepagemusic" => "Music", "sitepagemember" => "Member", "siteevent" => "Events", "sitevideo" => "Videos", "document" => "Documents", "sitecrowdfunding" => "Crowdfunding", "sitemusic" => "Advanced Music");

    $enableAllModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $enableModules = array_intersect(array_keys($includeModules), $enableAllModules);


    foreach( $enableModules as $module ) {
      if( $this->isPluginActivate($module) ) {
        if( $module == 'siteevent' ) {
          if( (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')) ) ) {
            $enableSubModules['sitepageevent'] = $includeModules['sitepageevent'];
          }
        } elseif( $module == 'sitevideo' ) {
          if( (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')) ) ) {
            $enableSubModules['sitepagevideo'] = $includeModules['sitepagevideo'];
          }
        } elseif( $module == 'document' ) {
          if( (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')) ) ) {
            $enableSubModules['sitepagedocument'] = $includeModules['sitepagedocument'];
          }
        } elseif( $module == 'sitecrowdfunding' ) {
          if( (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration') && Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')) ) ) {
            $enableSubModules['sitecrowdfunding'] = $includeModules['sitecrowdfunding'];
          }
        } elseif( $module == 'sitemusic' ) {
          if( (Engine_Api::_()->hasModuleBootstrap('sitemusic') && Engine_Api::_()->getDbtable('modules', 'sitemusic')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')) ) ) {
            $enableSubModules['sitemusic'] = $includeModules['sitemusic'];
          }
        } else {
          $enableSubModules[$module] = $includeModules[$module];
        }
      }
    }

    //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
    $sitepageintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration');
    if( !empty($sitepageintegrationEnabled) ) {
      $mixResults = Engine_Api::_()->getDbtable('mixsettings', 'sitepageintegration')->getIntegrationItems(null, $isDashboard = true);

      $mixSettings = array();
      $title = '';
      foreach( $mixResults as $modName ) {
        if( $modName['resource_type'] == 'list_listing' ) {
          $title = "Listings";
        } elseif( $modName['resource_type'] == 'sitereview_listing' ) {
          if( $tempPackages == 'adminPackages' ) {
            $title = "Reviews" . ' - ' . $modName['item_title'];
          } else {
            $title = $modName['item_title'];
          }
        } elseif( $modName['resource_type'] == 'sitebusiness_business' ) {
          $title = "Businesses";
        } elseif( $modName['resource_type'] == 'sitegroup_group' ) {
          $title = "Groups";
        } elseif( $modName['resource_type'] == 'document' ) {
          $title = "Documents";
        } elseif( $modName['resource_type'] == 'folder' ) {
          $title = "Folder";
        } elseif( $modName['resource_type'] == 'quiz' ) {
          $title = "Quiz";
        } elseif( $modName['resource_type'] == 'sitefaq_faq' ) {
          $title = "Faqs";
        } elseif( $modName['resource_type'] == 'sitetutorial_tutorial' ) {
          $title = "Tutorials";
        } elseif( $modName['resource_type'] == 'sitestoreproduct_product' ) {
          $title = "Store Products";
        }
        $mixSettings[$modName['resource_type'] . '_' . $modName['listingtype_id']] = $title;
      }

      $enableSubModules = array_merge($enableSubModules, $mixSettings);
    }
    //END FOR INRAGRATION WORK WITH OTHER PLUGIN.

    asort($enableSubModules);

    $enableSubModulesKeys = array_keys($enableSubModules);
    foreach( $enableSubModules as $resource_type => $item_title ) {
      if( strpos($resource_type, 'sitereview_listing_link') !== false ) {
        $tempString = str_replace('link', '', $resource_type);
        if( !in_array($tempString, $enableSubModulesKeys) ) {
          $enableSubModules[$tempString] = $item_title;
        }
        unset($enableSubModules[$resource_type]);
      }
    }

    return $enableSubModules;
  }

  /**
   * Check package is enable or not for site
   * @return bool
   */
  public function hasPackageEnable()
  {
    return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 1);
  }

  /**
   * Allow contect for perticuler package
   * @params $type : which check
   * $params $package_id : Id of page
   * $params $params : array some extra
   * */
  public function allowPackageContent($package_id, $type = null, $subModuleName = null)
  {

    if( !$this->hasPackageEnable() )
      return;
    $flage = false;
    if( Engine_Api::_()->core()->hasSubject('sitepage_page') ) {
      $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

      if( !empty($sitepage->pending) && !$this->isPageOwner($sitepage) ) {
        return $flage;
      }
    }
    $package = Engine_Api::_()->getItem('sitepage_package', $package_id);

    if( empty($package) )
      return $flage;

    switch( $type ) {
      case "modules":

        $includeArray = $this->getEnableSubModules();

        $modulesArray = unserialize($package->modules);
        if( empty($modulesArray) )
          $modulesArray = array();

        if( isset($includeArray[$subModuleName]) && @in_array($subModuleName, $modulesArray) ) {
          $flage = true;
        }
        break;
      default:
        if( isset($package->$type) && !empty($package->$type) )
          $flage = true;
        break;
    }

    return $flage;
  }

  /**
   * Get Page Profile Fileds level base on package
   * @params int $page_id : Id of page
   * @return bool
   * */
  public function getPackageProfileLevel($page_id = null)
  {
    if( !$this->hasPackageEnable() )
      return;
    $package = null;
    $page = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if( !empty($page) ) {
      $package = $page->getPackage();
    }
    if( !empty($package) )
      return $package->profile;
    else
      return 0;
  }

  /**
   * Get Page Profile Fileds If package set some fields
   * @params int $page_id : Id of page
   * @return array : profile fields
   * */
  public function getProfileFields($page_id = null)
  {
    $page = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if( !empty($page) ) {
      $package = $page->getPackage();
      return unserialize($package->profilefields);
    }
  }

  /**
   * Get Page Profile Fileds If package selected fields Id
   * @params int $page_id : Id of page
   * @return array : profile fields
   * */
  public function getSelectedProfilePackage($page_id = null)
  {
    $profileType = array();
    $profileType[""] = "";
    $page = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if( !empty($page) ) {
      $package = $page->getPackage();
      $profile = unserialize($package->profilefields);

      foreach( $profile as $value ) {
        $tc = @explode("_", $value);
        $profileType[$tc['1']] = $tc['1'];
      }
    }
    return array_unique($profileType);
  }

  /**
   * Get Page Profile Fileds  level base on level
   * @params int $level_id : level id of page owner
   * @return array : profile fields
   * */
  public function getLevelProfileFields($level_id)
  {
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $profileFields = $permissionsTable->getAllowed('sitepage_page', $level_id, array("profilefields"));
    return unserialize($profileFields['profilefields']);
  }

  /**
   * Get Page Profile Fileds If owner level selected fields Id
   * @params int $level_id : Level Id of page owner
   * @return array : profile fields
   * */
  public function getSelectedProfileLevel($level_id)
  {

    $profile = $this->getLevelProfileFields($level_id);
    $profileType = array();
    foreach( $profile as $value ) {
      $tc = @explode("_", $value);
      $profileType[$tc['1']] = $tc['1'];
    }

    return array_unique($profileType);
  }

  /**
   * Send emails for perticuler page
   * @params $type : which mail send
   * $params $pageId : Id of page
   * */
  public function sendMail($type, $pageId)
  {

    if( empty($type) || empty($pageId) ) {
      return;
    }
    $page = Engine_Api::_()->getItem('sitepage_page', $pageId);
    $mail_template = null;
    if( !empty($page) ) {

      $owner = Engine_Api::_()->user()->getUser($page->owner_id);
      switch( $type ) {
        case "APPROVAL_PENDING":
          $mail_template = 'sitepage_page_approval_pending';
          break;
        case "EXPIRED":
          if( !$this->hasPackageEnable() )
            return;
          if( $page->getPackage()->isFree() )
            $mail_template = 'sitepage_page_expired';
          else
            $mail_template = 'sitepage_page_renew';
          break;
        case "OVERDUE":
          $mail_template = 'sitepage_page_overdue';
          break;
        case "CANCELLED":
          $mail_template = 'sitepage_page_cancelled';
          break;
        case "ACTIVE":
          $mail_template = 'sitepage_page_active';
          break;
        case "PENDING":
          $mail_template = 'sitepage_page_pending';
          break;
        case "REFUNDED":
          $mail_template = 'sitepage_page_refunded';
          break;
        case "APPROVED":
          $mail_template = 'sitepage_page_approved';
          break;
        case "DISAPPROVED":
          $mail_template = 'sitepage_page_disapproved';
          break;
        case "DECLINED":
          $mail_template = 'sitepage_page_declined';
          break;
        case "RECURRENCE":
          $mail_template = 'sitepage_page_recurrence';
          break;
      }
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, $mail_template, array(
        'site_title' => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1),
        'page_title' => ucfirst($page->getTitle()),
        'page_description' => ucfirst($page->body),
        'page_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page->page_url), 'sitepage_entry_view', true) . '"  >' . ucfirst($page->getTitle()) . ' </a>',
        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page->page_url), 'sitepage_entry_view', true),
      ));
    }
  }

  /**
   * Check here that show payment link or not
   * $params $pageId : Id of page
   * @return bool $showLink
   * */
  public function canShowPaymentLink($page_id)
  {
    if( !$this->hasPackageEnable() )
      return;

    $showLink = true;
    $page = Engine_Api::_()->getItem('sitepage_page', $page_id);

    if( !empty($page->declined) ) {
      return (bool) false;
    }
    if( !empty($page) ) {
      if( !$this->isPageOwner($page) ) {
        return (bool) false;
      }
      $package = $page->getPackage();
      if( $package->isFree() ) {
        return (bool) false;
      }

      if( empty($page->expiration_date) || $page->expiration_date === "0000-00-00 00:00:00" ) {
        return (bool) true;
      }

      if( $page->status != "initial" && $page->status != "overdue" ) {
        return (bool) false;
      }

      if( ($package->isOneTime()) && !$package->hasDuration() && !empty($page->approved) ) {
        return false;
      }
    } else {
      $showLink = false;
    }
    return (bool) $showLink;
  }

  /**
   * Check here that show renew link or not
   * $params $pageId : Id of page
   * @return bool $showLink
   * */
  public function canShowRenewLink($page_id)
  {
    if( !$this->hasPackageEnable() )
      return;
    $showLink = false;
    $page = Engine_Api::_()->getItem('sitepage_page', $page_id);

    if( !empty($page->declined) ) {
      return (bool) false;
    }
    if( !empty($page) ) {
      if( !$this->isPageOwner($page) ) {
        return (bool) false;
      }
      $package = $page->getPackage();

      if( !$package->isOneTime() || $package->isFree() || (!empty($package->level_id) && !in_array($page->getOwner()->level_id, explode(",", $package->level_id))) ) {
        return (bool) false;
      }
      if( $package->renew ) {
        if( !empty($page->expiration_date) && $page->status != "initial" && $page->status != "overdue" ) {
          $diff_days = round((strtotime($page->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
          if( $diff_days <= $package->renew_before || $page->expiration_date <= date('Y-m-d H:i:s') ) {
            return (bool) true;
          }
        }
      }
    }
    return (bool) $showLink;
  }

  /**
   * Check here that show renew link  or not for admin
   * $params $pageId : Id of page
   * @return bool $showLink
   * */
  public function canAdminShowRenewLink($page_id)
  {
    if( !$this->hasPackageEnable() )
      return false;

    $showLink = false;
    $page = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if( !empty($page) ) {
      if( !empty($page->approved) && $page->expiration_date !== "2250-01-01 00:00:00" )
        $showLink = true;
    }
    return (bool) $showLink;
  }

  /**
   * DISAPROVED AFTER EXPIRY PAGE THIS IS USE ONLY FOR ENABLE PACKAGE MENAGEMENT
   * @params array $params
   * */
  public function updateExpiredPages($params = array())
  {

//PACKAGE MANAGMENT NOT ENABLE
    if( !$this->hasPackageEnable() )
      return;

    $table = Engine_Api::_()->getDbtable('pages', 'sitepage');

    $rName = $table->info('name');
//LIST FOR PAGES WHICH ARE EXPIRIED NOW AND SEND MAIL
    $select = $table->select()
      ->from($rName, array('page_id'))
      ->where('status <>  ?', 'expired')
      ->where('approved = ?', '1')
      ->where('expiration_date <= ?', date('Y-m-d H:i:s'));
    //START PAGE-EVENT CODE

    $sitepageeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
    $siteeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
    $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
    $documentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document');
    $documentintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('documentintegration');
    $sitecrowdfundingEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding');
    $sitemusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic');
    foreach( $table->fetchAll($select) as $page ) {
      $this->sendMail("EXPIRED", $page->page_id);
    }

//UPDATE THE STATUS
    $table->update(array(
      'approved' => 0,
      'status' => 'expired'
      ), array(
      'status <>?' => 'expired',
      'expiration_date <=?' => date('Y-m-d H:i:s'),
    ));
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.task.updateexpiredpages', time());

    $select = $table->select()
      ->from($rName, array('page_id'))
      ->where('status =  ?', 'expired');
    foreach( $table->fetchAll($select) as $page ) {
      if( $sitepageeventEnabled ) {
        //FETCH Notes CORROSPONDING TO THAT Page ID
        $sitepageeventtable = Engine_Api::_()->getItemTable('sitepageevent_event');
        $select = $sitepageeventtable->select()
          ->from($sitepageeventtable->info('name'), 'event_id')
          ->where('page_id = ?', $page->page_id);
        $rows = $sitepageeventtable->fetchAll($select)->toArray();
        if( !empty($rows) ) {
          foreach( $rows as $key => $event_ids ) {
            $event_id = $event_ids['event_id'];
            if( !empty($event_id) ) {
              $sitepageeventtable->update(array(
                'search' => '0'
                ), array(
                'event_id =?' => $event_id
              ));
            }
          }
        }
      }

      if( $siteeventEnabled ) {
        //FETCH Notes CORROSPONDING TO THAT Page ID
        $siteeventtable = Engine_Api::_()->getItemTable('siteevent_event');
        $select = $siteeventtable->select()
          ->from($siteeventtable->info('name'), 'event_id')
          ->where('parent_type = ?', 'sitepage_page')
          ->where('parent_id = ?', $page->page_id);
        $rows = $siteeventtable->fetchAll($select)->toArray();
        if( !empty($rows) ) {
          foreach( $rows as $key => $event_ids ) {
            $event_id = $event_ids['event_id'];
            if( !empty($event_id) ) {
              $siteeventtable->update(array(
                'search' => '0'
                ), array(
                'event_id =?' => $event_id
              ));
            }
          }
        }
      }

      if( $sitevideoEnabled ) {
        //FETCH Notes CORROSPONDING TO THAT Page ID
        $sitevideotable = Engine_Api::_()->getItemTable('sitevideo_video');
        $select = $sitevideotable->select()
          ->from($sitevideotable->info('name'), 'video_id')
          ->where('parent_type = ?', 'sitepage_page')
          ->where('parent_id = ?', $page->page_id);
        $rows = $sitevideotable->fetchAll($select)->toArray();
        if( !empty($rows) ) {
          foreach( $rows as $key => $video_ids ) {
            $video_id = $video_ids['video_id'];
            if( !empty($video_id) ) {
              $sitevideotable->update(array(
                'search' => '0'
                ), array(
                'video_id =?' => $video_id
              ));
            }
          }
        }
      }

      if( $documentEnabled && $documentintegrationEnabled) {
        $documenttable = Engine_Api::_()->getItemTable('document');
        $select = $documenttable->select()
          ->from($documenttable->info('name'), 'document_id')
          ->where('parent_type = ?', 'sitepage_page')
          ->where('parent_id = ?', $page->page_id);
        $rows = $documenttable->fetchAll($select)->toArray();
        if( !empty($rows) ) {
          foreach( $rows as $key => $document_ids ) {
            $document_id = $document_ids['document_id'];
            if( !empty($document_id) ) {
              $documenttable->update(array(
                'search' => '0'
                ), array(
                'document_id =?' => $document_id
              ));
            }
          }
        }
      }

      if( $sitecrowdfundingEnabled ) {
        //FETCH Notes CORROSPONDING TO THAT Page ID
        $projecttable = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
        $select = $projecttable->select()
          ->from($projecttable->info('name'), 'project_id')
          ->where('parent_type = ?', 'sitepage_page')
          ->where('parent_id = ?', $page->page_id);
        $rows = $projecttable->fetchAll($select)->toArray();
        if( !empty($rows) ) {
          foreach( $rows as $key => $project_ids ) {
            $project_id = $project_ids['project_id'];
            if( !empty($project_id) ) {
              $projecttable->update(array(
                'search' => '0'
                ), array(
                'project_id =?' => $project_id
              ));
            }
          }
        }
      }

      if( $sitemusicEnabled ) {
        //FETCH Notes CORROSPONDING TO THAT Page ID
        $musicAlbumtable = Engine_Api::_()->getItemTable('sitemusic_pl');
        $select = $musicAlbumtable->select()
          ->from($musicAlbumtable->info('name'), 'playlist_id')
          ->where('parent_type = ?', 'sitepage_page')
          ->where('parent_id = ?', $page->page_id);
        $rows = $musicAlbumtable->fetchAll($select)->toArray();
        if( !empty($rows) ) {
          foreach( $rows as $key => $album_ids ) {
            $album_id = $album_ids['playlist_id'];
            if( !empty($album_id) ) {
              $musicAlbumtable->update(array(
                'search' => '0'
                ), array(
                'playlist_id =?' => $album_id
              ));
            }
          }
        }
      }

      $table->update(array(
        'search' => 0
        ), array(
        'status =?' => 'expired',
      ));
    }
  }

  /**
   * Get expiry date for page
   * $params object $page
   * @return date
   * */
  public function getExpiryDate($page)
  {
    if( empty($page->expiration_date) || $page->expiration_date === "0000-00-00 00:00:00" )
      return "-";
    $translate = Zend_Registry::get('Zend_Translate');
    if( $page->expiration_date === "2250-01-01 00:00:00" )
      return $translate->translate('Never Expires');
    else {
      if( strtotime($page->expiration_date) < time() )
        return "Expired";

      return date("M d,Y g:i A", strtotime($page->expiration_date));
    }
  }

  /**
   * Get status of page
   * $params object $page
   * @return string
   * */
  public function getPageStatus($page)
  {
    $translate = Zend_Registry::get('Zend_Translate');

    return $translate->translate($page->state);
//    if( !empty($page->declined) ) {
//      return "<span style='color: red;'>" . $translate->translate("Declined") . "</span>";
//    }
//
//    if( !empty($page->pending) ) {
//      return $translate->translate("Approval Pending");
//    }
//    if( !empty($page->approved) ) {
//      return $translate->translate("Approved");
//    }
//
//
//    if( empty($page->approved) ) {
//      return $translate->translate("Dis-Approved");
//    }

   // return "Approved";
  }

  /**
   * On installation time enable submodule for default package
   * $params string $modulename
   * */
  public function oninstallPackageEnableSubMOdules($modulename)
  {
    if( !Engine_Api::_()->sitepage()->hasPackageEnable() )
      return;
    $package = Engine_Api::_()->getItemtable('sitepage_package')->fetchRow(array('defaultpackage = ?' => 1));
    if( !empty($package) ) {
      $values = array();
      $values = unserialize($package->modules);
      $values[] = $modulename;
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $package->modules = serialize($values);
        $package->save();
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
    }
  }

//Count the no of likes on a page
  public function getPageLikes($values = array())
  {
    if( empty($values['page_id']) )
      return;

    $page_id = $values['page_id'];
    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $like_name = $likeTable->info('name');

    $like_select = $likeTable->select()
      ->from($like_name)
      ->where('resource_type = ?', 'sitepage_page')
      ->where('resource_id = ?', $page_id);

    if( !empty($values['startTime']) && !empty($values['endTime']) ) {
      $like_select->where($like_name . '.creation_date >= ?', gmdate('Y-m-d H:i:s', $values['startTime']))
        ->where($like_name . '.creation_date < ?', gmdate('Y-m-d H:i:s', $values['endTime']));
    }
    return count($like_select->query()->fetchAll());
  }

//Calculate the no of likes on a page date or month wise
  public function getReportLikes($values = array())
  {
    if( empty($values['page_id']) )
      return;

    $page_id = $values['page_id'];
    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $like_name = $likeTable->info('name');

    $like_select = $likeTable->select()
      ->from($like_name, array('COUNT(like_id) as page_likes', 'creation_date'))
      ->where('resource_type = ?', 'sitepage_page')
      ->where('resource_id = ?', $page_id)
      ->group('resource_id');

    if( !empty($values['startTime']) && !empty($values['endTime']) ) {
      $like_select->where($like_name . '.creation_date >= ?', gmdate('Y-m-d', $values['startTime']))
        ->where($like_name . '.creation_date < ?', gmdate('Y-m-d', $values['endTime']));
    }

    if( !empty($values['user_report']) ) {
      if( !empty($values['time_summary']) ) {
        if( $values['time_summary'] == 'Monthly' ) {
          $startTime = date('Y-m', mktime(0, 0, 0, $values['month_start'], date('d'), $values['year_start']));
          $endTime = date('Y-m', mktime(0, 0, 0, $values['month_end'], date('d'), $values['year_end']));
        } else {
          if( !empty($values['start_daily_time']) ) {
            $start = $values['start_daily_time'];
          }
          if( !empty($values['start_daily_time']) ) {
            $end = $values['end_daily_time'];
          }
          $startTime = date('Y-m-d', $start);
          $endTime = date('Y-m-d', $end);
        }
      }
      if( !empty($values['time_summary']) ) {

        switch( $values['time_summary'] ) {

          case 'Monthly':
            $like_select
              ->where("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m') >= ?", $startTime)
              ->where("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m') <= ?", $endTime);
            if( !isset($values['total_stats']) && empty($values['total_stats']) ) {
              $like_select->group("DATE_FORMAT(" . $like_name . " .creation_date, '%m')");
            }
            break;

          case 'Daily':
            $like_select
              ->where("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m-%d') >= ?", $startTime)
              ->where("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m-%d') <= ?", $endTime);
            if( !isset($values['total_stats']) && empty($values['total_stats']) ) {
              $like_select->group("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m-%d')");
            }
            break;
        }
      }
    }
    $like_array = $likeTable->fetchAll($like_select)->toarray();
    return $like_array;
  }

//Calculate the no of comments on a page date or month wise
  public function getReportComments($values = array())
  {
    if( empty($values['page_id']) )
      return;

    $page_id = $values['page_id'];
    $commentTable = Engine_Api::_()->getItemTable('core_comment');
    $comment_name = $commentTable->info('name');

    $comment_select = $commentTable->select()
      ->from($comment_name, array('COUNT(comment_id) as page_comments', 'creation_date'))
      ->where('resource_type = ?', 'sitepage_page')
      ->where('resource_id = ?', $page_id)
      ->group('resource_id');

    if( !empty($values['startTime']) && !empty($values['endTime']) ) {
      $comment_select->where($comment_name . '.creation_date >= ?', gmdate('Y-m-d', $values['startTime']))
        ->where($comment_name . '.creation_date < ?', gmdate('Y-m-d', $values['endTime']));
    }

    if( !empty($values['user_report']) ) {
      if( !empty($values['time_summary']) ) {
        if( $values['time_summary'] == 'Monthly' ) {
          $startTime = date('Y-m', mktime(0, 0, 0, $values['month_start'], date('d'), $values['year_start']));
          $endTime = date('Y-m', mktime(0, 0, 0, $values['month_end'], date('d'), $values['year_end']));
        } else {
          if( !empty($values['start_daily_time']) ) {
            $start = $values['start_daily_time'];
          }
          if( !empty($values['start_daily_time']) ) {
            $end = $values['end_daily_time'];
          }
          $startTime = date('Y-m-d', $start);
          $endTime = date('Y-m-d', $end);
        }
      }
      if( !empty($values['time_summary']) ) {

        switch( $values['time_summary'] ) {

          case 'Monthly':
            $comment_select
              ->where("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m') >= ?", $startTime)
              ->where("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m') <= ?", $endTime);
            if( !isset($values['total_stats']) && empty($values['total_stats']) ) {
              $comment_select->group("DATE_FORMAT(" . $comment_name . " .creation_date, '%m')");
            }
            break;

          case 'Daily':
            $comment_select
              ->where("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m-%d') >= ?", $startTime)
              ->where("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m-%d') <= ?", $endTime);
            if( !isset($values['total_stats']) && empty($values['total_stats']) ) {
              $comment_select->group("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m-%d')");
            }
            break;
        }
      }
    }
    $comment_array = $commentTable->fetchAll($comment_select)->toarray();
    return $comment_array;
  }

//Count the no of comments on a page
  public function getPageComments($values = array())
  {
    if( empty($values['page_id']) )
      return;

    $page_id = $values['page_id'];
    $commentTable = Engine_Api::_()->getItemTable('core_comment');
    $comment_name = $commentTable->info('name');

    $comment_select = $commentTable->select()
      ->from($comment_name)
      ->where('resource_type = ?', 'sitepage_page')
      ->where('resource_id = ?', $page_id);

    if( !empty($values['startTime']) && !empty($values['endTime']) ) {
      $comment_select->where($comment_name . '.creation_date >= ?', gmdate('Y-m-d H:i:s', $values['startTime']))
        ->where($comment_name . '.creation_date < ?', gmdate('Y-m-d H:i:s', $values['endTime']));
    }
    return count($comment_select->query()->fetchAll());
  }

  // This function checks that whether comments have to be displayed in insights or not
  public function displayCommentInsights()
  {
    $userlayout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    if( !empty($userlayout) ) {
      $ContentTable = Engine_Api::_()->getDbtable('content', 'sitepage');
    } else {
      $ContentTable = Engine_Api::_()->getDbtable('content', 'core');
    }
    $select = $ContentTable->select()->where('name= ?', 'sitepage.info-sitepage')->limit(1);
    $infoWidget = $ContentTable->fetchRow($select);
    if( !empty($infoWidget) ) {
      return true;
    } else {
      return false;
    }
  }

  public function hasPageLike($RESOURCE_ID, $viewer_id)
  {
    if( empty($RESOURCE_ID) || empty($viewer_id) )
      return false;

    $sub_status_table = Engine_Api::_()->getItemTable('core_like');

    $sub_status_select = $sub_status_table->select()
      ->where('resource_type = ?', 'sitepage_page')
      ->where('resource_id = ?', $RESOURCE_ID)
      ->where('poster_id = ?', $viewer_id);
    $fetch_sub = $sub_status_table->fetchRow($sub_status_select);
    if( !empty($fetch_sub) )
      return true;
    else
      return false;
  }

  /**
   * check photo show in light box or not
   * */
  public function canShowPhotoLightBox()
  {
      return SEA_SITEPAGEALBUM_LIGHTBOX;
  }

  /**
   * check in case draft, not approved viewer can view page
   * */
  public function canViewPage($sitepage)
  {
    $can_view = true;
    if( empty($sitepage->draft) || (empty($sitepage->aprrove_date)) || (empty($sitepage->approved) && empty($sitepage->pending) ) || !empty($sitepage->declined) || ($this->hasPackageEnable() && $sitepage->expiration_date !== "2250-01-01 00:00:00" && strtotime($sitepage->expiration_date) < time()) ) {
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
      if( empty($isManageAdmin) ) {
        $can_view = false;
      }
    }
    return $can_view;
  }

  public function attachPageActivity($sitepage)
  {
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($sitepage->getOwner(), $sitepage, 'sitepage_new');

      if( $action != null ) {
        Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action, true);
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitepage);
      }

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }

  public function onPageDelete($page_id)
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    if( empty($page_id) || empty($viewer_id) ) {
      return;
    }

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    Engine_Api::_()->getDbtable('locations', 'sitepage')->delete(array('page_id =?' => $page_id));

    //FETCH PHOTO AND OTHER BELONGINGS
    $table = Engine_Api::_()->getItemTable('sitepage_photo');
    $select = $table->select()->where('page_id = ?', $page_id);
    $rows = $table->fetchAll($select);
    if( !empty($rows) ) {
      foreach( $rows as $sitepagephoto ) {
        //DELETE PHOTO AND OTHER BELONGINGS
        $sitepagephoto->delete();
      }
    }

    $table = Engine_Api::_()->getItemTable('sitepage_album');
    $select = $table->select()->where('page_id = ?', $page_id);
    $rows = $table->fetchAll($select);
    if( !empty($rows) ) {
      foreach( $rows as $sitepagealbum ) {
        //DELETE ALBUM AND OTHER BELONGINGS
        $sitepagealbum->delete();
      }
    }

    $table = Engine_Api::_()->getItemTable('sitepage_service');
    $select = $table->select()->where('page_id = ?', $page_id);
    $rows = $table->fetchAll($select);
    if( !empty($rows) ) {
      foreach( $rows as $sitepageservice ) {
        //DELETE SERVICE AND OTHER BELONGINGS
        $sitepageservice->delete();
      }
    }

    $table = Engine_Api::_()->getItemTable('sitepage_timing');
    $select = $table->select()->where('page_id = ?', $page_id);
    $rows = $table->fetchAll($select);
    if( !empty($rows) ) {
      foreach( $rows as $sitepagetiming ) {
        //DELETE SERVICE AND OTHER BELONGINGS
        $sitepagetiming->delete();
      }
    }

    $table = Engine_Api::_()->getItemTable('sitepage_button');
    $select = $table->select()->where('page_id = ?', $page_id);
    $rows = $table->fetchAll($select);
    if( !empty($rows) ) {
      foreach( $rows as $sitepagebutton ) {
        //DELETE SERVICE AND OTHER BELONGINGS
        $sitepagebutton->delete();
      }
    }
    //END PAGE-ALBUM CODE
    //START PAGE-BADGE CODE
    $sitepagebadgeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge');
    if( $sitepagebadgeEnabled ) {
      //DELETE BADGE REQUESTS CORROSPONDING TO THAT PAGE ID
      Engine_Api::_()->getItemTable('sitepagebadge_badgerequest')->delete(array('page_id = ?' => $page_id));
    }
    //END PAGE-BADGE CODE
    //START PAGE-DISCUSSION CODE
    $sitepageDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion');
    if( $sitepageDiscussionEnabled ) {

      $table = Engine_Api::_()->getItemTable('sitepage_topic');
      $select = $table->select()->where('page_id = ?', $page_id);
      $rows = $table->fetchAll($select);
      if( !empty($rows) ) {
        foreach( $rows as $topic ) {
          $topic->delete();
        }
      }

      $table = Engine_Api::_()->getItemTable('sitepage_post');
      $select = $table->select()->where('page_id = ?', $page_id);
      $rows = $table->fetchAll($select);
      if( !empty($rows) ) {
        foreach( $rows as $post ) {
          $post->delete();
        }
      }

      Engine_Api::_()->getDbtable('topicwatches', 'sitepage')->delete(array('page_id =?' => $page_id));
    }
    //END PAGE-DISCUSSION CODE
    //START PAGE-DOCUMENT CODE
    $sitepagedocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
    if( $sitepagedocumentEnabled ) {

      //FETCH DOCUMENTS CORROSPONDING TO THAT SITEPAGE ID
      $table = Engine_Api::_()->getItemTable('sitepagedocument_document');
      $select = $table->select()
        ->from($table->info('name'), 'document_id')
        ->where('page_id = ?', $page_id);
      $rows = $table->fetchAll($select)->toArray();
      if( !empty($rows) ) {
        foreach( $rows as $key => $document_ids ) {
          $document_id = $document_ids['document_id'];
          if( !empty($document_id) ) {
            Engine_Api::_()->sitepagedocument()->deleteContent($document_id);
          }
        }
      }
    }
    //END PAGE-DOCUMENT CODE
    //START PAGE-EVENT CODE
    $sitepageeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
    if( $sitepageeventEnabled ) {
      //FETCH Notes CORROSPONDING TO THAT Page ID
      $table = Engine_Api::_()->getItemTable('sitepageevent_event');
      $select = $table->select()
        ->from($table->info('name'), 'event_id')
        ->where('page_id = ?', $page_id);
      $rows = $table->fetchAll($select)->toArray();
      if( !empty($rows) ) {
        foreach( $rows as $key => $event_ids ) {
          $event_id = $event_ids['event_id'];
          if( !empty($event_id) ) {
            //DELETE EVENT, ALBUM AND EVENT IMAGES
            Engine_Api::_()->sitepageevent()->deleteContent($event_id);
          }
        }
      }
    }
    //END PAGE-EVENT CODE
    //START ADVANCED-EVENT CODE
    $siteeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
    if( $siteeventEnabled ) {
      //FETCH Notes CORROSPONDING TO THAT Page ID
      $table = Engine_Api::_()->getItemTable('siteevent_event');
      $select = $table->select()
        ->from($table->info('name'), 'event_id')
        ->where('parent_id = ?', $page_id)
        ->where('parent_type = ?', 'sitepage_page');
      $rows = $table->fetchAll($select)->toArray();
      if( !empty($rows) ) {
        foreach( $rows as $key => $event_ids ) {
          $resource = Engine_Api::_()->getItem('siteevent_event', $event_ids['event_id']);
          if( $resource )
            $resource->delete();
        }
      }
    }

    $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
    if( $sitevideoEnabled ) {
      //FETCH Notes CORROSPONDING TO THAT Page ID
      $table = Engine_Api::_()->getItemTable('sitevideo_video');
      $select = $table->select()
        ->from($table->info('name'), 'video_id')
        ->where('parent_id = ?', $page_id)
        ->where('parent_type = ?', 'sitepage_page');
      $rows = $table->fetchAll($select)->toArray();
      if( !empty($rows) ) {
        foreach( $rows as $key => $video_ids ) {
          $resource = Engine_Api::_()->getItem('sitevideo_video', $video_ids['video_id']);
          if( $resource )
            $resource->delete();
        }
      }
    }

    $documentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document');
    $documentintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('documentintegration');
    if( $documentEnabled && $documentintegrationEnabled) {
      $table = Engine_Api::_()->getItemTable('document');
      $select = $table->select()
        ->from($table->info('name'), 'document_id')
        ->where('parent_id = ?', $page_id)
        ->where('parent_type = ?', 'sitepage_page');
      $rows = $table->fetchAll($select)->toArray();
      if( !empty($rows) ) {
        foreach( $rows as $key => $document_ids ) {
          $resource = Engine_Api::_()->getItem('document', $document_ids['document_id']);
          if( $resource )
            $resource->delete();
        }
      }
    }

    $sitecrowdfundingEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding');
    if( $sitecrowdfundingEnabled ) {
      //FETCH Notes CORROSPONDING TO THAT Page ID
      $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
      $select = $table->select()
        ->from($table->info('name'), 'project_id')
        ->where('parent_id = ?', $page_id)
        ->where('parent_type = ?', 'sitepage_page');
      $rows = $table->fetchAll($select)->toArray();
      if( !empty($rows) ) {
        foreach( $rows as $key => $project_ids ) {
          $resource = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_ids['project_id']);
          if( $resource && $resource->isbacked() ) {
            $resource->parent_type = 'user';
            $resource->save();
          } else if( $resource )
            $resource->delete();
        }
      }
    }

        $sitemusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic');
    if( $sitemusicEnabled ) {
      //FETCH Notes CORROSPONDING TO THAT Page ID
      $table = Engine_Api::_()->getItemTable('sitemusic_pl');
      $select = $table->select()
        ->from($table->info('name'), 'playlist_id')
        ->where('parent_id = ?', $page_id)
        ->where('parent_type = ?', 'sitepage_page');
      $rows = $table->fetchAll($select)->toArray();
      if( !empty($rows) ) {
        foreach( $rows as $key => $album_ids ) {
          $resource = Engine_Api::_()->getItem('sitemusic_pl', $album_ids['playlist_id']);
          if( $resource && $resource->isbacked() ) {
            $resource->parent_type = 'user';
            $resource->save();
          } else if( $resource )
            $resource->delete();
        }
      }
    }

    //END ADVANCED-EVENT CODE
    //START PAGE-FORM CODE
    $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
    if( $sitepageFormEnabled ) {
      $mapstable = Engine_Api::_()->fields()->getTable('sitepageform', 'maps');
      $optiontable = Engine_Api::_()->fields()->getTable('sitepageform', 'options');

      $pagequetion_table = Engine_Api::_()->getDbtable('pagequetions', 'sitepageform');
      $sitepageform_table = Engine_Api::_()->getDbtable('sitepageforms', 'sitepageform');
      $select = $pagequetion_table->select()->where('page_id =?', $page_id);
      $optionid = $pagequetion_table->fetchRow($select);
      $option_id = $optionid->option_id;
      if( !empty($option_id) ) {
        $matatable = Engine_Api::_()->fields()->getTable('sitepageform', 'meta');
        $select_options = $matatable->select()->where('option_id =?', $option_id);
        $select_options_result = $select_options->from($matatable->info('name'), array('field_id'));
        $result = $matatable->fetchAll($select_options_result)->toArray();
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
          foreach( $result as $key => $id ) {
            $field_id = $id['field_id'];
            $matatable->delete(array('field_id =?' => $field_id));
            $optiontable->delete(array('field_id =?' => $field_id));
          }
          $optiontable->delete(array('option_id =?' => $option_id));
          $pagequetion_table->delete(array('option_id =?' => $option_id));
          $mapstable->delete(array('option_id =?' => $option_id));
          $sitepageform_table->delete(array('page_id =?' => $page_id));
          $db->commit();
        } catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }
      }
    }
    //END PAGE-FORM CODE
    //START PAGE-NOTE CODE
    $sitepagenoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
    if( $sitepagenoteEnabled ) {
      //FETCH Notes CORROSPONDING TO THAT Page ID
      $table = Engine_Api::_()->getItemTable('sitepagenote_note');
      $select = $table->select()
        ->from($table->info('name'), 'note_id')
        ->where('page_id = ?', $page_id);
      $rows = $table->fetchAll($select)->toArray();
      if( !empty($rows) ) {
        foreach( $rows as $key => $note_ids ) {
          $note_id = $note_ids['note_id'];
          if( !empty($note_id) ) {

            //DELETE NOTE, ALBUM AND NOTE IMAGES
            Engine_Api::_()->sitepagenote()->deleteContent($note_id);
          }
        }
      }
    }
    //END PAGE-NOTE CODE
    //START PAGE-OFFER CODE
    $sitepageofferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');
    if( $sitepageofferEnabled ) {
      //FETCH Offers CORROSPONDING TO THAT Page ID
      $table = Engine_Api::_()->getItemTable('sitepageoffer_offer');
      $select = $table->select()->where('page_id = ?', $page_id);
      $rows = $table->fetchAll($select);
      if( !empty($rows) ) {
        foreach( $rows as $sitepageoffer ) {
          Engine_Api::_()->sitepageoffer()->deleteContent($sitepageoffer->offer_id);
        }
      }
    }
    //END PAGE-OFFER CODE
    //START PAGE-POLL CODE
    $sitepagepollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
    if( $sitepagepollEnabled ) {
      //FETCH POLLS CORROSPONDING TO THAT GROUP ID
      $table = Engine_Api::_()->getItemTable('sitepagepoll_poll');
      $select = $table->select()->where('page_id = ?', $page_id);
      $rows = $table->fetchAll($select);
      if( !empty($rows) ) {
        foreach( $rows as $sitepagepoll ) {
          //DELETE POLL AND OTHER BELONGINGS
          $sitepagepoll->delete();
        }
      }
    }
    //END PAGE-POLL CODE
    //START PAGE-REVIEW CODE
    $sitepagereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
    if( $sitepagereviewEnabled ) {

      //FETCH REVIEWS
      $table = Engine_Api::_()->getItemTable('sitepagereview_review');
      $select = $table->select()->where('page_id = ?', $page_id);
      $rows = $table->fetchAll($select);

      if( !empty($rows) ) {
        foreach( $rows as $review ) {
          Engine_Api::_()->sitepagereview()->deleteContent($review->review_id);
        }
      }
    }
    //END PAGE-REVIEW CODE
    //START PAGE-WISHLIST CODE
    $sitepagewishlistEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagewishlist');
    if( $sitepagewishlistEnabled ) {
      Engine_Api::_()->getDbtable('pages', 'sitepagewishlist')->delete(array('page_id =?' => $page_id));
    }
    //END PAGE-WISHLIST CODE
    //START PAGE-VIDEO CODE
    $sitepagevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
    if( $sitepagevideoEnabled ) {
      //FETCH VIDEOS CORROSPONDING TO THAT SITEPAGE ID


      $table = Engine_Api::_()->getItemTable('sitepagevideo_video');
      $select = $table->select()->where('page_id = ?', $page_id);
      $rows = $table->fetchAll($select);
      if( !empty($rows) ) {
        foreach( $rows as $video ) {
          //DELETE VIDEO AND OTHER BELONGINGS
          Engine_Api::_()->getDbtable('ratings', 'sitepagevideo')->delete(array('video_id = ?' => $video->video_id));
          $video->delete();
        }
      }
    }
    //END PAGE-VIDEO CODE
    //START PAGE-MUSIC CODE
    $sitepagemusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic');
    if( $sitepagemusicEnabled ) {
      //FETCH PLAYLIST CORROSPONDING TO THAT PAGE ID
      $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sitepagemusic');
      $playlistSelect = $playlistTable->select()->where('page_id = ?', $page_id);
      foreach( $playlistTable->fetchAll($playlistSelect) as $playlist ) {
        foreach( $playlist->getSongs() as $song ) {
          $song->deleteUnused();
        }
        $playlist->delete();
      }
    }
    //END PAGE-MUSIC CODE
    //START PAGE-MEMBER CODE
    $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    if( $sitepagememberEnabled ) {
      $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
      $membershipTable->delete(array('resource_id =?' => $page_id, 'page_id =?' => $page_id));
    }
    //END PAGE-MUSIC CODE
    //FINALLY START PAGE CODE

    $searchTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'search');
    $valuesTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'values');

    $pagestatisticsTable = Engine_Api::_()->getDbtable('pagestatistics', 'sitepage');

    $writesTable = Engine_Api::_()->getDbtable('writes', 'sitepage');
    $listsTable = Engine_Api::_()->getDbtable('lists', 'sitepage');

    //$viewedsTable = Engine_Api::_()->getDbtable('vieweds', 'sitepage');
    $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');

    $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitepage');

    $authAllowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $claimTable = Engine_Api::_()->getDbtable('claims', 'sitepage');
    $sitepageitemofthedaysTable = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage');

    $layoutcontentTable = Engine_Api::_()->getDbtable('content', 'sitepage');
    $layoutcontentpageTable = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
    //GETTING THE CONTENTPAGE ID FROM CONTENTPAGES TABLE SO THAT WE CAN REMOVE THE CONTENT FROM CONTENT TABLE ALSO.
    $LayoutContentPageName = $layoutcontentpageTable->info('name');

    $select = $layoutcontentpageTable->select()->from($LayoutContentPageName, 'contentpage_id')->where('page_id = ?', $page_id);
    $LayoutContentPageid = $layoutcontentpageTable->fetchRow($select);
    if( !empty($LayoutContentPageid) ) {
      $LayoutContentPageid = $LayoutContentPageid->toarray();
    }

    $searchTable->delete(array('item_id =?' => $page_id));
    $valuesTable->delete(array('item_id =?' => $page_id));
    $writesTable->delete(array('page_id =?' => $page_id));
    $listsTable->delete(array('page_id =?' => $page_id));
    $pagestatisticsTable->delete(array('page_id =?' => $page_id));

    // $viewedsTable->delete(array('page_id =?' => $page_id));
    $manageadminsTable->delete(array('page_id =?' => $page_id));
    $locationsTable->delete(array('page_id =?' => $page_id));

    $sitepageitemofthedaysTable->delete(array('resource_id =?' => $page_id, 'resource_type' => 'sitepage_page'));

    $authAllowTable->delete(array('resource_id =?' => $page_id, 'resource_type =?' => 'sitepage_page'));
    $claimTable->delete(array('page_id =?' => $page_id));

    //DELETE FIELD ENTRIES IF EXISTS
    $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'values');
    $fieldvalueTable->delete(array(
      'item_id = ?' => $page_id,
    ));

    $fieldsearchTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'search');
    $fieldsearchTable->delete(array(
      'item_id = ?' => $page_id,
    ));

    if( !empty($LayoutContentPageid) ) {
      $layoutcontentTable->delete(array('contentpage_id =?' => $LayoutContentPageid['contentpage_id']));
    }

    $layoutcontentpageTable->delete(array('page_id =?' => $page_id));
    $sitepage->cancel();
    $sitepage->delete();

    //END PAGE CODE
  }

  public function isEnabledModPackage($mod)
  {
    if( !empty($mod) ) {
      $arrayName = strrev('sepyt.dom.egapetis');
    } else {
      $arrayName = strrev('sgnittes.dom.egapetis');
    }
    return Engine_Api::_()->getApi('settings', 'core')->getSetting($arrayName, null);
  }

  public function isModulesActivated()
  {
    $pageModArray = array(
      'sitepagealbum' => 'Directory / Pages - Albums Extension',
      'sitepagebadge' => 'Directory / Pages - Badges Extension',
      'sitepagedocument' => 'Directory / Pages - Documents Extension',
      'sitepageevent' => 'Directory / Pages - Events Extension',
      'sitepageform' => 'Directory / Pages - Form Extension',
      'sitepageinvite' => 'Directory / Pages - Inviter Extension',
      'sitepagenote' => 'Directory / Pages - Notes Extension',
      'sitepageoffer' => 'Directory / Pages - Offers Extension',
      'sitepagepoll' => 'Directory / Pages - Polls Extension',
      'sitepagereview' => 'Directory / Pages - Reviews and Ratings Extension',
      'sitepagevideo' => 'Directory / Pages - Videos Extension',
      'sitepagemusic' => 'Directory / Pages - Music Extension',
      'sitepagemember' => 'Directory / Pages - Page Members Extension',
      'sitepagelikebox' => 'Directory / Pages - Like Box',
      'communityad' => 'Advertisements / Community Ads Plugin',
      'sitepageintegration' => 'Directory / Pages - Multiple Listings and Products Showcase Extension',
      'siteevent' => 'Advanced Events',
      'sitevideo' => 'Advanced Videos',
      'document' => 'Documents',
      'sitecrowdfunding' => 'Crowdfunding / Donation',
      'sitemusic' => 'Advanced Music'
    );
    $notActivatedModArray = array();
    foreach( $pageModArray as $modNameKey => $modNameValue ) {
      $isModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($modNameKey);
      if( $modNameKey == 'communityad' ) {
        $isModuleActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.navi.auth', null);
      } else {
        $isModuleActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting($modNameKey . '.isActivate', null);
      }
      //Condition: If Plugin enabled but not activated.
      if( !empty($isModuleEnabled) && empty($isModuleActivate) ) {
        $notActivatedModArray[$modNameKey] = $modNameValue;
      }
    }
    return $notActivatedModArray;
  }

  public function isEnabled()
  {
    $hostType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.viewpage.sett', 0);
    $hostName = convert_uudecode($hostType);
    if( $hostName == 'localhost' || strpos($hostName, '192.168.') != false || strpos($hostName, '127.0.') != false ) {
      return;
    }

    return 1;
  }

  public function isPluginActivate($modName)
  {
    return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting($modName . '.isActivate', 0);
  }

  /**
   *  CHECK Payment PLUGIN ENABLE / DISABLE
   * */
  public function enablePaymentPlugin()
  {
    return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('payment');
  }

  /**
   *  Check Viewer able to edit style
   * */
  public function allowStyle()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if( !empty($viewer_id) ) {
      return (bool) Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('sitepage_page', $viewer->level_id, 'style');
    } else {
      return (bool) 0;
    }
  }

  /**
   * get viewer like pages
   */
  public function getMyLikePages($params = array())
  {
//    $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
//    $likeName = $likeTable->info('name');
//    $select = $likeTable->select()
//            ->where($likeName . '.poster_id = ?', $params['poster_id'])
//            ->where($likeName . '.poster_type = ?', $params['poster_type'])
//            ->where($likeName . '.resource_type = ?', $params['resource_type'])
//            ->order($likeName . '.creation_date DESC');
//    return $likeTable->fetchAll($select);
  }

  /**
   * Gets member like pages
   *
   * $member User_Model_User
   */
  public function getMemberLikePagesOfIds($member)
  {
    $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
    $likeName = $likeTable->info('name');
    return $select = $likeTable->select()
      ->from($likeName, "resource_id")
      ->where($likeName . '.poster_id = ?', $member->getIdentity())
      ->where($likeName . '.poster_type = ?', $member->getType())
      ->where($likeName . '.resource_type = ?', 'sitepage_page')
      ->order($likeName . '.creation_date DESC')
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);
    ;
  }

  /**
   * Gets feed for which viewer like
   *
   * @$user User_Model_User
   * @param array $params
   */
  public function getFeedActionLikedPages(User_Model_User $user, array $params = array())
  {
//    $ids = array();
//    if (!(bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feed.likepage', 0))
//      return $ids;
//    $getMyLikes = $this->getMyLikePages(array("poster_type" => $user->getType(), "poster_id" => $user->getIdentity(), "resource_type" => "sitepage_page"));
//    $count = count($getMyLikes);
//
//    if (!empty($count)) {
//      $resource_ids = array();
//      foreach ($getMyLikes as $likeItems) {
//        $resource_ids[] = $likeItems->resource_id;
//      }
//
//      if (!empty($resource_ids)) {
//        //Proc args
//        extract($params); //action_id, limit, min_id, max_id
//        $actionDbTable = Engine_Api::_()->getDbtable('actions', 'activity');
//        $typesAdmin = array("sitepage_post_self", "sitepagealbum_admin_photo_new", "sitepagevideo_admin_new", "sitepageevent_admin_new", "sitepagenote_admin_new", "sitepagepoll_admin_new", "sitepagedocument_admin_new", "sitepageoffer_admin_new", "sitepage_admin_topic_create", "sitepagemusic_admin_new", "sitepage_profile_photo_update");
//        $select = $actionDbTable->select()
//                ->where("type in (?)", new Zend_Db_Expr("'" . join("', '", $typesAdmin) . "'"))
//                ->where("subject_type = ? ", "sitepage_page")
//                ->where("subject_id IN(?)", new Zend_Db_Expr(join(',', $resource_ids)))
//                ->order('action_id DESC')
//                ->limit($limit);
//
//        if (null !== $action_id) {
//          $select->where('action_id = ?', $action_id);
//        } else {
//          if (null !== $min_id) {
//            $select->where('action_id >= ?', $min_id);
//          } else if (null !== $max_id) {
//            $select->where('action_id <= ?', $max_id);
//          }
//        }
//        $results = $actionDbTable->fetchAll($select);
//        foreach ($results as $actionData)
//          $ids[] = $actionData->action_id;
//      }
//    }
//    return $ids;
  }

  /**
   * Gets feed type page title and photo is enable
   *
   * @return bool
   */
  public function isFeedTypePageEnable()
  {
    return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feed.type', 0);
  }

  /**
   * Set default widget in core pages table
   *
   * @param object $table
   * @param string $tablename
   * @param int $page_id
   * @param string $type
   * @param string $widgetname
   * @param int $middle_id
   * @param int $order
   * @param string $title
   * @param int $titlecount
   */
  function setDefaultDataWidget($table, $tablename, $page_id, $type, $widgetname, $middle_id, $order, $title = null, $titlecount = null, $advanced_activity_params = null)
  {

    $selectWidgetId = $table->select()
      ->where('page_id =?', $page_id)
      ->where('type = ?', $type)
      ->where('name = ?', $widgetname)
      ->where('parent_content_id = ?', $middle_id)
      ->limit(1);
    $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
    if( empty($fetchWidgetContentId) ) {
      $contentWidget = $table->createRow();
      $contentWidget->page_id = $page_id;
      $contentWidget->type = $type;
      $contentWidget->name = $widgetname;
      $contentWidget->parent_content_id = $middle_id;
      $contentWidget->order = $order;
      if( empty($advanced_activity_params) && $title && $titlecount ) {
        $contentWidget->params = "{\"title\":\"$title\",\"titleCount\":$titlecount}";
      } else {
        $contentWidget->params = "$advanced_activity_params";
      }
      $contentWidget->save();
    }
  }

  /**
   * Return page paginator
   *
   * @param int $total_items
   * @param int $items_per_page
   * @param int $p
   * @return paginator
   */
  public function makePage($total_items, $items_per_page, $p)
  {
    if( !$items_per_page )
      $items_per_page = 1;
    $maxpage = ceil($total_items / $items_per_page);
    if( $maxpage <= 0 )
      $maxpage = 1;
    $p = ( ($p > $maxpage) ? $maxpage : ( ($p < 1) ? 1 : $p ) );
    $start = ($p - 1) * $items_per_page;
    return array($start, $p, $maxpage);
  }

  /**
   * Return count
   *
   * @param string $tablename
   * @param string $modulename
   * @param int $page_id
   * @param int $title_count
   * @return paginator
   */
  public function getTotalCount($page_id, $modulename, $tablename)
  {

    if( $modulename == 'siteevent' || $modulename == 'sitevideo' || $modulename == 'document' || $modulename == 'sitecrowdfunding'|| $modulename == 'sitemusic' ) {
      $table = Engine_Api::_()->getDbtable($tablename, $modulename);
      $count = 0;
      $select = $table->select();
      $currentDate = date('Y-m-d H:i:s');
      $select->from($table->info('name'), array('count(*) as count'))
        ->where("parent_type = ?", 'sitepage_page')
        ->where("parent_id =?", $page_id);
      if( $modulename == 'sitecrowdfunding' ) {
        $select->where("approved =?", 1)
          ->where("state <> ?", 'draft')
          ->where("is_gateway_configured = ?", 1)
          ->where("start_date <= '$currentDate'");
      }
      $count = $select->query()->fetchColumn();
    } else {
      $table = Engine_Api::_()->getDbtable($tablename, $modulename);
      $count = 0;
      $select = $table
        ->select()
        ->from($table->info('name'), array('count(*) as count'))
        ->where("page_id = ?", $page_id);

      if( $tablename == 'albums' && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.hide.autogenerated', 1) ) {
        $select->where($table->info('name') . '.default_value' . '= ?', 0);
        $select->where($table->info('name') . ".type is Null");
      }

      $count = $select->query()->fetchColumn();
    }
    return $count;
  }
  public function getTotalCustomCount($page_id, $modulename, $tablename)
    {

        if( $modulename == 'siteevent' || $modulename == 'sitevideo' || $modulename == 'document' || $modulename == 'sitecrowdfunding'|| $modulename == 'sitemusic' ) {
            $table = Engine_Api::_()->getDbtable($tablename, $modulename);
            $count = 0;
            $select = $table->select();
            $currentDate = date('Y-m-d H:i:s');
            $select->from($table->info('name'), array('count(*) as count'))
                ->where("parent_type = ?", 'sitepage_page')
                ->where("parent_id =?", $page_id);
            if( $modulename == 'sitecrowdfunding' ) {
                $select->where("approved =?", 1)
                    ->where("state <> ?", 'draft')
                    ->where("is_gateway_configured = ?", 1)
                    ->where("start_date <= '$currentDate'");
            }
            $count = $select->query()->fetchColumn();
        } else {
            $table = Engine_Api::_()->getDbtable($tablename, $modulename);
            $count = 0;
            $select = $table
                ->select()
                ->from($table->info('name'), array('count(*) as count'))
                ->where("page_id = ?", $page_id)
               ->where("user_id <> ?", 0);

            if( $tablename == 'albums' && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.hide.autogenerated', 1) ) {
                $select->where($table->info('name') . '.default_value' . '= ?', 0);
                $select->where($table->info('name') . ".type is Null");
            }

            $count = $select->query()->fetchColumn();
        }
        return $count;
    }

  /**
   * Return tabid
   *
   * @param string $widgetname
   * @param int $page_id
   * @param int $layout
   * @return tabid
   */
  public function GetTabIdinfo($widgetname, $pageid, $layout)
  {

    $tab_id = '';
    if( !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') ) {
      if( !$layout ) {
        if( Engine_Api::_()->sitemobile()->checkMode('mobile-mode') ) {
          $tablecontent = Engine_Api::_()->getDbtable('content', 'sitemobile');
          $select = $tablecontent->select()
            ->from($tablecontent->info('name'), 'content_id')
            ->where('name = ?', $widgetname)
            ->limit(1);
          $tab_id = $select->query()->fetchColumn();
        } elseif( Engine_Api::_()->sitemobile()->checkMode('tablet-mode') ) {
          $tablecontent = Engine_Api::_()->getDbtable('tabletcontent', 'sitemobile');
          $select = $tablecontent->select()
            ->from($tablecontent->info('name'), 'content_id')
            ->where('name = ?', $widgetname)
            ->limit(1);
          $tab_id = $select->query()->fetchColumn();
        }
      } else {
        $table = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
        $select = $table->select()
          ->from($table->info('name'), 'mobilecontentpage_id')
          ->where('name = ?', 'sitepage_index_view')
          ->where('page_id = ?', $pageid)
          ->limit(1);
        $mobilecontentpage_id = $select->query()->fetchColumn();
        if( $mobilecontentpage_id ) {

          $tablecontent = Engine_Api::_()->getDbtable('mobileContent', 'sitepage');
          $select = $tablecontent->select()
            ->from($tablecontent->info('name'), 'mobilecontent_id')
            ->where('name = ?', $widgetname)
            ->where('mobilecontentpage_id = ?', $mobilecontentpage_id)
            ->limit(1);
          $tab_id = $select->query()->fetchColumn();
        } else {
          $page_id = $this->getMobileWidgetizedPage()->page_id;
          if( !empty($page_id) ) {
            $tablecontent = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitepage');
            $select = $tablecontent->select()
              ->from($tablecontent->info('name'), 'mobileadmincontent_id')
              ->where('name = ?', $widgetname)
              ->where('page_id = ?', $page_id)
              ->limit(1);
            $tab_id = $select->query()->fetchColumn();
          }
        }
      }
      return $tab_id;
    }

    if( !$layout ) {
      $tablecontent = Engine_Api::_()->getDbtable('content', 'core');
      $select = $tablecontent->select()
        ->from($tablecontent->info('name'), 'content_id')
        ->where('name = ?', $widgetname)
        ->where('page_id = ?', $this->getWidgetizedPage()->page_id)
        ->limit(1);
      $tab_id = $select->query()->fetchColumn();
    } else {

      $table = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
      $select = $table->select()
        ->from($table->info('name'), 'contentpage_id')
        ->where('name = ?', 'sitepage_index_view')
        ->where('page_id = ?', $pageid)
        ->limit(1);
      $contentpage_id = $select->query()->fetchColumn();
      if( $contentpage_id ) {

        $tablecontent = Engine_Api::_()->getDbtable('content', 'sitepage');
        $select = $tablecontent->select()
          ->from($tablecontent->info('name'), 'content_id')
          ->where('name = ?', $widgetname)
          ->where('contentpage_id = ?', $contentpage_id)
          ->limit(1);
        $tab_id = $select->query()->fetchColumn();
      } else {
        $page_id = $this->getWidgetizedPage()->page_id;
        if( !empty($page_id) ) {
          $tablecontent = Engine_Api::_()->getDbtable('admincontent', 'sitepage');
          $select = $tablecontent->select()
            ->from($tablecontent->info('name'), 'admincontent_id')
            ->where('name = ?', $widgetname)
            ->where('page_id = ?', $page_id)
            ->limit(1);
          $tab_id = $select->query()->fetchColumn();
        }
      }
    }

    return $tab_id;
  }

  /**
   * Gets widgetized page
   *
   * @return Zend_Db_Table_Select
   */
  public function getWidgetizedPage()
  {

    //GET CORE PAGE TABLE
    $tableNamePage = Engine_Api::_()->getDbtable('pages', 'core');
    $select = $tableNamePage->select()
      ->from($tableNamePage->info('name'), array('page_id', 'description', 'keywords'))
      ->where('name =?', 'sitepage_index_view')
      ->limit(1);

    return $tableNamePage->fetchRow($select);
  }

  /**
   * Return parse string
   *
   * @param string $content
   * @return parse string
   */
  public function parseString($content)
  {
    return str_replace("'", "\'", trim($content));
  }

  /**
   * Return option of showing the widget of third type layout
   *
   * @param int $page_id
   * @param int $layout
   * @return third type layout show or not
   */
  public function getwidget($layout, $page_id)
  {
    if( !$layout ) {
      $page_id = $this->getWidgetizedPage()->page_id;
      if( !empty($page_id) ) {
        $table = Engine_Api::_()->getDbtable('content', 'core');
        $selectContent = $table->select()
          ->from($table->info('name'), 'page_id')
          ->where("name IN ('core.container-tabs', 'sitepage.widgetlinks-sitepage')")
          ->where('page_id =?', $page_id)
          ->limit(1);
        $contentinfo = $selectContent->query()->fetchAll();
        if( empty($contentinfo) ) {
          $contentinformation = 0;
        } else {
          $contentinformation = 1;
        }
      }
    } else {
      $table = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
      $select = $table->select()
        ->from($table->info('name'), 'contentpage_id')
        ->where('name = ?', 'sitepage_index_view')
        ->where('page_id = ?', $page_id)
        ->limit(1);
      $row = $table->fetchRow($select);
      if( $row !== null ) {
        $page_id = $row->contentpage_id;
        $table = Engine_Api::_()->getDbtable('content', 'sitepage');
        $selectContent = $table->select()
          ->from($table->info('name'), 'contentpage_id')
          ->where("name IN ('core.container-tabs', 'sitepage.widgetlinks-sitepage')")
          ->where('contentpage_id =?', $page_id);
        $contentinfo = $selectContent->query()->fetchAll();
        if( !empty($contentinfo) ) {
          $contentinformation = 1;
        } else {
          $contentinformation = 0;
        }
      } else {
        $page_id = $this->getWidgetizedPage()->page_id;
        $table = Engine_Api::_()->getDbtable('admincontent', 'sitepage');
        $selectContent = $table->select()
          ->from($table->info('name'), 'page_id')
          ->where("name IN ('core.container-tabs', 'sitepage.widgetlinks-sitepage')")
          ->where('page_id =?', $page_id);
        $contentinfo = $selectContent->query()->fetchAll();
        if( !empty($contentinfo) ) {
          $contentinformation = 1;
        } else {
          $contentinformation = 0;
        }
      }
    }
    return $contentinformation;
  }

  /**
   * Return option of showing the top title for widgets
   *
   * @param int $pages_id
   * @param int $layout
   * @return top title show or not
   */
  public function showtoptitle($layout, $pages_id)
  {
    if( !$layout ) {
      $page_id = $this->getWidgetizedPage()->page_id;
      if( !empty($page_id) ) {
        $table = Engine_Api::_()->getDbtable('content', 'core');
        $tablename = $table->info('name');
        $selectContent = $table->select()
          ->from($table->info('name'), 'page_id')
          ->where('name =?', 'core.container-tabs')
          ->where('page_id =?', $page_id)
          ->limit(1);
        $contentinfo = $selectContent->query()->fetchAll();
        if( empty($contentinfo) ) {
          $contentinformation = 1;
        } else {
          $contentinformation = 0;
        }
      }
    } else {
      $table = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
      $select = $table->select()
        ->from($table->info('name'), 'contentpage_id')
        ->where('name = ?', 'sitepage_index_view')
        ->where('page_id =?', $pages_id)
        ->limit(1);
      $row = $table->fetchRow($select);
      if( $row !== null ) {
        $page_id = $row->contentpage_id;
        $table = Engine_Api::_()->getDbtable('content', 'sitepage');
        $selectContent = $table->select()
          ->from($table->info('name'), 'contentpage_id')
          ->where('name =?', 'core.container-tabs')
          ->where('contentpage_id =?', $page_id)
          ->limit(1);
        $contentinfo = $selectContent->query()->fetchAll();
        if( empty($contentinfo) ) {
          $contentinformation = 1;
        } else {
          $contentinformation = 0;
        }
      } else {
        $page_id = $this->getWidgetizedPage()->page_id;
        $table = Engine_Api::_()->getDbtable('admincontent', 'sitepage');
        $selectContent = $table->select()
            ->from($table->info('name'), 'page_id')
            ->where('name =?', 'core.container-tabs')
            ->where('page_id =?', $page_id)->limit(1);
        ;
        $contentinfo = $selectContent->query()->fetchAll();
        if( empty($contentinfo) ) {
          $contentinformation = 1;
        } else {
          $contentinformation = 0;
        }
      }

      return $contentinformation;
    }
  }


    public function getTotalNoOfFollowersCount($pageId){

        // 1. Get userId who are following the given Page
        $pageFollowTable = Engine_Api::_()->getDbtable('follows', 'seaocore');
        $pageFollowTableName = $pageFollowTable->info('name');
        $pageFollowTableSelect = $pageFollowTable->select()
            ->from($pageFollowTableName,  'poster_id' )
            ->where('resource_type = ?', 'sitepage_page')
            ->where('resource_id = ?', $pageId);
        $pageFollowUserIds = $pageFollowTableSelect->query()->fetchAll();

        // 2. Get userId who are following Project
        $pageTable = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
        $projectTable = Engine_Api::_()->getDbtable( 'projects' , 'sitecrowdfunding' ) ;
        $favouriteTable = Engine_Api::_()->getDbtable('favourites','seaocore');
        $pageTableName = $pageTable->info('name') ;
        $projectTableName = $projectTable->info('name') ;
        $favouriteTableName = $favouriteTable->info('name') ;

        // 2.a. Get projects of the page
        $projectSelect = $projectTable->select()
                ->from($projectTableName, 'project_id')
                ->joinInner( $pageTableName , "$pageTableName . project_id = $projectTableName . project_id", null )
                ->where( $pageTableName . '.page_id = ?' , $pageId );
        $projectIds = $projectSelect->query()->fetchAll();
        $projectFollowerUserIds = array();
        if(count($projectIds) > 0){
            // 2.b. Get project's favourite
            $favouriteSelect = $favouriteTable->select()
                ->from($favouriteTableName, 'poster_id')
                ->where($favouriteTableName . '.resource_type = ?' , 'sitecrowdfunding_project' )
                ->where($favouriteTableName . '.resource_id IN (?)', (array) $projectIds);
            $projectFollowerUserIds = $favouriteSelect->query()->fetchAll();
        }

        // 3. get count of unique userIds
        $userId = array_unique(array_merge($pageFollowUserIds,$projectFollowerUserIds),SORT_REGULAR);
        $totalCount = count($userId);

        return $totalCount;

    }

  /**
   * Return true or false for ad show on paid pages
   *
   * @param object $sitepage
   * @return true or false
   */
  public function showAdWithPackage($sitepage)
  {
    if( !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.isActivate', 1) ) {
      return 0;
    }
    $params = array();
    $params['lim'] = 1;
    $fetch_community_ads = Engine_Api::_()->communityad()->getAdvertisement($params);
    if( empty($fetch_community_ads) )
      return 0;

    $package = $sitepage->getPackage();

    if( isset($package->ads) ) {
      return (bool) $package->ads;
    } else {
      if( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) ) {
        return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adwithpackage', 1);
      } else {
        return 0;
      }
    }
  }

  public function getModulelabel($title)
  {

    $menuitemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
    $selectMenuitemsTable = $menuitemsTable->select()->where('name =?', "core_admin_main_plugins_$title");
    $resultMenuitems = $menuitemsTable->fetchRow($selectMenuitemsTable);
    return $resultMenuitems;
  }

  public function getBannedUrls()
  {
    $merge_array = array();
    $businessUrlFinalArray = array();
    $groupUrlFinalArray = array();
    $storeUrlFinalArray = array();
    $staticpageUrlFinalArray = array();

    $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
    $urlArray = $bannedPageurlsTable->select()->from($bannedPageurlsTable, 'word')
        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

    $enableSitebusiness = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');

    if( $enableSitebusiness ) {
      $businessTable = Engine_Api::_()->getDbtable('business', 'sitebusiness');
      $businessUrlArray = $businessTable->select()->from($businessTable, ('business_url'))
          ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      foreach( $businessUrlArray as $url ) {
        $businessUrlFinalArray[] = strtolower($url);
      }
      $merge_array = array_merge($urlArray, $businessUrlFinalArray);
    } else {
      $merge_array = $urlArray;
    }

    $enableSitegroup = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup');

    if( $enableSitegroup ) {
      $groupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
      $groupUrlArray = $groupTable->select()->from($groupTable, 'group_url')
          ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      foreach( $groupUrlArray as $url ) {
        $groupUrlFinalArray[] = strtolower($url);
      }
      $merge_array = array_merge($merge_array, $groupUrlFinalArray);
    }

    $enableSitestore = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
    if( $enableSitestore ) {
      $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
      $storeUrlArray = $storeTable->select()->from($storeTable, 'store_url')
          ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      foreach( $storeUrlArray as $url ) {
        $storeUrlFinalArray[] = strtolower($url);
      }
      $merge_array = array_merge($merge_array, $storeUrlFinalArray);
    }

    $enableSitestaticpage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestaticpage');
    if( $enableSitestaticpage ) {
      $staticpageTable = Engine_Api::_()->getDbtable('pages', 'sitestaticpage');
      $staticpageUrlArray = $staticpageTable->select()->from($staticpageTable, 'page_url')
          ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      foreach( $staticpageUrlArray as $url ) {
        $staticpageUrlFinalArray[] = strtolower($url);
      }
      $merge_array = array_merge($merge_array, $staticpageUrlFinalArray);
    }

    return $merge_array;
  }

  /**
   * Return tabid
   *
   * @param string $widgetname
   * @param int $page_id
   * @param int $layout
   * @return tabid
   */
  public function getTabIdInfoIntegration($widgetname, $pageid, $layout, $resource_type = null)
  {

    $tab_id = '';
    if( !$layout ) {
      $tablecontent = Engine_Api::_()->getDbtable('content', 'core');
      $select = $tablecontent->select()
        ->from($tablecontent->info('name'), 'content_id')
        ->where('name = ?', $widgetname);

      if( !empty($resource_type) ) {
        $select->where('params LIKE ?', '%' . $resource_type . '%');
      }

      $select->order('order ASC')->limit(1);
      $tab_id = $select->query()->fetchColumn();
    } else {
      $table = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
      $select = $table->select()
        ->from($table->info('name'), 'contentpage_id')
        ->where('name = ?', 'sitepage_index_view')
        ->where('page_id = ?', $pageid)
        ->limit(1);
      $contentpage_id = $select->query()->fetchColumn();
      if( $contentpage_id ) {
        $tablecontent = Engine_Api::_()->getDbtable('content', 'sitepage');
        $select = $tablecontent->select()
          ->from($tablecontent->info('name'), 'content_id')
          ->where('name = ?', $widgetname)
          ->where('contentpage_id = ?', $contentpage_id);

        if( !empty($resource_type) ) {
          $select->where('params LIKE ?', '%' . $resource_type . '%');
        }

        $select->order('order ASC')->limit(1);
        $tab_id = $select->query()->fetchColumn();
      } else {
        $page_id = $this->getWidgetizedPage()->page_id;
        $tablecontent = Engine_Api::_()->getDbtable('admincontent', 'sitepage');
        $select = $tablecontent->select()
          ->from($tablecontent->info('name'), 'admincontent_id')
          ->where('name = ?', $widgetname)
          ->where('page_id = ?', $page_id);

        if( !empty($resource_type) ) {
          $select->where('params LIKE ?', '%' . $resource_type . '%');
        }

        $select->order('order ASC')->limit(1);
        $tab_id = $select->query()->fetchColumn();
      }
    }

    return $tab_id;
  }

  public function getBannedPageUrls()
  {

    $merge_array = array();
    // GET THE ARRAY OF BANNED PAGEURLS
    if( !defined('SITEPAGE_BANNED_URLS') ) {
      $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
      $urlArray = $bannedPageurlsTable->select()->from($bannedPageurlsTable, 'word')
          ->query()->fetchAll(Zend_Db::FETCH_COLUMN);


      $enableSitebusiness = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');
      if( $enableSitebusiness ) {
        $businessTable = Engine_Api::_()->getDbtable('business', 'sitebusiness');
        $businessUrlArray = $businessTable->select()->from($businessTable, 'business_url')
            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $merge_array = array_merge($urlArray, $businessUrlArray);
      } else {
        $merge_array = $urlArray;
      }

      $enableSitegroup = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup');
      if( $enableSitegroup ) {
        $groupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
        $groupUrlArray = $groupTable->select()->from($groupTable, 'group_url')
            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $merge_array = array_merge($merge_array, $groupUrlArray);
      }

      $enableSitestore = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
      if( $enableSitestore ) {
        $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
        $storeUrlArray = $storeTable->select()->from($storeTable, 'store_url')
            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $merge_array = array_merge($merge_array, $storeUrlArray);
      }

      $enableSitestaticpage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestaticpage');
      if( $enableSitestaticpage ) {
        $staticpageTable = Engine_Api::_()->getDbtable('pages', 'sitestaticpage');
        $staticpageUrlArray = $staticpageTable->select()->from($staticpageTable, 'page_url')
            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $merge_array = array_merge($merge_array, $staticpageUrlArray);
      }

      define('SITEPAGE_BANNED_URLS', serialize($merge_array));
    }
    return $banneUrlArray = unserialize(SITEPAGE_BANNED_URLS);
  }

  public function isLessThan417AlbumModule()
  {
    $pagealbumModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepagealbum');
    $pagealbumModuleVersion = $pagealbumModule->version;
    if( empty(Engine_Api::_()->seaocore()->checkVersion($pagealbumModuleVersion, '4.1.7')) ) {
      return true;
    } else {
      return false;
    }
  }

  //ACTION FOR LIKES
  public function autoLike($resource_id, $resource_type)
  {

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GETTING THE VALUE OF RESOURCE ID AND RESOURCE TYPE
// 		$resource_id = $this->_getParam('resource_id');
// 		$resource_type = $this->_getParam('resource_type');

    if( empty($viewer_id) ) {
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

    //GET THE VALUE OF LIKE ID
    //$like_id = $this->_getParam('like_id');
    //$status = $this->_getParam('smoothbox', 1);
    //$this->view->status = true;
    //GET LIKES.
    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
    $resource = Engine_Api::_()->getItem($resource_type, $resource_id);

    //CHECK FOR LIKE ID
    if( empty($like_id) ) {

      //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
      $like_id_temp = Engine_Api::_()->sitepage()->checkAvailability($resource_type, $resource_id);
      if( empty($like_id_temp[0]['like_id']) ) {

        if( !empty($resource) ) {
          $like_id = $likeTable->addLike($resource, $viewer);
          if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike') )
            Engine_Api::_()->sitelike()->setLikeFeed($viewer, $resource);
        }

        $notify_table = Engine_Api::_()->getDbtable('notifications', 'activity');
        $db = $likeTable->getAdapter();
        $db->beginTransaction();
        try {

          //CREATE THE NEW ROW IN TABLE
          if( !empty($getOwnerId) && $getOwnerId != $viewer_id ) {
            $user = Engine_Api::_()->getItem('user', $getOwnerId);
            $notifyData = $notify_table->addNotification($user, $viewer, $resource, 'liked', $resource->getShortType());
//            $notifyData->user_id = $getOwnerId;
//            $notifyData->subject_type = $viewer->getType();
//            $notifyData->subject_id = $viewer_id;
//            $notifyData->object_type = $object_type;
//            $notifyData->object_id = $resource_id;
//            $notifyData->type = 'liked';
//            $notifyData->params = $resource->getShortType();
//            $notifyData->date = date('Y-m-d h:i:s', time());
//            $notifyData->save();
          }

          //PASS THE LIKE ID.
          $this->view->like_id = $like_id;
          $this->view->error_mess = 0;
          $db->commit();
        } catch( Exception $e ) {

          $db->rollBack();
          throw $e;
        }
        $like_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Liked.');
      } else {
        $this->view->like_id = $like_id_temp[0]['like_id'];
        $this->view->error_mess = 1;
      }
    }
// 		else {
// 			if (!empty($resource)) {
// 				$likeTable->removeLike($resource, $viewer);
// 				  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
//           Engine_Api::_()->sitelike()->removeLikeFeed($viewer, $resource);
// 			}
// 			$this->view->error_mess = 0;
//
// 			$like_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Unliked.');
//     }
//     if (empty($status)) {
//       $this->_forward('success', 'utility', 'core', array(
//               'smoothboxClose' => true,
//               'parentRefresh' => true,
//               'messages' => array($like_msg)
//           )
//       );
//     }
  }

  /**
   * Return categoryid
   *
   * @param string $content_id
   * @param string $widgetname
   * @return categoryid
   */
  public function getSitepageCategoryid($content_id = null, $widgetname)
  {

    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    $page_id = $contentTable
      ->select()
      ->from($contentTable->info('name'), array('page_id'))
      ->where('content_id =?', $content_id)
      ->query()
      ->fetchColumn();
    //GET CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    $params = $contentTable
      ->select()
      ->from($contentTable->info('name'), array('params'))
      ->where('page_id =?', $page_id)
      ->where('name =?', $widgetname)
      ->query()
      ->fetchColumn();
    if( $params )
      $params = Zend_Json::decode($params);
    if( $params && isset($params['category_id']) && !empty($params['category_id']) ) {
      return $params['category_id'];
    } else {
      return 0;
    }
  }

  //SEND NOTIFICATION TO PAGE ADMIN WHEN OWN PAGE LIKE AND COMMENT.
  public function itemCommentLike($subject, $notificationType, $baseOnContentOwner = null)
  {

//         $item_title = $subject->getShortType();
//         $item_title_url = $subject->getHref();
//         $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
//         $item_title_link = "<a href='$item_title_baseurl'>" . $item_title . " </a>";
    //FETCH DATA
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') ) {

      $object_type = $subject->getType();
      $object_id = $subject->getIdentity();
// 					if ($notificationType == 'sitepage_contentcomment') {
// 						if ($baseOnContentOwner) {
// 								$subject_type = $viewer->getType();
// 								$subject_id = $viewer->getIdentity();
// 						} else {
// 								$subject_type = $viewer->getType();
// 								$subject_id = $viewer->getIdentity();
// 						}
// 					} else {
      $subject_type = $viewer->getType();
      $subject_id = $viewer->getIdentity();
      //}

      if( $notificationType == 'sitepage_contentlike' ) {
        $notification = '%"notificationlike":"1"%';
        $notificationFriend = '%"notificationlike":"2"%';
      } else {
        $notification = '%"notificationcomment":"1"%';
        $notificationFriend = '%"notificationcomment":"2"%';
      }

      $db = Zend_Db_Table_Abstract::getDefaultAdapter();

      $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();

      if( $friendId ) {
        $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitepage_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitepage_membership` WHERE (engine4_sitepage_membership.page_id = " . $subject->page_id . ") AND (engine4_sitepage_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitepage_membership.notification = 1) AND (engine4_sitepage_membership.action_notification LIKE '" . $notification . "' or (engine4_sitepage_membership.action_notification LIKE '" . $notificationFriend . "' and (engine4_sitepage_membership .user_id IN (" . join(",", $friendId) . "))))");
      } else {
        $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitepage_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitepage_membership` WHERE (engine4_sitepage_membership.page_id = " . $subject->page_id . ") AND (engine4_sitepage_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitepage_membership.notification = 1) AND (engine4_sitepage_membership.action_notification LIKE '" . $notification . "')");
      }
    } else {
      if( isset($subject->page_id) ) {
        $manageAdminsIds = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($subject->page_id, $viewer_id);

        foreach( $manageAdminsIds as $value ) {
          $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
          $action_notification = unserialize($value['action_notification']);
          if( !empty($value['notification']) && (in_array('like', $action_notification) || in_array('comment', $action_notification)) ) {

            $notifyApi->addNotification($user_subject, $viewer, $subject, $notificationType);
//          $row = $notifyApi->createRow();
//          $row->user_id = $user_subject->getIdentity();
//
//// 						if ($notificationType == 'sitepage_contentcomment') {
//// 								if ($baseOnContentOwner) {
//// 										$row->subject_type = $subject->parent_type;
//// 										$row->subject_id = $subjectParent->getIdentity();
//// 								} else {
//// 										$row->subject_type = $viewer->getType();
//// 										$row->subject_id = $viewer->getIdentity();
//// 								}
//// 						} else {
//          $row->subject_type = $viewer->getType();
//          $row->subject_id = $viewer->getIdentity();
//          //}
//          $row->type = "$notificationType";
//          $row->object_type = $subject->getType();
//          $row->object_id = $subject->getIdentity();
//          //$row->params = '{"eventname":"' . $item_title_link . '"}';
//          $row->date = date('Y-m-d H:i:s');
//          $row->save();
          }
        }
      }
    }
  }

  public function sendNotificationToFollowers($object, $actionObject, $notificationType, $count = null)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $page_id = $object->page_id;
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    //ITEM TITLE AND TILTE WITH LINK.
    $item_title = isset($object->title) ? $object->title : $object->getTitle();
    $item_title_url = $object->getHref();
    $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
    $item_title_link = "<a href='$item_title_baseurl'>" . $item_title . "</a>";
    $followersIds = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollowers('sitepage_page', $page_id, $viewer->getIdentity());
    $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $notidicationSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feed.type', 0);
    foreach( $followersIds as $value ) {

      if( !empty($notidicationSettings) ) {
        $notificationsTable->delete(array('type =?' => "$notificationType", 'object_type = ?' => $object->getType(), 'object_id = ?' => $object->getIdentity(), 'subject_id = ?' => $sitepage->getIdentity(), 'subject_type = ?' => $sitepage->getType(), 'user_id =?' => $value['poster_id']));
      } else {
        $notificationsTable->delete(array('type =?' => "$notificationType", 'object_type = ?' => $object->getType(), 'object_id = ?' => $object->getIdentity(), 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'user_id =?' => $value['poster_id']));
      }

      $user_subject = Engine_Api::_()->user()->getUser($value['poster_id']);

      if( !empty($notidicationSettings) ) {
        $subject = $sitepage;
      } else {
        $subject = $viewer;
      }

      if($notificationType == 'sitepagealbum_create') {
          $params =  array('count' => $count);
      } else {
          $params =  array('eventname' => $item_title_link);
      }

      $notificationsTable->addNotification($user_subject, $subject, $object, $notificationType, $params);
    }
  }

  public function allowInThisPage($sitepage, $packagePrivacyName, $levelPrivacyName)
  {
    if( $this->hasPackageEnable() ) {
      if( !$this->allowPackageContent($sitepage->package_id, "modules", $packagePrivacyName) ) {
        return false;
      }
    } else {
      $isPageOwnerAllow = $this->isPageOwnerAllow($sitepage, $levelPrivacyName);
      if( empty($isPageOwnerAllow) ) {
        return false;
      }
    }
    return true;
  }

  public function sendNotificationEmail($object, $actionObject, $notificationType = null, $emailType = null, $params = null, $count = null)
  {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');

    $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $notificationSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feed.type', 0);

    $manageAdminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $page_id = $object->page_id;

    $subject = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $owner = $subject->getOwner();

    //previous notification is delete.
    $notificationsTable->delete(array('type =?' => "$notificationType", 'object_type = ?' => "sitepage_page", 'object_id = ?' => $page_id, 'subject_id = ?' => $viewer_id));

    //GET PAGE TITLE AND PAGE TITLE WITH LINK.
    $pagetitle = $subject->title;
    //$page_url = Engine_Api::_()->sitepage()->getPageUrl($subject->page_id);
    //$page_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page_url), 'sitepage_entry_view', true);
    //$page_title_link = '<a href="' . $page_baseurl . '"  >' . $pagetitle . ' </a>';
    //ITEM TITLE AND TILTE WITH LINK.
    if( $notificationType == 'sitepagedocument_create' ) {
      $item_title = $object->sitepagedocument_title;
    } else {
      $item_title = $object->title;
    }
    $item_title_url = $object->getHref();
    $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
    $item_title_link = "<a href='$item_title_baseurl' style='text-decoration:none;' >" . $item_title . " </a>";

    //POSTER TITLE AND PHOTO WITH LINK
    $poster_title = $viewer->getTitle();
    $poster_url = $viewer->getHref();
    $poster_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $poster_url;
    $poster_title_link = "<a href='$poster_baseurl' style='font-weight:bold;text-decoration:none;' >" . $poster_title . " </a>";
    if( $viewer->photo_id ) {
      $photo = 'http://' . $_SERVER['HTTP_HOST'] . $viewer->getPhotoUrl('thumb.icon');
    } else {
      $photo = 'http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/application/modules/Sitepage/externals/images/nophoto_user_thumb_icon.png';
    }
    $image = "<img src='$photo' />";
    $posterphoto_link = "<tr><td colspan='2' style='height:20px;'></td></tr><tr></tr><tr><td valign='top' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding-right:15px;text-align:left'><a href='$poster_baseurl'  >" . $image . " </a></td><td valign='top' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;width:100%;text-align:left'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;width:100%'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif'><span style='color:#333333;'>";

    //MEASSGE WITH LINK.
    if( isset($actionObject) ) {
      $post_baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $actionObject->getHref();
    }
    $created = $post = ' ';
    if( $notificationType == 'sitepagealbum_create' ) {
      $post = $poster_title . ' created a new album in page: ' . $pagetitle;
      $created = ' created the album ';
    } elseif( $notificationType == 'sitepagedocument_create' ) {
      $post = $poster_title . ' created a new document in page: ' . $pagetitle;
      $created = ' created the document ';
    } elseif( $notificationType == 'sitepageevent_create' ) {
      $post = $poster_title . ' created a new event in page: ' . $pagetitle;
      $created = ' created the event ';
    } elseif( $notificationType == 'sitepagemusic_create' ) {
      $post = $poster_title . ' created a new playlist in page: ' . $pagetitle;
      $created = ' created the music ';
    } elseif( $notificationType == 'sitepagenote_create' ) {
      $post = $poster_title . ' created a new note in page: ' . $pagetitle;
      $created = ' created the note ';
    } elseif( $notificationType == 'sitepageoffer_create' ) {
      $post = $poster_title . ' created a new offer in page: ' . $pagetitle;
      $created = ' created the offer ';
    } elseif( $notificationType == 'sitepagepoll_create' ) {
      $post = $poster_title . ' created a new poll in page: ' . $pagetitle;
      $created = ' created the poll ';
    } elseif( $notificationType == 'sitepagevideo_create' ) {
      $post = $poster_title . ' posted a new video in page: ' . $pagetitle;
      $created = ' created the video ';
    } elseif( $notificationType == 'sitepagediscussion_create' ) {
      $post = $poster_title . ' created a new discussion in page: ' . $pagetitle;
      $created = ' created the discussion ';
    }
    if( !empty($post_baseUrl) ) {
      if( $params == 'Activity Comment' || $params == 'Activity Reply' ) {
        $post_link = "<a href='$post_baseUrl'  >" . 'post' . "</a>";
        $post_linkformail = "<table cellspacing='0' cellpadding='0' border='0' style='border-collapse:collapse;' width='90%'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:20px;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;' width='100%'><tr><td colspan='2' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;border-bottom:1px solid #dddddd;padding-bottom:5px;'><a style='font-weight:bold;margin-bottom:10px;text-decoration:none;' href='$post_baseUrl'>" . 'post' . "</a></td></tr><tr><td valign='top' style='padding:10px 15px 10px 10px;'><a href='$poster_baseurl'  >" . $image . " </a></td><td valign='top' style='padding-top:10px;font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;width:100%;text-align:left;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;width:100%;'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;text-decoration:none;text-decoration:none;'>" . $poster_title_link . 'post' . $item_title_link . '.' . "</td></tr></table></td></tr></table></td></tr></table>";
      } else {
        $post_link = "<a href='$post_baseUrl'  >" . $post . "</a>";
        $post_linkformail = "<table cellspacing='0' cellpadding='0' border='0' style='border-collapse:collapse;' width='90%'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:20px;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;' width='100%'><tr><td colspan='2' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;border-bottom:1px solid #dddddd;padding-bottom:5px;'><a style='font-weight:bold;margin-bottom:10px;text-decoration:none;' href='$post_baseUrl'>" . $post . "</a></td></tr><tr><td valign='top' style='padding:10px 15px 10px 10px;'><a href='$poster_baseurl'  >" . $image . " </a></td><td valign='top' style='padding-top:10px;font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;width:100%;text-align:left;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;width:100%;'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;text-decoration:none;'>" . $poster_title_link . $created . $item_title_link . '.' . "</td></tr></table></td></tr></table></td></tr></table>";
      }
    }

    //FETCH DATA
    if( empty($sitepagememberEnabled) ) {
      $manageAdminsIds = $manageAdminTable->getManageAdmin($page_id, $viewer_id);
      foreach( $manageAdminsIds as $value ) {
        $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
        $action_notification = unserialize($value['action_notification']);
        if( !empty($value['notification']) && in_array('created', $action_notification) ) {

          if( $notificationSettings == 1 ) {
            $notify_subject = $subject;
          } else {
            $notify_subject = $viewer;
          }

          if($notificationType == 'sitepagealbum_create') {
              $params =  array('count' => $count);
          } else {
              $params =  array('eventname' => $item_title_link);
          }

          $notificationsTable->addNotification($user_subject, $notify_subject, $object, $notificationType, $params);
        }

        //EMAIL SEND TO ALL MANAGEADMINS.
        $action_email = json_decode($value['action_email']);
        if( !empty($value['email']) && in_array('created', $action_email) ) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "$emailType", array(
            'page_title' => $pagetitle,
            'item_title' => $item_title,
            'body_content' => $post_linkformail,
          ));
        }
      }
    }

    //START SEND EMAIL TO ALL MEMBER WHO HAVE JOINED THE PAGE INCLUDE MANAGE ADMINS.
    if( !empty($sitepagememberEnabled) ) {
      $membersIds = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($page_id, $viewer_id, $viewer_id, 0, 1);
      foreach( $membersIds as $value ) {
        $action_email = json_decode($value['action_email']);
        $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
        if( $params != 'Activity Comment' && $params != 'Activity Reply' ) {
          if( !empty($value['email_notification']) && $action_email->emailcreated == 1 ) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "$emailType", array(
              'page_title' => $pagetitle,
              'item_title' => $item_title,
              'body_content' => $post_linkformail,
            ));
          } elseif( !empty($value['email_notification']) && $action_email->emailcreated == 2 ) {
            $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
            if( in_array($value['user_id'], $friendId) ) {
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "$emailType", array(
                'page_title' => $pagetitle,
                'item_title' => $item_title,
                'body_content' => $post_linkformail,
              ));
            }
          }
        }
      }
    }
    //END SEND EMAIL TO ALL MEMBER WHO HAVE JOINED THE PAGE INCLUDE MANAGE ADMINS.

    if( $params != 'Activity Comment' && $params != 'Pageevent Invite' && $params != 'Activity Reply' ) {
      $object_type = $subject->getType();
      $object_id = $subject->getIdentity();
      $subject_type = $viewer->getType();
      $subject_id = $viewer->getIdentity();
    } elseif( $params == 'Pageevent Invite' ) {
      $object_type = $object->getType();
      $object_id = $object->getIdentity();
//      $subject_type = $viewer->getType();
//      $subject_id = $viewer->getIdentity();
      if( $notificationSettings == 1 ) {
        $subject_type = $subject->getType();
        $subject_id = $subject->getIdentity();
      } else {
        $subject_type = $viewer->getType();
        $subject_id = $viewer->getIdentity();
      }
    }

    if( $params != 'Activity Comment' && $params != 'Activity Reply' ) {
      $notificationcreated = '%"notificationcreated":"1"%';
      $notificationsinglecodecreated = '%"notificationcreated":1%';
      $notificationfriendcreated = '%"notificationcreated":"2"%';
      $notificationfriendsinglecodecreated = '%"notificationcreated":2%';
      if( $count ) {
        $countparams = '{"count":"' . $count . '"}';
      } else {
        $countparams = null;
      }
      if( !empty($sitepagememberEnabled) ) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
        if( !empty($friendId) ) {
          $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitepage_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, '" . $countparams . "' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitepage_membership` WHERE (engine4_sitepage_membership.page_id = " . $subject->page_id . ") AND (engine4_sitepage_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitepage_membership.notification = 1) AND (engine4_sitepage_membership.action_notification LIKE '" . $notificationcreated . "' or engine4_sitepage_membership.action_notification LIKE '" . $notificationsinglecodecreated . "' or (engine4_sitepage_membership.action_notification LIKE '" . $notificationfriendcreated . "' or engine4_sitepage_membership.action_notification LIKE '" . $notificationfriendsinglecodecreated . "' and (engine4_sitepage_membership .user_id IN (" . join(",", $friendId) . "))))");
        } else {
          $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitepage_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, '" . $countparams . "' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitepage_membership` WHERE (engine4_sitepage_membership.page_id = " . $subject->page_id . ") AND (engine4_sitepage_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitepage_membership.notification = 1) AND (engine4_sitepage_membership.action_notification LIKE '" . $notificationcreated . "' OR engine4_sitepage_membership.action_notification LIKE '" . $notificationsinglecodecreated . "')");
        }
      }
    }
  }

  /**
   * Check If The Attachment Types in Activity Feed Should be Enabled or Not
   */
  public function enableComposer($composerType = null)
  {
    return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($composerType) && method_exists(Engine_Api::_()->getApi('core', $composerType), 'enableComposer') ? Engine_Api::_()->getApi('core', $composerType)->enableComposer() : false;
  }

  /**
   * Gets widgetized page
   *
   * @return Zend_Db_Table_Select
   */
  public function getMobileWidgetizedPage()
  {

    if( !Engine_Api::_()->hasModuleBootstrap('sitemobile') )
      return false;

    //GET CORE PAGE TABLE
    $tableNamePage = Engine_Api::_()->getDbtable('pages', 'sitemobile');
    $select = $tableNamePage->select()
      ->from($tableNamePage->info('name'), array('page_id', 'description', 'keywords'))
      ->where('name =?', 'sitepage_index_view')
      ->limit(1);

    return $tableNamePage->fetchRow($select);
  }

  public function showTabsWithoutContent()
  {
    return Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.show.tabs.without.content', 0);
  }

  public function checkEnableForMobile($moduleName)
  {
    if( !Engine_Api::_()->hasModuleBootstrap('sitemobile') )
      return false;

    //GET CORE PAGE TABLE
    $modulesSitemobile = Engine_Api::_()->getDbtable('modules', 'sitemobile');
    $enable_mobile = $modulesSitemobile->select()
      ->from($modulesSitemobile->info('name'), array('enable_mobile'))
      ->where('name =?', $moduleName)
      ->where('enable_mobile= ?', 1)
      ->query()
      ->fetchColumn();

    return $enable_mobile;
  }

  public function sendInviteEmail($object, $actionObject = null, $params = array(), $memberIdsArray = array())
  {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');

    $manageAdminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $page_id = $params['parent_id'];

    $subject = Engine_Api::_()->getItem($params['parent_type'], $page_id);
    $owner = $subject->getOwner();

    //GET PAGE TITLE AND PAGE TITLE WITH LINK.
    $pagetitle = $subject->title;
    $page_url = Engine_Api::_()->sitepage()->getPageUrl($subject->page_id);
    $page_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page_url), 'sitepage_entry_view', true);
    $page_title_link = '<a href="' . $page_baseurl . '"  >' . $pagetitle . ' </a>';

    //ITEM TITLE AND TILTE WITH LINK.
    $item_title = $object->title;
    $item_title_url = $object->getHref();
    $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
    $item_title_link = "<a href='$item_title_baseurl'  >" . $item_title . " </a>";


    //POSTER TITLE AND PHOTO WITH LINK
    $poster_title = $viewer->getTitle();
    $poster_url = $viewer->getHref();
    $poster_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $poster_url;
    $poster_title_link = "<a href='$poster_baseurl'  >" . $poster_title . " </a>";
    if( $viewer->photo_id ) {
      $photo = 'http://' . $_SERVER['HTTP_HOST'] . $viewer->getPhotoUrl('thumb.icon');
    } else {
      $photo = 'http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/application/modules/Sitepage/externals/images/nophoto_sitepage_thumb_icon.png';
    }
    $image = "<img src='$photo' />";
    $posterphoto_link = "<a href='$poster_baseurl'  >" . $image . " </a>";

    //MEASSGE WITH LINK.
    if( !empty($actionObject) && isset($actionObject) ) {
      $post_baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $actionObject->getHref();
      $post = $poster_title . ' Posted in ' . $pagetitle;
      $post_link = "<a href='$post_baseUrl'  >" . $post . " </a>";
    }

    //FETCH DATA
    if( $params['tempValue'] == 'InviteMembers' ) {
      foreach( $memberIdsArray as $value ) {
        $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
        if( !empty($value['email']) ) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, $params['emailType'], array(
            'page_title' => $pagetitle,
            'item_title_with_link' => $item_title_link,
            'item_title' => $item_title,
            'viewertitle_link' => $poster_title_link,
            'viewerphoto_link' => $posterphoto_link,
          ));
        }
      }
    } elseif( $params['tempValue'] == 'Pageevent Invite' ) {
      $pageMembers = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($page_id);
      foreach( $pageMembers as $value ) {

        if( $value['user_id'] == $object->owner_id )
          continue;

        $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
        if( !empty($value['email']) ) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, $params['emailType'], array(
            'page_title' => $pagetitle,
            'item_title_with_link' => $item_title_link,
            'item_title' => $item_title,
            'viewertitle_link' => $poster_title_link,
            'viewerphoto_link' => $posterphoto_link,
          ));
        }
      }
    } else {
      $manageAdminsIds = $manageAdminTable->getManageAdmin($page_id, $viewer_id);
      foreach( $manageAdminsIds as $value ) {
        $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
        if( !empty($value['email']) ) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, $params['emailType'], array(
            'page_title' => $pagetitle,
            'item_title_with_link' => $item_title_link,
            'item_title' => $item_title,
            'viewertitle_link' => $poster_title_link,
            'viewerphoto_link' => $posterphoto_link,
          ));
        }
      }
    }

    $object_type = $object->getType();
    $object_id = $object->getIdentity();
    $subject_type = $viewer->getType();
    $subject_id = $viewer->getIdentity();

    if( !empty($sitepagememberEnabled) ) {
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      if( $params['tempValue'] == 'InviteMembers' ) {
        $friendId = $memberIdsArray;
      } else {
        $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
      }
      if( !empty($friendId) ) {
        $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitepage_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $params['notificationType'] . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitepage_membership` WHERE (engine4_sitepage_membership.page_id = " . $subject->page_id . ") AND (engine4_sitepage_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitepage_membership.notification = 1 or (engine4_sitepage_membership.notification = 2 and (engine4_sitepage_membership .user_id IN (" . join(",", $friendId) . "))))");
      } else {
        $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitepage_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $params['notificationType'] . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitepage_membership` WHERE (engine4_sitepage_membership.page_id = " . $subject->page_id . ") AND (engine4_sitepage_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitepage_membership.notification = 1)");
      }
    }
  }

  public function getsecondLevelMaps($option_id)
  {
    // Get second level fields
    $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitepage_page');
    $secondLevelMaps = array();
    $secondLevelFields = array();

    $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
    if( !empty($secondLevelMaps) ) {
      foreach( $secondLevelMaps as $map ) {
        $secondLevelFields[$map->child_id] = $map->getChild();
      }
    }
    return $secondLevelMaps;
  }

  /**
   * Gte Profiletype Label
   *
   * @param int $option_id
   * @return string $profie
   */
  public function getProfileTypeName($option_id)
  {

    $table_options = Engine_Api::_()->fields()->getTable('sitepage_page', 'options');
    $profie = $table_options->select()
      ->from($table_options->info('name'), 'label')
      ->where('option_id = ?', $option_id)
      ->query()
      ->fetchColumn();
    return $profie;
  }

  public function updateMemberCount($sitepage)
  {

    if( !$sitepage )
      return;

    $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');

    if( !$sitepagememberEnabled )
      return;

    $tableSitepageMembership = Engine_Api::_()->getDbtable('membership', 'sitepage');
    $tableSitepageMembershipName = $tableSitepageMembership->info('name');
    $count = $tableSitepageMembership->select()
      ->from($tableSitepageMembershipName, array('count(*) as count'))
      ->where('resource_id =?', $sitepage->getIdentity())
      ->where('active =?', 1)
      ->where('resource_approved =?', 1)
      ->where('user_approved =?', 1)
      ->query()
      ->fetchColumn();

    $sitepage->member_count = $count;
    $sitepage->save();
  }

  /**
   * Check here that show payment link or not
   * $params $page_id : Id of page
   * @return bool $showLink
   * */
  public function canShowCancelLink($page_id)
  {


    if( !Engine_Api::_()->sitepage()->hasPackageEnable() )
      return;

    $showLink = false;
    $page = Engine_Api::_()->getItem('sitepage_page', $page_id);


    if( !empty($page) ) {
      $package = $page->getPackage();

      if( !$package->isFree() && $page->status == "active" && !$package->isOneTime() && !empty($page->approved) ) {
        return (bool) true;
      }
    }

    return (bool) $showLink;
  }

  public function checkVersion($databaseVersion, $checkDependancyVersion)
  {
    $f = $databaseVersion;
    $s = $checkDependancyVersion;
    if( strcasecmp($f, $s) == 0 )
      return -1;

    $fArr = explode(".", $f);
    $sArr = explode('.', $s);
    if( count($fArr) <= count($sArr) )
      $count = count($fArr);
    else
      $count = count($sArr);

    for( $i = 0; $i < $count; $i++ ) {
      $fValue = $fArr[$i];
      $sValue = $sArr[$i];
      if( is_numeric($fValue) && is_numeric($sValue) ) {
        if( $fValue > $sValue )
          return 1;
        elseif( $fValue < $sValue )
          return 0;
        else {
          if( ($i + 1) == $count ) {
            return -1;
          } else
            continue;
        }
      }
      elseif( is_string($fValue) && is_numeric($sValue) ) {
        $fsArr = explode("p", $fValue);

        if( $fsArr[0] > $sValue )
          return 1;
        elseif( $fsArr[0] < $sValue )
          return 0;
        else {
          return 1;
        }
      } elseif( is_numeric($fValue) && is_string($sValue) ) {
        $ssArr = explode("p", $sValue);

        if( $fValue > $ssArr[0] )
          return 1;
        elseif( $fValue < $ssArr[0] )
          return 0;
        else {
          return 0;
        }
      } elseif( is_string($fValue) && is_string($sValue) ) {
        $fsArr = explode("p", $fValue);
        $ssArr = explode("p", $sValue);
        if( $fsArr[0] > $ssArr[0] )
          return 1;
        elseif( $fsArr[0] < $ssArr[0] )
          return 0;
        else {
          if( $fsArr[1] > $ssArr[1] )
            return 1;
          elseif( $fsArr[1] < $ssArr[1] )
            return 0;
          else {
            return -1;
          }
        }
      }
    }
  }

  //Function to return Currency Conversion rate
  public function getPriceWithCurrency($price, $priceOnly = 0, $search = 0)
  {

    if( empty($price) ) {
      return $price;
    }
    if( Engine_Api::_()->hasModuleBootstrap('sitemulticurrency') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemulticurrency') && Engine_Api::_()->getDbtable('modules', 'sitemulticurrency')->isModuleEnable('sitepage') ) {

      $priceStr = Engine_Api::_()->sitemulticurrency()->convertCurrencyRate($price, $priceOnly, $search);
    } else {
      $defaultParams = array();
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      if( empty($viewer_id) ) {
        $defaultParams['locale'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
      }
      $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
      $defaultParams['precision'] = 2;
      $price = (float) $price;
      if( !empty($priceOnly) )
        return $price;
      $priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($price, $currency, $defaultParams);
    }

    return $priceStr;
  }

  public function getTemplateData($param = null)
  {
    // Get packages and ordering
    $temp = Engine_Api::_()->getDbtable('packages', 'sitepage')->getSubscriptionPackages();
    foreach( $temp as $key => $value )
      $packages[$value['package_id']] = $value;

    // Get the package order
    $packageOrder = array();
    $packageOrder = Engine_Api::_()->getDbtable('packageorder', 'sitepage')->getPackageOrder();
    asort($packageOrder);
    $maxorder = max($packageOrder);

    // Remove package order packages that do not exist
    foreach( $packageOrder as $key => $value )
      if( !array_key_exists($key, $packages) )
        unset($packageOrder[$key]);

    // Enter package ids not in order table
    if( count($packages) != count($packageOrder) )
      foreach( $packages as $key => $value )
        if( !array_key_exists($key, $packageOrder) )
          $packageOrder[$key] = (string) ++$maxorder;

    // Get activated template ad its structure type
    $activeTemplate = ($param == null) ? Engine_Api::_()->getDbTable('templates', 'sitepage')->getActivatedTemplate() : Engine_Api::_()->getDbTable('templates', 'sitepage')->getTemplate($param);
    $layout_id = $activeTemplate['layout'];
    $structureType = Engine_Api::_()->getDbTable('layouts', 'sitepage')->getLayoutStructureType($layout_id);
    $templateStyle = json_decode($activeTemplate['template_values'], true);
    $best_choice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.popularplan', '0');

    // Get if feature is enabled
    // $structureTypeSettings = json_decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.structuretype'.$structureType,null),true);
    // $isFeatureEnabled = $structureTypeSettings['features'];
    if( $structureType == "type2" ) {
      $isFeatureEnabled = 0;
    } else {
      $isFeatureEnabled = 1;
    }

    $featuresRowData = Engine_Api::_()->getDbTable('fields', 'sitepage')->getFields($structureType);
    // var_dump($featuresRowData);die();
    foreach( $featuresRowData as $value ) {
      $features[$value['feature_order']]['field_id'] = $value['field_id'];
      $features[$value['feature_order']]['label'] = $value['feature_label'];
    }
    ksort($features);

    foreach( $featuresRowData as $feature ) {
      $valueRowData = Engine_Api::_()->getDbTable('values', 'sitepage')->getFieldValues($feature['field_id']);
      if( !empty($valueRowData) )
        foreach( $valueRowData as $value ) {
          $fieldValues[$feature['field_id']][$value['package_id']] = $value['value'];
        } else
        $fieldValues[$feature['field_id']] = [];
    }

    $return_arr = array(
      'packages' => $packages,
      'packageOrder' => $packageOrder,
      'isFeatureEnabled' => $isFeatureEnabled,
      'activeTemplate' => $activeTemplate,
      'layout_id' => $layout_id,
      'structureType' => $structureType,
      'best_choice' => $best_choice,
      'templateStyle' => $templateStyle,
      'featuresRowData' => $featuresRowData,
      'features' => $features,
      'valueRowData' => $valueRowData,
      'fieldValues' => $fieldValues,
    );
//      var_dump($packages)."<br>";die();
//      var_dump($packageOrder)."<br>";die();
//      var_dump($isFeatureEnabled)."<br>";die();
//      var_dump($activeTemplate)."<br>";die();
//      var_dump($layout_id)."<br>";die();
//      var_dump($structureType)."<br>";die();
//      var_dump($best_choice)."<br>";die();
//      var_dump($templateStyle)."<br>";die();
//      var_dump($featuresRowData)."<br>";die();
//      var_dump($features)."<br>";die();
//      var_dump($valueRowData)."<br>";die();
//      var_dump($fieldValues);
//      die();
    return $return_arr;
  }

  function toJsonReadableFormat($string = null)
  {
    if( empty($string) )
      return;

    $string = str_replace("'", "&#39;", $string);
    $string = str_replace('"', "&#34;", $string);

    return $string;
  }

  public function getCurrencySymbol()
  {
    $localeObject = Zend_Registry::get('Locale');
    $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $currencySymbol = Zend_Locale_Data::getContent($localeObject, 'currencysymbol', $currencyCode);
    return $currencySymbol;
  }

  public function getReccurenceDuration($package_id)
  {
    $package = Engine_Api::_()->getDbtable('packages', 'sitepage')->getSubscriptionPackage($package_id);

    if( (float) $package->price == 0.00 || $package->recurrence == '0' )
      return '';
    else if( $package->recurrence == '1' || $package->recurrence == '0' || $package->recurrence_type == 'forever' )
      return strtolower($this->translateDuration($package->recurrence_type));
    else
      return "per " . $package->recurrence . ' ' . $package->recurrence_type;
  }

  public function translateDuration($value)
  {
    if( $value == 'year' )
      return "Yearly";
    elseif( $value == 'month' )
      return "Monthly";
    elseif( $value == 'forever' )
      return "One-time";
    elseif( $value == 'day' )
      return "Daily";
    elseif( $value == 'week' )
      return "Weekly";
    else
      return "-";
  }
  public function isOnline($sitepage)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $date = new Zend_Date();
    $page_owner = Engine_Api::_()->getItem('user', $sitepage->owner_id);
    $date->setTimezone($page_owner->timezone);
    $time = $date->get(Zend_Date::TIME_MEDIUM);
    if($sitepage->days) {
      return true;
    }
    else {
      $day = strtolower($date->get(Zend_Date::WEEKDAY));
      $timings = Engine_Api::_()->getItemTable('sitepage_timing')->getTimings($sitepage->page_id, $day);
      if(is_null($timings)) {
        return false;
      }
      $now = strtotime($time);
      $start = strtotime($timings['start']);
      $end = strtotime($timings['end']);
      if (($now >= $start ) && ($now <= $end))
      return true;
    }
    return false;
  }

  public function getMappedLayout($row = array()) {
    $pageTable = Engine_Api::_()->getDbtable('definedlayouts', 'sitepage');
    foreach ($row as $key => $value) {
      $option[$key] = array();
      $pageName = $pageTable->select()
                            ->from($pageTable->info('name'), array('title'))
                            ->where('definedlayout_id = ?',$value->layout_id)
                            ->query()
                            ->fetchColumn();
      $option[$key]['layout_name'] = $pageName;
      $option[$key]['layout_id'] = $value->layout_id;

      if(array_key_exists('category_id', $value->toArray())) {
        $option[$key]['id'] = $value->category_id;
        $option[$key]['title'] = $value->category_name;
        $option[$key]['name'] = 'categories';
      } else if(array_key_exists('package_id', $value->toArray())) {
        $option[$key]['title'] = $value->title;
        $option[$key]['name'] = 'packages';
        $option[$key]['id'] = $value->package_id;
      }
    }
    return $option;
  }
}
