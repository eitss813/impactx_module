INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagevideo_create', 'sitepagevideo', '{item:$subject} has created a page video {var:$eventname}.', 0, '');


INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEVIDEO_CREATENOTIFICATION_EMAIL", "sitepagevideo", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");