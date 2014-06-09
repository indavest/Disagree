CREATE TABLE admin_usermember (
	id varchar(50) NOT NULL,
	email varchar(255) ,
	username varchar(255) NOT NULL,
	createdTime timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	lastModified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	password varchar(50),
	PRIMARY KEY (id)
) ENGINE=InnoDB;