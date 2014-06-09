<?php
class ArgumentVotesModel extends DA_Model {

    private $id;
    private $maleagreed;
    private $maledisagreed;
    private $femaleagreed;
    private $femaledisagreed;
    private $createdtime;
    private $lastmodified;
    private $argumentId;
    private $agreed;
    private $disagreed;
    private $memberId;
    private $gender;
    private $vote;

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

    public function getMaleagreed()
    {
        return $this->maleagreed;
    }

    public function setMaleagreed($maleagreed)
    {
        $this->maleagreed = $maleagreed;
    }

    public function getMaledisagreed()
    {
        return $this->maledisagreed;
    }

    public function setMaledisagreed($maledisagreed)
    {
        $this->maledisagreed = $maledisagreed;
    }

    public function getFemaleagreed()
    {
        return $this->femaleagreed;
    }

    public function setFemaleagreed($femaleagreed)
    {
        $this->femaleagreed = $femaleagreed;
    }

    public function getFemaledisagreed()
    {
        return $this->femaledisagreed;
    }

    public function setFemaledisagreed($femaledisagreed)
    {
        $this->femaledisagreed = $femaledisagreed;
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

    public function getArgumentId()
    {
        return $this->argumentId;
    }

    public function setArgumentId($argumentId)
    {
        $this->argumentId = $argumentId;
    }

    public function setAgreed($agreed)
    {
        $this->agreed = $agreed;
    }

    public function getAgreed()
    {
        return $this->agreed;
    }

    public function setDisagreed($disagreed)
    {
        $this->disagreed = $disagreed;
    }

    public function getDisagreed()
    {
        return $this->disagreed;
    }

    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setVote($vote)
    {
        $this->vote = $vote;
    }

    public function getVote()
    {
        return $this->vote;
    }

	public function getTotalAgreed($argumentId)
    {
        try {
            $query = $this->db->prepare("SELECT ifnull(maleagreed + femaleagreed, 0) as agreedCount FROM argumentvotes WHERE argumentId=:argumentId");
            $query->execute(array(':argumentId' => $argumentId));
            return $query->fetchObject();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getTotalDisagreed($argumentId)
    {
        try {
            $query = $this->db->prepare("SELECT ifnull(maledisagreed + femaledisagreed) as disagreedCount FROM argumentvotes WHERE argumentId=:argumentId");
            $query->execute(array(':argumentId' => $argumentId));
            return $query->fetchObject();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getByArgumentId($argumentId)
    {
        $baseActivity = new base();
        try {
            $query = $this->db->prepare("SELECT id, from argumentVotes where argumentId = :argumentId");
            $query->execute(array(":argumentId" => $argumentId));
            return $query->fetchObject();
        }
        catch (PDOException $e) {
            $e->getMessage();
        }
    }

    public function getNewlyVoted($timeInterval)
    {
    	try {
            $query = $this->db->prepare("SELECT ifnull(maleagreed + femaleagreed, 0) as agreedCount,ifnull(maledisagreed + femaledisagreed, 0) as disagreedCount, argumentId  FROM argumentvotes WHERE lastmodified BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW() ORDER BY lastmodified DESC");
            $query->execute(array(':timeInterval' => $timeInterval));
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    
    public function getNewlyVotedByArgumentId($argumentId){
    	try {
            $query = $this->db->prepare("SELECT ifnull(maleagreed + femaleagreed, 0) as agreedCount,ifnull(maledisagreed + femaledisagreed, 0) as disagreedCount, maleagreed,maledisagreed, femaleagreed, femaledisagreed, argumentId  FROM argumentvotes WHERE argumentid=:argumentId AND lastmodified BETWEEN DATE_SUB(NOW() , INTERVAL :timeInterval MINUTE) AND NOW() ORDER BY lastmodified DESC");
            $query->execute(array(':timeInterval' => AJAX_TIME_INTERVAL,":argumentId"=>$argumentId));
            if ($query->rowCount() > 0) {
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function addVote($data){
        try{
        	$data['id'] = $this->generateUniqueId();
	        $criteria = ($data['gender']=="M")?"male":"female";
	        $criteria .= ($data['vote'])?"agreed":"disagreed";
	        $query= "INSERT INTO argumentvotes (id,".$criteria.",argumentId,createdtime,lastmodified)VALUES(:id,1,:argumentId,now(),now())ON DUPLICATE KEY UPDATE ".$criteria." = ".$criteria."+1 , lastmodified = now()";
	        $query = $this->db->prepare($query);
	        if($query->execute(array("id"=>$data['id'],"argumentId"=>$data['argumentId']))){
	            return $data['id'];
	        }else{
	            return false;
	        }
        }catch(PDOException $e){
            $e->getMessage();
        }

    }

}