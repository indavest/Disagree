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

    public function add($data)
    {
        try {
        	$data['id'] = $this->generateUniqueId();
            $query = $this->db->prepare("INSERT INTO usermembervotes (id,vote,argumentId,memberId,createdtime) VALUES(:id,:vote,:argumentId,:memberId,now())");
            return $query->execute($data);
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

}