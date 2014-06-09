<?php 
if($loggedInUserMemberFlag){
	$this->load->view('includes/header'); 
} else {
	$this->load->view('includes/headeroff');
} 
$this->load->view($contentview);
if($loggedInUserMemberFlag){
	$this->load->view('includes/footer');	
}else {
	$this->load->view('includes/footeroff');
}
