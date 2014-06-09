DELIMITER $$

DROP PROCEDURE IF EXISTS `proc_onlinenotification` $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_onlinenotification`(IN inmemberId VARCHAR(50))
BEGIN
	DECLARE loopend BIT DEFAULT 0;
	DECLARE jobType INTEGER (11) UNSIGNED;
  DECLARE memberCheck varchar (50);
	DECLARE jobsqueue CURSOR FOR select notification_queue.type from notification_queue;
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET loopend = 1;
	OPEN jobsqueue;
  IF inmemberId = '' THEN
      SET memberCheck = '%';
  ELSE
      SET memberCheck = inmemberid;
  END IF;
	DROP TEMPORARY TABLE IF EXISTS tmpdata;
	CREATE TEMPORARY TABLE tmpdata(
		id VARCHAR(50),
		type TINYINT,
		recordId VARCHAR(50),
		ownerId VARCHAR(50),
		ownerEmail VARCHAR(255),
		userprofilephoto VARCHAR(255),
		userid VARCHAR(50),
		username VARCHAR(255),
		argumentId VARCHAR(50),
		argumentTitle VARCHAR(500),
		createdtime TIMESTAMP
	);
	FETCH jobsqueue INTO jobType;
  		WHILE(NOT loopend) DO
      -- processing Agree / disagree notification
			IF jobType = 0 OR jobType = 1 THEN
				INSERT INTO tmpdata
					SELECT queue.id
						 , queue.type
						 , queue.recordId
						 , argument.memberId AS ownerId
						 , owner.email AS ownerEmail
						 , followinguserprofile.profilephoto AS userprofilephoto
						 , followinguser.id AS userid
						 , followinguser.username AS username
						 , argument.id AS argumentId
						 , argument.title AS argumentTitle
						 , usermembervotes.createdtime
					FROM notification_queue AS queue
							JOIN usermembervotes ON queue.recordId = usermembervotes.id
							JOIN argument ON usermembervotes.argumentId = argument.id
							JOIN usermember AS followinguser ON usermembervotes.memberid = followinguser.id
							JOIN usermemberprofile AS followinguserprofile ON usermembervotes.memberid = followinguserprofile.memberid
							JOIN usermember AS owner ON argument.memberId = owner.id
              JOIN usermemberprofile AS ownerProfile ON argument.memberId = ownerProfile.memberId
              WHERE argument.memberId = inmemberId AND (queue.type=0 OR queue.type=1);
      -- processing comment notificaion
        ELSEIF jobType = 2 THEN
        INSERT INTO tmpdata
          SELECT queue.id
						 , queue.type
						 , queue.recordId
						 , argument.memberId AS ownerId
						 , owner.email AS ownerEmail
						 , commenteduserprofile.profilephoto AS followinguserprofilephoto
						 , commenteduser.id AS followinguserid
						 , commenteduser.username followingusername
						 , argument.id AS argumentId
						 , argument.title AS argumentTitle
						 , argumentcomment.createdtime
					FROM notification_queue AS queue
							JOIN argumentcomment ON queue.recordId = argumentcomment.id
							JOIN argument ON argumentcomment.argumentId = argument.id
							JOIN usermember AS commenteduser ON argumentcomment.memberid = commenteduser.id
							JOIN usermemberprofile AS commenteduserprofile ON argumentcomment.memberid = commenteduserprofile.memberid
							JOIN usermember AS owner ON argument.memberId = owner.id
              JOIN usermemberprofile AS ownerProfile ON argument.memberId = ownerProfile.memberId
              WHERE argument.memberId = inmemberId AND queue.type=2;
      -- processing follow argument notification
      ELSEIF jobType=3 THEN
				INSERT INTO tmpdata
					SELECT queue.id
						 , queue.type
						 , queue.recordId
						 , argument.memberId AS ownerId
						 , owner.email AS ownerEmail
						 , followinguserprofile.profilephoto AS userprofilephoto
						 , followinguser.id AS userid
						 , followinguser.username username
						 , argument.id AS argumentId
						 , argument.title AS argumentTitle
						 , usermemberfollowedargument.createdtime
					FROM notification_queue AS queue
						JOIN usermemberfollowedargument ON queue.recordId = usermemberfollowedargument.id
						JOIN argument ON usermemberfollowedargument.argumentId = argument.id
						JOIN usermember AS followinguser ON usermemberfollowedargument.memberid = followinguser.id
						JOIN usermemberprofile AS followinguserprofile ON usermemberfollowedargument.memberId = followinguserprofile.memberid
						JOIN usermember AS owner ON argument.memberId = owner.id
            JOIN usermemberprofile AS ownerProfile ON argument.memberId = ownerProfile.memberId
            WHERE argument.memberId = inmemberId AND queue.type=3;
      -- processing follow member notification
      ELSEIF jobType=4 THEN
      INSERT INTO tmpdata
        SELECT queue.id
						 , queue.type
						 , queue.recordId
						 , owner.id AS ownerId
						 , owner.email AS ownerEmail
						 , followingmemberprofile.profilephoto AS userprofilephoto
						 , followingmember.id AS userid
						 , followingmember.username username
						 , NULL AS argumentId
						 , NULL AS argumentTitle
						 , usermemberfollowedmember.createdtime
					FROM notification_queue AS queue
						JOIN usermemberfollowedmember ON queue.recordId = usermemberfollowedmember.id
						JOIN usermember AS followingmember ON usermemberfollowedmember.memberid = followingmember.id
						JOIN usermemberprofile AS followingmemberprofile ON usermemberfollowedmember.memberid = followingmemberprofile.memberid
						JOIN usermember AS owner ON usermemberfollowedmember.followedmemberid = owner.id
            JOIN usermemberprofile AS ownerProfile ON owner.id = ownerProfile.memberId
          WHERE owner.id = inmemberId AND queue.type=4;
      -- processing reply notification to comment Owner
      ELSEIF jobType = 6 THEN
        INSERT INTO tmpdata
         SELECT queue.id,
                queue.type
              , queue.recordId
              , commenteduser.id AS ownerId
              , commenteduser.email AS ownerEmail
              , replyUserProfile.profilephoto AS userprofilephoto
              , replyUser.id AS userid
              , replyUser.username as username
              , argumentcommented.id AS argumentId
              , argumentcommented.commentText as argumentTitle
              , replycomment.createdtime AS createdtime
          FROM notification_queue AS queue
               JOIN argumentcomment AS replycomment ON queue.recordId = replycomment.id
               JOIN usermember AS replyUser ON replycomment.memberId = replyUser.id
               JOIN usermemberprofile AS replyUserProfile ON replycomment.memberId = replyUserProfile.memberid
               JOIN argumentcomment AS argumentcommented ON replycomment.parentid = argumentcommented.id
               JOIN usermember AS commenteduser ON argumentcommented.memberid = commenteduser.id
               JOIN usermemberprofile AS commenteduserprofile ON argumentcommented.memberid = commenteduserprofile.memberid
          WHERE commenteduser.id = inmemberId AND queue.type=6;
      -- processing reply notification to argument Owner
      ELSEIF jobType = 7 THEN
        INSERT INTO tmpdata
          SELECT queue.id
						 , queue.type
						 , queue.recordId
						 , owner.id AS ownerId
						 , owner.email AS ownerEmail
						 , commenteduserprofile.profilephoto AS followinguserprofilephoto
						 , commenteduser.id AS followinguserid
						 , commenteduser.username followingusername
						 , argument.id AS argumentId
						 , argument.title AS argumentTitle
						 , argumentcomment.createdtime
					FROM notification_queue AS queue
							JOIN argumentcomment ON queue.recordId = argumentcomment.id
							JOIN argument ON argumentcomment.argumentId = argument.id
							JOIN usermember AS commenteduser ON argumentcomment.memberid = commenteduser.id
							JOIN usermemberprofile AS commenteduserprofile ON argumentcomment.memberid = commenteduserprofile.memberid
							JOIN usermember AS owner ON argument.memberId = owner.id
              JOIN usermemberprofile AS ownerProfile ON argument.memberId = ownerProfile.memberId
          WHERE owner.id = inmemberId AND queue.type=7 order by createdtime desc;
			END IF;
			FETCH jobsqueue INTO jobType;
		END WHILE;
	CLOSE jobsqueue;

  SELECT DISTINCT * FROM tmpdata ORDER BY createdtime desc;

	DROP TEMPORARY TABLE IF EXISTS tmpdata;

END $$

DELIMITER ;