ALTER TABLE usermember add column lastloggedin timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE usermember add column lastloggedout timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE usermember add column online TINYINT NOT NULL DEFAULT 0;