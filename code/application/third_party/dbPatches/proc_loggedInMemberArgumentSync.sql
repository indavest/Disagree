DELIMITER $$

DROP PROCEDURE IF EXISTS `proc_loggedInMemberArgumentSync` $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_loggedInMemberArgumentSync`(IN inputMemberId VARCHAR(50), IN timeInterval INT)
BEGIN

  DECLARE b BOOLEAN DEFAULT 0;
    DECLARE a VARCHAR(50);


    DECLARE cur1 CURSOR FOR

    SELECT DISTINCT(argumentId) FROM (

    SELECT * FROM

    (SELECT createdtime, memberId,  id as argumentId FROM argument WHERE id in (SELECT argumentId FROM usermemberfollowedargument WHERE usermemberfollowedargument.memberId = inputMemberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()
      UNION
    SELECT createdtime, memberId, argumentId FROM argumentcomment WHERE argumentId in (SELECT argumentId FROM usermemberfollowedargument WHERE usermemberfollowedargument.memberId = inputMemberId) AND memberId <> inputMemberId AND  uservote IN (0,1,-2,-3) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=1 AND argumentId in (SELECT argumentId FROM usermemberfollowedargument WHERE usermemberfollowedargument.memberId = inputMemberId) AND memberId <> inputMemberId AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=0 AND argumentId in (SELECT argumentId FROM usermemberfollowedargument WHERE usermemberfollowedargument.memberId = inputMemberId) AND memberId <> inputMemberId AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()

      UNION

    SELECT createdtime, memberId,  id as argumentId FROM argument WHERE memberId in (SELECT followedmemberId FROM usermemberfollowedmember WHERE usermemberfollowedmember.memberId = inputMemberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()
      UNION
    SELECT createdtime, memberId, argumentId FROM argumentcomment WHERE memberId in (SELECT followedmemberId FROM usermemberfollowedmember WHERE usermemberfollowedmember.memberId = inputMemberId) AND memberId <> inputMemberId AND  uservote IN (0,1,-2,-3) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=1 AND memberId in (SELECT followedmemberId FROM usermemberfollowedmember WHERE usermemberfollowedmember.memberId = inputMemberId) AND memberId <> inputMemberId AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=0 AND memberId in (SELECT followedmemberId FROM usermemberfollowedmember WHERE usermemberfollowedmember.memberId = inputMemberId) AND memberId <> inputMemberId AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()

      UNION

    SELECT createdtime, memberId, argumentId FROM argumentcomment WHERE argumentId in (SELECT id FROM argument WHERE memberId = inputMemberId) AND  uservote IN (0,1,-2,-3) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=1 AND argumentId in (SELECT id FROM argument WHERE memberId = inputMemberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=0 AND argumentId in (SELECT id FROM argument WHERE memberId = inputMemberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()

      UNION

    SELECT createdtime, memberId,  id as argumentId FROM argument WHERE topic in (SELECT followedtopicId FROM usermemberfollowedtopic WHERE usermemberfollowedtopic.memberId = inputMemberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()
      UNION
    SELECT createdtime, memberId, argumentId FROM argumentcomment WHERE argumentId in (SELECT id FROM argument WHERE topic IN (SELECT followedtopicId FROM usermemberfollowedtopic WHERE usermemberfollowedtopic.memberId = inputMemberId) AND memberId <> inputMemberId) AND  uservote IN (0,1,-2,-3) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=1 AND argumentId in (SELECT id FROM argument WHERE topic IN (SELECT followedtopicId FROM usermemberfollowedtopic WHERE usermemberfollowedtopic.memberId = inputMemberId) AND memberId <> inputMemberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW()
      UNION
    SELECT createdtime, memberId, argumentId FROM usermembervotes WHERE vote=0 AND argumentId in (SELECT id FROM argument WHERE topic IN (SELECT followedtopicId FROM usermemberfollowedtopic WHERE usermemberfollowedtopic.memberId = inputMemberId) AND memberId <> inputMemberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL timeInterval MINUTE) AND NOW())

    AS unsortedLoggedInMemberTimeline ORDER BY createdtime desc

    )AS loggedInMemberTimeline WHERE argumentId NOT IN (SELECT argumentId FROM argumenthide WHERE memberId = inputMemberId) AND argumentId NOT IN (SELECT id FROM argument WHERE status = -1);

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET b = 1;


    OPEN cur1;

    DROP TEMPORARY TABLE IF EXISTS tmpArgumentList;
    CREATE TEMPORARY TABLE tmpArgumentList (id varchar(50),title varchar(150),argument MEDIUMTEXT,createdtime timestamp,lastmodified timestamp,memberId varchar(50),status tinyint(1),topic varchar(50),source varchar(200), commentsCount varchar(10),agreed varchar(10),disagreed varchar(10));

    WHILE (NOT b) DO
      FETCH cur1 INTO a;
      IF NOT b  THEN
        INSERT INTO tmpArgumentList SELECT argument.*,count(argumentcomment.id) commentsCount,(ifnull(argumentvotes.maleagreed, 0) + ifnull(argumentvotes.femaleagreed, 0) + ifnull(argumentvotes.generalagreed, 0)) AS agreed, (ifnull(argumentvotes.maledisagreed, 0) + ifnull(argumentvotes.femaledisagreed, 0) + ifnull(argumentvotes.generaldisagreed, 0)) AS disagreed FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId LEFT JOIN argumentcomment ON argument.id = argumentcomment.argumentId WHERE argument.id = a;
      END IF;
    END WHILE;

    SELECT * FROM tmpArgumentList;
  	drop temporary table if exists tmpArgumentList;
    CLOSE cur1;

END $$

DELIMITER ;