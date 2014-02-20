<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file sit-host.php 										// 
	//     														//
	//	/site.host												//
	//                                        	        		//
	//	This file is part of the openCDN project				//
	//															//
	//	http://ocdn.me                              	    	//
	//                                                  		//
	//////////////////////////////////////////////////////////////

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$host = filter('host', '/^[0-9\.a-zA-Z\-]{0,255}$/', 'Host格式错误');
	$site_id = filter('site_id', '/^[0-9]{1,9}$/', '站点ID格式错误');

	$site = model('site');
	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');

	if($host == 'auto') $host = 'auto';
	elseif(test_ip($host)){
		$ip = test_ip($host);
		$host = $ip[0];
	}elseif(test_domain($domain)){
		$host = test_domain($domain);
	}else json(false, 'Host格式有误');

	$return = $site->update($site_id, array('host' => $host));

	if($return > 0){
		$site->confUpdate($site_id);
		json(true, 'Host更改成功');
	}else json(false, 'Host未更改');


?>