-- --------------------------------------------------------

--
-- Table structure for table `engine4_user_settings`
--

DROP TABLE IF EXISTS `engine4_siteuseravatar_avatars`;
CREATE TABLE IF NOT EXISTS `engine4_siteuseravatar_avatars` (
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `avatar_id` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;