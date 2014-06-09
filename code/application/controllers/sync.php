<?php

class Sync extends DA_Controller
{
    public $loggedInUserMember;

    function __construct()
    {
        parent::__construct();
        $this->loggedInUserMember = $this->getUserMemberData($_COOKIE['id']);
    }

    function argument()
    {
        $this->load->model('argumentModel');
        $argumentList = $this->argumentModel->syncFeed(AJAX_TIME_INTERVAL, $this->loggedInUserMember->id);
        if ($argumentList) {
            foreach ($argumentList as $argument) {
                $argument->userMember = $this->getUserMemberData($argument->memberId);
            }
            $response = array("response" => true, "argumentList" => $argumentList);
        } else {
            $response = array("reponse" => false, "argumentList" => null);
        }
        echo json_encode($response);
    }

    function argumentListData()
    {
        $this->load->model('argumentModel');
        if (sizeof($this->input->post('argumentIdArray')) == 0) {
            exit();
        }
            $argumentList = $this->argumentModel->syncFeedData($this->input->post('argumentIdArray'));
        if ($argumentList) {
            $response = array("response" => true, "data" => $argumentList);
        } else {
            $response = array("reponse" => false, "data" => null);
        }
        echo json_encode($response);
    }

    public function detail()
    {
        $arguemntId = $this->input->post('argumentId', true);
        $memberId = $this->input->post('memberId', true);
        $this->load->model('argumentvotesmodel');
        $this->load->model('argumentcommentmodel');
        $this->load->model('followargumentmodel');
        $data = null;

        $Votes = $this->argumentvotesmodel->getNewlyVotedByArgumentId($arguemntId);
        $comments = $this->argumentcommentmodel->getNewlyCreatedComments($arguemntId, $memberId);
        $replyCountList = $this->argumentcommentmodel->getNewlyRepliedCount($arguemntId, $memberId);
        $favoriteStatus = $this->followargumentmodel->getByArgumentAndMemberId($memberId, $arguemntId);


        $data->votes = $Votes;
        $data->comments = $comments;
        $data->replyCount = $replyCountList;
        $data->favorite = $favoriteStatus;
        $response = true;

        echo json_encode(array("response" => $response, "data" => $data));
    }

    function profileArgumentsStartedByUser()
    {
        $this->load->model('usermembermodel');
        $argumentList = $this->usermembermodel->getNewlyCreatedArgumentsbyUserMember($this->input->post('memberId'));

        if ($argumentList) {
            foreach ($argumentList as $argument) {
                $argument->userMember = $this->getUserMemberData($argument->memberId);
            }
            $response = array("response" => true, "argumentList" => $argumentList);
        } else {
            $response = array("reponse" => false, "argumentList" => null);
        }
        echo json_encode($response);
       /* $loggedInUserMemberId =$this->loggedInUserMember->id;
        if ($this->input->post('call') == 'argStarted') {
            $data = $this->usermembermodel->syncUserArgumentsData($loggedInUserMemberId,$this->input->post('memberId'), $this->input->post('limit'));
        }
        $response= $data?1:0;

        $result = array('response' => $response, "data" => $data);
        echo json_encode($result);*/
    }

    function  profileArgumentsFollwedByUser(){
        $this->load->model('usermembermodel');
        $argumentList = $this->usermembermodel->getNewlyFollowedArgumentsbyUserMember($this->input->post('memberId'));

        if ($argumentList) {
            foreach ($argumentList as $argument) {
                $argument->userMember = $this->getUserMemberData($argument->memberId);
            }
            $response = array("response" => true, "argumentList" => $argumentList);
        } else {
            $response = array("reponse" => false, "argumentList" => null);
        }
        echo json_encode($response);
    }
    
    function userProfileStatBoard(){
    	
    	$userMemberObject = $this->getUserMemberData($this->input->post('memberId'));
    	
    	if(empty($userMemberObject)){
			$response = false;
		}
		$response = true;
		echo json_encode(array("response" => $response, "data" => $userMemberObject));
		
    	
    }
}