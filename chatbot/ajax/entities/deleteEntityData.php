<?php

	if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }
	$devAccessToken = "76c59f2365aa4cae94cf259635c87dfe";
    $baseUrl = "https://api.api.ai/v1/entities/";
    $thisID = $_GET['id'];
    $thisValue = $_GET['value'];
    $dateToday = date("Ymd");
    
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_URL, $baseUrl . $thisID . "?v=" . $dateToday );
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $thisValue);
	
	$headers = array();
	$headers[] = "Accept: application/json";
	$headers[] = "Content-Type: application/json";
	$headers[] = "Authorization: Bearer " . $devAccessToken;
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    echo 'Error:' . curl_error($ch);
	}
	curl_close ($ch);
?>