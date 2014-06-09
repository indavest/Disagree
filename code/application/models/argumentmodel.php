<?php

class ArgumentModel extends DA_Model {
	
	private $id;
    private $title;
    private $argument;
    private $createdtime;
    private $lastmodified;
    private $memberId;
    private $status;
    private $topic;
    private $source;
    private $agreed;
    private $disagreed;
    private $profilephoto;
    private $commentsCount;
    
    public function __construct(){
    		
    }
    
    public function __destruct(){}
    
    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function getArgument(){
        return $this->argument;
    }

    public function setArgument($argument){
        $this->argument = $this->dbConnectObj->clean($argument);
    }

    public function getCreatedtime(){
        return $this->createdtime;
    }

    public function setCreatedtime($createdtime){
        $this->createdtime = $createdtime;
    }

    public function getLastmodified(){
        return $this->lastmodified;
    }

    public function setLastmodified($lastmodified){
        $this->lastmodified = $lastmodified;
    }

    public function getMemberId(){
        return $this->memberId;
    }

    public function setMemberId($memberId){
        $this->memberId =  $memberId;
    }

    public function getStatus(){
        return $this->status;
    }

    public function setStatus($status){
        $this->status = $status;
    }

    public function getTopic(){
        return $this->topic;
    }

    public function setTopic($topic){
        $this->topic = $topic;
    }

    public function getSource(){
        return $this->source;
    }

    public function setSource($source){
        $this->source = $source;
    }

    public function getAgreed(){
        return $this->agreed;
    }

    public function setAgreed($value){
        $this->agreed = $value;
    }

    public function getDisagreed(){
        return $this->disagreed;
    }

    public function setDisagreed($value){
        $this->disagreed = $value;
    }

    public function getProfilephoto(){
        return $this->profilephoto;
    }

    public function setProfilephoto($value){
        $this->profilephoto = $value;
    }

    public function setCommentsCount($commetCount){
        $this->commentsCount = $commetCount;
    }

    public function getCommentsCount(){
        return $this->commentsCount;
    }
	
