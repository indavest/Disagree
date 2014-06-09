<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends AD_Controller{
	
	private $data = array();
	
	function __construct(){
		parent::__construct();
		
		$this->data['loggedInAdminUser'] = $this->loggedInAdminuser;
		$this->data['loggedInAdminUserFlag'] = $this->loggedInAdminUserFlag;
		$this->data['interests'] = $this->interests;
		$this->data['fromDate'] = & $this->fromDate;
		$this->data['toDate'] = & $this->toDate;
		$this->data['dateRangeFlag'] = true;
		if(!$this->loggedInAdminUserFlag){
			$this->data['contentview'] = 'login';
			$this->load->view('includes/template', $this->data);
			exit();
		}
		
	}
	
	public function index()
	{		
		$this->data['jsList'] = array("dashboard");
		$this->load->model('dashboardModel');
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		if($fromDate != '' && $toDate != '') {
			$this->setDates($fromDate, $toDate); 
		}
		$this->data['dashboardData'] = $this->dashboardModel->getDashBoardDataWithinDateRange($this->fromDate,$this->toDate);
		$this->data['contentview'] = 'dashboard';
		$this->load->view('includes/template', $this->data);
		//}
		
	}
	
	public function arguments(){
		$this->data['jsList'] = array("argumentDashboard");
		$this->load->model('argumentModel');
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		if($fromDate != '' && $toDate != '') {
			$this->setDates($fromDate, $toDate); 
		}
			$this->data['argumentList'] = $this->argumentModel->getArgumentsWithinDateRange($this->fromDate,$this->toDate);
			$this->data['contentview'] = 'argumentDashboard';
			$this->load->view('includes/template', $this->data);
		
	}
	
	public function argument(){
		$argumentId = $this->input->post('id');
		$this->load->model('argumentModel');
		$data['argument'] = $this->argumentModel->getArgumentData($argumentId);
		$data['lastAction'] = $this->argumentModel->getArgumentLastActionTime($argumentId);
		if($data['argument']){
			$response = true;
		}else {
			$response = false;
		}
		if($data['lastAction']) {
			$lastActionTimeResponse = true;
		}
		else {
			$lastActionTimeResponse = false;
		}
		echo json_encode(array('response' => $response,'data' => $data, 'lastActionTimeresponse' => $lastActionTimeResponse));		
	}

	public function users(){
		$this->data['jsList'] = array("userDashboard");
		$this->load->model('userMemberModel');
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		if($fromDate != '' && $toDate != '') {
			$this->setDates($fromDate, $toDate); 
		}
		
		$this->data['userList'] = $this->userMemberModel->getAllUsersWithinDateRange($this->fromDate,$this->toDate);
		$this->data['contentview'] = 'userDashboard';
		$this->load->view('includes/template', $this->data);
		

	}
	
	public function user(){
		$userId = $this->input->post('id');
		$this->load->model('userMemberModel');
		$data['user'] = $this->userMemberModel->getUserData($userId);
		$data['lastAction'] = $this->userMemberModel->getUserLastActionTime($userId);
		if($data['user']){
			$response = true;
		}else {
			$response = false;
		}
		if($data['lastAction']) {
			$lastActionTimeResponse = true;
		}
		else {
			$lastActionTimeResponse = false;
		}
		echo json_encode(array('response' => $response, 'data' => $data, 'lastActionTimeresponse' => $lastActionTimeResponse));
	}
	
	public function loadDashboardAnalytics(){
		$config['email'] = 'analytics@disagree.me';
        $config['passwd'] = 'indavest123';

        $pageView = array();
        $visits = array();
        $this->load->library('gapi', $config);
        $ga = $this->gapi;
        $ga->requestReportData('60810184', array('browser'), array('pageviews','visits'), $sort_metric=null, $filter=null, $start_date=null, $end_date=null, $start_index=1, $max_results=30);
		
		foreach($ga->getResults() as $result){
			array_push($pageView, $result->getPageviews());
			array_push($visits, $result->getVisits());	
		}
		echo json_encode(array("response" => true, "pageView" => $pageView, "visits" => $visits));
	}
	
	public function spam(){
		$this->data['jsList'] = array("spamDashboard");
		$this->data['argumentList'] = array();
		$this->data['commentList'] = array();
		$this->load->model('baseModel');
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		if($fromDate != '' && $toDate != '') {
			$this->setDates($fromDate, $toDate); 
		}
		$spamList = $this->baseModel->getSpamWithinDateRange($this->fromDate,$this->toDate);
		foreach ($spamList as $spam){
			if($spam->type == 'argument'){
				array_push($this->data['argumentList'], $spam);
			}else{
				array_push($this->data['commentList'], $spam);
			}
		}
		$this->data['contentview'] = 'spamDashboard';
		$this->load->view('includes/template', $this->data);
	}
	
	public function spamDetail(){
		$type = $this->input->post('type');
		$recordId = $this->input->post('recordId');
		$this->load->model('argumentModel');
		$this->load->model('argumentCommentModel');
		if($type == 'argument'){
			$data = $this->argumentModel->getById($recordId);
		}else{
			$data = $this->argumentCommentModel->getById($recordId);
		}
		if($data){
			$response = true;
		}else{
			$response = false;
		}
		echo json_encode(array("response" => $response, "data" => $data));
	}
	
	public function markNotSpam(){
		$this->load->model('baseModel');
		if($this->baseModel->markNotSpam($this->input->post('id'))){
			$response = true;
		}else {
			$response = false;
		}
		echo json_encode(array("response" => $response));
	}
	
	public function deleteRecord(){
		$type = $this->input->post('type');
		$recordId = $this->input->post('recordId');
		$id = $this->input->post('id');
		$response = false;
		$this->load->model('baseModel');
		$this->load->model('argumentModel');
		$this->load->model('argumentCommentModel');
		$this->load->model('argumentVotesModel');
		if($type == 'argument'){
			$response = $this->argumentModel->delete($recordId);
		}else {
			$comment = $this->argumentCommentModel->getById($recordId);
			$comment->userMember = $this->getFrontEndUserMemberData($comment->memberId);
			//Check it is a vote if vote get Agree or Disagree
			$data = array();
			$data['argumentId'] = $comment->argumentId;
			$data['gender'] = $comment->userMember->gender;
			$data['vote'] = $comment->vote;
			if($comment->vote == 0 || $comment->vote == 1){
				//Reduce vote count
				$this->argumentVotesModel->reduceVote($data);	
			} 
			if($this->argumentCommentModel->delete($recordId) && $this->baseModel->markNotSpam($id)){
				$response = true;
			}
			//Delete the record from spam table
		}
		echo json_encode(array("response" => $response));
	}
	public function interests() {
		$this->data['jsList'] = array("interests");
		$this->load->model('topicmodel');
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		if($fromDate != '' && $toDate != '') {
			$this->setDates($fromDate, $toDate); 
		}
		$this->data['dateRangeFlag'] = false;
		$this->data['interestList'] = $this->topicmodel->getAll();
		$this->data['contentview'] = 'interests';
		$this->load->view('includes/template', $this->data);
		
	}
	public function ajaxinterests() {
	
		$this->load->model('topicmodel');
		$this->data['interestList'] = array();
		$this->data['interestList'] = $this->topicmodel->getAll();
		if($this->data['interestList']) {
			$response = true;	
		}
		else {
			$response = false;
		}
		$result = array("response" => $response, "data" => $interests);
		echo json_encode($result);
	}
	
	public function uploadTopic() {
			$interestname = trim($this->input->post('topic'));
			$this->load->model('topicmodel');
			$data['topic'] =  $interestname;
			if(!($this->topicmodel->checkByName($data))) {
				if($newTopic = $this->topicmodel->create($data)) {
					redirect("dashboard/interests");
				}	 
			}
			redirect("dashboard/interests");
	} 
	
	public function editTopic() {
		$interestname = trim($this->input->post('topic'));
		$interestid = trim($this->input->post('id'));
		$this->load->model('topicmodel');
		if($interestid != "" && $interestname != 'Enter Interest') {
			$data['topic'] =  $interestname;
			$data['id'] = $interestid;
			if($topicFromDb = $this->topicmodel->update($data)){
				redirect("dashboard/interests");
			}
			else {
				redirect("dashboard/interests");
			}	
						
		}
	}
	public function deleteTopic() {
		$interestid = trim($this->input->post('id'));	
		$this->load->model('topicmodel');
		if($interestid != "") {
			$data['id'] = $interestid;
			if($this->topicmodel->delete($data)) {
				
				$response = true;
			}
			else {
				$response = false;
			}
			$result = array("response" => $response, "data" => $this->data);
			echo json_encode($result);
		}
	}
	
	
	public function checkInterest() {
		
		$interestName = trim($this->input->post('topic'));
		$this->load->model('topicmodel');
		if($interestName != "") {
			$data['topic'] = $interestName;
			if($topic =$this->topicmodel->checkByName($data)) {
				$response = true;
				$this->data['topic'] = $topic;
			}
			else {
				$response = false;
			}
		}
		$result = array("response" => $response, "data" => $this->data);
		echo json_encode($result);
	}
	
	public function userLatestAction() {
		$data['id'] = trim($this->input->post('id'));
		$data['lastActionTime'] = trim($this->input->post('lastmodified'));
		$data['activityType'] = trim($this->input->post('type'));
		$data['userId'] = trim($this->input->post('userId'));
		$this->load->model('basemodel');
		if($LastAction = $this->basemodel->getLatestAction($data)) {
			$response = true;
			$data['lastAction'] = $LastAction;
		}
		else {
			$response = false;
		}
		$result = array("response" => $response, "data" => $data);
		echo json_encode($result);
	}
	

	
	
	

			
}