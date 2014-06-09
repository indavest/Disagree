<?php
class FollowArgumentModel extends CI_Model {

    private $argumentId;
    private $memberId;
    
    public function __construct(){}

    public function __destruct()
    {
        $this->dbConnect = null;
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

    public function getByArgumentAndMemberId($memberId, $argumentId)
    {
        try {
            $query = $this->db->prepare("SELECT id FROM usermemberfollowedargument WHERE memberId=:memberId AND argumentId=:argumentId");
            $query->execute(array(":argumentId" => $argumentId, ":memberId" => $memberId));
            if ($query->fetch()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    
}