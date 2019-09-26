<?php
//put your API Key and Secret in these two variables.
$api_key = "pmuT5XXCR8hHSW1ktJddGUV4SZG4ZAfo";
$api_secret = "nRAPqOiAwzSYYaSa"; 
 
//When called this function will request an Access Token and then return just
//the token value. 
function GetOAuthToken(){ 
    global $api_key,$api_key;  
 
    $ch = curl_init("http://api.awhere.com/oauth/token");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                                "Content-Type: application/x-www-form-urlencoded",
                                                "Authorization: Basic ".base64_encode($api_key.":".$api_key)
                                            ));
 
    $result = curl_exec($ch); 
    //$result = CurlExecute($ch); 
    $result = json_decode($result);
    return $result->access_token;
}

GetOAuthToken($api_key,);
?>
