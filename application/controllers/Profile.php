<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {
	
	private $credentials_file = "";

	public function index()
	{
		if($this->session->userdata('logged_in'))
		{
			$this->load->model("Users");
			$Users = new Users(); 
			
			$userData = $Users->getUser($this->session->userdata('email'));
			if($userData && (bool)$userData->is_active)
			{
				echo "Welcome ".$this->session->userdata("name")."!";
				
				$seoData["seoTitle"] = $this->lang->line("userprofile_Title");
				$seoData["seoDescription"] = $this->lang->line("userprofile_Description");
				$this->load->view("Header",$seoData);
				$this->load->view("userprofile/Userpanel");
				$this->load->view("Footer");	
			}
			else
				header('Location: ' . filter_var($this->config->base_url()."logout", FILTER_SANITIZE_URL));
		}
		else
		{	
			$this->load->view("Header");
			$this->load->view("LoginForm",array("errorMessage" => $this->lang->line('unAuthorized_access')));
			$this->load->view("Footer");
		}
	}
}
