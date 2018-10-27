<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	private $credentials_file = "";

	public function index()
	{
		$this->load->model("Cities");
		$Cities = new Cities();
		
		$extraScripts = array('<script src="'.$this->config->base_url().'js/Chart.bundle.js"></script>',
		'<script src="'.$this->config->base_url().'js/utils.js"></script>','<script>var baseUrl="'.$this->config->base_url().'";</script>');
		
		$this->load->view("Header", array("extraScripts" => $extraScripts));
		$this->load->view("StartPage",array("allCities" => $Cities->getAllCitiesWithWeatherData()));
		$this->load->view("Footer");
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
			  
				if($client->getAccessToken()) 
				{
					$objOAuthService = new Google_Service_Oauth2($client);
					$userData = $objOAuthService->userinfo->get();
					$_SESSION['access_token'] = $client->getAccessToken();
					if(isset($userData->name) && isset($userData->email) && strlen($userData->name)>0 && filter_var($userData->email, FILTER_VALIDATE_EMAIL))
					{
						$picture = "";
						if(isset($userData->picture) && filter_var($userData->picture, FILTER_VALIDATE_URL))
							$picture = $userData->picture;
							
						$newdata = array(
							'name'  => $userData->name,
							'email'     => $userData->email,
							'profile_pic' => $picture,
							'logged_in' => FALSE
						);
						$this->session->set_userdata($newdata);

						$this->load->model('Users');
						$Users = new Users();
						
						$userData = $Users->getUser($this->session->userdata('email'));
						if($userData && (bool)$userData->is_active)
						{
							//update his current session data
							$currentSession = $this->session->userdata();
							$currentSession['logged_in'] = true;
							$currentSession['is_superuser'] = (bool)$userData->is_superuser;
							$this->session->set_userdata($currentSession);
							$this->redirect_to_profile();
						}
						else
							header('Location: ' . filter_var($this->config->base_url()."show_registration_form", FILTER_SANITIZE_URL));
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
	
	function show_registration_form()
	{
		if($this->session->userdata('logged_in'))
			$this->redirect_to_profile();
		else if($this->session->userdata('name') && $this->session->userdata('email'))
		{
			$this->load->model('Users');
			$Users = new Users();
			$userData = $Users->getUser($this->session->userdata('email'));
			if(!$userData)
				$this->load->view('RegistrationForm',array('name' => $this->session->userdata('name'), 'email' => $this->session->userdata('email'), 'profile_pic' => $this->session->userdata('profile_pic')));
			else if((bool)$userData->is_active)
			{
				//update his current session data
				$currentSession = $this->session->userdata();
				$currentSession['logged_in'] = true;
				$currentSession['is_superuser'] = (bool)$userData->is_superuser;
				$this->session->set_userdata($currentSession);
				$this->redirect_to_profile();
			}
			else //deactivated user
				$this->load->view('RegistrationForm',array('errorMessage' => $this->lang->line('registration_deactivatedAcc')));
		}
	}
	
	function register_user()
	{
		$this->load->model('Users');
		$Users = new Users();
	
		if($this->session->userdata('logged_in')) //user is already logged in
			$this->redirect_to_profile();
		else if($this->session->userdata('email') && $this->session->userdata('name')) //user is not yet logged in to our application
		{
		
			if(isset($_POST['consent']) && intval($_POST['consent'])>0)
			{
				$userData = $Users->getUser($this->session->userdata('email'));
				if(!$userData)
				{
					if($Users->registerUser($this->session->userdata('name'),$this->session->userdata('email'),$this->session->userdata('profile_pic')))
					{
						$userData = $Users->getUser($this->session->userdata('email'));
						
						//update his current session data
						$currentSession = $this->session->userdata();
						$currentSession['logged_in'] = true;
						$currentSession['is_superuser'] = (bool)$userData->is_superuser;
						$this->session->set_userdata($currentSession);
						$this->redirect_to_profile();
					}
					else
						$this->load->view('RegistrationForm',array('errorMessage' => $this->lang->line('registration_error')));
				}
				else if((bool)$userData->is_active)
				{
					//update his current session data
					$currentSession = $this->session->userdata();
					$currentSession['logged_in'] = true;
					$currentSession['is_superuser'] = (bool)$userData->is_superuser;
					$this->session->set_userdata($currentSession);
					$this->redirect_to_profile();
					
				}
				else //deactivated user
					$this->load->view('RegistrationForm',array('errorMessage' => $this->lang->line('registration_deactivatedAcc')));
			}
			else //didn`t agree to registration terms
					$this->load->view('RegistrationForm',array('errorMessage' => $this->lang->line('registration_agreeToTerms')));
		}
		else //missing google session data
			$this->google_login();
	}
	
	function showWeatherStats()
	{
		if(isset($_GET['cityId']) && intval($_GET['cityId'])>0)
		{
			$this->load->model($Cities);
			$Cities ) new Cities();
		
		}
		else
			returnJSON(false,400);
	}
	
	function redirect_to_profile()
	{
		if($this->session->userdata('is_superuser'))
			header('Location: ' . filter_var($this->config->base_url()."adminpanel", FILTER_SANITIZE_URL));
		else
			header('Location: ' . filter_var($this->config->base_url()."profile", FILTER_SANITIZE_URL));
	}
	
	function logout()
	{
		$this->session->sess_destroy();
		header('Location: ' . filter_var($this->config->base_url(), FILTER_SANITIZE_URL));
	}
}
