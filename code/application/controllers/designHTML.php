<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Indavest
 * Date: 5/17/12
 * Time: 12:12 PM
 * To change this template use File | Settings | File Templates.
 */
class designHTML extends DA_Controller
{

    private $data = array();

    public function __construct()
    {
        parent::__construct();
        $this->data['loggedInUserMember'] = $this->getUserMemberData($this->input->cookie('id'));
        $this->data['loggedInUserMemberFlag'] = $this->input->cookie('isLoggedIn');
        $this->data['topicList'] = $this->topicList;
    }

    public function index()
    {
        $this->data['contentview'] = 'HTMLTilePage';
        $this->load->view('includes/template', $this->data);
    }
}

?>