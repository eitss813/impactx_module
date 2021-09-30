<?php
//folder name or directory name.
$module_name = 'sesblog';

//product title and module title.
$module_title = 'Advanced Blog Plugin';

if (!$this->getRequest()->isPost()) {
  return;
}

if (!$form->isValid($this->getRequest()->getPost())) {
  return;
}

if ($this->getRequest()->isPost()) {

  $postdata = array();
//domain name
  $postdata['domain_name'] = $_SERVER['HTTP_HOST'];
//license key
  $postdata['licenseKey'] = @base64_encode($_POST['sesblog_licensekey']);
  $postdata['module_name'] = @base64_encode($module_name);
  $postdata['module_title'] = @base64_encode($module_title);

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, "https://socialnetworking.solutions/licensenewcheck.php");


  curl_setopt($ch, CURLOPT_POST, 1);

// in real life you should use something like:
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

// receive server response ...
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $server_output = curl_exec($ch);
  $output = explode(" sesquerysql ",$server_output);
  $error = 0;
  if (curl_error($ch)) {
    $error = 1;
  }
  curl_close($ch);

  //Here we can set some variable for checking in plugin files.
  if ($output[0] == "OK" && $error != 1) {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.pluginactivated')) {
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();

			$db->query('INSERT IGNORE INTO `engine4_sesbasic_integrateothermodules` ( `module_name`, `type`, `content_type`, `content_type_photo`, `content_id`, `content_id_photo`, `enabled`) VALUES ("sesblog", "lightbox", "sesblog_album", "sesblog_photo", "album_id", "photo_id", 1);');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_favourites`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_favourites` (
        `favourite_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL,
        `resource_type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
        `resource_id` int(11) NOT NULL,
        PRIMARY KEY (`favourite_id`),
        KEY `user_id` (`user_id`,`resource_type`,`resource_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_roles`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_roles` (
        `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL,
        `blog_id` int(11) unsigned NOT NULL,
        PRIMARY KEY (`role_id`),
        KEY `user_id` (`blog_id`,`user_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_blogs`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_blogs` (
        `blog_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `custom_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
        `parent_id` int(11) DEFAULT "0",
        `photo_id` int(11) DEFAULT "0",
        `title` varchar(224) COLLATE utf8_unicode_ci NOT NULL,
        `body` longtext COLLATE utf8_unicode_ci NOT NULL,
        `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
        `owner_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
        `owner_id` int(11) unsigned NOT NULL,
        `category_id` int(11) unsigned NOT NULL DEFAULT "0",
        `subcat_id` int(11) DEFAULT "0",
        `subsubcat_id` int(11) DEFAULT "0",
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `publish_date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
        `starttime` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
        `endtime` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
        `view_count` int(11) unsigned NOT NULL DEFAULT "0",
        `comment_count` int(11) unsigned NOT NULL DEFAULT "0",
        `like_count` int(11) unsigned NOT NULL DEFAULT "0",
        `blog_contact_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
        `blog_contact_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
        `blog_contact_phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
        `blog_contact_website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
        `blog_contact_facebook` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
        `parent_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
        `seo_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
        `seo_keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
        `seo_description` text COLLATE utf8_unicode_ci,
        `featured` tinyint(1) NOT NULL DEFAULT "0",
        `sponsored` tinyint(1) NOT NULL DEFAULT "0",
        `verified` tinyint(1) NOT NULL DEFAULT "0",
        `is_approved` tinyint(1) NOT NULL DEFAULT "1",
        `ip_address` varchar(55) NOT NULL DEFAULT "0.0.0.0",
        `favourite_count` tinyint(11) NOT NULL DEFAULT "0",
        `offtheday` tinyint(1) NOT NULL,
        `style` tinyint(1) NOT NULL DEFAULT "1",
        `rating` float NOT NULL,
        `search` tinyint(1) NOT NULL DEFAULT "1",
        `draft` tinyint(1) unsigned NOT NULL DEFAULT "0",
        `is_publish` tinyint(1) NOT NULL DEFAULT "0",
        `readtime` VARCHAR(64) NULL,
        PRIMARY KEY (`blog_id`),
        KEY `owner_type` (`owner_type`,`owner_id`),
        KEY `search` (`search`,`creation_date`),
        KEY `owner_id` (`owner_id`,`draft`),
        KEY `draft` (`draft`,`search`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_albums`;');
			$db->query('CREATE TABLE `engine4_sesblog_albums` (
        `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `blog_id` int(11) unsigned NOT NULL,
        `owner_id` int(11) UNSIGNED NOT NULL,
        `title` varchar(128) NOT NULL,
        `description` mediumtext NOT NULL,
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `search` tinyint(1) NOT NULL default "1",
        `photo_id` int(11) unsigned NOT NULL default "0",
        `view_count` int(11) unsigned NOT NULL default "0",
        `comment_count` int(11) unsigned NOT NULL default "0",
        `collectible_count` int(11) unsigned NOT NULL default "0",
        `like_count` int(11) NOT NULL DEFAULT "0",
        `position_cover` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
        `art_cover` int(11) NOT NULL DEFAULT "0",
        `favourite_count` int(11) UNSIGNED NOT NULL DEFAULT "0",
        PRIMARY KEY (`album_id`),
        KEY `blog_id` (`blog_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_photos`;');
			$db->query('CREATE TABLE `engine4_sesblog_photos` (
        `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `album_id` int(11) unsigned NOT NULL,
        `blog_id` int(11) unsigned NOT NULL,
        `user_id` int(11) unsigned NOT NULL,
        `title` varchar(128) NOT NULL,
        `description` varchar(255) NOT NULL,
        `collection_id` int(11) unsigned NOT NULL,
        `file_id` int(11) unsigned NOT NULL,
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `view_count` int(11) UNSIGNED NOT NULL DEFAULT "0",
        `comment_count` int(11) UNSIGNED NOT NULL DEFAULT "0",
        `like_count` int(11) UNSIGNED NOT NULL DEFAULT "0",
        `order` int(11) NOT NULL DEFAULT "0",
        `position_cover` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
        `art_cover` int(11) NOT NULL DEFAULT "0",
        `favourite_count` int(11) UNSIGNED NOT NULL DEFAULT "0",
        PRIMARY KEY (`photo_id`),
        KEY `album_id` (`album_id`),
        KEY `blog_id` (`blog_id`),
        KEY `user_id` (`user_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_mapevents`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_mapevents` (
        `mapevent_id` int(11) NOT NULL AUTO_INCREMENT,
        `event_id` int(11) unsigned NOT NULL,
        `blog_id` int(11) unsigned NOT NULL,
        `request_owner_blog` tinyint(1) NOT NULL,
        `request_owner_event` tinyint(1) NOT NULL,
        `approved` tinyint(1) NOT NULL DEFAULT "1",
        PRIMARY KEY (`mapevent_id`),
        KEY `event_id` (`event_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_categories`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_categories` (
        `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) unsigned NOT NULL,
        `category_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
        `description` text COLLATE utf8_unicode_ci,
        `order` int(11) NOT NULL DEFAULT "0",
        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
        `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
        `subcat_id` int(11) DEFAULT "0",
        `subsubcat_id` int(11) DEFAULT "0",
        `thumbnail` int(11) NOT NULL DEFAULT "0",
        `cat_icon` int(11) NOT NULL DEFAULT "0",
        `colored_icon` int(11) NOT NULL,
        `color` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
        `profile_type_review` int(11) DEFAULT NULL,
        `profile_type` int(11) DEFAULT NULL,
        PRIMARY KEY (`category_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query("INSERT IGNORE INTO `engine4_sesblog_categories` (`category_id`, `user_id`, `category_name`, `description`, `order`, `title`, `slug`, `subcat_id`, `subsubcat_id`, `thumbnail`, `cat_icon`, `colored_icon`, `color`, `profile_type_review`, `profile_type`) VALUES (1, 1, 'Arts & Culture', '', 11, 'Arts & Culture', 'arts-culture', 0, 0, 0, 0, 0, NULL, 0, 0),(2, 1, 'Business', '', 10, 'Business', 'business', 0, 0, 0, 0, 0, NULL, NULL, 0),(3, 1, 'Entertainment', '', 9, 'Entertainment', 'entertainment', 0, 0, 0, 0, 0, NULL, NULL, 0),(5, 1, 'Family & Home', '', 8, 'Family & Home', 'family-home', 0, 0, 0, 0, 0, NULL, NULL, 0),(6, 1, 'Health', '', 7, 'Health', 'health', 0, 0, 0, 0, 0, NULL, NULL, 0),(7, 1, 'Recreation', '', 6, 'Recreation', 'recreation', 0, 0, 0, 0, 0, NULL, NULL, 0),(8, 1, 'Personal', '', 5, 'Personal', 'personal', 0, 0, 0, 0, 0, NULL, NULL, 0),(9, 1, 'Shopping', '', 4, 'Shopping', 'shopping', 0, 0, 0, 0, 0, NULL, NULL, 0),(10, 1, 'Society', '', 3, 'Society', 'society', 0, 0, 0, 0, 0, NULL, NULL, 0),(11, 1, 'Sports', '', 2, 'Sports', 'sports', 0, 0, 0, 0, 0, NULL, NULL, 0),(12, 1, 'Technology', '', 1, 'Technology', 'technology', 0, 0, 0, 0, 0, NULL, NULL, 0),(13, 1, 'Other', '', 0, 'Other', 'other', 0, 0, 0, 0, 0, NULL, NULL, 0)");
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_dashboards` ;');
			$db->query('CREATE TABLE `engine4_sesblog_dashboards` (
        `dashboard_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `type` varchar(128) NOT NULL,
        `title` varchar(128) NOT NULL,
        `enabled` tinyint(1) NOT NULL default "1",
        `main` tinyint(1) NOT NULL default "0",
        PRIMARY KEY (`dashboard_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('INSERT IGNORE INTO `engine4_sesblog_dashboards` (`type`, `title`, `enabled`, `main`) VALUES
      ("manage_blog", "Manage Blog", "1", "1"),
      ("edit_blog", "Edit Blog", "1", "0"),
      ("edit_photo", "Edit Photo", "1", "0"),
      ("blog_role", "Blog Roles", "1", "0"),
      ("manage_blog_video", "Manage Videos", "1", "0"),
      ("manage_blog_music", "Manage Music Albums", "1", "0"),
      ("change_owner", "Transfer Ownership", 1, 0),
      ("manage_blog_albums", "Manage Albums", "1", "0"),
      ("contact_information", "Contact Information", "1", "0"),
      ("edit_style", "Edit Style", "1", "0"),
      ("edit_location", "Edit Location", "1", "0"),
      ("seo", "Seo Details", "1", "0");');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_claims` ;');
			$db->query('CREATE TABLE `engine4_sesblog_claims` (
        `claim_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `blog_id` int(11) NOT NULL,
        `title` varchar(128) NOT NULL,
        `user_email` varchar(128) NOT NULL,
        `user_name` varchar(128) NOT NULL,
        `contact_number` varchar(128) NOT NULL,
        `description` text COLLATE utf8_unicode_ci,
        `creation_date` datetime NOT NULL,
        `status` tinyint(1) NOT NULL default "0",
        PRIMARY KEY (`claim_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_subscriptions`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_subscriptions` (
        `subscription_id` int(10) unsigned NOT NULL auto_increment,
        `user_id` int(10) unsigned NOT NULL,
        `subscriber_user_id` int(10) unsigned NOT NULL,
        PRIMARY KEY  (`subscription_id`),
        UNIQUE KEY `user_id` (`user_id`,`subscriber_user_id`),
        KEY `subscriber_user_id` (`subscriber_user_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_blog_fields_maps`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_blog_fields_maps` (
        `field_id` int(11) NOT NULL,
        `option_id` int(11) NOT NULL,
        `child_id` int(11) NOT NULL,
        `order` smallint(6) NOT NULL,
        PRIMARY KEY (`field_id`,`option_id`,`child_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
			$db->query('INSERT IGNORE INTO `engine4_sesblog_blog_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES (0, 0, 1, 1);');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_blog_fields_meta`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_blog_fields_meta` (
        `field_id` int(11) NOT NULL AUTO_INCREMENT,
        `type` varchar(24) NOT NULL,
        `label` varchar(64) NOT NULL,
        `description` varchar(255) NOT NULL DEFAULT "",
        `alias` varchar(32) NOT NULL DEFAULT "",
        `required` tinyint(1) NOT NULL DEFAULT "0",
        `display` tinyint(1) unsigned NOT NULL,
        `publish` tinyint(1) unsigned NOT NULL DEFAULT "0",
        `search` tinyint(1) unsigned NOT NULL DEFAULT "0",
        `show` tinyint(1) unsigned DEFAULT "0",
        `order` smallint(3) unsigned NOT NULL DEFAULT "999",
        `config` text NOT NULL,
        `validators` text COLLATE utf8_unicode_ci,
        `filters` text COLLATE utf8_unicode_ci,
        `style` text COLLATE utf8_unicode_ci,
        `error` text COLLATE utf8_unicode_ci,
        PRIMARY KEY (`field_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');
			$db->query('INSERT IGNORE INTO `engine4_sesblog_blog_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `show`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES (1, "profile_type", "Profile Type", "", "profile_type", 1, 0, 0, 2, 0, 999, "", NULL, NULL, NULL, NULL);');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_blog_fields_options`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_blog_fields_options` (
        `option_id` int(11) NOT NULL AUTO_INCREMENT,
        `field_id` int(11) NOT NULL,
        `label` varchar(255) NOT NULL,
        `order` smallint(6) NOT NULL DEFAULT "999",
        PRIMARY KEY (`option_id`),
        KEY `field_id` (`field_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('INSERT IGNORE INTO `engine4_sesblog_blog_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES (1, 1, "Rock Blogs", 0);');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_blog_fields_search`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_blog_fields_search` (
        `item_id` int(11) NOT NULL,
        `profile_type` smallint(11) unsigned DEFAULT NULL,
        PRIMARY KEY (`item_id`),
        KEY `profile_type` (`profile_type`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_blog_fields_values`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_blog_fields_values` (
        `item_id` int(11) NOT NULL,
        `field_id` int(11) NOT NULL,
        `index` smallint(3) NOT NULL DEFAULT "0",
        `value` text NOT NULL,
        PRIMARY KEY (`item_id`,`field_id`,`index`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; ');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_parameters`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_parameters` (
        `parameter_id` int(11) NOT NULL AUTO_INCREMENT,
        `category_id` int(11) NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `rating` float NOT NULL,
        PRIMARY KEY (`parameter_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `priority`) VALUES
      ("SNS - Advanced Blog - Rebuild Privacy", "sesblog_maintenance_rebuild_privacy", "sesblog", "Sesblog_Plugin_Job_Maintenance_RebuildPrivacy", 50);');
			$db->query('INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
      ("sesblog_main", "standard", "SNS - Advanced Blog - Main Navigation Menu"),
      ("sesblog_quick", "standard", "SNS - Advanced Blog - Quick Navigation Menu"),
      ("sesblog_gutter", "standard", "SNS - Advanced Blog - Gutter Navigation Menu"),
      ("sesblogreview_profile", "standard", "SNS - Advanced Blog - Review Profile Options Menu");');

			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
			("sesblogreview_profile_edit", "sesblog", "Edit Review", "Sesblog_Plugin_Menus", "", "sesblogreview_profile", "", 1),
      ("sesblogreview_profile_delete", "sesblog", "Delete Review", "Sesblog_Plugin_Menus", "", "sesblogreview_profile", "", 2),
      ("sesblogreview_profile_report", "sesblog", "Report", "Sesblog_Plugin_Menus", "", "sesblogreview_profile", "", 3),
      ("sesblogreview_profile_share", "sesblog", "Share", "Sesblog_Plugin_Menus", "", "sesblogreview_profile", "", 4),
			("core_main_sesblog", "sesblog", "Blogs", "", \'{"route":"sesblog_general"}\', "core_main", "", 4),
      ("core_sitemap_sesblog", "sesblog", "Blogs", "", \'{"route":"sesblog_general"}\', "core_sitemap", "", 4),
      ("mobi_browse_sesblog", "sesblog", "Blogs", "", \'{"route":"sesblog_general"}\', "mobi_browse", "", 3),
      ("sesblog_main_browsehome", "sesblog", "Blog Home", "", \'{"route":"sesblog_general","action":"home"}\', "sesblog_main", "", 1),
      ("sesblog_main_browsecategory", "sesblog", "Browse Categories", "", \'{"route":"sesblog_category"}\', "sesblog_main", "", 3),


      ("sesblog_main_browse", "sesblog", "Browse Blogs", "Sesblog_Plugin_Menus::canViewSesblogs", \'{"route":"sesblog_general","action":"browse"}\', "sesblog_main", "", 2),
      ("sesblog_main_location", "sesblog", "Locations", "Sesblog_Plugin_Menus::locationEnable", \'{"route":"sesblog_general","action":"locations"}\', "sesblog_main", "", 4),
      ("sesblog_main_claim", "sesblog", "Claim For Blog", "Sesblog_Plugin_Menus::canClaimSesblogs", \'{"route":"sesblog_general","action":"claim"}\', "sesblog_main", "", 5),
      ("sesblog_main_reviews", "sesblog", "Blog Reviews", "Sesblog_Plugin_Menus::reviewEnable", \'{"route":"sesblog_review","action":"browse"}\', "sesblog_main", "", 6),
      ("sesblog_main_manage", "sesblog", "My Blogs", "Sesblog_Plugin_Menus::canCreateSesblogs", \'{"route":"sesblog_general","action":"manage"}\', "sesblog_main", "", 7),
      ("sesblog_main_create", "sesblog", "Write New Blog", "Sesblog_Plugin_Menus::canCreateSesblogs", \'{"route":"sesblog_general","action":"create"}\', "sesblog_main", "", 8),
      ("sesblog_quick_create", "sesblog", "Write New Blog", "Sesblog_Plugin_Menus::canCreateSesblogs", \'{"route":"sesblog_general","action":"create","class":"buttonlink icon_sesblog_new"}\', "sesblog_quick", "", 1),


      ("sesblog_quick_style", "sesblog", "Edit Blog Style", "Sesblog_Plugin_Menus", \'{"route":"sesblog_general","action":"style","class":"smoothbox buttonlink icon_sesblog_style"}\', "sesblog_quick", "", 2),
      ("sesblog_gutter_list", "sesblog", "View All Blogs", "Sesblog_Plugin_Menus", \'{"route":"sesblog_view","class":"buttonlink icon_sesblog_viewall"}\', "sesblog_gutter", "", 1),
      ("sesblog_gutter_create", "sesblog", "Write New Blog", "Sesblog_Plugin_Menus", \'{"route":"sesblog_general","action":"create","class":"buttonlink icon_sesblog_new"}\', "sesblog_gutter", "", 2),
      ("sesblog_gutter_subblog_create", "sesblog", "Create Sub Blog", "Sesblog_Plugin_Menus", \'{"route":"sesblog_general","action":"create","class":"buttonlink icon_sesblog_new"}\', "sesblog_gutter", "", 3),
      ("sesblog_gutter_dashboard", "sesblog", "Dashboard", "Sesblog_Plugin_Menus", \'{"route":"sesblog_dashboard","action":"edit","class":"buttonlink icon_sesblog_edit"}\', "sesblog_gutter", "", 4),
      ("sesblog_gutter_delete", "sesblog", "Delete This Blog", "Sesblog_Plugin_Menus", \'{"route":"sesblog_specific","action":"delete","class":"buttonlink smoothbox icon_sesblog_delete"}\', "sesblog_gutter", "", 5),
      ("sesblog_gutter_share", "sesblog", "Share", "Sesblog_Plugin_Menus", \'{"route":"default","module":"activity","controller":"index","action":"share","class":"buttonlink smoothbox icon_comments"}\', "sesblog_gutter", "", 6),
      ("sesblog_gutter_report", "sesblog", "Report", "Sesblog_Plugin_Menus", \'{"route":"default","module":"core","controller":"report","action":"create","class":"buttonlink smoothbox icon_report"}\', "sesblog_gutter", "", 7),
      ("sesblog_gutter_subscribe", "sesblog", "Subscribe", "Sesblog_Plugin_Menus", \'{"route":"default","module":"sesblog","controller":"subscription","action":"add","class":"buttonlink smoothbox icon_sesblog_subscribe"}\', "sesblog_gutter", "", 8),

      ("sesblog_admin_main_manage", "sesblog", "Manage Blogs", "", \'{"route":"admin_default","module":"sesblog","controller":"manage"}\', "sesblog_admin_main", "", 2),
      ("sesblog_admin_main_blogsettings", "sesblog", "Blog Creation Settings", "", \'{"route":"admin_default","module":"sesblog","controller":"settings", "action":"createsettings"}\', "sesblog_admin_main", "", 2),      
      ("sesblog_admin_main_level", "sesblog", "Member Level Settings", "", \'{"route":"admin_default","module":"sesblog","controller":"level"}\', "sesblog_admin_main", "", 3),
      ("sesblog_admin_main_categories", "sesblog", "Categories", "", \'{"route":"admin_default","module":"sesblog","controller":"categories","action":"index"}\', "sesblog_admin_main", "", 4),
      ("sesblog_admin_main_subcategories", "sesblog", "Categories", "", \'{"route":"admin_default","module":"sesblog","controller":"categories","action":"index"}\', "sesblog_admin_categories", "", 1),
      ("sesblog_admin_main_subfields", "sesblog", "Form Questions", "", \'{"route":"admin_default","module":"sesblog","controller":"fields"}\', "sesblog_admin_categories", "", 2),
      ("sesblog_admin_main_reviewsettings", "sesblog", "Review Settings", "", \'{"route":"admin_default","module":"sesblog","controller":"review", "action":"review-settings"}\', "sesblog_admin_main", "", 5),
      ("sesblog_admin_main_review_settings", "sesblog", "Review & Rating Settings", "",\'{"route":"admin_default","module":"sesblog","controller":"review", "action":"review-settings"}\', "sesblog_admin_main_reviewsetting", "", 1),
      ("sesblog_admin_main_managereview", "sesblog", "Manage Reviews", "", \'{"route":"admin_default","module":"sesblog","controller":"review", "action":"manage-reviews"}\', "sesblog_admin_main_reviewsetting", "", 2),
      ("sesblog_admin_main_levelsettings", "sesblog", "Member Level Setting", "", \'{"route":"admin_default","module":"sesblog","controller":"review", "action":"level-settings"}\', "sesblog_admin_main_reviewsetting", "", 3),
      ("sesblog_admin_main_review_cat", "sesblog", "Rating Parameters", "", \'{"route":"admin_default","module":"sesblog","controller":"review-categories","action":"index"}\', "sesblog_admin_main_reviewsetting", "", 4),
      ("sesblog_admin_main_review_subcategories", "sesblog", "Categories & Mapping", "", \'{"route":"admin_default","module":"sesblog","controller":"review-categories","action":"index"}\', "sesblog_admin_main_review_cat", "", 1),
      ("sesblog_admin_main_review_subfields", "sesblog", "Form Questions", "", \'{"route":"admin_default","module":"sesblog","controller":"review-fields"}\', "sesblog_admin_main_review_cat", "", 2),
      ("sesblog_admin_main_claim", "sesblog", "Claim Requests", "", \'{"route":"admin_default","module":"sesblog","controller":"manage", "action":"claim"}\', "sesblog_admin_main", "", 6),
      ("sesblog_admin_main_statistic", "sesblog", "Statistics", "", \'{"route":"admin_default","module":"sesblog","controller":"settings","action":"statistic"}\', "sesblog_admin_main", "", 7),
      ("sesblog_admin_main_managepages", "sesblog", "Widgetized Pages", "", \'{"route":"admin_default","module":"sesblog","controller":"settings", "action":"manage-widgetize-page"}\', "sesblog_admin_main", "", 999);');

			$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("sesblog_new", "sesblog", \'{item:$subject} wrote a new blog entry:\', 1, 5, 1, 3, 1, 1), ("comment_sesblog", "sesblog", \'{item:$subject} commented on {item:$owner}\'\'s {item:$object:blog entry}: {body:$body}\', 1, 1, 1, 1, 1, 0),
			("sesblog_blog_like", "sesblog", \'{item:$subject} likes the blog {item:$object}:\', 1, 7, 1, 1, 1, 1),
      ("sesblog_album_like", "sesblog", \'{item:$subject} likes the blog album {item:$object}:\', 1, 7, 1, 1, 1, 1),
      ("sesblog_photo_like", "sesblog", \'{item:$subject} likes the blog photo {item:$object}:\', 1, 7, 1, 1, 1, 1),
      ("sesblog_blog_favourite", "sesblog", \'{item:$subject} added blog {item:$object} to favorite:\', 1, 7, 1, 1, 1, 1);');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_categorymappings`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_categorymappings` (
        `categorymapping_id` int(11) NOT NULL AUTO_INCREMENT,
        `module_name` varchar(64) NOT NULL,
        `category_id` int(11) NOT NULL,
        `profiletype_id` int(11) NOT NULL,
        `profile_type` varchar(255) NOT NULL,
        PRIMARY KEY (`categorymapping_id`),
        KEY `category_id` (`category_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_reviews`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_reviews` (
        `review_id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
        `pros` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
        `cons` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
        `description` text COLLATE utf8_unicode_ci NOT NULL,
        `recommended` tinyint(1) NOT NULL DEFAULT "1",
        `owner_id` int(11) unsigned NOT NULL,
        `blog_id` int(11) unsigned NOT NULL DEFAULT "0",
        `creation_date` datetime NOT NULL,
        `modified_date` datetime NOT NULL,
        `like_count` int(11) NOT NULL,
        `comment_count` int(11) NOT NULL,
        `view_count` int(11) NOT NULL,
        `rating` tinyint(1) DEFAULT NULL,
        `featured` tinyint(1) NOT NULL DEFAULT "0",
        `sponsored` tinyint(1) NOT NULL DEFAULT "0",
        `verified` tinyint(1) NOT NULL DEFAULT "0",
        `oftheday` tinyint(1) DEFAULT "0",
        `starttime` datetime DEFAULT NULL,
        `endtime` datetime DEFAULT NULL,
        PRIMARY KEY (`review_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_review_fields_maps`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_review_fields_maps` (
        `field_id` int(11) NOT NULL,
        `option_id` int(11) NOT NULL,
        `child_id` int(11) NOT NULL,
        `order` smallint(6) NOT NULL,
        PRIMARY KEY (`field_id`,`option_id`,`child_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
			$db->query('INSERT IGNORE INTO `engine4_sesblog_review_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES (0, 0, 1, 1);');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_review_fields_meta`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_review_fields_meta` (
        `field_id` int(11) NOT NULL AUTO_INCREMENT,
        `type` varchar(24) NOT NULL,
        `label` varchar(64) NOT NULL,
        `description` varchar(255) NOT NULL DEFAULT "",
        `alias` varchar(32) NOT NULL DEFAULT "",
        `required` tinyint(1) NOT NULL DEFAULT "0",
        `display` tinyint(1) unsigned NOT NULL,
        `publish` tinyint(1) unsigned NOT NULL DEFAULT "0",
        `search` tinyint(1) unsigned NOT NULL DEFAULT "0",
        `show` tinyint(1) unsigned DEFAULT "0",
        `order` smallint(3) unsigned NOT NULL DEFAULT "999",
        `config` text NOT NULL,
        `validators` text COLLATE utf8_unicode_ci,
        `filters` text COLLATE utf8_unicode_ci,
        `style` text COLLATE utf8_unicode_ci,
        `error` text COLLATE utf8_unicode_ci,
        PRIMARY KEY (`field_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');
			$db->query('INSERT IGNORE INTO `engine4_sesblog_review_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `show`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES (1, "profile_type", "Profile Type", "", "profile_type", 1, 0, 0, 2, 0, 999, "", NULL, NULL, NULL, NULL);');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_review_fields_options`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_review_fields_options` (
      `option_id` int(11) NOT NULL AUTO_INCREMENT,
      `field_id` int(11) NOT NULL,
      `label` varchar(255) NOT NULL,
      `order` smallint(6) NOT NULL DEFAULT "999",
      PRIMARY KEY (`option_id`),
      KEY `field_id` (`field_id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('INSERT IGNORE INTO `engine4_sesblog_review_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES (1, 1, "Default", 0);');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_review_fields_search`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_review_fields_search` (
      `item_id` int(11) NOT NULL,
      `profile_type` smallint(11) unsigned DEFAULT NULL,
      PRIMARY KEY (`item_id`),
      KEY `profile_type` (`profile_type`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_review_fields_values`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_review_fields_values` (
      `item_id` int(11) NOT NULL,
      `field_id` int(11) NOT NULL,
      `index` smallint(3) NOT NULL DEFAULT "0",
      `value` text NOT NULL,
      PRIMARY KEY (`item_id`,`field_id`,`index`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesblog_review_parametervalues`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblog_review_parametervalues` (
        `parametervalue_id` int(11) NOT NULL AUTO_INCREMENT,
        `parameter_id` int(11) NOT NULL,
        `rating` float NOT NULL,
        `user_id` INT(11) NOT NULL,
        `resources_id` INT(11) NOT NULL,
        `content_id` INT(11) NOT NULL,
        PRIMARY KEY (`parametervalue_id`),
        UNIQUE KEY `uniqueKey` (`parameter_id`,`user_id`,`resources_id`,`content_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
			$db->query('INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`) VALUES ("SNS - Advanced Blog - Publish", "sesblog", "Sesblog_Plugin_Task_Publish", 15);');

      $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
      ("sesblog_subscribed_new", "sesblog", \'{item:$subject} has posted a new blog entry: {item:$object}.\', 0, ""),
      ("sesblog_link_event", "sesblog", \'{item:$subject} wants to add blog: {var:$blogPageLink} in your event : {item:$object}.\', 0, ""),
      ("sesblog_link_blog", "sesblog", \'{item:$subject} wants to add blog: {var:$blogPageLink} in event : {item:$object}.\', 0, ""),
      ("sesuser_claim_blog", "sesblog", \'{item:$subject} has claimed your blog {item:$object}.\', 0, ""),
      ("sesuser_claimadmin_blog", "sesblog", \'{item:$subject} has claimed a blog {item:$object}.\', 0, ""),
      ("sesblog_claim_approve", "sesblog", \'Site admin has approved your claim request for the blog: {item:$object}.\', 0, ""),
      ("sesblog_claim_declined", "sesblog", \'Site admin has rejected your claim request for the blog: {item:$object}.\', 0, ""),
      ("sesblog_owner_informed", "sesblog", \'Site admin has been approved claim for your blog: {item:$object}.\', 0, ""),
      ("sesblog_reject_event_request", "sesblog", \'{item:$subject} has been rejected to add blog: {var:$blogPageLink} in your event : {item:$object}.\', 0, ""),
      ("sesblog_reject_blog_request", "sesblog", \'{item:$subject} has been rejected to add blog: {var:$blogPageLink} in event : {item:$object}.\', 0, "");');
      
      include_once APPLICATION_PATH . "/application/modules/Sesblog/controllers/defaultsettings.php";
      include_once APPLICATION_PATH . "/application/modules/Sesblogpackage/controllers/License.php";

      Engine_Api::_()->getApi('settings', 'core')->setSetting('sesblog.pluginactivated', 1);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sesblog.licensekey', $_POST['sesblog_licensekey']);
    }
    $domain_name = @base64_encode(str_replace(array('http://','https://','www.'),array('','',''),$_SERVER['HTTP_HOST']));
    $licensekey = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.licensekey');
    $licensekey = @base64_encode($licensekey);
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sesblog.sesdomainauth', $domain_name);
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sesblog.seslkeyauth', $licensekey);
    $error = 1;
  } else {
    $error = $this->view->translate('Please enter correct License key for this product.');
    $error = Zend_Registry::get('Zend_Translate')->_($error);
    $form->getDecorator('errors')->setOption('escape', false);
    $form->addError($error);
    $error = 0;
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sesblog.licensekey', $_POST['sesblog_licensekey']);
    return;
    $this->_helper->redirector->gotoRoute(array());
  }
}
