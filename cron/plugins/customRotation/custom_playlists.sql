DROP TABLE IF EXISTS `{dbp}custom_playlists`;
CREATE TABLE IF NOT EXISTS `{dbp}custom_playlists` (
  `server_playlist_id` int(10) unsigned NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `playlist_id` int(10) unsigned NOT NULL,
  `in_window` tinyint(1) NOT NULL DEFAULT '0',
  `last_set` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`server_playlist_id`,`server_id`,`playlist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `{dbp}custom_playlists` 
	ADD FOREIGN KEY ( `server_playlist_id`,`server_id` ) REFERENCES `{dbp}servers_playlists` (`server_playlist_id`,`server_id`) ON DELETE CASCADE ON UPDATE CASCADE;
