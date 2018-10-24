<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	private $credentials_file = "";

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
	
	function getUserProfileData()
	{
		if($this->session->userdata("logged_in") && $this->session->userdata("is_superuser"))
		{
			$result = new stdClass();
			$result->success = false;
		
			if(isset($_GET["userId"]) && intval($_GET["userId"])>0)
			{
				$this->load->model("Users");
				$Users = new Users(); 
				
				$userId = intval($_GET["userId"]);
				$userData = $Users->getUser($userId);
				if($userData)
				{
					$result->success = true;
					$result->userData = $userData;
					$result->userCities = $Users->getUserCities($userId);
					$result->allCities = $Users->getAllCities();
				}
				else
					$result->message = $this->lang->line("user_not_found");
			}

			echo json_encode($result);
		}
		else
			die();
	}
	
	function updateUserProfile()
	{
		if($this->session->userdata("logged_in") && $this->session->userdata("is_superuser"))
		{
			$result = new stdClass();
			$result->success = false;
		
			if(isset($_POST["userId"]) && intval($_POST["userId"])>0)
			{
				$this->load->model("Users");
				$Users = new Users(); 
				
				$userId = intval($_POST["userId"]);
				$userData = $Users->getUser($userId);
				if($userData)
				{
					$Users->clearUserCities($userId);
					
					if(isset($_POST["selectedCities"]) && is_array($_POST["selectedCities"]) && count($_POST["selectedCities"])>0)
						$result->success = $Users->insertUserCities($userId,$_POST["selectedCities"]);
					else
						$result->success = true;
				}
				else
					$result->message = $this->lang->line("user_not_found");
			}

			echo json_encode($result);
		}
		else
			die();
	}
}
