<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Project.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_Project extends Core_Model_Item_Abstract {

    protected $_owner_type = 'user';
    protected $_package;
    protected $_statusChanged;

    //  /**
    //  * Return rich content for feed items
    //  * */
    public function getRichContent() {
        $view = Zend_Registry::get('Zend_View');
        $view = clone $view;
        $view->clearVars();
        $view->addScriptPath('application/modules/Sitecrowdfunding/views/scripts/');
        $view->project = $this;
        $RESOURCE_TYPE = 'sitecrowdfunding_project';
        $photoURL = $this->getPhotoUrl();
        $view->photoURL = !empty($photoURL) ? $photoURL : $view->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png';

        return $view->render('activity-feed/_project.tpl');
    }

    // General
    public function getMediaType() {

        return 'project';
    }

    public function getLeaderList() {
        $table = Engine_Api::_()->getItemTable('sitecrowdfunding_list');
        $select = $table->select()
            ->where('owner_id = ?', $this->getIdentity())
            ->where('title = ?', 'SITECROWDFUNDING_LEADERS')
            ->limit(1);
        $list = $table->fetchRow($select);
        if (null === $list) {
            $list = $table->createRow();
            $list->setFromArray(array(
                'owner_id' => $this->getIdentity(),
                'title' => 'SITECROWDFUNDING_LEADERS',
            ));
            $list->save();
        }
        return $list;
    }

    /**
     * Set project photo
     *
     * */
    public function setPhoto($photo, $param = array()) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new Zend_Exception("invalid argument passed to setPhoto");
        }
        if (!$fileName) {
            $fileName = basename($file);
        }

        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => 'sitecrowdfunding_project',
            'parent_id' => $this->getIdentity(),
            'user_id' => $this->owner_id,
            'name' => $fileName,
        );

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        //Fetching the width and height of thumbmail
        $mainHeight = 680;
        $mainWidth = 1400;
        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize($mainWidth, $mainHeight)
            ->write($mainPath)
            ->destroy();

        // Resize image (profile)
        $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_l.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(300, 500)
            ->write($profilePath)
            ->destroy();

        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(140, 160)
            ->write($normalPath)
            ->destroy();

        //RESIZE IMAGE (Midum)
        $mediumPath = $path . DIRECTORY_SEPARATOR . $base . '_im.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(200, 200)
            ->write($mediumPath)
            ->destroy();

        // Resize image (icon)
        $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
            ->write($squarePath)
            ->destroy();

        // Store
        $iMain = $filesTable->createFile($mainPath, $params);
        $iCover = $filesTable->createFile($mainPath, $params);
        $iProfile = $filesTable->createFile($profilePath, $params);
        $iIconNormal = $filesTable->createFile($normalPath, $params);
        $iSquare = $filesTable->createFile($squarePath, $params);
        $iMedium = $filesTable->createFile($mediumPath, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iCover, 'thumb.cover');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iMedium, 'thumb.midum');
        $iMain->bridge($iSquare, 'thumb.icon');
        $iMain->bridge($iMain, 'thumb.main');

        // Remove temp files
        @unlink($mainPath);
        @unlink($mediumPath);
        @unlink($profilePath);
        @unlink($normalPath);
        @unlink($squarePath);

        //ADD TO ALBUM
        $viewer = Engine_Api::_()->user()->getViewer();

        $photoTable = Engine_Api::_()->getItemTable('sitecrowdfunding_photo');
        $rows = $photoTable->fetchRow($photoTable->select()->from($photoTable->info('name'), 'order')->order('order DESC')->limit(1));
        $order = 0;
        if (!empty($rows)) {
            $order = $rows->order + 1;
        }
        $sitephotoAlbum = $this->getSingletonAlbum();
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'project_id' => $this->getIdentity(),
            'album_id' => $sitephotoAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $sitephotoAlbum->getIdentity(),
            'order' => $order,
        ));
        $iden = $photoItem->save();
        if (!isset($param['setProjectMainPhoto'])) {
            // Update row
            $this->modified_date = date('Y-m-d H:i:s');
            $this->photo_id = $iMain->getIdentity();

            if (Engine_Api::_()->hasModuleBootstrap('sitecontentcoverphoto') && Engine_Api::_()->getDbtable('modules', 'sitecontentcoverphoto')->checkEnableModule(array('resource_type' => "sitecrowdfunding_project")) && isset($this->project_cover)) {
                $this->project_cover = $photoItem->getIdentity();
            }
            $this->save();
        }
        if (isset($param['return']) && $param['return'] == 'photo') {
            return $photoItem;
        }
        return $this;
    }

    public function updateAllCoverPhotos() {
        $photo = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id);
        if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
            $name = basename($file);
            $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
            $params = array(
                'parent_type' => 'sitecrowdfunding_project',
                'parent_id' => $this->getIdentity(),
            );

            //STORE
            $storage = Engine_Api::_()->storage();
            $iMain = $photo;

            $thunmProfile = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.profile');

            if (empty($thunmProfile) || empty($thunmProfile->parent_file_id)) {
                //RESIZE IMAGE (PROFILE)
                $image = Engine_Image::factory();
                $image->open($file)
                    ->resize(300, 500)
                    ->write($path . '/p_' . $name)
                    ->destroy();
                $iProfile = $storage->create($path . '/p_' . $name, $params);
                $iMain->bridge($iProfile, 'thumb.profile');
                @unlink($path . '/p_' . $name);
            }

            $thunmMidum = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.midum');
            if (empty($thunmMidum) || empty($thunmMidum->parent_file_id)) {
                //RESIZE IMAGE (Midum)
                $image = Engine_Image::factory();
                $image->open($file)
                    ->resize(200, 200)
                    ->write($path . '/im_' . $name)
                    ->destroy();
                $iIconNormalMidum = $storage->create($path . '/im_' . $name, $params);
                $iMain->bridge($iIconNormalMidum, 'thumb.midum');
                //REMOVE TEMP FILES

                @unlink($path . '/m_' . $name);
            }
        }
    }

    /**
     * Set project location
     *
     */
    public function setLocation() {

        $id = $this->project_id;

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
            $project = $this;
            if (!empty($project)) {
                $location = $project->location;
            }

            if (!empty($location)) {
                $locationTable = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding');
                $locationRow = $locationTable->getLocation(array('id' => $id));
                if (isset($_POST['locationParams']) && $_POST['locationParams']) {
                    if (is_string($_POST['locationParams'])) {
                        $_POST['locationParams'] = Zend_Json_Decoder::decode($_POST['locationParams']);
                    }

                    if ($_POST['locationParams']['location'] === $location) {
                        try {
                            $loctionV = $_POST['locationParams'];
                            $loctionV['project_id'] = $id;
                            $loctionV['zoom'] = 16;
                            if (empty($locationRow)) {
                                $locationRow = $locationTable->createRow();
                            }

                            $locationRow->setFromArray($loctionV);
                            $locationRow->save();
                        } catch (Exception $e) {
                            throw $e;
                        }
                        return;
                    }
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

                        //ITERATE THROUGH THE ROWS, GEOCODING EACH ADDRESS
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
                            $doGetContents = ob_get_contents();
                            $json_resopnse = !empty($doGetContents) ? Zend_Json::decode($doGetContents) : array();
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
                                        if ($address == '') {
                                            $address = $result[0]['address_components'][$i]['long_name'];
                                        } else {
                                            $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
                                        }

                                    } else if ($types_location == 'locality') {
                                        if ($address == '') {
                                            $address = $result[0]['address_components'][$i]['long_name'];
                                        } else {
                                            $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
                                        }

                                    } else if ($types_location == 'route') {
                                        if ($address == '') {
                                            $address = $result[0]['address_components'][$i]['long_name'];
                                        } else {
                                            $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
                                        }

                                    } else if ($types_location == 'sublocality') {
                                        if ($address == '') {
                                            $address = $result[0]['address_components'][$i]['long_name'];
                                        } else {
                                            $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
                                        }

                                    }
                                }

                                try {
                                    $loctionV = array();
                                    $loctionV['project_id'] = $id;
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
                                    if (empty($locationRow)) {
                                        $locationRow = $locationTable->createRow();
                                    }

                                    $locationRow->setFromArray($loctionV);
                                    $locationRow->save();
                                    if (!empty($enableSocialengineaddon)) {
                                        $location = Engine_Api::_()->getDbtable('locations', 'seaocore')->setLocation($loctionV);
                                    }
                                } catch (Exception $e) {
                                    throw $e;
                                }
                            } else if (strcmp($status, "620") == 0) {
                                //SENT GEOCODE TO FAST
                                $delay += 100000;
                            } else {
                                // FAILURE TO GEOCODE
                                $geocode_pending = false;
                                echo "Address " . $locationLocal . " failed to geocoded. ";
                                echo "Received status " . $status . "\n";
                            }
                            usleep($delay);
                        }
                    } else {

                        try {
                            //CREATE PROJECT LOCATION
                            $loctionV = array();
                            if (empty($locationRow)) {
                                $locationRow = $locationTable->createRow();
                            }

                            $value = $getSEALocation->toarray();
                            unset($value['location_id']);
                            $value['project_id'] = $id;
                            $locationRow->setFromArray($value);
                            $locationRow->save();
                        } catch (Exception $e) {
                            throw $e;
                        }
                    }
                } else {

                    try {
                        //CREATE PROJECT LOCATION
                        $loctionV = array();
                        if (empty($locationRow)) {
                            $locationRow = $locationTable->createRow();
                        }

                        $value = $locationValue->toarray();
                        unset($value['location_id']);
                        $value['project_id'] = $id;
                        $locationRow->setFromArray($value);
                        $locationRow->save();
                    } catch (Exception $e) {
                        throw $e;
                    }
                }
            }
        }
    }

    /**
     * Gets an absolute URL to the store to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {

        $slug = $this->getSlug();
        $params = array_merge(array(
            'route' => 'sitecrowdfunding_entry_view',
            'reset' => true,
            'project_id' => $this->project_id,
            'slug' => $slug,
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);

        return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
    }

    /**
     * Return slug
     * */
    public function getSlug($str = null, $maxstrlen = 64) {

        if (null === $str) {
            $str = $this->title;
        }

        $maxstrlen = 225;

        return Engine_Api::_()->seaocore()->getSlug($str, $maxstrlen);
    }

    /**
     * Gets a url to the current project representing this item. Return null if none
     * set
     *
     * @param string The project type (null -> main, thumb, icon, etc);
     * @return string The project url
     */
    public function getPhotoUrl($type = null) {
        $photo_id = $this->photo_id;
        if (!$photo_id) {
            return null;
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id, $type);
        if (!$file) {
            return null;
        }

        return $file->map();
    }

    public function getPhoto($photo_id) {

        $photoTable = Engine_Api::_()->getItemTable('sitecrowdfunding_photo');
        $select = $photoTable->select()
            ->where('file_id = ?', $photo_id)
            ->limit(1);

        $photo = $photoTable->fetchRow($select);
        return $photo;
    }

    public function getSingletonAlbum() {

        $table = Engine_Api::_()->getItemTable('sitecrowdfunding_album');
        $select = $table->select()
            ->where('project_id = ?', $this->getIdentity())
            ->order('album_id ASC')
            ->limit(1);

        $album = $table->fetchRow($select);

        if (null == $album) {
            $album = $table->createRow();
            $album->setFromArray(array(
                'title' => $this->getTitle(),
                'project_id' => $this->getIdentity(),
            ));
            $album->save();
        }

        return $album;
    }

    /**
     * Gets a proxy object for the tags handler
     *
     * @return Engine_ProxyObject
     * */
    public function tags() {

        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
    }

    /**
     * Insert global searching value
     *
     * */
    protected function _insert() {

        if (is_null($this->search)) {
            $this->search = 1;
        }

        parent::_insert();
    }

    public function isExpired() {
        if ($this->funding_end_date === "2250-01-01 00:00:00") {
            return false;
        }
        elseif ($this->funding_end_date == null) {
            return false;
        }
        elseif (strtotime($this->funding_end_date) <= strtotime(date('Y-m-d'))) {
            return true;
        }
        return false;
    }

    public function getExpiryDate() {
        $expiryDate = date('Y-m-d', strtotime($this->funding_end_date));
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        return $view->localeDate($expiryDate);
    }

    public function getPackage() {
        if (empty($this->package_id)) {
            return null;
        }
        if (null === $this->_package) {
            $this->_package = Engine_Api::_()->getItem('sitecrowdfunding_package', $this->package_id);
        }
        return $this->_package;
    }

    public function canView($user = null) {
        if (!($user instanceof User_Model_User)) {
            $user = Engine_Api::_()->user()->getViewer();
        }

        return Engine_Api::_()->authorization()->isAllowed($this, $user, 'view') && $this->isViewableByNetwork();
    }

    public function isViewableByNetwork() {
        $regName = 'view_privacy_' . $this->getGuid();
        if (!Zend_Registry::isRegistered($regName)) {
            $flage = true;
            $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.network', 0);
            $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.networkprofile.privacy', 0);
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($enableNetwork && $viewPricavyEnable && !$this->isOwner($viewer)) {
                $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);
                if (Engine_Api::_()->sitecrowdfunding()->listBaseNetworkEnable()) {
                    if ($this->networks_privacy) {
                        if (!empty($viewerNetworkIds)) {
                            $channelNetworkId = explode(",", $this->networks_privacy);
                            $commanIds = array_intersect($channelNetworkId, $viewerNetworkIds);
                            if (empty($commanIds)) {
                                $flage = false;
                            }

                        } else {
                            $flage = false;
                        }
                    }
                } else {
                    if (!empty($viewerNetworkIds)) {
                        $ownerNetworkIds = $networkMembershipTable->getMembershipsOfIds($this->getOwner('user'));
                        if ($ownerNetworkIds) {
                            $commanIds = array_intersect($ownerNetworkIds, $viewerNetworkIds);
                            if (empty($commanIds)) {
                                $flage = false;
                            }

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

    /**
     * Get state of project
     * $params object $project
     * @return string
     * */
    public function getProjectState() {
        $translate = Zend_Registry::get('Zend_Translate');
        if ($this->state == 'draft') {
            return $translate->translate("Draft");
        }else if($this->state === 'rejected'){
            return $translate->translate("Rejected");
        }else if($this->state === 'submitted'){
            return $translate->translate("Under Review");
        }else if ($this->state == 'published' && $this->approved == 1) {
            return $translate->translate("Published");
        }
        return $translate->translate("Draft");
    }

    public function getProjectFundingState() {
        $currentDate = date('Y-m-d H:i:s');
        $translate = Zend_Registry::get('Zend_Translate');
        if ($this->funding_state == 'draft') {
            return $translate->translate("Draft");
        }else if($this->funding_state === 'submitted'){
            return $translate->translate("Under Review");
        }else if($this->funding_state === 'rejected'){
            return $translate->translate("Rejected");
        }else if ($this->funding_state == 'successful') {
            return $translate->translate('Funding Successful');
        }else if ($this->funding_state == 'failed') {
            return $translate->translate('Funding Failed');
        }else if ($this->is_fund_raisable && $this->funding_state == 'published' && $this->funding_approved == 1 && !$this->is_gateway_configured) {
            return  $translate->translate('Configure Payment Methods');
        }else if ($this->is_fund_raisable && $this->funding_state == 'published' && $this->funding_approved == 1 && $this->is_gateway_configured) {
            if($currentDate < $this->funding_start_date){
                $days = Engine_Api::_()->sitecrowdfunding()->findDays2($this->funding_start_date);
                if ($days == 1) {
                    return "$days" . $translate->translate(" Day to Live");
                } else if ($days > 1) {
                    return "$days" . $translate->translate(" Days to Live");
                } else {
                    return $translate->translate("Live");
                }
            }else{
                return $translate->translate("Live");
            }
        }
        return $translate->translate("Draft");
    }

    public function clearStatusChanged() {
        $this->_statusChanged = null;
        return $this;
    }

    public function didStatusChange() {
        return (bool) $this->_statusChanged;
    }

    public function cancel($is_upgrade_package = false) {
        $otherInfoTable = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        $gateway_profile_id = $otherInfoTable->getColumnValue($this->project_id, 'gateway_profile_id');
        $gateway_id = $otherInfoTable->getColumnValue($this->project_id, 'gateway_id');
        // Try to cancel recurring payments in the gateway
        if (!empty($gateway_id) && !empty($gateway_profile_id)) {
            try {
                $gateway = Engine_Api::_()->getItem('sitecrowdfunding_gateway', $gateway_id);
                $gatewayPlugin = $gateway->getPlugin();
                if (method_exists($gatewayPlugin, 'cancelProject')) {
                    $gatewayPlugin->cancelProject($gateway_profile_id);
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

    public function onPaymentSuccess() {
        $this->_statusChanged = false;
        if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {

            if (in_array($this->status, array('initial', 'pending', 'overdue'))) {
                //ON COMPLETION OF PAYMENT - ADD ACTIVITY FEED & SEND NOTIFICATION TO HOST.
                $this->setActive(true);
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

    // Active
    public function setActive($flag = true, $deactivateOthers = null) {

        $package = $this->getPackage();
        if (!($package && $package->isAutoApprove())) {
            return false;
        }
        $this->approved = true;
        $this->pending = 0;
        $viewer = Engine_Api::_()->user()->getViewer();
        if (empty($this->approved_date)) {
            $this->approved_date = date('Y-m-d H:i:s');
            $currentDate = date('Y-m-d H:i:s');
            if ($this->state != 'draft' && $this->search && $this->is_gateway_configured && $this->start_date <= $currentDate) {
                //ATTACH ACTIVITY FEED
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $this, 'sitecrowdfunding_project_new');
                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $this);
                }
                $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
                if (!empty($enable_Facebooksefeed)) {
                    $sitecrowdfunding_array = array();
                    $sitecrowdfunding_array['type'] = 'sitecrowdfunding_project_new';
                    $sitecrowdfunding_array['object'] = $this;
                    Engine_Api::_()->facebooksefeed()->sendFacebookFeed($sitecrowdfunding_array);
                }
            }
        }
        $this->save();
        return $this;
    }

    public function payoutButton() {

        if (!in_array($this->state, array('successful', 'failed'))) {
            return false;
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        if (!($settings->getSetting('sitecrowdfunding.payment.method', 'normal') == 'escrow')) {
            return false;
        }
        $otherInfoTable = Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding');
        $otherInfoTableName = $otherInfoTable->info('name');
        $select = $otherInfoTable->select()->from($otherInfoTableName, 'project_gateway');
        $gateways = $select->where('project_id = ?', $this->project_id)
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (!$gateways) {
            return false;
        }

        $gatewayArr = Zend_Json_Decoder::decode($gateways);
        $gatewayArr = array_flip($gatewayArr);
        if (!(in_array('mangopay', $gatewayArr))) {
            return false;
        }
        $projectGatewayObj = null;
        if (in_array('mangopay', $gatewayArr)) {
            $projectGatewayObj = Engine_Api::_()->getDbtable('projectGateways', 'sitecrowdfunding')->fetchRow(array('project_id = ?' => $this->project_id, 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));
        }

        return $this->state;
    }

    public function isProjectSucceeded() {
        $timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
        date_default_timezone_set($timezone);
        $projectExpr = strtotime(date('Y-m-d', strtotime($this->funding_end_date)));
        if (strtotime(date('Y-m-d')) <= $projectExpr) {
            return false;
        }
        $amount = $this->getFundedAmount();
        if ($amount >= $this->goal_amount) {
            return true;
        } else {
            return false;
        }
    }

    /*public function getFundedAmount($byViewer = false) {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $backerTableName = $backerTable->info('name');
        $select = $backerTable->select()->from($backerTableName, 'sum(amount)');
        $select->where('project_id = ?', $this->project_id);
        if ($byViewer) {
            $select->where('user_id = ?', $viewer_id);
        }
        $amount = $select->where('payment_status = "active" OR payment_status = "authorised"')
            ->limit(1)
            ->query()
            ->fetchColumn();

        return (!empty($amount)) ? round($amount, 0) : 0;
    }*/

    public function getFundedAmount($byViewer = false) {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $backerTableName = $backerTable->info('name');
        $select = $backerTable->select()->from($backerTableName, 'sum(amount)');
        $select->where('project_id = ?', $this->project_id);
        if ($byViewer) {
            $select->where('user_id = ?', $viewer_id);
        }
        $amount = $select->where('payment_status = "active" OR payment_status = "authorised"')
            ->limit(1)
            ->query()
            ->fetchColumn();


        $internalAmount =  (!empty($amount)) ? round($amount, 0) : 0;

        $externalFundingAmount = Engine_Api::_()->getDbTable('externalfundings','sitecrowdfunding')->getFundingAmountByUserId($this->project_id, $byViewer ? $viewer_id : 0);
        $externalFundingAmount =  (!empty($externalFundingAmount)) ? round($externalFundingAmount, 0) : 0;

        if(!$byViewer){
            $externalORGFundingAmount = Engine_Api::_()->getDbTable('externalfundings','sitecrowdfunding')->getFundingAmountORG($this->project_id);
            $externalORGFundingAmount =  (!empty($externalORGFundingAmount)) ? round($externalORGFundingAmount, 0) : 0;

            $externalORGFundingAmount = $externalORGFundingAmount + $this->invested_amount;
        }

        return $internalAmount + $externalFundingAmount + $externalORGFundingAmount;
    }

    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    public function payout() {
        $otherInfoTable = Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding');
        $otherInfoTableName = $otherInfoTable->info('name');
        $select = $otherInfoTable->select()->from($otherInfoTableName, 'project_gateway');
        $gateways = $select->where('project_id = ?', $this->project_id)
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (!$gateways) {
            return false;
        }

        $gatewayArr = $gateways = Zend_Json_Decoder::decode($gateways);
        $gatewayArr = array_flip($gatewayArr);
        if (!(in_array('mangopay', $gatewayArr))) {
            return false;
        }
        $mangoMessage = $paypalMessage = $message = "";
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $params = array('resource_type' => 'sitecrowdfunding_backer', 'project_id' => $this->project_id);
        if (in_array('mangopay', $gatewayArr)) {
            $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
            $gatewayPlugin = $adminGateway->getPlugin();
            if (method_exists($gatewayPlugin, 'payoutAllTransaction')) {
                $mangoMessage = $gatewayPlugin->payoutAllTransaction($params);
            }
        }

        if (empty($mangoMessage) && empty($paypalMessage)) {
            $message = 'Payout Succesfully.';
        } else {
            $message = $mangoMessage . "<br />" . $paypalMessage;
        }
        return $message;
    }

    public function refund() {
        $otherInfoTable = Engine_Api::_()->getDbtable('otherinfo', 'sitecrowdfunding');
        $otherInfoTableName = $otherInfoTable->info('name');
        $select = $otherInfoTable->select()->from($otherInfoTableName, 'project_gateway');
        $gateways = $select->where('project_id = ?', $this->project_id)
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (!$gateways) {
            return false;
        }

        $gatewayArr = $gateways = Zend_Json_Decoder::decode($gateways);
        $gatewayArr = array_flip($gatewayArr);

        if (!(in_array('mangopay', $gatewayArr))) {
            return false;
        }

        $mangoMessage = $paypalMessage = $message = "";
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $params = array('resource_type' => 'sitecrowdfunding_backer', 'project_id' => $this->project_id);
        if (in_array('mangopay', $gatewayArr)) {
            $adminGateway = $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
            $gatewayPlugin = $adminGateway->getPlugin();
            $mangoMessage = $gatewayPlugin->refundAllTransaction($params);
        }
        if (empty($mangoMessage) && empty($paypalMessage)) {
            $message = 'Refund Succesfully.';
        } else {
            $message = $mangoMessage . "<br />" . $paypalMessage;
        }
        return $message;
    }

    public function getFundedRatio() {
        $fundedAmount = $this->getFundedAmount();
        if ($fundedAmount == 0) {
            return 0;
        }

        $totalAmount = $this->goal_amount;
        $ratio = $fundedAmount / $totalAmount * 100;
        $fundedRatio = round($ratio, 2);
        return $fundedRatio;
    }

    public function getTotalCommission() {
        $table = Engine_Api::_()->getDbTable('backers', 'sitecrowdfunding');
        $tableName = $table->info('name');
        $total_commission = $table->select()
            ->from($tableName, array("sum(commission_value)"))
            ->where("$tableName.payment_status = 'active'")
            ->where("$tableName.payout_status = 'success'")
            ->where("project_id = ? ", $this->project_id)->query()->fetchColumn();
        if ($total_commission) {
            return $total_commission;
        }

        return 0;
    }

    public function getRemainingDays($number_format = 0, $text_in_one_line = false) {

        if(empty($this->funding_start_date) || empty($this->funding_end_date) ){
            return 0;
        }

        $days = Engine_Api::_()->sitecrowdfunding()->findDays($this->funding_end_date);
        $daysToStart = Engine_Api::_()->sitecrowdfunding()->findDays($this->funding_start_date);
        $currentDate = date('Y-m-d H:i:s');
        $translate = Zend_Registry::get('Zend_Translate');

        if ($this->lifetime && $days > 90) {
            return $translate->translate('Life Time');
        }
        //  SOME WHERE WE NEED TEXT WITHOUT <BR> SO WE PASS $text_in_one_line
        if ($currentDate < $this->funding_start_date) {
            if ($daysToStart >= 1 && $number_format) {
                return $days;
            } else if ($daysToStart == 1) {
                return $text_in_one_line ? "<b>$daysToStart</b>" . $translate->translate(" Day to Live") : "<b>$daysToStart </b></br> " . $translate->translate("Day to Live");
            } else if ($daysToStart > 1) {
                return $text_in_one_line ? "<b>$daysToStart" . $translate->translate(" Days to Live") : "<b>$daysToStart </b></br> " . $translate->translate("Days to Live");
            } else {
                return $translate->translate("Live");
            }
        } else {
            if ($days >= 1 && $number_format) {
                return $days;
            } else if ($days == 1) {
                return $text_in_one_line ? "<b>$days</b>" . $translate->translate(" Day to go") : "<b>$days </b></br> " . $translate->translate("Day to go");
            } else if ($days > 1) {
                return $text_in_one_line ? "<b>$days</b>" . $translate->translate(" Days to go") : "<b>$days </b></br> " . $translate->translate("Days to go");
            }
        }
        $projectState = $this->getProjectFundingStatus();
        return $translate->translate($projectState);
    }

    public function getProjectStatus() {

        if ($this->state == 'successful') {
            return 'Funding <br>Successful';
        } else if ($this->state == 'failed') {
            return 'Funding <br>Failed';
        }
        return '';
    }

    public function getProjectFundingStatus() {

        if ($this->funding_state == 'successful') {
            return 'Funding Successful';
        } else if ($this->funding_state == 'failed') {
            return 'Funding Failed';
        }
        return '';
    }

    public function getStartDate() {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $date = $view->localeDate($this->start_date);
        return $date;
    }

    //rows related one user backed a project one or multiple times
    public function getBackingDetailForLoginUser() {
        $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $backerTableName = $backersTable->info('name');
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $select = $backersTable->select()->from($backerTableName, '*');
        $select = $select->where('project_id = ?', $this->project_id);
        $select = $select->where('user_id = ?', $viewer_id);
        $select = $select->where('payment_status = "active" OR payment_status = "authorised"');
        return $backersTable->fetchAll($select);
    }

    //FUNCTION TO GET GATEWAY TYPE SET IN BACKER TABLE ENTRY
    public function getGatewayType() {
        $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $backerTableName = $backersTable->info('name');
        $select = $backersTable->select()->from($backerTableName, array('gateway_type'));
        $gatewayType = $select->where('project_id = ?', $this->project_id)
            ->where('payment_status = "active" OR payment_status = "authorised"')
            ->limit(1)
            ->query()
            ->fetchColumn();
        return $gatewayType;
    }

    public function getUniqueBackers() {
        $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $backerTableName = $backersTable->info('name');
        $select = $backersTable->select()->from($backerTableName, array('user_id'))
            ->where('project_id = ?', $this->project_id)
            ->where('payment_status = "active" OR payment_status = "authorised"');

        $backers = $backersTable->fetchAll($select);
        $result = array();
        foreach ($backers as $backer) {
            $result[] = $backer->user_id;
        }

        $result = array_unique($result);
        return $result;
    }

    protected function _delete() {

        //Delete all backers of this project
        $backersTable = Engine_Api::_()->getItemTable('sitecrowdfunding_backer');
        $backersTable->delete(array('project_id=?' => $this->project_id));

        //Delete all albums & their photos of this project
        $albumsTable = Engine_Api::_()->getItemTable('sitecrowdfunding_album');
        $albumsTable->delete(array('project_id=?' => $this->project_id));

        $photosTable = Engine_Api::_()->getItemTable('sitecrowdfunding_photo');
        $photosTable->delete(array('project_id=?' => $this->project_id));

        //Delete all video related to the project
        if (Engine_Api::_()->hasModuleBootstrap('sitevideo')) {
            $videotable = Engine_Api::_()->getDbtable('videos', 'sitevideo');
            $videotable->delete(array('parent_id = ?' => $this->project_id, 'parent_type = ?' => 'sitecrowdfunding_project'));
        } elseif (Engine_Api::_()->hasModuleBootstrap('video')) {
            $videotable = Engine_Api::_()->getDbtable('videos', 'video');
            $videotable->delete(array('parent_id = ?' => $this->project_id, 'parent_type = ?' => 'sitecrowdfunding_project'));
        }
        //Delete all announcement of this project
        $announcementTable = Engine_Api::_()->getItemTable('sitecrowdfunding_announcement');
        $announcementTable->delete(array('project_id=?' => $this->project_id));

        //Delete all the post & their topics
        $postTable = Engine_Api::_()->getItemTable('sitecrowdfunding_post');
        $postTable->delete(array('project_id=?' => $this->project_id));

        //Delete all the rewards of this project
        $rewardTable = Engine_Api::_()->getItemTable('sitecrowdfunding_reward');
        $rewardTable->delete(array('project_id=?' => $this->project_id));

        //Delete all the shipping location of this project
        $rewardShippingLocationTable = Engine_Api::_()->getItemTable('sitecrowdfunding_rewardshippinglocation');
        $rewardShippingLocationTable->delete(array('project_id=?' => $this->project_id));
        //Delete all the Project Gateway of this project
        $projectGatewayTable = Engine_Api::_()->getItemTable('sitecrowdfunding_projectGateway');
        $projectGatewayTable->delete(array('project_id=?' => $this->project_id));

        //Delete location of this project
        $locationTable = Engine_Api::_()->getDbTable('locations', 'sitecrowdfunding');
        $locationTable->delete(array('project_id=?' => $this->project_id));

        // Delete other info of this project
        $otherInfoTable = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        $otherInfoTable->delete(array('project_id=?' => $this->project_id));

        $listTable = Engine_Api::_()->getItemTable('sitecrowdfunding_list');
        $listChildTable = Engine_Api::_()->getItemTable('sitecrowdfunding_list_item');
        //Find all the leader for this project
        $leadersSelect = $listTable->select()->from($listTable->info('name'), '*')->where('owner_id = ?', $this->project_id);

        $leaders = $listTable->fetchAll($leadersSelect);
        //Delete each leader
        foreach ($leaders as $leader) {
            $listChildTable->delete(array('list_id=?' => $leader->list_id));
        }
        // Delete  Leaders of this project
        $listTable->delete(array('owner_id' => $this->project_id));

        Engine_Api::_()->getDbTable('locationitems', 'seaocore')->delete(array(
            'resource_id = ?' => $this->project_id,
            'resource_type = ?' => 'sitecrowdfunding_project',
        ));

        //Delete records from Crowdfunding search and value table
        Engine_Api::_()->fields()->getTable('sitecrowdfunding_project', 'search')->delete(array('item_id = ?' => $this->project_id));
        Engine_Api::_()->fields()->getTable('sitecrowdfunding_project', 'values')->delete(array('item_id = ?' => $this->project_id));

        // delete storage files (Project thumb)
        if (Engine_Api::_()->getItem('storage_file', $this->photo_id)) {
            Engine_Api::_()->getItem('storage_file', $this->photo_id)->remove();
        }

        // delete crowdfunding favourites
        Engine_Api::_()->getDbtable('favourites', 'seaocore')->delete(array(
            'resource_id = ?' => $this->project_id,
            'resource_type = ?' => 'sitecrowdfunding_project',
        ));

        Engine_Api::_()->getDbtable('transactions', 'sitecrowdfunding')->delete(array(
            'source_id = ?' => $this->project_id,
            'source_type = ?' => 'sitecrowdfunding_project',
        ));
        parent::_delete();
    }

    public function isOpen() {
        if (in_array($this->state, array('draft', 'published', 'submitted', 'rejected'))) {
            return true;
        }
        return false;
    }

    public function isActive(){
        if(($this->state == 'published' || $this->state == 'successful') && $this->approved){
            return true;
        }
        return false;
    }

    //USED IN THE PAGES, GROUPS, EVENT, REVIEW AND BUSINESS PLUGIN
    public function isbacked() {
        $table = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $tableName = $table->info('name');
        $backer_count = $table->select()
            ->from($tableName, array("count(*) as backer_count"))
            ->where("project_id = ? ", $this->project_id)
            ->where('payment_status = "active" OR payment_status = "authorised"
                OR payment_status = "failed" OR payment_status = "refunded"
                OR payment_status = "pending"')
            ->query()->fetchColumn();
        if ($backer_count == 0) {
            return false;
        }

        return true;
    }

    //FUNCTION TO CHECK THE CONDITIONS TO SHOW BACK PROJECT LINK
    public function showBackProjectLink() {
        $currentDate = date('Y-m-d');
        $projectStartDate = date('Y-m-d', strtotime($this->funding_start_date));
        if (strtotime($currentDate) >= strtotime($projectStartDate) && $this->is_gateway_configured && (!$this->isExpired()) && $this->funding_status == 'active') {
            return true;
        }
        return false;
    }

    public function isFundingApproved() {
        $state = array('published', 'successful','failed');
        if ($this->is_fund_raisable && in_array($this->funding_state, $state ) && $this->funding_status == 'active' && $this->funding_approved ) {
            return true;
        }
        return false;
    }

}
