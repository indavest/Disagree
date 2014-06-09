<?php

class Login extends AD_Controller {
	
	public function index(){
		
	} 
	
	public function authenticate(){
		$userMember = array("email" => $this->input->post('admin_email'), "password" => $this->input->post('admin_password'));
		$this->load->model('userMemberModel');
		if($loggedInMember = $this->userMemberModel->authenticate($userMember)){
			$this->setUserCookie($loggedInMember);
			redirect('dashboard');	
		} else {
			$this->data['jsList'] =  array("login");
			$this->data['contentview'] = 'login';
			$this->data['login_failed'] = true;
			$this->load->view('includes/template', $this->data);
			exit();

		}
	}
	
	public function setUserCookie($loggedInMember){
		$expire = time() + 60 * 60 * 24 * 30;
		setcookie('admin_user', $loggedInMember->username, $expire, '/');
        setcookie('admin_id', $loggedInMember->id, $expire, '/');
        setcookie('isAdminLoggedIn', true, $expire, '/');
    }
    
    public function logout(){
        $expire = 3600;
		setcookie('admin_user', '', time() - $expire, '/');
        setcookie('admin_id', '', time() - $expire, '/');
        setcookie('isAdminLoggedIn', false, time() - $expire, '/');
        redirect('dashboard');
	}
}