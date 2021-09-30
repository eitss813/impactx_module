--
-- Dumping data for table `engine4_core_modules`
--


INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitealbum', 'Advanced Photo Albums', 'Advanced Photo Albums Plugin', '5.0.0p1', 1, 'extra') ;
--
-- Change the Commentable & Shareable values
--

UPDATE engine4_activity_actiontypes SET commentable=3,shareable=3 WHERE (type='comment_album' or type = 'comment_album_photo') and module='sitealbum';