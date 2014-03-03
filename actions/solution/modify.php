<?php

// +----------------------------------------------------------------------+
// | Warning: Design By Everyone's Dreams                                 |
// +----------------------------------------------------------------------+
// | FileName: modify.php                                                 |
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

$solution_id = filter('solution_id', '/^[0-9]{1,9}$/', '解决方案ID格式错误');
$content = filter('content', '/^[\s\S]+$/', '内容格式错误');

require('./libs/Parsedown.php');
$parsedown = new Parsedown();
$problem = model('problem');
$job = model('job');
$solution = model('solution');

$solInfo = $solution->get($solution_id);
if(empty($solInfo)) json(false, '解决方案不存在');
$problem_id = $solInfo['problem_id'];

$proInfo = $problem->get($problem_id);
if(empty($proInfo)) json(false, '问题不存在');
if($proInfo['done_time'] > 0) json(false, '该问题已完成，无法再更新');

$jobInfo = $job->get($proInfo['job_id']);
if(empty($jobInfo)) json(false, '任务不存在');
if($jobInfo['user_id'] != $user_id) json(false, '用户无权操作该任务');
if($jobInfo['done_time'] > 0) json(false, '该任务已完成，无法再操作');

$updateArray = array(
	'html' => $parsedown->parse($content),
	'content' => $content,
	'last_update_time' => time()
);
$id = $solution->update($updateArray, "solution_id = '{$solution_id}'");
if($id == 0) json(false, '解决方案未更新');

json(true, '解决方案问题更新成功');

?>