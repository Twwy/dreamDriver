<?php


// +----------------------------------------------------------------------+
// | Warning: Design By Everyone's Dreams                                 |
// +----------------------------------------------------------------------+
// | FileName: passwd.php                                                 |
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

$newPass = filter('newPass', '/^.{6,40}$/', '新密码需要为6-40位字符');
$oldPass = filter('oldPass', '/^.{6,40}$/', '旧密码需要为6-40位字符');

$info = $user->get($user_id);

if(empty($info)) json(false, '用户不存在');

$enPass = $user->passEncode($oldPass, $info['usalt']);
if(strcasecmp($enPass, $info['passwd']) !== 0) json(false, '旧密码错误');

$newSalt = random('str', 27);
$enPass = $user->passEncode($newPass, $newSalt);
$updateArray = array(
	'usalt' => $newSalt,
	'passwd' => $enPass
);
$update = $user->update($user_id, $updateArray);
if($update > 0) json(true, '密码更新成功');
json(false, '密码未更新');


?>