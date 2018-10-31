<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	/**
	* PHP Function index, default function for our admin panel. 
	* It loads all users and lists them in order of registration date.
	* @name: index
	**/
	function index()
	{
		if($this->session->userdata('logged_in') && $this->session->userdata('is_superuser'))
		{
			$this->load->model("Users");
			$Users = new Users(); 
			
			$seoData["seoTitle"] = $this->lang->line("userprofile_Title");
			$seoData["seoDescription"] = $this->lang->line("userprofile_Description");
			$this->load->view("Header",$seoData);
			$data["users"] = $Users->getAllUsers();
			$this->load->view("adminpanel/Adminpanel",$data);
			$this->load->view("Footer");	
		}
		else
			header('Location: ' . filter_var($this->config->base_url()."logout", FILTER_SANITIZE_URL));
	}
}