    public function create($data){
        try {

        	$data['id']=$this->generateUniqueId();
            $query = $this->db->prepare("INSERT INTO `argument` (id, title, argument, createdtime, memberId, status,topic,source) VALUES(:id, :title, :argument, now(), :memberId, :status, :topic, :source)");
            if ($query->execute($data)) {
                $query = $this->db->prepare("SELECT * FROM argument WHERE id=:id");
                $query->execute(array(":id" => $data['id']));
                return $query->fetchObject();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function update($data){
        try {
            $query = $this->db->prepare("update `argument` set title=:title, argument=:argument, lastmodified=now(), memberId=:memberId, status=:status,topic=:topic,source=:source where id=:id");
            if ($query->execute($data)) {
                $query = $this->db->prepare("SELECT * FROM argument WHERE id=:id");
                $query->execute(array(":id" => $data['id']));
                return $query->fetchObject();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getById($argumentId){
        try {
            $query = $this->db->prepare("SELECT argument.*,usermemberprofile.profilephoto as profilephoto,ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed , 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed , 0) AS disagreed,ifnull(count(argumentcomment.commenttext) , 0) as commentsCount,ifnull(argumentvotes.maleagreed, 0) as maleagreed,ifnull(argumentvotes.femaleagreed,0) as femaleagreed,ifnull(argumentvotes.maledisagreed,0) as maledisagreed,ifnull(argumentvotes.femaledisagreed ,0) as femaledisagreed FROM argumentcomment,usermember,usermemberprofile,argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.id = :argumentId and argumentcomment.argumentId = :argumentId and argument.memberid = usermember.id ORDER BY createdTime DESC");
            $query->execute(array(":argumentId" => $argumentId));
            if ($data = $query->fetchObject()) {
                return $data;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        } 
    }

    public function getByMemberAndArgumentId($memberId,$argumentId){
        try{
            $memberId = is_null($memberId)?'':$memberId;        //to fix null issue. query returns null if you pass any argument null. insted pass empty string.
            $query = $this->db->prepare("SELECT
                                            argument.*,
                                            usermemberprofile.profilephoto as profilephoto,
                                            ifnull(count(argumentcomment.commenttext) , 0) as commentsCount,
                                            ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed , 0) AS agreed,
                                            ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed,
                                            ifnull(argumentvotes.maleagreed, 0) as maleagreed,
                                            ifnull(argumentvotes.femaleagreed,0) as femaleagreed,
                                            ifnull(argumentvotes.maledisagreed,0) as maledisagreed,
                                            ifnull(argumentvotes.femaledisagreed ,0) as femaledisagreed,
                                            (select count(id) from usermemberfollowedargument  where memberId=:memberId and argumentId=:argumentId) as isFollowing,
                                            (select count(id) from usermembervotes where memberId=:memberId and argumentId=:argumentId) as isVoted
                                        FROM
                                          argument
                                            LEFT JOIN argumentcomment ON argument.id = argumentcomment.argumentId
                                            LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId
                                            LEFT JOIN usermember ON argument.memberId = usermember.id
                                            LEFT JOIN usermemberprofile ON argument.memberId = usermemberprofile.memberId
                                        WHERE
                                            argument.id = :argumentId
                                            AND argumentcomment.parentId IS NULL
                                       ORDER BY createdTime DESC"
            );
            $query->execute(array(":memberId"=>$memberId,":argumentId"=>$argumentId));
            if($data = $query->fetchObject()){
                return $data;
            }else{
                return false;
            }
        }catch(PDOException $e){
            return $e->getMessage();
        }
    }

	public function delete() {
		try {
			//$query = $this->dbConnect->prepare("DELETE FROM argument WHERE id=:argumentId AND memberId=:memberId");
			//$query = $this->dbConnect->query("DELETE FROM argument WHERE id=".$this->argumentId." AND memberId=".$this->memberId);
			$query = $this->db->query("DELETE FROM argument WHERE id=".$this->getId()."AND memberId=".$this->getMemberId()."");
			if ($query)//->execute(array(":argumentId" =>  $argumentId , ":memberId" => $memberId))) {
			{
				return true;
			} else {
				return false;
			}
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}
    
	public function getByNumber($lowerLimit, $upperLimit){
    	try {
            $query = $this->db->prepare("SELECT argument.*,count(argumentcomment.id) commentsCount,ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed , 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId LEFT JOIN argumentcomment ON argument.id = argumentcomment.argumentId WHERE argumentcomment.parentId IS NULL GROUP BY argument.id ORDER BY argument.createdTime DESC LIMIT $lowerLimit, $upperLimit");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    
    public function getByNumberByAjax($lowerLimit, $upperLimit){
    	try {
            $query = $this->db->prepare("SELECT argument.*,usermemberprofile.profilephoto ,usermember.username ,usermemberprofile.location,usermemberprofile.fullname,count(argumentcomment.id) commentsCount,ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed ,0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed ,0) AS disagreed,ifnull(count(argumentcomment.commenttext) , 0) as commentsCount FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId LEFT JOIN argumentcomment ON argument.id = argumentcomment.argumentId LEFT JOIN usermember ON argument.memberId = usermember.id JOIN usermemberprofile ON argument.memberId = usermemberprofile.memberid GROUP BY argument.id ORDER BY argument.createdTime DESC LIMIT $lowerLimit,$upperLimit");
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

	public function getTimelineByActivity($memberId, $lowerLimit, $limitCount){
    	try {
    		$query = $this->db->prepare("CALL proc_loggedInMemberTimeline(:memberId, :lowerLimit, :limitCount)");
    		$query->execute(array(":memberId" => $memberId, ":lowerLimit" => $lowerLimit, ":limitCount" => $limitCount));
    		if($data = $query->fetchAll(PDO::FETCH_OBJ)){
    			return $data;
    		}else {
    			return false;
    		}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    public function getTimelineByInterest($interest){
    	try {
    		$query = $this->db->prepare("SELECT * FROM (SELECT argument.*,count(argumentcomment.id) commentsCount,ifnull(argumentvotes.maleagreed  + argumentvotes.femaleagreed, 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0)  AS disagreed FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId LEFT JOIN argumentcomment ON argument.id = argumentcomment.argumentId GROUP BY argument.id ORDER BY argument.createdTime DESC) resultTable WHERE topic IN (:interest) ORDER BY commentsCount DESC");
    		$query->execute(array(":interest" => $interest));
    		if($data = $query->fetchAll(PDO::FETCH_OBJ)){
    			return $data;
    		}else{
    			return false;
    		}
    	} catch (Exception $e) {
    	}
    	
    }

    public function getCommentCount(){
        try {
            $query = $this->db->prepare("SELECT COUNT(*) count FROM argumentcomment WHERE argumentId=:argumentId");
            $query->execute(array(":argumentId" => $this->getId()));
            return $query->fetchObject();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getByKeyword($keyword){
        try {
            $query = $this->db->prepare("SELECT argument.*,ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0)  AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed FROM argument,argumentvotes WHERE argument.title LIKE :keyword AND argument.id = argumentvotes.argumentId ORDER BY createdtime DESC");
            $query->execute(array(":keyword" => '%' . $keyword . '%'));
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getByTopic($topicId, $limit, $memberId){
        try {
            $query = $this->db->prepare("SELECT argument.*,count(argumentcomment.id) as commentsCount,ifnull((argumentvotes.maleagreed + argumentvotes.femaleagreed),0) AS agreed, ifnull((argumentvotes.maledisagreed + argumentvotes.femaledisagreed),0) AS disagreed FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId LEFT JOIN argumentcomment ON argument.id = argumentcomment.argumentId WHERE argument.topic = :topicId AND argument.id NOT IN (SELECT argumentId FROM argumenthide WHERE memberId=:memberId) AND argumentcomment.parentId IS NULL GROUP BY argument.id ORDER BY createdTime DESC LIMIT $limit, ".ARGUMENT_AJAX_FETCH_COUNT);
            $query->execute(array(":topicId" => $topicId,":memberId" => $memberId));
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getTopByNumber($number){
        try {
            $query = $this->db->prepare("SELECT argument.*,topic.topic,count(argument.id) as commentCount,ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0)  AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed FROM argument,argumentcomment,argumentvotes,topic WHERE argument.id=argumentcomment.argumentId AND argument.id = argumentvotes.argumentId AND argument.topic = topic.id GROUP BY argument.id ORDER BY (agreed+disagreed+count(argument.id)) DESC LIMIT :number");
            $query->execute(array(":number" => $number));
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getMostPopular(){
        try {
            $query = $this->db->prepare("SELECT argument.*,count(argumentcomment.id) commentsCount,ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0)  AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed FROM argument,argumentcomment,argumentvotes WHERE argument.id = argumentcomment.argumentId AND argument.id = argumentvotes.argumentId GROUP BY argument.id ORDER BY (agreed + disagreed) DESC");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getMostActive(){
        try {
            $query = $this->db->prepare("SELECT argument.*,count(argumentcomment.id),ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0)  AS disagreed FROM argument,argumentcomment,argumentvotes WHERE argument.id = argumentcomment.argumentId AND argument.id = argumentvotes.argumentId GROUP BY argument.id ORDER BY COUNT(argumentcomment.id) DESC");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getFollowingMemberFavoritesByMemberId($memberId){
    	try {
    		$query = $this->db->prepare("SELECT * FROM argument WHERE id IN (SELECT argumentId FROM usermemberfollowedargument WHERE memberId IN (SELECT followedmemberId FROM usermemberfollowedmember WHERE memberId = :memberId)) AND memberId != :memberId");
    		$query->execute(array(":memberId" => $memberId));
    		return $query->fetchAll(PDO::FETCH_OBJ);
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    public function syncFeed($timeInterval, $memberId){
    	try {
    		$query = $this->db->prepare("CALL proc_loggedInMemberArgumentSync(:memberId, :timeInterval)");
    		$query->execute(array(":memberId" => $memberId, ":timeInterval" => $timeInterval));
    		if($data = $query->fetchAll(PDO::FETCH_OBJ)){
    			return $data;
    		}else {
    			return false;
    		}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    public function syncFeedData($argumentIdArray){
    	try {
    		$values = count($argumentIdArray);
            if($values > 0){
	        $criteria = sprintf("?%s", str_repeat(",?", ($values ? $values-1 : 0)));
	        //Returns ?,?,?
                $sql = sprintf("SELECT argument.id, ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0)  AS disagreed,count(argumentcomment.commenttext) as commentsCount FROM argument left join argumentcomment on argumentcomment.argumentid = argument.id left join argumentvotes on argumentvotes.argumentid = argument.id WHERE argument.id in (%s) AND argumentcomment.parentId IS NULL group by argument.id ORDER BY argument.createdTime DESC", $criteria);
	        $query= $this->db->prepare($sql);
	        $query->execute($argumentIdArray);
    		if($data = $query->fetchAll(PDO::FETCH_OBJ)){
    			return $data;
    		}else {
    			return false;
    		}
            }else{return false;}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }

    public function getCreatedArgumentsbyUserMember($memberId){
        try {
            //$query = $this->dbConnect->prepare("SELECT argument.*,(ifnull(argumentvotes.maleagreed, 0) + ifnull(argumentvotes.femaleagreed, 0) + ifnull(argumentvotes.generalagreed, 0)) AS agreed, (ifnull(argumentvotes.maledisagreed, 0) + ifnull(argumentvotes.femaledisagreed, 0) + ifnull(argumentvotes.generaldisagreed, 0)) AS disagreed FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.memberId = '" . $memberId . "'ORDER BY createdTime DESC" );
            $query = $this->db->prepare("SELECT argument.*, ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed,count(argumentcomment.commenttext) as commentsCount FROM argument left join argumentcomment on argumentcomment.argumentid = argument.id left join argumentvotes on argumentvotes.argumentid = argument.id WHERE argument.id in (select argument.id from argument where argument.memberId=:memberId) group by argument.id ORDER BY argument.createdTime DESC");
            $query->execute(array(':memberId' => $memberId));
            if ($query->rowCount() > 0) {
                return $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getAjaxCreatedArgumentsbyUserMember($memberId,$lowerlimit,$noofrecords){
        try {
            $query = $this->db->prepare("SELECT argument.*, ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0)  AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed,count(argumentcomment.commenttext) as commentsCount FROM argument left join argumentcomment on argumentcomment.argumentid = argument.id left join argumentvotes on argumentvotes.argumentid = argument.id WHERE argument.memberId=:memberId AND argumentcomment.parentId IS NULL group by argument.id ORDER BY argument.createdTime DESC LIMIT $lowerlimit, $noofrecords");
            $query->bindValue(":memberId" ,$memberId ,PDO::PARAM_STR);
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
		
	public function getCommentedArgumentsbyUserMember($memberId){
		try {
			//$query = $this->dbConnect->prepare("SELECT argument.*,(ifnull(argumentvotes.maleagreed, 0) + ifnull(argumentvotes.femaleagreed, 0) + ifnull(argumentvotes.generalagreed, 0)) AS agreed, (ifnull(argumentvotes.maledisagreed, 0) + ifnull(argumentvotes.femaledisagreed, 0) + ifnull(argumentvotes.generaldisagreed, 0)) AS disagreed FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.id IN (SELECT argumentId from argumentcomment WHERE memberId=:memberId) AND memberId <> :memberId ORDER BY createdTime DESC");
			$query = $this->db->prepare(" SELECT argument.*, ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0)  AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed, count(argumentcomment.commenttext) as commentsCount FROM argument left join argumentcomment on argumentcomment.argumentid = argument.id left join argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.id IN (SELECT argumentId from argumentcomment WHERE argumentcomment.memberId=:memberId) AND argument.memberId <> '" . $memberId . "' group by argument.id ORDER BY createdTime DESC");
			$query->execute(array(':memberId' => $memberId));
			if ($query->rowCount()>0){
				return $query->fetchAll(PDO::FETCH_OBJ);
			}else {
				return false;
			}
		} catch (PDOException $e) {
			return $e->getMessage();
		}

	}
    
	public function getMemberInterestedArguments($memberId, $limit){
		try {
			$query = $this->db->prepare("SELECT DISTINCT * FROM (
                                          SELECT argument.*,
                                          ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0) As agreed,
                                          ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed,
                                          count(argumentcomment.commenttext) as commentsCount
                                          FROM argument LEFT JOIN argumentcomment on argumentcomment.argumentid = argument.id
                                          LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId
                                          WHERE argumentcomment.parentId IS NULL AND
                                          argument.id IN (
                                          SELECT argumentId FROM usermemberfollowedargument
                                          WHERE memberId IN (
                                          SELECT followedmemberId FROM usermemberfollowedmember WHERE memberId = '6136416949')
                                          )
                                          AND argument.memberId <> '6136416949' group by argument.id
                                         UNION
                                         SELECT argument.*,
                                         ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0)  AS agreed,
                                         ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed,
                                         COUNT(argumentcomment.id) commentsCount
                                         FROM argument LEFT JOIN argumentcomment ON argument.id = argumentcomment.argumentId
                                         LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId
                                         WHERE argumentcomment.parentId IS NULL AND
                                         argument.memberId <> '' AND
                                         argumentcomment.memberId <> '6136416949' AND
                                         argument.Id NOT IN (
                                         SELECT argumentId FROM usermemberfollowedargument WHERE memberId='6136416949'
                                         UNION
                                         SELECT argumentId FROM usermemberfollowedargument
                                         WHERE memberId IN (SELECT followedmemberId FROM usermemberfollowedmember WHERE memberId = '6136416949') group by argument.id
                                     )
                                     GROUP BY argument.id ORDER BY (agreed + disagreed) DESC
                                     ) AS tmp
                                     WHERE tmp.id NOT IN (SELECT argumentId from argumenthide where memberId='6136416949') LIMIT $limit, ".ARGUMENT_AJAX_FETCH_COUNT
                                        );
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
	
	public function getArgumentsByKeyword($keyword){
    	try {
    		$query = $this->db->prepare("SELECT argument.*,count(argumentcomment.id) commentsCount,ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed , 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId LEFT JOIN argumentcomment ON argument.id = argumentcomment.argumentId AND argument.title LIKE :keyword GROUP BY argument.id ORDER BY argument.createdTime DESC");
    		$query->execute(array(":keyword" => "%".$keyword."%"));
    		if($data = $query->fetchAll(PDO::FETCH_OBJ)){
    			return $data;
    		}else {
    			return false;
    		}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }

    public function getFollowingUserCountByArgumentId($argumentId){
        try{
            $query = $this->db->prepare("select count(id) as followCount from usermemberfollowedargument where argumentId=:argumentId");
            $query->execute(array(":argumentId"=>$argumentId));
            if($data = $query->fetchObject()){
                return $data;
            }else{
                return false;
            }
        }catch (PDOException $e){
            return $e->getMessage();
        }
    }
} 