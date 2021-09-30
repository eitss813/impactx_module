<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Video.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_Video extends Core_Model_Item_Abstract {

    protected $_searchTriggers = array('title', 'description', 'search');
    public $skipChannelDeleteHook;
    protected $_type = 'video';
    protected $_parent_type = 'sitevideo_channel';

    protected $_owner_type = 'user';
    protected $_parent_is_owner = true;

    //This function is used to mapping the video with channel
    public function addVideomap() {
        $videoMaptable = Engine_Api::_()->getDbtable('videomaps', 'sitevideo');
        $videomap = $videoMaptable->createRow();
        if ($this->main_channel_id)
            $videomap->channel_id = $this->main_channel_id;
        else
            $videomap->channel_id = 0;
        $videomap->video_id = $this->video_id;
        $videomap->owner_type = $this->owner_type;
        $videomap->owner_id = $this->owner_id;
        $videomap->save();
        return $videomap->videomap_id;
    }

    public function saveVideoThumbnail($photo) {
        
        $valid_thumb = true;
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
        } else {
            $file = $photo;
            $fileName = $photo;
            if (@GetImageSize($fileName)) {
                $valid_thumb = true;
            } else {
                $valid_thumb = false;
            }
        }
        if(empty($fileName))
            return;
        if (!$fileName) {
            $fileName = basename($file);
        }
        $params = array(
            'parent_type' => $this->getType(),
            'parent_id' => $this->getIdentity(),
            'user_id' => $this->owner_id,
            'name' => $fileName,
        );

        $thumbnail_parsed = @parse_url($fileName);
        $ext = ltrim(strrchr($fileName, '.'), '.');
        if (isset($thumbnail_parsed['path'])) {
            $ext = ltrim(strrchr($thumbnail_parsed['path'], '.'), '.');
        }
        if ($valid_thumb && $fileName && $ext && $thumbnail_parsed && in_array(strtolower($ext), array('jpg', 'jpeg', 'gif', 'png'))) {

            $file = APPLICATION_PATH . '/temporary/link_' . md5($fileName) . '.' . $ext;
            $mainPath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_m.' . $ext;
            $normalPath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_in.' . $ext;
            $largePath = APPLICATION_PATH . '/temporary/link_thumb_' . md5($fileName) . '_l.' . $ext;
            //Fetching the width and height of thumbmail
            $normalHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.height', 375);
            $normalWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
            $largeHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.height', 720);
            $largeWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
            $mainHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
            $mainWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.width', 1600);

            $src_fh = fopen($fileName, 'r');
            $tmp_fh = fopen($file, 'w');
            stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 30);
            // Resize image (main)
            if (file_exists($file)) {
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize($mainWidth, $mainHeight)
                        ->write($mainPath)
                        ->destroy();

                // Resize image (large)
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize($largeWidth, $largeHeight)
                        ->write($largePath)
                        ->destroy();

                // Resize image (normal)
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize($normalWidth, $normalHeight)
                        ->write($normalPath)
                        ->destroy();
            }
            $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
            // Store
            $iMain = $filesTable->createFile($mainPath, $params);
            $iLarge = $filesTable->createFile($largePath, $params);
            $iNormal = $filesTable->createFile($normalPath, $params);

            $iMain->bridge($iLarge, 'thumb.large');
            $iMain->bridge($iNormal, 'thumb.normal');
            $iMain->bridge($iMain, 'thumb.main');
            // Remove temp files
            @unlink($mainPath);
            @unlink($largePath);
            @unlink($normalPath);
            $this->photo_id = $iMain->getIdentity();
            $this->save();
            return $this;
        }
        return NULL;
    }

    public function isSubscribed() {
        if (empty($this->main_channel_id))
            return false;

        $owner_id = Engine_Api::_()->user()->getViewer()->user_id;
        $subscription = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo');
        $subscriptionModel = $subscription->fetchRow($subscription->select()
                        ->where('owner_id = ?', $owner_id)
                        ->where('channel_id = ?', $this->main_channel_id));
        if ($subscriptionModel)
            return true;
        return false;
    }

    public function getChannelModel() {
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $this->main_channel_id);
        return $channel;
    }

    public function getSubscriptionModel() {
        if (empty($this->main_channel_id))
            return false;

        $owner_id = Engine_Api::_()->user()->getViewer()->user_id;
        $subscription = Engine_Api::_()->getDbtable('subscriptions', 'sitevideo');
        $subscriptionModel = $subscription->fetchRow($subscription->select()
                        ->where('owner_id = ?', $owner_id)
                        ->where('channel_id = ?', $this->main_channel_id));
        if ($subscriptionModel)
            return $subscriptionModel;
        return false;
    }

    public function isWatched() {
        $owner_id = Engine_Api::_()->user()->getViewer()->user_id;
        $Watchlaters = new Sitevideo_Model_DbTable_Watchlaters();
        $WatchlaterModel = $Watchlaters->fetchRow($Watchlaters->select()
                        ->where('owner_id = ?', $owner_id)
                        ->where('video_id = ?', $this->video_id));
        if ($WatchlaterModel)
            return true;
        return false;
    }

    public function saveWatchStatus() {
        $owner_id = Engine_Api::_()->user()->getViewer()->user_id;
        $Watchlaters = new Sitevideo_Model_DbTable_Watchlaters();
        $WatchlaterModel = $Watchlaters->fetchRow($Watchlaters->select()
                        ->where('owner_id = ?', $owner_id)
                        ->where('video_id = ?', $this->video_id));
        if ($WatchlaterModel) {
            $WatchlaterModel->watched = 1;
            $WatchlaterModel->save();
        }
    }

    public function getMediaType() {

        return 'video';
    }

    public function getType($inflect = false) {
        if ($inflect) {
            return str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_type)));
        }

        return $this->_type;
    }

    public function getHref($params = array()) {
      //  todo: 5.2.1 Upgrade => fixed video issue additional cloumn which is deleted in SE version 5
        //if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1) && $this->main_channel_id) {
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1) ) {
            $params = array_merge(array(
                'route' => 'sitevideo_extended',
                'reset' => true,
//                'channel_id' => $this->main_channel_id,
                'user_id' => $this->owner_id,
                'video_id' => $this->getIdentity(),
                'slug' => $this->getSlug(),
                    ), $params);
        } else {
            $params = array_merge(array(
                'route' => 'sitevideo_extended',
                'reset' => true,
                'user_id' => $this->owner_id,
                'video_id' => $this->getIdentity(),
                'slug' => $this->getSlug()
                    ), $params);
        }
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    /**
     * Return a alubm slug
     *
     * @return slug
     * */
    public function getSlug($str = null, $maxstrlen = 64) {

        if (null === $str) {
            $str = $this->getTitle();
        }
        $maxstrlen = 225;
        return Engine_Api::_()->seaocore()->getSlug($str, $maxstrlen);
    }

    public function getChannel() {
       // todo: 5.2.1 Upgrade => fixed video issue additional cloumn which is deleted in SE version 5
      //  return Engine_Api::_()->getItem('sitevideo_channel', $this->main_channel_id);
    }

    public function getParent($type = null) {
        if (empty($this->main_channel_id)) {
            // Return listing item separately, if parent is listing.
            if (strpos($this->parent_type, 'sitereview') !== false) {
                return Engine_Api::_()->getItem('sitereview_listing', $this->parent_id);
            }

            //ADDED AT THE TIME CONTENT MEMBER'S OPTION IN VIDEO PRIVACY SETTING
            $this->_parent_type = $this->parent_type ? : $this->_parent_type;
            $this->_parent_is_owner = $this->parent_type === null || $this->parent_type === $this->_owner_type;
          return parent::getParent($type);
        }
        if (null === $type || $type === 'sitevideo_channel') {
            return $this->getChannel();
        } else {
            if (!$this->getChannel())
                return null;
            return $this->getChannel()->getParent($type);
        }
    }

    /**
     * Gets a url to the current video representing this item. Return null if none
     * set
     *
     * @param string The video type (null -> main, thumb, icon, etc);
     * @return string The video photo url
     */
    public function getPhotoUrl($type = null) {
        $video_id = $this->photo_id;
        if (!$video_id) {
            return null;
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($video_id, $type);
        if (!$file) {
            return null;
        }

        return $file->map();
    }

    public function isSearchable() {
        $channel = $this->getChannel();
        if (!($channel instanceof Core_Model_Item_Abstract)) {
            return false;
        }
        return $channel->isSearchable();
    }

    public function isOwner($user) {
        if (empty($this->main_channel_id)) {
            return (($this->owner_id == $user->getIdentity()) && ($this->owner_type == $user->getType()));
        }
        return parent::isOwner($user);
    }

    public function setVideo($video, $paramss = array()) {
        if ($video instanceof Zend_Form_Element_File) {
            $file = $video->getFileName();
            $fileName = $file;
        } else if ($video instanceof Storage_Model_File) {
            $file = $video->temporary();
            $fileName = $video->name;
        } else if ($video instanceof Core_Model_Item_Abstract && !empty($video->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $video->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($video) && !empty($video['tmp_name'])) {
            $file = $video['tmp_name'];
            $fileName = $video['name'];
        } else if (is_string($video) && file_exists($video)) {
            $file = $video;
            $fileName = $video;
        } else {
            throw new User_Model_Exception('invalid argument passed to setVideo');
        }

        if (!$fileName) {
            $fileName = $file;
        }

        $name = basename($file);
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => $this->getType(),
            'parent_id' => $this->getIdentity(),
            'user_id' => $this->owner_id,
            'name' => $fileName,
        );
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        $mainHeight = $coreSettings->getSetting('main.video.height', 1600);
        $mainWidth = $coreSettings->getSetting('main.video.width', 1600);


        // Add autorotation for uploded images. It will work only for SocialEngine-4.8.9 Or more then.
        $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
        if (!empty($hasVersion)) {
            // Resize image (main)
            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize($mainWidth, $mainHeight)
                    ->write($mainPath)
                    ->destroy();

            $normalHeight = $coreSettings->getSetting('normal.video.height', 375);
            $normalWidth = $coreSettings->getSetting('normal.video.width', 375);
            // Resize image (normal)
            $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize($normalWidth, $normalHeight)
                    ->write($normalPath)
                    ->destroy();

            $normalLargeHeight = $coreSettings->getSetting('normallarge.video.height', 720);
            $normalLargeWidth = $coreSettings->getSetting('normallarge.video.width', 720);
            // Resize image (normal)
            $normalLargePath = $path . DIRECTORY_SEPARATOR . $base . '_inl.' . $extension;

            $image = Engine_Image::factory();
            $image->open($file)
                    ->resize($normalLargeWidth, $normalLargeHeight)
                    ->write($normalLargePath)
                    ->destroy();
        } else {
            // Resize image (main)
            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
            $image = Engine_Image::factory();
            $image->open($file)
                    ->autoRotate()
                    ->resize($mainWidth, $mainHeight)
                    ->write($mainPath)
                    ->destroy();

            $normalHeight = $coreSettings->getSetting('normal.video.height', 375);
            $normalWidth = $coreSettings->getSetting('normal.video.width', 375);
            // Resize image (normal)
            $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

            $image = Engine_Image::factory();
            $image->open($file)
                    ->autoRotate()
                    ->resize($normalWidth, $normalHeight)
                    ->write($normalPath)
                    ->destroy();

            $normalLargeHeight = $coreSettings->getSetting('normallarge.video.height', 720);
            $normalLargeWidth = $coreSettings->getSetting('normallarge.video.width', 720);
            // Resize image (normal)
            $normalLargePath = $path . DIRECTORY_SEPARATOR . $base . '_inl.' . $extension;

            $image = Engine_Image::factory();
            $image->open($file)
                    ->autoRotate()
                    ->resize($normalLargeWidth, $normalLargeHeight)
                    ->write($normalLargePath)
                    ->destroy();
        }





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
        try {
            $iMain = $filesTable->createFile($mainPath, $params);
            $iIconNormal = $filesTable->createFile($normalPath, $params);
            $iMain->bridge($iIconNormal, 'thumb.normal');
            $iIconNormalLarge = $filesTable->createFile($normalLargePath, $params);
            $iMain->bridge($iIconNormalLarge, 'thumb.medium');

            $iSquare = $filesTable->createFile($squarePath, $params);
            $iMain->bridge($iSquare, 'thumb.icon');
        } catch (Exception $e) {
            // Remove temp files
            @unlink($mainPath);
            @unlink($normalPath);
            @unlink($normalLargePath);
            @unlink($squarePath);
            // Throw
            if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
                throw new Channel_Model_Exception($e->getMessage(), $e->getCode());
            } else {
                throw $e;
            }
        }

        // Remove temp files
        @unlink($mainPath);
        @unlink($normalPath);
        @unlink($normalLargePath);
        @unlink($squarePath);
        // Update row
        $this->modified_date = date('Y-m-d H:i:s');

        if (isset($paramss['setPhotoId'])) {
            $this->photo_id = $iMain->file_id;
            $this->save();
        } else {
            $this->file_id = $iMain->file_id;
            $this->save();
        }

        // Delete the old file?
        if (!empty($tmpRow)) {
            $tmpRow->delete();
        }

        return $this;
    }

    public function getVideoIndex() {
        return $this->getTable()
                        ->select()
                        ->from($this->getTable(), new Zend_Db_Expr('COUNT(video_id)'))
                        ->where('main_channel_id = ?', $this->main_channel_id)
                        ->where('`order` < ?', $this->order)
                        ->order('order ASC')
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
    }

    public function getNextVideo() {
        $table = $this->getTable();
        $select = $table->select()
                ->where('main_channel_id = ?', $this->main_channel_id)
                ->where('`order` > ?', $this->order)
                ->order('order ASC')
                ->limit(1);
        $video = $table->fetchRow($select);

        if (!$video) {
            // Get first video instead
            $select = $table->select()
                    ->where('main_channel_id = ?', $this->main_channel_id)
                    ->order('order ASC')
                    ->limit(1);
            $video = $table->fetchRow($select);
        }

        return $video;
    }

    public function getPreviousVideo() {
        $table = $this->getTable();
        $select = $table->select()
                ->where('main_channel_id = ?', $this->main_channel_id)
                ->where('`order` < ?', $this->order)
                ->order('order DESC')
                ->limit(1);
        $video = $table->fetchRow($select);

        if (!$video) {
            // Get last video instead
            $select = $table->select()
                    ->where('main_channel_id = ?', $this->main_channel_id)
                    ->order('order DESC')
                    ->limit(1);
            $video = $table->fetchRow($select);
        }

        return $video;
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

    /**
     * Delete the video and belongings
     * 
     */
    protected function _postDelete() {

        $video_id = $this->video_id;

        $mainVideo = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id);
        $thumbVideo = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.normal');

        // Delete thumb
        if ($thumbVideo && $thumbVideo->getIdentity()) {
            try {
                $thumbVideo->delete();
            } catch (Exception $e) {
                
            }
        }

        // Delete main
        if ($mainVideo && $mainVideo->getIdentity()) {
            try {
                $mainVideo->delete();
            } catch (Exception $e) {
                
            }
        }

        //DELETE VIDEO ENTRY FROM RATING TABLE
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitevideo');
        $ratingTable->delete(array('resource_id =?' => $video_id, 'resource_type =?' => 'sitevideo_video'));

        //DELETE VIDEO ENTRY FROM ITEMOFTHEDAY TABLE
        $itemofthedays = Engine_Api::_()->getDbtable('itemofthedays', 'sitevideo');
        $itemofthedays->delete(array('resource_id =?' => $video_id, 'resource_type =?' => 'sitevideo_video'));

        // Change channel cover if applicable
        try {
            if (!empty($this->main_channel_id) && !$this->skipChannelDeleteHook) {
                $channel = $this->getChannel();
                $nextVideo = $this->getNextVideo();
                if (($channel instanceof Sitevideo_Model_Channel) &&
                        ($nextVideo instanceof Sitevideo_Model_Video) &&
                        (int) $channel->video_id == (int) $this->getIdentity()) {
                    $channel->video_id = $nextVideo->getIdentity();
                    $channel->save();
                }
                $channel->videos_count = $channel->videos_count - 1;
                $channel->save();
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
                $addlocationsTable = Engine_Api::_()->getDbTable('addlocations', 'sitetagcheckin');
                $addlocationsSelect = $addlocationsTable->select()
                        ->from($addlocationsTable->info('name'), array('addlocation_id'))
                        ->where('resource_id = ?', $video_id)
                        ->where('resource_type = ?', 'sitevideo_video');
                $addlocations = $addlocationsTable->fetchAll($addlocationsSelect);
                foreach ($addlocations as $addlocation) {
                    $addlocation->delete();
                }
            }

            $locationitemsTable = Engine_Api::_()->getDbTable('locationitems', 'seaocore');
            $locationitemsSelect = $locationitemsTable->select()
                    ->from($locationitemsTable->info('name'), array('locationitem_id'))
                    ->where('resource_id = ?', $video_id)
                    ->where('resource_type = ?', 'sitevideo_video');
            $locationitems = $locationitemsTable->fetchAll($locationitemsSelect);
            foreach ($locationitems as $locationitem) {
                $locationitem->delete();
            }
        } catch (Exception $e) {
            
        }

        parent::_postDelete();
    }

    public function getRichContent($view = false, $params = array(),$frameCode=false) {
        $session = new Zend_Session_Namespace('mobile');
        $mobile = $session->mobile;

        // if video type is youtube
        if ($this->checkType('youtube')) {
            $videoEmbedded = $this->compileYouTube($this->video_id, $this->code, $view, $mobile);
        }
        // if video type is vimeo
        else if ($this->checkType('vimeo')) {
            $videoEmbedded = $this->compileVimeo($this->video_id, $this->code, $view, $mobile);
        }
        // if video type is dailymotion
        else if ($this->checkType('dailymotion')) {
            $videoEmbedded = $this->compileDailymotion($this->video_id, $this->code, $view, $mobile);
        }
        // if video type is iframely
        else if ($this->checkType('iframely')) {
            $videoEmbedded = $this->code;
        }
        else if ($this->checkType('stream') && Engine_Api::_()->sitevideo()->enableStreamVideo() ) {           
          $storage_file = Engine_Api::_()->storage()->get($this->file_id, $this->getType());
          if( $storage_file && $storage_file->extension == 'm3u8') {
             $videoEmbedded = $this->compileHLSMedia($storage_file->getHref(), $view);
          }
          if( $storage_file && $storage_file->extension == 'mpd') {
             $videoEmbedded = $this->compileDASHMedia($storage_file->getHref(), $view);
          }
        }
        else if ($this->checkType('embedcode')) {
            $videoEmbedded = $this->compileOtherEmbedCode($this->video_id, $this->code, $view, $mobile);
        } else if ($this->checkType('instagram')) {
			$videoEmbedded = $this->compileInstagramEmbedCode($this->video_id, $this->code, $view, $mobile);
			
        } else if ($this->checkType('twitter')) {
			
            $videoEmbedded = $this->compileTwitterEmbedCode($this->video_id, $this->code, $view, $mobile);
        } else if ($this->checkType('pinterest')) {
            $videoEmbedded = $this->compilePinterestEmbedCode($this->video_id, $this->code, $view, $mobile);
        } else if( $this->type == 'iframely' ) {
            $videoEmbedded = $this->code;
        }

        // if video type is upload
        else if ($this->checkType('upload')) {
            $storage_file = Engine_Api::_()->storage()->get($this->file_id, $this->getType());
            $video_location = $storage_file->getHref();
            if ($storage_file->extension === 'flv') {
                $videoEmbedded = $this->compileFlowPlayer($video_location, $view);
            } else {
                $videoEmbedded = $this->compileHTML5Media($video_location, $view);
            }
        }

        // $view == false means that this rich content is requested from the activity feed
        if ($view == false) {

            // prepare the duration
            //
      $video_duration = "";
            if ($this->duration) {
                if ($this->duration >= 3600) {
                    $duration = gmdate("H:i:s", $this->duration);
                } else {
                    $duration = gmdate("i:s", $this->duration);
                }
                //$duration = ltrim($duration, '0:');

                $video_duration = "<span class='video_length'>" . $duration . "</span>";
            }

            // prepare the thumbnail
            $thumb = '';
            if( $this->type != 'iframely' ) {
            $thumb = Zend_Registry::get('Zend_View')->itemPhoto($this, 'thumb.video.activity');

            if ($this->photo_id) {
                $thumb = Zend_Registry::get('Zend_View')->itemPhoto($this, 'thumb.video.activity');
            } else {
                $thumb = '<img alt="" src="' . Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sitevideo/externals/images/video_default.png">';
            }

            if (!$mobile) {
                $thumb = '<a id="video_thumb_' . $this->video_id . '" style="" href="javascript:void(0);" onclick="javascript:var myElement = $(this);myElement.style.display=\'none\';var next = myElement.getNext(); next.style.display=\'block\';">
                  <div class="video_thumb_wrapper">' . $video_duration . $thumb . '</div>
                  </a>';
            } else {
                $thumb = '<a id="video_thumb_' . $this->video_id . '" class="video_thumb" href="' . $this->getHref() . '">
                  <div class="video_thumb_wrapper">' . $video_duration . $thumb . '</div>
                  </a>';
            }
            }
            // prepare title and description
            $title = "<a href='" . $this->getHref($params) . "'>$this->title</a>";
            $tmpBody = strip_tags($this->description);
            $description = "<div class='video_desc'>" . (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody) . "</div>";

            $videoEmbedded = $thumb . '<div id="video_object_' . $this->video_id . '" class="video_object">' . $videoEmbedded . '</div><div class="video_info">' . $title . $description . '</div>';
        }
        return $videoEmbedded;
    }

    public function getEmbedCode(array $options = null) {
        $options = array_merge(array(
            'height' => '525',
            'width' => '525',
                ), (array) $options);

        $view = Zend_Registry::get('Zend_View');
        $url = '//' . $_SERVER['HTTP_HOST']
                . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                    'module' => 'sitevideo',
                    'controller' => 'video',
                    'action' => 'external',
                    'video_id' => $this->getIdentity(),
                        ), 'default', true) . '?format=frame';
        return '<iframe '
                . 'src="' . $view->escape($url) . '" '
                . 'width="' . sprintf("%d", $options['width']) . '" '
                . 'height="' . sprintf("%d", $options['width']) . '" '
                . 'style="overflow:hidden;"'
                . '>'
                . '</iframe>';
    }

  public function compileHLSMedia($location, $view)
  {
    $viewHelper = Zend_Registry::get('Zend_View');
    return $viewHelper->partial('_player.tpl', 'sitevod', array('type' => 'hls','streamId' => 'sitevideo_rightcontent_' . $this->getGuid() . '_' . time(), 'src' => $location, 'poster' => $this->getPhotoUrl(), 'autoPlay' => $view, 'width' => ($view ? "10240" : "420"), 'height' => ($view ? "824" : "326")));
  }
    public function compileDASHMedia($location, $view)
  {
    $viewHelper = Zend_Registry::get('Zend_View');
    return $viewHelper->partial('_player.tpl', 'sitevod', array('type' => 'dash', 'streamId' => 'sitevideo_rightcontent_' . $this->getGuid() . '_' . time(), 'src' => $location, 'poster' => $this->getPhotoUrl(), 'autoPlay' => $view, 'width' => ($view ? "1024" : "420"), 'height' => ($view ? "824" : "326")));

  }

  public function compileYouTube($video_id, $code, $view, $mobile = false) {
        $autoplay = !$mobile && $view;

        $embedded = '
    <iframe
    title="YouTube video player"
    id="videoFrame' . $video_id . '"
    class="youtube_iframe' . ($view ? "_big" : "_small") . '"' .
                'src="//www.youtube.com/embed/' . $code . '?wmode=opaque' . ($autoplay ? "&autoplay=1" : "") . '"
    frameborder="0"
    allowfullscreen=""
    scrolling="no">
    </iframe>
    <script type="text/javascript">
        en4.core.runonce.add(function() {
        var doResize = function() {
            var aspect = 16 / 9;
            var el = document.id("videoFrame' . $video_id . '");
            var parent = el.getParent();
            var parentSize = parent.getSize();
            el.set("width", parentSize.x);
            el.set("height", parentSize.x / aspect);
        }
        window.addEvent("resize", doResize);
        doResize();
        });
    </script>
    ';

        return $embedded;
    }

    public function compileVimeo($video_id, $code, $view, $mobile = false) {
        $autoplay = !$mobile && $view;

        $embedded = '
        <iframe
        title="Vimeo video player"
        id="videoFrame' . $video_id . '"
        class="vimeo_iframe' . ($view ? "_big" : "_small") . '"' .
                ' src="//player.vimeo.com/video/' . $code . '?title=0&amp;byline=0&amp;portrait=0&amp;wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "") . '"
        frameborder="0"
        allowfullscreen=""
        scrolling="no">
        </iframe>
        <script type="text/javascript">
          en4.core.runonce.add(function() {
            var doResize = function() {
              var aspect = 16 / 9;
              var el = document.id("videoFrame' . $video_id . '");
              var parent = el.getParent();
              var parentSize = parent.getSize();
              el.set("width", parentSize.x);
              el.set("height", parentSize.x / aspect);
            }
            window.addEvent("resize", doResize);
            doResize();
          });
        </script>
        ';

        return $embedded;
    }

    public function compileOtherEmbedCode($video_id, $code, $view, $mobile = false) {
        $autoplay = !$mobile && $view;
        $embedded = '
        <iframe
        title="Video player"
        id="videoFrame' . $video_id . '"
        class="dailymotion_iframe' . ($view ? "_big" : "_small") . '"' .
                ' src="' . $code . '"
        frameborder="0"
        allowfullscreen=""
        scrolling="no">
        </iframe>
        <script type="text/javascript">
          en4.core.runonce.add(function() {
            var doResize = function() {
              var aspect = 16 / 9;
              var el = document.id("videoFrame' . $video_id . '");
              var parent = el.getParent();
              var parentSize = parent.getSize();
              el.set("width", parentSize.x);
              el.set("height", parentSize.x / aspect);
            }
            window.addEvent("resize", doResize);
            doResize();
          });
        </script>
        ';
        return $embedded;
    }

    public function compilePinterestEmbedCode($video_id, $code, $view, $mobile = false) {
        $autoplay = !$mobile && $view;
        //data-pin-width=medium,large,small
        $embedded = '
        <a  id="videoFrame' . $video_id . '" data-pin-do="embedPin" data-pin-width="large" data-pin-terse="true" class="dailymotion_iframe' . ($view ? "_big" : "_small") . '" href="' . $code . '"  ></a>
        <script async defer src="//assets.pinterest.com/js/pinit.js"></script>
        ';
        return $embedded;
    }

   public function compileInstagramEmbedCode($video_id, $code, $view, $mobile = false) {
        $embedded = '
        <blockquote class="instagram-media" data-instgrm-captioned data-instgrm-version="6" style="width:100%; width:-webkit-calc(100%); width:calc(100%);">
<a href="'.$code.'" target="_blank"></a>
 </blockquote>
<script async defer src="//platform.instagram.com/en_US/embeds.js"></script>
        ';

        return $embedded;
    }
	public function compileInstagramIframeEmbedCode($video_id, $code, $view, $mobile = false) {
        $autoplay = !$mobile && $view;
        $embedded = '
        <iframe
        title="Video player"
        id="videoFrame' . $video_id . '"
        class="dailymotion_iframe' . ($view ? "_big" : "_small") . '"' .
                ' src="' . $code . "embed/captioned" . '"
        frameborder="0"
        allowfullscreen=""
        scrolling="no">
        </iframe>
        <script type="text/javascript">
          en4.core.runonce.add(function() {
            var doResize = function() {
              var aspect = 16 / 9;
              var el = document.id("videoFrame' . $video_id . '");
              var parent = el.getParent();
              var parentSize = parent.getSize();
              el.set("width", parentSize.x);
              el.set("height", parentSize.x / aspect);
            }
            window.addEvent("resize", doResize);
            doResize();
          });
        </script>
        ';

        return $embedded;
    }

    public function compileTwitterEmbedCode($video_id, $code, $view, $mobile = false) {
        $embedded = '
        <blockquote class="twitter-tweet" data-lang="en">
        <a href="' . $code . '"></a></blockquote>
        <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
        ';
        return $embedded;
    }

    public function compileDailymotion($video_id, $code, $view, $mobile = false) {
        $autoplay = !$mobile && $view;

        $embedded = '
        <iframe
        title="Dailymotion video player"
        id="videoFrame' . $video_id . '"
        class="dailymotion_iframe' . ($view ? "_big" : "_small") . '"' .
                ' src="//www.dailymotion.com/embed/video/' . $code . '?wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "") . '"
        frameborder="0"
        allowfullscreen=""
        scrolling="no">
        </iframe>
        <script type="text/javascript">
          en4.core.runonce.add(function() {
            var doResize = function() {
              var aspect = 16 / 9;
              var el = document.id("videoFrame' . $video_id . '");
              var parent = el.getParent();
              var parentSize = parent.getSize();
              el.set("width", parentSize.x);
              el.set("height", parentSize.x / aspect);
            }
            window.addEvent("resize", doResize);
            doResize();
          });
        </script>
        ';

        return $embedded;
    }

    public function compileFlowPlayer($location, $view) {

        //GET CORE VERSION
        $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;

        $flowplayerSwf = !Engine_Api::_()->sitevideo()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version, '4.8.10') ? 'externals/flowplayer/flowplayer-3.1.5.swf' : 'externals/flowplayer/flowplayer-3.2.18.swf';
        $embedded = "
    <div id='videoFrame" . $this->video_id . "'></div>
    <script type='text/javascript'>
    en4.core.runonce.add(function(){\$('video_thumb_" . $this->video_id . "').removeEvents('click').addEvent('click', function(){flashembed('videoFrame$this->video_id',{src: '" . Zend_Registry::get('StaticBaseUrl') . $flowplayerSwf . "', width: " . ($view ? "480" : "420") . ", height: " . ($view ? "386" : "326") . ", wmode: 'opaque'},{config: {clip: {url: '$location',autoPlay: " . ($view ? "false" : "true") . ", duration: '$this->duration', autoBuffering: true},plugins: {controls: {background: '#000000',bufferColor: '#333333',progressColor: '#444444',buttonColor: '#444444',buttonOverColor: '#666666'}},canvas: {backgroundColor:'#000000'}}});})});
    </script>";

        return $embedded;
    }

    public function compileHTML5Media($location, $view) {
        $embedded = "
    <video id='video" . $this->video_id . "' controls preload='auto' width='" . ($view ? "480" : "420") . "' height='" . ($view ? "386" : "326") . "'>
      <source type='video/mp4;' src=" . $location . ">
    </video>";
        return $embedded;
    }

    /**
     * Set video location
     *
     */
    public function setLocation() {

        $id = $this->video_id;

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0)) {
            $sitevideo = $this;
            if (!empty($sitevideo))
                $location = $sitevideo->location;

            if (!empty($location)) {
                $locationTable = Engine_Api::_()->getDbtable('locations', 'sitevideo');
                $locationRow = $locationTable->getLocation(array('id' => $id));

                if (isset($_POST['locationParams']) && $_POST['locationParams']) {
                    if (is_string($_POST['locationParams']))
                        $_POST['locationParams'] = Zend_Json_Decoder::decode($_POST['locationParams']);
                    if ($_POST['locationParams']['location'] === $location) {
                        try {
                            $loctionV = $_POST['locationParams'];
                            $loctionV['video_id'] = $id;
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

                            $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=$urladdress&sensor=true";

                            $ch = curl_init();
                            $timeout = 5;
                            curl_setopt($ch, CURLOPT_URL, $request_url);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                            ob_start();
                            curl_exec($ch);
                            curl_close($ch);
                            $obGetContents = ob_get_contents();
                            $json_resopnse = !empty($obGetContents) ? Zend_Json::decode($obGetContents) : array();
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

                                try {
                                    $loctionV = array();
                                    $loctionV['video_id'] = $id;
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
                                    if (empty($locationRow))
                                        $locationRow = $locationTable->createRow();

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
                            //CREATE VIDEO LOCATION
                            $loctionV = array();
                            if (empty($locationRow))
                                $locationRow = $locationTable->createRow();
                            $value = $getSEALocation->toarray();
                            unset($value['location_id']);
                            $value['video_id'] = $id;
                            $locationRow->setFromArray($value);
                            $locationRow->save();
                        } catch (Exception $e) {
                            throw $e;
                        }
                    }
                } else {

                    try {
                        //CREATE VIDEO LOCATION
                        $loctionV = array();
                        if (empty($locationRow))
                            $locationRow = $locationTable->createRow();
                        $value = $locationValue->toarray();
                        unset($value['location_id']);
                        $value['video_id'] = $id;
                        $locationRow->setFromArray($value);
                        $locationRow->save();
                    } catch (Exception $e) {
                        throw $e;
                    }
                }
            }
        }
    }

    public function isViewableByNetwork() {
        $regName = 'view_privacy_' . $this->getGuid();
        if (!Zend_Registry::isRegistered($regName)) {
            $flage = true;
            $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.network', 0);
            $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.networkprofile.privacy', 0);
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($enableNetwork && $viewPricavyEnable && !$this->isOwner($viewer)) {
                $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);
                if (Engine_Api::_()->sitevideo()->videoBaseNetworkEnable()) {
                    if ($this->networks_privacy) {
                        if (!empty($viewerNetworkIds)) {
                            $videoNetworkId = explode(",", $this->networks_privacy);
                            $commanIds = array_intersect($videoNetworkId, $viewerNetworkIds);
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

    public function canView($user = null) {
        if (!($user instanceof User_Model_User))
            $user = Engine_Api::_()->user()->getViewer();
        $can_view = $this->isViewableByNetwork();

        if ($can_view) {
            $can_view = Engine_Api::_()->authorization()->isAllowed($this->getType(), $user, 'view');
        } else {
            // $can_view = Engine_Api::_()->authorization()->isAllowed($this->getType(), $user, 'edit');
        }

        // CHECK CHANNEL VIEW PERMISSION
        if ($can_view && $this->main_channel_id && $this->getChannel() ) {
            $can_view = $this->getChannel()->canView($user);
        }
        return $can_view;
    }

    public function checkPasswordProtection($password = null) {

        if (!$password)
            return false;

        $videoTable = Engine_Api::_()->getItemTable('sitevideo_video');
        $db = $videoTable->getAdapter();
        $select = $videoTable->select()
                ->from($videoTable, new Zend_Db_Expr('TRUE'))
                ->where('video_id = ?', $this->getIdentity())
                ->where('password = ?', $password)
                ->limit(1)
        ;

        $valid = $select
                ->query()
                ->fetchColumn()
        ;
        return $valid;
    }
    function checkType($type){
        return $this->type==$type;
    }

}
