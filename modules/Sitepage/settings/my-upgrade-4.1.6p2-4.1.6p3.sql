INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES
('sitepage', 'category_id', 0, 11, 'Category');

DELETE FROM `engine4_core_search` WHERE `engine4_core_search`.`type` = 'sitepage_album';
DELETE FROM `engine4_core_search` WHERE `engine4_core_search`.`type` = 'sitepage_import';
DELETE FROM `engine4_core_search` WHERE `engine4_core_search`.`type` = 'sitepage_importfile';