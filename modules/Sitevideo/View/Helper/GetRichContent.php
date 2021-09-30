<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GetContent.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_View_Helper_GetRichContent extends Advancedactivity_View_Helper_GetRichContent {
    /**
     * Assembles action string
     *
     * @return string
     */
    public function getRichContent($item, $feedSettings = array()) {
        if (!$item || $item->getType() != 'video') {
            return parent::getRichContent($item);
        }

        $videoSize = array();
        $videoSize['thumb.normal'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
        $videoSize['thumb.large'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
        $videoSize['thumb.main'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $videoSize['width'] = 400;
        $thumbnailType = Engine_Api::_()->getApi('core', 'sitevideo')->findThumbnailType($videoSize, $videoSize['width']);
        $session = new Zend_Session_Namespace('mobile');
        $mobile = $session->mobile;
        $view = false;
        $params = array();

        // if video type is youtube
        if ($item->checkType('youtube')) {
            $videoEmbedded = $item->compileYouTube($item->video_id, $item->code, $view, $mobile);
        }
        // if video type is vimeo
        else if ($item->checkType('vimeo')) {
            $videoEmbedded = $item->compileVimeo($item->video_id, $item->code, $view, $mobile);
        }
        // if video type is dailymotion
        else if ($item->checkType('dailymotion')) {
            $videoEmbedded = $item->compileDailymotion($item->video_id, $item->code, $view, $mobile);
        }
        // if video type is iframely
        else if ($item->checkType('iframely')) {
            $videoEmbedded = $item->code;
        }
        // if video type is embedcode
        else if ($item->checkType('embedcode')) {
            $videoEmbedded = $item->compileOtherEmbedCode($item->video_id, $item->code, $view, $mobile);
        }
        // if video type is instagram
        else if ($item->checkType('instagram')) {
            $videoEmbedded = $item->compileInstagramEmbedCode($item->video_id, $item->code, $view, $mobile);
        }
        // if video type is twitter
        else if ($item->checkType('twitter')) {
            $videoEmbedded = $item->compileTwitterEmbedCode($item->video_id, $item->code, $view, $mobile);
        }
        // if video type is pinterest
        else if ($item->checkType('pinterest')) {
            $videoEmbedded = $item->compilePinterestEmbedCode($item->video_id, $item->code, $view, $mobile);
        }
        // if video type is stream
        else if ($item->checkType('stream') && Engine_Api::_()->sitevideo()->enableStreamVideo() ) {
          $storage_file = Engine_Api::_()->storage()->get($item->file_id, $item->getType());
          if( $storage_file && $storage_file->extension == 'm3u8') {
             $videoEmbedded = $item->compileHLSMedia($storage_file->getHref(), $view);
          }
          if( $storage_file && $storage_file->extension == 'mpd') {
             $videoEmbedded = $item->compileDASHMedia($storage_file->getHref(), $view);
          }
        }

        // if video type is upload
        if ($item->checkType('upload')) {
            $storage_file = Engine_Api::_()->storage()->get($item->file_id, $item->getType());
            if (empty($storage_file)) {
                return;
            }
            $video_location = $storage_file->getHref();
            if ($storage_file->extension === 'flv') {
                $videoEmbedded = $item->compileFlowPlayer($video_location, $view);
            } else {
                $videoEmbedded = $item->compileHTML5Media($video_location, $view);
            }
        }

        // $view == false means that this rich content is requested from the activity feed
        if ($view == false) {

            $video_duration = "";
            if ($item->duration) {
                if ($item->duration >= 3600) {
                    $duration = gmdate("H:i:s", $item->duration);
                } else {
                    $duration = gmdate("i:s", $item->duration);
                }
                //$duration = ltrim($duration, '0:');

                $video_duration = "<span class='video_length'>" . $duration . "</span>";
            }

            // prepare the thumbnail
            $thumb = Zend_Registry::get('Zend_View')->itemPhoto($item, $thumbnailType, null);

            if ($item->photo_id) {
                $thumb = Zend_Registry::get('Zend_View')->itemPhoto($item, $thumbnailType, null);
            } else {
                $thumb = '<img alt="" src="' . Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sitevideo/externals/images/video_default.png">';
            }

            if (!$mobile) {
                $thumb = '<a class="Sitevideo_thumb" id="video_thumb_' . $item->video_id . '" style="" href="' . $item->getHref() . '">
                  <div class="sitevideo_thumb_wrapper sitevideo_thumb_viewer"><span class="video_overlay"></span> <span class="play_icon"></span>' . $video_duration . $thumb . '</div>
                  </a>';
            } else {
                $thumb = '<a class="Sitevideo_thumb" id="video_thumb_' . $item->video_id . '" class="video_thumb" href="' . $item->getHref() . '">
                  <div class="sitevideo_thumb_wrapper sitevideo_thumb_viewer"><span class="video_overlay"></span> <span class="play_icon"></span>' . $video_duration . $thumb . '</div>
                  </a>';
            }

            // prepare title and description
            $title = "<div class='feed_item_link_title'><a class='sea_add_tooltip_link feed_video_title' rel= \"" . $item->getType() . ' ' . $item->getIdentity() . "\" href='" . $item->getHref($params) . "' >$item->title</a></div>";
            $tmpBody = strip_tags($item->description);
            $description = "<div class='sitevideo_desc'>" . (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody) . "</div>";

            $videoEmbedded = $thumb . '<div id="video_object_' . $item->video_id . '" class="video_object" style="display:none;">' . $videoEmbedded . '
            </div><div class="video_info">' . $title . $description . '</div>';
        }

        return $videoEmbedded;
    }

}
