<?php
class TopicModel extends AD_Model
{

    private $id;
    private $topic;
    private $createdtime;
    private $lastmodified;

    public function __construct()
    {
    }
    public function __destruct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTopic()
    {
        return $this->topic;
    }

    public function setTopic($topic)
    {
        $this->topic = $topic;
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

    public function getAll()
    {
        try {
            $query = $this->db->prepare("SELECT * FROM topic ORDER BY topic ASC");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    public function getAllTopicsWithinDateRange($fromdate, $todate){
     try {
           /* $query = $this->db->prepare("SELECT * FROM topic WHERE DATE(createdtime) BETWEEN :fromdate AND :todate ORDER BY topic ASC");
            $query->execute(array(":fromdate"=>$fromdate, ":todate"=>$todate));*/
     		$query = $this->db->prepare("SELECT * FROM topic where DATE(createdTime) BETWEEN :fromdate AND :todate ORDER BY topic ASC");
     	$query->execute(array(":fromdate"=>$fromdate, ":todate"=>$todate));
			if($data = $query->fetchAll(PDO::FETCH_OBJ)){
				return $data;
			}else {
				return false;
			}
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getTopicArray()
    {
        try {
            $query = $this->db->prepare("SELECT id,topic FROM topic ORDER BY topic ASC");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getById($id)
    {
        try {
            $query = $this->db->prepare("SELECT * FROM topic WHERE id=:id");
            $query->execute(array(":id" => $id));
            return $query->fetchObject();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    
    public function checkByName($data) {
 		try {
            $query = $this->db->prepare("SELECT * FROM topic WHERE topic=:topic");
            $query->execute($data);
            if($value =$query->fetchObject()) {
            	return true;
            }
            else {
            	return false;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
	}

    public function getArgumentsById($id)
    {
        try {
            $query = $this->db->prepare("SELECT * FROM argument WHERE topic=:id");
            $query->execute(array(":id" => $id));
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getTopicArrayWithArgumentCount()
    {
        try {
            $query = $this->db->prepare("SELECT topic.id,COUNT(argument.id) AS argumentCount FROM argument RIGHT JOIN topic ON argument.topic = topic.id GROUP BY topic.topic order by topic.topic ASC");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            return $e->getMessage();
        }

    }
    public function create($data) {
    	try{
    		$data['id'] = $this->generateUniqueId();
            $query = $this->db->prepare("INSERT INTO topic (id,topic,createdtime,lastmodified) VALUES(:id,:topic,now(),now())");
           	if ($query->execute($data)) {
                $query = $this->db->prepare("SELECT * FROM topic WHERE id=:id");
                $query->execute(array(":id" => $data['id']));
                return $query->fetchObject();
    		
    		}
    	} catch(PDOException $e) {
    		return $e->getMessage();
    	}
    }
    public function update($data) {
    	try {
    	 	$query = $this->db->prepare("UPDATE topic SET topic = :topic,lastmodified = now() WHERE id = :id");
            if($query->execute(array(":id" => $data['id'],":topic" => $data['topic']))) {
            	$query = $this->db->prepare("SELECT * FROM topic WHERE id=:id");
                $query->execute(array(":id" => $data['id']));
                return $query->fetchObject();
            }
    	}
    	catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    public function delete($data) {
    	try {
    		$query = $this->db->prepare("DELETE FROM topic where id=:id");
    		if($query->execute($data)) {
    			return true;
    		}
    		else {
    			return false;
    		}
    	} 
    	catch(PDOException $e) {
    		 return $e->getMessage();
    	}
    }

}