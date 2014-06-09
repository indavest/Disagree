<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Indavest
 * Date: 6/25/12
 * Time: 11:52 AM
 * To change this template use File | Settings | File Templates.
 */
class editUser extends DA_Controller{
    public $loggedInUserMember;

    function __construct()
    {
        parent::__construct();
        $this->loggedInUserMember = $this->getUserMemberData($this->input->cookie('id'));
    }

    function index(){
        $this->data['jsList'] = array("jquery.form");
        $this->data['loggedInUserMember'] = $this->getUserMemberData($this->input->cookie('id'));// logged in memberObject
        $this->data['loggedInUserMemberFlag'] = $this->input->cookie('isLoggedIn'); //loggedin member flag
        $this->data['topicList'] = $this->topicList;                                            //topic id<>name
        $this->data['topicArgumentCount'] = $this->topicModel->getTopicArrayWithArgumentCount();//topic id<>argumentCount
        
        $interest = $this->data['loggedInUserMember']->interest;
		$interest = explode(",", $interest);
        /*if($this->data['loggedInUserMember']->oauth_provider == 'facebook'){
            $this->data['loggedInUserMember']->profilephoto .= "?type=large";
        }*/
		$this->data[interest] = $interest;
        $this->data['contentview'] = 'editProfile';
        $this->load->view('includes/template',$this->data);
    }
}
