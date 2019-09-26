<?php
/* aWhere Code Samples
 * Copyright (C) 2015 aWhere Inc.
 * License: MIT 
 * Author: Jeof Oyster (jeofoyster@awhere.com) 
 * 
 * These code samples show a variety of different use cases and demonstrate how to 
 * make API calls in PHP. Each file shows a different use case. And each file 
 * is designed so that if you load the file to a browser and access it from a server, 
 * you will see prettified results in HTML. 
 */ 


/* CODE SAMPLE: CREATE A FIELD LOCATION */ 



// Include Header 
// Be sure to change the variables in this header.php file, especially adding your 
// API Key and Secret or else the API calls will not run. This file uses three helper
// functions--GetOAuthToken(), makeAPICall(), and parseHTTPHeaders()--to streamline
// basic API operations. 

include("header.php"); 


// GET A TOKEN 
// First, you always need to generate a token. We built the GetOAuthToken 
// function (in header.php) to streamline that part

echo "<h1>Get Access Token</h1>"; 

try{ 	//if there is a cURL problem and the API call can't execute at all, 
		//the function throws an exception which we can catch to fail gracefully.
		
	$access_token = GetOAuthToken($api_key,$api_secret); 

} catch(Exception $e){ 
	echo $e->getMessage(); // For this script we're just echoing the error and stopping the rest of the script. 
	exit();  			   // in your code you'll want to handle the error and recover appropriately.
} 

echo "<p>Access Token = $access_token</p>"; 

try{
$newCropId = makeAPICall('GET',
						'https://api.awhere.com/v2/agronomics/crops',
						$access_token,
						$newCorpResponseCode,
						$newCorpResponseHeaders);
} catch(Exception $e){ 
	echo $e->getMessage(); 
	exit();  			   
} 
if($newCorpResponseCode==200){ 
	echo '<pre>'; 
	echo stripslashes(json_encode($newCropId,JSON_PRETTY_PRINT)); 	//Note: Stripslashes() is used just for prettier 
	echo '</pre>';
	echo $newCropId->crops[0]->id;
} else { 	
	echo "<p>ERROR: ".$newCorpResponseCode;
}

$newPlantingBody = array("crop"=>$newCropId->crops[0]->id,
			  			 "plantingDate"=>$new_plantingDate
			  			); 
						
try{ 
$newPlantingResponse = makeAPICall('POST', 						//verb			 
								   'https://api.awhere.com/v2/agronomics/fields/'.$new_field_id.'/plantings',
									$access_token,					//Access Token
									$newPlantingResponseHeaders,
									$newPlantingResponseCode,		//Status Code (returned from function)
									json_encode($newPlantingBody),  			  //Send the body as a json-formatted string
									array("Content-Type: application/json")   //The API requires an additional header to describe the payload.
									); 
} catch(Exception $e){ 
	echo $e->getMessage(); 
	exit(); 
} 

if($newPlantingResponseCode==200){  	// Code 200 means the request was successful

	echo '<pre>'; 
	echo stripslashes(json_encode($newPlantingResponse,JSON_PRETTY_PRINT)); 	//Note: Stripslashes() is used just for prettier 
	echo '</pre>';
	
} else { 							// If there is any other response code, there was a problem.
									// this code shows how to extract the two different error messages
									// You should not use the error messages themselves to drive behavior
									// (don't test them in if() or switch() statements)
									// use the status code for that. See developer.awhere.com/api/conventions 
									
	echo "<p>ERROR: ".$newPlantingResponseCode." - ".$newPlantingResponse->simpleMessage."<br>"; 
	echo $newPlantingResponse->detailedMessage."</p>"; 
	
}
?>

