<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file sit-cache.php 										// 
	//     														//
	//	/site.cache												//
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

	$mode = filter('mode', '/^0|1|2$/', '模式格式错误');
	$site_id = filter('site_id', '/^[0-9]{1,9}$/', '站点ID格式错误');

	$site = model('site');
	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');

	$return = $site->update($site_id, array('cache_mode' => $mode));

	if($mode == 0){
		$pattern = model('pattern');
		$list = $pattern->rule_list($site_id);
		if(count($list) < 1){
			$pattern->add($site_id, '/', -1, 0);
			$pattern->add($site_id, 'ico|jpg|jpeg|bmp|gif|png|js|css', 1800, 1);
		}
	}

	if($return > 0){

		//将配置conf_status设置为待分发
		$site->confUpdate($site_id);

		//补充任务，最近2分钟如果没有任务，就补充1个任务
		$count = $site->taskCheck($site_id, 60 * 2);
		if($count == 0){
			$site->taskComplete($site_id, 60, 1);
		}

		json(true, '模式更改成功');
	}else json(false, '模式更改失败');


?>