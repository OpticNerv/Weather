<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	private $credentials_file = "";

	public function index()
	{
		if($this->checkOauthCredentials() && file_exists($this->credentials_file))
		{
			require_once APPPATH . "libraries/google-api-php-client-2.2.2/vendor/autoload.php";
			
			session_start();

			$client = new Google_Client();
			$client->setAuthConfig($this->credentials_file);
			$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);

			if (isset($_SESSION['access_token']) && $_SESSION['access_token']) 
			{
			  $client->setAccessToken($_SESSION['access_token']);
			  $drive = new Google_Service_Drive($client);
			  $files = $drive->files->listFiles(array())->getItems();
			  echo json_encode($files);
			} 
			else 
			{
			  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/weather/google_auth';
			  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
			}	
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

			session_start();

			$client = new Google_Client();
					
			$client->setAuthConfigFile($this->credentials_file);
			$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/weather/google_auth');
			$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);

			if (! isset($_GET['code'])) 
			{
			  $auth_url = $client->createAuthUrl();
			  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
			} 
			else 
			{
			  $client->authenticate($_GET['code']);
			  $_SESSION['access_token'] = $client->getAccessToken();
			  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/weather/google_auth';
			  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
			}
		}			
		else
			die();
	}
}
