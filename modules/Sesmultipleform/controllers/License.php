<?php

//folder name or directory name.
$module_name = 'sesmultipleform';

//product title and module title.
$module_title = 'All in One Multiple Forms Plugin - Advanced Contact Us, Feedback, Query Forms, etc';

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
  $postdata['licenseKey'] = @base64_encode($_POST['sesmultipleform_licensekey']);
  $postdata['module_name'] = @base64_encode($module_name);
  $postdata['module_title'] = @base64_encode($module_title);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://socialnetworking.solutions/licensenewcheck.php");
  curl_setopt($ch, CURLOPT_POST, 1);
  //in real life you should use something like:
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
  //receive server response ...
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
  
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmultipleform.pluginactivated')) {
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      
			$db->query('DROP TABLE IF EXISTS `engine4_sesmultipleform_categories`;');
			$db->query('CREATE TABLE `engine4_sesmultipleform_categories` (
			`category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`subcat_id` int(11) DEFAULT "0",
			`subsubcat_id` int(11) DEFAULT "0",
			`form_id` int(11) NOT NULL,
			`user_id` int(11) UNSIGNED NOT NULL,
			`order` int(11) NOT NULL DEFAULT "0",
			`title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`profile_type` int(11) DEFAULT NULL,
			PRIMARY KEY (`category_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
			$sitetitle = $_SERVER['HTTP_HOST'];
			$db->query("INSERT INTO `engine4_sesmultipleform_categories` (`category_id`, `subcat_id`, `subsubcat_id`, `form_id`, `user_id`, `order`, `title`, `profile_type`) VALUES
									(9, 0, 0, 1, 0, 3, 'Site Feature', 5),
									(10, 0, 0, 1, 0, 1, 'Smart Features', 0),
									(11, 0, 0, 1, 0, 2, 'Payments', 0),
									(12, 0, 0, 1, 0, 0, 'Support & Feedback', 0),
									(13, 0, 0, 1, 0, 4, 'My Account, Login & Notifications', 0),
									(14, 0, 0, 2, 0, 4, 'Customer Support', 0),
									(15, 0, 0, 2, 0, 3, 'Experience on Site', 2),
									(17, 15, 0, 2, 0, 1, 'I love this Site', 0),
									(18, 15, 0, 2, 0, 0, 'Report a Site Bug', 0),
									(19, 0, 0, 2, 0, 2, 'Improve a Page', 3),
									(20, 0, 0, 2, 0, 1, 'Others - General Feedback', 4),
									(21, 0, 0, 2, 0, 0, 'Suggest New Feature Idea', 1),
									(22, 0, 18, 2, 0, 1, 'Page Break', 0)");
			$db->query('DROP TABLE IF EXISTS `engine4_sesmultipleform_entry_fields_maps`;');
			$db->query('CREATE TABLE `engine4_sesmultipleform_entry_fields_maps` (
			  `field_id` int(11) NOT NULL,
			  `option_id` int(11) NOT NULL,
			  `child_id` int(11) NOT NULL,
			  `order` smallint(6) NOT NULL,
			  PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;');
			$db->query('INSERT INTO `engine4_sesmultipleform_entry_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
									(0, 0, 1, 1),
									(1, 1, 2, 9999),
									(1, 2, 3, 9999),
									(1, 3, 4, 9999),
									(1, 4, 5, 9999),
									(1, 5, 6, 1),
									(1, 5, 7, 2),
									(8, 10, 9, 2)');
			$db->query('DROP TABLE IF EXISTS `engine4_sesmultipleform_entry_fields_meta`;');
			$db->query('CREATE TABLE `engine4_sesmultipleform_entry_fields_meta` (
			  `field_id` int(11) NOT NULL auto_increment,
			  `type` varchar(24) collate latin1_general_ci NOT NULL,
			  `label` varchar(64) NOT NULL,
			  `description` varchar(255) NOT NULL default "",
			  `alias` varchar(32) NOT NULL default "",
			  `required` tinyint(1) NOT NULL default "0",
			  `display` tinyint(1) unsigned NOT NULL,
			  `search` tinyint(1) unsigned NOT NULL default "0",
			  `show` tinyint(1) unsigned NOT NULL default "1",
			  `order` smallint(3) unsigned NOT NULL default "999",
			  `config` text NOT NULL,
			  `validators` text NULL,
			  `filters` text NULL,
			  `style` text NULL,
			  `error` text NULL,
			  PRIMARY KEY  (`field_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;');
				$db->query("INSERT INTO `engine4_sesmultipleform_entry_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `search`, `show`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
				(1, 'profile_type', 'Profile Type', '', 'profile_type', 1, 0, 2, 0, 999, '', NULL, NULL, NULL, NULL),
				(2, 'text', 'Phone Number', '', '', 0, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
				(3, 'text', 'Specific URL', '', '', 0, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
				(4, 'text', 'URL', '', '', 0, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
				(5, 'text', 'Add a new Category', '', '', 0, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
				(6, 'radio', 'Liked Site Feature', 'Do you like any existing feature of this site?', '', 0, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
				(7, 'text', 'Liked / Disliked Feature', 'Which feature you Liked / Disliked on this site?', '', 0, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
				(9, 'text', 'Any Suggestions for Improvement?', '', '', 0, 0, 0, 0, 999, '[]', NULL, NULL, '', '');");

			$db->query('DROP TABLE IF EXISTS `engine4_sesmultipleform_entry_fields_options`;');
			$db->query('CREATE TABLE `engine4_sesmultipleform_entry_fields_options` (
			  `option_id` int(11) NOT NULL auto_increment,
			  `field_id` int(11) NOT NULL,
			  `label` varchar(255) NOT NULL,
			  `order` smallint(6) NOT NULL default "999",
			  PRIMARY KEY  (`option_id`),
			  KEY `field_id` (`field_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;');

			$db->query("INSERT INTO `engine4_sesmultipleform_entry_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES
					(1, 1, 'Suggest New Feature Ideas', 0),
					(2, 1, 'Experience', 999),
					(3, 1, 'Improve a Page', 999),
					(4, 1, 'Other', 999),
					(5, 1, 'Site Feature', 999),
					(6, 6, 'Yes', 2),
					(7, 6, 'No', 1),
					(9, 8, 'Yes', 1),
					(10, 8, 'No', 2),
					(21, 12, 'Yes', 1),
					(22, 12, 'No', 2),
					(23, 12, 'No, but treated to make curly.', 3)");

			$db->query('DROP TABLE IF EXISTS `engine4_sesmultipleform_entry_fields_values`;');
			$db->query('CREATE TABLE `engine4_sesmultipleform_entry_fields_values` (
			  `item_id` int(11) NOT NULL,
			  `field_id` int(11) NOT NULL,
			  `index` smallint(3) NOT NULL default "0",
			  `value` text NOT NULL,
			  PRIMARY KEY  (`item_id`,`field_id`,`index`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesmultipleform_entry_fields_search`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesmultipleform_entry_fields_search` (
			  `item_id` int(11) NOT NULL,
			  `price` double NULL,
			  `location` varchar(255) NULL,
			  PRIMARY KEY  (`item_id`),
			  KEY `price` (`price`),
			  KEY `location` (`location`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesmultipleform_forms`;');
			$db->query('CREATE TABLE `engine4_sesmultipleform_forms` (
			  `form_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `category_required` tinyint(1) NOT NULL DEFAULT "1",
			  `description` tinyint(1) NOT NULL DEFAULT "1",
			  `description_required` tinyint(1) NOT NULL DEFAULT "1",
			  `order` int(11) NOT NULL DEFAULT "0",
				`heading_text` VARCHAR(255) NULL,
				`heading_description` TEXT NULL,
				`label_name` varchar(255) NOT NULL DEFAULT "Name",
				`label_email` varchar(255) NOT NULL DEFAULT "Email",
				`label_description` varchar(255) NOT NULL DEFAULT "Description",
				`label_submit` varchar(255) NOT NULL DEFAULT "Submit",
				`label_file_upload` varchar(255) NOT NULL DEFAULT "File Upload",
				`label_category` varchar(255) NOT NULL DEFAULT "Category",
				`label_subcategory` varchar(255) NOT NULL DEFAULT "Sub Catgeory",
				`label_subsubcategory` varchar(255) NOT NULL DEFAULT "Sub Sub Category",
			  `active` tinyint(1) NOT NULL DEFAULT "1",
				`creation_date` datetime NOT NULL,
			  `modified_date` datetime NOT NULL,
			  PRIMARY KEY (`form_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
			$db->query("INSERT INTO `engine4_sesmultipleform_forms` (`form_id`, `title`, `category_required`, `description`, `description_required`, `order`, `heading_text`, `heading_description`, `label_name`, `label_email`, `label_description`, `label_submit`, `label_file_upload`, `label_category`, `label_subcategory`, `label_subsubcategory`, `active`, `creation_date`, `modified_date`) VALUES
			(1, 'Contact Us', 1, 1, 1, 8, 'How can we help?', 'If you need a helping hand, have a question or would like to give us your valued feedback, please get in touch using the form below. We’ll be happy to assist you in any way we can.', 'Name', 'Email', 'Message', 'Submit', 'File Upload', 'Category', '2nd-Level Catgeory', '3rd-Level Category', 1, '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."'),(2, 'Feedback', 0, 1, 1, 6, 'Let us know what you think.', 'Do you have suggestions / ideas / feature requests? Let us know how we can improve our website.', 'Name', 'Email', 'Message', 'Send Feedback', 'File Upload', 'Category', '2nd Level Catgeory', '3rd Level Category', 1, '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')");

			$db->query('DROP TABLE IF EXISTS `engine4_sesmultipleform_form_settings`;');
			$db->query('CREATE TABLE `engine4_sesmultipleform_form_settings` (
			  `setting_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `form_id` int(11) NOT NULL,
			  `message_reciver_email` varchar(255) DEFAULT NULL,
			  `show_captcha` tinyint(1) NOT NULL DEFAULT "1",
			  `send_copy` tinyint(1) NOT NULL DEFAULT "1",
			  `color_asterisk` varchar(51) NOT NULL DEFAULT "FF0000",
			  `display_file_upload` tinyint(1) NOT NULL DEFAULT "1",
				`display_file_upload_required` tinyint(1) NOT NULL DEFAULT "0",
			  `file_upload` tinyint(1) NOT NULL DEFAULT "2",
			  `enable_terms` tinyint(1) NOT NULL DEFAULT "1",
			  `success_message` text  NULL,
			  `ipaddress_ban` text NULL,
			  `email_confirmation` tinyint(1) NOT NULL DEFAULT "0",
			  `confirmation_subject` text NULL,
			  `confirmation_message` text NULL,
			   PRIMARY KEY (`setting_id`)
			) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;');
			$db->query("INSERT INTO `engine4_sesmultipleform_form_settings` (`setting_id`, `form_id`, `message_reciver_email`, `show_captcha`, `send_copy`, `color_asterisk`, `display_file_upload`, `display_file_upload_required`, `file_upload`, `enable_terms`, `success_message`, `ipaddress_ban`, `email_confirmation`, `confirmation_subject`, `confirmation_message`) VALUES
				(1, 1, '', 0, 1, 'FF0000', 1, 0, 2, 1, 'Thank you for contacting us! We will get back to you soon.', '', 0, 'Thank you for contacting Us!', 'Thank you for contacting Us! \r\nWe will get back to you soon.'),
				(2, 2, '', 0, 0, 'FF0000', 0, 1, 1, 0, 'Thank you for providing your valuable feedback to us!', '', 1, 'Thank you for your valuable Feedback!', 'We have received your feedback and it is really important for us. If needed any of your further assistance, then our team will contact you. &nbsp;')");

			$db->query('DROP TABLE IF EXISTS `engine4_sesmultipleform_entries`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesmultipleform_entries` (
			  `entry_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `first_name` varchar(255) NOT NULL,
				`form_id` INT(11) NOT NULL,
			  `owner_id` int(11) NULL DEFAULT "0",
			  `description` longtext NULL,
			  `email` varchar(255) NULL,
			  `category_id` int(11) UNSIGNED NOT NULL DEFAULT "0",
			  `subcat_id` int(11) UNSIGNED NOT NULL DEFAULT "0",
			  `subsubcat_id` int(11) UNSIGNED NOT NULL DEFAULT "0",
			  `parent_id` int(11) NOT NULL,
			  `file_id` INT(11) NULL,
			  `ip_address` varchar(255) NOT NULL,
			  `creation_date` datetime NOT NULL,
			  PRIMARY KEY (`entry_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;');
			$db->query('DROP TABLE IF EXISTS `engine4_sesmultipleform_keycontacts`;');
			$db->query('CREATE TABLE `engine4_sesmultipleform_keycontacts` (
			  `keycontact_id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `designation` varchar(255) NOT NULL,
			  `active` tinyint(1) NOT NULL DEFAULT "1",
			  `order` tinyint(10) NOT NULL DEFAULT "0",
			  `creation_date` datetime NOT NULL,
			  `modified_date` datetime NOT NULL,
			   PRIMARY KEY (`keycontact_id`)
			) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;');
			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
			("sesmultipleform_admin_main_categories", "sesmultipleform", "Categories", "", \'{"route":"admin_default","module":"sesmultipleform","controller":"categories","action":"index"}\', "sesmultipleform_admin_main", "", 3),
			("sesmultipleform_admin_main_fields", "sesmultipleform", "Custom Fields", "", \'{"route":"admin_default","module":"sesmultipleform","controller":"fields"}\', "sesmultipleform_admin_main", "", 4),
			("sesmultipleform_admin_main_forms", "sesmultipleform", "Manage Forms", "", \'{"route":"admin_default","module":"sesmultipleform","controller":"forms"}\', "sesmultipleform_admin_main", "", 2),
			("sesmultipleform_admin_main_contacts", "sesmultipleform", "Key Contacts", "", \'{"route":"admin_default","module":"sesmultipleform","controller":"contacts","action":"index"}\', "sesmultipleform_admin_main", "", 5),
			("sesmultipleform_admin_main_aboutus", "sesmultipleform", "About Us", "", \'{"route":"admin_default","module":"sesmultipleform","controller":"settings","action":"aboutus"}\', "sesmultipleform_admin_main", "", 6),
			("sesmultipleform_mini_aboutus", "sesmultipleform", "About Us", "", \'{"route":"sesmultipleform_aboutus","module":"sesmultipleform","controller":"index","action":"aboutus"}\', "core_mini", "", 999),
			("sesmultipleform_main_aboutus", "sesmultipleform", "About Us", "", \'{"route":"sesmultipleform_aboutus","module":"sesmultipleform","controller":"index","action":"aboutus"}\', "core_main", "", 999),
			("sesmultipleform_footer_aboutus", "sesmultipleform", "About Us", "", \'{"route":"sesmultipleform_aboutus","module":"sesmultipleform","controller":"index","action":"aboutus"}\', "core_footer", "", 999);');
			
      include_once APPLICATION_PATH . "/application/modules/Sesmultipleform/controllers/defaultsettings.php";
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sesmultipleform.pluginactivated', 1);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sesmultipleform.licensekey', $_POST['sesmultipleform_licensekey']);
    }
    $domain_name = @base64_encode($_SERVER['HTTP_HOST']);
		$licensekey = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmultipleform.licensekey');
		$licensekey = @base64_encode($licensekey);
		Engine_Api::_()->getApi('settings', 'core')->setSetting('sesmultipleform.sesdomainauth', $domain_name);
		Engine_Api::_()->getApi('settings', 'core')->setSetting('sesmultipleform.seslkeyauth', $licensekey);
		$error = 1;
  } else {
    $error = $this->view->translate('Please enter correct License key for this product.');
    $error = Zend_Registry::get('Zend_Translate')->_($error);
    $form->getDecorator('errors')->setOption('escape', false);
    $form->addError($error);
    $error = 0;
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sesmultipleform.licensekey', $_POST['sesmultipleform_licensekey']);
    return;
    $this->_helper->redirector->gotoRoute(array());
  }
}
