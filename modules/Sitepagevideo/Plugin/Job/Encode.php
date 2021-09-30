<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Encode.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Plugin_Job_Encode extends Core_Plugin_Job_Abstract {

  protected function _execute() {
    // Get job and params
    $job = $this->getJob();

    // No video id?
    if (!($video_id = $this->getParam('video_id'))) {
      $this->_setState('failed', 'No video identity provided.');
      $this->_setWasIdle();
      return;
    }

    // Get video object
    $video = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);
    if (!$video || !($video instanceof Sitepagevideo_Model_Video)) {
      $this->_setState('failed', 'Video is missing.');
      $this->_setWasIdle();
      return;
    }

    // Check video status
    if (0 != $video->status) {
      $this->_setState('failed', 'Video has already been encoded, or has already failed encoding.');
      $this->_setWasIdle();
      return;
    }

    $type = $this->getParam('type');
    $type = empty($type) ? 'flv' : $this->getParam('type');
    
    // Process
    try {
      $this->_process($video, $type);
      $this->_setIsComplete(true);
    } catch( Exception $e ) {
      $this->_setState('failed', 'Exception: ' . $e->getMessage());
      
      // Attempt to set video state to failed
      try {
        if( 1 != $video->status ) {
          $video->status = 3;
          $video->save();
        }
      } catch( Exception $e ) {
        $this->_addMessage($e->getMessage());
      }
    }
  }

  protected function _process($video, $type, $compatibilityMode = false){
    // Make sure FFMPEG path is set
    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitepagevideo_ffmpeg_path;
    if (!$ffmpeg_path) {
      $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Ffmpeg not configured');
      throw new Sitepagevideo_Model_Exception($error_msg1);
    }
    // Make sure FFMPEG can be run
    if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path)) {
      $output = null;
      $return = null;
      exec($ffmpeg_path . ' -version', $output, $return);
      if ($return > 0) {
        $error_msg2 = Zend_Registry::get('Zend_Translate')->_('Ffmpeg found, but is not executable');
        throw new Sitepagevideo_Model_Exception($error_msg2);
      }
    }

    // Check we can execute
    if (!function_exists('shell_exec')) {
      $error_msg3 = Zend_Registry::get('Zend_Translate')->_('Unable to execute shell commands using shell_exec(); the function is disabled.');
      throw new Sitepagevideo_Model_Exception($error_msg3);
    }

    // Check the video temporary directory
    $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' .
            DIRECTORY_SEPARATOR . 'video';
    if (!is_dir($tmpDir)) {
      if (!mkdir($tmpDir, 0777, true)) {
        $error_msg4 = Zend_Registry::get('Zend_Translate')->_('Video temporary directory did not exist and could not be created.');
        throw new Sitepagevideo_Model_Exception($error_msg4);
      }
    }
    if (!is_writable($tmpDir)) {
      $error_msg5 = Zend_Registry::get('Zend_Translate')->_('Video temporary directory is not writable.');
      throw new Sitepagevideo_Model_Exception($error_msg5);
    }

    // Get the video object
    if (is_numeric($video)) {
      $video = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);
    }

    if (!($video instanceof Sitepagevideo_Model_Video)) {
      $error_msg6 = Zend_Registry::get('Zend_Translate')->_('Argument was not a valid video');
      throw new Sitepagevideo_Model_Exception($error_msg6);
    }

    // Update to encoding status
    $video->status = 2;
    $video->type = 3;
    $video->save();

    // Prepare information
    $owner = $video->getOwner();
    $filetype = $video->code;

    // Pull video from storage system for encoding
    $storageObject = Engine_Api::_()->getItem('storage_file', $video->file_id);
    if( !$storageObject ) {
      throw new Video_Model_Exception('Video storage file was missing');
    }

    $originalPath = $storageObject->temporary();
    if( !file_exists($originalPath) ) {
      throw new Video_Model_Exception('Could not pull to temporary file');
    }

    $outputPath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vconverted.' . $type;
    $thumbPath = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vthumb.jpg';
    
    $videoCommand = $ffmpeg_path . ' '
      . '-i ' . escapeshellarg($originalPath) . ' '
      . '-ab 64k' . ' '
      . '-ar 44100' . ' '
      . '-qscale 5' . ' '
      . '-r 25' . ' ';

    if ( $type == 'mp4' ){
      $videoCommand .= '-vcodec libx264' . ' '
      . '-acodec aac' . ' '
      . '-strict experimental' . ' '
      . '-preset veryfast' . ' '
      . '-f mp4' . ' '
      ;
    }else{
      $videoCommand .= '-vcodec flv -f flv ';
    }

    $videoCommand .=
      '-y ' . escapeshellarg($outputPath) . ' '
      . '2>&1';

    // Prepare output header
    $output = PHP_EOL;
    $output .= $originalPath . PHP_EOL;
    $output .= $outputPath . PHP_EOL;
    $output .= $thumbPath . PHP_EOL;

    // Prepare logger
    $log = null;
    //if( APPLICATION_ENV == 'development' ) {
    $log = new Zend_Log();
    $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/video.log'));
    //}
    // Execute video encode command
    $videoOutput = $output .
            $videoCommand . PHP_EOL .
            shell_exec($videoCommand);

    // Log
    if ($log) {
      $log->log($videoOutput, Zend_Log::INFO);
    }

    // Check for failure
    $success = true;

    // Unsupported format
    if (preg_match('/Unknown format/i', $videoOutput) ||
            preg_match('/Unsupported codec/i', $videoOutput) ||
            preg_match('/patch welcome/i', $videoOutput) ||
            preg_match('/Audio encoding failed/i', $videoOutput) ||
            !is_file($outputPath) ||
            filesize($outputPath) <= 0) {
      $success = false;
      $video->status = 3;
    }

    // This is for audio files
    else if (preg_match('/video:0kB/i', $videoOutput)) {
      $success = false;
      $video->status = 5;
    }

    // Failure
    if (!$success) {
       if (!$compatibilityMode) {
        $this->_process($video, true);
        return;
      }
      
      $exceptionMessage = '';

      $db = $video->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $video->save();


        // notify the owner
        $translate = Zend_Registry::get('Zend_Translate');
        $language = (!empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
        $notificationMessage = '';

        if ($video->status == 3) {
          $exceptionMessage = Zend_Registry::get('Zend_Translate')->_('Video format is not supported by FFMPEG.');
          $notificationMessage = $translate->translate(sprintf(
                                  'Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '', ''
                          ), $language);
        } else if ($video->status == 5) {
          $exceptionMessage = Zend_Registry::get('Zend_Translate')->_('Audio-only files are not supported.');
          $notificationMessage = $translate->translate(sprintf(
                                  'Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '', ''
                          ), $language);
        } else if( $video->status == 7 ) {
      $notificationMessage = 'Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.';
        } else {
          $exceptionMessage = Zend_Registry::get('Zend_Translate')->_('Unknown encoding error.');
        }

        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($owner, $owner, $video, 'sitepagevideo_processed_failed', array(
                    'message' => $notificationMessage,
                    'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view'), 'sitepagevideo_general', true),
                ));

        $db->commit();
      } catch (Exception $e) {
        $videoOutput .= PHP_EOL . $e->__toString() . PHP_EOL;
        if ($log) {
          $log->write($e->__toString(), Zend_Log::ERR);
        }
        $db->rollBack();
      }

      // Write to additional log in dev
      if (APPLICATION_ENV == 'development') {
        file_put_contents($tmpDir . '/' . $video->video_id . '.txt', $videoOutput);
      }

      throw new Sitepagevideo_Model_Exception($exceptionMessage);
    }

    // Success
    else {
      // Get duration of the video to caculate where to get the thumbnail
      if (preg_match('/Duration:\s+(.*?)[.]/i', $videoOutput, $matches)) {
        list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
        $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
      } else {
        $duration = 0; // Hmm
      }

      // Log duration
      if ($log) {
        $log->log('Duration: ' . $duration, Zend_Log::INFO);
      }

      // Fetch where to take the thumbnail
      $thumb_splice = $duration / 2;

      // Thumbnail proccess command
      $thumbCommand = $ffmpeg_path . ' '
              . '-i ' . escapeshellarg($outputPath) . ' '
              . '-f image2' . ' '
              . '-ss ' . $thumb_splice . ' '
              . '-vframes 1' . ' '
              . '-v 2' . ' '
              . '-y ' . escapeshellarg($thumbPath) . ' '
              . '2>&1'
      ;

      // Process thumbnail
      $thumbOutput = $output .
              $thumbCommand . PHP_EOL .
              shell_exec($thumbCommand);

      // Log thumb output
      if ($log) {
        $log->log($thumbOutput, Zend_Log::INFO);
      }

      // Check output message for success
      $thumbSuccess = true;
      if (preg_match('/video:0kB/i', $thumbOutput)) {
        $thumbSuccess = false;
      }

      // Resize thumbnail
      if ($thumbSuccess) {
        $image = Engine_Image::factory();
        $image->open($thumbPath)
                ->resize(120, 240)
                ->write($thumbPath)
                ->destroy();
      }

      // Save video and thumbnail to storage system
      $params = array(
          'parent_id' => $video->getIdentity(),
          'parent_type' => $video->getType(),
          'user_id' => $video->owner_id
      );

      $db = $video->getTable()->getAdapter();
      $db->beginTransaction();

      try {
        $storageObject->setFromArray($params);
        $storageObject->store($outputPath);
        // $videoFileRow = Engine_Api::_()->storage()->create($outputPath, $params);
        if ($thumbSuccess) {
          $thumbFileRow = Engine_Api::_()->storage()->create($thumbPath, $params);
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();

        // delete the files from temp dir
        unlink($originalPath);
        unlink($outputPath);
        if ($thumbSuccess) {
          unlink($thumbPath);
        }

        $video->status = 7;
        $video->save();

        // notify the owner
        $translate = Zend_Registry::get('Zend_Translate');
        $notificationMessage = '';
        $language = (!empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
        if ($video->status == 7) {
          $notificationMessage = $translate->translate(sprintf(
                                  'Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '', ''
                          ), $language);
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($owner, $owner, $video, 'sitepagevideo_processed_failed', array(
                    'message' => $notificationMessage,
                    'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view'), 'sitepagevideo_general', true),
                ));

        throw $e; // throw
      }

      // Video processing was a success!
      // Save the information
      // $video->file_id = $videoFileRow->file_id;
      if ($thumbSuccess) {
        $video->photo_id = $thumbFileRow->file_id;
      }
      $video->duration = $duration;
      $video->status = 1;
      $video->save();

      // delete the files from temp dir
      unlink($originalPath);
      unlink($outputPath);
      unlink($thumbPath);

      // insert action in a seperate transaction if video status is a success
      $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
      $db = $actionsTable->getAdapter();
      $db->beginTransaction();

      try {
        // new action
        $subject = Engine_Api::_()->getItem('sitepage_page', $video->page_id);
        $action = $actionsTable->addActivity($owner, $subject, 'sitepagevideo_new');
        if ($action) {
          $actionsTable->attachActivity($action, $video);
        }

        // notify the owner
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($owner, $owner, $video, 'sitepagevideo_processed');

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e; // throw
      }
    }
  }
  
}