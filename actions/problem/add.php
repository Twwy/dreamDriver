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

$job_id = filter('job_id', '/^[0-9]{1,9}$/', '任务ID格式错误');
$describe = filter('describe', '/^[a-zA-Z0-9\x{4e00}-\x{9fa5}\-\_\.]{1,1023}$/u', '格式错误，只支持中文英文数字-_.');
$expect = filter('expect', '/^[0-9]{1,9}$/', '期望时间值为数字(秒)', 600);
$relates = filter('relates', '/^[0-9\,]{1,255}$/', '关联问题ID');

//初始化模型
$job = model('job');
$problem = model('problem');

$jobInfo = $job->get($job_id);
if(empty($jobInfo)) json(false, '任务不存在');
if($jobInfo['user_id'] != $user_id) json(false, '用户无权访问该任务');
if($jobInfo['expect_time'] > 0 && $expect > $jobInfo['expect_time']) json(false, '疑问的预计时间不能大于任务的预计时间');
if($jobInfo['done_time'] > 0) json(false, '该任务已完成，无法继续添加疑问');

//添加问题关联
if(!empty($relates)){
	$gets = array();
	$relates = array_unique(explode(',', $relates));
	foreach ($relates as $value) if(is_numeric($value)) $gets[] = $value;
	$problems = $problem->gets($gets);
	$addRelates = array();
	foreach ($problems as $value){
		if($value['job_id'] != $job_id) json(false, '不能关联不属于同一个任务的问题');
		$addRelates[] = $value['problem_id'];
	}
}

$id = $problem->add($job_id, $user_id, $describe, $expect);
if($id == 0) json(false, '问题添加失败');
$rs = $problem->addRelates($id, $addRelates);
if($rs == 0) json(false, '问题添加成功，问题关联添加失败');

json(true, '问题添加成功');

?>