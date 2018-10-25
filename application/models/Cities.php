<?php
class Cities extends CI_Model 
{
	function __construct()
    {
        parent::__construct();
		$this->load->database();
    }

	/**
	* PHP Function getAllCities, returns all cities viable for weather search, sorted in ascended order.
	* @name: getAllCities
	**/
	function getAllCities()
	{
		$query = $this->db->query("SELECT * FROM cities WHERE cities.country_name='SI' ORDER BY city_name ASC");
		if($query->num_rows()>0)
			return $query->result();
		else
			return false;
	}
	
	/**
	* PHP Function getAllCitiesChosenByUsers, returns all unique cities that have been selected by user(s).
	* @name: getAllCitiesChosenByUsers
	**/
	function getAllCitiesChosenByUsers()
	{
		$query = $this->db->query("SELECT id 
		FROM cities 
		INNER JOIN user_cities
		ON user_cities.city_id = cities.id
		GROUP BY cities.id");
		
		if($query->num_rows()>0)
			return $query->result();
		else
			return false;
	}
	
	/**
	* PHP Function storeWeatherForecast, stores weather forecast data - type 0 is current weather, type 1 is forecast.
	* @name: storeWeatherForecast
	**/
	function storeWeatherForecast($weatherData,$type=0)
	{
		if(isset($weatherData->cnt) && $weatherData->cnt>0 && isset($weatherData->list) && is_array($weatherData->list) && count($weatherData->list)>0)
		{
			if(!is_numeric($type) || intval($type)<0)
				$type = 0;
			else
				$type = intval($type);
				
			$type = $this->db->escape($type);	
		
			$insertSQL = "";
			
			foreach($weatherData->list as $weather)
			{
				$insertSQL .= "(".$this->db->escape($weather->sys->id).",NOW(),".$this->db->escape($weather->main->temp).","
					.$this->db->escape($weather->main->temp_min).",".$this->db->escape($weather->main->temp_max).","
					.$this->db->escape($weather->main->humidity).",".$this->db->escape($weather->wind->speed).",$type),";	
			}
			
			if(strlen($insertSQL)>0)
			{
				$this->db->query("INSERT INTO cities_weather VALUES".rtrim($insertSQL,","));
				if($this->db->affected_rows()>0)
					return true;
				else
					return false;
			}
			else
				return false;
		}
		else
			return false;
	}

}
?>