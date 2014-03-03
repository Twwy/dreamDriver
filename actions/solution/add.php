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

// $problem_id = filter('problem_id', '/^[0-9]{1,9}$/', '问题ID格式错误');
// $content = filter('content', '/^[\s\S]+$/', '内容格式错误');

$problem_id = 1;
$content = '#dsds';

require('./libs/Parsedown.php');
$parsedown = new Parsedown();
$problem = model('problem');
$job = model('job');
$solution = model('solution');

$proInfo = $problem->get($problem_id);
if(empty($proInfo)) json(false, '问题不存在');
if($proInfo['done_time'] > 0) json(false, '该问题已完成，无法再操作');

$jobInfo = $job->get($proInfo['job_id']);
if(empty($jobInfo)) json(false, '任务不存在');
if($jobInfo['user_id'] != $user_id) json(false, '用户无权操作该任务');
if($jobInfo['done_time'] > 0) json(false, '该任务已完成，无法再操作');

$html = $parsedown->parse($content);
$id = $solution->add($proInfo['job_id'], $problem_id, $content, $html);
if($id == 0) json(false, '解决方案添加失败');

json(true, '解决方案问题添加成功');

?>