CREATE TABLE spamreport (
  id varchar(50) NOT NULL,
  createdTime timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  lastModified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  type varchar(20) NOT NULL,
  recordId varchar(50) NOT NULL,
  memberId varchar(50) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT FK_spamreport_member_id FOREIGN KEY FK_spamreport_member_id (memberId)
	  REFERENCES usermember(id)
	  ON DELETE CASCADE
	  ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;