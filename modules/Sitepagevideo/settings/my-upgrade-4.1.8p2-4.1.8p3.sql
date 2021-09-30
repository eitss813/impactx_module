INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagevideo_processed', 'sitepagevideo', 'Your {item:$object:page video} is ready to be viewed.', 0, ''),
('sitepagevideo_processed_failed', 'sitepagevideo', 'Your {item:$object:page video} has failed to process.', 0, '');
