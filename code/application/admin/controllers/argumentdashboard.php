<?php


class ArgumentDashboard extends AD_Controller{ 
	private $data = array();
	
	public function __construct(){
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
	
	public function argumentLatestAction() {
		$data['id'] = trim($this->input->post('id'));
		$data['lastActionTime'] = trim($this->input->post('lastmodified'));
		$data['activityType'] = trim($this->input->post('activitytype'));
		$data['argumentId'] = trim($this->input->post('argumentId'));
		
		$this->load->model('basemodel');
		
		if($LastAction = $this->basemodel->getArgumentLatestAction($data)) {
			$response = true;
			$this->data['lastAction'] = $LastAction;
		}
		else {
			$response = false;
			
		}
		$result = array("response" => $response, "data" => $this->data);
		echo json_encode($result);
	}
	 
	public function getComments() {
	    $this->load->model('argumentcommentmodel');
        $data = null;

        //getting 10 comments on argument (input argumentId,upperlimit,lowerlimit; output: array of comment objects)
        $commentsList = $this->argumentcommentmodel->getAjaxCommentsbyArgumentId($this->input->post('argumentId'), intval($this->input->post('lowerLimit')), intval($this->input->post('noofrecords')));

        if ($commentsList) {
            $commentIds = array();
            foreach ($commentsList as $index => $comment) {
                $commentIds[$index] = $comment['id'];
                $commentsList[$index]['userImage'] = $this->getThumbPath($comment['userImage']);
            }
            //print_r($commentsList);

            //getting replies on all comments fetch on an argument (input array of commentids; output:replies count if any comment have replies, false otherwise)
            $replyCountList = $this->argumentcommentmodel->getReplyCountByCommentIDs($commentIds);
            if (!$replyCountList) { // if repliescount is false fill that with zero's
                foreach ($commentsList as $index => $comment) {
                    $replyCountList[$comment['id']] = 0;
                }
            } else { //if replies count is true and some comments dont have replies then make those reply count zero in those comments
                foreach ($commentsList as $index => $comment) {
                    if (!(array_key_exists($comment['id'], $replyCountList))) {
                        $replyCountList[$comment['id']] = 0;
                    }
                }
            }

            $data->comments = $commentsList;
            $data->replyCount = $replyCountList;
            $response = true;
        } else {
            $response = false;
            $data->comments = false;
            $data->replyCount = false;
        }
        echo json_encode(array("response" => $response, "data" => $data));
    }
	

	
	function getReplies()
    {
       $this->load->model('argumentcommentmodel');   
        $commentId = $this->input->post('commentId');

        $replies = $this->argumentcommentmodel->getReplysByCommetnId($commentId);
        foreach ($replies as $reply){
        	$reply->userImage = $this->getThumbPath($reply->userImage);
        }
        $result = array("respose" => 1, "data" => $replies);

        echo json_encode($result);
    }
	
	

}
	