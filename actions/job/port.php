<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file port.php 											// 
	//     														//
	//	/site.port												//
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
	$port = filter('port', '/^[0-9]{1,5}$/', '端口格式错误');

	if($port <= 0 || $port > 65535) json(false, '端口号必须为1-65535之间');

	$site = model('site');
	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');

	if($port != 80){
		//如果变成非80端口，要把所有的非私有套餐全部关闭

		$client= new GearmanClient();  
		$client->addServer(); 
		$levels = $site->getLevel($site_id);

		foreach ($levels as $value) {
			if($value['level_id'] != 0 && $value['status'] == 0){
				$return = $site->levelSet($site_id, $value['level_id'], 'off');
				$data = array('site_id' => $site_id, 'level_id' => $value['level_id']);
				$data = json_encode($data);
				$client->doBackground('level', $data);
			}
		}
	}

	$updateArray = array('port' => $port);
	$update = $site->update($site_id, $updateArray);

	if($update > 0){
		//把私有节点设置为待分发状态
		$site->confUpdate($site_id, 0);

		json(true, '更改成功');

	}else json(false, '未进行更改');

?>