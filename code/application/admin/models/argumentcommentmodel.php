<?php
class ArgumentCommentModel extends CI_Model {

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
			$query = $this->db->prepare("SELECT * FROM argumentcomment WHERE id=:id");
			$query->execute(array(":id"=>$id));
			return $query->fetchObject();
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	
	public function delete($id){
		try {
			$query = $this->db->prepare("DELETE FROM argumentcomment WHERE id=:id");
			if($query->execute(array(":id" => $id))){
				return true;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
    public function getAjaxCommentsbyArgumentId($argumentId,$lowerLimit,$upperLimit){
        try {
            $query = $this->db->prepare("select argumentcomment.*,argument.source as source, usermember.username, usermemberprofile.profilephoto as userImage,usermemberprofile.fullname from argument join argumentcomment on argumentcomment.argumentid = argument.id join usermember on argumentcomment.memberid = usermember.id join usermemberprofile on usermember.id = usermemberprofile.memberid where argumentcomment.argumentid=:argumentId AND parentId is NULL AND uservote in(0,1,-2,-3) ORDER BY argumentcomment.createdtime DESC LIMIT :lowerLimit , :upperLimit");
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
            $query->bindParam(8 , $commentIds[8] , PDO::PARAM_INT);
            $query->bindParam(9 , $commentIds[9], PDO::PARAM_INT);
            $query->bindParam(10 ,$commentIds[10] ,PDO::PARAM_INT);


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
    
}