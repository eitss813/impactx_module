INSERT IGNORE INTO `engine4_seaocore_tabs` (`module` ,`type` ,`name` ,`title` ,`enabled` ,`order` ,`limit`)VALUES
('sitepagealbum', 'albums', 'recent_pagealbums', 'Recent', '1', '1', '24'),
('sitepagealbum', 'albums', 'liked_pagealbums', 'Most Liked', '1', '2', '24'),
('sitepagealbum', 'albums', 'viewed_pagealbums', 'Most Viewed', '1', '3', '24'),
('sitepagealbum', 'albums', 'commented_pagealbums', 'Most Commented', '0', '4', '24'),
('sitepagealbum', 'albums', 'featured_pagealbums', 'Featured', '0', '5', '24'),
('sitepagealbum', 'albums', 'random_pagealbums', 'Random', '0', '6', '24'),
('sitepagealbum', 'photos', 'recent_pagephotos', 'Recent', '1', '1', '24'),
('sitepagealbum', 'photos', 'liked_pagephotos', 'Most Liked', '1', '2', '24'),
('sitepagealbum', 'photos', 'viewed_pagephotos', 'Most Viewed', '1', '3', '24'),
('sitepagealbum', 'photos', 'commented_pagephotos', 'Most Commented', '0', '4', '24'),
('sitepagealbum', 'photos', 'featured_pagephotos', 'Featured', '0', '5', '24'),
('sitepagealbum', 'photos', 'random_pagephotos', 'Random', '0', '6', '24');

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepagealbum_home","action":"home"}' WHERE `engine4_core_menuitems`.`name` = 'sitepage_main_album' LIMIT 1 ;

UPDATE `engine4_core_pages` SET `name` = 'sitepage_album_browse' WHERE `engine4_core_pages`.`name` ='sitepage_album_albumlist' LIMIT 1 ;