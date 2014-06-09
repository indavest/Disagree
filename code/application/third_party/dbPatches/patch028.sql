ALTER TABLE `disagreeme`.`usermemberprofile` ADD COLUMN `fullname` VARCHAR(45) AFTER `birthdate`,
 ADD COLUMN `location` VARCHAR(100) AFTER `fullname`;
CREATE TABLE argumenthide (
  id varchar(50) NOT NULL,
  createdTime timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	lastModified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  argumentId varchar(50) NOT NULL,
	memberId varchar(50) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT FK_argumenthide_argument_id FOREIGN KEY FK_argumenthide_argument_id (argumentId)
	  REFERENCES argument(id)
	  ON DELETE CASCADE
	  ON UPDATE NO ACTION,
  CONSTRAINT FK_argumenthide_member_id FOREIGN KEY FK_argumenthide_member_id (memberId)
	  REFERENCES usermember(id)
	  ON DELETE CASCADE
	  ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;