<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitemodule_install.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class SiteModule_Installer extends Engine_Package_Installer_Module
{

  protected $_urls = array();

  
  public function init()
  {
    $this->_urls = array(
      'after_install_configure' => $this->_name . '/install/post-install',
    );
  }

  public function onPreInstall()
  {
    $errorMsg = $this->_checkDeependency();
    if( !empty($errorMsg) ) {
      $this->_error($errorMsg);
      return;
    }
    parent::onPreInstall();
  }

  public function onInstall()
  {
    parent::onInstall();
  }

  public function onPostInstall()
  {
    $this->configureAfterInstall();
  }

  protected function configureAfterInstall()
  {
    if( $this->_databaseOperationType != 'install' || empty($this->_urls['after_install_configure']) ) {
      return;
    }

    $token = md5(Engine_String::str_random(15));
    $db = $this->getDb();
    $name = $this->_name . ".install.ssotoken";
    $db->query("DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = '$name'");
    $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('$name', '$token');");
    $this->_triggerCurlPostRequest($this->_urls['after_install_configure'], array('key' => $token));
  }

  public function _triggerCurlPostRequest($uri, $postOptions)
  {
    // get general config
    $generalConfig = array();
    if( file_exists(APPLICATION_PATH . DS . 'application' . DS . 'settings' . DS . 'general.php') ) {
      $generalConfig = include APPLICATION_PATH . DS . 'application' . DS . 'settings' . DS . 'general.php';
    }
    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $prefix = str_replace('/install', '', $baseUrl);
    $url = $request->getScheme() . '://' . $request->getHttpHost() . $prefix . '/' . $uri;
    $curloptions = array();
    if( !empty($generalConfig['maintenance']['code']) ) {
      $curloptions[CURLOPT_COOKIE] = 'en4_maint_code=' . $generalConfig['maintenance']['code'];
    }
    // Try to handle basic htauth
    if( !empty($request->getServer('PHP_AUTH_USER')) && !empty($request->getServer('PHP_AUTH_PW')) ) {
      $curloptions[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
      $curloptions[CURLOPT_USERPWD] = $request->getServer('PHP_AUTH_USER') . ':' . $request->getServer('PHP_AUTH_PW');
    }
    // If SSL is enabled
    if( $request->getScheme() == 'https://' ) {
      $curloptions[CURLOPT_VERBOSE] = true;
      $curloptions[CURLOPT_SSL_VERIFYPEER] = false;
    }
    $client = new Zend_Http_Client();
    $client->setConfig(array('timeout' => 6000, 'adapter' => 'Zend_Http_Client_Adapter_Curl', 'curloptions' => $curloptions))
      ->setUri($url)
      ->setMethod(Zend_Http_Client::POST)
      ->setParameterPost($postOptions);
    $response = $client->request();
    $responseData = $response->getBody();
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