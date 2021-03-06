<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends DA_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	
	private $data = array();
	
	public function __construct(){
		parent::__construct();
		
		/*$this->data['cssList'] = array("static");*/
		$this->data['jsList'] = array();
		$this->data['loggedInUserMember'] = $this->getUserMemberData($this->input->cookie('id'));
		$this->data['loggedInUserMemberFlag'] = $this->input->cookie('isLoggedIn');
		$this->data['topicList'] = $this->topicList;
	}
	
	public function index()
	{
		$this->data['contentview'] = 'welcome';
		$this->load->view('includes/template', $this->data);
	}
	
	public function home(){
		$this->load->model('argument');
		$this->load->model('userMember');
		$data['argument'] = $this->argument->getByNumber(0,10);
		foreach ($data['argument'] as $argument){
			$argument->member = $this->userMember->getById($argument->memberId);
		}
		$data['contentview'] = 'home';
		$this->load->view('includes/template', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */