<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2018-2019 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: InstallController.php 6590 2018-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_InstallController extends Core_Controller_Action_Standard
{

  protected $_token;

  public function init()
  {
    $this->_helper->contextSwitch->initContext();
    $settingsTable = Engine_Api::_()->getDbtable('settings', 'core');
    $row = $settingsTable->fetchRow($settingsTable->select()
      ->where('name = ?', 'sitepage.install.ssotoken'));
    $this->_token = $row ? $row->value : null;    
    $token = $this->_getParam('key', false);
    if( $token != $this->_token ) {
      exit(0);
    }
  }

  public function postInstallAction()
  {
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("sitepage_insights", "sitepage", "", 0, ""), ("sitepage_manageadmin", "sitepage", "You have become an administrator of the page: {item:$object}.", 0, "")');

    $suggestionWidgets = 0;
    $select = new Zend_Db_Select($db);
    $check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
    $check_name = $check_table->info('name');
    $select
    ->from('engine4_core_modules')
    ->where('name = ?', 'suggestion');
    $modules_version = $select->query()->fetchObject();
    if (!empty($modules_version)) {
      $product_version = $modules_version->version;
      if ($product_version >= '4.1.5') {
        $suggestionWidgets = 1;
      }
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_main_claim');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_main_claim';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Claim a Page';
      $menu_item->plugin = 'Sitepage_Plugin_Menus::canViewClaims';
      $menu_item->params = '{"route":"sitepage_claimpages"}';
      $menu_item->menu = 'sitepage_main';
      $menu_item->submenu = '';
      $menu_item->order = 17;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_package');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_package';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Manage Packages';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"package","action":"index"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 2;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_profilemaps');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_profilemaps';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Category-Page Profile Mapping';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"profilemaps", "action":"manage"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 6;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_claim');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_claim';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Manage Claims';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"claim"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 9;
      $menu_item->save();
    }

    // $select = $check_table->select()
    // ->from($check_name, array('id'))
    // ->where('name = ?', 'sitepage_admin_main_layoutdefault');
    // $queary_info = $select->query()->fetchAll();
    // if (empty($queary_info)) {
    //   $menu_item = $check_table->createRow();
    //   $menu_item->name = 'sitepage_admin_main_layoutdefault';
    //   $menu_item->module = 'sitepage';
    //   $menu_item->label = 'Page Layout';
    //   $menu_item->plugin = '';
    //   $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"defaultlayout"}';
    //   $menu_item->menu = 'sitepage_admin_main';
    //   $menu_item->submenu = '';
    //   $menu_item->enabled = 0;
    //   $menu_item->order = 13;
    //   $menu_item->save();
    // }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_adsettings');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_adsettings';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Ad Settings';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"settings","action":"adsettings"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 14;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_transactions');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_transactions';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Transactions';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"payment"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 15;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_import');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_import';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Import';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"importlisting"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 15;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_extension');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_extension';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Extensions';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"extension","action":"upgrade"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 25;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_form_search');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_form_search';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Search Form Settings';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"settings","action":"form-search"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->enabled = 1;
      $menu_item->custom = 0;
      $menu_item->order = 14;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_main_home');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_main_home';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Pages Home';
      $menu_item->plugin = 'Sitepage_Plugin_Menus::canViewSitepages';
      $menu_item->params = '{"route":"sitepage_general","action":"home"}';
      $menu_item->menu = 'sitepage_main';
      $menu_item->submenu = '';
      $menu_item->order = 1;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_main_browse');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_main_browse';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Browse Pages';
      $menu_item->plugin = 'Sitepage_Plugin_Menus::canViewSitepages';
      $menu_item->params = '{"route":"sitepage_general","action":"index"}';
      $menu_item->menu = 'sitepage_main';
      $menu_item->submenu = '';
      $menu_item->order = 2;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_main_manage');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_main_manage';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'My Pages';
      $menu_item->plugin = 'Sitepage_Plugin_Menus::canCreateSitepages';
      $menu_item->params = '{"route":"sitepage_general","action":"manage"}';
      $menu_item->menu = 'sitepage_main';
      $menu_item->submenu = '';
      $menu_item->order = 3;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_main_create');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_main_create';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Create New Page';
      $menu_item->plugin = 'Sitepage_Plugin_Menus::canCreateSitepages';
      $menu_item->params = '{"route":"sitepage_packages"}';
      $menu_item->menu = 'sitepage_main';
      $menu_item->submenu = '';
      $menu_item->order = 4;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_quick_create');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_quick_create';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Create New Page';
      $menu_item->plugin = 'Sitepage_Plugin_Menus::canCreateSitepages';
      $menu_item->params = '{"route":"sitepage_packages","class":"buttonlink icon_sitepage_new"}';
      $menu_item->menu = 'sitepage_quick';
      $menu_item->submenu = '';
      $menu_item->order = 1;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_level');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_level';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Member Level Settings';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"level"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 3;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_viewsitepage');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_viewsitepage';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Manage Pages';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"viewsitepage"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 8;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_widget');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_widget';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Widget Settings';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"widgets"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 7;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_fields');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_fields';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Profile Fields';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"fields"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 5;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_sitepagecategories');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_sitepagecategories';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Categories';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"settings","action":"sitepagecategories"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 4;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_statistic');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_statistic';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Statistics';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"settings","action":"statistic"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 12;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_email');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_email';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Insights Email Settings';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"settings","action":"email"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 10;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'sitepage_admin_main_graph');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'sitepage_admin_main_graph';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Insights Graph Settings';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"settings","action":"graph"}';
      $menu_item->menu = 'sitepage_admin_main';
      $menu_item->submenu = '';
      $menu_item->order = 11;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'core_main_sitepage');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'core_main_sitepage';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Pages';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"sitepage_general","action":"home"}';
      $menu_item->menu = 'core_main';
      $menu_item->submenu = '';
      $menu_item->order = 4;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'core_sitemap_sitepage');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'core_sitemap_sitepage';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Pages';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"sitepage_general","action":"home"}';
      $menu_item->menu = 'core_sitemap';
      $menu_item->submenu = '';
      $menu_item->order = 4;
      $menu_item->save();
    }

    $select = $check_table->select()
    ->from($check_name, array('id'))
    ->where('name = ?', 'authorization_admin_level_sitepage');
    $queary_info = $select->query()->fetchAll();
    if (empty($queary_info)) {
      $menu_item = $check_table->createRow();
      $menu_item->name = 'authorization_admin_level_sitepage';
      $menu_item->module = 'sitepage';
      $menu_item->label = 'Pages';
      $menu_item->plugin = '';
      $menu_item->params = '{"route":"admin_default","module":"sitepage","controller":"level","action":"index"}';
      $menu_item->menu = 'authorization_admin_level';
      $menu_item->submenu = '';
      $menu_item->order = 999;
      $menu_item->save();
    }

    include APPLICATION_PATH . '/application/modules/Sitepage/controllers/license/widgetSettings.php';

    $settingsTable = Engine_Api::_()->getDbtable('settings', 'core');
    $settingsTable->delete(array('name = ?' => 'sitepage.install.ssotoken'));
    exit(0);
  }

}