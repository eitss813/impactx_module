<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
require_once realpath(dirname(__FILE__)) . '/sitemodule_install.php';

class Sitecoretheme_Installer extends SiteModule_Installer {
  /* If client are used the below plugins then need to check for upgrade */

  protected $_deependencyVersion = array(
    'advancedactivity' => '4.10.3p15',
    'sitead' => '4.10.3p2',
    'seaocore' => '4.10.3p52',
    'sitebanner' => '4.10.3p2',
    'sitemenu' => '4.10.3p19',
    'sitealbum' => '4.10.3p7',
    'sitemember' => '4.10.3p29',
    'sitepage' => '4.10.3p35',
    'siteadvsearch' => '4.10.3p2',
    'sitereview' => '4.10.3p4',
    'siteevent' => '4.10.3p14',
    'siteemailverification' => '4.10.3p3',
    'siteotpverifier' => '4.10.3p4',
    'sitecontentcoverphoto' => '4.10.3p1',
    'sitelogin' => '4.10.3p3',
    'sitepushnotification' => '4.10.3p6',
    'siteshare' => '4.10.3p2',
    'siteusercoverphoto' => '4.10.3p4',
    'userconnection' => '4.10.3',
    'sitemulticurrency' => '4.10.3p7',
    'sitenewsletter' => '4.10.3p7',
    'sitevideo' => '4.10.3p9',
  );
  protected $_installConfig = array(
    'sku' => 'sitecoretheme',
  );

  public function onInstall() {
    $db = $this->getDb();

    $this->_createCustomizationFile("/application/themes/sitecoretheme/customization.css", '/* ADD CUSTOM STYLE */');
    $this->_createCustomizationFile("/public/seaocore_themes/sitecorethemeThemeGeneralConstants.css", '/* EDIT CONSTANTS, DO NOT UPDATE IT*/');
    parent::onInstall();
  }

  private function _createCustomizationFile($filePath, $data) {
    $filePath = str_replace('/', DS, $filePath);
    $realfilePath = APPLICATION_PATH . $filePath;
    $is_file_exist = @file_exists($realfilePath);
    if (!empty($is_file_exist)) {
      return;
    }

    $global_directory_name = dirname($realfilePath);
    $is_dir_exist = @is_dir($global_directory_name);
    if (!$is_dir_exist) {
      @mkdir($global_directory_name);
    }
    @chmod($global_directory_name, 0777);


    if (!is_writable($global_directory_name)) {
      return $this->_error("<span style='color:red'>Note: You do not have writable permission on the path below, please give 'chmod 777 recursive permission' on it to continue with the installation process : <br /> 
  Path Name: " . $global_directory_name . "</span>");
    }
//    if (!is_readable($global_directory_name)) {
//      return $this->_error("<span style='color:red'>Note: You do not have readable permission on the path below, please give 'chmod 777 recursive permission' on it to continue with the installation process : <br /> 
//  Path Name: <b>" . $global_directory_name . "</b></span>");
//    }
    $fh = @fopen($realfilePath, 'w');
    @fwrite($fh, $data);
    @fclose($fh);
    @chmod($realfilePath, 0777);
  }

  private function updatePage($pageName, $title, $description) {
    $db = $this->getDb();
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', $pageName)
      ->limit(1)
      ->query()
      ->fetchColumn();
    $tableNameContentName = "engine4_core_content";
    $top_content_id = $db->select()
      ->from($tableNameContentName, 'content_id')
      ->where('page_id =?', $page_id)
      ->where('name =?', 'top')
      ->query()
      ->fetchColumn();
    if (empty($top_content_id)) {
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'parent_content_id' => null,
        'order' => 1,
        'params' => ''
      ));
      $content_id = $db->lastInsertId('engine4_core_content');
      $middle_content_id = $db->select()
        ->from($tableNameContentName, 'content_id')
        ->where('page_id =?', $page_id)
        ->where('parent_content_id =?', $content_id)
        ->where('name =?', 'middle')
        ->query()
        ->fetchColumn();

      if (empty($middle_content_id)) {
        $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $content_id,
          'order' => 2,
          'params' => ''
        ));

        $content_id = $db->lastInsertId('engine4_core_content');

        $middle_banner_id = $db->select()
          ->from($tableNameContentName, 'content_id')
          ->where('page_id =?', $page_id)
          ->where('parent_content_id =?', $content_id)
          ->where('name =?', 'sitecoretheme.banner-images')
          ->query()
          ->fetchColumn();
        if (!$middle_banner_id) {
          $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitecoretheme.banner-images',
            'page_id' => $page_id,
            'parent_content_id' => $content_id,
            'order' => 1,
            'params' => '{"showBanners":"1","selectedBanners":"","width":"","height":"280","speed":"5000","order":"2","verticalHtmlTitle":"' . $title . '","verticalHtmlDescription":"' . $description . '","title":"","nomobile":"0","name":"sitecoretheme.banner-images"}'
          ));
        }
      }
      $db->query("UPDATE `engine4_core_content` SET  `order` =  '2' WHERE  `engine4_core_content`.`page_id` = $page_id AND `engine4_core_content`.`name` = 'main' LIMIT 1 ;");
    } else {
      $middle_content_id = $db->select()
        ->from($tableNameContentName, 'content_id')
        ->where('page_id =?', $page_id)
        ->where('parent_content_id =?', $top_content_id)
        ->where('name =?', 'middle')
        ->query()
        ->fetchColumn();

      if (!empty($middle_content_id)) {

        $middle_banner_id = $db->select()
          ->from($tableNameContentName, 'content_id')
          ->where('page_id =?', $page_id)
          ->where('parent_content_id =?', $middle_content_id)
          ->where('name =?', 'sitecoretheme.banner-images')
          ->query()
          ->fetchColumn();

        if (!$middle_banner_id) {
          $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitecoretheme.banner-images',
            'page_id' => $page_id,
            'parent_content_id' => $middle_content_id,
            'order' => 1,
            'params' => '{"showBanners":"1","selectedBanners":"","width":"","height":"280","speed":"5000","order":"2","verticalHtmlTitle":"' . $title . '","verticalHtmlDescription":"' . $description . '","title":"","nomobile":"0","name":"sitecoretheme.banner-images"}'
          ));
        }
        $db->query("UPDATE `engine4_core_content` SET  `order` =  '2' WHERE  `engine4_core_content`.`page_id` = $page_id AND `engine4_core_content`.`name` = 'main' LIMIT 1;");
        $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $page_id AND `engine4_core_content`.`name` = 'spectacular.banner-images' LIMIT 1;");
      }
    }
  }

}
