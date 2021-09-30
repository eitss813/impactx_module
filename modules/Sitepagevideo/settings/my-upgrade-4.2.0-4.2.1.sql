UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepagevideo_home","action":"home"}' WHERE `engine4_core_menuitems`.`name` = 'sitepage_main_video' LIMIT 1 ;
UPDATE `engine4_core_pages` SET `name` = 'sitepagevideo_index_browse' WHERE `engine4_core_pages`.`name` ='sitepagevideo_index_videolist' LIMIT 1 ;

INSERT IGNORE INTO `engine4_seaocore_tabs` (`module` ,`type` ,`name` ,`title` ,`enabled` ,`order` ,`limit`)VALUES
('sitepagevideo', 'videos', 'recent_pagevideos', 'Recent', '1', '1', '24'),
('sitepagevideo', 'videos', 'liked_pagevideos', 'Most Liked', '1', '2', '24'),
('sitepagevideo', 'videos', 'viewed_pagevideos', 'Most Viewed', '1', '3', '24'),
('sitepagevideo', 'videos', 'commented_pagevideos', 'Most Commented', '0', '4', '24'),
('sitepagevideo', 'videos', 'featured_pagevideos', 'Featured', '0', '5', '24'),
('sitepagevideo', 'videos', 'random_pagevideos', 'Random', '0', '6', '24');