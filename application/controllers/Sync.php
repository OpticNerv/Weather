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
				
					//retrieve current weather conditions of cities selected by users
					foreach($userCities as $cityBatch)
					{
						$result = $this->getCurrentWeatherData($cityBatch);
						if($result)
							$Cities->storeWeatherForecast($result,0);
						
						sleep(40); //sleep for 40 seconds
					}
					
					//get 5 day weather forecast data, it can be only done for one city at a time
					foreach($userCities as $cityBatch)
					{
						foreach($cityBatch as $city)
						{
							$result = $this->getWeatherForecastData($city->id);
							if($result)
								$Cities->storeWeatherForecast($result,1);
						
							sleep(2); //sleep for 2 seconds
						}
					}
				}
				
				return true;
			}
			else
				die("Missing OpenWeatherMap API key and/or API URL. Or API call URL is invalid, so make sure to set them up in your config file.");
		}
		else
			die("Unauthorized access!");
	}
	
	
	/**
	* PHP Function getCurrentWeatherData, retrieves current weather information for an array of city Ids
	* cityId is id of the city, according to the OpenWeatherMapAPI
	* Max limit is set to 20 due to the OpenWeatherMap free acc limitations
	* @name: getCurrentWeatherData
	**/
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
							$ch = curl_init();
							curl_setopt ($ch, CURLOPT_URL, $requestUrl);
							curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
							$response = curl_exec($ch);
							if (curl_errno($ch)) {
								$response = "";
							} else {
							  curl_close($ch);
							}
					
							if($response)
							{
								$response = json_decode($response);
								if(isset($response->cnt) && $response->cnt>1)
									return $response;
								else
									return false;
							}
							else
								return false;
						}
						catch(Exception $e) { echo "error retriving data"; return false; }
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
	
	/**
	* PHP Function getWeatherForecastData, retrieves forecast for a specified city
	* cityId is id of the city, according to the OpenWeatherMapAPI
	* it retrieves data for 5 days in three hours interval
	* @name: getWeatherForecastData
	**/
	function getWeatherForecastData($cityId)
	{
		if(is_cli())
		{
			if(is_numeric($cityId) && intval($cityId)>0)
			{
				if($this->config->item('owp_api_key') && $this->config->item('owp_api_url') && strlen($this->config->item('owp_api_key'))>0 && filter_var($this->config->item('owp_api_url'), FILTER_VALIDATE_URL))
				{
					$requestUrl = $this->config->item('owp_api_url')."forecast?id=".$cityId."&APPID=".$this->config->item('owp_api_key')."&units=metric";
					try
					{
						$ch = curl_init();
						curl_setopt ($ch, CURLOPT_URL, $requestUrl);
						curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
						$response = curl_exec($ch);
						if (curl_errno($ch)) {
							$response = "";
						} else {
						  curl_close($ch);
						}
				
						if($response)
						{
							$response = json_decode($response);
							if(isset($response->cnt) && $response->cnt>1)
								return $response;
							else
								return false;
						}
						else
							return false;
					}
					catch(Exception $e) { echo "error retriving data"; return false; }
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
