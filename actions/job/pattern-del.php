<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file pattern-del.php 									// 
	//     														//
	//	/site.pattern.del										//
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

	$pattern_id = filter('pattern_id', '/^[0-9]{1,9}$/', '匹配ID格式错误');

	$pattern = model('pattern');
	$patternContent = $pattern->get($pattern_id);
	if(empty($patternContent)) json(false, '该规则不存在');
	$site_id = $patternContent['site_id'];

	$site = model('site');
	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');

	if($patternContent['type'] == 0 && $patternContent['rule'] == '/'){
		json(false, '根规则路径不允许删除');
	}
	
	$del = $pattern->del($pattern_id);

	if($del == 0) json(false, '删除失败');

	//将配置conf_status设置为待分发
	$site->confUpdate($site_id);

	//补充任务，最近2分钟如果没有任务，就补充1个任务
	$count = $site->taskCheck($site_id, 60 * 2);
	if($count == 0){
		$site->taskComplete($site_id, 60, 1);
	}

	json(true, '删除成功');


?>