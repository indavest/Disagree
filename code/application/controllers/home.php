<?php

class Home extends DA_Controller {
	
	private $data = array();
	
	public function __construct(){
		parent::__construct();
		$this->data['jsList'] = array("home");
		$this->data['loggedInUserMember'] = $this->getUserMemberData($this->input->cookie('id'));
		$this->data['loggedInUserMemberFlag'] = $this->input->cookie('isLoggedIn');
		$this->data['topicList'] = $this->topicList;
	}
	
	public function index() { //load argument list related to logged in user
		$this->data['contentview'] = 'argumentList';
		$this->load->view('includes/template',$this->data);	
	}
	
}
