DROP TABLE IF EXISTS `{dbp}gamemodes`;
CREATE TABLE IF NOT EXISTS `{dbp}gamemodes` (
  `codename` char(10) collate utf8_unicode_ci NOT NULL,
  `fullname` varchar(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`codename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{dbp}gamemodes` (`codename`, `fullname`) VALUES
('barebones', 'Barebones Matches'),
('hardcore', 'Hardcore Matches'),
('normal', 'Normal Matches'),
('wager', 'Wager Matches');

DROP TABLE IF EXISTS `{dbp}gametypes`;
CREATE TABLE IF NOT EXISTS `{dbp}gametypes` (
  `codename` char(4) collate utf8_unicode_ci NOT NULL,
  `fullname` varchar(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`codename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{dbp}gametypes` (`codename`, `fullname`) VALUES
('ctf', 'Capture The Flag'),
('dem', 'Demolition'),
('dm', 'Free For All'),
('dom', 'Domination'),
('gun', 'Gun Game'),
('hlnd', 'Sticks And Stones'),
('koth', 'Headquarters'),
('oic', 'One In The Chamber'),
('sab', 'Sabotage'),
('sd', 'Search And Destroy'),
('shrp', 'Sharpshooter'),
('tdm', 'Team Deathmatch');

DROP TABLE IF EXISTS `{dbp}playlists`;
CREATE TABLE IF NOT EXISTS `{dbp}playlists` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `gametype_codename` enum('tdm','dm','sab','dem','ctf','sd','dom','koth','hlnd','gun','shrp','oic') collate utf8_unicode_ci default NULL,
  `gamemode_codename` enum('normal','hardcore','barebones','wager') collate utf8_unicode_ci default NULL,
  `size` enum('normal','small') collate utf8_unicode_ci NOT NULL default 'normal',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{dbp}playlists` (`id`, `name`, `gametype_codename`, `gamemode_codename`, `size`) VALUES
(1, 'Team Deathmatch', 'tdm', 'normal', 'normal'),
(2, 'Free For All', 'dm', 'normal', 'normal'),
(3, 'Capture The Flag', 'ctf', 'normal', 'normal'),
(4, 'Search And Destroy', 'sd', 'normal', 'normal'),
(5, 'Headquarters', 'koth', 'normal', 'normal'),
(6, 'Domination', 'dom', 'normal', 'normal'),
(7, 'Sabotage', 'sab', 'normal', 'normal'),
(8, 'Demolition', 'dem', 'normal', 'normal'),
(9, 'Hardcore Team Deathmatch', 'tdm', 'hardcore', 'normal'),
(10, 'Hardcore Free For All', 'dm', 'hardcore', 'normal'),
(11, 'Hardcore Capture The Flag', 'ctf', 'hardcore', 'normal'),
(12, 'Hardcore Search And Destroy', 'sd', 'hardcore', 'normal'),
(13, 'Hardcore Headquarters', 'koth', 'hardcore', 'normal'),
(14, 'Hardcore Domination', 'dom', 'hardcore', 'normal'),
(15, 'Hardcore Sabotage', 'sab', 'hardcore', 'normal'),
(16, 'Hardcore Demolition', 'dem', 'hardcore', 'normal'),
(17, 'Barebones Team Deathmatch', 'tdm', 'barebones', 'normal'),
(18, 'Barebones Free For All', 'dm', 'barebones', 'normal'),
(19, 'Barebones Capture The Flag', 'ctf', 'barebones', 'normal'),
(20, 'Barebones Search And Destroy', 'sd', 'barebones', 'normal'),
(21, 'Barebones Headquarters', 'koth', 'barebones', 'normal'),
(22, 'Barebones Domination', 'dom', 'barebones', 'normal'),
(23, 'Barebones Sabotage', 'sab', 'barebones', 'normal'),
(24, 'Barebones Demolition', 'dem', 'barebones', 'normal'),
(25, 'Team Tactical', NULL, 'normal', 'normal'),
(26, 'One In The Chamber', 'oic', 'wager', 'normal'),
(27, 'Sticks And Stones', 'hlnd', 'wager', 'normal'),
(28, 'Gun Game', 'gun', 'wager', 'normal'),
(29, 'Sharpshooter', 'shrp', 'wager', 'normal'),
(30, 'Hardcore Team Tactical', NULL, 'hardcore', 'normal'),
(31, 'Barebones Team Tactical', NULL, 'barebones', 'normal'),
(32, 'Team Deathmatch 12 players', 'tdm', 'normal', 'small'),
(33, 'Free For All 12 players', 'dm', 'normal', 'small'),
(34, 'Capture The Flag 12 players', 'ctf', 'normal', 'small'),
(35, 'Search And Destroy 12 players', 'sd', 'normal', 'small'),
(36, 'Headquarters 12 players', 'koth', 'normal', 'small'),
(37, 'Domination 12 players', 'dom', 'normal', 'small'),
(38, 'Sabotage 12 players', 'sab', 'normal', 'small'),
(39, 'Demolition 12 players', 'dem', 'normal', 'small'),
(40, 'Team Tactical 12 players', NULL, 'normal', 'small'),
(41, 'Hardcore Team Deathmatch 12 players', 'tdm', 'hardcore', 'small'),
(42, 'Hardcore Free For All 12 players', 'dm', 'hardcore', 'small'),
(43, 'Hardcore Capture The Flag 12 players', 'ctf', 'hardcore', 'small'),
(44, 'Hardcore Search And Destroy 12 players', 'sd', 'hardcore', 'small'),
(45, 'Hardcore Headquarters 12 players', 'koth', 'hardcore', 'small'),
(46, 'Hardcore Domination 12 players', 'dom', 'hardcore', 'small'),
(47, 'Hardcore Sabotage 12 players', 'sab', 'hardcore', 'small'),
(48, 'Hardcore Demolition 12 players', 'dem', 'hardcore', 'small'),
(49, 'Hardcore Team Tactical 12 players', NULL, 'hardcore', 'small'),
(50, 'Barebones Team Deathmatch 12 players', 'tdm', 'barebones', 'small'),
(51, 'Barebones Free For All 12 players', 'dm', 'barebones', 'small'),
(52, 'Barebones Capture The Flag 12 players', 'ctf', 'barebones', 'small'),
(53, 'Barebones Search And Destroy 12 players', 'sd', 'barebones', 'small'),
(54, 'Barebones Headquarters 12 players', 'koth', 'barebones', 'small'),
(55, 'Barebones Domination 12 players', 'dom', 'barebones', 'small'),
(56, 'Barebones Sabotage 12 players', 'sab', 'barebones', 'small'),
(57, 'Barebones Team Tactical 12 players', NULL, 'barebones', 'small');

DROP TABLE IF EXISTS `{dbp}servers_playlists`;
CREATE TABLE IF NOT EXISTS `{dbp}servers_playlists` (
  `server_playlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `server_id` int(10) unsigned NOT NULL,
  `server_playlist_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`server_playlist_id`,`server_id`),
  UNIQUE KEY `server_id` (`server_id`,`server_playlist_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
	
DROP TABLE IF EXISTS `{dbp}custom_playlists`;
CREATE TABLE IF NOT EXISTS `{dbp}custom_playlists` (
  `server_playlist_id` int(10) unsigned NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `playlist_id` int(10) unsigned NOT NULL,
  `in_window` tinyint(1) NOT NULL DEFAULT '0',
  `last_set` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`server_playlist_id`,`server_id`,`playlist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `{dbp}servers_playlists` 
	ADD FOREIGN KEY ( `server_id` ) REFERENCES `{dbp}servers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
	
ALTER TABLE `{dbp}custom_playlists` 
	ADD FOREIGN KEY ( `server_playlist_id`,`server_id` ) REFERENCES `{dbp}servers_playlists` (`server_playlist_id`,`server_id`) ON DELETE CASCADE ON UPDATE CASCADE;
  
ALTER TABLE `{dbp}players`
  ADD CONSTRAINT `{dbp}players_ibfk_1` FOREIGN KEY (`server_id`) REFERENCES `{dbp}servers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;