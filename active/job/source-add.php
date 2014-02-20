<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file source-add.php 									// 
	//     														//
	//	/site.source-add										//
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
	$type = filter('type', '/^[0-9]{1,2}$/', '线路格式错误');
	$ip = filter('ip', '/^([0-9]{1,3}\.){3}[0-9]{1,3}(\:[0-9]{1,5})?$/', 'IP格式错误');

	$site = model('site');

	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');

	if($type > 5) json(false, '未知线路');
	$ip = explode(':', $ip);
	if(isset($ip[1])) $port = $ip[1];
	else $port = 80;
	$ip = $ip[0];

	//查询是否为节点
	$node = model('node');
	$nodeInfo = $node->get($ip);
	if(!empty($nodeInfo)) json(false, '不能设置节点IP为源站');

	$check = $site->checkSource($site_id, $ip, $port);
	if(!empty($check)) json(false, '该源站记录已添加');

	$insert = $site->addSource($site_id, $ip, $type, $port);

	if($insert > 0){
		
		//获取开启状态的level
		$levels = $site->getLevel($site_id);
		$client= new GearmanClient(); 
		$client->addServer(); 
		
		foreach ($levels as $value) {
			if($value['status'] == 0){
				$data = array(
					'site_id' => $site_id,
					'level_id' => $value['level_id']
				);
				$data = json_encode($data);
				$client->doBackground('level', $data);
			}
		}

		//补充任务，最近2分钟如果没有任务，就补充1个任务
		$count = $site->taskCheck($site_id, 60 * 2);
		if($count == 0){
			$site->taskComplete($site_id, 60, 1);
		}

		json(true, '源站记录添加成功');
	}else json(false, '源站记录添加失败');

?>