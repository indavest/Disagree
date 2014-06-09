<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AD_Controller extends CI_Controller {
	
	var $loggedInAdminuser = null;
	var $loggedInAdminUserFlag = null;
	var $interests = array();
	
	function __construct(){
		parent::__construct();
		
		$adminId = $this->input->cookie('admin_id');
		$this->loggedInAdminUserFlag = $this->input->cookie('isAdminLoggedIn');
		$this->loggedInAdminuser = $this->getUserMemberData($adminId);	
		$this->interests = 	$this->getAllTopics();
		$this->fromDate = date('Y-m-d', strtotime("-90 days"));
		$this->toDate = date('Y-m-d');
	}
	
	function getUserMemberData($id){
		$this->load->model('userMemberModel');
		//Construct UserMember Obj with profile and Stats
		$userMember = $this->userMemberModel->getById($id);
		return $userMember;
	}
	
	function getFrontEndUserMemberData($id){
		$this->load->model('userMemberModel');
		//Construct UserMember Obj with profile and Stats
		$userMember = $this->userMemberModel->getFrontEndUserById($id);
		return $userMember;
	}
	function getAllTopics() {
		$this->load->model('topicmodel');
		$interests = array();
		$interests = $this->topicmodel->getAll();
		return $interests;
	}
    public function getThumbPath($mainPath){
    	if(!strpos($mainPath,"graph.facebook.com")){
    		$picPath = explode("_", $mainPath);
    		$picExt = explode(".", $picPath[1]);
    		$thumbPath = $picPath[0]."_thumb.".$picExt[1];
    	}else {
    		$thumbPath = $mainPath;
    	}
    	 
    	return $thumbPath;
    }
	public function setDates($fromDate, $toDate) {
				
		$this->toDate = $toDate;
		$this->fromDate = $fromDate;
		
	}
}
