<?php
/**
* PHP Function returnJSON, helper function for returning data in json format, 
* also sets the desired response code.
* @name: returnJSON
**/
function returnJSON($data,$status=200)
{
	header("Content-Type: application/json");
	
	if(is_numeric($status) && intval($status)>0)
		http_response_code(intval($status));
	else
		http_response_code(500);
	
	$json_response = json_encode($data);
	if($json_response === false) 
		$json_response = json_encode("JSONError");
		
	echo $json_response;
}
?>
