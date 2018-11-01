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
			$this->load->model("Cities");
			$Users = new Users(); 
			$Cities = new Cities();
			
			$seoData["seoTitle"] = $this->lang->line("userprofile_Title");
			$seoData["seoDescription"] = $this->lang->line("userprofile_Description");
			
			$extraScripts = array('<script src="'.$this->config->base_url().'js/Chart.bundle.js"></script>',
			'	<script src="'.$this->config->base_url().'js/utils.js"></script>',
			'	<script src="'.$this->config->base_url().'js/jquery-ui/jquery-ui.js"></script>',
			'	<link rel="stylesheet" href="'.$this->config->base_url().'js/jquery-ui/jquery-ui.css">',
			'	<script>var baseUrl="'.$this->config->base_url().'"; var actualTempsLbl="'.
			$this->lang->line("actual_temps").'"; var predictedTempsLbl="'.$this->lang->line("forecast_temps").
			'"; var noData="'.$this->lang->line("no_data").'"; var yLabel="'.$this->lang->line("y_label").'"; var xLabel="'.$this->lang->line("x_label").'";</script>');
			$this->load->view("Header", array("extraScripts" => $extraScripts, "seoData" => $seoData));
			
			$this->load->view("adminpanel/Adminpanel",array( "users" => $Users->getAllUsers(), "allCities" => $Cities->getAllCitiesWithWeatherData()));
			$this->load->view("Footer");	
		}
		else
			header('Location: ' . filter_var($this->config->base_url()."logout", FILTER_SANITIZE_URL));
	}
}
