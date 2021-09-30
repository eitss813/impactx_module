--
-- Updating data for table `engine4_core_menuitems`
--
UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"user_general","action":"browse","icon":"fa-user"}'
WHERE `name` = 'core_main_user';