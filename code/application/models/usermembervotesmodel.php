<?php
class UserMemberVotesModel extends DA_Model {

    private $id;
    private $vote;
    private $argumentId;
    private $memberId;
    private $createdtime;
    private $lastmodified;    
    
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

    public function getVote()
    {
        return $this->vote;
    }

    public function setVote($vote)
    {
        $this->vote = $vote;
    }

    public function getArgumentId()
    {
        return $this->argumentId;
    }

    public function setArgumentId($argumentId)
    {
        $this->argumentId = $argumentId;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
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

    public function add($data,$commentId=null)
    {
        try {
        	$data['id'] = $this->generateUniqueId();
            $data['commentId'] = $commentId;
            $query = $this->db->prepare("INSERT INTO usermembervotes (id,vote,argumentId,memberId,commentId,createdtime) VALUES(:id,:vote,:argumentId,:memberId,:commentId,now())");
            if($query->execute($data)){
                return $data['id'];
            }else{
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function checkVotedByArgument($data)
    {
        try {
            $query = $this->db->prepare("SELECT * FROM usermembervotes WHERE argumentId=:argumentId AND memberId=:memberId");
            $query->execute(array(":argumentId" => $data['argumentId'], ":memberId" => $data['memberId']));
            if ($query->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    
    public function getById($id){
    	try {
    		$query = $this->db->prepare("SELECT * FROM usermembervotes WHERE id=:id");
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

    public function getAllVotedUsersByArgumentId($argumentId){
        try {
            $query = $this->db->prepare("SELECT argumentcomment.id as commentId,usermember.id as memberId,usermembervotes.vote as userVote,usermembervotes.createdtime as votedTime FROM usermembervotes LEFT JOIN argumentcomment ON usermembervotes.argumentId = argumentcomment.argumentId AND usermembervotes.memberId = argumentcomment.memberId AND usermembervotes.vote = argumentcomment.uservote JOIN usermember ON usermembervotes.memberId = usermember.Id JOIN usermemberprofile ON usermembervotes.memberId = usermemberprofile.memberId WHERE usermembervotes.argumentId = :argumentId GROUP BY usermembervotes.memberId");
            $query->execute(array(":argumentId" => $argumentId));
            if($data = $query->fetchAll(PDO::FETCH_ASSOC)){
                return $data;
            }else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();

        }
    }

    public function getVotedUsersByGenderAndVoteAndArgumentId($argumentId,$gender,$vote){
        try {
        $query = $this->db->prepare("SELECT argumentcomment.id as commentId,usermember.id as memberId,usermembervotes.vote as userVote,usermembervotes.createdtime as votedTime FROM usermembervotes LEFT JOIN argumentcomment ON usermembervotes.argumentId = argumentcomment.argumentId AND usermembervotes.memberId = argumentcomment.memberId AND usermembervotes.vote = argumentcomment.uservote JOIN usermember ON usermembervotes.memberId = usermember.Id JOIN usermemberprofile ON usermembervotes.memberId = usermemberprofile.memberId WHERE usermembervotes.argumentId = :argumentId AND usermemberprofile.gender=:gender AND usermembervotes.vote=:vote GROUP BY usermembervotes.memberId");
        $query->execute(array(":argumentId" => $argumentId,":gender"=>$gender ,":vote"=>$vote));
            if($data = $query->fetchAll(PDO::FETCH_ASSOC)){
                return $data;
            }else {
                return false;
            }
        } catch (Exception $e) {
        return $e->getMessage();

        }
    }

    public function getVotedUsersByVoteAndArgumentId($argumentId,$vote){
        try {
            $query = $this->db->prepare("SELECT argumentcomment.id as commentId,usermember.id as memberId,usermembervotes.vote as userVote,usermembervotes.createdtime as votedTime FROM usermembervotes LEFT JOIN argumentcomment ON usermembervotes.argumentId = argumentcomment.argumentId AND usermembervotes.memberId = argumentcomment.memberId AND usermembervotes.vote = argumentcomment.uservote JOIN usermember ON usermembervotes.memberId = usermember.Id JOIN usermemberprofile ON usermembervotes.memberId = usermemberprofile.memberId WHERE usermembervotes.argumentId = :argumentId AND usermembervotes.vote=:vote GROUP BY usermembervotes.memberId");
            $query->execute(array(":argumentId" => $argumentId,":vote"=>$vote));
            if($data = $query->fetchAll(PDO::FETCH_ASSOC)){
                return $data;
            }else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();

        }
    }

}