DROP TABLE IF EXISTS `{dbp}servers_playlists`;
CREATE TABLE IF NOT EXISTS `{dbp}servers_playlists` (
  `server_playlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `server_id` int(10) unsigned NOT NULL,
  `server_playlist_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`server_playlist_id`,`server_id`),
  UNIQUE KEY `server_id` (`server_id`,`server_playlist_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

ALTER TABLE `{dbp}servers_playlists` 
	ADD FOREIGN KEY ( `server_id` ) REFERENCES `{dbp}servers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
