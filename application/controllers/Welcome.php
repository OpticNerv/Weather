<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	private $credentials_file = "";

	/**
	* PHP Function index, default entry point for our page.
	* it loads default scripts and cities with weather data for the landing page
	* @name: index
	**/
	public function index()
	{
		$this->load->model("Cities");
		$Cities = new Cities();
		
		$extraScripts = array('<script src="'.$this->config->base_url().'js/Chart.bundle.js"></script>',
		'	<script src="'.$this->config->base_url().'js/utils.js"></script>',
		'	<script src="'.$this->config->base_url().'js/jquery-ui/jquery-ui.js"></script>',
		'	<link rel="stylesheet" href="'.$this->config->base_url().'js/jquery-ui/jquery-ui.css">',
		'	<script>var baseUrl="'.$this->config->base_url().'"; var actualTempsLbl="'.
		$this->lang->line("actual_temps").'"; var predictedTemps="'.$this->lang->line("forecast_temps").
		'"; var noData="'.$this->lang->line("no_data").'"; var yLabel="'.$this->lang->line("y_label").'"; var xLabel="'.$this->lang->line("x_label").'";</script>');
		$this->load->view("Header", array("extraScripts" => $extraScripts));
		$this->load->view("StartPage",array("allCities" => $Cities->getAllCitiesWithWeatherData()));
		$this->load->view("Footer");
	}
	
	/**
	* PHP Function googleLogin, helper function that redirects to google login function
	* or to users profile, if current user is logged in and his account is valid
	* @name: googleLogin
	**/
	function googleLogin()
	{
		if($this->session->userdata('logged_in'))
			header('Location: ' . filter_var($this->config->base_url()."profile", FILTER_SANITIZE_URL));
		else if($this->checkOauthCredentials() && file_exists($this->credentials_file))
		{
		  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/weather/googleAuth';
		  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		}
		else
			die();
		
	}
	
	/**
	* PHP Function checkOauthCredentials, helper function that chechs if 
	* our google oauth credentials are stored on our server
	* @name: checkOauthCredentials
	**/
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
	
	/**
	* PHP Function googleAuth, performs google authentication
	* if access token and user info is successfully retrieved 
	* it checks if user is already registered (and redirects him accordingly)
	* or it redirects him to registration page
	* @name: googleAuth
	**/
	function googleAuth()
	{
		if($this->checkOauthCredentials() && file_exists($this->credentials_file))
		{
			require_once APPPATH . "libraries/google-api-php-client-2.2.2/vendor/autoload.php";

			$client = new Google_Client();	
			$client->setAuthConfigFile($this->credentials_file);
			$client->addScope("https://www.googleapis.com/auth/userinfo.email");
			$client->addScope("https://www.googleapis.com/auth/userinfo.profile");
			$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/weather/googleAuth');

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
							if($this->updateUserSessionData($userData))
								$this->redirectToProfile();
							else
								$this->load->view('RegistrationForm',array('errorMessage' => $this->lang->line('profile_error')));
						}
						else
							header('Location: ' . filter_var($this->config->base_url()."showRegistrationForm", FILTER_SANITIZE_URL));
					}
				} 
				else 
				{
					$authUrl = $client->createAuthUrl();
					$data['authUrl'] = $authUrl;
					$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/weather/googleAuth';
					header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
				}
			}
		}			
		else
			die();
	}
	
	/**
	* PHP Function showRegistrationForm, helper function for loading registration form for new users
	* if user is already logged in and his account is valid, he is redirected accordingly
	* @name: showRegistrationForm
	**/
	function showRegistrationForm()
	{
		if($this->session->userdata('logged_in'))
			$this->redirectToProfile();
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
				if($this->updateUserSessionData($userData))
					$this->redirectToProfile();
				else
					$this->load->view('RegistrationForm',array('errorMessage' => $this->lang->line('profile_error')));
			}
			else //deactivated user
				$this->load->view('RegistrationForm',array('errorMessage' => $this->lang->line('registration_deactivatedAcc')));
		}
	}
	
	/**
	* PHP Function registerUser, function used for registering new users
	* it checks if user is already registered and if his account is valid it redirects him accordingly
	* if he is a new user, he has to consent to our page usage terms, then his account is created
	* @name: registerUser
	**/
	function registerUser()
	{
		$this->load->model('Users');
		$Users = new Users();
	
		if($this->session->userdata('logged_in')) //user is already logged in
			$this->redirectToProfile();
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
						if($this->updateUserSessionData($userData))
							$this->redirectToProfile();
						else
							$this->load->view('RegistrationForm',array('errorMessage' => $this->lang->line('profile_error')));
					}
					else
						$this->load->view('RegistrationForm',array('errorMessage' => $this->lang->line('registration_error')));
				}
				else if((bool)$userData->is_active)
				{
					//update his current session data
					if($this->updateUserSessionData($userData))
						$this->redirectToProfile();
					else
						$this->load->view('RegistrationForm',array('errorMessage' => $this->lang->line('profile_error')));
				}
				else //deactivated user
					$this->load->view('RegistrationForm',array('errorMessage' => $this->lang->line('registration_deactivatedAcc')));
			}
			else //didn`t agree to registration terms
					$this->load->view('RegistrationForm',array('errorMessage' => $this->lang->line('registration_agreeToTerms')));
		}
		else //missing google session data
			$this->googleLogin();
	}
	
	/**
	* PHP Function updateUserSessionData, helper function for adding necessary system session variables
	* to his current session, such as his user id and if user is admin
	* @name: updateUserSessionData
	**/
	function updateUserSessionData($userData)
	{
		if($this->session->userdata("email") && isset($userData->email) && $this->session->userdata("email") == $userData->email)
		{
			$currentSession = $this->session->userdata();
			$currentSession['logged_in'] = true;
			$currentSession['is_superuser'] = (bool)$userData->is_superuser;
			$currentSession['user_id'] = $userData->id;
			$this->session->set_userdata($currentSession);
			return true;
		}
		else
			return false;
	}
	
	/**
	* PHP Function redirectToProfile, redirects current to his profile, according to his 
	* privileges (admin/regular user)
	* @name: redirectToProfile
	**/
	function showWeatherStats()
	{
		if(isset($_GET['cityId']) && intval($_GET['cityId'])>0)
		{
			$this->load->model("Cities");
			$Cities = new Cities();
			
			$startDate = 0;
			$endDate = 0;
			$forecast = false;
			
			if(isset($_GET["startDate"]) && strlen($_GET["startDate"])>0)
				$startDate = strtotime($_GET["startDate"]);
			
			if(isset($_GET["endDate"]) && strlen($_GET["endDate"])>0)
				$endDate = strtotime($_GET["endDate"]);
			
			if(isset($_GET["forecast"]) && $_GET["forecast"] == "true")
				$forecast = true;
			else
				$forecast = false;
				
			/* correct the end date so it includes whole 23:59 time */	
			if($endDate)
				$endDate   = strtotime("tomorrow", $endDate) - 1;		
					
			returnJSON($Cities->getWeatherForecast(intval($_GET['cityId']),$startDate,$endDate,$forecast));
		}
		else
			returnJSON(false,400);
	}
	
	/**
	* PHP Function redirectToProfile, redirects current to his profile, according to his 
	* privileges (admin/regular user)
	* @name: redirectToProfile
	**/
	function redirectToProfile()
	{
		if($this->session->userdata('is_superuser'))
			header('Location: ' . filter_var($this->config->base_url()."adminpanel", FILTER_SANITIZE_URL));
		else
			header('Location: ' . filter_var($this->config->base_url()."profile", FILTER_SANITIZE_URL));
	}
	
	/**
	* PHP Function logout, destroys users current session and redirects to our landing page
	* @name: logout
	**/
	function logout()
	{
		$this->session->sess_destroy();
		header('Location: ' . filter_var($this->config->base_url(), FILTER_SANITIZE_URL));
	}
}
