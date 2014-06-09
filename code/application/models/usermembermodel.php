<?php

class UserMemberModel extends DA_Model {
	
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
	
	public function getById($id) {
		try {
			$query = $this->db->prepare("select usermember.id, usermember.oauth_provider, usermember.oauth_uid, usermember.email, usermember.status, usermember.username, usermember.createdTime, usermember.lastModified, usermember.lastloggedin, usermember.lastloggedout, usermember.online,usermemberprofile.memberId, usermemberprofile.createdTime, usermemberprofile.lastModified, usermemberprofile.gender, usermemberprofile.profilephoto, usermemberprofile.interest, usermemberprofile.birthdate, usermemberprofile.fullname, usermemberprofile.location,usermemberprofile.notifyFlag from usermember join usermemberprofile on usermember.id = usermemberprofile.memberid where usermember.id=:id");
			$query->execute(array(":id" => $id));
            if( $data = $query->fetchObject()){
                return $data;
            }else {
                return false;
            }
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

    /**
     * checkUserByUsernameOrEmail
     *
     * Uses: check username or email by ajax call , user creation check before fbuser creation (base_controller->checkUser)
     * @param $input
     * @return bool|string
     */
    public function checkUserByUsernameOrEmail($input){
		try {
			$query = $this->db->prepare("SELECT * FROM usermember WHERE username=:input OR email=:input");
			$query->execute(array(":input" => $input));
			if($query->fetchObject()){
				return true;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
		
	}

    /**
     * checkUserByUserNameAndEmail
     *
     * Uses: checkuser before site user creation (base_controlelr->userMemberCreate)
     * @param $username
     * @param $email
     * @return bool|string
     */
    public function checkUserByUsernameAndEmail($username, $email){
		try {
			$query = $this->db->prepare("SELECT * FROM usermember WHERE username=:username AND email=:email");
			$query->execute(array(":username" => $username, ":email" => $email));
			if($query->fetchObject()){
				return true;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

    /**
     * createUser - creates a site user
     *
     * creates a user only if username and email are unique (facebook user considered)
     * Uses: base_controller->userMemberCreate
     *
     * @param $data
     * @return array
     */
    public function createUser($data) {
		try {
			if ($data['id'] == null||$data['id'] == ''){$id=$this->generateUniqueId();}
			else {$id=$data['id'];}

			$conditionQuery = $this->db->prepare("SELECT * from usermember WHERE EXISTS (SELECT * FROM usermember where id=:memberId )");
			$conditionQuery->execute(array(':memberId'=>$id));
			if($conditionQuery->rowCount()==0){
				$checkUser = $this->db->prepare("SELECT * from usermember where email = :email");
				$checkUser->execute(array(':email'=>  $data['email']));
				if( $checkUser->rowCount() == 0 ){
					$activationKey = $this->generateUniqueId().$this->generateUniqueId().$this->generateUniqueId().$this->generateUniqueId().$this->generateUniqueId();
					$createMember = $this->db->prepare("INSERT INTO usermember (id, oauth_provider,oauth_uid,email,username,createdTime,password) VALUES(:id,:oauth_provider,:oauth_uid,:email,:username,now(),:password)");
					$createMemberProfile = $this->db->prepare('INSERT INTO usermemberprofile (memberId,createdtime,gender,location) VALUES (:memberId,now(),:gender,:location)');
				    if($createMember->execute(array(':id' => $id,':oauth_provider'=>$data['oauth_provider'],':oauth_uid'=>$data['oauth_uid'],':email'=>$data['email'],':username'=>$data['username'],":password"=>$data['password'])) && $createMemberProfile->execute(array(':memberId' => $id,":gender" => $data['gender'],":location"=>$data['location']))){
  				    $activatingMember = $this->db->prepare("INSERT INTO useractivation (id,memberId,activationKey) VALUES (:id,:memberId,:activationKey)");
				    if($activatingMember->execute(array(':id'=>$this->generateUniqueId(),':memberId'=>$id,':activationKey'=>$activationKey)))
				    	return array("activationkey"=>$activationKey,"memberId"=>$id) ;
				    else
                        echo 'user email already exists';
				    }
				}else {
					echo 'user name / email already exists';
				}
			}
			else {
				$pswd ='';
				if($data['password']!= null||$data['password']!=''){
			    $pswd =',password=:password';
				}
				$prepareStatement = "UPDATE usermember SET email=:email,username=:username".$pswd.",lastModified=now() WHERE id=:memberId";
				$query = $this->db->prepare($prepareStatement);
				$bindingArray = array(':memberId' => $id,':email'=>$data['email'],':username'=>$data['username']);
				if($data['password']!= null||$data['password']!=''){
					$bindingArray[':password']=$data['password'];
				}
				return $query->execute($bindingArray);
			}
		} catch (PDOException $exc) {
			echo $exc->getMessage();
		}
	}

    public  function updateUserProfilePic($memberId,$profilePic){
        try{
            $query = $this->db->prepare('Update usermemberprofile set profilephoto = :profilephoto, lastmodified = now() where memberid = :memberId');
            if($query->execute(array(':memberId'=>$memberId,":profilephoto"=>$profilePic))){
                return true;
            }else{
                return false;
            }
        }catch(PDOException $e){
            return $e->getMessage();
        }
    }
	
	public function checkUserAndActive($activationKey, $memberId){
		$query = $this->db->prepare("SELECT * FROM useractivation WHERE memberId=:memberId AND activationKey=:activationKey");
		$query->execute(array(":memberId" => $memberId, ":activationKey" => $activationKey));
		if($query->fetchObject()){
			$updateStatement = $this->db->prepare("UPDATE usermember SET status=:status, lastloggedin=now(), online=1 WHERE id=:memberId");
			if( $updateStatement->execute(array(":status" =>'1',":memberId" =>$memberId))){
				$query = $this->db->prepare("DELETE FROM useractivation WHERE memberId=:memberId AND activationKey=:activationKey ");
				return $query->execute(array(":memberId" =>$memberId,":activationKey"=>$activationKey));
			}		
		}else{
			return false;
		}
	}


    public function editUser($data)
    {
         try {
	        $query = $this->db->prepare('update usermemberprofile set fullname= :fullname,location= :location,birthdate= :birthdate,lastModified= now(),interest = :interest where memberid= :memberId');
             return $query->execute(array(":memberId"=>$data['memberid'],":fullname"=>$data['fullname'],":location"=>$data['location'],":birthdate"=>$data['dob'],":interest"=>$data['interests']));
         }catch(PDOException $e){
             return $e->getMessage();
         }
    }
	/*   public function editUser($memberId){
	 try {
	 $query = $this->dbConnect->prepare(" UPDATE usermember SET email=:email,username=:username,lastModified=now(),password=:password,gender=:gender,profilephoto=:profilephoto,birthdate=:birthdate WHERE id=:memberId");
	 $query->execute(array(':memberId'=>$memberId));
	 if ($query->rowCount() > 0) {
	 return $query->fetchAll(PDO::FETCH_CLASS, 'ArrayObject');
	 } else {
	 return false;
	 }
	 } catch (Exception $e) {
	 }
	 }*/

    /*
     * @params
     * $activation key
     * $pass
     * both are required
     */
    public function forgetPassReset($activationKey, $pass)
    {
        try {
            if ($pass == '') { //user came with activation key and asking for reset page
                $query = $this->db->prepare('select username from usermember where id=(select memberid from useractivation where activationkey=:activationkey)');
                $query->execute(array(":activationkey" => $activationKey));
                if ($data = $query->fetchAll(PDO::FETCH_OBJ)) {
                    return $data;
                } else {
                    return false;
                }
            }
            else { //user restting password
                $query = $this->db->prepare('CALL proc_forgetpass(:activationkey,:md5pass)');
                $query->execute(array(":activationkey" => $activationKey, ":md5pass" => $pass));
                if ($query->rowCount() > 0) {
                    return true;
                }
                else {
                    return false;
                }
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    /*
     * @params
     * $email
     * $activation key
     * both are required
     */
    public function forgetPassRequest($email)
    {
        try {
            $id = $this->generateUniqueId();
            $activationKey = $this->generateUniqueId() . $this->generateUniqueId() . $this->generateUniqueId() . $this->generateUniqueId() . $this->generateUniqueId();
            $query = $this->db->prepare('insert into useractivation select :id as id, u.id as memberid, :activationkey as activationkey from usermember u where u.email=:email AND oauth_uid IS NULL ON DUPLICATE KEY UPDATE activationkey=:activationkey');
            $query->execute(array(":id" => $id, ":email" => $email, ":activationkey" => $activationKey));
            if ($query->rowCount() > 0) {
                return $activationKey;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function changePassword($memberId,$passwd){
        try{
            $query = $this->db->prepare('update usermember set password= :passwd, lastmodified = now() where id=:memberid');
            if($query->execute(array(":passwd"=>$passwd,":memberid"=>$memberId))){
                return true;
            }else{
                return false;
            }

        }catch(PDOException $e){
            return $e->getMessage();
        }
    }
    public function chnageNotificationSettings($memberId,$notification){
        try{
            $query = $this->db->prepare('update usermemberprofile set notifyFlag= :notify, lastmodified = now() where memberId=:memberid');
            $query->bindValue(":notify" , $notification, PDO::PARAM_BOOL);
            $query->bindValue(":memberid" , $memberId , PDO::PARAM_STR);
            if($query->execute()){
                return true;
            }else{
                return false;
            }

        }catch(PDOException $e){
            return $e->getMessage();
        }
    }

	public function create($data) {
		if ($data['password'] != null) {
			$data['password'] = md5($data['password']);
		}
		try {
			$checkUser = $this->db->prepare("SELECT id FROM usermember WHERE email =:email");
			$checkUser->execute(array(':email' => $data['email']));
			if ($checkUser->rowCount() == 0) {
				$data['id'] = $this->generateUniqueId();
				$createMember = $this->db->prepare("INSERT INTO usermember (id, oauth_provider,oauth_uid,email,username,createdTime,password) VALUES(:id,:oauth_provider,:oauth_uid,:email,:username,now(),:password)");
				return $createMember->execute($data);
			} else {
				return false;
			}
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function authenticate($userMember) {
		try {
			$query = $this->db->prepare("SELECT * FROM usermember WHERE (email=:email OR username=:email) AND password=:password AND oauth_provider IS NULL");
			$query->execute(array(":email" => $userMember['email'], ":password" => md5($userMember['password'])));
			return $query->fetchObject();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getGoogleUser($oauth_uid, $oauth_provider) {
		try {
			$query = $this->db->prepare("SELECT * FROM `usermember` WHERE oauth_uid = :oauth_uid and oauth_provider = :oauth_provider");
			$query->execute(array(':oauth_uid' => $oauth_uid, ':oauth_provider' => $oauth_provider));
			return $query->fetchObject();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getFacebookUser($oauth_uid, $oauth_provider) {
		try {
			$query = $this->db->prepare("SELECT * FROM usermember WHERE oauth_uid = :oauth_uid and oauth_provider = :oauth_provider");
			$query->execute(array(':oauth_uid' => $oauth_uid, ':oauth_provider' => $oauth_provider));
			return $query->fetchObject();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function createFbUser($data) {
		try {
			$checkUser = $this->db->prepare("SELECT id FROM usermember WHERE oauth_uid = :oauth_uid");
			$checkUser->execute(array(':oauth_uid' => $data['oauth_uid']));
			if ($checkUser->rowCount() == 0) {
				$data['id'] = $this->generateUniqueId();
				print_r($data);
				$createMember = $this->db->prepare("INSERT INTO `usermember` (id,oauth_provider, oauth_uid, username, email,createdtime) VALUES (:id, :oauth_provider, :oauth_uid, :username, :email, now())");
				if($createdUser = $createMember->execute(array(":id" => $data['id'],":oauth_provider" => $data['oauth_provider'],":oauth_uid" => $data['oauth_uid'], ":username" => $data['username'], ":email" => $data['email']))){
					$query = $this->db->prepare("INSERT INTO usermemberprofile (memberId,createdtime,gender,profilephoto,location) VALUES (:id, now(), :gender, :userphoto,:location)");
					$query->execute(array(":id" => $data['id'], ":gender" => $data['gender'], ":userphoto" => $data['userphoto'],":location" => $data['location']));
					return $createdUser;
				}
			} else {
				return false;
			}
		} catch (PDOException $e) {
			return false;
		}
	}

    function  checkFBUserData($oauth_uid,$memberId){
        try{
           $query = $this->db->prepare('select user.id, user.oauth_uid, (select count(id) from usermemberfollowedmember AS followedmember where followedmember.memberId=:memberId AND followedmember.followedmemberId=user.id) as status,"user" as type from usermember AS user where FIND_IN_SET(user.oauth_uid,:oauth_Ids) UNION SELECT i.id as id,i.fbid AS oauth_uid, 1 AS status,"invitedmember" as type FROM invitedmember i, usermember u where NOT FIND_IN_SET(u.oauth_uid,:oauth_Ids) AND FIND_IN_SET(i.fbid,:oauth_Ids) AND i.invitedby=:memberId');
          	//$query = $this->db->prepare('select user.id, user.oauth_uid, (select count(id) from usermemberfollowedmember AS followedmember where followedmember.memberId=:memberId AND followedmember.followedmemberId=user.id) as status from usermember AS user where FIND_IN_SET(user.oauth_uid,:oauth_Ids)');
        	$query->execute(array(':memberId'=>$memberId,":oauth_Ids"=>$oauth_uid));
            if($data = $query->fetchAll(PDO::FETCH_ASSOC)){
                return $data;
            }else{
                return false;
            }
        }catch (PDOException $e){
            return $e->getMessage();
        }

    }

	public function createGoogleUser($data) {
		try {
			$checkUser = $this->db->prepare("SELECT id FROM usermember WHERE oauth_uid = :oauth_uid and oauth_provider='Google'");
			$checkUser->execute(array(':oauth_uid' => $data['oauth_uid']));
			if ($checkUser->rowCount() == 0) {
				$data['id'] = $this->generateUniqueId();
				$createMember = $this->db->prepare("INSERT INTO `usermember` ( id,oauth_provider,oauth_uid,username,email,gender,profilephoto,createdtime) VALUES (:id, :oauth_provider, :oauth_uid, :username, :email,:gender, :profilephoto, now())");
				return $createMember->execute($data);
			} else {
				return false;
			}
		} catch (PDOException $e) {
			return false;
		}
	}

	public function getTopByNumber($number) {
		try {
			$query = $this->db->prepare("SELECT argument.*,topic.topic,count(argument.id) as commentCount,ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0)  AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0)  AS disagreed FROM argument,argumentcomment,argumentvotes,topic WHERE argument.id=argumentcomment.argumentId AND argument.id = argumentvotes.argumentId AND argument.topic = topic.id GROUP BY argument.id ORDER BY (agreed+disagreed+count(argument.id)) DESC LIMIT :number");
			$query->execute(array(':number' => $number));
			if ($query->rowCount() > 0) {
				return $query->fetchAll(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getAll() {
		try {
			$query = $this->db->prepare("SELECT * FROM usermember");
			$query->execute();
			return $query->fetchAll(PDO::FETCH_OBJ);
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function toggleArgumentFollow($argumentId, $memberId) {
		try {
			$query = $this->db->prepare("SELECT * FROM usermemberfollowedargument WHERE argumentId = :argumentId AND memberId = :memberId");
			$query->execute(array(':argumentId' => $argumentId, ':memberId' => $memberId));
			if ($query->fetch()) {
				$query = $this->db->prepare("DELETE FROM usermemberfollowedargument WHERE memberId=:memberId AND argumentId=:argumentId");
				if($query->execute(array(':argumentId' => $argumentId, ':memberId' => $memberId))){
					return 0;
				}else{
					return -1;
				}
			} else {
				$id = $this->generateUniqueId();
				$followArgument = $this->db->prepare("INSERT INTO usermemberfollowedargument (id,createdtime,memberId,argumentId) VALUES(:id,now(),:memberId,:argumentId)");
				if($followArgument->execute(array(':id' => $id, ':memberId' => $memberId, ':argumentId' => $argumentId))){
					return $id;
				}else{
					return -1;
				}
			}
		} catch (PDOException $e) {
			//return -1;
			$e->getMessage();
		}
	}

	public function argumentUnfollow($argumentId, $memberId) {
		try {
			$query = $this->db->prepare("DELETE FROM usermemberfollowedargument WHERE memberId=:memberId AND argumentId=:argumentId");
			return $query->execute(array(':argumentId' => $argumentId, ':memberId' => $memberId));
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}
	
	public function argumentLock($argumentId,$memberId){        //it will flip status 0 / 1 dynamically to prevent user overrides
		try {
		$query = $this->db->prepare("UPDATE argument SET status=NOT status, lastmodified=now() WHERE memberId=:memberId AND id=:argumentId");
		return $query->execute(array(':argumentId'=>$argumentId,':memberId'=>$memberId));
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}
	
	public function getFollowedAndCreatedArguments($memberId) {
		try {
			$query = $this->db->prepare("SELECT argument.*,ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.memberId = '" . $memberId . "' OR argument.id IN (SELECT argumentId FROM usermemberfollowedargument WHERE memberId = :memberId) ORDER BY createdTime DESC");
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

	public function getCreatedArgumentsbyUserMember($memberId) {
		try {
			//$query = $this->dbConnect->prepare("SELECT argument.*,(ifnull(argumentvotes.maleagreed, 0) + ifnull(argumentvotes.femaleagreed, 0) + ifnull(argumentvotes.generalagreed, 0)) AS agreed, (ifnull(argumentvotes.maledisagreed, 0) + ifnull(argumentvotes.femaledisagreed, 0) + ifnull(argumentvotes.generaldisagreed, 0)) AS disagreed FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.memberId = '" . $memberId . "'ORDER BY createdTime DESC" );
			$query = $this->db->prepare("SELECT argument.*, ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed,count(argumentcomment.commenttext) as commentsCount FROM argument left join argumentcomment on argumentcomment.argumentid = argument.id left join argumentvotes on argumentvotes.argumentid = argument.id WHERE argument.id in (select argument.id from argument where argument.memberId='" . $memberId . "') group by argument.id ORDER BY argument.createdTime DESC");
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

    public function getNewlyCreatedArgumentsbyUserMember($memberId){
        try {
            $query = $this->db->prepare("SELECT argument.*, ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed,count(argumentcomment.commenttext) as commentsCount FROM argument left join argumentcomment on argumentcomment.argumentid = argument.id left join argumentvotes on argumentvotes.argumentid = argument.id WHERE argument.id in (select argument.id from argument where argument.memberId= :memberId AND argument.createdtime BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW()) group by argument.id ORDER BY argument.createdTime DESC");
            $query->execute(array(':memberId' => $memberId, ':timeInterval' => AJAX_TIME_INTERVAL));
            if ($query->rowCount() > 0) {
                return $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

	public function getCountArgumentCreatedbyUserMember($memberId){
		try {
			$query = $this->db->prepare("SELECT count(argumentvotes.argumentId) AS argumentCreatedCount  FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.memberId = '" . $memberId . "'  ");
			$query->execute(array(':memberId'=>$memberId));
			if ($query->rowCount()>0){
				 return $query->fetchObject();
			}
			else {
				return false;
			}
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getFollowedArgumentsbyUserMember($memberId,$lowerLimit,$upperLimit){
		try {
			//$query = $this->dbConnect->prepare("SELECT argument.*,(ifnull(argumentvotes.maleagreed, 0) + ifnull(argumentvotes.femaleagreed, 0) + ifnull(argumentvotes.generalagreed, 0)) AS agreed, (ifnull(argumentvotes.maledisagreed, 0) + ifnull(argumentvotes.femaledisagreed, 0) + ifnull(argumentvotes.generaldisagreed, 0)) AS disagreed FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId   WHERE argument.id IN (SELECT argumentId FROM usermemberfollowedargument WHERE memberId = :memberId) AND memberId <> :memberId ORDER BY createdTime DESC");
			$query = $this->db->prepare("SELECT argument.*,ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed,count(argumentcomment.commenttext) as commentsCount FROM argument left join argumentcomment on argumentcomment.argumentid = argument.id LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId  WHERE argument.id IN (SELECT argumentId FROM usermemberfollowedargument WHERE memberId = :memberId AND argument.memberId != usermemberfollowedargument.memberId) AND argumentcomment.parentId IS NULL   group by argument.id ORDER BY createdTime DESC LIMIT $lowerLimit, $upperLimit");
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

    public function getNewlyFollowedArgumentsbyUserMember($memberId){
        try {
            $query = $this->db->prepare("SELECT argument.*,ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed,count(argumentcomment.commenttext) as commentsCount FROM argument left join argumentcomment on argumentcomment.argumentid = argument.id LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId  WHERE argument.id IN (SELECT argumentId FROM usermemberfollowedargument WHERE memberId = :memberId AND argument.memberId != usermemberfollowedargument.memberId AND usermemberfollowedargument.lastmodified BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW()) AND argumentcomment.parentId IS NULL group by argument.id ORDER BY createdTime DESC");
            $query->execute(array(':memberId'=>$memberId,":timeInterval"=>AJAX_TIME_INTERVAL));
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
			$query = $this->db->prepare("SELECT argument.*, ifnull(argumentvotes.maleagreed + argumentvotes.femaleagreed, 0) AS agreed, ifnull(argumentvotes.maledisagreed + argumentvotes.femaledisagreed, 0) AS disagreed, count(argumentcomment.commenttext) as commentsCount FROM argument left join argumentcomment on argumentcomment.argumentid = argument.id left join argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.id IN (SELECT argumentId from argumentcomment WHERE argumentcomment.memberId='" . $memberId . "') AND argument.memberId <> '" . $memberId . "' group by argument.id ORDER BY createdTime DESC");
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
	
	public function getParticipatedArgumentsCount($memberId){
	try {
			$query = $this->db->prepare("SELECT count(argument.id) AS participatedCount FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.id IN (SELECT argumentId from argumentcomment WHERE memberId='" . $memberId . "') AND memberId <> '" . $memberId . "' ");
			$query->execute(array(':memberId'=>$memberId));
			if ($query->rowCount()>0){
				 return $query->fetchObject();
			}
			else {
				return false;
			}
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getMembersFollowingMe($memberId, $lowerLimit, $upperLimit){
		try {
			$query = $this->db->prepare("SELECT usermember.id, usermember.oauth_provider, usermember.oauth_uid, usermember.email, usermember.status, usermember.username, usermember.createdTime, usermember.lastModified, usermember.lastloggedin, usermember.lastloggedout, usermember.online,usermemberprofile.memberId, usermemberprofile.createdTime, usermemberprofile.lastModified, usermemberprofile.gender, usermemberprofile.profilephoto, usermemberprofile.interest, usermemberprofile.birthdate, usermemberprofile.fullname, usermemberprofile.location FROM usermember,usermemberprofile WHERE usermember.id IN(SELECT memberId FROM usermemberfollowedmember WHERE followedmemberId=:memberId) AND usermember.id = usermemberprofile.memberId LIMIT $lowerLimit,$upperLimit");
			$query->execute(array(':memberId'=> $memberId));
			if ($query->rowCount()>0){
				return $query->fetchAll(PDO::FETCH_OBJ);
			}
			else {return false;
			}
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getMembersFollowedByMe($memberId, $lowerLimit, $upperLimit){
		try {
			$query = $this->db->prepare("SELECT usermember.id, usermember.oauth_provider, usermember.oauth_uid, usermember.email, usermember.status, usermember.username, usermember.createdTime, usermember.lastModified, usermember.lastloggedin, usermember.lastloggedout, usermember.online,usermemberprofile.memberId, usermemberprofile.createdTime, usermemberprofile.lastModified, usermemberprofile.gender, usermemberprofile.profilephoto, usermemberprofile.interest, usermemberprofile.birthdate, usermemberprofile.fullname, usermemberprofile.location FROM usermember,usermemberprofile WHERE usermember.id IN(SELECT followedmemberId FROM usermemberfollowedmember WHERE memberId=:memberId) AND usermember.id = usermemberprofile.memberId LIMIT  $lowerLimit,$upperLimit");
			$query->execute(array(':memberId'=>$memberId));
			if ($query->rowCount()>0){
				return $query->fetchAll(PDO::FETCH_OBJ);
			}
			else {return false;
			}
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getUserCount() {
		try {
			$query = $this->db->prepare("select count(*) as noofusers from usermember");
			$query->execute();
			return $query->fetchObject();
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getUserList() {
		$result = mysql_query("select id, oauth_provider, email, username, createdtime, lastmodified from usermember");
		if ($result) {
			$fieldcount = mysql_num_fields($result);
			$fieldnames = array();
			$userArrayObject = array();
			for ($i = 0; i < $fieldcount; $i++, $fieldcount--) {
				array_push($fieldnames, mysql_field_name($result, $i));
			}
			array_push($userArrayObject, $fieldnames);

			while ($row = mysql_fetch_object($result)) {
				array_push($userArrayObject, $row);
			}
			return $userArrayObject;
		} else {
			return false;
		}
	}

	public function __destruct() {}
	
	public function getProfileStatistics($memberId){
		try {
			//$query = $this->db->prepare("SELECT (SELECT COUNT(*) FROM usermemberfollowedmember WHERE memberId =:memberId) as followedCount,(SELECT COUNT(*) FROM usermemberfollowedmember WHERE followedmemberId =:memberId) as followerCount,(SELECT COUNT(*) FROM argument WHERE memberId=:memberId) as argumentCreatedCount, (SELECT COUNT(*) FROM usermemberfollowedargument WHERE memberId=:memberId) as argumentFollowCount, (SELECT count(*) FROM usermemberfollowedtopic WHERE memberId=:memberId) as topicFollowCount,(SELECT count(argument.id) FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.id IN (SELECT argumentId from argumentcomment WHERE memberId=:memberId) AND memberId <> :memberId ORDER BY argument.createdTime DESC) as participatedCount ");
			$query = $this->db->prepare("SELECT (SELECT COUNT(*) FROM usermemberfollowedmember WHERE memberId =:memberId) as followedCount,(SELECT COUNT(*) FROM usermemberfollowedmember WHERE followedmemberId =:memberId) as followerCount,(SELECT COUNT(*) FROM argument WHERE memberId=:memberId) as argumentCreatedCount, (SELECT COUNT(*) FROM usermemberfollowedargument WHERE memberId=:memberId AND argumentId NOT IN (SELECT id FROM argument WHERE memberId =:memberId)) as argumentFollowCount, (SELECT count(*) FROM usermemberfollowedtopic WHERE memberId=:memberId) as topicFollowCount,(SELECT count(argument.id) FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.id IN (SELECT argumentId from argumentcomment WHERE memberId=:memberId) AND memberId <>:memberId ORDER BY argument.createdTime DESC) as participatedCount, (SELECT count(*) FROM notification_queue WHERE memberId=:memberId) as notificationCount");
			$query->execute(array(':memberId' => $memberId));
			if($data = $query->fetchObject()){
				return $data;
			}else{
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function getProfileStatisticsByAjax($memberId){
		try {
		//	$query = $this->db->prepare("SELECT (SELECT COUNT(*) FROM usermemberfollowedmember WHERE memberId =:memberId) as followedCount,(SELECT COUNT(*) FROM usermemberfollowedmember WHERE followedmemberId =:memberId) as followerCount,(SELECT COUNT(*) FROM argument WHERE memberId=:memberId) as argumentCreatedCount, (SELECT COUNT(*) FROM usermemberfollowedargument WHERE memberId=:memberId AND argumentId NOT IN (SELECT id FROM argument WHERE memberId = :memberId)) as argumentFollowCount, (SELECT count(*) FROM usermemberfollowedtopic WHERE memberId=:memberId) as topicFollowCount,(SELECT count(argument.id) FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.id IN (SELECT argumentId from argumentcomment WHERE memberId=:memberId) AND memberId <> :memberId ORDER BY argument.createdTime DESC) as participatedCount ");
			$query = $this->db->prepare("SELECT (SELECT COUNT(*) FROM usermemberfollowedmember WHERE memberId =:memberId) as followedCount,(SELECT COUNT(*) FROM usermemberfollowedmember WHERE followedmemberId =:memberId) as followerCount,(SELECT COUNT(*) FROM argument WHERE memberId=:memberId) as argumentCreatedCount, (SELECT COUNT(*) FROM usermemberfollowedargument WHERE memberId=:memberId AND argumentId NOT IN (SELECT id FROM argument WHERE memberId =:memberId)) as argumentFollowCount, (SELECT count(*) FROM usermemberfollowedtopic WHERE memberId=:memberId) as topicFollowCount,(SELECT count(argument.id) FROM argument LEFT JOIN argumentvotes ON argument.id = argumentvotes.argumentId WHERE argument.id IN (SELECT argumentId from argumentcomment WHERE memberId=:memberId) AND memberId <>:memberId ORDER BY argument.createdTime DESC) as participatedCount");
			$query->execute(array(':memberId' => $memberId));
			if($data = $query->fetchObject()){
				return $data;
			}else{
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function getUsersNotFollowingFromIdList($list, $memberId){
		try {
			$query = $this->db->prepare("SELECT * FROM usermember WHERE oauth_uid IN ($list) AND oauth_uid NOT IN (SELECT usermember.oauth_uid FROM usermember,usermemberfollowedmember follow WHERE follow.memberId = '$memberId' AND follow.memberId = usermember.id)");
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
	
	public function getTopUsersByTopicByAjax($topic,$memberId){
		try {
			$query = $this->db->prepare("SELECT dataSet.*, sum(argumentCount) topUser FROM (SELECT usermember.id, usermember.oauth_provider, usermember.oauth_uid, usermember.email, usermember.status, usermember.username, usermember.createdTime, usermember.lastModified, usermember.lastloggedin, usermember.lastloggedout, usermember.online,count(argument.memberId) argumentCount FROM usermember,argument WHERE usermember.id = argument.memberId AND FIND_IN_SET(:topic,usermember.interest) AND usermember.id != :memberId GROUP BY usermember.id
												UNION
												SELECT usermember.id, usermember.oauth_provider, usermember.oauth_uid, usermember.email, usermember.status, usermember.username, usermember.createdTime, usermember.lastModified, usermember.lastloggedin, usermember.lastloggedout, usermember.online,count(vote.memberId) argumentCount FROM usermember,usermembervotes vote WHERE usermember.id = vote.memberId AND FIND_IN_SET(:topic,usermember.interest) AND usermember.id != :memberId GROUP BY usermember.id) dataSet WHERE dataSet.id NOT IN(SELECT follow.followedmemberId FROM usermemberfollowedmember follow WHERE follow.memberId = :memberId) GROUP BY dataSet.id ORDER BY topUser DESC LIMIT 0,10");
			$query->execute(array(":topic"=>$topic,':memberId'=>$memberId));
			if($data = $query->fetchAll(PDO::FETCH_OBJ)){
				return $data;
			}else {
				return false;		
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function setUserOnline($memberId){
		try {
			$query = $this->db->prepare("UPDATE usermember SET online=1, lastloggedin=now() WHERE id=:memberId");
			if($query->execute(array(":memberId" => $memberId))){
				return true;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function setUserOffline($memberId){
		try {
			$query = $this->db->prepare("UPDATE usermember SET online=0, lastloggedout=now() WHERE id=:memberId AND online=1");
			if($query->execute(array(":memberId" => $memberId))){
				return true;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function getUserAccessUpdatedTime($memberId){
		try {
			$query = $this->db->prepare("SELECT lastloggedin,lastloggedout FROM usermember WHERE id=:memberId");
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
	
    public function syncUserArgumentsData($loggedInmemberId,$profileMemberId,$limit){
        try{
            $query = $this->db->prepare("select argument.id as argumentid, ifnull(count(argumentcomment.id),0) as commentcount,(maleagreed+femaleagreed) as agreed, (maledisagreed+femaledisagreed) as disagreed from argument left join argumentcomment on argument.id = argumentcomment.argumentid  left join argumentvotes on argument.id = argumentvotes.argumentid where argument.memberid = :pMemberId AND argumentcomment.memberId != :lMemberId AND argumentcomment.parentid IS NULL and argumentcomment.lastmodified BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW()  group by argument.id order by argument.createdtime ASC LIMIT 0,$limit");
            $query->execute(array(':lMemberId'=>$loggedInmemberId,":pMemberId"=>$profileMemberId,":timeInterval"=>AJAX_TIME_INTERVAL));
            if($query->rowCount()>0){
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
            else{
                return false;
            }
        }catch(PDOException $e){
            return $e->getMessage();
        }
    }
    
    public function getUsersByKeyword($keyword){
    	try {
    		$query = $this->db->prepare("SELECT user.*,profile.* FROM usermember, usermemberprofile WHERE user.id = usermemberprofile.memberId AND username LIKE :keyword");
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

    /**
     * retrives recent activity of given user
     * applies limit $limit and $count
     * order by createdtime desc
     *--------------------------------------------------------------------------------------------------------------------------
     * Object Structure
     * recordId
     * action (1-argument started, 2,3-vote(Agree/ Disagree), 4- commented on argument, 5- replied on an argument, 6-Follow User, 7- Follow Argument)
     * ExtraField1-Extrafield-5
     * createdTime
     * --------------------------------------------------------------------------------------------------------------------------
     * status = 1 => Started Argument: returns argumentid as recordid, argumentTitle as extraField1, createdtime
     * status = 2,3 => Vote : returns argumentid as recordid, argumentTitle,commentid,commenttext,uservote as extraFields, createdtime
     * status = 4 => Comment: returns argumentid as recordid, argumentTitle,commentid,commenttext,uservote as extraFields, createdtime
     * status = 5 => Reply: returns argumentid as recordid, argumentTitle,commentid,commenttext,replyId,replyText as extraFields, createdtime
     * status = 6 => Follow User: returns followedmemberid as recordid, followedmemberusername as extraFields, createdtime
     * status = 7 => Follow Argument: returns argumentid as recordid, argumentTitle as extraFields, createdtime
     * --------------------------------------------------------------------------------------------------------------------------
     *
     * @param $memberId
     * @param $start
     * @param $count
     * @return bool|Array[Object]
     */
    public function getUserActivityByMemberId($memberId, $start, $count)
    {
        try {
            $query = $this->db->prepare('CALL proc_user_activity(:memberId,:start,:count);');
            $query->bindValue(":memberId", $memberId, PDO::PARAM_STR);
            $query->bindValue(":start", $start, PDO::PARAM_INT);
            $query->bindValue(":count", $count, PDO::PARAM_INT);
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
}