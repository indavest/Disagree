<?php
class ArgumentVotesModel extends CI_Model {

    private $id;
    private $maleagreed;
    private $maledisagreed;
    private $femaleagreed;
    private $femaledisagreed;
    private $generalagreed;
    private $generaldisagreed;
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

    public function getGeneralagreed()
    {
        return $this->generalagreed;
    }

    public function setGeneralagreed($generalagreed)
    {
        $this->generalagreed = $generalagreed;
    }

    public function getGeneraldisagreed()
    {
        return $this->generaldisagreed;
    }

    public function setGeneraldisagreed($generaldisagreed)
    {
        $this->generaldisagreed = $generaldisagreed;
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

	public function reduceVote($data){
		try{
        	$criteria = ($data['gender']=="M")?"male":"female";
	        $criteria .= ($data['vote'])?"agreed":"disagreed";
	        $query= "UPDATE argumentvotes SET ".$criteria." = ".$criteria."-1 , lastmodified = now() WHERE argumentId=:argumentId";
	        $query = $this->db->prepare($query);
	        if($query->execute(array(":argumentId"=>$data['argumentId']))){
	            return true;
	        }else{
	            return false;
	        }
        }catch(PDOException $e){
            return $e->getMessage();
        }
	}

}