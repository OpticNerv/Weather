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
	* PHP Function getAllCitiesWithWeatherData, retrieves all cities with current weather data, used for front page.
	* @name: getAllCitiesWithWeatherData
	**/
	function getAllCitiesWithWeatherData()
	{
		$query = $this->db->query("SELECT cities.* 
		FROM cities 
		INNER JOIN cities_weather
		ON cities_weather.city_id = cities.id
		GROUP BY cities.id
		ORDER BY city_name ASC");
		
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
			if(!is_numeric($type) || intval($type)<0 ||intval($type)>1)
				$type = 0;
			else
				$type = intval($type);
				
			$type = $this->db->escape($type);	
		
			$insertSQL = "";
			
			foreach($weatherData->list as $weather)
			{
				if(isset($weatherData->city->id) && $weatherData->city->id>0)
					$cityId = $this->db->escape($weatherData->city->id);
				else if(isset($weather->id) && $weather->id>0)
					$cityId = $this->db->escape($weather->id);
				else
					$cityId = $this->db->escape(0);
			
				if(intval($cityId)>0)
					$insertSQL .= "(".$this->db->escape($cityId).",".$this->db->escape($weather->dt).",".$this->db->escape($weather->main->temp).","
						.$this->db->escape($weather->main->temp_min).",".$this->db->escape($weather->main->temp_max).","
						.$this->db->escape($weather->main->humidity).",".$this->db->escape($weather->wind->speed).",$type),";	
			}
			
			if(strlen($insertSQL)>0)
			{
				$this->db->query("INSERT IGNORE INTO cities_weather VALUES".rtrim($insertSQL,","));
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
	
	/**
	* PHP Function getWeatherForecast, retrieves weather forecast data - type 0 is current weather, type 1 is forecast, 2 is both.
	* @name: getWeatherForecast
	**/
	function getWeatherForecast($cityId, $minDate=0, $maxDate=0, $type=0)
	{
		if(is_numeric($cityId) && $cityId>0)
		{
			$condition = "";
			
			if(!is_numeric($type) || $type<0 || $type>3)
				$type = 0;
			
			if($type==2)
				$condition = "(cities_weather.type=0 OR cities_weather.type=1)";
			else
				$condition = "cities_weather.type=".$this->db->escape($type);
			
			if(is_numeric($minDate) && is_numeric($maxDate) && $minDate<$maxDate)
				$query = $this->db->query("SELECT cities_weather.*,FROM_UNIXTIME(cities_weather.timestamp,'%d.%m.%Y %h:%i') 
				FROM cities_weather
				WHERE city_id=$cityId AND $condition
				AND cities_weather.timestamp>=".$this->db->escape($minDate)." AND cities_weather.timestamp<=".$this->db->escape($maxDate));
			else
				$query = $this->db->query("SELECT cities_weather.*,FROM_UNIXTIME(cities_weather.timestamp,'%d.%m.%Y %h:%i') 
				FROM cities_weather
				WHERE city_id=$cityId AND $condition
				AND cities_weather.timestamp>=UNIX_TIMESTAMP(CURDATE()) AND cities_weather.timestamp<=UNIX_TIMESTAMP(NOW())");
				
			if($query->num_rows()>0)
				return $query->result();
			else
				return false;
			
		}
		else
			return false;
	}
}
?>