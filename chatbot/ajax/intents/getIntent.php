<?php

	if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }
	$devAccessToken = "e14e1c6b19b74fac95c1bdf52689f6b7";
    $baseUrl = "https://api.api.ai/v1/entities/";
    $myArray = array();
    $dateToday = date("Ymd");

    $ch = curl_init();


	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_URL, $baseUrl . "?v=" . $dateToday );

	$headers = array();
	$headers[] = "Authorization: Bearer 76c59f2365aa4cae94cf259635c87dfe";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    echo 'Error:' . curl_error($ch);
	}





	curl_close ($ch);
?>
