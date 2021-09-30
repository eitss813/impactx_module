<?php

/**
 * SocialEngine
 *
 * @category   Application_Module
 * @package    Siteuseravatar
 * @copyright  Copyright 2017-2018 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminSettingsController.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteuseravatar_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //TABS CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteuseravatar_admin_main', array(), 'siteuseravatar_admin_main_global');

    $this->view->form = $form = new Siteuseravatar_Form_Admin_Settings_Global();
    $values = array();

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
      foreach( $values as $key => $value ) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteuseravatar_' . $key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $userIds = $db->select()
      ->from('engine4_users', 'user_id')
      ->where('photo_id = ?', 0)
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);

    $this->view->noPhotosUsers = $userIds;
  }

  public function addAvatarAction()
  {
    ini_set('max_execution_time', 0);
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $userIds = $db->select()
      ->from('engine4_users', 'user_id')
      ->where('photo_id = ?', 0)
      ->limit(100)
      ->order('user_id DESC')
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);
    foreach( $userIds as $userId ) {
      $db->beginTransaction();
      try {
        Engine_Api::_()->siteuseravatar()->setDefaultAvatar($userId);
        $db->commit();
      } catch( Exception $ex ) {
        $db->rollback();
        continue;
      }
    }
    return $this->_helper->redirector->gotoRoute(array('module' => 'siteuseravatar', 'controller' => 'settings', 'action' => 'index'), 'admin_default', true);
  }

  public function previewAction()
  {
    //TABS CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteuseravatar_admin_main', array(), 'siteuseravatar_admin_main_global');

    $this->view->form = $form = new Siteuseravatar_Form_Admin_Settings_Preview();
    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) ) {
      $this->view->needToSubmit = true;
    }
    $values = $form->getValues();
    $this->view->imageSRC = Engine_Api::_()->siteuseravatar()->genrateDefaultAvatar($values['name'], 'toBase64', $values);
  }

  public function downloadAction()
  {
    $fileUrl = base64_decode('aHR0cHM6Ly93d3cuc29jaWFsZW5naW5lYWRkb25zLmNvbS9Tb2NpYWxFbmdpbmUvU29jaWFsZW5naW5lTW9kdWxlcy9TaXRldXNlcmF2YXRhci9TaXRldXNlcmF2YXRhci56aXA=');
    set_time_limit(0);
    $newfilenameArray = explode('/', trim(parse_url($fileUrl, PHP_URL_PATH), '/'));
    $newfilename = end($newfilenameArray);
    $local_path = str_replace('/', DS, APPLICATION_PATH_PUB . '/temporary/');
    $path = $local_path . $newfilename;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fileUrl);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    ob_start();
    $result = curl_exec($ch);
    if( empty($result) ) {
      $result = file_get_contents($fileUrl);
    }
    curl_close($ch);
    ob_end_clean();
    if( !empty($result) && !strstr(substr($result, 0, 50), 'error') ) {

      $file = fopen($path, 'wb');
      chmod($path, 0777);
      $result = fwrite($file, $result);
      fclose($file);
      // Extract
      $archive = new Archive_Zip($path);
      $rval = $archive->extract(array('add_path' => APPLICATION_PATH_PUB));
      if( $archive->errorCode() > 1 ) {
        throw new Engine_Package_Exception('Error in archive: ' . $archive->errorInfo());
      }
      $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(APPLICATION_PATH_PUB . '/Siteuseravatar'), RecursiveIteratorIterator::SELF_FIRST);
      foreach( $iterator as $item ) {
        @chmod($item, 0777);
      }
      @unlink($path);
    }
    return $this->_helper->redirector->gotoRoute(array('module' => 'siteuseravatar', 'controller' => 'settings', 'action' => 'index'), 'admin_default', true);
  }

  function faqAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('siteuseravatar_admin_main', array(), 'siteuseravatar_admin_main_faq');
  }

  function chmodRecursive($path, $filemode)
  {
    if( !is_dir($path) ) {
      return chmod($path, $filemode);
    }
    $dh = opendir($path);
    while( $file = readdir($dh) ) {
      if( $file != '.' && $file != '..' ) {
        $fullpath = $path . '/' . $file;
        if( !is_dir($fullpath) ) {
          if( !chmod($fullpath, $filemode) ) {
            return false;
          }
        } else {
          if( !$this->chmodRecursive($fullpath, $filemode) ) {
            return false;
          }
        }
      }
    }

    closedir($dh);
    return chmod($path, $filemode);
  }

}

?>