<?php

// +----------------------------------------------------------------------+
// | Warning: Design By Everyone's Dreams                                 |
// +----------------------------------------------------------------------+
// | FileName: list.php                                                   |
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

$page = filter('page', '/^[0-9]{1,9}$/', '显示第几页格式错误', 1);
$limit = filter('limit', '/^[0-9]{1,9}$/', '每页显示数格式错误', 10);
$type = filter('type', '/^0|1|2$/', '类型错误(0|1|2)', 0);
$search = filter('search', '/^[a-zA-Z0-9\x{4e00}-\x{9fa5}\-\_\.]{1,255}$/u$/', '格式错误，只支持中文英文数字-_.', '');

$job = model('job');
$start = ($page - 1) * $limit;
if(empty($search)) $search = false;

if($type == 1){
	list($list, $count) = $job->user($user_id, $start, $limit, 0, $search);
}elseif($type == 2){
	list($list, $count) = $job->user($user_id, $start, $limit, 1, $search);
}else{
	list($list, $count) = $job->user($user_id, $start, $limit, -1, $search);
}

$rs = array(
	'total' => $count,
	'list' => $list,
	'page' => $page,
	'limit' => $limit,
	'search' => $search
);

json(true, '获取', $rs);

?>