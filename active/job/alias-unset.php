<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file alias-unset.php									// 
	//     														//
	//	/site.alias-unset										//
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
	$domain = filter('domain', '/^[a-zA-z0-9\-\.]+$/', '域名格式错误');

	$domain = test_domain($domain);
	if(!$domain) json(false, '域名格式不合法');

	$site = model('site');
	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');

	if(empty($siteInfo['multi_domain'])) json(false, '别名为空');

	$domains = array();
	$domains = explode('|', $siteInfo['multi_domain']);
	$search = array_search($domain, $domains);
	if($search === false) json(false, '该别名不存在');
	unset($domains[$search]);
	$updateArray = array('multi_domain' => implode('|', $domains));

	$update = $site->update($site_id, $updateArray);
	
	//将配置conf_status设置为待分发
	$site->confUpdate($site_id);

	//补充任务，最近2分钟如果没有任务，就补充1个任务
	$count = $site->taskCheck($site_id, 60 * 2);
	if($count == 0){
		$site->taskComplete($site_id, 60, 1);
	}

	if($update > 0) json(true, '别名删除成功');
	json(false, '别名删除失败');

?>