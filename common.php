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
	// if(!$result){
	// 	$curlinfo = curl_getinfo($curl);
	// 	var_dump($curlinfo['http_code']);
	// }
	curl_close($curl);
	return $result;	
}


function test_domain($domain){
	$testDomain = explode('.', $domain);
	if(count($testDomain) < 2) return false;
	for ($i = 0; $i < count($testDomain); $i++) { 
		if($i == count($testDomain) - 1){
			if(!preg_match('/^[a-zA-Z]{2,10}$/', $testDomain[$i])) return false;
		}else{
			if(!preg_match('/^[a-zA-Z0-9\-]{1,255}$/', $testDomain[$i])) return false;
		}
	}
	return strtolower($domain);
}

function test_ip($ip){
	$port = 80;
	$ipArray = explode(':', $ip);
	if(isset($ipArray[1]) && $ipArray[1] > 0 && $ipArray[1] <= 65535) $port = $ipArray[1];
	$ip = explode('.', $ipArray[0]);
	foreach ($ip as $value) if(!($value >= 0 && $value <= 255)) return false;
	return array($ipArray[0], $port);
}

function bitShow($g, $round = 1, $space = true){	//round为小数点后几位，如果是-1的话，则显示1GB2MB3KB
	$z = '';
	if($g < 0){
		$z = '-';
		$g = abs($g);
	}
	$bitArray = array('GB', 'MB', 'KB', 'B');
	$show = array_shift($bitArray);
	while ( $g < 1 ) {
		if(count($bitArray) == 0) break;
		$show = array_shift($bitArray);
		$g = $g * 1024;
	}
	if($round > 0) $byte = floor($g * pow(10, $round)) / pow(10, $round);
	else return false;
	if($space) return "{$z}{$byte} {$show}";
	else return "{$z}{$byte}{$show}";
}

function topDomain($domain){
	$domainArray = explode('.', $domain);
	if(count($domainArray) == 2) return $domain;
	$suffix = array('org.cn','com.cn','net.cn','gov.cn');
	$count = count($domainArray);
	$domainSuffix = $domainArray[$count - 2].'.'.$domainArray[$count - 1];
	if(in_array($domainSuffix, $suffix)) return $domainArray[$count - 3].'.'.$domainSuffix;
	else return $domainSuffix;
}



?>