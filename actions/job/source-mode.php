<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file source-mode.php 									// 
	//     														//
	//	/site.source-mode										//
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

	$mode = filter('mode', '/^0|1$/', '回源格式错误');
	$site_id = filter('site_id', '/^[0-9]{1,9}$/', '站点ID格式错误');

	$site = model('site');
	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');

	$return = $site->update($site_id, array('source_mode' => $mode));

	//补充任务，最近2分钟如果没有任务，就补充1个任务
	$count = $site->taskCheck($site_id, 60 * 2);
	if($count == 0){
		$site->taskComplete($site_id, 60, 1);
	}

	if($return > 0){
		$site->confUpdate($site_id);
		json(true, '回源模式更改成功');
	}else json(false, '回源模式未更改');


?>