<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Action extends DA_Controller
{

    public $loggedInUserMember;

    function __construct()
    {
        parent::__construct();
        $this->loggedInUserMember = $this->getUserMemberData($this->input->cookie('id'));
    }

    function argumentCreate()
    {
        $this->load->model('argumentModel');
        $this->load->model('userMemberModel');
        $this->load->model('topicModel');
        $argumentData = array('title' => $this->input->post('argumentTitle', true), 'argument' => $this->input->post('argumentDesc', true), 'memberId' => $this->input->post('memberId'), 'status' => 1, 'topic' => $this->input->post('topic'), 'source' => $this->input->post('source'));
        $postedArgument = $this->argumentModel->create($argumentData);
        $postedUser = $this->userMemberModel->getById($postedArgument->memberId);
        $argumentTopicObj = $this->topicModel->getById($postedArgument->topic);
        $postedArgument->userMember = $postedUser;
        if ($postedArgument) {
            $result = array("response" => true, "data" => $postedArgument);
        } else {
            $result = array("response" => false);
        }
        echo json_encode($result);
    }

    function argumentUpdate(){
        $this->load->model('argumentModel');
        $this->load->model('userMemberModel');
        $this->load->model('topicModel');
        $argumentData = array('id'=>$this->input->post('argumentId', true),'title' => $this->input->post('argumentTitle', true), 'argument' => $this->input->post('argumentDesc', true), 'memberId' => $this->input->post('memberId'), 'status' => 1, 'topic' => $this->input->post('topic'), 'source' => $this->input->post('source'));
        $postedArgument = $this->argumentModel->update($argumentData);
        $postedUser = $this->userMemberModel->getById($postedArgument->memberId);
        $argumentTopicCountObj = $this->topicModel->getTopicArrayWithArgumentCount($postedArgument->topic);
        $postedArgument->userMember = $postedUser;
        $postedArgument->topicArgumentCount = $argumentTopicCountObj[$argumentData['topic']];
        if ($postedArgument) {
            $result = array("response" => true, "data" => $postedArgument);
        } else {
            $result = array("response" => false);
        }
        echo json_encode($result);
    }

    function argumentFollow()
    {
        $this->load->model('userMemberModel');
        $this->load->model('argumentModel');
        $argumentId = $this->input->post('argumentId');
        $memberId = $this->loggedInUserMember->id;
        $argument = $this->argumentModel->getById($argumentId);
        if($argument->status == 1){             //to prevent action on locked argument
        $response = $this->userMemberModel->toggleArgumentFollow($argumentId, $memberId);
        if ($response) {
            $result = array("response" => 1, "data" => 1);
            $this->load->model('BaseModel');
            $this->BaseModel->notificationRequest(FOLLOW_ARGUMENT_NOTIFICATION,$response,$this->loggedInUserMember->id,null);
        } elseif ($response == 0) {
            $result = array("response" => 1, "data" => 0);
        } elseif ($response == -1) {
            $result = array("response" => 0);
        }
        }else{
            $result = array("response" => 0);
        }
        echo json_encode($result);
    }

    function argumentFetch()
    {
        $this->load->model('argumentModel');
        $argumentObj = $this->argumentModel;
        $limit = $this->input->post('limit');
        $limit = $limit * 6;
        $argumentList = $argumentObj->getByNumberByAjax($limit, 6);

        echo json_encode(array("argumentList" => $argumentList));
    }

    function setUserOnline()
    {
        $memberId = $this->input->post('memberId');
        $this->load->model('userMemberModel');
        $this->load->model('baseModel');
        $userAccess = $this->userMember->getUserAccessUpdatedTime($memberId); //Get the user last offline time
        if ($this->userMember->setUserOnline($memberId)) { //Set user online
            $timeInterval = $this->base->time_difference_DB_Call($userAccess->lastmodified);
            $data = $this->base->getOnlineNotification($memberId, $timeInterval); // Get Recent Notifications
            foreach ($data as $onlineNotification) { //update them in onlinenotification table
                $this->base->registerOnlineNotification($onlineNotification);
            }
            $notificationCount = $this->base->getUnreadNotificationCount($memberId); //get all the unread notification Count (New notifications + old unread notifications)
            if ($notificationCount->count > 0) {
                $result = array("response" => 1, "data" => $notificationCount->count);
            } else {
                $result = array("response" => 0);
            }
        } else {
            $result = array("response" => 0);
        }
        echo json_encode($result);
    }

    public function search()
    {
        $this->load->model('baseModel');
        $searchObj = $this->baseModel;
        $startLimit = 0;
        $endLimit = 5;
        $searchItemListArray = $searchObj->liveSearch($this->input->post('keyword'), $startLimit, $endLimit);
        $argumentList = $searchItemListArray['argument'];
        foreach ($argumentList as $argument){
        	$argument->profileThumb = $this->getThumbPath($argument->profilephoto);
        }
        $userMemberList = $searchItemListArray['usermember'];
        foreach ($userMemberList as $userMember){
        	$userMember->profileThumb = $this->getThumbPath($userMember->profilephoto);
        }
        $argumentListHtml = "";
        $userMemberListHtml = "";
        $data = null;

        $data->argumentList = $argumentList;
        $data->userMemberList = $userMemberList;

        echo json_encode(array('response' => true, 'data' => $data));
    }

    public function profileStartedArgument()
    { //LOAD ARGUMENTS CREATED BY A USERMEMBER
        //GET DATABASE FILES
        $this->load->model('userMemberModel');
        $this->load->model('argumentModel');
        $this->load->model('followArgumentModel');
        $this->load->model('argumentVotesModel');
        $this->load->model('userMemberVotesModel');
        $response = 0;
		
        $limit = $this->input->post('limit') * ARGUMENT_AJAX_FETCH_COUNT;
        //GET DATA  AS PER POST DATA
        $argumentList = $this->argumentModel->getAjaxCreatedArgumentsbyUserMember($this->input->post('memberId'),$limit,ARGUMENT_AJAX_FETCH_COUNT);

        //PROCESS THE NEEDFULL
        if ($argumentList != "") {
            foreach ($argumentList as $argument) {
                $isLoggedInMemberArgumentOwner = ($this->loggedInUserMember->id == $argument->memberId) ? true : false;
                $argument->userMember = $this->getUserMemberData($argument->memberId);
                if ($isLoggedInMemberArgumentOwner) {
                    $argument->isFavorite = -1;
                    $argument->isLoggedInMemberOwner = true;
                } else {
                    $argument->isFavorite = $this->followArgumentModel->getByArgumentAndMemberId($this->loggedInUserMember->id, $argument->id);
                    $argument->isLoggedInMemberOwner = false;
                }
                $argument->isLoggedInUserMemberVoted = $this->userMemberVotesModel->checkVotedByArgument(array("argumentId" => $argument->id, "memberId" => $this->loggedInUserMember->id));
                //$argument->createdtime = $this->time_difference($argument->createdtime);
            }
            $response = 1;
        }else{
            $response = 0;
            $argumentList = null;
        }
        $result = array("response" => $response, "data" => $argumentList);
        echo json_encode($result);
    }

    function profileFollowingArgument()
    { //LOADS ARGUMNETS A USERMEMBER FOLLOWS
        $this->load->model('userMemberModel');
        $this->load->model('argumentModel');
        $this->load->model('followArgumentModel');
        $this->load->model('argumentVotesModel');
        $this->load->model('userMemberVotesModel');

        $userMemberId = $this->input->post('memberId');
        $response = 0;
		$limit = $this->input->post('limit') * ARGUMENT_AJAX_FETCH_COUNT;
        $argumentListFollowed = $this->userMemberModel->getFollowedArgumentsbyUserMember($userMemberId,$limit,ARGUMENT_AJAX_FETCH_COUNT);

        if ($argumentListFollowed != "") {
            foreach ($argumentListFollowed as $argument) {
                $argument->userMember = $this->userMemberModel->getById($argument->memberId);
                $isLoggedInMemberArgumentOwner = ($this->loggedInUserMember->id == $argument->memberId) ? true : false;
                $argument->userMember = $this->getUserMemberData($argument->memberId);
                $argument->agreed = $argument->agreed;
                $argument->disagreed = $argument->disagreed;
                //$argument->createdtime = $this->time_difference($argument->createdtime);
                if ($isLoggedInMemberArgumentOwner) {
                    $argument->isFavorite = -1;
                    $argument->isLoggedInMemberOwner = true;
                } else {
                    $argument->isFavorite = $this->followArgumentModel->getByArgumentAndMemberId($this->loggedInUserMember->id, $argument->id);
                    $argument->isLoggedInMemberOwner = false;
                }
                $argument->isLoggedInUserMemberVoted = $this->userMemberVotesModel->checkVotedByArgument(array("argumentId" => $argument->id, "memberId" => $this->loggedInUserMember->id));
            }
            $response = 1;
        }else{
            $response = 0;
            $argumentListFollowed = null;
        }

        $result = array("response" => $response, "data" => $argumentListFollowed);
        echo json_encode($result);
    }

    function profileMemberFollowing()
    {
    //LOADS THE MEMBER FOLLOWING
        $this->load->model('userMemberModel');
        $response = false;
        $UserDataList = array();

        //GET THE PARAMETERS AND DATA NEEDED
        $limit = $this->input->post('limit') * USER_AJAX_FETCH_COUNT;
        $this->data['membersFollowingMeList'] = $this->userMemberModel->getMembersFollowedByMe($this->input->post('memberId'), $limit, USER_AJAX_FETCH_COUNT);

        //Fetching User Following users Data

        if ($this->data['membersFollowingMeList'] != "") {
            foreach ($this->data['membersFollowingMeList'] as $member) {
                //fetches User Data
                $usermemberData = $this->getUserMemberProfileData($member->id);
                array_push($UserDataList,$usermemberData);
            }
            $response = true;
        }
        else {
            $response = false;
        }
        $result = array("response" => $response, "data" => $UserDataList);
        echo json_encode($result);
    }

    function profileMemberFollowed()
    {
        //LOADS THE MEMBER FOLLOWING
        $this->load->model('userMemberModel');
        $response = false;
        $UserDataList = array();

        //GET THE PARAMETERS AND DATA NEEDED
        $limit = $this->input->post('limit') * USER_AJAX_FETCH_COUNT;
        $this->data['membersFollowedByMeList'] = $this->userMemberModel->getMembersFollowingMe($this->input->post('memberId'), $limit, USER_AJAX_FETCH_COUNT);

        //Fetching User Following users Data

        if ($this->data['membersFollowedByMeList'] != "") {
            foreach ($this->data['membersFollowedByMeList'] as $member) {
                //fetches User Data
                $usermemberData = $this->getUserMemberProfileData($member->id);
                array_push($UserDataList,$usermemberData);
            }
            $response = true;
        }
        else {
            $response = false;
        }
        $result = array("response" => $response, "data" => $UserDataList);
        echo json_encode($result);
    }


    function profileParticipatedArgument()
    { //LOADS ARGUMENTS PARTICIPATED BY A USERMEMBER
        $this->load->model('userMemberModel');
        $this->load->model('argumentModel');
        $this->load->model('followArgumentModel');
        $this->load->model('argumentVotesModel');

        $userMemberId = $this->input->post('memberId');

        $argumentParticipatedList = $this->argumentModel->getCommentedArgumentsbyUserMember($userMemberId);
        $display = "";
        $userMemberObj = "";

        if ($argumentParticipatedList != "") {
            foreach ($argumentParticipatedList as $argument) {
                $argument->userMember = $this->userMemberModel->getById($argument->memberId);
                $isLoggedInMemberArgumentOwner = ($this->loggedInUserMember->id == $argument->memberId) ? true : false;
                $argument->userMember = $this->userMemberModel->getById($argument->memberId);
                $argument->agreed = $argument->agreed;
                $argument->disagreed = $argument->disagreed;
                if ($isLoggedInMemberArgumentOwner) {
                    $argument->isFavorite = -1;
                } else {
                    $argument->isFavorite = $this->followArgumentModel->getByArgumentAndMemberId($this->loggedInUserMember->id, $argument->id);
                }

            }
        }
        $result = array("response" => 1, "data" => $argumentParticipatedList);
        echo json_encode($result);
    }

    function followMember()
    { //FOLLOW A USERMEMBER
        //GET DATABASE FILES
        $this->load->model('userMemberFollowedMemberModel');

        //GET THE PARAMETERS AND DATA NEEDED
        $memberId = $this->input->post('memberId');
        $followMemberId = $this->input->post('followMemberId');

        //INSERT THE RECORD INTO THE usermemberfollowedmember TABLE
        $result = null;
        if ($id = $this->userMemberFollowedMemberModel->followUserMember($memberId, $followMemberId)) {
            $result = array("response" => 1);
            $this->load->model('BaseModel');
            $this->BaseModel->notificationRequest(FOLLOW_MEMBER_NOTIFICATION,$id,$this->loggedInUserMember->id,null);
        } else {
            $result = array("response" => 0);
        }
        echo json_encode($result);
    }

    function unfollowMember()
    { //UNFOLLOW A USERMEMBER
        //GET DATABASE FILES
        $this->load->model('userMemberFollowedMemberModel');

        //GET THE PARAMETERS AND DATA NEEDED
        $memberId = $this->input->post('memberId');
        $followMemberId = $this->input->post('followMemberId');

        //DELETE THE RECORD FROM usermemberfollowedmember TABLE
        $result = $this->userMemberFollowedMemberModel->unfolloweUserMember($memberId, $followMemberId);
        if ($result) {
            $result = array("response" => 1);
        } else {
            $result = array("response" => 0);
        }
        echo json_encode($result);
    }

    /**
     * returns all notifications data of loggedinusermember
     * serves through ajax call
     *
     * @param null
     * @return String       notifications data - json string
     */
    function memberNotifications()
    {
        $this->load->model('BaseModel');
        $this->load->model('argumentCommentModel');
        $memberId = $this->loggedInUserMember->id;
    	$notifications = $this->BaseModel->loadOnlineNotificationQueue($memberId); //fetch online notifications of loggedInMember
        if ($notifications) {
            foreach ($notifications as $notification) { //notification to process according to type
                if($notification->type == REPLY_TO_COMMENT_OWNER_NOTIFICATION || $notification->type == REPLY_TO_ARGUMENT_OWNER_NOTICTION){
                    $argumentAndCommentData = $this->argumentCommentModel->getArgumentAndCommentByReplyId($notification->recordId);
                    $notification->argumentId = $argumentAndCommentData->argumentId;
                    $notification->argumentTitle = $argumentAndCommentData->argumentTitle;
                    $notification->commentId = $argumentAndCommentData->commentId;
                    $notification->commentText = $argumentAndCommentData->comment;
                }
            }
	        $response = true;	
        } else {
        	$response = false;
        }
        $result = array("response" => $response, "data" => $notifications);
        echo json_encode($result);
    }


    function facebookFeed()
    {
        set_time_limit(0);
        $config['appId'] = $this->config->item('fbApiId');
        $config['secret'] = $this->config->item('fbSecret');
        $this->load->library('facebook', $config);
        $facebook = $this->facebook;

        $user = $facebook->getUser();
        //print_r($user);
        //echo $user;	$user != null &&
        if ($this->input->cookie('id') && $this->input->cookie('oauth_id') && $this->input->cookie('oauth_provider') && $this->input->cookie('oauth_provider') == 'facebook') {
            try {
                // Proceed knowing you have a logged in user who's authenticated.
                $newsfeeds = $facebook->api('/me/HOME?limit=50');
                $wallfeeds = $facebook->api('/me/links?limit=10');
                $newsfeedsCount = sizeof($newsfeeds['data']);
                $wallfeedsCount = sizeof($wallfeeds['data']);
                if ($newsfeedsCount == 0 || $wallfeedsCount == 0) {
                    $result = array("response" => "You don't have any activities on your facebook to retrive.");
                    echo json_encode($result);
                } else {
                    $feedsHTML = "<div id='mcs_container'><div class='customScrollBox'><div class='container'><div class='content'><ul style='float:left;padding:0;'>";
                    for ($i = 0, $x = 0; $i <= $newsfeedsCount; $i++) {
                        if (array_key_exists('message', $newsfeeds['data'][$i])) {
                            $feedUser = $newsfeeds['data'][$i]['from']['name'];
                            //$feedUserObj =  $facebook->api($newsfeeds['data'][$i]['from']['id']);
                            $feedUserImg = "http://graph.facebook.com/" . $newsfeeds['data'][$i]['from']['id'] . "/picture";
                            $feedContent = $newsfeeds['data'][$i]['message'];
                            $feedTime = $newsfeeds['data'][$i]['created_time'];
                            $feedsHTML .= "<li class='feedItem' oauth_id='" . $this->input->cookie('oauth_id') . "' memberid='" . $this->input->cookie('id') . "' feedId='" . $newsfeeds['data'][$i]['id'] . "'>";
                            $feedsHTML .= '<div class="sectionTopicsHeader">
									<img src="' . $feedUserImg . '" alt="facebook user" style="width: 35px; height: 35px; display: inline; float: left; margin: 0pt 10px 0pt 0pt;">
									<span class="feedUser" >' . $feedUser . '</span>
									<span class="feedTime" >' . $feedTime . '</span>
									<span class="feedContent" > ' . $feedContent . '</span><br/>
									<input type="hidden" name="source" class="argumentSource" value="facebook.com" />
									<span class="argueLink">Start Argument</span>
									</div></li>';
                            $x++;
                        }
                        if ($x == 10) {
                            break 1;
                        }
                        /*if($x=0&&$i=100){
                                          //first 100 feeds dont have text-only feeds.load next 100.
                                          //$newsfeeds = facebook->api(substr($newsfeeds['paging']['next'],26));
                                          $newsfeedsCount = sizeof($newsfeeds['data']);
                                          $i=0;
                                      }*/
                    }
                    for ($i = 0, $x = 0; $i < $wallfeedsCount; $i++) {
                        if (array_key_exists('message', $wallfeeds['data'][$i])) {
                            $feedUser = $wallfeeds['data'][$i]['from']['name'];
                            $feedContent = $wallfeeds['data'][$i]['description'];
                            $feedTime = $wallfeeds['data'][$i]['created_time'];
                            $feedsHTML .= "<li class='feedItem' oauth_id='" . $this->input->cookie('oauth_id') . "' memberid='" . ('id') . "' feedId='" . $wallfeeds['data'][$i]['id'] . "'>";
                            $feedsHTML .= '<div class="sectionTopicsHeader">
									<img src="http://graph.facebook.com/' . $this->input->cookie('oauth_id') . '/picture" alt="Facebook User" style="width: 35px; height: 35px; display: inline; float: left; margin: 0pt 10px 0pt 0pt;">
									<span class="feedUser" >' . $feedUser . '</span>
									<span class="feedContent"> ' . $feedContent . '</span><br/>
									<span class="feedTime" >' . $feedTime . '</span>
									<input type="hidden" name="source" class="argumentSource" value="facebook.com" />
									<span class="argueLink" style="float:right;cursor:pointer;display:none;">argue</span>
									</div></li>';
                            $x++;
                        }
                        if ($x == 10) {
                            break 1;
                        }
                        /*if($x=0&&$i=10){
                                          //first 10 feeds dont have text-only feeds.load next 10.
                                          //$newsfeeds = facebook->api(substr($newsfeeds['paging']['next'],26));
                                          $newsfeedsCount = sizeof($wallfeeds['data']);
                                          $i=0;
                                      }*/
                    }
                    $feedsHTML .= "</ul></div></div><div class='dragger_container'><div class='dragger'></div></div></div></div>";
                    //error_log('Feed HTML:' . $feedsHTML, 3, 'debug.log');
                    $result = array("response" => $feedsHTML);
                    echo json_encode($result);
                }
            } catch (FacebookApiException $e) {
                //error_log($e);
                $user = null;
            }
        }
        else {
            /*$result = array("response" => "<a href='/login-facebook.php'><img src='/images/login-facebook.jpg' width='154' height='22' alt='Login with Facebook' style=\"margin: 140px 0pt 0pt 95px;\"/></a>");
                    echo json_encode($result);*/
            $FB_LOGIN_URL = '"/login-facebook.php"';
            $FB_PARAM = '"Facebook","menubar=no,width=930,height=560,toolbar=no"';
            $FB_LOGIN_IMG = '"/images/login-facebook.jpg"';
            $FB_LOGIN = "<img src='/images/login-facebook.jpg' onClick='window.open(" . $FB_LOGIN_URL . ", " . $FB_PARAM . ");' value='Login to Facebook' alt='Login to Facebook' style=\"margin: 40px 0pt 40px 40px;\">";
            $result = array("response" => $FB_LOGIN);
            echo json_encode($result);

        }
    }

    function memberRecommend()
    {
        $memberId = $this->input->post('memberId');
        $this->load->model('userMemberModel');
        $userMemberObj = $this->userMemberModel;
        $userMember = $userMemberObj->getById($memberId);
        $this->load->model('userMemberFollowedMemberModel');
        $followMemberObj = $this->userMemberFollowedMemberModel;
        $userType = $userMember->oauth_provider;
        $recommendedMemberListHtml = "";
        $recommendedMemberList = null;
        $friendIdArray = array();
        $friendIdList = "";
        if ($userType == 'facebook') {
            $config['appId'] = $this->config->item('fbApiId');
            $config['secret'] = $this->config->item('fbSecret');
            //load Facebook php-sdk library with $config[] options
            $this->load->library('facebook', $config);
            $client = $this->facebook;
            $user = $client->getUser();
            if ($user) {
                try {
                    $user_profile = $client->api('/me');
                    $fql = "SELECT uid, name, pic_square, online_presence FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = $user)";
                    $result = $client->api(array('method' => 'fql.query', 'query' => $fql));
                    foreach ($result as $fbFriend) {
                        array_push($friendIdArray, $fbFriend['uid']);
                    }
                    $friendIdList = implode(",", $friendIdArray);
                    $recommendedMemberList = $userMemberObj->getUsersNotFollowingFromIdList($friendIdList, $memberId);
                } catch (FacebookApiException $e) {
                    $user = null;
                }
            } else {

            }
        }
        $memberListSize = sizeof($recommendedMemberList);
        if ($memberListSize < 3 && $memberListSize > 0) {
            $recommedationFromFollowList = $followMemberObj->getFollowingOfFollowingByAjax($memberId);
            if (is_array($recommedationFromFollowList)) {
                $recommendedMemberList = array_push($recommendedMemberList, $followMemberObj->getFollowingOfFollowingByAjax($memberId));
            }
        } else {
            $recommendedMemberList = $followMemberObj->getFollowingOfFollowingByAjax($memberId);
        }

        if (is_array($recommendedMemberList)) {
            $recommendedMemberList = array_merge($recommendedMemberList, $userMemberObj->getTopUsersByTopicByAjax($userMember->interest, $memberId));
        } else {
            $recommendedMemberList = $userMemberObj->getTopUsersByTopicByAjax($userMember->interest, $memberId);
        }

        $result = array("data" => $recommendedMemberList);
        echo json_encode($result);
    }

    function postCommentVote(){
        $this->load->model('argumentVotesModel');
        $this->load->model('userMemberVotesModel');
        $this->load->model('argumentCommentModel');
        $this->load->model('argumentModel');
        //Check wether user just vote or vote with comment
        $commentText = $this->input->post('commenttext');
        if (!empty($commentText)) {
            $isCommented = true;
        } else {
            $isCommented = false;
        }
        //prepare data to post to argumentcomment table and uservote table and argumentvotes table
        $vote = $this->input->post('vote');
        $argumentId = $this->input->post('argumentId');
        $argument = $this->argumentModel->getById($argumentId);
        $memberId = $this->input->post('memberId');
        $parentId = $this->input->post('parentId');
        $parentId = (!empty($parentId)) ? $parentId : null;
        $postedMemberData = $this->getUserMemberData($memberId);
        $postedComment = null;
        $commentTableData = null;
        $isReplyVote = false;

        if($argument->status==1){           //prevent action on locked argument at applicationlevel

        //usermembervotes table data
        $userMemberVotesTableData['argumentId'] = $argumentId;
        $userMemberVotesTableData['memberId'] = $memberId;
        //change user vote as if user already voted ot not.
        $checkVoted = $this->userMemberVotesModel->checkVotedByArgument($userMemberVotesTableData);
        if ($this->loggedInUserMember->id == $argument->memberId || $vote == REPLY_ID) {    //if user already voted or vote is reply skip recording user vote
            $checkVoted = true;
        }
        if ($checkVoted) {
            if ($vote == AGREE_VOTE_ID) {
                $vote = AGREE_COMMENT_ID;
            } else if ($vote == DISAGREE_VOTE_ID) {
                $vote = DISAGREE_COMMENT_ID;
            } else {
                $isReplyVote = true;
                $vote = REPLY_ID;
            }
        }
        $userMemberVotesTableData['vote'] = $vote;

        //argumentvotes table data
        if (!$checkVoted) {
            $argumentVotesTableData['argumentId'] = $argumentId;
            $argumentVotesTableData['vote'] = $vote;
            $argumentVotesTableData['gender'] = $postedMemberData->gender;
        }

        //argumentcomment table data
        if ($isCommented) {
            $commentTableData['commenttext'] = $this->input->post('commenttext');
            $commentTableData['memberId'] = $memberId;
            $commentTableData['argumentId'] = $argumentId;
            $commentTableData['parentId'] = $parentId;
            $commentTableData['uservote'] = $vote;
        }

        $case = (!$checkVoted) ? (($isCommented) ? "commentvote" : "vote") : "comment";


        //Add comment, add or update argument vote and add record in usermember vote table
        $this->load->model('BaseModel');
        switch ($case) {
            case "vote":
                if (($argumentVoteId=$this->argumentVotesModel->addVote($argumentVotesTableData)) && ($userVoteId = $this->userMemberVotesModel->add($userMemberVotesTableData))) {
                    $postedComment->voted = true;
                    $this->BaseModel->notificationRequest(intval($vote),$userVoteId,$this->loggedInUserMember->id,null);
                } else {
                    $postedComment->voted = false;
                }
                $postedComment->commented = false;
                $result = array("response" => 1, "data" => $postedComment);
                break;

            case "commentvote":
                if (($argumentVoteId = $this->argumentVotesModel->addVote($argumentVotesTableData)) && ($comment = $this->argumentCommentModel->create($commentTableData)) && ($userVoteId = $this->userMemberVotesModel->add($userMemberVotesTableData,$comment['id']))) {
                    $postedComment->voted = true;
                    $postedComment->commented = true;
                    $this->BaseModel->notificationRequest(intval($vote),$userVoteId,$this->loggedInUserMember->id,null);
                    $this->BaseModel->notificationRequest(COMMENT_NOTIFICATION,$comment->id,$this->loggedInUserMember->id,null);
                    $postedComment->comment = $comment;
                } else {
                    $postedComment->voted = false;
                    $postedComment->commented = false;
                }
                $result = array("response" => 1, "data" => $postedComment);
                break;

            case "comment":
                if (($comment = $this->argumentCommentModel->create($commentTableData))) {
                    $postedComment->commented = true;
                    $postedComment->comment = $comment;
                    $this->BaseModel->notificationRequest(($isReplyVote)?REPLY_NOTIFICATION:COMMENT_NOTIFICATION,$comment['id'],$this->loggedInUserMember->id,null);
                    //$this->BaseModel->notificationRequest(COMMENT_NOTIFICATION,$comment['id'],$this->loggedInUserMember->id,null);

                } else {
                    $postedComment->commented = false;
                }
                $postedComment->voted = false;

                $result = array("response" => 1, "data" => $postedComment);
                break;
        }
        }else{
            $result = array("response" => 0, "data" => false);
        }
        echo json_encode($result);
    }

    function updateCommentReply(){
        $this->load->model('argumentCommentModel');
        $data = array("commenttext"=>$this->input->post("commenttext",true),"id"=>$this->input->post('commentId',true),"memberId"=>$this->input->cookie('id'));
        $postedComment = $this->argumentCommentModel->update($data);
        $response = array("response"=>true,"data"=>$postedComment);
        echo json_encode($response);
    }

    function loadVotedPeople(){
        $this->load->model('userMemberVotesModel');
        $this->load->model('argumentCommentModel');
        $this->load->model('argumentModel');

        $argumentId = $this->input->post('argumentId');
        $gender = $this->input->post('gender');
        $vote = $this->input->post('vote');
        $data = array();

        if(empty($gender) && $vote!=''){
            $data = $this->userMemberVotesModel->getVotedUsersByVoteAndArgumentId($argumentId,$vote);
        }else if(empty($gender) && $vote==''){
            $data = $this->userMemberVotesModel->getAllVotedUsersByArgumentId($argumentId);
        }else if(!empty($gender) && $vote!=''){
            $data = $this->userMemberVotesModel->getVotedUsersByGenderAndVoteAndArgumentId($argumentId,$gender,$vote);
        }
        $result = array();
        foreach($data as $record){
            $temp = $this->getUserMemberData($record['memberId']);
            $temp->vote = $record['userVote'];
            $temp->votedTime = $record['votedTime'];
            $temp->commentId = $record['commentId'];
            array_push($result,$temp);
        }
        $result = array("response"=>1,"data"=>$result);
        echo json_encode($result);
    }

    function loadFollowersAndFollowingPeople(){
        $this->load->model('userMemberFollowedMemberModel');
        $users = $this->userMemberFollowedMemberModel->getFollowedAndFollowersByMemberId($this->loggedInUserMember->id);
        $result = array();
        $response = 0;
        foreach($users as $user){
            array_push($result,$this->getUserMemberProfileData($user->memberId));
            $response = 1;
        }
        $result = array("response"=>$response,"data"=>$result);
        echo json_encode($result);
    }

    function sendArgumentInviteToUsers(){
        $this->load->model('BaseModel');
        $users = $this->input->post('suggest');
        $argumentId = $this->input->post('argumentId');
        $status = 0;
        foreach($users as $user){
            if($this->BaseModel->notificationRequest(8,$argumentId,$this->loggedInUserMember->id,$user)){
                $status = 1;
            }else{
                $status = 0;
            }
        }
        $result = array("response"=>$status);
        echo json_encode($result);
    }

    //Home page Arguments Feed
    function feed()
    {
        $this->load->model('argumentModel');
        $this->load->model('userMemberModel');
        $this->load->model('followArgumentModel');
        $this->load->model('userMemberVotesModel');
        $isNewMember = false;
        //load arguments related to user
        $limit = $this->input->post('limit') * ARGUMENT_AJAX_FETCH_COUNT;
        //$argumentList = $this->argumentModel->getByNumber($limit, ARGUMENT_AJAX_FETCH_COUNT);
        $argumentList = $this->argumentModel->getTimelineByActivity($this->loggedInUserMember->id, $limit, ARGUMENT_AJAX_FETCH_COUNT);
        if (!$argumentList && !$limit) {
            $argumentList = $this->argumentModel->getByNumber(0,24);//By default loading 24 argument for a new User
            $argumentList = $this->setUserMemberToArgument($argumentList);
            $isNewMember = true;
            $response = true;
        } elseif ($argumentList) {
            $argumentList = $this->setUserMemberToArgument($argumentList);
            $isNewMember = false;
            $response = true;
        } else {
            $response = false;
            $argumentList = null;
            $isNewMember = false;
        }
        $result = array("response" => $response, "data" => $argumentList, "isNewMember" => $isNewMember);
        echo json_encode($result);
    }

    function setUserMemberToArgument($argumentList)
    {
        $this->load->model('userMemberModel');
        $this->load->model('userMemberVotesModel');
        $this->load->model('followArgumentModel');
        //	map user objects to data object
        foreach ($argumentList as $argument) {
            $isLoggedInMemberArgumentOwner = ($this->loggedInUserMember->id == $argument->memberId) ? true : false;
            //	print_r($argument);
            $argument->userMember = $this->getUserMemberData($argument->memberId);
            //	print_r($argument->userMember);
            //prepare votebar(votes, percentages, winning and loosing classes for styling)
            $agreed = $argument->agreed;
            $disagreed = $argument->disagreed;
            $totalVote = $agreed + $disagreed;
            $agreedPercentage = ($totalVote == 0) ? 0 : round(($agreed / $totalVote) * 100);
            $disagreedPercentage = ($totalVote == 0) ? 0 : round(($disagreed / $totalVote) * 100);
            $disagreeBarWidth = ($totalVote == 0) ? 0 : ($disagreed / $totalVote) * 130;
            $startVoteClass = ($totalVote == 0) ? "startVote" : "";
            $hideVoteHolderClass = ($totalVote == 0) ? "hideVoteHolder" : "";
            if ($argument->agreed > $argument->disagreed) {
                $statusClass = "winning";
            } elseif ($argument->agreed < $argument->disagreed) {
                $statusClass = "loosing";
            } else {
                $statusClass = "tie";
            }
            if ($this->loggedInUserMember->id == $argument->userMember->id) {
                $argument->isLoggedInMemberOwner = true;
            } else {
                $argument->isLoggedInMemberOwner = false;
            }
            $argument->isLoggedInUserMemberVoted = $this->userMemberVotesModel->checkVotedByArgument(array("argumentId" => $argument->id, "memberId" => $this->loggedInUserMember->id));
            //$argument->createdtime = $this->time_difference($argument->createdtime);
            $argument->totalVote = $totalVote;
            $argument->agreedPercentage = $agreedPercentage;
            $argument->disagreedPercentage = $disagreedPercentage;
            $argument->disagreeBarWidth = $disagreeBarWidth;
            $argument->startVoteClass = $startVoteClass;
            $argument->hideVoteHolderClass = $hideVoteHolderClass;
            $argument->statusClass = $statusClass;
            if ($isLoggedInMemberArgumentOwner) {
                $argument->isFavorite = -1;
            } else {
                $argument->isFavorite = $this->followArgumentModel->getByArgumentAndMemberId($this->loggedInUserMember->id, $argument->id);
            }

        }
        return $argumentList;
    }

    //Home page Interested Arguments
    function interest()
    {
        $this->load->model('argumentModel');
        $this->load->model('userMemberModel');
        $this->load->model('followArgumentModel');
        //load arguments related to user
        $limit = $this->input->post('limit') * ARGUMENT_AJAX_FETCH_COUNT;
        $argumentList = $this->argumentModel->getMemberInterestedArguments($this->loggedInUserMember->id, $limit);
        //$argumentList = $this->argumentModel->getTimelineByActivity($this->loggedInUserMember->id);
        if ($argumentList) {
            //map user objects to data object
            $argumentList = $this->setUserMemberToArgument($argumentList);
            $response = true;
        } else {
            $response = false;
            $argumentList == null;
        }
        $result = array("response" => $response, "data" => $argumentList);
        echo json_encode($result);
    }

//Home page Popular Arguments
    function popular()
    {
        $this->load->model('argumentModel');
        $this->load->model('userMemberModel');
        $this->load->model('followArgumentModel');
        //load arguments related to user
        //$this->data['argumentList'] = $this->argumentModel->getByNumber(0, 6);
        $argumentList = $this->argumentModel->getMostPopular();

        //map user objects to data object
        $argumentList = $this->setUserMemberToArgument($argumentList);
        $result = array("response" => true, "data" => $argumentList);
        echo json_encode($result);
    }
    
	//Home page Interested Arguments
    function topicArguments()
    {
        $this->load->model('argumentModel');
        $this->load->model('userMemberModel');
        $this->load->model('followArgumentModel');
        //load arguments related to user
        $limit = $this->input->post('limit') * ARGUMENT_AJAX_FETCH_COUNT;
        $topicId = $this->input->post('topic');
        $argumentList = null;
        if($topicId == 1){          //to fetch all arguments
            $argumentList = $this->argumentModel->getByNumber($limit,ARGUMENT_AJAX_FETCH_COUNT);
        }else{
            $argumentList = $this->argumentModel->getByTopic($topicId, $limit, $this->loggedInUserMember->id);
        }

        if ($argumentList) {
            //map user objects to data object
            $argumentList = $this->setUserMemberToArgument($argumentList);
            $response = true;
        } else {
            $response = false;
            $argumentList == null;
        }
        $result = array("response" => $response, "data" => $argumentList);
        echo json_encode($result);
    }

    function getUserProfile()
    { //fetches User Data to use in tooltip etc
        $userMemberData = $this->getUserMemberData($this->input->post('id'));
        $this->load->model('userMemberFollowedMemberModel');
        $loggedInMemberId = $this->loggedInUserMember->id;
        $userMemberData->isFollowing = $this->userMemberFollowedMemberModel->checkFollowByMemberId($this->loggedInUserMember->id, $this->input->post('id'));
        $result = array("response" => true, "data" => $userMemberData);

        echo json_encode($result);
    }

    function getReplies()
    {
        $this->load->model('argumentCommentModel');
        $commentId = $this->input->post('commentId');

        $replies = $this->argumentCommentModel->getReplysByCommetnId($commentId);
        foreach ($replies as $reply){
        	$reply->userImage = $this->getThumbPath($reply->userImage);
        }
        $result = array("respose" => 1, "data" => $replies);

        echo json_encode($result);
    }

    function lockArgument()
    {
        $this->load->model('argumentModel');
        $this->load->model('UserMemberModel');

        $argumentId = $this->input->post('argumentId');
        $memberId = $this->input->post('memberId');

        $argument = $this->argumentModel->getById($argumentId);
        if ($argument->memberId == $memberId) {
            if ($this->UserMemberModel->argumentLock($argumentId, $memberId)) {
                $isLocked = abs(1-$argument->status);
                $resp = true;
            } else {
                $isLocked = false;
                $resp = true;
            }
        } else {
            $isLocked = false;
            $resp = false;
        }
        $result = array("response" => $resp, "data" => $isLocked);
        echo json_encode($result);
    }

    /*function clean(){
        //$inputText = htmlspecialchars($this->input->post('text'),ENT_QUOTES,UTF-8 );
        $this->load->helper('string');
        $inputText = strip_quotes(quotes_to_entities(reduce_double_slashes(htmlspecialchars($this->input->post('text'),ENT_QUOTES,UTF-8 ))));
        echo json_encode(array("text"=>$inputText));
    }*/

    public function fbPostBack()
    {
        /*try {
            //Enter your Application Id and Application Secret keys
            $config['appId'] = FB_API_ID;
            $config['secret'] = FB_SECRET;

            //Do you want cookies enabled?
            $config['cookie'] = true;

            //load Facebook php-sdk library with $config[] options
            $this->load->library('facebook', $config);

            $attachment = array(
                'message' => 'http://betav4.disagree.me/index.php/detail?id=1324905542',
                'name' => 'Cricket Fever',
                //'caption' => "Caption of the Post",
                //'link' => 'http://beta.disagree.me',
                'description' => 'Only a leg spinner will do.',
                'picture' => 'http://betav4.disagree.me/images/disagree-logo.png',
                'actions' => array(
                    array(
                        'name' => 'Username',
                        'link' => 'http://beta.disagree.me/'
                    )
                )
            );
            $result = $this->facebook->api('/me/feed/', 'post', $attachment);
        } catch (FacebookApiException $e) {
            $result = $e->getMessage();
        }*/
        echo json_encode(array("response" => 1, "data" => true));
    }



    public function getComments()
    {
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

    function memberOffline(){
        $memberId = $this->input->post('memberId');
        $this->load->model('userMemberModel');
        if($this->userMemberModel->setUserOffline($memberId)){
            $result = array("response" => 1);
        }else {
            $result = array("response" => 0);
        }
        echo json_encode($result);
    }

    function memberOnline(){
        $memberId = $this->input->post('memberId');
		$this->load->model('userMemberModel');
		$userMemberObj = $this->userMemberModel;
		$this->load->model('baseModel');
		$baseObj = $this->baseModel;
		if($userMemberObj->setUserOnline($memberId)){  //Set user online
			$notificationCount = $baseObj->getUnreadNotificationCount($memberId);  //get all the unread notification Count (New notifications + old unread notifications)
			if($notificationCount->count > 0){
				$result = array("response" => 1, "data" => $notificationCount->count);
			}else {
				$result = array("response" => 0);
			}
		}else {
			$result = array("response" => 0);
		}
		echo json_encode($result);
    }

    /**
     * set all notifications as read for loggdInUsermember
     * call by ajax
     *
     * @param null
     * @return boolen
     */
    function notificationRead(){
        $memberId = $this->loggedInUserMember->id;
        $this->load->model('baseModel');
        if($this->baseModel->setOnlineNotificationAsRead($memberId)){
            $result = array("response" => true);
        }else{
            $result = array("response" => false);
        }
        echo json_encode($result);
    }

    /**
     * returns no.of unread notifications of given member(loggedinmember)
     * serves through ajax call
     *
     * @param null
     * @return json count of unread notifications of loggedinmember
     *
     */
    function getNotification(){
        $memberId = $this->loggedInUserMember->id;
        $this->load->model('baseModel');
        $baseObj = $this->baseModel;
        $notificationCount = $baseObj->getUnreadNotificationCount($memberId);  //get all the unread notification Count (New notifications + old unread notifications)
        if($notificationCount->count > 0){
            $result = array("response" => 1, "data" => $notificationCount->count);
        }else {
            $result = array("response" => 0);
        }
        echo json_encode($result);
    }
    
    function hideArgument(){
    	$memberId = $this->input->post('memberId');
    	$argumentId = $this->input->post('argumentId');
    	$this->load->model('baseModel');
    	$data = array('argumentId' => $argumentId, 'memberId' => $memberId);
    	if($this->baseModel->hideArgument($data)){
    		$result = array("response" => true);
    	}else{
    		$result = array("response" => false);
    	}
    	echo json_encode($result);
    }
    
	function spam(){
    	$memberId = $this->input->post('memberId');
    	$type = $this->input->post('type');
    	$recordId = $this->input->post('recordId');
    	$this->load->model('baseModel');
    	$data = array("memberId" => $memberId, "type" => $type, "recordId" => $recordId);
    	if($this->baseModel->reportSpam($data)){
    		$result = array("response" => true);
    	}else{
    		$result = array("response" => false);
    	}
    	echo json_encode($result);
    }

    function updateProfile(){
        $res = 0;
        if (array_key_exists('profilepictureEdit', $_POST) && strcmp($this->input->post('profilepictureEdit'), $this->loggedInUserMember->profilephoto) != 0 && substr($this->input->post('profilepictureEdit'), 0, 26) != 'https://graph.facebook.com') {
            //user changed profile picture
            if ($this->processUserProfilePic($this->loggedInUserMember->id, $this->input->post('profilepictureEdit'), $this->loggedInUserMember->profilephoto)) {
                //profile picture updated successfully
            } else {
                //profile picture update fail
                $res = 7;
                header('Location: /profile?id=' . $this->loggedInUserMember->id . '&res=' . $res);
            }
        } else {
            //user didn't changed his profile picture
        }
        $fullname = $this->input->post('newuserfname');
        $location = $this->input->post('newulocaion');
        $dob = $this->input->post('newudob');
        $interests = $this->input->post('categories');
    	$interest = implode(",",$interests);
       
        $data = array("fullname" => $fullname, "location" => $location, "dob" => $dob, "memberid" => $this->loggedInUserMember->id, "interests" => $interest );
        $this->load->model('UserMemberModel');
        
        $res = $this->UserMemberModel->editUser($data);
        $pass = $this->input->post('newpasswd');
        $notification = $this->input->post('notificationReq') == '' ? 0 : 1;
        $this->UserMemberModel->chnageNotificationSettings($this->loggedInUserMember->id, $notification);
        if (!empty($pass)) {
            $this->UserMemberModel->changePassword($this->loggedInUserMember->id, md5($pass));
        }
        header('Location: /profile?id=' . $this->loggedInUserMember->id . '&res=' . $res);
    }

    function ajaxImgLoad()
    {
        $config['image_library'] = 'gd';
		$path = $_SERVER['DOCUMENT_ROOT']."/images/temporaryLocation/";
        $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg", 'JPG', 'JPEG', 'PNG', 'GIF', 'BMP');
        if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
        {
            $name = $_FILES['uimage']['name'];
            $size = $_FILES['uimage']['size'];
            if(strlen($name))
            {
                list($txt, $ext) = explode(".", $name);
                if(in_array($ext,$valid_formats))
                {
                    if($size<(1024*1024))
                    {
                        $actual_image_name = time().substr(str_replace(" ", "_", $txt), 5).".".$ext;
                        $tmp = $_FILES['uimage']['tmp_name'];
                        if(move_uploaded_file($tmp, $path.$actual_image_name))
                        {
                        	$image_location = $_SERVER['DOCUMENT_ROOT'].'/images/temporaryLocation/'.$actual_image_name;
                        	$image_size = getimagesize($image_location);
                        	$width = $image_size[0];
                        	$height = $image_size[1];
                            if($width>179 && $height > 0){
                                $scale = $width / $height;
                                if($scale > 1){
                                    $scale = 180 / $height;
                                }else{
                                    $scale = 180 / $width;
                                }
                                $this->imageResize($image_location, $width, $height, $scale);
                                //$res = '<img src="/images/temporaryLocation/'.$actual_image_name.'" alt="default user image" id="profilePic"/>';
								$res = $actual_image_name;
                                $res = array('responseText'=>true,"responseCode"=>'100',"data"=>$res);
                                echo json_encode($res);
								 exit;
                            }else{
                                $res = array('responseText'=>false,"responseCode"=>'101',"data"=>false);
                                echo json_encode($res);
                                exit;
                            }
                        }else{
                            $res = array('responseText'=>false,"responseCode"=>'102',"data"=>false);
                            echo json_encode($res);
                            exit;
                        }
                    }
                    else{
                        //echo "Image file size max 1 MB";
                        $res = array('responseText'=>false,"responseCode"=>'103',"data"=>false);
                        echo json_encode($res);
                        exit;
                    }
                }else
                    $res = array('responseText'=>false,"responseCode"=>'104',"data"=>false);
                    echo json_encode($res);
                    exit;
            }else{
                $res = array('responseText'=>false,"responseCode"=>'105',"data"=>false);
                echo json_encode($res);
                exit;
            }
            exit;
        }
    }
    
    public function cropSelection(){
    	
    	#find out what type of image this is
    	$image = $_SERVER['DOCUMENT_ROOT'].$this->input->post('image');
    	$thumb_image_name = $image;
    	$width = $this->input->post('w');
    	$height = $this->input->post('h');
    	$start_width = $this->input->post('x1');
    	$start_height = $this->input->post('y1');
    	$scale = 180/$width;
    	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		switch($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image); 
				break;
		    case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image); 
				break;
		    case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image); 
				break;
	  	}
	  	
	  	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
		switch($imageType) {
			case "image/gif":
		  		imagegif($newImage,$thumb_image_name); 
				break;
	      	case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
		  		imagejpeg($newImage,$thumb_image_name,90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$thumb_image_name);  
				break;
	    }
		chmod($thumb_image_name, 0777);
		return $thumb_image_name;
    }
    
    public function invite(){
    	$emailString = $this->input->post('email');
    	$message = '"'.$this->loggedInUserMember->username.' wants to hear your opinions on Disagree.me"<br/>Please click <a href="'.base_url().'" target="_blank">here</a><br/>';
    	//$message .= $this->input->post('message');
    	
    	/*$emailList = explode(";", $emailString);*/
    	if($this->sendEmail($emailString, WEBMASTER_EMAIL, 'Invitation From Disagree', $message, 'html')){
    		$response = true;
    	}else {
    		$response = false;
    	}
    	echo json_encode(array("response" => $response));
    }

    public function checkFbUserData(){
        $this->load->model('userMemberModel');
        
        $oauth_uids = $this->input->post('oauth_id',true);
        $oauth_uids = implode(',',$oauth_uids);
        $result = $this->userMemberModel->checkFBUserData($oauth_uids,$this->loggedInUserMember->id);
        $response = ($result)?true:false;
      	$result = array("response"=>$response,"data"=>$result);
        echo json_encode($result);
    }
    
    public function getUserMemberProfileData($id){
        $this->load->model('userMemberFollowedMemberModel');
        $userMemberData = $this->getUserMemberData($id);
        $userMemberData->isFollowing = $this->userMemberFollowedMemberModel->checkFollowByMemberId($this->loggedInUserMember->id, $id);
        return $userMemberData;
    }
    
    
    public function contactus(){
        $suggestText = $this->input->post('text');
        $from = $this->input->post('email');
        $fromname = $this->input->post('name');
        $subject= $this->input->post('Subject');
        $message = 'Hello Team,<br/><br/>';
        $message .= 'We got New Message from ' .$fromname. '. Here is the Envoloup.<br/><br/>';
        $message .= 'Email: '.$from.'<br/>';
        $message .= 'Name:'.$fromname.'<br/>';
        $message .= 'Subject:'.$subject.'<br/>';
        $message .= 'Message:'.$suggestText.'<br/>';
        $message .='<br/><br/>Thank You.';
        if($this->sendEmail(CONTACT_EMAIL,$from,CONTACT_US_SUBJECT,$message,'html')){
            $result = true;
        }else{
            $result = false;
            log_message('error','we missed to send an email regarding suggestion from user. logging data'.$fromnameLink.'-'.$message.'-'.$userpage,false);
        }
        $result = array('response'=>$result);
        echo json_encode($result);
    }

    public function getUserActivity(){
        $this->load->model('userMemberModel');
        $response = false;
        $memberId = $this->input->post('memberId');
        $start = $this->input->post('start') * ACTIVITY_AJAX_FETCH_COUNT;
        if($data = $this->userMemberModel->getUserActivityByMemberId($memberId,$start,ACTIVITY_AJAX_FETCH_COUNT)){
            $response = true;
        }else{
            $response = false;
        }
        $result = array("response"=>$response,"data"=>$data);
        echo json_encode($result);
    }

    public function getUserSuggestion(){
        $suggestText = $this->input->post('suggestText');
        $from = $this->loggedInUserMember->email!=null?$this->loggedInUserMember->email:WEBMASTER_EMAIL;
        $fromname = $this->loggedInUserMember->email!=null?$this->loggedInUserMember->username:'Anonymous User';
        $fromnameLink = $this->loggedInUserMember->email!=null?'<a href="'.base_url().'profile?id='.$this->loggedInUserMember->id.'">'.$fromname.'</a>':$fromname;
        $to = FEEDBACK_EMAIL;
        $userpage =$_SERVER['HTTP_REFERER'];
        $message = 'Hello Team,<br/><br/>';
        $message .= 'We got New Suggestion from ' .$fromnameLink. '. Here is the Envoloup.<br/>';
        $message .= 'User:'.$fromnameLink.'<br/>';
        $message .= 'Suggestion Page:'.$userpage.'<br/>';
        $message .= 'Suggestion By User:'.$suggestText.'<br/>';
        $message .='<br/><br/>Thank You.';
        if($this->sendEmail($to,$from,SUGGEST_FEEDBACK_SUBJECT,$message,'html')){
            $result = true;
        }else{
            $result = false;
            log_message('error','we missed to send an email regarding suggestion from user. logging data'.$fromnameLink.'-'.$message.'-'.$userpage,false);
        }
        $result = array('response'=>$result);
        echo json_encode($result);
    }
    
    
  
    public function saveInvitedUser() {
    	$this->load->model('BaseModel');
        $response = false;
        $data = array();
        $data['memberId'] = $this->input->post('memberId');       	
     	$data['invitationtype'] = $this->input->post('invitationType');
      	if($data['invitationtype'] == 'fb') {
      		$data['fbid'] = $this->input->post('fbId');
       		$data['name'] = $this->input->post('name');
      		if($data = $this->BaseModel->saveFBInvitedUser($data)){
            	$response = true;
        	}else{
            	$response = false;
        	}
      	} else {
      		$data['email'] = explode(',',$this->input->post('email'));
      		//$data['email'] = $this->input->post('email');
      		if($data = $this->BaseModel->saveEmailInvitedUser($data)){
            	$response = true;
        	}else{
            	$response = false;
        	}
      	}

        $result = array("response"=>$response,"data"=>$data);
        echo json_encode($result);
    }
    
    
    
    
    
    
    
}