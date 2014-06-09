<?php
class BaseModel extends DA_Model {

	public function __construct(){}

    public function __destruct()    {
        $this->dbConnect = null;
    }

    public function getRecentActivity()    {
        try {
            $query = $this->db->prepare('(SELECT title AS activity,"Argument" as activityType, createdtime, memberId, id as argumentId FROM argument)
						UNION ALL
					  (SELECT commenttext AS activity,"Comment", createdtime, memberId,argumentId FROM argumentcomment)
					   	UNION ALL
					  (SELECT agreed AS activity,"Agreed", createdtime, memberId,argumentId FROM usermembervotes WHERE agreed=1)
						UNION ALL
					  (SELECT disagreed AS activity,"Disgreed", createdtime, memberId,argumentId FROM usermembervotes WHERE disagreed=1)
						ORDER BY createdtime DESC');
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function generateMessage($type, $argument, $activityMember, $comment)    {
        switch ($type) {
            case "comment":
                $message = ($activityMember->fullname === null || $activityMember->fullname == '')?$activityMember->username:$activityMember->fullname . " commented <br/><i>'" . $comment . "'</i><br/> on <b>" . $argument->argument . "</b>";
                break;

            case "agree":
                $message = ($activityMember->fullname === null || $activityMember->fullname == '')?$activityMember->username:$activityMember->fullname . " Agree to <b>" . $argument->argument . "</b>";
                break;

            case "disagree":
                $message = ($activityMember->fullname === null || $activityMember->fullname == '')?$activityMember->username:$activityMember->fullname . " Disagreed to <b>" . $argument->argument . "</b>";
                break;

            case "followMember":
                $message = ($activityMember->fullname === null || $activityMember->fullname == '')?$activityMember->username:$activityMember->fullname . " is following you";
                break;

            default:
                $message = "";
                break;
        }

        return $message;
    }

    public function notify($type, $argumentId, $activityMemberId, $commentText)    {
        $argumentObj = new argument();
        $userMemberObj = new userMember();
        $argument = $argumentObj->getById($argumentId);
        $createdMember = $userMemberObj->getById($argument->memberId);
        $activityMember = $userMemberObj->getById($activityMemberId);
        $message = $this->generateMessage($type, $argument, $activityMember, $commentText);
        if($this->sendEmail($message, $to)){
        	return true;
        }else {
        	return false;
        }
    }


    public function addNotification($data)    {
        try {
            //$data['id'] = $this->dbConnectObj->generateUniqueId();
            $query = $this->db->prepare("INSERT INTO notification_queue (id, type, createdtime, memberId, argumentId, msg) VALUES(:id,:type,now(),:memberId,:argumentId,:msg)");
            if ($query->execute($data)) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getNotificationQueue()    {
        try {
            $query = $this->db->prepare("SELECT * FROM notification_queue WHERE status = 0");
            $query->execute();
            if ($query->rowCount() > 0) {
                return $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function setNotificationCompleted($id)    {
        try {
            $query = $this->db->prepare("UPDATE notification_queue SET status = 1 WHERE id = :id");
            return $query->execute(array(':id' => $id));
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function deleteNotification($id)    {
        try {
            $query = $this->db->prepare("DELETE FROM notification_queue WHERE id = :id");
            return $query->execute(array(':id' => $id));
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function liveSearch($keyword, $startLimit, $endLimit)    {
        try {
            $resultArray = array();
            $query = $this->db->prepare("SELECT argument.*,usermember.username,usermemberprofile.profilephoto,usermemberprofile.fullname FROM argument,usermember,usermemberprofile WHERE argument.title like :keyword AND argument.memberId = usermember.id AND usermember.id = usermemberprofile.memberId ORDER BY argument.createdtime DESC LIMIT $startLimit, $endLimit");
            $query->execute(array(":keyword" => '%' . $keyword . '%'));
            if ($query->rowCount() > 0) {
                $resultArray["argument"] = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $resultArray["argument"] = null;
            }
            $query = $this->db->prepare("SELECT usermember.id, usermember.oauth_provider, usermember.oauth_uid, usermember.email, usermember.status, usermember.username, usermember.createdTime, usermember.lastModified, usermember.lastloggedin, usermember.lastloggedout, usermember.online, usermemberprofile.memberId, usermemberprofile.createdTime, usermemberprofile.lastModified, usermemberprofile.gender, usermemberprofile.profilephoto, usermemberprofile.interest, usermemberprofile.birthdate, usermemberprofile.fullname, usermemberprofile.location FROM usermember, usermemberprofile WHERE (username LIKE :username OR fullname LIKE :username)AND usermember.id = usermemberprofile.memberId ORDER BY usermember.createdtime DESC LIMIT $startLimit, $endLimit");
            $query->execute(array(":username" => '%' . $keyword . '%'));
            if ($query->rowCount() > 0) {
                $resultArray["usermember"] = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                $resultArray["usermember"] = null;
            }
            return $resultArray;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getNotificationCount($memberId, $timeInterval)    {
    	try {
    		$query = $this->db->prepare("SELECT SUM(count) as count FROM ((SELECT COUNT(*) count FROM argument WHERE memberId IN (SELECT followedmemberid FROM usermemberfollowedmember WHERE memberId=:memberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW() ORDER BY createdtime DESC)
												UNION
												(SELECT COUNT(*) count FROM usermemberfollowedmember WHERE followedmemberId = :memberId AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW() ORDER BY createdtime DESC)
												UNION
												(SELECT COUNT(*) count FROM argumentcomment WHERE argumentId IN (SELECT id FROM argument WHERE memberId=:memberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW() ORDER BY createdtime DESC)) AS resulttable");
    		$query->execute(array(":memberId" => $memberId,":timeInterval" => $timeInterval));
    		return $query->fetchObject();
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }

    public function getArgumentNotification($memberId, $timeInterval){
    	try {
    		$query = $this->db->prepare("SELECT argumentId FROM argumentcomment WHERE argumentId IN (SELECT id FROM argument WHERE memberId=:memberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW() ORDER BY createdtime DESC");
    		$query->execute(array(":memberId" => $memberId,":timeInterval" => $timeInterval));
    		return $query->fetchAll(PDO::FETCH_OBJ);
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }

    public function getArgumentNotificationTip($memberId, $timeInterval){
    	try {
    		$query = $this->db->prepare("SELECT argumentId, sum(agreeCount) agreeCount, sum(disagreeCount) disagreeCount FROM
    					((SELECT argumentId, count(uservote) agreeCount, 0 as disagreeCount FROM argumentcomment WHERE argumentId IN (SELECT id FROM argument WHERE memberId=:memberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW() AND uservote=1 GROUP BY argumentId ORDER BY createdtime DESC)
							UNION
						(SELECT argumentId, 0 as agreeCount, count(uservote) disagreeCount FROM argumentcomment WHERE argumentId IN (SELECT id FROM argument WHERE memberId=:memberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW() AND uservote=0 GROUP BY argumentId ORDER BY createdtime DESC)) as resultTable GROUP BY argumentId");
    		$query->execute(array(":memberId" => $memberId,":timeInterval" => $timeInterval));
    		return $query->fetchAll(PDO::FETCH_OBJ);
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }

    public function time_difference($date)    {

        if (empty($date)) {
            return "No date provided";
        }

        $periods = array("s", "m", "h", "day", "week", "month", "year", "decade");
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

        $now = time();
        $unix_date = strtotime($date);


        // check validity of date
        if (empty($unix_date)) {
            return "Bad date";
        }

        // is it future date or past date
        if ($now > $unix_date) {
            $difference = $now - $unix_date;
            //$tense = "ago";

        } else {
            $difference = $unix_date - $now;
            $tense = "from now";
        }
		$hourDifference = $difference/(60*60);
        if($hourDifference < 24){
        	for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
	            $difference /= $lengths[$j];
	        }
	        $difference = round($difference);
	        /*if ($difference != 1) {
	            $periods[$j] .= "s";
	        }*/

	        $dateDifference = "$difference$periods[$j]";
        } else{
        	$difference = date("j M", $unix_date);
        	$dateDifference = "$difference";
        }

        return $dateDifference;
    }

    public function time_difference_DB_Call($date)    {

        if (empty($date)) {
            return "No date provided";
        }

        $periods = array("MINUTE");
        $lengths = array("60");

        $now = time();
        $unix_date = strtotime($date);


        // check validity of date
        if (empty($unix_date)) {
            return "Bad date";
        }

        // is it future date or past date
        if ($now > $unix_date) {
            $difference = $now - $unix_date;
            //$tense = "ago";

        } else {
            $difference = $unix_date - $now;
            $tense = "from now";
        }
		//$hourDifference = $difference/(60*60);
        //if($hourDifference < 24){
        	for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
	            $difference /= $lengths[$j];
	        }
	        $difference = round($difference / 60);
	        /*if ($difference != 1) {
	            $periods[$j] .= "s";
	        }*/

	        $dateDifference = "$difference";
        /*} else{
        	$difference = date("j M", $unix_date);
        	$dateDifference = "$difference {$tense}";
        }*/

        return $dateDifference;
    }

	function sendInvitation($message, $to){
		if($this->sendEmail($message, $to)){
			return true;
		}else {
			return false;
		}
	}

	function getOnlineNotification($memberId, $timeInterval){
		$data = array();
		try {
			$query = $this->db->prepare("SELECT type, memberId, argumentId, $memberId as ownerId FROM (SELECT 'memberfollow' as type,memberId as memberId, null as argumentId,createdtime FROM usermemberfollowedmember WHERE followedmemberId = :memberId AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL $timeInterval MINUTE) AND NOW()
			UNION
			SELECT 'comment' as type,memberId as memberId, argumentId as argumentId,createdtime  FROM argumentcomment WHERE argumentId IN (SELECT id FROM argument WHERE memberId=:memberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL $timeInterval MINUTE) AND NOW()
			UNION
			SELECT 'agreed' as type,memberId as memberId, argumentId as argumentId,createdtime FROM usermembervotes WHERE argumentId IN (SELECT id FROM argument WHERE memberId=:memberId) AND vote=1 AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL $timeInterval MINUTE) AND NOW()
			UNION
			SELECT 'disagreed' as type,memberId as memberId, argumentId as argumentId,createdtime FROM usermembervotes WHERE argumentId IN (SELECT id FROM argument WHERE memberId=:memberId) AND vote=0 AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL $timeInterval MINUTE) AND NOW()
			UNION
			SELECT 'favorite' as type,memberId as memberId, argumentId as argumentId,createdtime FROM usermemberfollowedargument WHERE argumentId IN (SELECT id FROM argument WHERE memberId = :memberId) AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL $timeInterval MINUTE) AND NOW()
			UNION
			SELECT 'memberfollow' as type,memberId as memberId, null as argumentId,createdtime FROM usermemberfollowedmember WHERE followedmemberId =:memberId AND createdtime BETWEEN DATE_SUB(NOW() , INTERVAL $timeInterval MINUTE) AND NOW()) AS resulttable WHERE memberId != :memberId ORDER BY resulttable.createdtime");

			$query->execute(array(":memberId" => $memberId));
			if($data = $query->fetchAll(PDO::FETCH_ASSOC)){
				//$data['ownerId'] = $memberId;
				return $data;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	function registerOnlineNotification($data){
		try {
			$data['id'] = $this->generateUniqueId();
			$query = $this->db->prepare("INSERT INTO onlinenotification (id, type, memberId, ownerId, argumentId, createdtime) VALUES (:id, :type, :memberId, :ownerId, :argumentId, now())");
			if($query->execute($data)){
				return true;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

    /**
     * setOnlineNotificationAsRead
     * This function sets all notifications as read once user came to notifications tab
     * uses: notificationRead (action controller)
     *
     * @param $memberId
     * @return bool
     */
    function setOnlineNotificationAsRead($memberId){
		try {
			$query = $this->db->prepare("UPDATE notification_queue SET notified=1 WHERE memberId=:memberId");
			if($query->execute(array(":memberId" => $memberId))){
				return true;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

    /**
     * returns number of unread notifications of a specified member
     * used in: getNotification(action controller)
     *
     * @param $memberId
     * @return bool|string
     */
	function getUnreadNotificationCount($memberId){
		try {
			$query = $this->db->prepare("SELECT count(*) as count FROM notification_queue WHERE memberId=:memberId AND notified=0");
			$query->execute(array(":memberId" => $memberId));
			if($data = $query->fetchObject()){
				return $data;
			}else{
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	function getUnreadNotification($memberId){
		try {
			$query = $this->db->prepare("SELECT * FROM onlinenotification WHERE ownerId=:memberId AND checked=0");
			$query->execute(array(":memberId" => $memberId));
			if($data = $query->fetchAll(PDO::FETCH_OBJ)){
				return $data;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

    /**
     * returns notificatios objects array of given memberId
     * used: memberNotifications (action controller)
     *
     * @param $memberId
     * @return bool|string
     */
	function getProfileNotification($memberId){
		try {
			$query = $this->db->prepare("SELECT * FROM notification_queue WHERE memberId=:memberId ORDER BY createdtime DESC");
			$query->execute(array(":memberId" => $memberId));
			if($data = $query->fetchAll(PDO::FETCH_OBJ)){
				return $data;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

    function notificationRequest($type = null,$recordId = null,$loggedInMemberId = null,$user = null){
        $data = null;
        switch($type){
            case DISAGREE_NOTIFICATION:
                //process disagree vote
                $query = $this->db->prepare('select argument.memberid as ownerid, usermember.online as owneronlinestatus from argument join usermembervotes on argument.id = usermembervotes.argumentid join usermember on argument.memberId = usermember.id where usermembervotes.id = :recordId LIMIT 1');
                $query->execute(array(":recordId"=>$recordId));
                if(($data = $query->fetchObject()) && ($data->ownerid != $loggedInMemberId)){ //to filter self notifications
                	$query = $this->db->prepare('INSERT INTO notification_queue (id,type,createdtime,lastmodified,memberid,recordId,msg) values (:id,:type,now(),now(),:memberid,:recordId,"test")');
                	if($query->execute(array(":id"=>$this->generateUniqueId(),":type"=>DISAGREE_NOTIFICATION,":memberid"=>$data->ownerid,":recordId"=>$recordId))){
                		log_message('debug',''.$recordId.' record in usermemberfollowedargument table added a offline notification request');
                        return true;
                	}else{
                		log_message('debug',''.$recordId.' record in usermemberfollowedargument table fail to add offline notification request');
                        return false;
                	}
                }else{
                    log_message('debug','some thing we should take of at backend. '.$recordId.' cant fetch its argument owner status.');
                    return false;
                }
                break;
            case AGREE_NOTIFICATION:
                //process agree vote
                $query = $this->db->prepare('select argument.memberid as ownerid, usermember.online as owneronlinestatus from argument join usermembervotes on argument.id = usermembervotes.argumentid join usermember on argument.memberId = usermember.id where usermembervotes.id = :recordId LIMIT 1');
                $query->execute(array(":recordId"=>$recordId));
                if(($data = $query->fetchObject()) && ($data->ownerid != $loggedInMemberId)){ //to filter self notifications
                	$query = $this->db->prepare('INSERT INTO notification_queue (id,type,createdtime,lastmodified,memberid,recordId,msg) values (:id,:type,now(),now(),:memberid,:recordId,"test")');
                	if($query->execute(array(":id"=>$this->generateUniqueId(),":type"=>AGREE_NOTIFICATION,":memberid"=>$data->ownerid,":recordId"=>$recordId))){
                		log_message('debug',''.$recordId.' record in usermemberfollowedargument table added a offline notification request');
                        return true;
                	}else{
                		log_message('debug',''.$recordId.' record in usermemberfollowedargument table fail to add offline notification request');
                        return false;
                	}
                }else{
                    log_message('debug','some thing we should take of at backend. '.$recordId.' cant fetch its argument owner status.');
                    return false;
                }
                break;
            case COMMENT_NOTIFICATION:
                //process comment
                $query = $this->db->prepare('select argument.memberid as ownerid, usermember.online as owneronlinestatus from argument join argumentcomment on argument.id = argumentcomment.argumentid join usermember on argument.memberId = usermember.id where argumentcomment.id = :recordId LIMIT 1');
                $query->execute(array(":recordId"=>$recordId));
                if(($data = $query->fetchObject()) && ($data->ownerid != $loggedInMemberId)){ //to filter self notifications
                	$query = $this->db->prepare('INSERT INTO notification_queue (id,type,createdtime,lastmodified,memberid,recordId,msg) values (:id,:type,now(),now(),:memberid,:recordId,"test")');
                	if($query->execute(array(":id"=>$this->generateUniqueId(),":type"=>COMMENT_NOTIFICATION,":memberid"=>$data->ownerid,":recordId"=>$recordId))){
                		log_message('debug',''.$recordId.' record in argumentcomment table added a offline notification request');
                        return true;
                	}else{
                		log_message('debug',''.$recordId.' record in argumentcomment table fail to add offline notification request');
                        return false;
                	}
                }else{
                    log_message('debug','some thing we should take of at backend. '.$recordId.' cant fetch its argument owner status.');
                    return false;
                }
                break;
            case FOLLOW_ARGUMENT_NOTIFICATION:
                //process favorite
                $query = $this->db->prepare('select argument.memberid as ownerid, usermember.online as owneronlinestatus from argument join usermemberfollowedargument on argument.id = usermemberfollowedargument.argumentid join usermember on argument.memberId = usermember.id where usermemberfollowedargument.id = :recordId  LIMIT 1');
                $query->execute(array(":recordId"=>$recordId));
                if(($data = $query->fetchObject()) && ($data->ownerid != $loggedInMemberId)){ //to filter self notifications
                	$query = $this->db->prepare('INSERT INTO notification_queue (id,type,createdtime,lastmodified,memberid,recordId,msg) values (:id,:type,now(),now(),:memberid,:recordId,"test")');
                	if($query->execute(array(":id"=>$this->generateUniqueId(),":type"=>FOLLOW_ARGUMENT_NOTIFICATION,":memberid"=>$data->ownerid,":recordId"=>$recordId))){
                		log_message('info',''.$recordId.' record in usermemberfollowedargument table added a offline notification request');
                        return true;
                	}else{
                		log_message('info',''.$recordId.' record in usermemberfollowedargument table fail to add offline notification request');
                        return false;
                	}
                }else{
                    log_message('info','some thing we should take of at backend. '.$recordId.' cant fetch its argument owner status.');
                    return false;
                }

                break;
            case FOLLOW_MEMBER_NOTIFICATION:
                //process follow
                $query = $this->db->prepare('SELECT followedmember.id AS ownerid,followedmember.online AS owneronlinestatus FROM usermemberfollowedmember JOIN usermember AS followedmember ON usermemberfollowedmember.followedmemberid = followedmember.id where usermemberfollowedmember.id=:recordId LIMIT 1');
                $query->execute(array(":recordId"=>$recordId));
                if(($data = $query->fetchObject()) && ($data->ownerid != $loggedInMemberId)){ //to filter self notifications
                	$query = $this->db->prepare('INSERT INTO notification_queue (id,type,createdtime,lastmodified,memberid,recordId,msg) values (:id,:type,now(),now(),:memberid,:recordId,"test")');
                	if($query->execute(array(":id"=>$this->generateUniqueId(),":type"=>FOLLOW_MEMBER_NOTIFICATION,":memberid"=>$data->ownerid,":recordId"=>$recordId))){
                		log_message('info',''.$recordId.' record in usermemberfollowedusermember table added a offline notification request');
                        return true;
                	}else{
                		log_message('info',''.$recordId.' record in usermemberfollowedusermember table fail to add offline notification request');
                        return false;
                	}
                }else{
                    log_message('info','some thing we should take of at backend. '.$recordId.' cant fetch its argument owner status.');
                    return false;
                }

                break;
            case REPLY_NOTIFICATION:
                //process reply on a commnet
                $query = $this->db->prepare('select usermember.id AS ownerid, usermember.status from argumentcomment as reply join argument on reply.argumentId = argument.id
                                                join usermember on  argument.memberid = usermember.id  where reply.id = :recordId LIMIT 2
                                            UNION ALL
                                            select usermember.id AS ownerid, usermember.status from argumentcomment as reply join argumentcomment as comment on reply.parentId = comment.id
                                                join usermember on  comment.memberId = usermember.id where reply.id = :recordId LIMIT 2');
                $query->execute(array(":recordId"=>$recordId));
                if(($fetchdata = $query->fetchAll(PDO::FETCH_OBJ))){
                        $query = $this->db->prepare('INSERT INTO notification_queue (id,type,createdtime,lastmodified,memberid,recordId,msg) values (:id,:type,now(),now(),:memberid,:recordId,"test")');
                        $this->db->beginTransaction();
                        if(($fetchdata[0]->ownerid != $loggedInMemberId) ){ //to filter self notifications
                            if($query->execute(array(":id"=>$this->generateUniqueId(),":type"=>REPLY_TO_ARGUMENT_OWNER_NOTICTION,":memberid"=>$fetchdata[0]->ownerid,":recordId"=>$recordId))){
                                log_message('debug',''.$recordId.' record in argumentcomment table added a offline notification request');
                            }else{
                                log_message('debug',''.$recordId.' record in argumentcomment table fail to add offline notification request');
                            }
                        }
                        if(($fetchdata[1]->ownerid != $loggedInMemberId) ){ //to filter self notifications
                            if($query->execute(array(":id"=>$this->generateUniqueId(),":type"=>REPLY_TO_COMMENT_OWNER_NOTIFICATION,":memberid"=>$fetchdata[1]->ownerid,":recordId"=>$recordId))){
                                log_message('debug',''.$recordId.' record in argumentcomment table added a offline notification request');
                            }else{
                                log_message('debug',''.$recordId.' record in argumentcomment table fail to add offline notification request');
                            }
                        }
                        $this->db->commit();
                    return true;
                }else{
                    log_message('debug','some thing we should take care of at backend. '.$recordId.' cant fetch its argument owner status.');
                    return false;
                }
                break;
            case INVITE_TO_ARGUMENT:
                //processing inver users to arguement
                $query = $this->db->prepare('INSERT INTO notification_queue (id,type,createdtime,lastmodified,memberid,recordId,msg) values (:id,:type,now(),now(),:memberid,:recordId,"test")');
                if($query->execute(array(":id"=>$this->generateUniqueId(),":type"=>INVITE_TO_ARGUMENT,":memberid"=>$user,":recordId"=>$recordId))){
                    log_message('debug',''.$recordId.' argument record added to offline notification request to send invite to users.');
                    return true;
                }else{
                    log_message('debug',''.$recordId.' argument record failed to add offline notification request to send invite to users.');
                    return false;
                }
        }
    }

    function loadOfflineNotificationQueue(){
        try {
            $query = $this->db->prepare('CALL proc_offlinenotification()');
            $query->execute();
            if ($data = $query->fetchAll(PDO::FETCH_OBJ)) {
                return $data;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    function loadOnlineNotificationQueue($memberId){
        try {
            $query = $this->db->prepare('CALL proc_onlinenotification(:memberId)');
            $query->execute(array(":memberId"=>$memberId));
            if ($data = $query->fetchAll(PDO::FETCH_OBJ)) {
                return $data;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    function clearNotificationQueue($elements){
        try{
            $data = array();
            foreach($elements as $element){
                $query = $this->db->prepare('DELETE FROM notification_queue where id = :id');
                $query->bindValue(':id',$element,PDO::PARAM_STR);
                array_push($data,$query->execute());
            }
            return $data;
        }catch(PDOException $e){
            return $e->getMessage();
        }
    }
    
    function markNotificationAsRead($elements){
    	try {
    		$data = array();
    		foreach ($elements as $element){
    			$query = $this->db->prepare('UPDATE notification_queue SET status = 1,lastmodified= now() WHERE id = :id');
    			$query->bindValue(':id', $element, PDO::PARAM_STR);
    			array_push($data, $query->execute());
    		}
    		return $data;
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    function hideArgument($data){
    	try {
    		$data['id'] = $this->generateUniqueId();
    		$query = $this->db->prepare("INSERT INTO argumenthide (id, createdtime, argumentId, memberId) VALUES (:id, now(), :argumentId, :memberId)");
    		if($query->execute($data)){
    			return true;
    		}else {
    			return false;
    		}	
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    function reportSpam($data){
    	try {
    		$data['id'] = $this->generateUniqueId();
    		$query = $this->db->prepare("SELECT * FROM spamreport WHERE memberId = :memberId AND recordId=:recordId AND type=:type");
    		$query->execute(array(":memberId" => $data['memberId'],":type" => $data['type'],":recordId" => $data['recordId']));
    		if($query->rowCount() == 0){
    			$query = $this->db->prepare("INSERT INTO spamreport (id, createdtime,type, recordId, memberId) VALUES (:id, now(),:type, :recordId, :memberId)");
    			$query->execute($data);
    			return true;	
    		}else{
    			return false;		
    		}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    
    
	function getdailyReport() {
		
     try {
            $query = $this->db->prepare("SELECT (SELECT count(*) FROM usermember where DATE(createdtime) = CURDATE() -1) as yesterdaynewusercount, (SELECT count(*) from usermember) as totalusers,(SELECT count(*) FROM argument) as totalArguments, (SELECT COUNT(distinct argumentId) FROM ((SELECT argumentId FROM usermemberfollowedargument WHERE argumentId in (SELECT id from argument) AND DATE(createdtime) = CURDATE()- interval 1 day) UNION ALL (SELECT id as argumentId FROM argument WHERE DATE(lastmodified) = CURDATE()- interval 1 day) UNION ALL (SELECT argumentId FROM argumentcomment WHERE argumentId in (SELECT id from argument) AND DATE(createdtime) = CURDATE()- interval 1 day) UNION ALL (SELECT recordId as argumentId FROM spamreport WHERE recordId in (SELECT id from argument) AND DATE(createdtime) = CURDATE() - interval 1 day) UNION ALL (SELECT recordId as argumentId FROM spamreport WHERE recordId in (select id from argumentcomment) AND DATE(createdtime) = CURDATE()- interval 1 day) UNION ALL (SELECT argumentId FROM usermembervotes WHERE argumentId in (SELECT id from argument) AND DATE(createdtime) = CURDATE()- interval 1 day)) AS a) as yesterdaysactivearguments, (SELECT count(*) FROM argument where DATE(createdtime) = CURDATE() -1) as newargumentcount,(SELECT COUNT(DISTINCT id) as yesteractiveuser from ((SELECT memberId as id FROM usermemberfollowedargument WHERE DATE(createdtime) = curdate() and memberId in (select id from usermember)) UNION ALL (SELECT memberId as id FROM usermemberfollowedtopic WHERE DATE(createdtime) = curdate() and memberId in(select id from usermember))
UNION ALL (SELECT memberId as id FROM usermemberfollowedmember WHERE DATE(createdtime) = curdate() and memberId in (select id from usermember))
UNION ALL (SELECT memberId as id FROM usermembervotes WHERE DATE(createdtime) = curdate() and memberId in (select id from usermember))
UNION ALL (SELECT memberId as id FROM argumentcomment WHERE DATE(createdtime) = curdate() and memberId in (select id from usermember))
UNION ALL (SELECT memberId as id FROM argument WHERE DATE(lastmodified) = curdate() and memberId in (select id from usermember))
UNION ALL (SELECT memberId as id FROM spamreport WHERE DATE(createdtime) = curdate() and memberId in (select id from usermember))) as a) as activeusers,(SELECT COUNT(DISTINCT id) as 30dayscount from ((SELECT memberId as id FROM usermemberfollowedargument WHERE DATE(createdtime) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE() and memberId in (select id from usermember)) UNION ALL (SELECT memberId as id FROM usermemberfollowedtopic WHERE DATE(createdtime) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE() and memberId in(select id from usermember))
UNION ALL (SELECT memberId as id FROM usermemberfollowedmember WHERE DATE(createdtime) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE() and memberId in (select id from usermember))
UNION ALL (SELECT memberId as id FROM usermembervotes WHERE DATE(createdtime) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE() and memberId in (select id from usermember))
UNION ALL (SELECT memberId as id FROM argumentcomment WHERE DATE(createdtime) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE() and memberId in (select id from usermember))
UNION ALL (SELECT memberId as id FROM argument WHERE DATE(lastmodified) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE() and memberId in (select id from usermember))
UNION ALL (SELECT memberId as id FROM spamreport WHERE DATE(createdtime) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE() AND memberId in (select id from usermember))) as a) as Last30daysactiveusercount");
            $query->execute();
            if ($data = $query->fetchAll(PDO::FETCH_OBJ)) {
                return $data;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    	
    }
    
    
    
    public function saveFBInvitedUser($data) {
    	try{
    			$checkInvitedUser = $this->db->prepare("SELECT * FROM invitedmember WHERE fbid = :fbid AND invitedby = :memberId");
				$checkInvitedUser->execute(array(':fbid' => $data['fbid'], ':memberId' => $data['memberId']));
    			if($checkInvitedUser->rowCount()==0) {
    				$data['id'] = $this->generateUniqueId();
    				$query = $this->db->prepare("INSERT INTO invitedmember (id, name, fbid, invitationtype, invitedby, createdTime, lastmodified) VALUES (:id, :name, :fbid, :invitationtype, :memberId, now(), now())");
    				$res = $query->execute(array(':id' => $data['id'],'name'=>$data['name'],':fbid' => $data['fbid'],':invitationtype' => $data['invitationtype'],':memberId' => $data['memberId']));
    			} 	
      		 else {
    			$query = $this->db->prepare("UPDATE invitedmember set lastmodified = now(),inviationcount = inviationcount+1 WHERE fbid = :fbid and invitedby = :memberId");
    			$res = $query->execute(array(':fbid' => $data['fbid'], ':memberId' => $data['memberId']));
      		}
    		if($res) {
    			return true;
    		}else{
    			return false;		
    		}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    } 
   public function saveEmailInvitedUser($data) {
    	try{
    		$res = array();
    	   
    		foreach ($data['email'] as $key => $email){
    			
    			$checkInvitedUser = $this->db->prepare("SELECT * FROM invitedmember WHERE email = :email AND invitedby = :memberId");
				$checkInvitedUser->execute(array(':email' =>$email, ':memberId' => $data['memberId']));
				
				
				if($checkInvitedUser->rowCount()==0) { 
					$data['id'] = $this->generateUniqueId();
	    			$query = $this->db->prepare("INSERT INTO invitedmember (id, invitationtype, email, invitedby, createdTime, lastModified) VALUES (:id, :invitationtype, :email, :memberId, now(), now())");
	    			$result = $query->execute(array(':id' => $data['id'], ':invitationtype' => $data['invitationtype'], ':email' => $email, ':memberId' => $data['memberId']));
						
				} else {
					$query = $this->db->prepare("UPDATE invitedmember set lastmodified = now(),inviationcount = inviationcount+1 WHERE email = :email and invitedby = :memberId");
					$result = $query->execute(array(':email' => $email, ':memberId' => $data['memberId']));
				}
				
    			if($result) {
    				$res[$email] = true;
    			} else {
    				$res[$email] = false;		
    			}
    		
    		}
    	  return $res;
			
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
  }

