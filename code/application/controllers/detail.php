<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Indavest
 * Date: 5/15/12
 * Time: 12:32 PM
 * To change this template use File | Settings | File Templates.
 */
class Detail extends DA_Controller{
    private $data = array();

    public function __construct(){
        parent::__construct();
        /*load argumentcomments model*/
        $this->load->model('argumentcommentmodel');

        /*data which is global and accessible on detail page*/
        $this->data['csslist'] = array("base");                                     // css files to load
        $this->data['jsList'] = array("argumentDetail");                            // js files to load
        $this->data['loggedInUserMember'] = $this->getUserMemberData($this->input->cookie('id'));// logged in memberObject
        $this->data['loggedInUserMemberFlag'] = $this->input->cookie('isLoggedIn'); //loggedin member flag
        $this->data['topicList'] = $this->topicList;                                            //topic id<>name
        $this->data['topicArgumentCount'] = $this->topicModel->getTopicArrayWithArgumentCount();//topic id<>argumentCount
        $this->data['currentArgumentId'] = $this->input->get('id');
    }

    public function index(){
        $this->load->model('argumentModel');                                        // models(db access) required in this page
        $this->load->model('userMemberModel');
        $this->load->model('userMemberVotesModel');

        $this->data['argumentData'] = $this->argumentModel->getByMemberAndArgumentId($this->data['loggedInUserMember']->id ,$this->data['currentArgumentId']);  // current argument Object
        if(empty($this->data['argumentData']->id)){
        	$this->error_404();
        	exit;
        }
        $this->data['argumentOwner'] = $this->getUserMemberData($this->data['argumentData']->memberId);
        $this->data['isArgumentOwner'] = ($this->data['argumentOwner']->id == $this->data['loggedInUserMember']->id)?true:false;


        $this->data['argumentData']->followingUserCount = $this->argumentModel->getFollowingUserCountByArgumentId($this->data['currentArgumentId'])->followCount;
        $totalVotes = $this->data['argumentData']->agreed+$this->data['argumentData']->disagreed;
        if($totalVotes>0){
            $agreePercentage = round(($this->data['argumentData']->agreed/$totalVotes)*100);
            $this->data['totalAgreedPercentage'] = $this->DARoundPercentage($agreePercentage);
            $this->data['totalDisagreePercentage'] = 100-$this->data['totalAgreedPercentage'];
            $this->data['totalVotes'] = $totalVotes;
        }else{
            $this->data['totalAgreedPercentage'] = 0;
            $this->data['totalDisagreePercentage'] = 0;
            $this->data['totalVotes'] = 0;
        }

        $this->data['contentview'] = 'argumentDetail';
        $this->load->view('includes/template',$this->data);
    }


}
