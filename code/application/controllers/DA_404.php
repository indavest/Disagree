<?php
	class DA_404 extends DA_Controller {
		
		private $data = array();
		
		public function __construct(){
			parent::__construct();
		}
		
		public function index(){
			$this->output->set_status_header('404');
			$this->data['loggedInUserMember'] = $this->getUserMemberData($this->input->cookie('id'));
			$this->data['loggedInUserMemberFlag'] = $this->input->cookie('isLoggedIn');
			$this->data['contentview'] = 'error/error_404';
			$this->load->view('includes/template',$this->data);
		}
	}