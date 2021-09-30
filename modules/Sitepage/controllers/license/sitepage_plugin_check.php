<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitepage_plugin_version.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$db = $this->getDb();
$sitepage_version_correct = true;

$errorMsg = '';
$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

$finalModules = '';
$select = new Zend_Db_Select($db);
$select->from('engine4_core_modules', array('title', 'version'))
        ->where('name = ?', "sitepage")
        ->where('enabled = ?', 1);
$getModVersion = $select->query()->fetchObject();
if (!empty($getModVersion)) {
//$isModSupport = strcasecmp($getModVersion->version, $sitepage_plugin_version);
  $running_version = $getModVersion->version;
  $product_version = $sitepage_plugin_version;
  $shouldUpgrade = false;
  if (!empty($running_version) && !empty($product_version)) {
    $temp_running_verion_2 = $temp_product_verion_2 = 0;
    if (strstr($product_version, "p")) {
      $temp_starting_product_version_array = @explode("p", $product_version);
      $temp_product_verion_1 = $temp_starting_product_version_array[0];
      $temp_product_verion_2 = $temp_starting_product_version_array[1];
    } else {
      $temp_product_verion_1 = $product_version;
    }
    $temp_product_verion_1 = @str_replace(".", "", $temp_product_verion_1);


    if (strstr($running_version, "p")) {
      $temp_starting_running_version_array = @explode("p", $running_version);
      $temp_running_verion_1 = $temp_starting_running_version_array[0];
      $temp_running_verion_2 = $temp_starting_running_version_array[1];
    } else {
      $temp_running_verion_1 = $running_version;
    }
    $temp_running_verion_1 = @str_replace(".", "", $temp_running_verion_1);


    if (($temp_running_verion_1 < $temp_product_verion_1) || (($temp_running_verion_1 == $temp_product_verion_1) && ($temp_running_verion_2 < $temp_product_verion_2))) {
      $shouldUpgrade = true;
    }
  }
}

if (!empty($shouldUpgrade)) {
  $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the Directory / Pages Plugin. Please upgrade Directory / Pages Plugin on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Directory / Pages Plugin".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
  $sitepage_version_correct = false;
  return $this->_error($errorMsg);
}