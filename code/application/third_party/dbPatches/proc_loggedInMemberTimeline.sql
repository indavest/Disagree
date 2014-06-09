DELIMITER $$

DROP PROCEDURE IF EXISTS `proc_loggedInMemberTimeline` $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_loggedInMemberTimeline`(IN inputMemberId VARCHAR(50), IN LimitStart_ INT, IN LimitCnt_ INT)
BEGIN

    DECLARE b BOOLEAN DEFAULT 0;
    DECLARE a VARCHAR(50);

    DECLARE cur1 CURSOR FOR

    SELECT DISTINCT(argumentId) FROM (

    SELECT * FROM

    (SELECT createdtime, memberId,  id as argumentId FROM argument WHERE id in (SELECT argumentId FROM usermemberfollowedargument WHERE usermemberfollowedargument.memberId = inputMemberId)
      UNION
    SELECT createdtime, memberId, argumentId FROM argumentcomment WHERE argumentId in (SELECT argumentId FROM usermemberfollowedargument WHERE usermemberfollowedargument.memberId = inputMemberId) AND memberId <> inputMemberId AND parentId IS NULL
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=1 AND argumentId in (SELECT argumentId FROM usermemberfollowedargument WHERE usermemberfollowedargument.memberId = inputMemberId) AND memberId <> inputMemberId
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=0 AND argumentId in (SELECT argumentId FROM usermemberfollowedargument WHERE usermemberfollowedargument.memberId = inputMemberId) AND memberId <> inputMemberId

      UNION

    SELECT createdtime, memberId,  id as argumentId FROM argument WHERE memberId in (SELECT followedmemberId FROM usermemberfollowedmember WHERE usermemberfollowedmember.memberId = inputMemberId)
      UNION
    SELECT createdtime, memberId, argumentId FROM argumentcomment WHERE memberId in (SELECT followedmemberId FROM usermemberfollowedmember WHERE usermemberfollowedmember.memberId = inputMemberId) AND memberId <> inputMemberId AND parentId IS NULL
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=1 AND memberId in (SELECT followedmemberId FROM usermemberfollowedmember WHERE usermemberfollowedmember.memberId = inputMemberId) AND memberId <> inputMemberId
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=0 AND memberId in (SELECT followedmemberId FROM usermemberfollowedmember WHERE usermemberfollowedmember.memberId = inputMemberId) AND memberId <> inputMemberId

      UNION

    SELECT createdtime, memberId,  id as argumentId FROM argument WHERE topic in (SELECT followedtopicId FROM usermemberfollowedtopic WHERE usermemberfollowedtopic.memberId = inputMemberId)
      UNION
    SELECT createdtime, memberId, argumentId FROM argumentcomment WHERE argumentId in (SELECT id FROM argument WHERE topic IN (SELECT followedtopicId FROM usermemberfollowedtopic WHERE usermemberfollowedtopic.memberId = inputMemberId) AND memberId <> inputMemberId) AND parentId IS NULL
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=1 AND argumentId in (SELECT id FROM argument WHERE topic IN (SELECT followedtopicId FROM usermemberfollowedtopic WHERE usermemberfollowedtopic.memberId = inputMemberId) AND memberId <> inputMemberId)
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=0 AND argumentId in (SELECT id FROM argument WHERE topic IN (SELECT followedtopicId FROM usermemberfollowedtopic WHERE usermemberfollowedtopic.memberId = inputMemberId) AND memberId <> inputMemberId))

    AS unsortedLoggedInMemberTimeline ORDER BY createdtime desc

    )AS loggedInMemberTimeline WHERE argumentId NOT IN (SELECT argumentId FROM argumenthide WHERE memberId = inputMemberId) AND argumentId NOT IN (SELECT id FROM argument WHERE status = -1);

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET b = 1;


    OPEN cur1;

    DROP TEMPORARY TABLE IF EXISTS tmpArgumentList;
    CREATE TEMPORARY TABLE tmpArgumentList (id varchar(50),title varchar(150),argument MEDIUMTEXT,createdtime timestamp,lastmodified timestamp,memberId varchar(50),status tinyint(1),topic varchar(50),source varchar(200), commentsCount varchar(10),agreed varchar(10),disagreed varchar(10));

    WHILE (NOT b) DO
      FETCH cur1 INTO a;
      IF NOT b  THEN
        INSERT INTO tmpArgumentList SELECT argument.*,count(argumentcomment.id) commentsCount,(ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0)) AS agreed, (ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0)) AS disagreed FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId LEFT JOIN argumentcomment ON argument.id = argumentcomment.argumentId WHERE argument.id = a AND argumentcomment.parentId IS NULL;
      END IF;
    END WHILE;

	SET @limitStart=LimitStart_;
	SET @rowcount=LimitCnt_;
    PREPARE STMT FROM 'SELECT * FROM tmpArgumentList LIMIT ?,?';
    EXECUTE STMT USING @limitStart,@rowcount;
    deallocate prepare STMT;
    drop temporary table if exists tmpArgumentList;
    CLOSE cur1;

    END $$

DELIMITER ;