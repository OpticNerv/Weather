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
				$this->load->view("userprofile/Userpanel",array("userCities" => $Users->getUserCities($this->session->userdata("user_id"))));
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
