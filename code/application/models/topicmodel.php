<?php
class TopicModel extends CI_Model
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
}