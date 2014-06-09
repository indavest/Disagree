<?php
class ArgumentCommentModel extends DA_Model {

	private $id;
	private $commenttext;	
	private $createdtime;
	private $lastmodified;
	private $memberId;
	private $argumentId;
	private $parentId;
    private $uservote;
    private $username;
    private $userImage;
    
    public function __construct() {}
	
	public function __destruct() {
		$this->dbConnect = null;
	}
	
    public function getId() {
    	return $this->id;
    }

    public function setId($id) {
    	$this->id = $id;
    }

    public function getCommenttext() {
    	return $this->commenttext;
    }

    public function setCommenttext($commenttext) {
    	$this->commenttext = $commenttext;
    }

    public function getCreatedtime() {
    	return $this->createdtime;
    }

    public function setCreatedtime($createdtime) {
    	$this->createdtime = $createdtime;
    }

    public function getLastmodified() {
    	return $this->lastmodified;
    }

    public function setLastmodified($lastmodified) {
    	$this->lastmodified = $lastmodified;
    }

    public function getMemberId() {
    	return $this->memberId;
    }

    public function setMemberId($memberId) {
    	$this->memberId = $memberId;
    }

    public function getArgumentId() {
    	return $this->argumentId;
    }

    public function setArgumentId($argumentId) {
    	$this->argumentId = $argumentId;
    }

    public function getParentId() {
    	return $this->parentId;
    }

    public function setParentId($parentId) {
    	$this->parentId = $parentId;
    }

    public function setUserVote($userVote)
    {
    	$this->uservote = $userVote;
    }

    public function getUserVote()
    {
    	return $this->uservote;
    }

    public function setUserImage($userImage)
    {
        $this->userImage = $userImage;
    }

    public function getUserImage()
    {
        return $this->userImage;
    }

    public function setUserName($username)
    {
        $this->username = $username;
    }

    public function getUserName()
    {
        return $this->username;
    }
    
