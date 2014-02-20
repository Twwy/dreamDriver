<?php


	//////////////////////////////////////////////////////////////
	//															//
	//	file per-add.php 										// 
	//     														//
	//	/site.per.add											//
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

	$domain = filter('domain', '/^[a-zA-z0-9\-\.]+$/', '域名格式错误');

	$domain = test_domain($domain);
	if(!$domain) json(false, '域名格式不合法');

	$site = model('site');
	
	$userSite = $site->get($domain);
	if(!empty($userSite)) json(false, '域名已经被添加');

	$ip = gethostbyname($domain);
	if($ip && $ip != $domain){
		json(true, '检测成功,进行可以添加站点', $ip);
	}else{
		json(true, '检测成功,进行可以添加站点', false);
	}

