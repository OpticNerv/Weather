<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sync extends CI_Controller {
	
	
	/* So this is how this works. :) It takes distinct cities from all users and retrieves data for them. 
   Depending on number of said cities it retrieves info for them. 
   
   - If there is <20 of those cities all are updates
   - If there are >20 cities selected, it updates oldest/missing records first 
   - If there are 0 cities selected it updates the ones with no data so far
   
   Examples of API calls:
	http://api.openweathermap.org/data/2.5/group?id=524901,703448,2643743&units=metric

	The limit of locations is 20.
	NOTE: A single ID counts as a one API call! So, the above example is treated as a 3 API calls.
	
	Calls per minute (no more than)	60 - vseh slo mest 349
   */
	function syncOWMP($limit=20)
	{
		if(is_cli())
		{
			if($this->config->item('owp_api_key') && $this->config->item('owp_api_url') && strlen($this->config->item('owp_api_key'))>0 && filter_var($this->config->item('owp_api_url'), FILTER_VALIDATE_URL))
			{
				$this->load->model('Cities');
				$Cities = new Cities();
				
				$userCities = $Cities->getAllCitiesChosenByUsers();
				if($userCities)
				{
					$userCities = array_chunk($userCities,$limit);
				
					foreach($userCities as $cityBatch)
					{
						//get the current weather data
						$result = $this->getCurrentWeatherData($cityBatch);
						if($result)
							$Cities->storeWeatherForecast($result,0);
						
						sleep(40); //sleep fo 40 seconds
						
						
						//get the forecast weather data
						
						
					}
				}
			}
			else
				die("Missing OpenWeatherMap API key and/or API URL. Or API call URL is invalid, so make sure to set them up in your config file.");
		}
		else
			die("Unauthorized access!");
	}
	
	
	/* Max limit is set to 30 due to the OpenWeatherMap free acc limitations*/
	function getCurrentWeatherData($cityIds,$limit=20)
	{
		if(is_cli())
		{
			if(is_array($cityIds) && count($cityIds)<=$limit)
			{
				if($this->config->item('owp_api_key') && $this->config->item('owp_api_url') && strlen($this->config->item('owp_api_key'))>0 && filter_var($this->config->item('owp_api_url'), FILTER_VALIDATE_URL))
				{
					$requestUrl = "";

					foreach($cityIds as $city)
					{
						if(is_numeric($city->id) && intval($city->id)>0)
						{
							if(strlen($requestUrl)>0)
								$requestUrl .= ",".$city->id;
							else
								$requestUrl = $city->id;
						}
					}
					
					
					//at least one city was added to the list
					if(strlen($requestUrl)>0)
					{
						$requestUrl = $this->config->item('owp_api_url')."group?id=".$requestUrl."&APPID=".$this->config->item('owp_api_key')."&units=metric";
						try
						{
							$response = json_decode(file_get_contents($requestUrl));
							if(isset($response->cnt) && $response->cnt>1)
								return $response;
							else
								return false;
						}
						catch(Exception $e) { echo "error retriving data"; }
					}
					else
						return false;
				}
				else
					return false;
			}
			else
				return false;
		}
		else
			die("Unauthorized access!");
	}
}