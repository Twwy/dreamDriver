<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file sit-remove.php 									// 
	//     														//
	//	/site.remove											//
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
	if($siteInfo['remove'] == 1) json(false, '已经处于删除状态');

	$return = $site->update($site_id, array('remove' => 1));

	//补充任务，最近2分钟如果没有任务，就补充2个任务
	$count = $site->taskCheck($site_id, 20);
	if($count == 0){
		$site->taskComplete($site_id, 10, 2);
	}
	
	if($return > 0) json(true, '删除成功');
	else json(false, '未进行删除');


?>