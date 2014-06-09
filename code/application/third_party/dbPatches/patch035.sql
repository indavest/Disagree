ALTER TABLE `usermembervotes`
	ADD COLUMN `commentId` VARCHAR(50) DEFAULT NULL AFTER `memberId`,
	ADD CONSTRAINT `FK_usermembervotes_argument_comment_id` FOREIGN KEY `FK_usermembervotes_argumentcomment_id` (`commentId`)
      REFERENCES `argumentcomment` (`id`)
        ON DELETE CASCADE
        ON UPDATE RESTRICT;

update usermembervotes
  JOIN argumentcomment
      ON usermembervotes.memberid = argumentcomment.memberid
          AND usermembervotes.argumentId = argumentcomment.argumentid
          AND usermembervotes.vote = argumentcomment.uservote
SET usermembervotes.commentId = argumentcomment.id;
