<?php

class Profile extends DA_Controller {
	
	private $data = array();
	
	public function __construct(){
		parent::__construct();
		//$this->data['cssList'] = array("profile");
		$this->data['jsList'] = array("profile");
		$this->data['loggedInUserMember'] = $this->getUserMemberData($this->input->cookie('id'));
		$this->data['loggedInUserMemberFlag'] = $this->input->cookie('isLoggedIn');
		$this->data['topicList'] = $this->topicList;
		
	}
	
	public function index(){ 
		$this->load->model('userMemberFollowedMemberModel');
		$this->data['userMemberObject'] = $this->getUserMemberData($this->input->get('id'));
		if(empty($this->data['userMemberObject']->id)){
			$this->error_404();
			exit();
		}
		/*if($this->data['userMemberObject']->oauth_provider == 'facebook'){
			$this->data['userMemberObject']->profilephoto .= "?type=large";
		}*/
		if ($this->input->cookie('id') == $this->input->get('id')){
			$this->data['isLoggedInUserProfilePage'] = true;
		}else {
			$this->data['isLoggedInUserProfilePage'] = false;
		}
		$this->data['isFollowing'] =  $this->userMemberFollowedMemberModel->checkFollowByMemberId($this->input->cookie('id'),$this->input->get('id'));
		$this->data['followClass'] = ($this->data['isFollowing']) ? "unfollowMember" : "followMember";
		$this->data['followText'] = ($this->data['isFollowing']) ? "Unfollow" : "Follow";
		$this->data['imagetoggleFollow']  = ($this->data['followText']=="Follow") ? "tickIconW":"unfollowIconW";

        $this->data['res'] = $this->input->get('res');
		$this->data['contentview'] = 'profile';
		
		$this->load->view('/includes/template',$this->data);
		
	}
	
}