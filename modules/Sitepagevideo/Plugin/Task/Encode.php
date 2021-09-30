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
class Sitepagevideo_Plugin_Task_Encode extends Core_Plugin_Task_Abstract {

  public function getTotal() {
    $table = Engine_Api::_()->getDbTable('videos', 'sitepagevideo');
    return $table->select()
            ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
            ->where('status = ?', 0)
            ->query()
            ->fetchColumn(0)
    ;
  }

  public function execute() {
    // Check allowed jobs vs executing jobs
    // @todo this does not function correctly as the task system only allows
    // one to run at a time, unless encoding takes more than 15 minutes anyway
    $sitepagevideoTable = Engine_Api::_()->getItemTable('sitepagevideo_video');
    $maxAllowedJobs = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.jobs', 2);
    $currentlyEncodingCount = $sitepagevideoTable
                    ->select()
                    ->from($sitepagevideoTable->info('name'), new Zend_Db_Expr('COUNT(*)'))
                    ->where('status = ?', 2)
                    ->query()
                    ->fetchColumn(0)
    ;

    // Let's run some more
    $startedCount = 0;
    if ($currentlyEncodingCount < $maxAllowedJobs) {
      //for( $i = $currentlyEncodingCount + 1, $l = $maxAllowedJobs; $i <= $l; $i++ ) {
      $sitepagevideoSelect = $sitepagevideoTable->select()
                      ->where('status = ?', 0)
                      ->order('video_id ASC')
                      ->limit(1)
      ;
      $sitepagevideo = $sitepagevideoTable->fetchRow($sitepagevideoSelect);
      if ($sitepagevideo instanceof Sitepagevideo_Model_Sitepagevideo) {
        $startedCount++;
        $this->_process($sitepagevideo);
      }
      //}
    }

    // We didn't do anything
    if ($startedCount <= 0) {
      $this->_setWasIdle();
    }
  }

  protected function _process($sitepagevideo) {
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
            DIRECTORY_SEPARATOR . 'sitepagevideo';
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
    if (is_numeric($sitepagevideo)) {
      $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);
    }

    if (!($sitepagevideo instanceof Sitepagevideo_Model_Sitepagevideo)) {
      $error_msg6 = Zend_Registry::get('Zend_Translate')->_('Argument was not a valid video');
      throw new Sitepagevideo_Model_Exception($error_msg6);
    }

    // Update to encoding status
    $sitepagevideo->status = 2;
    $sitepagevideo->save();

    // Prepare information
    $owner = $sitepagevideo->getOwner();
    $filetype = $sitepagevideo->code;

    $originalPath = $tmpDir . DIRECTORY_SEPARATOR . $sitepagevideo->getIdentity() . '.' . $filetype;
    $outputPath = $tmpDir . DIRECTORY_SEPARATOR . $sitepagevideo->getIdentity() . '_vconverted.flv';
    $thumbPath = $tmpDir . DIRECTORY_SEPARATOR . $sitepagevideo->getIdentity() . '_vthumb.jpg';

    $sitepagevideoCommand = $ffmpeg_path . ' '
            . '-i ' . escapeshellarg($originalPath) . ' '
            . '-ab 64k' . ' '
            . '-ar 44100' . ' '
            . '-qscale 5' . ' '
            . '-vcodec flv' . ' '
            . '-f flv' . ' '
            . '-r 25' . ' '
            . '-s 480x386' . ' '
            . '-v 2' . ' '
            . '-y ' . escapeshellarg($outputPath) . ' '
            . '2>&1'
    ;

    $thumbCommand = $ffmpeg_path . ' '
            . '-i ' . escapeshellarg($outputPath) . ' '
            . '-f image2' . ' '
            . '-ss 4.00' . ' '
            . '-v 2' . ' '
            . '-y ' . escapeshellarg($thumbPath) . ' '
            . '2>&1'
    ;

    // Prepare output header
    $output = PHP_EOL;
    $output .= $originalPath . PHP_EOL;
    $output .= $outputPath . PHP_EOL;
    $output .= $thumbPath . PHP_EOL;

    // Prepare logger
    $log = null;
    //if( APPLICATION_ENV == 'development' ) {
    $log = new Zend_Log();
    $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/sitepagevideo.log'));
    //}
    // Execute sitepagevideo encode command
    $sitepagevideoOutput = $output .
            $sitepagevideoCommand . PHP_EOL .
            shell_exec($sitepagevideoCommand);

    // Log
    if ($log) {
      $log->log($sitepagevideoOutput, Zend_Log::INFO);
    }

    // Check for failure
    $success = true;

    // Unsupported format
    if (preg_match('/Unknown format/i', $sitepagevideoOutput) ||
            preg_match('/Unsupported codec/i', $sitepagevideoOutput) ||
            preg_match('/patch welcome/i', $sitepagevideoOutput) ||
            !is_file($outputPath) ||
            filesize($outputPath) <= 0) {
      $success = false;
      $sitepagevideo->status = 3;
    }

