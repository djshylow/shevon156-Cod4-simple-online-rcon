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