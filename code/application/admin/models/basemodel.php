<?php
class BaseModel extends CI_Model {

	public function __construct(){}

    public function __destruct()
    {
        $this->dbConnect = null;
    }

    public function getSpam(){
    	try {
	    	$query = $this->db->prepare("SELECT * FROM spamreport");
	    	$query->execute();
	    	if($data = $query->fetchAll(PDO::FETCH_OBJ)){
	    		return $data;
	    	}else {
	    		return false;
	    	}	
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    public function getSpamWithinDateRange($fromdate, $todate){
    	try {
	    	$query = $this->db->prepare("SELECT * FROM spamreport where DATE(createdTime) BETWEEN :fromdate AND :todate");
	    	$query->execute(array(":fromdate"=>$fromdate, ":todate"=>$todate));
	    	if($data = $query->fetchAll(PDO::FETCH_OBJ)){
	    		return $data;
	    	}else {
	    		return false;
	    	}	
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    public function markNotSpam($id){
    	try {
    		$query = $this->db->prepare('DELETE FROM spamreport WHERE id=:id');
    		if($query->execute(array(":id" => $id))){
    			return true;
    		}else {
    			return false;
    		}
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    
    public function getLatestAction($data) {
    	try {
			if($data['activityType'] ==  RECENT_ACTION_CREATE_ARGUMENT) {
				
				$query = $this->db->prepare("SELECT a.*,t.topic FROM argument a,topic t WHERE a.id = :id AND a.lastmodified = :createdtime AND a.memberId = :memberId AND a.topic = t.id ORDER BY a.lastmodified DESC LIMIT 1");

				
			} else if($data['activityType'] == RECENT_ACTION_COMMENT_ARGUMENT) {
				
				$query = $this->db->prepare("SELECT ac.*, a.id as argumentid,a.title,a.argument,a.status,a.topic,t.topic,(SELECT pac.commenttext FROM argumentcomment pac WHERE ac.parentId IS NOT NULL AND pac.id = ac.parentId) as parentcomment FROM argumentcomment ac, argument a, topic t WHERE ac.id = :id AND ac.createdtime = :createdtime AND ac.memberId = :memberId AND ac.argumentId = a.id AND a.topic = t.id ORDER BY ac.createdtime DESC LIMIT 1");
			
			} else if($data['activityType'] == RECENT_ACTION_SPAM_REPORT) {
				
				$query = $this->db->prepare("(SELECT s.*, a.id as argumentid, a.title AS title,a.argument AS argument,a.status as status, t.topic AS topic,'no comment' AS commenttext FROM spamreport s, argument a, topic t WHERE s.type = 'argument' AND s.id = :id AND s.memberId = :memberId AND s.createdtime = :createdtime AND a.id = s.recordId AND a.topic = t.id ORDER BY s.createdtime DESC LIMIT 1) UNION ALL
					(SELECT s.*, a.id as argumentid, a.title AS title,a.argument AS arguement,a.status as status, t.topic AS topic,ac.commenttext AS commenttext FROM spamreport s, argument a,argumentcomment ac, topic t WHERE s.type = 'comment' and s.id = :id AND s.memberId = :memberId AND s.createdtime = :createdtime and ac.id = s.recordId and a.id = ac.argumentId AND a.topic = t.id ORDER BY s.createdtime DESC LIMIT 1)");
			
			} else if($data['activityType'] == RECENT_ACTION_FOLLOWED_A_ARGUMENT) {

				$query = $this->db->prepare("SELECT u.*,a.id as argumentid, a.title AS title,a.argument AS argument,a.status as status, t.topic AS topic FROM usermemberfollowedargument u, argument a,topic t WHERE u.id = :id AND u.createdtime = :createdtime AND u.memberId = :memberId AND u.argumentId = a.id AND a.topic = t.id ORDER BY u.createdtime DESC LIMIT 1");
				   
			} else if($data['activityType'] == RECENT_ACTION_FOLLOWED_A_MEMBER) {

				$query = $this->db->prepare("SELECT m.*,u.memberId as memberId FROM usermemberfollowedmember u, usermember m WHERE u.id = :id AND u.createdtime = :createdtime AND u.memberId = :memberId AND u.followedmemberId = m.id ORDER BY u.createdtime DESC LIMIT 1");
				   
			} else if ($data['activityType'] == RECENT_ACTION_VOTE_AN_ARGUMENT) {
				
				$query = $this->db->prepare("SELECT uv.vote, uv.id as voteid, a.*, (SELECT ac.commenttext FROM argumentcomment ac WHERE ac.argumentId = a.id AND ac.memberId = :memberId) as commenttext, t.topic FROM usermembervotes uv, argument a, topic t WHERE uv.id = :id AND uv.createdtime = :createdtime AND uv.memberId = :memberId AND uv.argumentId = a.id AND a.topic = t.id ORDER BY uv.createdtime DESC LIMIT 1");
			}
			
			$query->execute(array(":id" => $data['id'],":createdtime" => $data['lastActionTime'],":memberId" => $data['userId']));

			if($data = $query->fetchAll(PDO::FETCH_OBJ)){
                return $data;
            }else {
              return false; 
            }
		} catch (PDOException $e) { 
			return $e->getMessage();
		}
    }
    
     public function getArgumentLatestAction($data) { 
     	try {
     		
     		if($data['activityType'] == ARGUMENT_RECENT_ACTION_STATUS_CHANGE) {
     			
     			$query = $this->db->prepare("SELECT a.status as argumentstatus, m.* from argument a,usermember m where a.id = :id AND a.id= :argumentId AND  a.lastmodified = :createdtime AND a.memberId= m.id");
     		} else if($data['activityType'] == ARGUMENT_RECENT_ACTION_COMMENT) {
				
				$query = $this->db->prepare("SELECT ac.*, m.id as userid,m.username as username,m.status as memberstatus, (SELECT pac.commenttext FROM argumentcomment pac WHERE ac.parentId IS NOT NULL AND pac.id = ac.parentId) as parentcomment FROM argumentcomment ac, usermember m WHERE ac.id = :id AND ac.createdtime = :createdtime AND ac.argumentId = :argumentId AND ac.memberId = m.id ORDER BY ac.createdtime DESC LIMIT 1");
			
			} else if($data['activityType'] == ARGUMENT_RECENT_ACTION_SPAM_ARGUMENT) {
				
				$query = $this->db->prepare("SELECT m.*, s.id as spamid,'no comment' AS commenttext FROM spamreport s, usermember m WHERE s.type = 'argument' AND s.id = :id AND s.recordId = :argumentId AND s.createdtime = :createdtime AND m.id = s.memberId ORDER BY s.createdtime DESC LIMIT 1");
			
			}  else if($data['activityType'] == ARGUMENT_RECENT_ACTION_SPAM_ARGUMENT_COMMENT) {
				
				$query = $this->db->prepare("SELECT ac.*,m.username as username, m.id as userid, m.status as memberstatus FROM spamreport s, usermember m, argumentcomment ac WHERE s.type = 'comment' AND s.id = :id AND s.createdtime = :createdtime AND ac.id = s.recordId AND ac.argumentId = :argumentId AND m.id = s.memberId ORDER BY s.createdtime DESC LIMIT 1");
							   
			} else if($data['activityType'] == ARGUMENT_RECENT_ACTION_FOLLOWED_BY_MEMBER) {

				$query = $this->db->prepare("SELECT m.*,u.memberId as memberId FROM usermemberfollowedargument u, usermember m WHERE u.id = :id AND u.createdtime = :createdtime AND u.argumentId = :argumentId AND u.memberId = m.id ORDER BY u.createdtime DESC LIMIT 1"); 
			} else if ($data['activityType'] == ARGUMENT_RECENT_ACTION_VOTE_AN_ARGUMENT) {
				
				$query = $this->db->prepare("SELECT uv.vote, uv.id as voteid, m.*, (SELECT ac.commenttext FROM argumentcomment ac WHERE ac.argumentId = :argumentId AND ac.memberId = uv.memberId) as commenttext FROM usermembervotes uv, userMember m WHERE uv.id = :id AND uv.createdtime = :createdtime AND uv.argumentId = :argumentId AND uv.memberId = m.id ORDER BY uv.createdtime DESC LIMIT 1");
			}
     		
     		$query->execute(array(":id" => $data['id'],":createdtime" => $data['lastActionTime'],":argumentId" => $data['argumentId']));
     		if($data = $query->fetchAll(PDO::FETCH_OBJ)){
     			return $data;
 
            }else {
              return false; 
            }
     	} catch (PDOException $e) {
			return $e->getMessage();
		}
     }


    
}