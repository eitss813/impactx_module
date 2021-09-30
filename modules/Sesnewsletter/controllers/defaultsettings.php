<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: defaultsettings.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

$db = Zend_Db_Table_Abstract::getDefaultAdapter();


$pageId = $db->select()
    ->from('engine4_core_pages', 'page_id')
    ->where('name = ?', 'sesnewsletter_settings_newsletter-settings')
    ->limit(1)
    ->query()
    ->fetchColumn();
if( !$pageId ) {
    // Insert page
    $db->insert('engine4_core_pages', array(
        'name' => 'sesnewsletter_settings_newsletter-settings',
        'displayname' => 'SES - Newsletter Settings Page',
        'title' => 'Newsletter Settings',
        'description' => 'This page is the newsletter settings page.',
        'custom' => 0,
    ));
    $pageId = $db->lastInsertId();

    // Insert top
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $pageId,
        'order' => 1,
    ));
    $topId = $db->lastInsertId();

    // Insert main
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $pageId,
        'order' => 2,
    ));
    $mainId = $db->lastInsertId();

    // Insert top-middle
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $topId,
    ));
    $topMiddleId = $db->lastInsertId();

    // Insert main-middle
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $pageId,
        'parent_content_id' => $mainId,
        'order' => 2,
    ));
    $mainMiddleId = $db->lastInsertId();

    // Insert menu
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'user.settings-menu',
        'page_id' => $pageId,
        'parent_content_id' => $topMiddleId,
        'order' => 1,
    ));

    // Insert content
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $pageId,
        'parent_content_id' => $mainMiddleId,
        'order' => 1,
    ));
}

//Default Template
$template_id = $db->select()
            ->from('engine4_sesnewsletter_templates', 'template_id')
            ->where('name =?', 'sesnewsletter_default_template')
            ->limit(1)
            ->query()
            ->fetchColumn();
if( !$template_id ) {
    $db->insert('engine4_sesnewsletter_templates', array(
        'name' => 'sesnewsletter_default_template',
        'displayname' => 'Default Newsletter Template',
        'provides' => 'no-viewer;no-subject',
        'custom' => 0,
    ));
    $template_id = $db->lastInsertId();
    $db->insert('engine4_sesnewsletter_content', array(
        'type' => 'container',
        'name' => 'main',
        'template_id' => $template_id,
    ));
    $mainId = $db->lastInsertId();
    $db->insert('engine4_sesnewsletter_content', array(
        'type' => 'container',
        'name' => 'middle',
        'template_id' => $template_id,
        'parent_content_id' => $mainId,
        'order' => 2,
    ));
    $middleId = $db->lastInsertId();
    $db->insert('engine4_sesnewsletter_content', array(
        'type' => 'widget',
        'name' => 'sesnewsletter.content',
        'template_id' => $template_id,
        'parent_content_id' => $middleId,
        'order' => 1,
    ));
}

//Footer Default Work
$footerContent_id = $this->widgetCheck(array('widget_name' => 'sesnewsletter.newsletter', 'page_id' => '2'));
$parent_content_id = $db->select()
        ->from('engine4_core_content', 'content_id')
        ->where('type = ?', 'container')
        ->where('page_id = ?', '2')
        ->where('name = ?', 'main')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (empty($footerContent_id)) {
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesnewsletter.newsletter',
      'page_id' => 2,
      'parent_content_id' => $parent_content_id,
      'order' => 56,
  ));
}

//Default Privacy Set Work
$permissionsTable = Engine_Api::_()->getDbTable('permissions', 'authorization');
foreach (Engine_Api::_()->getDbTable('levels', 'authorization')->fetchAll() as $level) {
  $form = new Sesnewsletter_Form_Admin_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
  ));
  $values = $form->getValues();
  $valuesForm = $permissionsTable->getAllowed('sesnewsletter', $level->level_id, array_keys($form->getValues()));

  $form->populate($valuesForm);
  if ($form->defattribut)
    $form->defattribut->setValue(0);
  $db = $permissionsTable->getAdapter();
  $db->beginTransaction();
  try {
    $nonBooleanSettings = $form->nonBooleanFields();
    $permissionsTable->setAllowed('sesnewsletter', $level->level_id, $values, '', $nonBooleanSettings);
    // Commit
    $db->commit();
  } catch (Exception $e) {
    $db->rollBack();
    throw $e;
  }
}

$db->query('DROP TABLE IF EXISTS `engine4_sesnewsletter_emails`;');
$db->query('CREATE TABLE `engine4_sesnewsletter_emails` (
  `email_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_address` varchar(124) DEFAULT NULL,
  `from_name` varchar(124) DEFAULT NULL,
  `subject` varchar(124) DEFAULT NULL,
  `body` text NULL,
  `email` varchar(124) DEFAULT NULL,
  `template_id` int(11) unsigned NOT NULL DEFAULT "0",
  `stop` tinyint(1) NOT NULL DEFAULT "1",
  PRIMARY KEY (`email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
$db->query('INSERT IGNORE INTO `engine4_core_tasks` (`title`,  `module`, `plugin`,`timeout`, `processes`) VALUES ("SES - Newsletter Email Send to Members",  "sesnewsletter", "Sesnewsletter_Plugin_Task_Newsletteremail",  120, 1);');
$db->query('ALTER TABLE `engine4_sesnewsletter_campaigns` ADD `choose_member` INT(11) NOT NULL DEFAULT "0";');
$db->query('ALTER TABLE `engine4_sesnewsletter_campaigns` ADD `member_levels` VARCHAR(255) NULL AFTER `choose_member`, ADD `networks` VARCHAR(255) NULL AFTER `member_levels`, ADD `profile_types` VARCHAR(255) NULL AFTER `networks`;');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sesnewsletter_admin_message_mail", "sesnewsletter",  "Email All Members", "",  \'{"route":"admin_default","module":"sesnewsletter","controller":"message","action":"mail"}\',  "sesnewsletter_admin_main", "",  990);');
