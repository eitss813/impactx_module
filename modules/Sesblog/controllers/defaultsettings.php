<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: defaultsettings.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

$db = Zend_Db_Table_Abstract::getDefaultAdapter();
//$this->uploadDefaultCategory();
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ("sesblog_admin_main_importblog", "sesblog", "Import SE Blog", "", \'{"route":"admin_default","module":"sesblog","controller":"import-blog"}\', "sesblog_admin_main", "", 996);');

//Blogs Welcome Page
$page_id = $db->select()
              ->from('engine4_core_pages', 'page_id')
              ->where('name = ?', 'sesblog_index_welcome')
              ->limit(1)
              ->query()
              ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_welcome',
    'displayname' => 'SNS - Advanced Blog - Blogs Welcome Page',
    'title' => 'Blog Welcome Page',
    'description' => 'This page is the blog welcome page.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  
  $pageName = 'sesblog_index_welcome';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Blog Home Page
$select = $db->select()
            ->from('engine4_core_pages')
            ->where('name = ?', 'sesblog_index_home')
            ->limit(1);
$info = $select->query()->fetch();
if (empty($info)) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_home',
    'displayname' => 'SNS - Advanced Blog - Blogs Home Page',
    'title' => 'Blog Home',
    'description' => 'This is the blog home page.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId('engine4_core_pages');
  
  $pageName = 'sesblog_index_home';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Blog Browse Page
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesblog_index_browse')
  ->limit(1)
  ->query()
  ->fetchColumn();
// insert if it doesn't exist yet
if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_browse',
    'displayname' => 'SNS - Advanced Blog - Browse Blogs Page',
    'title' => 'Blog Browse',
    'description' => 'This page lists blog entries.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_index_browse';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Browse Categories Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesblog_category_browse')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {

  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesblog_category_browse',
      'displayname' => 'SNS - Advanced Blog - Browse Categories Page',
      'title' => 'Blog Browse Category',
      'description' => 'This page lists blog categories.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_category_browse';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Browse Locations Page
$page_id = $db->select()
    ->from('engine4_core_pages', 'page_id')
    ->where('name = ?', 'sesblog_index_locations')
    ->limit(1)
    ->query()
    ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesblog_index_locations',
      'displayname' => 'SNS - Advanced Blog - Browse Locations Page',
      'title' => 'Blog Browse Location',
      'description' => 'This page show blog locations.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $pageName = 'sesblog_index_locations';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Browse Reviews Page
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesblog_review_browse')
  ->limit(1)
  ->query()
  ->fetchColumn();
// insert if it doesn't exist yet
if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_review_browse',
    'displayname' => 'SNS - Advanced Blog - Browse Reviews Page',
    'title' => 'Blog Browse Reviews',
    'description' => 'This page show blog browse reviews page.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_review_browse';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Manage Blogs Page
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesblog_index_manage')
  ->limit(1)
  ->query()
  ->fetchColumn();

// insert if it doesn't exist yet
if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_manage',
    'displayname' => 'SNS - Advanced Blog - Manage Blogs Page',
    'title' => 'My Blog Entries',
    'description' => 'This page lists a user\'s blog entries.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_index_manage';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}


//New Claims Page
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesblog_index_claim')
  ->limit(1)
  ->query()
  ->fetchColumn();

// insert if it doesn't exist yet
if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_claim',
    'displayname' => 'SNS - Advanced Blog - New Claims Page',
    'title' => 'Blog Claim',
    'description' => 'This page lists blog entries.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_index_claim';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Blog Create Page
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesblog_index_create')
  ->limit(1)
  ->query()
  ->fetchColumn();

if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_create',
    'displayname' => 'SNS - Advanced Blog - Blog Create Page',
    'title' => 'Write New Blog',
    'description' => 'This page is the blog create page.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_index_create';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}


//Browse Tags Page
$page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'sesblog_index_tags')
            ->limit(1)
            ->query()
            ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_tags',
    'displayname' => 'SNS - Advanced Blog - Browse Tags Page',
    'title' => 'Blog Browse Tags Page',
    'description' => 'This page displays the blog tags.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $pageName = 'sesblog_index_tags';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Album View Page
