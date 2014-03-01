<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file source-remove.php 									// 
	//     														//
	//	/site.source-remove										//
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
	
	$site = model('site');

	$source = $site->getSource(0, $source_id);

	if(empty($source)) json(false, '源站记录不存在');

	$site_id = $source['site_id'];
	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');

	$list = $site->getSource($site_id);
	if(count($list) < 2) json(false, '至少要有一条源站记录');

	$remove = $site->removeSource($source_id);
	if($remove > 0){
		
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

		json(true, '源站记录删除成功');
	}else json(false, '源站记录删除失败');

?>