<?php

router('default',function(){
	$user = model('user');
	$user_id = $user->sessionCheck(function(){return false;});
	if($user_id) exit(header('Location: ./main'));
	exit(header('Location: ./board'));
});

router('reg',function(){
	view('reg.html', array());
});

router('board',function(){
	view('board.html', array());
});

router('job-add',function(){
	$user = model('user');
	$user_id = $user->sessionCheck();

	$data = array('login' => true);

	view('job-add.html', $data);
});

router('login',function(){
	view('login.html', array());
});

router('404',function(){
	$user = model('user');
	$user_id = $user->sessionCheck(function(){});
	if(empty($user_id)) $login = false;
	else $login = true;
	$data = array('login' => $login);
	if($login) $data['session_data'] = $_SESSION['data'];
	view('404.html', $data);
});

router('main(\-([0-9]{1,9}))?',function($matches){
	$user = model('user');
	$user_id = $user->sessionCheck();

	$pagenum = 1;
	if(isset($matches[2])) $pagenum = $matches[2];

	$job = model('job');
	$limit = 10;
	$start = ($pagenum - 1) * $limit;

	list($list, $count) = $job->user($user_id, $start, $limit);

	$data = array(
		'login' => true, 
		'session_data' => $_SESSION['data'],
		'list' => $list,
		'total' => $count,
		'limit' => $limit,
		'pagenum' => $pagenum,
	);

	view('main.html', $data);
});

// router('job\-([0-9]{1,9})',function($matches){		//用于跳转
// 	$user = model('user');
// 	$user_id = $user->sessionCheck();
	
// 	$job = model('job');
// 	$job_id = $matches[1];
// 	$jobInfo = $job->get($job_id);
// 	if(empty($jobInfo)) exit('该任务不存在');
// 	if($jobInfo['user_id'] != $user_id) exit('任务无权访问');

// 	$date = date('Y-m-d');
// 	header('Location: ./board');


// });

router('job\-([0-9]{1,9})\?([0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2})',function($matches){
	$user = model('user');
	$user_id = $user->sessionCheck();
	
	$job = model('job');
	$job_id = $matches[1];
	$jobInfo = $job->get($job_id);
	if(empty($jobInfo)) exit('该任务不存在');
	if($jobInfo['user_id'] != $user_id) exit('任务无权访问');

	$date = $matches[2];
	$time = strtotime("{$date} 0:0:0");
	$start_time = $time;
	$stop_time = $time + 60 * 60 * 24;

	$problem = $job->problemShow($job_id, $start_time, $stop_time);

	foreach ($problem as $key => $value) {
		# code...
	}

	$data = array(
		'login' => true,
		'session_data' => $_SESSION['data'],
		'info' => $jobInfo,
		'date' => date('Y年m月d日', $time),
		'problem' => $problem
	);

	view('job.html', $data);
});


router('setting',function($matches){
	$user = model('user');
	$user_id = $user->sessionCheck();
	
	$userInfo = $user->get($user_id);
	if(empty($userInfo)) exit('The User is not exists.');

	$path = array();
	$path[] = array('name' => '首页', 'href' => './main');
	$path[] = array('name' => '设置');

	$data = array(
		'login' => true,
		'session_data' => $_SESSION['data'],
		'user' => $userInfo,
		'navPath' => $path
	);

	view('setting.html', $data);
});


router('exit',function($matches){
	$user = model('user');
	$user_id = $user->sessionCheck();
	session_destroy();
	exit(header('Location: ./'));
});


?>