$page_id = $db->select()
              ->from('engine4_core_pages', 'page_id')
              ->where('name = ?', 'sesblog_album_view')
              ->limit(1)
              ->query()
              ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_album_view',
    'displayname' => 'SNS - Advanced Blog - Album View Page',
    'title' => 'Blog Album View',
    'description' => 'This page displays an blog album.',
    'provides' => 'subject=album',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  $pageName = 'sesblog_album_view';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Photo View Page
$page_id = $db->select()
              ->from('engine4_core_pages', 'page_id')
              ->where('name = ?', 'sesblog_photo_view')
              ->limit(1)
              ->query()
              ->fetchColumn();
if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_photo_view',
    'displayname' => 'SNS - Advanced Blog - Photo View Page',
    'title' => 'Blog Album Photo View',
    'description' => 'This page displays an blog album\'s photo.',
    'provides' => 'subject=sesblog_photo',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_photo_view';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}


//Browse Claim Requests Page
$page_id = $db->select()
              ->from('engine4_core_pages', 'page_id')
              ->where('name = ?', 'sesblog_index_claim-requests')
              ->limit(1)
              ->query()
              ->fetchColumn();
// insert if it doesn't exist yet
if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_claim-requests',
    'displayname' => 'SNS - Advanced Blog - Browse Claim Requests Page',
    'title' => 'Blog Claim Requests',
    'description' => 'This page lists blog claims request entries.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_index_claim-requests';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Blog List Page
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesblog_index_list')
  ->limit(1)
  ->query()
  ->fetchColumn();

// insert if it doesn't exist yet
if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_list',
    'displayname' => 'SNS - Advanced Blog - Blog List Page',
    'title' => 'Blog List',
    'description' => 'This page lists a member\'s blog entries.',
    'provides' => 'subject=user',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_index_list-requests';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Blog Profile Page Design 1
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesblog_index_view_1')
  ->limit(1)
  ->query()
  ->fetchColumn();

// insert if it doesn't exist yet
if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_view_1',
    'displayname' => 'SNS - Advanced Blog - Blog Profile Page Design 1',
    'title' => 'Blog View',
    'description' => 'This page displays a blog entry.',
    'provides' => 'subject=sesblog',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_index_view_1';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}



//Blog Profile Page Design 2
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesblog_index_view_2')
  ->limit(1)
  ->query()
  ->fetchColumn();

// insert if it doesn't exist yet
if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_view_2',
    'displayname' => 'SNS - Advanced Blog - Blog Profile Page Design 2',
    'title' => 'Blog View',
    'description' => 'This page displays a blog entry.',
    'provides' => 'subject=sesblog',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_index_view_2';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}


//Blog Profile Page Design 3
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesblog_index_view_3')
  ->limit(1)
  ->query()
  ->fetchColumn();

// insert if it doesn't exist yet
if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_view_3',
    'displayname' => 'SNS - Advanced Blog - Blog Profile Page Design 3',
    'title' => 'Blog View',
    'description' => 'This page displays a blog entry.',
    'provides' => 'subject=sesblog',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_index_view_3';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}


//Blog Profile Page Design 4
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesblog_index_view_4')
  ->limit(1)
  ->query()
  ->fetchColumn();

// insert if it doesn't exist yet
if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesblog_index_view_4',
    'displayname' => 'SNS - Advanced Blog - Blog Profile Page Design 4',
    'title' => 'Blog View',
    'description' => 'This page displays a blog entry.',
    'provides' => 'subject=sesblog',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_index_view_4';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Review Profile Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesblog_review_view')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sesblog_review_view',
      'displayname' => 'SNS - Advanced Blog - Review Profile Page',
      'title' => 'Blog Review View',
      'description' => 'This page displays a blog review entry.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_review_view';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

//Category View Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesblog_category_index')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesblog_category_index',
      'displayname' => 'SNS - Advanced Blog - Blog Category View Page',
      'title' => 'Blog Category View Page',
      'description' => 'This page lists blog category view page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  $pageName = 'sesblog_category_index';
  include APPLICATION_PATH . "/application/modules/Sesblog/controllers/resetPage.php";
}

$blog_table_exist = $db->query('SHOW TABLES LIKE \'engine4_sesblog_blogs\'')->fetch();
if (!empty($blog_table_exist)) {
  $ssesblog_id = $db->query('SHOW COLUMNS FROM engine4_sesblog_blogs LIKE \'ssesblog_id\'')->fetch();
  if (empty($ssesblog_id)) {
    $db->query('ALTER TABLE `engine4_sesblog_blogs` ADD `ssesblog_id` INT(11) NOT NULL DEFAULT "0";');
  }
}

