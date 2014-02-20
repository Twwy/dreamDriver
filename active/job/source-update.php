<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file source-update.php 									// 
	//     														//
	//	/site.source-update										//
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

	$source_id = filter('source_id', '/^[0-9]{1,9}$/', '源站ID格式错误');
	$type = filter('type', '/^[0-9]{1,2}$/', '线路格式错误');
	$ip = filter('ip', '/^([0-9]{1,3}\.){3}[0-9]{1,3}(\:[0-9]{1,5})?$/', 'IP格式错误');

	$site = model('site');

	$source = $site->getSource(0, $source_id);

	if(empty($source)) json(false, '源站记录不存在');

	$site_id = $source['site_id'];
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
	if(!empty($nodeInfo)) json(false, '不能设置为源站CDN节点IP');

	$check = $site->checkSource($site_id, $ip, $port, $source_id);
	if(!empty($check)) json(false, '该源站记录已添加');

	$update = $site->updateSource($source_id, $ip, $type, $port);

	if($update > 0){

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

		// $data = json_encode($data);

		// $client= new GearmanClient();  
		// $client->addServer(); 
		// $client->doBackground('level', $data);

		// $data = array('site_id' => $site_id);
		// $data = json_encode($data);
		// $client= new GearmanClient();  
		// $client->addServer(); 
		// $client->doBackground('config', $data);
		
		json(true, '源站更新成功');
	}else json(false, '源站更新失败');

?>