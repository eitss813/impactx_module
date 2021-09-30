<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: VideoController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_VideoController extends Seaocore_Controller_Action_Standard {

  public function init() {

    //GET VIDEO ID AND VIDEO SUBJECT
    $video = null;
    $video_id = $this->_getParam('video_id', $this->_getParam('id', null));
    if ($video_id) {
      $video = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);
      if ($video) {
        Engine_Api::_()->core()->setSubject($video);
      }
    }

    //IF SUBJECT IS NOT SET THEN RETURN
    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }
  }

  //ACTION FOR EMBEDING THE VIDEO
  public function embedAction() {

    //GET SUBJECT
    $this->view->video = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');

    //CHECK THAT EMBEDDING IS ALLOWED OR NOT
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.embeds', 1)) {
      $this->view->error = 1;
      return;
    } else if (isset($video->allow_embed) && !$video->allow_embed) {
      $this->view->error = 2;
      return;
    }

    //GET EMBED CODE
    $this->view->embedCode = $video->getEmbedCode();
  }

  //ACTION FOR FETCHING THE VIDEO INFORMATION
  public function externalAction() {

    //GET SUBJECT
    $this->view->video = $video = Engine_Api::_()->core()->getSubject('sitepagevideo_video');

    //CHECK THAT EMBEDDING IS ALLOWED OR NOT
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.embeds', 1)) {
      $this->view->error = 1;
      return;
    } else if (isset($video->allow_embed) && !$video->allow_embed) {
      $this->view->error = 2;
      return;
    }

    //GET EMBED CODE
    $this->view->videoEmbedded = "";
    if ($video->status == 1) {
      $video->view_count++;
      $video->save();
      $this->view->videoEmbedded = $video->getRichContent(true);
    }

    //TRACK VIEWS FROM EXTERNAL SOURCES
    Engine_Api::_()->getDbtable('statistics', 'core')
            ->increment('video.embedviews');

    //GET FILE LOCATION
    if ($video->type == 3 && $video->status == 1) {
      if (!empty($video->file_id)) {
        $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
        if ($storage_file) {
          $this->view->video_location = $storage_file->map();
        }
      }
    }

    //GET RATING DATA
    $this->view->rating_count = Engine_Api::_()->getDbTable('ratings', 'sitepagevideo')->ratingCount($video->getIdentity());
  }

}
?>