    // This is for audio files
    else if (preg_match('/sitepagevideo:0kB/i', $sitepagevideoOutput)) {
      $success = false;
      $sitepagevideo->status = 5;
    }

    // Failure
    if (!$success) {

      $db = $sitepagevideo->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $sitepagevideo->save();

        // notify the owner
        $translate = Zend_Registry::get('Zend_Translate');
        $language = (!empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
        $notificationMessage = '';
        if ($sitepagevideo->status == 3) {
          $notificationMessage = $translate->translate(sprintf(
                                  'Video conversion failed. Sitepagevideo format is not supported by FFMPEG. Please try %1$sagain%2$s.', '', ''
                          ), $language);
        } else if ($sitepagevideo->status == 5) {
          $notificationMessage = $translate->translate(sprintf(
                                  'SVideo conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '', ''
                          ), $language);
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($owner, $owner, $sitepagevideo, 'sitepagevideo_processed_failed', array(
                    'message' => $notificationMessage,
                    'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view'), 'sitepagevideo_general', true),
                ));

        $db->commit();
      } catch (Exception $e) {
        $sitepagevideoOutput .= PHP_EOL . $e->__toString() . PHP_EOL;
        if ($log) {
          $log->write($e->__toString(), Zend_Log::ERR);
        }
        $db->rollBack();
      }

      // Write to additional log in dev
      if (APPLICATION_ENV == 'development') {
        file_put_contents($tmpDir . '/' . $sitepagevideo->video_id . '.txt', $sitepagevideoOutput);
      }
    }

    // Success
    else {
      // Get duration of the sitepagevideo to caculate where to get the thumbnail
      if (preg_match('/Duration:\s+(.*?)[.]/i', $sitepagevideoOutput, $matches)) {
        list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
        $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
      } else {
        $duration = 0; // Hmm
      }

      // Log duration
      if ($log) {
        $log->log('Duration: ' . $duration, Zend_Log::INFO);
      }

      // Process thumbnail
      $thumbOutput = $output .
              $thumbCommand . PHP_EOL .
              shell_exec($thumbCommand);

      // Log thumb output
      if ($log) {
        $log->log($thumbOutput, Zend_Log::INFO);
      }

      // Resize thumbnail
      $image = Engine_Image::factory();
      $image->open($thumbPath)
              ->resize(120, 240)
              ->write($thumbPath)
              ->destroy();

      // Save sitepagevideo and thumbnail to storage system
      $params = array(
          'parent_id' => $sitepagevideo->getIdentity(),
          'parent_type' => $sitepagevideo->getType(),
          'user_id' => $sitepagevideo->owner_id
      );

      $db = $sitepagevideo->getTable()->getAdapter();
      $db->beginTransaction();

      try {
        $sitepagevideoFileRow = Engine_Api::_()->storage()->create($outputPath, $params);
        $thumbFileRow = Engine_Api::_()->storage()->create($thumbPath, $params);

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();

        // delete the files from temp dir
        unlink($originalPath);
        unlink($outputPath);
        unlink($thumbPath);

        $sitepagevideo->status = 7;
        $sitepagevideo->save();

        // notify the owner
        $translate = Zend_Registry::get('Zend_Translate');
        $notificationMessage = '';
        $language = (!empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
        if ($sitepagevideo->status == 7) {
          $notificationMessage = $translate->translate(sprintf(
                                  'Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '', ''
                          ), $language);
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($owner, $owner, $sitepagevideo, 'sitepagevideo_processed_failed', array(
                    'message' => $notificationMessage,
                    'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view'), 'sitepagevideo_general', true),
                ));

        throw $e; // throw
      }

      // Sitepagevideo processing was a success!
      // Save the information
      $sitepagevideo->file_id = $sitepagevideoFileRow->file_id;
      $sitepagevideo->photo_id = $thumbFileRow->file_id;
      $sitepagevideo->duration = $duration;
      $sitepagevideo->status = 1;
      $sitepagevideo->save();

      // delete the files from temp dir
      unlink($originalPath);
      unlink($outputPath);
      unlink($thumbPath);

      // insert action in a seperate transaction if sitepagevideo status is a success
      $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
      $db = $actionsTable->getAdapter();
      $db->beginTransaction();

      try {
        // new action
        $action = $actionsTable->addActivity($owner, $sitepagevideo, 'sitepagevideo_new');
        if ($action) {
          $actionsTable->attachActivity($action, $sitepagevideo);
        }

        // notify the owner
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($owner, $owner, $sitepagevideo, 'sitepagevideo_processed');

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e; // throw
      }
    }
  }

}
?>