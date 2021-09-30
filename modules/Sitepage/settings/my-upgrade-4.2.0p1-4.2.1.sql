ALTER TABLE `engine4_sitepage_packages` ADD `level_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' AFTER `description`;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_mailtemplates`
--
 INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES
("sitepage_page_recurrence", "sitepage", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[page_title],[page_description],[object_link]");
