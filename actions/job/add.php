<?php


	//////////////////////////////////////////////////////////////
	//															//
	//	file add.php 											// 
	//     														//
	//	/job-add												//
	//                                        	        		//
	//															//
	//	http://twwy.net                              	    	//
	//                                                  		//
	//////////////////////////////////////////////////////////////

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$name = filter('name', '/^[a-zA-Z0-9\x{4e00}-\x{9fa5}\-\_\.]{1,255}$/u', '格式错误，只支持中文英文数字-_.');

	$job = model('job');
	
	//同时进行任务数上限查询
	$userInfo = $user->get($user_id);
	if(empty($userInfo)) json(false, '用户不存在');
	if($userInfo['undone_job'] > 0){
		$count = $job->undone($user_id);
		if($count >= $userInfo['undone_job']) json(false, '同时进行的任务已到上限');
	}

	//任务名查重
	$userJob = $job->getByName($name, $user_id);
	if(!empty($userJob)) json(false, '任务名重复');

	//添加站点
	$job_id = $job->add($user_id, $name, time());
	if($job_id == 0) json(false, '任务添加失败');

	json(true, '任务添加成功', array('job_id' => $job_id));

?>