<?php



function random($type = 'str', $length){
	$chars = array('num' => '0123456789', 'str' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890');
	$result = '';
	$chars_length = strlen($chars[$type]) - 1;
	for ($i = 0; $i < $length; $i++){
		if($type == 'num' && $i == 0) $result .= $chars[$type]{rand(1, $chars_length)};
		else $result .= $chars[$type]{rand(0, $chars_length)};
	}
	return $result;
}

function wget($url, $timeout = 3){
	$curl = curl_init();
	
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

	$result = curl_exec($curl);
	$curlinfo = curl_getinfo($curl);
	curl_close($curl);
	return array('body' => $result, 'info' => $curlinfo);
}

function post($url, $param = array(), $timeout = 5){
	$curl = curl_init();
	
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
	if(!empty($param)){
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($param));
	}
	$result = curl_exec($curl);
	curl_close($curl);
	return $result;	
}




?>