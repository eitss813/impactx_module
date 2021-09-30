<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Plugin_Stream
{

  public function onSitevodStreamCompleted($event)
  {
    $payload = $event->getPayload();
    if( $payload->parent_type != 'video' ) {
      return;
    }
    $video = $payload->getParent();
    if( empty($video) || $video->file_id !=  $payload->file_id) {
      return;
    }
    $storage_file = Engine_Api::_()->storage()->get($video->file_id, $video->getType());
    // Prepare information
    $owner = $video->getOwner();
    $video->code = $storage_file->extension;
    $video->duration = $payload->duration;
    $video->type = 'stream';
    $video->status = 1;
    $video->save();
    try {
      $frameUrl = $payload->getFrameUrl();
      if( !$video->photo_id && $frameUrl ) {
        $video->saveVideoThumbnail($frameUrl);
      }
    } catch( Exception $e ) {
      
    }
    // insert action in a separate transaction if video status is a success
    $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $db = $actionsTable->getAdapter();
    $db->beginTransaction();

    try {
// new action
      $chanel = $video->getChannelModel();
      $actionType = $chanel ? 'sitevideo_channel_video_new' : 'sitevideo_video_new';
      $actionObject = $chanel ? $chanel : $video;
      $action = $actionsTable->addActivity($owner, $actionObject, $actionType);

      if( $action ) {
        $actionsTable->attachActivity($action, $video);
      }

// notify the owner
      Engine_Api::_()->getDbtable('notifications', 'activity')
        ->addNotification($owner, $owner, $video, 'sitevideo_processed');
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e; // throw
    }
  }

}