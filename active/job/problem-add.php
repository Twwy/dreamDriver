<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file problem-add.php 									// 
	//     														//
	//	/job.problem.add										//
	//                                        	        		//
	//	http://twwy.net                              	    	//
	//                                                  		//
	//////////////////////////////////////////////////////////////

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$time = time();

	$job_id = filter('job_id', '/^[0-9]{1,9}$/', '任务ID格式错误');
	$describe = filter('describe', '/^[a-zA-Z0-9\x{4e00}-\x{9fa5}\-\_\.]{1,1023}$/u', '格式错误，只支持中文英文数字-_.');
	$show_time = filter('show_time', '/^[0-9]{1,9}$/', '时间格式错误', $time);

	$job = model('job');
	$jobInfo = $job->get($job_id);
	if(empty($jobInfo)) json(false, '任务不存在');
	if($jobInfo['user_id'] != $user_id) json(false, '用户无法访问该任务');

	if($show_time < $jobInfo['creat_time']) json(false, '疑问时间不能早于任务创建时间');

	$id = $job->problemAdd($job_id, $user_id, $describe, $show_time);
	if($id == 0) json(false, '问题添加失败');

	json(true, '问题添加成功');

?>