$blogcat_table_exist = $db->query('SHOW TABLES LIKE \'engine4_sesblog_categories\'')->fetch();
if (!empty($blogcat_table_exist)) {
  $ssesblog_cayegoryid = $db->query('SHOW COLUMNS FROM engine4_sesblog_categories LIKE \'ssesblog_categoryid\'')->fetch();
  if (empty($ssesblog_cayegoryid)) {
    $db->query('ALTER TABLE `engine4_sesblog_categories` ADD `ssesblog_categoryid` INT(11) NULL;');
  }
}

$db->query("DELETE FROM `engine4_activity_notificationtypes` WHERE `engine4_activity_notificationtypes`.`type` = 'sesblog_link_blog';");
$db->query("DELETE FROM `engine4_activity_notificationtypes` WHERE `engine4_activity_notificationtypes`.`type` = 'sesblog_link_event';");
$db->query("DELETE FROM `engine4_activity_notificationtypes` WHERE `engine4_activity_notificationtypes`.`type` = 'sesblog_reject_blog_request';");
$db->query("DELETE FROM `engine4_activity_notificationtypes` WHERE `engine4_activity_notificationtypes`.`type` = 'sesblog_reject_event_request';");
$db->query("DELETE FROM `engine4_activity_notificationtypes` WHERE `engine4_activity_notificationtypes`.`type` = 'sesuser_claimadmin_blog';");

$table_exist = $db->query("SHOW TABLES LIKE 'engine4_sesblog_categories'")->fetch();
if (!empty($table_exist)) {
    $member_levels = $db->query("SHOW COLUMNS FROM engine4_sesblog_categories LIKE 'member_levels'")->fetch();
    if (empty($member_levels)) {
        $db->query('ALTER TABLE `engine4_sesblog_categories` ADD `member_levels` VARCHAR(255) NULL DEFAULT NULL;');
    }
}
$db->query('UPDATE `engine4_sesblog_categories` SET `member_levels` = "1,2,3,4" WHERE `engine4_sesblog_categories`.`subcat_id` = 0 and  `engine4_sesblog_categories`.`subsubcat_id` = 0;');


$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ("sesblog_admin_main_integrateothermodule", "sesblog", "Integrate Plugins", "", \'{"route":"admin_default","module":"sesblog","controller":"integrateothermodule","action":"index"}\', "sesblog_admin_main", "", 995);');

$db->query('DROP TABLE IF EXISTS `engine4_sesblog_integrateothermodules`;');
$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_integrateothermodules` (
  `integrateothermodule_id` int(11) unsigned NOT NULL auto_increment,
  `module_name` varchar(64) NOT NULL,
  `content_type` varchar(64) NOT NULL,
  `content_url` varchar(255) NOT NULL,
  `content_id` varchar(64) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`integrateothermodule_id`),
  UNIQUE KEY `content_type` (`content_type`,`content_id`),
  KEY `module_name` (`module_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');

$table_exist = $db->query("SHOW TABLES LIKE 'engine4_sesblog_blogs'")->fetch();
if (!empty($table_exist)) {
    $resource_type = $db->query("SHOW COLUMNS FROM engine4_sesblog_blogs LIKE 'resource_type'")->fetch();
    if (empty($resource_type)) {
        $db->query('ALTER TABLE `engine4_sesblog_blogs` ADD `resource_type` VARCHAR(128) NULL;');
    }
    $resource_id = $db->query("SHOW COLUMNS FROM engine4_sesblog_blogs LIKE 'resource_id'")->fetch();
    if (empty($resource_id)) {
        $db->query('ALTER TABLE `engine4_sesblog_blogs` ADD `resource_id` INT(11) NOT NULL DEFAULT "0";');
    }
    $networks = $db->query("SHOW COLUMNS FROM engine4_sesblog_blogs LIKE 'networks'")->fetch();
    if (empty($networks)) {
        $db->query('ALTER TABLE `engine4_sesblog_blogs` ADD `networks` VARCHAR(255) NULL');
    }
    $levels = $db->query("SHOW COLUMNS FROM engine4_sesblog_blogs LIKE 'levels'")->fetch();
    if (empty($levels)) {
        $db->query('ALTER TABLE `engine4_sesblog_blogs` ADD `levels` VARCHAR(255) NULL');
    }
    $cotinuereading = $db->query("SHOW COLUMNS FROM engine4_sesblog_blogs LIKE 'cotinuereading'")->fetch();
    if (empty($cotinuereading)) {
        $db->query('ALTER TABLE `engine4_sesblog_blogs` ADD `cotinuereading` TINYINT(1) NOT NULL DEFAULT \'0\';');
    }

    $continue_height = $db->query("SHOW COLUMNS FROM engine4_sesblog_blogs LIKE 'continue_height'")->fetch();
    if(empty($continue_height)) {
        $db->query('ALTER TABLE `engine4_sesblog_blogs` ADD `continue_height` INT(11) NOT NULL DEFAULT "0";');
    }
}

