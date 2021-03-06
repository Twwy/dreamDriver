<?php

// +----------------------------------------------------------------------+
// | Warning: Design By Everyone's Dreams                                 |
// +----------------------------------------------------------------------+
// | FileName: add.php                                                    |
// +----------------------------------------------------------------------+
// | Version: 1.0                                                         |
// +----------------------------------------------------------------------+
// | Author: Twwy                                                         |
// | Email: twwwwy@gmail.com                                              |
// +----------------------------------------------------------------------+

$user = model('user');
$user_id = $user->sessionCheck(function(){
	json(false, '未登录');
});

$name = filter('name', '/^[a-zA-Z0-9\x{4e00}-\x{9fa5}\-\_\.]{1,255}$/u', '格式错误，只支持中文英文数字-_.');
$expect = filter('expect', '/^[0-9]{1,9}$/', '期望时间值为数字(秒)', 3600);

$job = model('job');

//check the parallel job counts
$userInfo = $user->get($user_id);
if(empty($userInfo)) json(false, '用户不存在');
if($userInfo['parallel_job'] > 0){
	$count = $job->undone($user_id);
	if($count >= $userInfo['parallel_job']) json(false, '同时进行的任务已到上限');
}

//任务名查重
$userJob = $job->conflictName($name, $user_id);
if(!empty($userJob)) json(false, '任务名冲突。同时正在进行的任务，任务名称不能重复');

//添加站点
$job_id = $job->add($user_id, $name, time(), $expect);
if($job_id == 0) json(false, '任务添加失败');

json(true, '任务添加成功', array('job_id' => $job_id));

?>