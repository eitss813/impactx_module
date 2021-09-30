<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WidgetSettings.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

if(array_key_exists('language_phrases_pages', $_POST) ) {
  Engine_Api::_()->getApi('settings', 'core')->setSetting('language_phrases_pages', $_POST['language_phrases_pages']);
}

if(array_key_exists('language_phrases_page', $_POST) ) {
  Engine_Api::_()->getApi('settings', 'core')->setSetting('language_phrases_page', $_POST['language_phrases_page']);
}

$db = Engine_Db_Table::getDefaultAdapter(); 

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitepagemember_notificationpost", "sitepagemember", \'{item:$subject} posted in {item:$object}.\', 0, "");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("sitepage_main_member", "sitepagemember", "Members", \'Sitepagemember_Plugin_Menus::canViewMembers\', \'{"route":"sitepagemember_home","action":"home"}\', "sitepage_main", "", 1, 0, 999);');

$db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEMEMBER_REQUEST_EMAIL", "sitepagemember", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");');

$db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEMEMBER_APPROVE_EMAIL", "sitepagemember", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");');

$select = new Zend_Db_Select($db);
$advancedactivity = $select->from('engine4_core_modules', 'name')
				->where('name = ?', 'advancedactivity')
				->query()
				->fetchcolumn();

$is_enabled = $select->query()->fetchObject();
if (!empty($advancedactivity)) {
	$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`) VALUES ("sitepage_join", "sitepagemember", \'{item:$subject} joined the page {item:$object}:\', 1, 3, 1, 1, 1, 1, 1);');
} else {
	$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("sitepage_join", "sitepagemember", \'{item:$subject} joined the page {item:$object}:\', 1, 3, 1, 1, 1, 1);');
}

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitepage_addmember", "sitepagemember", \'{item:$subject} has joined you in page {item:$object}.\', 0, "");');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitepagemember_approve", "sitepagemember", \'{item:$subject} has requested to join the page {item:$object}.\', 0, "");');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitepagemember_accepted", "sitepagemember", \'Your request to join the page {item:$object} has been approved.\', 0, "");');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitepagemember_invite", "sitepagemember", \'{item:$subject} has invited you to the page {item:$object}.\', 1, "sitepagemember.widget.request-member");');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitepage_join", "sitepagemember", \'{item:$subject} joined the page {item:$object}.\', 0, "");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`,`order`) VALUES
("sitepagemember_admin_manage_member", "sitepagemember", "Manage Members", "", \'{"route":"admin_default","module":"sitepagemember","controller":"manage","action":"index"}\', "sitepagemember_admin_main", "", 3),
("sitepagemember_admin_main_managecategory", "sitepagemember", "Manage Member Roles", "", \'{"route":"admin_default","module":"sitepagemember","controller":"settings", "action": "manage-category"}\', "sitepagemember_admin_main", "", 2),
("sitepagemember_admin_widget_settings", "sitepagemember", "Member Of the Day", "", \'{"route":"admin_default","module":"sitepagemember","controller":"widgets","action":"index"}\', "sitepagemember_admin_main", "", 4);');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
("sitepage_gutter_join", "sitepagemember", "Join Page", "Sitepagemember_Plugin_Menus", \'{"route":"sitepage_profilepagemember", "class":"buttonlink smoothbox icon_sitepage_join","action":"join"}\', "sitepage_gutter", "", 0, 1),
("sitepage_gutter_leave", "sitepagemember", "Leave Page", "Sitepagemember_Plugin_Menus", \'{"route":"sitepage_profilepagemember", "class":"buttonlink smoothbox icon_sitepage_leave","action":"leave"}\', "sitepage_gutter", "", 0, 2),
("sitepage_gutter_request", "sitepagemember", "Join Page", "Sitepagemember_Plugin_Menus", \'{"route":"sitepage_profilepagemember", "class":"buttonlink smoothbox icon_sitepage_join","action":"request"}\', "sitepage_gutter", "", 0, 3),
("sitepage_gutter_cancel", "sitepagemember", "Cancel Membership Request", "Sitepagemember_Plugin_Menus", \'{"route":"sitepage_profilepagemember", "class":"buttonlink smoothbox  icon_sitepage_cancel","action":"cancel"}\', "sitepage_gutter", "", 0, 4),
("sitepage_gutter_invite", "sitepagemember", "Add People", "Sitepagemember_Plugin_Menus", \'{"route":"sitepage_profilepagemember", "class":"buttonlink smoothbox icon_sitepage_ad_member","action":"invite"}\', "sitepage_gutter", "", 0, 5),
("sitepage_gutter_invite_pageadmin", "sitepagemember", "Add People", "Sitepagemember_Plugin_Menus", \'{"route":"sitepage_profilepagemember", "class":"buttonlink smoothbox icon_sitepage_ad_member","action":"invite-members"}\', "sitepage_gutter", "", 0, 8),
("sitepage_gutter_respondinvite", "sitepagemember", "Respond to Membership Request", "Sitepagemember_Plugin_Menus", \'{"route":"sitepage_profilepagemember", "class":"buttonlink smoothbox icon_sitepage_accept","action":"respond"}\', "sitepage_gutter", "", 0, 998),
("sitepage_gutter_respondmemberinvite", "sitepagemember", "Respond to Membership Invitation", "Sitepagemember_Plugin_Menus", \'{"route":"sitepage_profilepagemember", "class":"buttonlink smoothbox icon_sitepage_accept","action":"respondinvite"}\', "sitepage_gutter", "", 0, 990);');



$select = new Zend_Db_Select($db);
$select->from('engine4_core_modules')
			->where('name = ?', 'sitepage')
			->where('enabled = ?', 1);
$check_sitepage = $select->query()->fetchObject();
if(!empty($check_sitepage)) {
	$select = new Zend_Db_Select($db);
	$select_page = $select
							->from('engine4_core_pages', 'page_id')
							->where('name = ?', 'sitepage_index_view')
							->limit(1);
	$page = $select_page->query()->fetchAll();
	
	if(!empty($page)) {
		$page_id = $page[0]['page_id'];

		//INSERTING THE MEMBER WIDGET IN SITEPAGE_ADMIN_CONTENT TABLE ALSO.
		Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagemember.profile-sitepagemembers', $page_id, 'Members', 'true', '122');
		//INSERTING THE MEMBER WIDGET IN SITEPAGE_ADMIN_CONTENT TABLE ALSO.
		Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagemember.profile-sitepagemembers-announcements', $page_id, 'Announcements', 'true', '123');

		//INSERTING THE MEMBER WIDGET IN CORE_CONTENT TABLE ALSO.
		Engine_Api::_()->getApi('layoutcore', 'sitepage')->setContentDefaultInfo('sitepagemember.profile-sitepagemembers', $page_id, 'Members', 'true', '122');
		Engine_Api::_()->getApi('layoutcore', 'sitepage')->setContentDefaultInfo('sitepagemember.profile-sitepagemembers-announcements', $page_id, 'Announcements', 'true', '123');

		//INSERTING THE MEMBER WIDGET IN SITEPAGE_CONTENT TABLE ALSO.
		$select = new Zend_Db_Select($db);
		$contentpage_ids = $select->from('engine4_sitepage_contentpages', 'contentpage_id')->query()->fetchAll();
		foreach ($contentpage_ids as $contentpage_id) {
			if(!empty($contentpage_id)) {
				Engine_Api::_()->getDbtable('content', 'sitepage')->setDefaultInfo('sitepagemember.profile-sitepagemembers', $contentpage_id['contentpage_id'], 'Members', 'true', '122');
				Engine_Api::_()->getDbtable('content', 'sitepage')->setDefaultInfo('sitepagemember.profile-sitepagemembers-announcements', $contentpage_id['contentpage_id'], 'Announcements', 'true', '123');
				if(!empty($_POST['sitepagemember_enabled_group_layout'])) {
					$select = new Zend_Db_Select($db);
					$select_content = $select
											->from('engine4_sitepage_content')
											->where('contentpage_id = ?', $contentpage_id['contentpage_id'])
											->where('type = ?', 'widget')
											->where('name = ?', 'sitepagemember.pagecover-photo-sitepagemembers')
											->limit(1);
					$content = $select_content->query()->fetchAll();
					if(empty($content)) {
						$select = new Zend_Db_Select($db);
						$select_container = $select
												->from('engine4_sitepage_content', 'content_id')
												->where('contentpage_id = ?', $contentpage_id['contentpage_id'])
												->where('type = ?', 'container')
												->limit(1);
						$container = $select_container->query()->fetchAll();
						if(!empty($container)) {
							$container_id = $container[0]['content_id'];
							$select = new Zend_Db_Select($db);
							$select_left = $select
												->from('engine4_sitepage_content')
												->where('parent_content_id = ?', $container_id)
												->where('type = ?', 'container')
												->where('name = ?', 'middle')
												->limit(1);
							$left = $select_left->query()->fetchAll();
							if(!empty($left)) {
								$left_id = $left[0]['content_id'];
								$db->insert('engine4_sitepage_content', array(
								'contentpage_id' => $contentpage_id['contentpage_id'],
								'type' => 'widget',
								'name' => 'sitepagemember.pagecover-photo-sitepagemembers',
								'parent_content_id' => $left_id,
								'order' => 1,
								'params' => '{"title":""}',
								));
							}
						}
					}
				} 
			}
		}

    if(!empty($_POST['sitepagemember_enabled_group_layout'])) {
			Engine_Api::_()->getDbtable('admincontent', 'sitepage')->delete(array('name =?' => 'sitepage.photorecent-sitepage'));
			Engine_Api::_()->getDbtable('content', 'sitepage')->delete(array('name =?' => 'sitepage.photorecent-sitepage'));	
			Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitepage.photorecent-sitepage'));	
			Engine_Api::_()->getDbtable('admincontent', 'sitepage')->delete(array('name =?' => 'sitepage.page-cover-information-sitepage'));
			Engine_Api::_()->getDbtable('content', 'sitepage')->delete(array('name =?' => 'sitepage.page-cover-information-sitepage'));	
			Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitepage.page-cover-information-sitepage'));
			$select = new Zend_Db_Select($db);
			$select_content = $select
									->from('engine4_sitepage_admincontent')
									->where('page_id = ?', $page_id)
									->where('type = ?', 'widget')
									->where('name = ?', 'sitepagemember.pagecover-photo-sitepagemembers')
									->limit(1);
			$content = $select_content->query()->fetchAll();
			if(empty($content)) {
				$select = new Zend_Db_Select($db);
				$select_container = $select
										->from('engine4_sitepage_admincontent', 'admincontent_id')
										->where('page_id = ?', $page_id)
										->where('type = ?', 'container')
										->limit(1);
				$container = $select_container->query()->fetchAll();
				if(!empty($container)) {
					$container_id = $container[0]['admincontent_id'];
					$select = new Zend_Db_Select($db);
					$select_left = $select
										->from('engine4_sitepage_admincontent')
										->where('parent_content_id = ?', $container_id)
										->where('type = ?', 'container')
										->where('name = ?', 'middle')
										->limit(1);
					$left = $select_left->query()->fetchAll();
					if(!empty($left)) {
						$left_id = $left[0]['admincontent_id'];
						$db->insert('engine4_sitepage_admincontent', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagemember.pagecover-photo-sitepagemembers',
						'parent_content_id' => $left_id,
						'order' => 1,
						'params' => '{"title":""}',
						));
					}
				}
			} 
    
			$select = new Zend_Db_Select($db);
			$select_content = $select
									->from('engine4_core_content')
									->where('page_id = ?', $page_id)
									->where('type = ?', 'widget')
									->where('name = ?', 'sitepagemember.pagecover-photo-sitepagemembers')
									->limit(1);
			$content = $select_content->query()->fetchAll();
			if(empty($content)) {
				$select = new Zend_Db_Select($db);
				$select_container = $select
										->from('engine4_core_content', 'content_id')
										->where('page_id = ?', $page_id)
										->where('type = ?', 'container')
										->limit(1);
				$container = $select_container->query()->fetchAll();
				if(!empty($container)) {
					$container_id = $container[0]['content_id'];
					$select = new Zend_Db_Select($db);
					$select_left = $select
										->from('engine4_core_content')
										->where('parent_content_id = ?', $container_id)
										->where('type = ?', 'container')
										->where('name = ?', 'middle')
										->limit(1);
					$left = $select_left->query()->fetchAll();
					if(!empty($left)) {
						$left_id = $left[0]['content_id'];
						$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagemember.pagecover-photo-sitepagemembers',
						'parent_content_id' => $left_id,
						'order' => 1,
						'params' => '{"title":""}',
						));
					}
				}
			}
    }
	}
}