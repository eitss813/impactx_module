--
-- Updating data for table `engine4_core_menuitems`
--
UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"sitepage_general","action":"index","icon":"fa-file"}'
WHERE `name` = 'core_main_sitepage';

--
-- Change the Commentable & Shareable values
--
UPDATE engine4_activity_actiontypes SET commentable=3,shareable=3 WHERE type='comment_sitepage_page' and module='sitepage';