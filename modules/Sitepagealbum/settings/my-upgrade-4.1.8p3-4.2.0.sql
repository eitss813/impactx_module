UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title}:', `displayable` = '6', `is_object_thumb` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'sitepagealbum_admin_photo_new' LIMIT 1 ;

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES ('sitepage_admin_profile_photo', 'sitepage', '{item:$object} changed their Page profile photo.', '1', '6', '2', '1', '1', '1', '1');

INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `order` )VALUES
 ('sitepage_main_album', 'sitepagealbum', 'Albums', 'Sitepagealbum_Plugin_Menus::canViewAlbums', '{"route":"sitepage_albumlist","action":"albumlist"}', 'sitepage_main', '', 1, '19');

UPDATE  `engine4_activity_actiontypes` SET  `is_generated` =  '1'
WHERE  `engine4_activity_actiontypes`.`type` =  'sitepagealbum_admin_photo_new';
