INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagealbum_create', 'sitepagealbum', '{item:$subject} has created a page album {var:$eventname}.', 0, '');


INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEALBUM_CREATENOTIFICATION_EMAIL", "sitepagealbum", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");