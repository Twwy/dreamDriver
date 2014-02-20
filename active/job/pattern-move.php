<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file pattern-move.php 									// 
	//     														//
	//	/site.pattern.move										//
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
	$move = filter('move', '/^0|1$/', '移动格式错误');		//move:0-向上,move:1-向下

	$pattern = model('pattern');
	$patternContent = $pattern->get($pattern_id);
	if(empty($patternContent)) json(false, '该规则不存在');
	$site_id = $patternContent['site_id'];

	if($patternContent['type'] == 0 && $patternContent['rule'] == '/'){
		json(false, '根规则路径不允许移动');
	}

	$site = model('site');
	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');

	//找出下一个
	if($move == 0) $where = " AND weight < {$patternContent['weight']} ORDER BY weight DESC";
	else $where = " AND weight > {$patternContent['weight']} ORDER BY weight ASC";
	$sql = "SELECT * FROM site_pattern WHERE site_id = '{$site_id}' {$where} LIMIT 0, 1";
	$find = $site->db()->query($sql, 'row');

	if(empty($find) || ($find['rule'] == '/' && $find['type'] == 0)) json(false, '无法再移动');

	//交换weight值
	$updateArray = array('weight' => $find['weight']);
	$update = $pattern->update($pattern_id, $updateArray);

	$updateArray = array('weight' => $patternContent['weight']);
	$update = $pattern->update($find['site_pattern_id'], $updateArray);

	if($update == 0) json(false, '更新失败');

	//将配置conf_status设置为待分发
	$site->confUpdate($site_id);

	//补充任务，最近2分钟如果没有任务，就补充1个任务
	$count = $site->taskCheck($site_id, 60 * 2);
	if($count == 0){
		$site->taskComplete($site_id, 60, 1);
	}

	json(true, '更新成功');


?>