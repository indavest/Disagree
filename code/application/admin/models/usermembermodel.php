<?php

class UserMemberModel extends CI_Model {
	
	/* User Member Properties */
	private $id;
	private $oauth_provider;
	private $oauth_uid;
	private $email;
	private $username;
	private $createdTime;
	private $lastModified;
	private $password;
	private $gender;
	private $profilephoto;
	private $birthdate;
	private $interest;
	private $argumentCreatedCount;
	private $followerCount;
	private $followedCount;
	private $argumentFollowCount;
	private $topicFollowCount;
	private $participatedCount;
	private $lastloggedin;

        
	public function __construct() {}
	
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getOauth_provider() {
		return $this->oauth_provider;
	}

	public function setOauth_provider($oauth_provider) {
		$this->oauth_provider = $oauth_provider;
	}

	public function getOauth_uid() {
		return $this->oauth_uid;
	}

	public function setOauth_uid($oauth_uid) {
		$this->oauth_uid = $oauth_uid;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setUsername($username) {
		$this->username = $username;
	}

	public function getCreatedTime() {
		return $this->createdTime;
	}

	public function setCreatedTime($createdTime) {
		$this->createdTime = $createdTime;
	}
        
    public function getLastModified() {
        return $this->lastModified;
    }

    public function setLastModified($lastModified) {
        $this->lastModified = $lastModified;
    }

    public function getPassword() {
    	return $this->password;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	public function getGender() {
		return $this->gender;
	}

	public function setGender($gender) {
		$this->gender = $gender;
	}

	public function getProfilephoto() {
		return $this->profilephoto;
	}

	public function setProfilephoto($profilephoto) {
		$this->profilephoto = $profilephoto;
	}

	public function getBirthdate() {
		return $this->birthdate;
	}

	public function setBirthdate($birthdate) {
		$this->birthdate = $birthdate;
	}
	    
	public function getInterest() 
	{
	  return $this->interest;
	}
	
	public function setInterest($value) 
	{
	  $this->interest = $value;
	}
	
	public function getArgumentCreatedCount() 
	{
	  return $this->argumentCreatedCount;
	}
	
	public function setArgumentCreatedCount($value) 
	{
	  $this->argumentCreatedCount = $value;
	}
	
	public function getFollowerCount() 
	{
	  return $this->followerCount;
	}
	
	public function setFollowerCount($value) 
	{
	  $this->followerCount = $value;
	}
	
	public function getFollowedCount() 
	{
	  return $this->followedCount;
	}
	
	public function setFollowedCount($value) 
	{
	  $this->followedCount = $value;
	}
	
	public function getArgumentFollowCount() 
	{
	  return $this->argumentFollowCount;
	}
	
	public function setArgumentFollowCount($value) 
	{
	  $this->argumentFollowCount = $value;
	}
	
	public function getTopicFollowCount() 
	{
	  return $this->topicFollowCount;
	}
	
	public function setTopicFollowCount($value) 
	{
	  $this->topicFollowCount = $value;
	}
	public function getParticipatedCount()
	{
		return $this->participatedCount;
	}
	
	public function setParticipatedCount($value)
	{
		$this->participatedCount = $value;
	}
	
	public function getLastloggedin()
	{
		return $this->lastloggedin;
	}
	
	public function setLastloggedin($value)
	{
		$this->lastloggedin = $value;
	}
	
	
	public function getById($id) {
		try {
			$query = $this->db->prepare("SELECT user.* FROM admin_usermember user WHERE user.id='".$id."'");
			$query->execute();
            if( $data = $query->fetchObject()){
                return $data;
            }else {
                return false;
            }
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function authenticate($userMember) {
		try {
			$query = $this->db->prepare("SELECT * FROM admin_usermember WHERE email=:email AND password=:password");
			$query->execute(array(":email" => $userMember['email'], ":password" => md5($userMember['password'])));
			return $query->fetchObject();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}
	
	public function getAllUsers(){
		try {
			$query = $this->db->prepare("SELECT * FROM usermember");
			$query->execute();
			if($data = $query->fetchAll(PDO::FETCH_OBJ)){
				return $data;
			}else {
				return false;
			}	
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	public function getAllUsersWithinDateRange($fromdate, $todate){
		try {
			$query = $this->db->prepare("SELECT * FROM usermember where DATE(createdTime) BETWEEN :fromdate AND :todate");
			$query->execute(array(":fromdate"=>$fromdate, ":todate"=>$todate));
			if($data = $query->fetchAll(PDO::FETCH_OBJ)){
				return $data;
			}else {
				return false;
			}	
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	public function getUserData($userId){
		try {
			$defaulttime  = "0000-00-00 00:00:00";
			$query = $this->db->prepare("SELECT (SELECT count(*) FROM argument WHERE memberId = :userId) as argument, (SELECT count(*) FROM argumentcomment WHERE memberId=:userId) as comment, (SELECT count(*) FROM usermembervotes WHERE memberId=:userId AND vote=1) as agreed, (SELECT count(*) FROM usermembervotes WHERE memberId=:userId AND vote=0) as disagreed, (SELECT count(*) FROM usermemberfollowedargument WHERE memberId=:userId) as favorite,(SELECT count(*) FROM usermemberfollowedmember WHERE memberId=:userId) as following,(SELECT count(*) FROM usermemberfollowedmember WHERE followedmemberId=:userId) as followed,greatest((IFNULL((SELECT lastmodified FROM argumentcomment where memberId = :userId order by lastmodified desc limit 1),:defaulttime)),(IFNULL((SELECT lastmodified FROM usermemberfollowedargument where memberId = :userId order by lastmodified desc limit 1),:defaulttime)),(IFNULL((SELECT lastmodified  FROM usermembervotes where memberId = :userId order by lastmodified desc limit 1),:defaulttime)),(IFNULL((SELECT lastmodified as ut FROM usermemberfollowedtopic where memberId = :userId order by lastmodified desc limit 1),:defaulttime)),(IFNULL((SELECT lastmodified FROM usermemberfollowedmember where memberId = :userId order by lastmodified desc limit 1),:defaulttime)),(IFNULL((SELECT lastmodified FROM argument where memberId = :userId order by lastmodified desc limit 1),:defaulttime))) as lastaction, (SELECT count(*) FROM invitedmember WHERE invitedby=:userId) as invitedmembercount");
			$query->execute(array(":userId" => $userId,":defaulttime" => $defaulttime));
			if($data = $query->fetchObject()){
				return $data;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	public function getUserLastActionTime($userId) {
		try {
			$query = $this->db->prepare('(SELECT "4" as activitytype, id as id, createdtime as lastactiontime FROM usermemberfollowedargument WHERE memberId = :userId ORDER BY createdtime DESC LIMIT 1) UNION ALL (SELECT "6" as activitytype, id as id, createdtime as lastactiontime FROM usermemberfollowedtopic where memberId=:userId ORDER BY createdtime DESC LIMIT 1)
UNION ALL (SELECT "5" as activitytype, id as id, createdtime as lastactiontime FROM usermemberfollowedmember WHERE memberId = :userId ORDER BY createdtime DESC LIMIT 1)
UNION ALL (SELECT "7" as activitytype, id as id, createdtime as lastactiontime FROM usermembervotes WHERE memberId = :userId ORDER BY createdtime DESC LIMIT 1)
UNION ALL (SELECT "2" as activitytype, id as id, createdtime as lastactiontime FROM argumentcomment WHERE memberId = :userId ORDER BY createdtime DESC LIMIT 1)
UNION ALL (SELECT "1" as activitytype, id as id, lastmodified as lastactiontime FROM argument WHERE memberId = :userId ORDER BY lastmodified DESC LIMIT 1)
UNION ALL (SELECT "3" as activitytype, id as id, createdtime as lastactiontime FROM spamreport WHERE memberId = :userId ORDER BY createdtime DESC LIMIT 1) order by lastactiontime desc limit 1');
			$query->execute(array(":userId" => $userId));
			if($data = $query->fetchObject()){
				return $data;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function getFrontEndUserById($id){
		try {
			$query = $this->db->prepare("SELECT user.*, profile.* FROM usermember user, usermemberprofile profile WHERE user.id='".$id."' AND user.id = profile.memberId");
			$query->execute();
            if( $data = $query->fetchObject()){
                return $data;
            }else {
                return false;
            }
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}
	
	

}