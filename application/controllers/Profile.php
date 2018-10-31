<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {
	
	/**
	* PHP Function index, default function for our profile`s section.
	* it loads the users`s profile, provided the user is logged in and his account is active
	* @name: index
	**/
	public function index()
	{
		if($this->session->userdata('logged_in'))
		{
			$this->load->model("Users");
			$Users = new Users(); 
			
			$userData = $Users->getUser($this->session->userdata('email'));
			if($userData && (bool)$userData->is_active)
			{
				$this->load->model("Users");
				$Users = new Users();
				
				$extraScripts = array('<script src="'.$this->config->base_url().'js/Chart.bundle.js"></script>',
				'	<script src="'.$this->config->base_url().'js/utils.js"></script>',
				'	<script src="'.$this->config->base_url().'js/jquery-ui/jquery-ui.js"></script>',
				'	<link rel="stylesheet" href="'.$this->config->base_url().'js/jquery-ui/jquery-ui.css">',
				'	<script>var baseUrl="'.$this->config->base_url().'"; var actualTempsLbl="'.
				$this->lang->line("actual_temps").'"; var predictedTemps="'.$this->lang->line("forecast_temps").
				'"; var noData="'.$this->lang->line("no_data").'"; var yLabel="'.$this->lang->line("y_label").'"; var xLabel="'.$this->lang->line("x_label").'";</script>');
				
				$seoData["seoTitle"] = $this->lang->line("userprofile_Title");
				$seoData["seoDescription"] = $this->lang->line("userprofile_Description");
				$this->load->view("Header",array("seoData" => $seoData, "extraScripts" => $extraScripts));
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
	
	/**
	* PHP Function getUserProfileData, retrieves user`s profiles data (basic info + selected cities)
	* it can be accessed by admin (provided the userId is specified) or by user itself
	* @name: getUserProfileData
	**/
	function getUserProfileData()
	{
		if($this->session->userdata("logged_in") && (!$this->session->userdata("is_superuser") || ($this->session->userdata("is_superuser") && isset($_GET["userId"]) && intval($_GET["userId"])>0)))
		{
			$result = new stdClass();
			$result->success = false;
		
			if($this->session->userdata("is_superuser"))
				$userId = intval($_GET["userId"]);
			else
				$userId = $this->session->userdata("user_id");
			
			$this->load->model("Users");
			$this->load->model("Cities");
			$Users = new Users(); 
			$Cities = new Cities();
			
			$userData = $Users->getUser($userId);
			if($userData)
			{
				$result->success = true;
				$result->userData = $userData;
				$result->userCities = $Users->getUserCities($userId);
				$result->allCities = $Cities->getAllCities();
			}
			else
				$result->message = $this->lang->line("user_not_found");


			returnJSON($result);
		}
		else
			die();
	}
	
	/**
	* PHP Function updateUserProfile, updates user`s profiles cities
	* it can be accessed by admin (provided the userId is specified) or by user itself
	* @name: updateUserProfile
	**/
	function updateUserProfile()
	{
		if($this->session->userdata("logged_in") && (!$this->session->userdata("is_superuser") || ($this->session->userdata("is_superuser") && isset($_POST["userId"]) && intval($_POST["userId"])>0)))
		{
			$result = new stdClass();
			$result->success = false;
			
			if($this->session->userdata("is_superuser"))
				$userId = intval($_POST["userId"]);
			else
				$userId = $this->session->userdata("user_id");
		
			
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
			
			returnJSON($result);
		}
		else
			die();
	}
}
?>