	public function getById($id) {
		try {
			$query = $this->db->prepare("SELECT argumentcomment.*,usermember.username, usermemberprofile.profilephoto AS userImage,usermemberprofile.fullname FROM argumentcomment JOIN usermember ON usermember.id = argumentcomment.memberId JOIN usermemberprofile ON usermemberprofile.memberid = argumentcomment.memberid WHERE argumentcomment.id= :commentId");
			$query->execute(array(":commentId"=>$id));
			return $query->fetchObject();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

    public function create($data) {
        //$baseActivity = new base();
        try {
            $data['id'] = $this->generateUniqueId();
            //$notificationData = array("id" => $data['id'], "type" => "comment", "memberId" => $data['memberId'], "argumentId" => $data['argumentId'], "msg" => $data['commenttext']);
            $query = $this->db->prepare("INSERT INTO argumentcomment (id,commenttext,createdtime,memberId,argumentId,parentId,uservote) VALUES(:id,:commenttext,now(),:memberId,:argumentId,:parentId,:uservote)");
            if ($query->execute($data)){
                /*$query = $this->db->prepare("SELECT * FROM argumentcomment WHERE id = :id");
                    $query->execute(array(":id" => $data['id']));*/
                //$baseActivity->addNotification($notificationData);
                return $data;
            }else{
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function update($data) {
        try {
            $query = $this->db->prepare("UPDATE `argumentcomment` SET commenttext=:commenttext,lastmodified=now() WHERE id=:id and memberId=:memberId");
            if ($query->execute($data)){
                return $data;
            }else{
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }


    public function getAllCommentsbyArgumentId($argumentId){
        try {
            $query = $this->db->prepare("select argumentcomment.*, argument.source as source, usermember.username, usermemberprofile.profilephoto as userImage,usermemberprofile.fullname from argument join argumentcomment on argumentcomment.argumentId = argument.id join usermember on argument.memberid = usermember.id JOIN usermemberprofile ON argument.memberid = usermemberprofile.memberid where argument.id= :argumentId AND argumentcomment.parentId is NULL ORDER BY argumentcomment.createdtime DESC");
            $query->execute(array(":argumentId" => $argumentId));
            if ($query->rowCount() > 0) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        } catch (PDoException $e) {
            return $e->getMessage();
        }
    }

    public function getAjaxCommentsbyArgumentId($argumentId,$lowerLimit,$upperLimit){
        try {
            $query = $this->db->prepare("select argumentcomment.*,argument.source as source, usermember.username, usermemberprofile.profilephoto as userImage,usermemberprofile.fullname from argument join argumentcomment on argumentcomment.argumentid = argument.id join usermember on argumentcomment.memberid = usermember.id join usermemberprofile on usermember.id = usermemberprofile.memberid where argumentcomment.argumentid=:argumentId AND parentId is NULL AND uservote in(0,1,-2,-3) ORDER BY argumentcomment.createdtime ASC LIMIT :lowerLimit , :upperLimit");
            $query->bindParam(":argumentId" ,$argumentId ,PDO::PARAM_STR);
            $query->bindParam(":lowerLimit" , $lowerLimit , PDO::PARAM_INT);
            $query->bindParam(":upperLimit" , $upperLimit , PDO::PARAM_INT);
            $query->execute();
            if ($query->rowCount() > 0) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        } catch (PDoException $e) {
            return $e->getMessage();
        }
    }

    public function getReplyCountByCommentIDs($commentIds){
        try {
            $query = $this->db->prepare("select parentId,count(*) from argumentcomment where parentId IN (?,?,?,?,?,?,?,?,?,?) group by parentId");
            $query->bindParam(1 ,$commentIds[0] ,PDO::PARAM_INT);
            $query->bindParam(2 , $commentIds[1] , PDO::PARAM_INT);
            $query->bindParam(3 , $commentIds[2], PDO::PARAM_INT);
            $query->bindParam(4 ,$commentIds[3] ,PDO::PARAM_INT);
            $query->bindParam(5 , $commentIds[4] , PDO::PARAM_INT);
            $query->bindParam(6 , $commentIds[5], PDO::PARAM_INT);
            $query->bindParam(7 ,$commentIds[6] ,PDO::PARAM_INT);
            $query->bindParam(8 , $commentIds[7] , PDO::PARAM_INT);
            $query->bindParam(9 , $commentIds[8], PDO::PARAM_INT);
            $query->bindParam(10 ,$commentIds[9] ,PDO::PARAM_INT);


            $query->execute();
            if ($query->rowCount() > 0) {
                return $query->fetchAll(PDO::FETCH_KEY_PAIR);
            } else {
                return false;
            }
        } catch (PDoException $e) {
            return $e->getMessage();
        }
    }

    public function  getUpdateReplyCountByCommentIDs($commentIds){

       /* $values = count($commentIds);
        $criteria = sprintf("?%s", str_repeat(",?", ($values ? $values-1 : 0)));
        //Returns ?,?,?
        $sql = sprintf("select parentId,count(*) from argumentcomment where parentId IN (%s) group by parentId", $criteria);
        //Returns DELETE FROM table where column NOT IN(?,?,?)
        $query= $this->db->prepare($sql);
        $query->execute($commentIds);*/


    }

    public function getNewlyCreatedComments($argumentId, $memberId){
        try{
            $query = $this->db->prepare('select argumentcomment.*,argument.source as source, usermember.username, usermemberprofile.profilephoto as userImage,usermemberprofile.fullname from argument join argumentcomment on argumentcomment.argumentid = argument.id join usermember on argumentcomment.memberid = usermember.id join usermemberprofile on usermember.id = usermemberprofile.memberid where argumentcomment.argumentid=:argumentId AND parentId is NULL AND argumentcomment.memberId != :memberId AND uservote in(0,1,-2,-3) and argumentcomment.lastmodified BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW() ORDER BY argumentcomment.createdtime DESC');
            $query->execute(array(':argumentId'=>$argumentId,':memberId'=>$memberId,":timeInterval"=>AJAX_TIME_INTERVAL));
            if($data = $query->fetchAll(PDO::FETCH_ASSOC)){
                return $data;
            }else{
                return false;
            }
        }catch(PDOException $e){
            return $e->getMessage();
        }
    }

    public function getNewlyRepliedCount($argumentid, $memberId){
        try{
            $query = $this->db->prepare('select parentId,count(id) from argumentcomment WHERE argumentId =:argumentId AND memberId != :memberId AND parentid IS NOT NULL AND argumentcomment.lastmodified BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW() group by parentid ORDER BY argumentcomment.createdtime DESC');
            $query->execute(array(':argumentId'=>$argumentid,':memberId'=>$memberId,':timeInterval'=>AJAX_TIME_INTERVAL));
            if($data = $query->fetchAll(PDO::FETCH_KEY_PAIR)){
                return $data;
            }else{
                return false;
            }
        }catch(PDOException $e){
            return $e->getMessage();
        }
    }



	public function getByArgumentId($argumentId) {
		try {
			$query = $this->db->prepare("select argumentcomment.*,usermember.username, usermemberprofile.profilephoto as userImage, usermemberprofile.fullname from argument join argumentcomment on argumentcomment.argumentId = argument.id join usermember on argument.memberid = usermember.id JOIN usermemberprofile ON argument.memberid = usermemberprofile.memberid where argument.id= :argumentId AND argumentcomment.parentId is NULL ORDER BY argumentcomment.createdtime DESC");
			$query->execute(array(":argumentId" => $argumentId));
			if ($query->rowCount()>0) {

				return $query->fetchAll(PDO::FETCH_OBJ);
                //return $query->rowCount();
                //return true;
			} else {
                return null;
				//return false;
			}
		} catch (PDoException $e) {
			return $e->getMessage();
		}
	}
	
	public function getAgreedByArgumentId($argumentId) {
		try {
			$query = $this->db->prepare("SELECT * FROM argumentcomment WHERE uservote=1 and argumentId=:argumentId AND parentId is NULL ORDER BY createdtime DESC");
			$query->execute(array(":argumentId" => $argumentId));
			if ($data = $query->fetchAll(PDO::FETCH_OBJ)) {
				return $data;
			} else {
				return false;
			}
		} catch (PDoException $e) {
			return $e->getMessage();
		}
	}

	public function getDisagreedByArgumentId($argumentId) {
		try {
			$query = $this->db->prepare("SELECT * FROM argumentcomment WHERE uservote=0 and argumentId=:argumentId AND parentId is NULL ORDER BY createdtime DESC");
			$query->execute(array(":argumentId" => $argumentId));
			if ($query->rowCount() > 0) {
				return $query->fetchAll(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		} catch (PDoException $e) {
			return $e->getMessage();
		}
	}
	public function getInitiatorCommentByArgumentId($argumentId,$loggedInMemberId){
		try {
			$query = $this->db->prepare("SELECT * FROM argumentcomment WHERE memberId=:loggedInMemberId and argumentId=:argumentId AND parentId is NULL ORDER BY createdtime DESC");
			$query->execute(array(":argumentId" => $argumentId,"loggedInMemberId" => $loggedInMemberId));
			if ($query->rowCount() > 0) {
				return $query->fetchAll(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		} catch (PDoException $e) {
			return $e->getMessage();
		}
	}

	public function getAjaxAgreedByArgumentId($argumentId) {
		try {
			$query = $this->db->prepare("SELECT comment.*, userprofile.profilephoto, userprofile.fullname, user.username FROM argumentcomment comment join usermember user on comment.memberId = user.id JOIN usermemberprofile userprofile ON comment.memberid = userprofile.memberid WHERE comment.uservote=1 and comment.argumentId=:argumentId AND comment.parentId is NULL ORDER BY comment.createdtime DESC");
			$query->execute(array(":argumentId" => $argumentId));
			if ($query->fetch()) {
				return $query->fetchAll(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		} catch (PDoException $e) {
			return $e->getMessage();
		}
	}
	
	public function getAjaxDisagreedByArgumentId($argumentId) {
		try {
			$query = $this->db->prepare("SELECT comment.*, userprofile.profilephoto, userprofile.fullname, user.username FROM argumentcomment comment join usermember user on comment.memberId = user.id JOIN usermemberprofile userprofile ON comment.memberid = userprofile.memberid WHERE comment.uservote=0 and comment.argumentId=:argumentId AND comment.parentId is NULL ORDER BY comment.createdtime DESC");
			$query->execute(array(":argumentId" => $argumentId));
			if ($query->rowCount() > 0) {
				return $query->fetchAll(PDO::FETCH_OBJ);
			} else {
				return false;
			}
		} catch (PDoException $e) {
			return $e->getMessage();
		}
	}
	
	public function getTotalCommentByArgumentId($argumentId){
		try {
			$query = $this->db->prepare("SELECT comment.*, usermemberprofile.profilephoto, usermemberprofile.fullname,usermember.username FROM argumentcomment comment JOIN usermember on comment.memberId = usermember.id JOIN usermemberprofile ON comment.memberId = usermemberprofile.memberid WHERE comment.argumentId=:argumentId AND comment.parentId is NULL ORDER BY comment.createdtime DESC");
			$query->execute(array(":argumentId" => $argumentId));
			if($result = $query->fetchAll(PDO::FETCH_OBJ)){
				return $result;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function delete($commentId) {
		try {
			$query = $this->db->prepare("DELETE FROM argumentcomment WHERE id=:commentId");
			$res = $query->execute(array(":commentId" => $commentId));
		} catch (PDOException $e) {
			return $e->getMessage();
		}
		return $res;
	}

	public function getTotalCountByArgumentId($argumentId) {
		try {
			$query = $this->db->prepare("SELECT COUNT(id) AS totalComments FROM argumentcomment WHERE argumentId=:argumentId");
			$query->execute(array(":argumentId" => $argumentId));
			return $query->fetchObject(get_class($this));
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function getRecentByArgumentId($argumentId) {
		try {
			$query = $this->db->prepare("SELECT * FROM argumentcomment WHERE argumentId=:argumentId ORDER BY createdtime DESC LIMIT 2");
			$query->execute(array(":argumentId" => $argumentId));
			if ($query->rowCount() > 0) {
				return $query->fetchAll(PDO::FETCH_CLASS, get_class($this));
			} else {
				return false;
			}
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}
	public function getReplysByCommetnId($commentId){
		try{
			$query = $this->db->prepare("select argumentcomment.*,argument.source as source, usermember.username, usermemberprofile.profilephoto as userImage, usermemberprofile.fullname from argument join argumentcomment on argumentcomment.argumentid = argument.id join usermember on argumentcomment.memberid = usermember.id join usermemberprofile on usermember.id = usermemberprofile.memberid where parentId =:commentId AND argumentcomment.uservote='-1' ORDER BY argumentcomment.createdtime ASC");
			$query->execute(array(":commentId"=>$commentId));
			if($data = $query->fetchAll(PDO::FETCH_OBJ)){
				return $data;
			}else{
				return false;
			}
		}catch(PDOException $e){
			$e->getMessage();
		}
	}
	
	public function checkUserVoted(){
		try{
			$query = $this->db->prepare("SELECT COUNT(`id`) AS votes FROM `argumentcomment` WHERE `argumentId`=:argumentId AND `memberId`=:memberId");
			$query->execute(array(":memberId"=>$this->getMemberId(),":argumentId"=>$this->getArgumentId()));
			$data = $query->fetch(PDO::FETCH_OBJ);
			if($data->votes > 0){
				return true;					
			}else{
				return false;
			}
		}catch (PDOException $e){
			$e->getMessage();
		}
	}
	
	public function getNewlyCommentedByArgumentId($argumentId,$memberId,$timeInterval){
	 try {
            $query = $this->db->prepare("select argumentcomment.*,usermember.username as userMember,usermemberprofile.profilephoto, usermemberprofile.fullname from argumentcomment JOIN usermember ON argumentcomment.memberId=usermember.id JOIN usermemberprofile ON argumentcomment.memberid=usermemberprofile.memberid where argumentId=:argumentId and argumentcomment.memberId!=:memberId and argumentcomment.lastmodified BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW()");
            $query->execute(array(':timeInterval' => $timeInterval,":argumentId"=>$argumentId,":memberId"=>$memberId));
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
	}

    public function getNewlyRepliedByArgumentId($argumentId,$memberId,$timeInterval){
        try {
            $query = $this->db->prepare("select argumentcomment.*,usermember.username as username,usermember.profilephoto as profilephoto,usermemberprofile.fullname from argumentcomment,usermember where argumentcomment.memberId=usermember.id and argumentcomment.argumentId=:argumentId and argumentcomment.memberId!=:memberId and argumentcomment.lastmodified BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW()");
            $query->execute(array(':timeInterval' => $timeInterval,":argumentId"=>$argumentId,":memberId"=>$memberId));
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

	public function getByArgumentAndMember($argumentId,$memberId){
		try {
			$query = $this->db->prepare("SELECT * FROM argumentcomment WHERE argumentId=:argumentId AND memberId=:memberId ORDER BY createdtime DESC LIMIT 1");
			$query->execute(array(":argumentId" => $argumentId, ":memberId" => $memberId));
			if($data = $query->fetchObject()){
				return $data;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

    /**
     * getArgumentAndCommentByReplyId
     *
     * Fetch argument data and comment data of a reply
     * written intended for notification message preparation.
     *
     * @param  string $replyId  replyid for which argument and commetn data to be fetched
     * @return object           all argument and comment data as a single object
     */
    public function getArgumentAndCommentByReplyId($replyId){
        try{
            $query = $this->db->prepare("SELECT argument.id AS argumentId, argument.title AS argumentTitle, argument.argument AS argumentDesc, argument.createdtime AS argumentCreatedTime, argument.lastmodified AS argumentLastModified, argument.memberId AS argumentMemberId, argument.status as argumentStatus, argument.topic as argumentTopic, argument.source as argumentSource,comment.id AS commentId, comment.commenttext AS comment, comment.createdtime AS commentCreatedTime, comment.lastmodified AS commentLastmodified, comment.memberId AS commentMemberId, comment.argumentId AS commentArgumentId, comment.parentId AS commentParentId, comment.uservote AS commetUserVote
                                            FROM argument
                                                JOIN argumentcomment ON argument.id = argumentcomment.argumentid
                                                JOIN argumentcomment AS comment ON argumentcomment.parentid = comment.id
                                            where argumentcomment.id=:replyId"
            );
            $query->execute(array(":replyId"=>$replyId));
            if($data = $query->fetchObject()){
                return $data;
            }else{
                $error = $query->errorInfo();
                log_message('error',$query->errorInfo(),true);
                return false;
            }
        }catch(PDOException $e){
            return $e->getMessage();
        }
    }
}