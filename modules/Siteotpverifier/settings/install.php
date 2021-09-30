<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2017-03-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteotpverifier_Installer extends Engine_Package_Installer_Module
{
  public function onPreInstall()
  {
    parent::onPreInstall();
  }

  public function onInstall()
  {
    $this->_phoneNumberManagePage();
    $this->_addSignupProcess();
    parent::onInstall();
  }
  public function onDisable() {
    $db = $this->getDb();
    $db->delete('engine4_user_signup', array('class = ?' => 'Siteotpverifier_Plugin_Signup_Otpverify'));
    parent::onDisable();
  }

  public function onEnable() {
    $db = $this->getDb();
    $isQueryExist = $db->query("SELECT * FROM `engine4_user_signup` WHERE `class` LIKE 'Siteotpverifier_Plugin_Signup_Otpverify' LIMIT 1")->fetch();
    if(empty($isQueryExist)){
    $db->query('INSERT INTO `engine4_user_signup` (`class` ) VALUES ("Siteotpverifier_Plugin_Signup_Otpverify"); ');
    }
    parent::onEnable();
  }

  protected function _addSignupProcess()
  {
    $db = $this->getDb();
    $otpverifyAdded = $db->query("SELECT * FROM  `engine4_user_signup` WHERE  `class` Like  'Siteotpverifier_Plugin_Signup_Otpverify'")->fetch();
    if( !empty($otpverifyAdded) ) {
      return;
    }
    $result = $db->query("SELECT * FROM  `engine4_user_signup` WHERE  `class` Like  '%_Plugin_Signup_Account'")->fetch();
    $order = !empty($result) ? $result['order'] + 1 : 3;
    $db->query("INSERT IGNORE INTO `engine4_user_signup` (`class`, `order`, `enable`) VALUES ( 'Siteotpverifier_Plugin_Signup_Otpverify', '" . $order . "', '1')");
  }

  protected function _isTableExists($tableName)
  {
    $db = $this->getDb();
    $isTableExists = $db->query("SHOW TABLES LIKE '$tableName'")->fetch();
    return !empty($isTableExists);
  }

  protected function _isColumnExists($tableName, $columnName)
  {
    $db = $this->getDb();
    $isColumnExists = $db->query("SHOW COLUMNS FROM $tableName LIKE '$columnName'")->fetch();
    return !empty($isColumnExists);
  }

  protected function _phoneNumberManagePage()
  {

    $db = $this->getDb();
    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'siteotpverifier_auth_verification')
      ->limit(1)
      ->query()
      ->fetchColumn();

    // insert if it doesn't exist yet
    if( !$page_id ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'siteotpverifier_auth_verification',
        'displayname' => 'OTP Verifier - Phone Number Details Page',
        'title' => 'Phone Number Details Page',
        'description' => 'This page allows user to get details of their phone number.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();

      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();

      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'user.settings-menu',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
        'params' => '{"title":"","name":"user.settings-menu"}',
      ));
      // Insert left widgets
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
        'params' => '{"title":"","name":"core.content"}',
      ));
    }

    return $this;
  }

  protected function _checkDeependency()
  {
    return $this->_checkDeependencyVersion();
  }

  protected function _checkDeependencyVersion()
  {
    $db = $this->getDb();

    $errorMsg = '';
    $finalModules = $getResultArray = array();
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $modArray = $this->_deependencyVersion;

    foreach( $modArray as $key => $value ) {
      $isMod = $db->query("SELECT * FROM  `engine4_core_modules` WHERE  `name` LIKE  '" . $key . "'")->fetch();
      if( !empty($isMod) && !empty($isMod['version']) ) {
        $isModSupport = $this->_compareDependancyVersion($isMod['version'], $value);
        if( empty($isModSupport) ) {
          $finalModules['modName'] = $key;
          $finalModules['title'] = $isMod['title'];
          $finalModules['versionRequired'] = $value;
          $finalModules['versionUse'] = $isMod['version'];
          $getResultArray[] = $finalModules;
        }
      }
    }

    foreach( $getResultArray as $modArray ) {
      $errorMsg .= '<div class="tip"><span>Note: Your website does not have the latest version of "' . $modArray['title'] . '". Please upgrade "' . $modArray['title'] . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with this plugin.<br/> Please <a href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
    }
    return $errorMsg;
  }

  private function _compareDependancyVersion($databaseVersion, $checkDependancyVersion)
  {
    $f = $databaseVersion;
    $s = $checkDependancyVersion;
    if( strcasecmp($f, $s) == 0 )
      return -1;

    $fArr = explode(".", $f);
    $sArr = explode('.', $s);
    if( count($fArr) <= count($sArr) )
      $count = count($fArr);
    else
      $count = count($sArr);

    for( $i = 0; $i < $count; $i++ ) {
      $fValue = $fArr[$i];
      $sValue = $sArr[$i];
      if( is_numeric($fValue) && is_numeric($sValue) ) {
        if( $fValue > $sValue )
          return 1;
        elseif( $fValue < $sValue )
          return 0;
        else {
          if( ($i + 1) == $count ) {
            return -1;
          } else
            continue;
        }
      }
      elseif( is_string($fValue) && is_numeric($sValue) ) {
        $fsArr = explode("p", $fValue);

        if( $fsArr[0] > $sValue )
          return 1;
        elseif( $fsArr[0] < $sValue )
          return 0;
        else {
          return 1;
        }
      } elseif( is_numeric($fValue) && is_string($sValue) ) {
        $ssArr = explode("p", $sValue);

        if( $fValue > $ssArr[0] )
          return 1;
        elseif( $fValue < $ssArr[0] )
          return 0;
        else {
          return 0;
        }
      } elseif( is_string($fValue) && is_string($sValue) ) {
        $fsArr = explode("p", $fValue);
        $ssArr = explode("p", $sValue);
        if( $fsArr[0] > $ssArr[0] )
          return 1;
        elseif( $fsArr[0] < $ssArr[0] )
          return 0;
        else {
          if( $fsArr[1] > $ssArr[1] )
            return 1;
          elseif( $fsArr[1] < $ssArr[1] )
            return 0;
          else {
            return -1;
          }
        }
      }
    }
  }

}