$db->query('DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = "sesblog.chooselayout";');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sesblog_blogadmin", "sesblog", \'You are now a admin in this blog {item:$object}.\', 0, ""),
("sesblog_acceptadminre", "sesblog", \'{item:$subject} accepted as admin request for blog {item:$object}.\', 0, ""),
("sesblog_rejestadminre", "sesblog", \'{item:$subject} rejected as admin request for blog {item:$object}.\', 0, ""),
("sesblog_removeadminre", "sesblog", \'{item:$subject} removed as admin request for blog {item:$object}.\', 0, "");');
$db->query('ALTER TABLE `engine4_sesblog_roles` ADD `resource_approved` TINYINT(1) NOT NULL DEFAULT "1";');
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sesblog_profile_member", "sesblog", "Member", "Sesblog_Plugin_Menus", "", "sesblog_gutter", "", 3);');


$permissionsTable = Engine_Api::_()->getDbTable('permissions', 'authorization');
foreach (Engine_Api::_()->getDbTable('levels', 'authorization')->fetchAll() as $level) {
  $form = new Sesblog_Form_Admin_Level_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => (in_array($level->type, array('admin', 'moderator'))),
  ));
  $values = $form->getValues();
  $valuesForm = $permissionsTable->getAllowed('sesblog_blog', $level->level_id, array_keys($form->getValues()));

  $form->populate($valuesForm);
  if ($form->defattribut)
    $form->defattribut->setValue(0);
  $db = $permissionsTable->getAdapter();
  $db->beginTransaction();
  try {
    if ($level->type != 'public') {
      // Set permissions
      $values['auth_comment'] = (array) $values['auth_comment'];
      $values['auth_view'] = (array) $values['auth_view'];
    }
    $nonBooleanSettings = $form->nonBooleanFields();
    $permissionsTable->setAllowed('sesblog_blog', $level->level_id, $values, '', $nonBooleanSettings);
    $claimValue = array('create' => $values['allow_claim']);
    $permissionsTable->setAllowed('sesblog_claim', $level->level_id, $claimValue);
    // Commit
    $db->commit();
  } catch (Exception $e) {
    $db->rollBack();
    throw $e;
  }
}

$permissionsTable = Engine_Api::_()->getDbTable('permissions', 'authorization');
foreach (Engine_Api::_()->getDbTable('levels', 'authorization')->fetchAll() as $level) {
  $form = new Sesblog_Form_Admin_Review_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => (in_array($level->type, array('admin', 'moderator'))),
  ));
  $values = $form->getValues();
  $valuesForm = $permissionsTable->getAllowed('sesblog_review', $level->level_id, array_keys($form->getValues()));

  $form->populate($valuesForm);
  if ($form->defattribut)
    $form->defattribut->setValue(0);
  $db = $permissionsTable->getAdapter();
  $db->beginTransaction();
  try {
    if ($level->type != 'public') {
      // Set permissions
      $values['auth_comment'] = (array) $values['auth_comment'];
      $values['auth_view'] = (array) $values['auth_view'];
    }
    $nonBooleanSettings = $form->nonBooleanFields();
    $permissionsTable->setAllowed('sesblog_review', $level->level_id, $values, '', $nonBooleanSettings);
    // Commit
    $db->commit();
  } catch (Exception $e) {
    $db->rollBack();
    throw $e;
  }
}

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ("sesblog_admin_main_extension", "sesblog", "Extensions", "", \'{"route":"admin_default","module":"sesblog","controller":"settings", "action": "extensions"}\', "sesblog_admin_main", "", 999);');
