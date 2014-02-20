<?php


	//////////////////////////////////////////////////////////////
	//															//
	//	file purge.php 											// 
	//     														//
	//	/site.purge												//
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

	$site_id = filter('site_id', '/^[0-9]{1,9}$/', '站点ID格式错误');

	$site = model('site');
	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');


	$data = array('site_id' => $site_id);
	$data = json_encode($data);

	$client= new GearmanClient();  
	$client->addServer(); 
	$client->doBackground('purge', $data);

	json(true, '已提交全站缓存清除任务');

