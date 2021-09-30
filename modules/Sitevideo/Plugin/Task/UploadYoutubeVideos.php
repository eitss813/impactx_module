<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UpdateProjectStatus.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Plugin_Task_UploadYoutubeVideos extends Core_Plugin_Task_Abstract {

    public function execute() {
        $isAllowAutomaticUpload = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo_automatic_videouploadallow', 0);
        $maxAllowedVideo = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo_automatic_uploadvideoscount', 20);
        $uploadAfterDay = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo_automatic_uploadvideoindays', 3);
        if (!$isAllowAutomaticUpload || !$maxAllowedVideo || !$uploadAfterDay || $uploadAfterDay <= 0 || $maxAllowedVideo <= 0) {
            return false;
        }
        if ($maxAllowedVideo > 50) {
            $maxAllowedVideo = 50;
        }
        $tillVideoUploaded = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.youtubevideo.uploaded', 0);
        $currentDate = date('Y-m-d');
        if ($tillVideoUploaded) {
            $nextUploadDate = mktime(0, 0, 0, date('m', strtotime($tillVideoUploaded)), date('d', strtotime($tillVideoUploaded)) + $uploadAfterDay + 1, date('Y', strtotime($tillVideoUploaded)));
            if (strtotime($currentDate) < $nextUploadDate)
                return;
        }else {
            $tillVideoUploaded = date('Y-m-d', mktime(0, 0, 0, date('m', strtotime($currentDate)), date('d', strtotime($currentDate)) - $uploadAfterDay, date('Y', strtotime($currentDate))));
            $nextUploadDate = mktime(0, 0, 0, date('m', strtotime($tillVideoUploaded)), date('d', strtotime($tillVideoUploaded)) + $uploadAfterDay, date('Y', strtotime($tillVideoUploaded)));
        }
        try {
            $this->uploadVideo($maxAllowedVideo, $tillVideoUploaded, $nextUploadDate);
            $tillUploadedDate = date('Y-m-d H:i:s', $nextUploadDate - 1);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitevideo.youtubevideo.uploaded', $tillUploadedDate);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function uploadVideo($max, $tillVideoUploaded, $nextUploadDate) {

        $apiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.youtube.apikey');
        $channels = Engine_Api::_()->getDbtable('channels', 'sitevideo')->fetchAll(array('youtube_channel_id<>?' => ''));
        $maxResults = ($max * 3) > 50 ? 50 : ($max * 3);
        $publishedAfter = date("Y-m-d\TH:i:sP", strtotime($tillVideoUploaded));
        $publishedBefore = date("Y-m-d\TH:i:sP", $nextUploadDate);
        $params = array();
        $params['publishedAfter'] = urlencode($publishedAfter);
        $params['publishedBefore'] = urlencode($publishedBefore);
        $params['maxResults'] = $maxResults;
        $params['key'] = $apiKey;
        foreach ($channels as $channel) {
            $youtubeChannelId = $channel->youtube_channel_id;
            $mapParams = array('channel_id' => $channel->getIdentity(), 'type' => 'youtube');
            $youtubeVideos = Engine_Api::_()->getDbtable('videomaps', 'sitevideo')->findVideoMaps($mapParams);
            $existingVideo = array();
            foreach ($youtubeVideos as $video) {
                $existingVideo[] = $video->code;
            }
            $params['channelId'] = $youtubeChannelId;
            $data = $this->fetchYoutubeVideos($params);

            if (!$data) {
                continue;
            }
            $videosList = Zend_Json::decode($data);
            if (!isset($videosList['pageInfo']) || !isset($videosList['pageInfo']['totalResults']) || !isset($videosList['items']) || $videosList['pageInfo']['totalResults'] == 0 || count($videosList['items']) == 0) {
                continue;
            }

            $items = $videosList['items'];
            $counter = 0;
            foreach ($items as $item) {
                $id = $item['id']['videoId'];
                if (in_array($id, $existingVideo)) {
                    continue;
                }
                $this->createVideo($id, $channel);
                $counter++;
                if ($counter >= $max) {
                    break;
                }
            }
        }
    }

    public function fetchYoutubeVideos($params) {
        $args = array('part=snippet', 'order=date','type=video');
        foreach ($params as $key => $value) {
            $args[] = "$key=$value";
        }
        $argsStr = implode("&", $args);
        $url = "https://www.googleapis.com/youtube/v3/search?$argsStr";
        $data = @file_get_contents($url);
        return $data;
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
                        ->fetchRow(array('category_name=?' => $channelCategory->category_name));
                if ($videoCategory) {
                    $video->category_id = $videoCategory->category_id;
                    if (!empty($channel->subcategory_id)) {
                        $channelSubCategory = Engine_Api::_()->getItem('sitevideo_channel_category', $channel->subcategory_id);
                        $videoSubCategory = Engine_Api::_()->getItemTable('sitevideo_video_category')
                                ->fetchRow(array('category_name=?' => $channelSubCategory->category_name, 'cat_dependency=?' => $videoCategory->category_id));
                        if ($videoSubCategory) {
                            $video->subcategory_id = $videoSubCategory->category_id;
                        }
                    }
                } else {
                    $videoCategory = Engine_Api::_()->getItemTable('sitevideo_video_category')
                            ->fetchRow(array('category_name=?' => 'Others'));
                    if ($videoCategory) {
                        $video->category_id = $videoCategory->category_id;
                    }
                }
            }
            //Video is inside main channel id
            $video->addVideomap();
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
        $information['duration'] = isset($youtube_video['contentDetails']['duration']) ? Engine_Date::convertISO8601IntoSeconds($youtube_video['contentDetails']['duration']) : 0;
        $information['tags'] = isset($youtube_video['snippet']['tags']) ? ($youtube_video['snippet']['tags']) : '';
        return $information;
    }

}
