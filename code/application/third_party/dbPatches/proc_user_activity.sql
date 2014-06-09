DELIMITER $$

DROP PROCEDURE IF EXISTS `proc_user_activity` $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_user_activity`(IN memberId VARCHAR(50),IN LimitStart_ INT,IN LimitCnt_ INT)
BEGIN
SET @limitStart = LimitStart_;
SET @rowcount = LimitCnt_;
SET @memberId = memberId;


PREPARE STMT FROM 'SELECT * FROM
  (
  -- (1)Started Argument: returns argumentid as recordid, argumentTitle as extraField1, createdtime
    SELECT
        argument.id AS recordId
      , 1 AS action
      , argument.title AS ExtraField1
      , NULL AS ExtraField2
      , NULL AS ExtraField3
      , NULL AS ExtraField4
      , NULL AS ExtraField5
      , argument.createdtime AS createdtime
    FROM argument WHERE memberId=@memberId

    UNION

  -- (2,3)Vote (+ comment) : returns argumentid as recordid,uservote as action,argumentTitle,commentid,commenttext as extraFields, createdtime
    SELECT
        argument.id AS recordId
      , (usermembervotes.vote+2) AS action
      , argument.title AS ExtraField1
      , argumentcomment.id AS ExtraField2
      , argumentcomment.commenttext AS ExtraField3
      , NULL AS ExtraField4
      , NULL AS ExtraField5
      , usermembervotes.createdtime AS createdtime
    FROM usermembervotes
          JOIN argument ON argument.id = usermembervotes.argumentId
          LEFT JOIN argumentcomment ON usermembervotes.commentid = argumentcomment.id
    WHERE usermembervotes.memberId=@memberId

    UNION

  -- (4)Comment: returns argumentid as recordid, argumentTitle,commentid,commenttext,uservote as extraFields, createdtime
    SELECT
        argumentcomment.argumentid AS recordId
      , 4 AS action
      , argument.title AS ExtraField1
      , argumentcomment.id AS ExtraField2
      , argumentcomment.commenttext AS ExtraField3
      , argumentcomment.uservote AS ExtraField4
      , NULL AS ExtraField5
      , argumentcomment.createdtime AS createdtime
    FROM argumentcomment
          JOIN argument ON argument.id = argumentcomment.argumentId
    WHERE argumentcomment.memberId=@memberId AND argumentcomment.uservote IN (-2,-3)

    UNION
  -- (5)Reply: returns argumentid as recordid, argumentTitle,commentid,commenttext,replyId,replyText as extraFields, createdtime
    SELECT
        reply.argumentId AS recordId
      , 5 AS action
      , argument.title AS ExtraField1
      , comment.id AS ExtraField2
      , comment.commenttext AS ExtraField3
      , reply.id AS ExtraField4
      , reply.commenttext AS ExtraField5
      , reply.createdtime AS createdtime
      FROM argumentcomment as reply
         JOIN argument ON reply.argumentId = argument.id
         JOIN argumentcomment as comment ON reply.parentid = comment.id
    WHERE reply.memberId=@memberId AND reply.uservote="-1"

    UNION
  -- (6)Follow User: returns followedmemberid as recordid, followedmemberusername as extraFields, createdtime
    SELECT
          usermemberfollowedmember.followedmemberid AS recordId
        , 6 AS action
        , usermember.username AS ExtraField1
        , NULL AS ExtraField2
        , NULL AS ExtraField3
        , NULL AS ExtraField4
        , NULL AS ExtraField5
        , usermemberfollowedmember.createdtime AS createdtime
    FROM usermemberfollowedmember
         JOIN usermember ON usermemberfollowedmember.followedmemberid = usermember.id
    WHERE usermemberfollowedmember.memberid=@memberId

    UNION
  -- (7)Follow Argument: returns argumentid as recordid, argumentTitle as extraFields, createdtime
    SELECT
          usermemberfollowedargument.argumentId AS recordId
        , 7 As action
        , argument.title AS ExtraField1
        , NULL AS ExtraField2
        , NULL AS ExtraField3
        , NULL AS ExtraField4
        , NULL AS ExtraField5
        , usermemberfollowedargument.createdtime AS createdtime
    FROM usermemberfollowedargument
         JOIN argument ON usermemberfollowedargument.argumentid = argument.id
    WHERE usermemberfollowedargument.memberId=@memberId
) AS tmp
ORDER BY createdtime DESC
LIMIT ? , ?';

EXECUTE STMT USING @limitStart,@rowcount;
deallocate prepare STMT;

END $$

DELIMITER ;