<?php
class Bootstrap extends DA_Controller
{

    public $loggedInUserMember;

    function __construct()
    {
        parent::__construct();
        $this->loggedInUserMember = $this->getUserMemberData($this->input->cookie('id'));
    }

    function getComments(){
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

    function getTopicArrayData(){
        $this->load->model('topicModel');
        $data = $this->topicModel->getTopicArray();
        $result = array("response"=>true,"data"=>$data);
        echo json_encode($result);
    }
}