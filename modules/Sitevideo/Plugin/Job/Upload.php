<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UploadYoutubeVideos.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Plugin_Job_Upload extends Core_Plugin_Job_Abstract {

    protected function _execute() {

        set_time_limit(0);
        // No channel id?
        if (!($channel_id = $this->getParam('channel_id'))) {
            $this->_setState('failed', 'No channel identity provided.');
            $this->_setWasIdle();
            return;
        }
        //No Youtube API KEY
        $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.youtube.apikey');
        if (empty($key)) {
            $this->_setState('failed', 'Youtube API key is not configured.');
            $this->_setWasIdle();
            return;
        }
        // Get channel object
        $channel = Engine_Api::_()->getItem('sitevideo_channel', $channel_id);
        if (!$channel) {
            $this->_setState('failed', 'Channel is missing.');
            $this->_setWasIdle();
            return;
        }

        // Check Channel Pending videos
        if (empty($channel->pending_video)) {
            $this->_setState('failed', 'Video has been already uploaded.');
            $this->_setWasIdle();
            return;
        }


        $youtubeVideos = $tempVideos = explode(',', $channel->pending_video);
        try {
            foreach ($youtubeVideos as $key => $youtubeVideo) {
                try {
                    $this->createVideo($youtubeVideo, $channel);
                    unset($tempVideos[$key]);
                    $channel->pending_video = implode(',', $tempVideos);
                    $channel->save();
                } catch (Exception $e) {
                    throw $e;
                }
            }
            $this->_setIsComplete(true);
        } catch (Exception $e) {
            $this->_setState('failed', 'Exception: ' . $e->getMessage());
            $this->_addMessage($e->getMessage());
        }
    }

    public function createVideo($youtubeVideoId, $channel) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $table = Engine_Api::_()->getDbtable('videos', 'sitevideo');
            $video = $table->createRow();
            $information = $this->handleInformation($youtubeVideoId);
            $thumbnail = $this->handleThumbnail($youtubeVideoId);
            $video->type = 'youtube';
            $video->code = $youtubeVideoId;
            if (isset($information['duration'])) {
                $video->duration = $information['duration'];
            }
            if (isset($information['description'])) {
                $video->description = $information['description'];
            }
            if (isset($information['title'])) {
                $video->title = $information['title'];
            } else {
                $video->title = $channel->title;
            }
            $video->owner_id = $channel->owner_id;
            $video->owner_type = $channel->owner_type;
            $video->save();
            $video->saveVideoThumbnail($thumbnail);
            $video->synchronized = 1;
            $video->status = 1;
            $video->main_channel_id = $channel->channel_id;

            $video->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $db->beginTransaction();
        try {
            //if Channel category find in video category then insert category otherwise escape
            if (!empty($channel->category_id)) {
                $channelCategory = Engine_Api::_()->getItem('sitevideo_channel_category', $channel->category_id);
                $videoCategory = Engine_Api::_()->getItemTable('sitevideo_video_category')
                        ->fetchRow(array('category_name=?' => $channelCategory->category_name,'cat_dependency=?'=>0));
                if ($videoCategory){
                    $video->category_id = $videoCategory->category_id;
                    if(!empty($channel->subcategory_id)){
                        $channelSubCategory = Engine_Api::_()->getItem('sitevideo_channel_category', $channel->subcategory_id);
                        $videoSubCategory = Engine_Api::_()->getItemTable('sitevideo_video_category')
                        ->fetchRow(array('category_name=?' => $channelSubCategory->category_name,'cat_dependency=?'=>$videoCategory->category_id));
                        if($videoSubCategory){
                            $video->subcategory_id = $videoSubCategory->category_id;
                        }
                    }
                }else{
                    $videoCategory = Engine_Api::_()->getItemTable('sitevideo_video_category')
                        ->fetchRow(array('category_name=?' => 'Others'));
                    if($videoCategory){
                        $video->category_id = $videoCategory->category_id;
                    }
                }
            }
            //Video is inside main channel id
            $video->addVideomap();
            //Send Site Notification
            Engine_Api::_()->getApi('core', 'sitevideo')->sendSiteNotification($video, $channel, 'sitevideo_video_new');
            //Send Email Notification
            Engine_Api::_()->getApi('core', 'sitevideo')->sendEmailNotification($video, $channel, 'sitevideo_video_new', 'SITEVIDEO_CREATENOTIFICATION_EMAIL');
            $video->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $db->beginTransaction();
        try {

            //ADDING TAGS
            $keywords = '';
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.tags.enabled', 1)) {
                if (isset($information['tags']) && is_array($information['tags']) && count($information['tags']) > 0) {
                    $tags = $information['tags'];
                    $tags = array_filter(array_map("trim", $tags));
                    $video->tags()->addTagMaps($video->getOwner(), $tags);
                }
            }
            //Channel privacy inserted into video privacy
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            foreach ($roles as $role) {
                if (1 === $auth->isAllowed($channel, $role, 'view')) {
                    $auth->setAllowed($video, $role, 'view', true);
                }
                if (1 === $auth->isAllowed($channel, $role, 'comment')) {
                    $auth->setAllowed($video, $role, 'comment', true);
                }
            }

            //Insert activity feeds
            $owner = $video->getOwner();
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $actionTable->addActivity($owner, $channel, 'sitevideo_channel_video_new');
            $actionTable->attachActivity($action, $video);
            foreach ($actionTable->getActionsByObject($video) as $action) {
                $actionTable->resetActivityBindings($action);
            }

            //update channel videos count
            $channel->videos_count = $channel->videos_count + 1;
            $channel->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function handleThumbnail($code = null) {
        $thumbnail = "";
        $thumbnailSize = array('maxresdefault', 'sddefault', 'hqdefault', 'mqdefault', 'default');
        foreach ($thumbnailSize as $size) {
            $thumbnailUrl = "https://i.ytimg.com/vi/$code/$size.jpg";
            $file_headers = @get_headers($thumbnailUrl);
            if (isset($file_headers[0]) && strpos($file_headers[0], '404 Not Found') == false) {
                $thumbnail = $thumbnailUrl;
                break;
            }
        }
        return $thumbnail;
    }

    // retrieves infromation and returns title + desc
    public function handleInformation($code) {
        $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.youtube.apikey');
        $data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $code . '&key=' . $key);
        if (empty($data)) {
            return;
        }
        $data = Zend_Json::decode($data);
        $information = array();
        $youtube_video = $data['items'][0];
        $information['title'] = $youtube_video['snippet']['title'];
        $information['description'] = $youtube_video['snippet']['description'];
        $information['duration'] = Engine_Date::convertISO8601IntoSeconds($youtube_video['contentDetails']['duration']);
        $information['tags'] = $youtube_video['snippet']['tags'];
        return $information;
    }

}
