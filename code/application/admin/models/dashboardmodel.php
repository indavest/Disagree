<?php
class DashboardModel extends CI_Model {
	
	function getDashBoardData(){
		try {
			$query = $this->db->prepare("SELECT (SELECT count(*) FROM usermember) as userCount,(SELECT count(*) FROM argument) as argumentCount,(SELECT count(*) FROM argumentcomment) as commentCount,(SELECT SUM(maleagreed+femaleagreed) FROM argumentvotes) as agreedCount,(SELECT SUM(maledisagreed+femaledisagreed) FROM argumentvotes) as disagreedCount, (SELECT Count(*) FROM usermember WHERE online = 1) as onlineCount");
			$query->execute();
			if($data = $query->fetchObject()){
				return $data;
			}else {
				return false;
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}	
	}

	function getDashBoardDataWithinDateRange($fromdate,$todate){
		try {
			$query = $this->db->prepare("SELECT (SELECT count(*) FROM usermember where DATE(createdtime) BETWEEN :fromdate and :todate) as userCount,(SELECT count(*) FROM argument where DATE(createdtime) BETWEEN :fromdate and :todate) as argumentCount,(SELECT count(*) FROM argument where DATE(createdtime) BETWEEN :fromdate and :todate) as argumentCount,(SELECT count(*) FROM argumentcomment where DATE(createdtime) BETWEEN :fromdate and :todate) as commentCount,(SELECT SUM(maleagreed+femaleagreed) FROM argumentvotes where DATE(createdtime) BETWEEN :fromdate and :todate) as agreedCount,(SELECT SUM(maledisagreed+femaledisagreed) FROM argumentvotes where DATE(createdtime) BETWEEN :fromdate and :todate) as disagreedCount, (SELECT Count(*) FROM usermember WHERE online = 1 AND DATE(createdtime) BETWEEN :fromdate and :todate) as onlineCount");
			$query->execute(array(":fromdate"=>$fromdate, ":todate"=>$todate));
			
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