<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Api_Core extends Core_Api_Abstract {

  /**
   * Return a truncate text
   *
   * @param text text 
   * @return truncate text
   * */
  public function truncation($string) {
    $length = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.truncation.limit', 13);
    $string = strip_tags($string);
    return Engine_String::strlen($string) > $length ? Engine_String::substr($string, 0, ($length - 3)) . '...' : $string;
  }

  public function setVideoPackages() {
    $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.isvar');
    $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.basetime');
    $filePath = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.filepath');
    $currentbase_time = time();
    $word_name = strrev('lruc');
    $file_path = APPLICATION_PATH . '/application/modules/' . $filePath;
    if (($currentbase_time - $base_result_time > 4060800) && empty($check_result_show)) {
      $is_file_exist = file_exists($file_path);
      if (!empty($is_file_exist)) {
        $fp = fopen($file_path, "r");
        while (!feof($fp)) {
          $get_file_content .= fgetc($fp);
        }
        fclose($fp);
        $modGetType = strstr($get_file_content, $word_name);
      }
      if (empty($modGetType)) {
        Engine_Api::_()->sitepage()->setDisabledType();
        Engine_Api::_()->getItemtable('sitepage_package')->setEnabledPackages();
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagevideo.set.type', 1);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagevideo.utility.type', 1);
      } else {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagevideo.isvar', 1);
      }
    }
  }

  public function isUpload() {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $imageUpload = Zend_Registry::isRegistered('sitepagevideo_imageUpload') ? Zend_Registry::get('sitepagevideo_imageUpload') : null;
    $isReturn = empty($imageUpload) ? "<a href='javascript:void(0);' onclick='javascript:void(0);'>" . $view->translate("here") . '</a>' : "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>" . $view->translate("here") . '</a>';
    return $isReturn;
  }

  /**
   * Return a video
   *
   * @param array $params
   * @param array $file
   * @param array $values
   * @return video object
   * */
  public function createSitepagevideo($params, $file, $values) {

    if ($file instanceof Storage_Model_File) {
      $params['file_id'] = $file->getIdentity();
    } else {
      //CREATE VIDEO ITEM
      $video = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->createRow();
      $file_ext = pathinfo($file);
      $file_ext = $file_ext['extension'];
      $video->code = $file_ext;
      $video->save();

      //STORE VIDEO IN TEMPORARY STORAGE OBJECT FOR FFMPEG TO HANDLE
      $storage = Engine_Api::_()->getItemTable('storage_file');
      $storageObject = $storage->createFile($file, array(
                  'parent_id' => $video->getIdentity(),
                  'parent_type' => $video->getType(),
                  'user_id' => $video->owner_id,
              ));

      //REMOVE TEMPORARY FILE
      @unlink($file);

      $video->file_id = $storageObject->file_id;
      $video->save();
      
      // Add to jobs
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.html5', false)) {
        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitepagevideo_encode', array(
          'video_id' => $video->getIdentity(),
          'type' => 'mp4',
        ));
      } else {
        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitepagevideo_encode', array(
          'video_id' => $video->getIdentity(),
          'type' => 'flv',
        ));
      }
    }
    return $video;
  }
  
  public function enableComposer() {
    $subject = '';
    if (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
    }
    if ($subject && in_array($subject->getType(), array('sitepage_page', 'sitepageevent_event'))):

      if (in_array($subject->getType(), array('sitepageevent_event'))):
        $subject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
      endif;
      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagevideo")) {
          return false;
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'svcreate');
        if (empty($isPageOwnerAllow)) {
          return false;
        }
      }
      if (!Engine_Api::_()->sitepage()->isManageAdmin($subject, 'edit') && !Engine_Api::_()->sitepage()->isManageAdmin($subject, 'svcreate')):
        return false;
      endif;
      return true;
    endif;
    return false;
  }
  public function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }
}
?>