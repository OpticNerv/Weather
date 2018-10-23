<?php
class Users extends CI_Model 
{
	function __construct()
    {
        parent::__construct();
		$this->load->database();
    }


	/**
	* PHP Function registerUser, registers a new user if it doesn`t exist yet. Validation is done via email.
	* @name: loadData
	**/
	function registerUser($name,$email,$user_image="",$superuser=0)
	{
		if(strlen($name)>0 && strlen($email)>0)
		{
			if(!$this->getUser($email))
			{	
				$name = $this->db->escape($name);
				$email = $this->db->escape($email);
				$superuser = $this->db->escape($superuser);
				
				if(!filter_var($user_image, FILTER_VALIDATE_URL))
					$user_image="";
					
				$user_image = $this->db->escape($user_image);	
				
				$this->db->query("INSERT INTO users VALUES(null,$email,$name,$user_image,$superuser,1,NOW())");
				if($this->db->affected_rows()>0)
					return $this->db->insert_id();
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
	* PHP Function getUser, retrieves users information from database by email or by Id.
	* @name: getUser
	**/
	function getUser($userInfo)
	{
		if(is_numeric($userInfo) && $userInfo>0) //user Id was provided
		{
			$userId = $this->db->escape($userInfo);
			$query = $this->db->query("SELECT * FROM users WHERE id=$userInfo");
			if($query->num_rows()>0)
				return $query->row();
			else
				return false;
		}
		else if(filter_var($userInfo, FILTER_VALIDATE_EMAIL)) //a valid user email was provided
		{
			$userInfo = $this->db->escape($userInfo);
			$query = $this->db->query("SELECT * FROM users WHERE email=$userInfo");
			if($query->num_rows()>0)
				return $query->row();
			else
				return false;
		}
		else
			return false;
	}
	
	/**
	* PHP Function clearUserCities, clears all user cities.
	* @name: clearUserCities
	**/
	function clearUserCities($userId)
	{
		if($userId>0)
		{
			$this->db->query("DELETE FROM user_cities WHERE user_id=$userId");
			if($this->db->affected_rows()>0)
				return true;
			else
				return false;
		}
		else
			return false;
	}
	
	/**
	* PHP Function insertUserCities, bulk inserts user cities.
	* @name: insertUserCities
	**/
	function insertUserCities($userId,$cities)
	{
		if($userId>0 && is_array($cities) && count($cities)>0)
		{
		
		}
		else
			return false;
	}
	
	/**
	* PHP Function promoteToSuperUser, promotes a user to superuser.
	* @name: promoteToSuperUser
	**/
	function promoteToSuperUser($userId)
	{
		if(is_numeric($userId) && $userId>0)
		{
			$userData = $this->getUser($userId);
			if($userData && $userData->is_active)
			{
				if(!(bool)$userData->is_superuser)
				{
					$userId = $this->db->escape($userId);
					$this->db->query("UPDATE users SET is_superuser=1 WHERE id=$userId");
					if($this->db->affected_rows()>0)
						return true;
					else
						return false;
				}
				else //is already a superuser
					return true;
			}
			else
				return false;
		}
		else
			return false;
	}
	
	
	/**
	* PHP Function demoteFromSuperUser,	demotes a user from superuser.
	* @name: demoteFromSuperUser
	**/
	function demoteFromSuperUser($userId)
	{
		if(is_numeric($userId) && $userId>0)
		{
			$userData = $this->getUser($userId);
			if($userData && $userData->is_active)
			{
				if((bool)$userData->is_superuser)
				{
					$userId = $this->db->escape($userId);
					$this->db->query("UPDATE users SET is_superuser=0 WHERE id=$userId");
					if($this->db->affected_rows()>0)
						return true;
					else
						return false;
				}
				else //is already a regular user
					return true;
			}
			else
				return false;
		}
		else
			return false;
	}
}
?>