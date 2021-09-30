<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_PlaylistPlayallController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->playlist = $playlist = Engine_Api::_()->core()->getSubject();
        if ($playlist->video_count<=0) {
            return $this->setNoRender();
        }
        $this->view->playlistOptions = $this->_getParam('playlistOptions', 0);
        $this->view->height = $this->_getParam('height', 540);
        $this->view->titleTruncation = $this->_getParam('titleTruncation', 35);
        if (empty($this->view->height)) {
            $this->view->height = 540;
        }
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $videoId = 0;
        if (isset($params['video_id'])) {
            $videoId = $params['video_id'];
        }
        $this->view->maps = $maps = $playlist->getPlaylistAllMap(array('orderby' => 'video_type'));
        $this->view->videosList = array();
        $this->view->currentPosition = 0;
        $c = 0;
        $this->view->isEmbedVideo = false;
        $this->view->isVideo = false;
        foreach ($maps as $map) {
            $video = $map->getVideoDetail();
            $return = true;
            if ($video->status != 1)
                continue;
            $code = $video->code;
            $ext = "";
            switch ($video->type) {
                case "youtube" :
                    $this->view->isVideo = true;
                    $url = "http://www.youtube.com/embed/{$code}?enablejsapi=1";
                    $type = "youtube";
                    $return = false;
                    break;
                case "vimeo" :
                    $this->view->isVideo = true;
                    $url = "https://player.vimeo.com/video/{$code}?api=1&player_id=player";
                    $type = "vimeo";
                    $return = false;
                    break;
                case "upload" :
                    if (!empty($video->file_id)) {
                        $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
                        if ($storage_file) {
                            $this->view->isVideo = true;
                            $url = $storage_file->map();
                            $ext = $storage_file->extension;
                            $type = 'mycomputer';
                            $return = false;
                        }
                    }
                    break;
                case "dailymotion" :
                    $this->view->isVideo = true;
                    $url = "";
                    $type = "dailymotion";
                    $return = false;
                    break;
                case "embedcode" :
                    $this->view->isEmbedVideo = true;
                    $url = $code;
                    $type = "other";
                    $return = false;
                    break;
                case "instagram" :
                    $this->view->isEmbedVideo = true;
                    $url = $video->getRichContent(true);
                    $type = "instagram";
                    $return = false;
                    break;
                case "twitter" :
                    $this->view->isEmbedVideo = true;
                    $url = $video->getRichContent(true);
                    $type = "twitter";
                    $return = false;
                    break;
                case "pinterest" :
                    $this->view->isEmbedVideo = true;
                    $url = $video->getRichContent(true);
                    $type = "pinterest";
                    $return = false;
                    break;
            }
            if ($return)
                continue;

            if ($video->video_id == $videoId) {
                $this->view->currentPosition = $c;
            }
            if ($video->main_channel_id) :
                $channel = $video->getChannelModel();
                if ($channel->file_id) :
                    $otherInfo = $this->view->htmlLink($channel->getHref(), $this->view->itemPhoto($channel, 'thumb.icon'), array('class' => 'author_img'));
                else :
                    $otherInfo = $this->view->htmlLink($channel->getHref(), $this->view->itemPhoto($channel, 'thumb.icon'), array('class' => 'author_img'));
                endif;
                $otherInfo .= $this->view->htmlLink($channel, $channel->getTitle(), array('class' => 'author_name'));
            else :
                $otherInfo = $this->view->htmlLink($video->getOwner()->getHref(), $this->view->itemPhoto($video->getOwner(), 'thumb.icon'), array('class' => 'author_img'));
                $otherInfo .= $this->view->htmlLink($video->getOwner(), $video->getOwner()->getTitle(), array('class' => 'author_name'));
            endif;
            $this->view->videosList[] = array(
                "url" => $url,
                "type" => $type,
                "id" => $code,
                "ext" => $ext,
                'duration' => $video->duration,
                "title" => $video->title,
                "description" => $this->view->string()->truncate($video->description, 200),
                "like_count" => $video->like_count,
                "favourite_count" => $video->favourite_count,
                "rating" => $video->rating,
                "comment_count" => $video->comment_count,
                "view_count" => $video->view_count,
                'vid' => $video->video_id,
                'posted_date' => "Posted on " . $this->view->locale()->toDate($video->creation_date, array('format' => 'MMMM')) . " " . $this->view->locale()->toDate($video->creation_date, array('format' => 'dd')),
                'otherInfo' => $otherInfo,
                'titleLink' => $this->view->htmlLink($video->getHref(), $video->getTitle()),
                'videoOwnerLink' => $this->view->htmlLink($video->getOwner()->getHref(), $video->getOwner()->getTitle()),
                'playallLink' => $this->view->url(array('module' => 'sitevideo', 'controller' => 'playlist', 'action' => 'playall', 'playlist_id' => $playlist->playlist_id, 'video_id' => $video->getIdentity()), 'sitevideo_playlist')
            );
            $c++;
        }
        $this->view->viewer_id = $viewer->getIdentity();
    }

}
