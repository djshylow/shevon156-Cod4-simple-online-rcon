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