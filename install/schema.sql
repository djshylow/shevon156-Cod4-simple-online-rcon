CREATE TABLE IF NOT EXISTS `{dbp}logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{dbp}roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `{dbp}roles` (`id`, `name`, `description`) VALUES
(1, 'login', 'Login privileges, granted after account confirmation'),
(3, 'logs', 'Log management'),
(4, 'servers', 'Server management'),
(5, 'users', 'User management');

CREATE TABLE IF NOT EXISTS `{dbp}roles_users` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{dbp}servers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(127) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0.0.0',
  `port` int(5) unsigned NOT NULL DEFAULT '3074',
  `password` varchar(127) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{dbp}servers_users` (
  `server_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `permissions` int(11) NOT NULL DEFAULT '0',
  KEY `server_id` (`server_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `{dbp}users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` char(50) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{dbp}user_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(32) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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

ALTER TABLE `{dbp}logs`
  ADD CONSTRAINT `{dbp}logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `{dbp}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{dbp}roles_users`
  ADD CONSTRAINT `{dbp}roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `{dbp}users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `{dbp}roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `{dbp}roles` (`id`) ON DELETE CASCADE;
  
ALTER TABLE `{dbp}servers_users`
  ADD CONSTRAINT `{dbp}servers_users_ibfk_1` FOREIGN KEY (`server_id`) REFERENCES `{dbp}servers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `{dbp}servers_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `{dbp}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  
ALTER TABLE `{dbp}user_tokens`
  ADD CONSTRAINT `{dbp}user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `{dbp}users` (`id`) ON DELETE CASCADE;

ALTER TABLE `{dbp}messages`
  ADD CONSTRAINT `{dbp}messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `{dbp}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `{dbp}messages_ibfk_2` FOREIGN KEY (`server_id`) REFERENCES `{dbp}servers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  
ALTER TABLE `{dbp}players`
  ADD CONSTRAINT `{dbp}players_ibfk_1` FOREIGN KEY (`server_id`) REFERENCES `{dbp}servers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE