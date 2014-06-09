DROP TABLE IF EXISTS `disagreeme`.`invitedmember`;
CREATE TABLE  `disagreeme`.`invitedmember` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `fbid` varchar(50) DEFAULT NULL,
  `invitationtype` enum('fb','tw','email') DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `invitedby` varchar(255) NOT NULL,
  `createdTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `inviationcount` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_invitedmember` (`invitedby`),
  CONSTRAINT `FK_invitedmember` FOREIGN KEY (`invitedby`) REFERENCES `usermember` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;