<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	private $credentials_file = "";

	public function index()
	{
		if($this->session->userdata('logged_in'))
			header('Location: ' . filter_var($this->config->base_url()."profile", FILTER_SANITIZE_URL));
		else
		{	
			$this->load->view("Header");
			$this->load->view("LoginForm");
			$this->load->view("Footer");
		}
	}
	
	function google_login()
	{
		if($this->session->userdata('logged_in'))
			header('Location: ' . filter_var($this->config->base_url()."profile", FILTER_SANITIZE_URL));
		else if($this->checkOauthCredentials() && file_exists($this->credentials_file))
		{
		  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/weather/google_auth';
		  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		}
		else
			die();
		
	}
	
	function checkOauthCredentials()
	{
		if(is_array(glob("credentials/client_secret*")) && count(glob("credentials/client_secret*"))==1)
		{
			$this->credentials_file = glob("credentials/client_secret*")[0];
			return true;
		}
		else	
		{
			echo "Your Google Oauth client secret json file is either missing or there is more than one.. Make sure to visit https://developers.google.com/console, set up your credentials and store them in credentials folder as JSON file.";
			return false;
		}
	}
	
	function google_auth()
	{
		if($this->checkOauthCredentials() && file_exists($this->credentials_file))
		{
			require_once APPPATH . "libraries/google-api-php-client-2.2.2/vendor/autoload.php";

			$client = new Google_Client();	
			$client->setAuthConfigFile($this->credentials_file);
			$client->addScope("https://www.googleapis.com/auth/userinfo.email");
			$client->addScope("https://www.googleapis.com/auth/userinfo.profile");
			$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/weather/google_auth');

			if (! isset($_GET['code'])) 
			{
			  $auth_url = $client->createAuthUrl();
			  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
			} 
			else 
			{
				$client->authenticate($_GET['code']);
				$_SESSION['access_token'] = $client->getAccessToken();
			  
				if ($client->getAccessToken()) 
				{
					$objOAuthService = new Google_Service_Oauth2($client);
					$userData = $objOAuthService->userinfo->get();
					$_SESSION['access_token'] = $client->getAccessToken();
					if(isset($userData->name) && isset($userData->email) & strlen($userData->name)>0 && strlen($userData->email)>0)
					{
						$picture = "";
						if(isset($userData->picture) && filter_var($userData->picture, FILTER_VALIDATE_URL))
							$picture = $userData->picture;
							
						$newdata = array(
							'name'  => $userData->name,
							'email'     => $userData->email,
							'profilePic' => $picture,
							'logged_in' => TRUE
						);

						$this->session->set_userdata($newdata);
						$this->index();
					}
				} 
				else 
				{
					$authUrl = $client->createAuthUrl();
					$data['authUrl'] = $authUrl;
					$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/weather/google_auth';
					header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
				}
			}
		}			
		else
			die();
	}
	
	function logout()
	{
		$this->session->sess_destroy();
		header('Location: ' . filter_var($this->config->base_url(), FILTER_SANITIZE_URL));
	}
}
