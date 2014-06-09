<?php

class ArgumentModel extends CI_Model {
	
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
    
    public function __construct()
    {
    		
    }
    
    public function __destruct(){}
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getArgument()
    {
        return $this->argument;
    }

    public function setArgument($argument)
    {
        $this->argument = $this->dbConnectObj->clean($argument);
    }

    public function getCreatedtime()
    {
        return $this->createdtime;
    }

    public function setCreatedtime($createdtime)
    {
        $this->createdtime = $createdtime;
    }

    public function getLastmodified()
    {
        return $this->lastmodified;
    }

    public function setLastmodified($lastmodified)
    {
        $this->lastmodified = $lastmodified;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    public function setMemberId($memberId)
    {
        $this->memberId =  $memberId;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getTopic()
    {
        return $this->topic;
    }

    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getAgreed()
    {
        return $this->agreed;
    }

    public function setAgreed($value)
    {
        $this->agreed = $value;
    }

    public function getDisagreed()
    {
        return $this->disagreed;
    }

    public function setDisagreed($value)
    {
        $this->disagreed = $value;
    }

    public function getProfilephoto()
    {
        return $this->profilephoto;
    }

    public function setProfilephoto($value)
    {
        $this->profilephoto = $value;
    }

    public function setCommentsCount($commetCount)
    {
        $this->commentsCount = $commetCount;
    }

    public function getCommentsCount()
    {
        return $this->commentsCount;
    }
	
    function getArguments(){
    	try {
    		$query = $this->db->prepare("SELECT * FROM argument ORDER BY createdtime");
    		$query->execute();
    		if($data = $query->fetchAll(PDO::FETCH_OBJ)){
    			return $data;
    		}else{
    			return false;
    		}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    function getArgumentsWithinDateRange($fromdate, $todate){
    	try {
    		$query = $this->db->prepare("SELECT * FROM argument WHERE DATE(createdtime) BETWEEN :fromdate AND :todate ORDER BY createdtime");
    		$query->execute(array(":fromdate"=>$fromdate, ":todate"=>$todate));
    		if($data = $query->fetchAll(PDO::FETCH_OBJ)){
    			return $data;
    		}else{
    			return false;
    		}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    function getArgumentData($argumentId){
    	try {
    		$query = $this->db->prepare("SELECT (SELECT count(*) FROM argumentcomment WHERE argumentId=:argumentId AND uservote in (0,1,-2,-3) AND parentId is NULL) as commentCount, (SELECT maleagreed FROM argumentvotes WHERE argumentId=:argumentId) as maleAgreedCount,(SELECT femaleagreed FROM argumentvotes WHERE argumentId=:argumentId) as femaleAgreedCount, (SELECT maledisagreed FROM argumentvotes WHERE argumentId=:argumentId) as maleDisagreedCount, (SELECT femaledisagreed FROM argumentvotes WHERE argumentId=:argumentId) as femaleDisagreedCount, (SELECT count(*) FROM usermemberfollowedargument WHERE argumentId=:argumentId) as favoriteCount,(SELECT COUNT(*) FROM spamreport where recordId = :argumentId and type = 'argument') as reportCount,(SELECT u.username FROM usermember u, argument a WHERE a.id = :argumentId AND u.id = a.memberId) as userName,(SELECT u.id FROM usermember u, argument a WHERE a.id = :argumentId AND u.id = a.memberId) as userId");
    		$query->execute(array(":argumentId" =>$argumentId));
    		if($data = $query->fetchObject()){
    			return $data;
    		}else {
    			return false;
    		}
    	} catch (Exception $e) {
    	}
    }
    
    function getArgumentLastActionTime($argumentId){
    	try {
    		$query = $this->db->prepare("(SELECT 'followed by member' as activitytype, id as id, createdtime as lastactiontime FROM usermemberfollowedargument WHERE argumentId = :argumentId ORDER BY createdtime DESC LIMIT 1) UNION ALL (SELECT 'argument status change' as activitytype, id as id, lastmodified as lastactiontime FROM argument WHERE id = :argumentId AND createdtime != lastmodified ORDER BY lastmodified DESC LIMIT 1) UNION ALL (SELECT 'commenting argument' as activitytype, id as id, createdtime as lastactiontime FROM argumentcomment WHERE argumentId = :argumentId ORDER BY createdtime DESC LIMIT 1) UNION ALL (SELECT 'spam an argument' as activitytype, id as id, createdTime as lastactiontime FROM spamreport WHERE recordId = :argumentId ORDER BY createdTime DESC LIMIT 1) UNION ALL (SELECT 'spam an argugment comment' as activitytype, id as id, createdTime as lastactiontime FROM spamreport WHERE recordId in (select id from argumentcomment where argumentId = :argumentId) AND recordId IS NOT NULL ORDER BY createdTime DESC LIMIT 1) UNION ALL (SELECT 'vote an argument' as activitytype, id as id, createdtime as lastactiontime FROM usermembervotes WHERE argumentId = :argumentId ORDER BY createdtime DESC LIMIT 1) ORDER BY lastactiontime DESC LIMIT 1");
    		//$query = $this->db->prepare("(SELECT 'followed by member' as activitytype, id as id, createdtime as lastactiontime FROM usermemberfollowedargument WHERE argumentId = :argumentId ORDER BY createdtime DESC LIMIT 1) UNION ALL (SELECT 'argument status change' as activitytype, id as id, lastmodified as lastactiontime FROM argument WHERE id = :argumentId ORDER BY lastmodified DESC LIMIT 1) UNION ALL (SELECT 'commenting argument' as activitytype, id as id, createdtime as lastactiontime FROM argumentcomment WHERE argumentId = :argumentId ORDER BY createdtime DESC LIMIT 1) UNION ALL (SELECT 'spam an argument' as activitytype, id as id, createdTime as lastactiontime FROM spamreport WHERE recordId = :argumentId ORDER BY createdTime DESC LIMIT 1) UNION ALL (SELECT 'spam an argugment comment' as activitytype, id as id, createdTime as lastactiontime FROM spamreport WHERE recordId in (select id from argumentcomment where argumentId = :argumentId) AND recordId IS NOT NULL ORDER BY createdTime DESC LIMIT 1) UNION ALL (SELECT 'vote an argument' as activitytype, id as id, createdtime as lastactiontime FROM usermembervotes WHERE argumentId = :argumentId ORDER BY createdtime DESC LIMIT 1) ORDER BY lastactiontime DESC LIMIT 1");
    		$query->execute(array(":argumentId" =>$argumentId));
    		if($data = $query->fetchObject()){
    			return $data;
    		}else {
    			return false;
    		}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    function getById($argumentId){
    	try {
    		$query = $this->db->prepare("SELECT * FROM argument WHERE id=:id");
    		$query->execute(array(":id" => $argumentId));
    		if($data = $query->fetchObject()){
    			return $data;
    		}else {
    			return false;
    		}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    function delete($argumentId){
    	try {
    		$query = $this->db->prepare("DELETE FROM argument WHERE id=:id");
    		if($query->execute(array(":id" => $argumentId))){
    			return true;
    		}else {
    			return false;
    		}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
} 