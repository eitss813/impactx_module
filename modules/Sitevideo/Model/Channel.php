<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Channel.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_Channel extends Core_Model_Item_Abstract {

    protected $_parent_type = 'user';
    protected $_owner_type = 'user';
    protected $_parent_is_owner = true;
    protected $_type = 'sitevideo_channel';

    public function getSubscriptionModel() {
        if (empty($this->channel_id))
            return false;

        $owner_id = Engine_Api::_()->user()->getViewer()->user_id;
        $subscription = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo');
        $subscriptionModel = $subscription->fetchRow($subscription->select()
                        ->where('owner_id = ?', $owner_id)
                        ->where('channel_id = ?', $this->channel_id));
        if ($subscriptionModel)
            return $subscriptionModel;
        return false;
    }

    public function isSubscribed() {
        if (empty($this->channel_id))
            return false;

        $owner_id = Engine_Api::_()->user()->getViewer()->user_id;
        $subscription = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo');
        $subscriptionModel = $subscription->fetchRow($subscription->select()
                        ->where('owner_id = ?', $owner_id)
                        ->where('channel_id = ?', $this->channel_id));
        if ($subscriptionModel)
            return true;
        return false;
    }

    public function getType($inflect = false) {
        if ($inflect) {
            return str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_type)));
        }

        return $this->_type;
    }

    /**
     * Gets an absolute URL to the store to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {
        $slug = $this->getSlug();
        $params = array_merge(array(
            'route' => 'sitevideo_entry_view',
            'reset' => true,
            'channel_url' => $this->channel_url,
                ), $params);
        $channel_url = $this->channel_url;
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        $urlO = Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
        $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.manifestUrlS', "channel");
        $banneUrlArray = Engine_Api::_()->sitevideo()->getBannedUrls();

        // GET THE CHANNEL LIKES AFTER WHICH SHORTEN CHANNEL WILL BE WORK 
        $channel_likes = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.likelimit.forurlblock', "5");
        $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.change.url', 1);
        $replaceStr = str_replace("/" . $routeStartS . "/", "/", $urlO);

        if ((!empty($change_url)) && $this->like_count >= $channel_likes && !in_array($channel_url, $banneUrlArray)) {
            $urlO = $replaceStr;
        }
        return $urlO;
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
     * Gets a url to the current video representing this item. Return null if none
     * set
     *
     * @param string The video type (null -> main, thumb, icon, etc);
     * @return string The video url
     */
    public function getPhotoUrl($type = null) {
        $photo_id = $this->file_id;
        if (!$photo_id) {
            return null;
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id, $type);
        if (!$file) {
            return null;
        }

        return $file->map();
    }

    public function getFirstVideo() {
        $videoTable = Engine_Api::_()->getItemTable('sitevideo_video');
        $select = $videoTable->select()
                ->where('channel_id = ?', $this->channel_id)
                ->order('order ASC')
                ->limit(1);
        return $videoTable->fetchRow($select);
    }

    public function getLastVideo() {
        $videoTable = Engine_Api::_()->getItemTable('sitevideo_video');
        $select = $videoTable->select()
                ->where('channel_id = ?', $this->channel_id)
                ->order('order DESC')
                ->limit(1);
        return $videoTable->fetchRow($select);
    }

    public function count() {
        $videoTable = Engine_Api::_()->getItemTable('sitevideo_video');
        return $videoTable->select()
                        ->from($videoTable, new Zend_Db_Expr('COUNT(video_id)'))
                        ->where('channel_id = ?', $this->channel_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
    }

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

    public function isViewableByNetwork() {
        $regName = 'view_privacy_' . $this->getGuid();
        if (!Zend_Registry::isRegistered($regName)) {
            $flage = true;
            $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.network', 0);
            $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.networkprofile.privacy', 0);
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($enableNetwork && $viewPricavyEnable && !$this->isOwner($viewer)) {
                $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);
                if (Engine_Api::_()->sitevideo()->channelBaseNetworkEnable()) {
                    if ($this->networks_privacy) {
                        if (!empty($viewerNetworkIds)) {
                            $channelNetworkId = explode(",", $this->networks_privacy);
                            $commanIds = array_intersect($channelNetworkId, $viewerNetworkIds);
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

    public function getSingletonAlbum() {

        $table = Engine_Api::_()->getItemTable('sitevideo_album');
        $select = $table->select()
                ->where('channel_id = ?', $this->getIdentity())
                ->order('album_id ASC')
                ->limit(1);

        $album = $table->fetchRow($select);

        if (null == $album) {
            $album = $table->createRow();
            $album->setFromArray(array(
                'title' => $this->getTitle(),
                'channel_id' => $this->getIdentity()
            ));
            $album->save();
        }

        return $album;
    }

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
            'parent_type' => 'sitevideo_channel',
            'parent_id' => $this->getIdentity(),
            'user_id' => $this->owner_id,
            'name' => $fileName,
        );

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        //Fetching the width and height of thumbmail
        $normalHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.height', 375);
        $normalWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
        $largeHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.height', 720);
        $largeWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
        $mainHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $mainWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize($mainWidth, $mainHeight)
                ->write($mainPath)
                ->destroy();

        // Resize image (large)
        $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_l.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize($largeWidth, $largeHeight)
                ->write($profilePath)
                ->destroy();

        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize($normalWidth, $normalHeight)
                ->write($normalPath)
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
        $iProfile = $filesTable->createFile($profilePath, $params);
        $iIconNormal = $filesTable->createFile($normalPath, $params);
        $iSquare = $filesTable->createFile($squarePath, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');
        $iMain->bridge($iMain, 'thumb.main');

        // Remove temp files
        @unlink($mainPath);
        @unlink($profilePath);
        @unlink($normalPath);
        @unlink($squarePath);

        //ADD TO ALBUM
        $viewer = Engine_Api::_()->user()->getViewer();

        $photoTable = Engine_Api::_()->getItemTable('sitevideo_photo');
        $rows = $photoTable->fetchRow($photoTable->select()->from($photoTable->info('name'), 'order')->order('order DESC')->limit(1));
        $order = 0;
        if (!empty($rows)) {
            $order = $rows->order + 1;
        }
        $sitephotoAlbum = $this->getSingletonAlbum();
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'channel_id' => $this->getIdentity(),
            'album_id' => $sitephotoAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $sitephotoAlbum->getIdentity(),
            'order' => $order
        ));
        $iden = $photoItem->save();
        if (!isset($param['setChannelMainPhoto'])) {
            // Update row
            $this->modified_date = date('Y-m-d H:i:s');
            $this->file_id = $iMain->getIdentity();
        }

        if (isset($this->channel_cover) && !$this->channel_cover) {
            $this->channel_cover = $iden;
            $this->save();
        }
        if (isset($param['return']) && $param['return'] == 'photo') {
            return $photoItem;
        }
        return $this;
    }

    public function updateAllCoverPhotos() {
        $photo = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id);
        if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
            $name = basename($file);
            $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
            $params = array(
                'parent_type' => 'sitevideo_channel',
                'parent_id' => $this->getIdentity()
            );

            //STORE
            $storage = Engine_Api::_()->storage();
            $iMain = $photo;

            $thunmProfile = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.profile');

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



            $thunmMidum = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.midum');
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

  public function canView($user = null)
  {
    if( !($user instanceof User_Model_User) ) {
      $user = Engine_Api::_()->user()->getViewer();
    }
    return Engine_Api::_()->authorization()->isAllowed($this, $user, 'view') && $this->isViewableByNetwork();
  }

}
