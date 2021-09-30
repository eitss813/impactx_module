<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Page.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_Page extends Core_Model_Item_Abstract {

    // Properties
    protected $_parent_type = 'user';
    protected $_searchTriggers = array('title', 'body', 'search', 'approved', 'closed', 'declined', 'draft', 'expiration_date', 'location');
    protected $_parent_is_owner = true;
    protected $_modifiedTriggers = false;
    protected $_user;
    protected $_gateway;
    protected $_package;
    protected $_statusChanged;
    protected $_keyWords = null;

    public function isSearchable() {

        return ( (!isset($this->search) || $this->search) && !empty($this->_searchTriggers) && is_array($this->_searchTriggers) && empty($this->closed) && $this->approved && empty($this->declined) && !empty($this->draft) && ($this->expiration_date > date("Y-m-d H:i:s")));
    }

    public function getMediaType() {
        $translate = Zend_Registry::get('Zend_Translate');
        return $translate->translate('MEDIA_TYPE_SITEPAGE_PAGE');
    }

    public function membership() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('membership', 'sitepage'));
    }

    // General
    public function getShortType($inflect = false) {

        if ($inflect)
            return 'Page';

        return 'page';
    }

    public function isViewableByNetwork() {
        $regName = 'view_privacy_' . $this->getGuid();
        if (!Zend_Registry::isRegistered($regName)) {
            $flage = true;
            $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.network', 0);
            $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.networkprofile.privacy', 0);
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($enableNetwork && $viewPricavyEnable && !$this->isOwner($viewer)) {
                $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);
                if (Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable()) {

                    if ($this->networks_privacy) {
                        if (!empty($viewerNetworkIds)) {
                            $pageNetworkId = explode(",", $this->networks_privacy);
                            $commanIds = array_intersect($pageNetworkId, $viewerNetworkIds);
                            if (empty($commanIds))
                                $flage = false;
                        } else {
                            $flage = false;
                        }
                    }
                } else {
                    if (!empty($viewerNetworkIds)) {
                        $ownerNetworkIds = $networkMembershipTable->getMembershipsOfIds($this->getOwner('user'));
                        if ($ownerNetworkIds) {
                            $commanIds = array_intersect($ownerNetworkIds, $viewerNetworkIds);
                            if (empty($commanIds))
                                $flage = false;
                        }
                    }
                }
            }
            Zend_Registry::set($regName, $flage);
        } else {
            $flage = Zend_Registry::get($regName);
        }
        return $flage;
    }

    // Ownership
    public function isOwner($owner) {
        return Engine_Api::_()->sitepage()->isPageOwner($this, $owner);
    }

    public function getCategory() {
        if (empty($this->category_id))
            return;

        return Engine_Api::_()->getItem('sitepage_category', $this->category_id);
    }

    public function getCategoryName() {
        $category = $this->getCategory();

        return !empty($category) ? $category->category_name : '';
    }

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {
        $slug = $this->getSlug();
        $params = array_merge(array(
            'route' => 'sitepage_entry_view',
            'reset' => true,
            'page_url' => $this->page_url,
                ), $params);
        $page_url = $this->page_url;
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        $urlO = Zend_Controller_Front::getInstance()->getRouter()
                ->assemble($params, $route, $reset);
        $sitepageUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageurl');
        if (!empty($sitepageUrlEnabled)) {
            $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manifestUrlS', "pageitem");
            $banneUrlArray = Engine_Api::_()->sitepage()->getBannedPageUrls();

            // GET THE PAGE LIKES AFTER WHICH SHORTEN PAGEURL WILL BE WORK
            $page_likes = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.likelimit.forurlblock', "5");
            $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.change.url', 1);
            $replaceStr = str_replace("/" . $routeStartS . "/", "/", $urlO);
            if ((!empty($change_url)) && $this->like_count >= $page_likes && !in_array($page_url, $banneUrlArray)) {
                $urlO = $replaceStr;
            }
        }
        return $urlO;
    }

    /**
     * Get page description
     *
     * @return string
     */
    public function getDescription() {
        // @todo decide how we want to handle multibyte string functions
        $tmpBody = strip_tags($this->body);
        return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
    }

    public function getKeywords($separator = ' ')
  {
    if ($this->_keyWords === null) {
    $keywords = array();
    foreach( $this->tags()->getTagMaps() as $tagmap ) {
      $tag = $tagmap->getTag();
      if( !empty($tag) ) {
      $keywords[] = $tag->getTitle();
      }
    }
        $this->_keyWords = $keywords;
    }
    $keywords = $this->_keyWords;
    if( null === $separator ) {
      return $keywords;
    }

    return join($separator, $keywords);
  }

    /**
     * Set page main photo
     *
     */
    public function setPhoto($photo) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            $erro_msgg = Zend_Registry::get('Zend_Translate')->_('invalid argument passed to setPhoto');
            throw new Sitepage_Model_Exception($erro_msgg);
        }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'sitepage_page',
            'parent_id' => $this->getIdentity()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Add autorotation for uploded images. It will work only for SocialEngine-4.8.9 Or more then.
        $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
        if (!empty($hasVersion)) {
            // Resize image (main)
            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(720, 720)
                    ->write($path . '/m_' . $name)
                    ->destroy();

            // Resize image (profile)
            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(300, 500)
                    ->write($path . '/p_' . $name)
                    ->destroy();

            // Resize image (normal)
            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize(140, 160)
                    ->write($path . '/in_' . $name)
                    ->destroy();
        } else {
            // Resize image (main)
            $image = Engine_Image::factory();
            $image->open($file)
                    ->autoRotate()
                    ->resize(720, 720)
                    ->write($path . '/m_' . $name)
                    ->destroy();

            // Resize image (profile)
            $image = Engine_Image::factory();
            $image->open($file)
                    ->autoRotate()
                    ->resize(300, 500)
                    ->write($path . '/p_' . $name)
                    ->destroy();

            // Resize image (normal)
            $image = Engine_Image::factory();
            $image->open($file)
                    ->autoRotate()
                    ->resize(140, 160)
                    ->write($path . '/in_' . $name)
                    ->destroy();
        }

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $name)
                ->destroy();

        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);


        // Add to album
        $viewer = Engine_Api::_()->user()->getViewer();
        $photoTable = Engine_Api::_()->getItemTable('sitepage_photo');
        $album_id = '';
        $sitepageAlbum = $this->getSingletonAlbum($album_id);
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'page_id' => $this->getIdentity(),
            'album_id' => $sitepageAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $sitepageAlbum->getIdentity(),
        ));
        $photoItem->save();

        // Update row
        $this->modified_date = date('Y-m-d H:i:s');
        $this->photo_id = $photoItem->file_id;
        $this->save();

        return $this;
    }

    /**
     * Get Photo
     *
     * @return object $photo
     */
    public function getPhoto($photo_id) {
        $photoTable = Engine_Api::_()->getItemTable('sitepage_photo');
        $select = $photoTable->select()
                ->where('file_id = ?', $photo_id)
                ->limit(1);

        $photo = $photoTable->fetchRow($select);
        return $photo;
    }

    /**
     * Get Page Admins
     *
     * @return object $admins
     */
    public function getPageAdmins() {
        $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $select = $manageadminTable->select()
                ->from($manageadminTable->info('name'), 'user_id')
                ->where('page_id = ?', $this->getIdentity());
        $user_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return Engine_Api::_()->getItemMulti('user', $user_ids);
    }

    /**
     * Get Page Style
     *
     * @return object $photo
     */
    public function getPageStyle() {
        $table = Engine_Api::_()->getDbtable('styles', 'core');
        $select = $table->select()
                ->where('type = ?', $this->getType())
                ->where('id = ?', $this->getIdentity());
        $row = $table->fetchRow($select);
        $style = null;
        if (null !== $row) {
            $style = $row->style;
        }
        return $style;
    }

    /**
     * Set location
     *
     */
    public function setLocation($params = null, $location_name = null) {
        $id = $this->page_id;
        $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);
        if ($locationFieldEnable) {
            $sitepage = $this;

            if (empty($params)) {
                if (!empty($sitepage))
                    $location = $sitepage->location;
            } else {
                $location = $params;
            }

            if (!empty($location)) {
                $locationTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
                $locationName = $locationTable->info('name');

                if (empty($params)) {
                    //$locationRow = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation(array('id' => $id));
                }

                $locationRow = $locationTable->getLocation(array('location' => $location));
                if (isset($_POST['locationParams']) && $_POST['locationParams']) {
                    if (is_string($_POST['locationParams']))
                        $_POST['locationParams'] = Zend_Json_Decoder::decode($_POST['locationParams']);

                    try {
                        $loctionV = $_POST['locationParams'];
                        $loctionV['page_id'] = $id;
                        $loctionV['zoom'] = 16;
                        if (empty($locationRow))
                            $locationRow = $locationTable->createRow();
                        $locationRow->setFromArray($loctionV);
                        $locationRow->save();
                    } catch (Exception $e) {
                        throw $e;
                    }

                    return;
                }

                $selectLocQuery = $locationTable->select()->where('location = ?', $location);
                $locationValue = $locationTable->fetchRow($selectLocQuery);

                $enableSocialengineaddon = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('seaocore');

                if (empty($locationValue)) {
                    $getSEALocation = array();
                    if (!empty($enableSocialengineaddon)) {
                        $getSEALocation = Engine_Api::_()->getDbtable('locations', 'seaocore')->getLocation(array('location' => $location));
                    }
                    if (empty($getSEALocation)) {

                        $locationLocal = $location;
                        $urladdress = urlencode($locationLocal);
                        $delay = 0;
                        // Iterate through the rows, geocoding each address
                        $geocode_pending = true;
                        while ($geocode_pending) {
                            $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=$urladdress";
                            $ch = curl_init();
                            $timeout = 5;
                            curl_setopt($ch, CURLOPT_URL, $request_url);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                            ob_start();
                            curl_exec($ch);
                            curl_close($ch);

                            $get_value = ob_get_contents();
                            if (empty($get_value)) {
                                $get_value = @file_get_contents($request_url);
                            }
                            $json_resopnse = !empty($get_value) ? Zend_Json::decode($get_value) : array();

                            ob_end_clean();
                            $status = $json_resopnse['status'];
                            if (strcmp($status, "OK") == 0) {
                                // Successful geocode
                                $geocode_pending = false;
                                $result = $json_resopnse['results'];

                                // Format: Longitude, Latitude, Altitude
                                $lat = $result[0]['geometry']['location']['lat'];
                                $lng = $result[0]['geometry']['location']['lng'];
                                $f_address = $result[0]['formatted_address'];


                                $len_add = count($result[0]['address_components']);

                                $address = '';
                                $country = '';
                                $state = '';
                                $zip_code = '';
                                $city = '';
                                for ($i = 0; $i < $len_add; $i++) {
                                    $types_location = $result[0]['address_components'][$i]['types'][0];

                                    if ($types_location == 'country') {

                                        $country = $result[0]['address_components'][$i]['long_name'];
                                    } else if ($types_location == 'administrative_area_level_1') {
                                        $state = $result[0]['address_components'][$i]['long_name'];
                                    } else if ($types_location == 'administrative_area_level_2') {
                                        $city = $result[0]['address_components'][$i]['long_name'];
                                    } else if ($types_location == 'postal_code' || $types_location == 'zip_code') {
                                        $zip_code = $result[0]['address_components'][$i]['long_name'];
                                    } else if ($types_location == 'street_address') {
                                        if ($address == '')
                                            $address = $result[0]['address_components'][$i]['long_name'];
                                        else
                                            $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
                                    }else if ($types_location == 'locality') {
                                        if ($address == '')
                                            $address = $result[0]['address_components'][$i]['long_name'];
                                        else
                                            $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
                                    }else if ($types_location == 'route') {
                                        if ($address == '')
                                            $address = $result[0]['address_components'][$i]['long_name'];
                                        else
                                            $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
                                    }else if ($types_location == 'sublocality') {
                                        if ($address == '')
                                            $address = $result[0]['address_components'][$i]['long_name'];
                                        else
                                            $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
                                    }
                                }
                                //   $db = Engine_Db_Table::getDefaultAdapter();
                                //   $db->beginTransaction();

                                try {
                                    // Create sitepage
                                    $loctionV = array();
                                    $loctionV['page_id'] = $id;
                                    $loctionV['latitude'] = $lat;
                                    $loctionV['location'] = $locationLocal;
                                    $loctionV['longitude'] = $lng;
                                    $loctionV['formatted_address'] = $f_address;
                                    $loctionV['country'] = $country;
                                    $loctionV['state'] = $state;
                                    $loctionV['zipcode'] = $zip_code;
                                    $loctionV['city'] = $city;
                                    $loctionV['address'] = $address;
                                    $loctionV['zoom'] = 16;
                                    if (!empty($location_name))
                                        $loctionV['locationname'] = $location_name;
                                    if (empty($locationRow))
                                        $locationRow = $locationTable->createRow();

                                    $locationRow->setFromArray($loctionV);
                                    $locationRow->save();
                                    // Commit
                                    // $db->commit();

                                    if (!empty($enableSocialengineaddon)) {
                                        $location = Engine_Api::_()->getDbtable('locations', 'seaocore')->setLocation($loctionV);
                                    }
                                } catch (Exception $e) {
                                    // $db->rollBack();
                                    throw $e;
                                }
                            } else if (strcmp($status, "620") == 0) {
                                // sent geocodes too fast
                                $delay += 100000;
                            } else {
                                // failure to geocode
                                $geocode_pending = false;
                                echo "Address " . $locationLocal . " failed to geocoded. ";
                                echo "Received status " . $status . "\n";
                            }
                            usleep($delay);
                        }
                    } else {
                        //  $db = Engine_Db_Table::getDefaultAdapter();
                        //  $db->beginTransaction();

                        try {
                            // Create sitepage location
                            $loctionV = array();
                            if (empty($locationRow))
                                $locationRow = $locationTable->createRow();
                            $value = $getSEALocation->toarray();
                            unset($value['location_id']);
                            $value['page_id'] = $id;
                            if (!empty($location_name))
                                $value['locationname'] = $location_name;
                            $locationRow->setFromArray($value);
                            $locationRow->save();
                            // Commit
                            // $db->commit();
                        } catch (Exception $e) {
                            // $db->rollBack();
                            throw $e;
                        }
                    }
                } else {
                    // $db = Engine_Db_Table::getDefaultAdapter();
                    // $db->beginTransaction();

                    try {
                        // Create sitepage location
                        $loctionV = array();
                        if (empty($locationRow))
                            $locationRow = $locationTable->createRow();
                        $value = $locationValue->toarray();
                        unset($value['location_id']);
                        $value['page_id'] = $id;
                        if (!empty($location_name))
                            $value['locationname'] = $location_name;
                        $locationRow->setFromArray($value);
                        $locationRow->save();
                        // Commit
                        //  $db->commit();
                    } catch (Exception $e) {
                        //  $db->rollBack();
                        throw $e;
                    }
                }
            }
        }
    }

    /**
     * Get Page Album
     *
     * @return object $album
     */
    public function getSingletonAlbum($album_id) {
        $table = Engine_Api::_()->getItemTable('sitepage_album');
        $select = $table->select()
                ->where('page_id = ?', $this->getIdentity())
                ->order('album_id ASC')
                ->limit(1);

        if (!empty($album_id)) {
            $select->where('album_id = ?', $album_id);
        }
        $album = $table->fetchRow($select);

        if (null === $album) {
            $album = $table->createRow();
            $album->setFromArray(array(
                'title' => $this->getTitle(),
                'page_id' => $this->getIdentity(),
                'search' => 1,
                'view_count' => 1,
            ));
            $id = $album->save();

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');

            $tagMax = array_search('registered', $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
            }

            //COMMENT PRIVACY
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $auth_comment = "everyone";
            $commentMax = array_search($auth_comment, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
            }
        }

        return $album;
    }

    /**
     * Gets a proxy object for the fields handler
     *
     * @return Engine_ProxyObject
     */
    public function fields() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getApi('core', 'fields'));
    }

    public function getOffer() {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $urlO = $request->getRequestUri();
        $request_url = explode('/', $urlO);
        $param = 1;
        if (empty($request_url['2'])) {
            $param = 0;
        }
        $return_url = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
        $currentUrl = urlencode($urlO);

        $str = null;

        $getPackageOffer = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepageoffer');
        if (empty($getPackageOffer)) {
            return;
        }

        if (!Engine_Api::_()->sitepage()->isPageOwnerAllow($this, 'offer')) {
            return;
        }

        if (!Engine_Api::_()->sitepage()->hasPackageEnable() || !Engine_Api::_()->sitepage()->allowPackageContent($this->package_id, "modules", "sitepageoffer")) {
            return;
        }

        if ($this->offer) {
            $sitepageoffersTable = Engine_Api::_()->getDbtable('offers', 'sitepageoffer');
            $result = $sitepageoffersTable->getSitepageoffer($this->page_id);
            if (empty($result)) {
                return;
            }
            $today = date("Y-m-d H:i:s");
            $var = '';
            if ($result->end_settings == 1 && ($result->end_time >= $today)) {
                $var = 1;
            } elseif ($result->end_settings == 0) {
                $var = 2;
            }
            if ($var == 1 || $var == 2) {
                $view = Zend_Registry::get('Zend_View');
                $view = clone $view;
                $view->clearVars();
                $tmpBody = $result->description;
                $offer_description = Engine_String::strlen($tmpBody) > 165 ? Engine_String::substr($tmpBody, 0, 165) . '...' : $tmpBody;
                $content = '';
                $content .= '
					' . $view->htmlLink($this->getHref()) . '
					' . nl2br($view->viewMore($offer_description)) . '';

                $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);

                if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                    $tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageoffer.sitemobile-profile-sitepageoffers', $this->page_id, $layout);
                } else {
                    $tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageoffer.profile-sitepageoffers', $this->page_id, $layout);
                }
                $offer_tabinformation = $view->url(array('user_id' => $result->owner_id, 'offer_id' => $result->offer_id, 'tab' => $tab_id, 'slug' => $result->getSlug()), 'sitepageoffer_view');
                $offer_title = "<a href=$offer_tabinformation>$result->title</a>";
                if (strlen($tmpBody) >= 165) {
                    $error_msg = Zend_Registry::get('Zend_Translate')->_('View More');
                    $content .= "<a href=$offer_tabinformation>$error_msg</a>";
                }

                if ($result->claim_count == -1 && ($result->end_time > $today || $result->end_settings == 0)) {
                    $show_offer_claim = 1;
                } elseif ($result->claim_count > 0 && ($result->end_time > $today || $result->end_settings == 0)) {
                    $show_offer_claim = 1;
                } else {
                    $show_offer_claim = 0;
                }

                $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

                $claim_value = Engine_Api::_()->getDbTable('claims', 'sitepageoffer')->getClaimValue($viewer_id, $result->offer_id, $result->page_id);

                $content_claim = '';
                $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageoffer.getofferlink', 1)) {
                    if (!empty($show_offer_claim) && empty($claim_value)) {

                        if (!empty($viewer_id)) {
                            $content_claim .= '<span><img src="' . $view->layout()->staticBaseUrl . 'application/modules/Sitepageoffer/externals/images/invite.png" alt="" class="get_offer_icon" />';
                            $content_claim .= $view->htmlLink(array('route' => 'sitepageoffer_general', 'action' => 'getoffer', 'id' => $result->offer_id), Zend_Registry::get('Zend_Translate')->_('Get Offer'), array(
                                        'onclick' => 'owner(this);return false',
                                    )) . '</span>';
                        } else {
                            $offer_tabinformation = $view->url(array('action' => 'getoffer', 'id' => $result->offer_id, 'param' => $param, 'request_url' => $request_url['1']), 'sitepageoffer_general') . "?" . "return_url=" . $return_url . $_SERVER['HTTP_HOST'] . $currentUrl;
                            $title = $view->translate('Get Offer');
                            $content_claim .= '<span><img src="' . $view->layout()->staticBaseUrl . 'application/modules/Sitepageoffer/externals/images/invite.png" alt="" class="get_offer_icon" />' . "<a href=$offer_tabinformation>$title</a>" . '</span>';
                        }
                    } elseif (!empty($claim_value) && !empty($show_offer_claim)) {
                        $content_claim .= '<span><img src="' . $view->layout()->staticBaseUrl . 'application/modules/Sitepageoffer/externals/images/invite.png" alt="" class="get_offer_icon" />';
                        $content_claim .= $view->htmlLink(array('route' => 'sitepageoffer_general', 'action' => 'resendoffer', 'id' => $result->offer_id), Zend_Registry::get('Zend_Translate')->_('Resend Offer'), array(
                                    'onclick' => 'owner(this);return false',
                                )) . '</span>';
                    } else {
                        $content_claim .= '<span><b>' . Zend_Registry::get('Zend_Translate')->_('Expired') . '</b></span>';
                    }
                    $content_claim .= '<span><b>&middot;</b></span><span>' . $result->claimed . ' ' . Zend_Registry::get('Zend_Translate')->_('claimed') . '</span>';

                    if ($result->claim_count != -1) {
                        $content_claim .= '<span><b>&middot;</b></span><span>' . $result->claim_count . ' ' . Zend_Registry::get('Zend_Translate')->_('claims left.') . '</span>';
                    }
                }
                if (empty($result->photo_id)) {
                    if ($result)
                        $offer_image = "<a href=$offer_tabinformation><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitepageoffer/externals/images/nophoto_offer_thumb_icon.png' alt='' /></a>";
                    $str = "<div class='sitepage_offer_block'><div class='sitepage_offer_photo'>$offer_image</div><div class='sitepage_offer_details'><div class='sitepage_offer_title'>" . $offer_title . "</div>" . '<div class="sitepage_offer_stats">' . $content . '</div><div class="sitepage_offer_date seaocore_txt_light">' . $content_claim . "</div></div></div>";
                }
                else {
                    if ($result) {
                        $offer_image_path = $view->itemPhoto($result, 'thumb.icon');
                        $offer_image = "<a href=$offer_tabinformation>$offer_image_path</a>";
                        $str = "<div class='sitepage_offer_block'><div class='sitepage_offer_photo'>$offer_image</div><div class='sitepage_offer_details'><div class='sitepage_offer_title'>" . $offer_title . "</div>" . '<div class="sitepage_offer_stats">' . $content . '</div><div class="sitepage_offer_date seaocore_txt_light">' . $content_claim . "</div></div></div>";
                    }
                }
            }
        }

        return $str;
    }

    public function getUser() {
        if (empty($this->user_id)) {
            return null;
        }
        if (null === $this->_user) {
            $this->_user = Engine_Api::_()->getItem('user', $this->user_id);
        }
        return $this->_user;
    }

    public function getGateway() {
        if (empty($this->gateway_id)) {
            return null;
        }
        if (null === $this->_gateway) {
            $this->_gateway = Engine_Api::_()->getItem('payment_gateway', $this->gateway_id);
        }
        return $this->_gateway;
    }

    public function getPackage() {
        if (empty($this->package_id)) {
            return null;
        }
        if (null === $this->_package) {
            $this->_package = Engine_Api::_()->getItem('sitepage_package', $this->package_id);
        }
        return $this->_package;
    }

    public function cancel($is_upgrade_package = false) {
        // Try to cancel recurring payments in the gateway
        if (!empty($this->gateway_id) && !empty($this->gateway_profile_id)) {
            try {
                $gateway = Engine_Api::_()->getItem('sitepage_gateway', $this->gateway_id);
                $gatewayPlugin = $gateway->getPlugin();
                if (method_exists($gatewayPlugin, 'cancelPage')) {
                    $gatewayPlugin->cancelPage($this->gateway_profile_id);
                }
            } catch (Exception $e) {
                // Silence?
            }
        }
        // Cancel this row
        if ($is_upgrade_package) {
            $this->approved = false; // Need to do this to prevent clearing the user's session
        }
        $this->onCancel();
        return $this;
    }

    // Active
    public function setActive($flag = true, $deactivateOthers = null) {
        $this->approved = true;
        $this->pending = 0;
        if (empty($this->aprrove_date)) {
            $this->aprrove_date = date('Y-m-d H:i:s');
            if (!empty($this->draft) && empty($this->pending)) {
                Engine_Api::_()->sitepage()->attachPageActivity($this);
            }
        }

        $this->save();
        return $this;
    }

    // Events

    public function clearStatusChanged() {
        $this->_statusChanged = null;
        return $this;
    }

    public function didStatusChange() {
        return (bool) $this->_statusChanged;
    }

    public function onPaymentSuccess() {
        $this->_statusChanged = false;
        if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {

            if (in_array($this->status, array('initial', 'pending', 'overdue'))) {
                $this->setActive(true);
            }

            // Update expiration to expiration + recurrence or to now + recurrence?
            $package = $this->getPackage();
            $expiration = $package->getExpirationDate();
            $diff_days = 0;
            if ($package->isOneTime() && !empty($this->expiration_date) && $this->expiration_date !== '0000-00-00 00:00:00') {
                $diff_days = round((strtotime($this->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
            }
            if ($expiration) {
                $date = date('Y-m-d H:i:s', $expiration);

                if ($diff_days >= 1) {

                    $diff_days_expiry = round((strtotime($date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                    $incrmnt_date = date('d', time()) + $diff_days_expiry + $diff_days;
                    $incrmnt_date = date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m"), $incrmnt_date));
                } else {
                    $incrmnt_date = $date;
                }
                $this->expiration_date = $incrmnt_date;
            } else {
                $this->expiration_date = '2250-01-01 00:00:00';
            }

            // Change status
            if ($this->status != 'active') {
                $this->status = 'active';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    public function onPaymentPending() {
        $this->_statusChanged = false;
        if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
            // Change status
            if ($this->status != 'pending') {
                $this->status = 'pending';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    public function onPaymentFailure() {
        $this->_statusChanged = false;
        if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
            // Change status
            if ($this->status != 'overdue') {
                $this->status = 'overdue';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    public function onCancel() {
        $this->_statusChanged = false;
        if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'cancelled'))) {
            // Change status
            if ($this->status != 'cancelled') {
                $this->status = 'cancelled';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    public function onExpiration() {
        $this->_statusChanged = false;
        if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'expired'))) {
            // Change status
            if ($this->status != 'expired') {
                $this->status = 'expired';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    public function onRefund() {
        $this->_statusChanged = false;
        if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'refunded'))) {
            // Change status
            if ($this->status != 'refunded') {
                $this->status = 'refunded';
                $this->_statusChanged = true;
            }
        }
        $this->save();
        return $this;
    }

    /**
     * Process ipn of page transaction
     *
     * @param Payment_Model_Order $order
     * @param Engine_Payment_Ipn $ipn
     */
    public function onPaymentIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {
        $gateway = Engine_Api::_()->getItem('sitepage_gateway', $order->gateway_id);
        $gateway->getPlugin()->onPageTransactionIpn($order, $ipn);
        return true;
    }

    // Interfaces

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    /**
     * Gets a proxy object for the tags handler
     *
     * @return Engine_ProxyObject
     * */
    public function tags() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
    }

    protected function _insert() {
        if (null === $this->search) {
            $this->search = 1;
        }
        parent::_insert();
    }

    /**
     * Gets a proxy object for the follow handler
     *
     * @return Engine_ProxyObject
     * */
    public function follows() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('follows', 'seaocore'));
    }

    /**
     * Gets a page admin accroding to user id.
     *
     * @return page admin
     * */
    public function isPageAdmin($user_id) {
        return Engine_Api::_()->getDbTable('manageadmins', 'sitepage')->isPageAdmins($user_id, $this->getIdentity());
    }

    public function getPageOwnerList() {

        $owner_id = $this->owner_id;
        $table = Engine_Api::_()->getItemTable('sitepage_list');
        $select = $table->select()
                ->where('owner_id = ?', $owner_id)
                ->where('page_id = ?', $this->getIdentity())
                ->where('title = ?', 'SITEPAGE_LIKE')
                ->limit(1);

        $list = $table->fetchRow($select);

        if (null === $list) {
            $list = $table->createRow();
            $list->setFromArray(array(
                'owner_id' => $owner_id,
                'title' => 'SITEPAGE_LIKE',
                'page_id' => $this->getIdentity(),
            ));
            $list->save();
        }
        return $list;
    }

    /**
     * Delete the listing and belongings
     *
     */
    public function _delete() {
        //DELETE LISTING
        parent::_delete();
    }

    public function setImportPhoto($photo, $flag) {
        if (is_string($photo)) {
            $file_exists = 1;
        } else {
            $file_exists = 0;
        }
        if (is_string($photo) && !empty($file_exists)) {
            if (fopen($photo, "r")) {
                $file = $photo;
            } else {
                return;
            }
        } else {
            throw new Engine_Exception('invalid argument passed to setPhoto');
        }
        $name = basename($file);

        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'sitepage_page',
            'parent_id' => $this->getIdentity()
        );

        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(1500, 1500)
                ->write($path . '/m_' . $name)
                ->resize(300, 500)
                ->write($path . '/p_' . $name)
                ->resize(140, 160)
                ->write($path . '/in_' . $name)
                ->resize(200, 200)
                ->write($path . '/im_' . $name);


        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $name)
                ->destroy();


        //STORE
        $storage = Engine_Api::_()->storage();
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iIconNormalMidum = $storage->create($path . '/im_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormalMidum, 'thumb.midum');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        //REMOVE TEMP FILES
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);

        //ADD TO ALBUM
        $viewer = Engine_Api::_()->user()->getViewer();

        $photoTable = Engine_Api::_()->getItemTable('sitepage_photo');
        $rows = $photoTable->fetchRow($photoTable->select()->from($photoTable->info('name'), 'order')->order('order DESC')->limit(1));
        $order = 0;
        if (!empty($rows)) {
            $order = $rows->order + 1;
        }
        $sitePageAlbum = $this->getSingletonAlbum();
        $photoItem = $photoTable->createRow();


        $tempArray = array(
            'page_id' => $this->getIdentity(),
            'album_id' => $sitePageAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $sitePageAlbum->getIdentity(),
            'order' => $order
        );

        if (empty($flag))
            $tempArray['photo_id'] = $iMain->getIdentity();

        $photoItem->setFromArray($tempArray);
        $photoItem->save();

        //UPDATE ROW
        if (!empty($flag)) {
            $this->modified_date = date('Y-m-d H:i:s');
            $this->photo_id = $photoItem->file_id;
            $this->save();
        }
        return $this;
    }

}
