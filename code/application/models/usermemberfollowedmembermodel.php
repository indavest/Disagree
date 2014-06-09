<?php
class UserMemberFollowedMemberModel extends DA_Model {

    private $id;
    private $createdtime;
    private $lastmodified;
    private $memberId;
    private $followedmemberId;
    
    public function __construct(){}

    public function __destruct()
    {
        $this->dbConnect = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
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
        $this->memberId = $memberId;
    }

    public function getFollowedmemberId()
    {
        return $this->followedmemberId;
    }

    public function setFollowedmemberId($followedmemberId)
    {
        $this->followedmemberId = $followedmemberId;
    }

    public function followUserMember($memberId, $followedMemberId)
    {
 
        try {
            $id = $this->generateUniqueId();
            //$notificationData = array("id" => $this->dbConnectObj->generateUniqueId(), "type" => "followMember", "memberId" => $memberId, "argumentId" => null, "msg" => null);
            $query = $this->db->prepare("INSERT INTO usermemberfollowedmember (id, createdtime, memberId, followedmemberId) VALUES(:id,now(),:memberId,:followedMemberId)");
            if ($query->execute(array(':id' => $id, ':memberId' => $memberId, ':followedMemberId' => $followedMemberId))) {
            	return $id;
                //$baseActivity->addNotification($notificationData);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function unfolloweUserMember($memberId, $followedMemberId)
    {
        try {
            $query = $this->db->prepare("DELETE FROM usermemberfollowedmember WHERE memberId = :memberId AND followedmemberId = :followedMemberId");
            return $query->execute(array(':memberId' => $memberId, ':followedMemberId' => $followedMemberId));
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getFollowedByMemberId($memberId)
    {
        try {
            $query = $this->db->prepare("SELECT * FROM usermember WHERE id IN (SELECT followedmemberId FROM usermemberfollowedmember WHERE memberId = :memberId)");
            $query->execute(array(':memberId' => $memberId));
            if ($query->rowCount() > 0) {
                return $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getFollowersByMemberId($memberId)
    {
        try {
            $query = $this->db->prepare("SELECT * FROM usermember WHERE id IN (SELECT memberId FROM usermemberfollowedmember WHERE followedmemberId = :memberId)");
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

    public function getFollowedActivity()
    {
        return true;
    }

    public function getToBeFollowedMembers($memberId)
    {
        try {
            $query = $this->db->prepare("SELECT * FROM usermember WHERE id NOT IN (SELECT followedmemberId FROM usermemberfollowedmember WHERE memberId=:memberId) AND id != :memberId");
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

    public function getFollowedAndFollowersByMemberId($memberId){
        try{
            $query = $this->db->prepare("SELECT memberId AS memberId FROM usermemberfollowedmember where followedmemberId=:memberid UNION SELECT followedmemberId AS memberId FROM usermemberfollowedmember where memberId=:memberid");
            $query->execute(array(':memberid'=>$memberId));
            if($query->rowCount() >0){
                return $query->fetchAll(PDO::FETCH_OBJ);
            }else{
                return false;
            }
        }catch (PDOException $e){
            return $e->getMessage();
        }
    }
 
    public function checkFollowByMemberId($memberId, $followedMemberId){
     	try {
            $query = $this->db->prepare("SELECT id FROM usermemberfollowedmember WHERE memberId=:memberId AND followedmemberId=:followedmemberId");
            $query->execute(array(":followedmemberId" => $followedMemberId, ":memberId" => $memberId));
            if ($query->fetch()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
	
    public function getFollowingOfFollowingByAjax($memberId){
    	try {
    		$query = $this->db->prepare("SELECT * FROM usermember WHERE id IN (SELECT followedmemberId FROM usermemberfollowedmember WHERE memberId IN(SELECT followedmemberId FROM usermemberfollowedmember WHERE memberId = :memberId)) AND id != :memberId");
    		$query->execute(array(":memberId" => $memberId));
    		if ($data = $query->fetchAll(PDO::FETCH_OBJ)){
    			return $data;
    		} else {
    			return false;
    		}
    	} catch (Exception $e) {
    	}
    }
    
    public function getById($id){
    	try {
    		$query = $this->db->prepare("SELECT * FROM usermemberfollowedmember WHERE id=:id");
    		$query->execute(array(":id" => $id));
    		if($data = $query->fetchObject()){
    			return $data;
    		}else {
    			return false;
    		}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
}