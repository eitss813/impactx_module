

-- added the default member level settings for the project view and comment prrivacy

UPDATE `engine4_authorization_permissions` SET `params` = '[\"everyone\",\"registered\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"parent_member\",\"leader\"]' WHERE `engine4_authorization_permissions`.`level_id` NOT IN(5) AND `engine4_authorization_permissions`.`type` = 'sitecrowdfunding_project' AND `engine4_authorization_permissions`.`name` = 'auth_comment';
UPDATE `engine4_authorization_permissions` SET `params` = '[\"everyone\",\"registered\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"parent_member\",\"leader\"]' WHERE `engine4_authorization_permissions`.`level_id` NOT IN(5) AND `engine4_authorization_permissions`.`type` = 'sitecrowdfunding_project' AND `engine4_authorization_permissions`.`name` = 'auth_view';
