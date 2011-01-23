ALTER TABLE `{dbp}servers_users`
  DROP `can_kick`,
  DROP `can_ban`,
  DROP `can_temp_ban`,
  DROP `can_maps`,
  DROP `can_cvars`,
  DROP `can_messages`;
  
ALTER TABLE  `{dbp}servers_users` ADD  `permissions` INT NOT NULL DEFAULT  '0';

CREATE TABLE IF NOT EXISTS `{dbp}messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `server_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `current` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `server_id` (`server_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{dbp}players` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_addresses` text COLLATE utf8_unicode_ci NOT NULL,
  `names` text COLLATE utf8_unicode_ci NOT NULL,
  `ping_total` int(10) unsigned NOT NULL DEFAULT '0',
  `ping_scans` int(10) unsigned NOT NULL DEFAULT '0',
  `ping_last` int(10) unsigned NOT NULL DEFAULT '0',
  `server_id` int(10) unsigned NOT NULL DEFAULT '0',
  `last_update` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `server_id` (`server_id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `{dbp}messages`
  ADD CONSTRAINT `{dbp}messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `{dbp}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `{dbp}messages_ibfk_2` FOREIGN KEY (`server_id`) REFERENCES `{dbp}servers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  
ALTER TABLE `{dbp}players`
  ADD CONSTRAINT `{dbp}players_ibfk_1` FOREIGN KEY (`server_id`) REFERENCES `{dbp}servers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE