<?php 
if($loggedInAdminUserFlag){
	$this->load->view('includes/header');
	$this->load->view('includes/left'); 
} else {
	$this->load->view('includes/headeroff');
} 

$this->load->view($contentview);

$this->load->view('includes/footer');
