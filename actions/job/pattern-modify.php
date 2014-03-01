<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file pattern-modify.php 								// 
	//     														//
	//	/site.pattern.modify									//
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
	$type = filter('type', '/^0|1$/', '类型格式错误');
	$rule = filter('rule', '/^[\|\/0-9a-zA-Z\*]{1,1000}$/', '规则格式错误');
	$keep_time = filter('keep_time', '/^[0-9\-]{1,9}$/', '缓存时间格式错误');

	$pattern = model('pattern');
	$patternContent = $pattern->get($pattern_id);
	if(empty($patternContent)) json(false, '该规则不存在');
	$site_id = $patternContent['site_id'];

	$site = model('site');
	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');

	//rule过滤
	if($type == 1){
		$rule = explode('|', $rule);
		foreach ($rule as $key => $value) $rule[$key] = strtolower($value);
		$rule = array_unique($rule);
		$frule = array();
		foreach ($rule as $value) {
			if(preg_match('/^[0-9a-zA-Z]{1,9}$/', $value)){
				$frule[] = $value;
			}
		}
		if(count($frule) == 0) json(false, '后缀不符合要求，请检查');
		$rule = implode('|', $frule);
	}else{
		if($patternContent['type'] == 0 && $patternContent['rule'] == '/'){
			if($rule != '/' || $type != 0) json(false, '根规则路径不允许修改');
		}
		if(substr($rule, 0, 1) != '/') $rule = "/{$rule}";
	}

	$has = $pattern->select($site_id, $rule, $type, $pattern_id);
	if(!empty($has)) json(false, '规则不能重复');

	$updateArray = array(
		'type' => $type,
		'rule' => $rule,
		'keep_time' => $keep_time,
		'last_update_time' => time()
	);
	$update = $pattern->update($pattern_id, $updateArray);
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