<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		include_once APPPATH . "libraries/google-api-php-client-2.2.2/vendor/autoload.php";
		
		if($this->checkOauthCredentials())
		{
			$this->load->view('welcome_message');
		}
		else
			die();
	}
	
	
	
	function checkOauthCredentials()
	{
		if($this->config->item("google_client_id") && $this->config->item("google_client_secret") && $this->config->item("google_redirect_uri") && $this->config->item("google_api_key")
			&& strlen($this->config->item("google_client_id"))>0 && strlen($this->config->item("google_client_secret"))>0 && strlen($this->config->item("google_redirect_uri"))>0
			&& strlen($this->config->item("google_api_key"))>0)
				return true;
		else	
		{
			echo "It appears your Google auth credentials in /config/config.php are not set. Make sure to visit https://developers.google.com/console and set them up.";
			return false;
		}
	}
}
