<?php

// +----------------------------------------------------------------------+
// | Warning: Design By Everyone's Dreams                                 |
// +----------------------------------------------------------------------+
// | FileName: login.php                                                  |
// +----------------------------------------------------------------------+
// | Version: 1.0                                                         |
// +----------------------------------------------------------------------+
// | Author: Twwy                                                         |
// | Email: twwwwy@gmail.com                                              |
// +----------------------------------------------------------------------+


$mail = filter('mail', '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix', '邮箱格式不符');
$pass = filter('pass', '/^.{6,40}$/', '密码需要为6-40位字符');

$user = model('user');
$info = $user->get($mail, 'mail');

if(empty($info)) json(false, '邮箱不存在，登录失败');

$enPass = $user->passEncode($pass, $info['usalt']);
if(strcasecmp($enPass, $info['passwd']) !== 0) json(false, '邮箱或密码错误，登录失败');

$data = array();
$user->login($info['user_id'], $data);

setcookie('mail', $mail, time() + 3600 * 24 * 30, '/');
json(true, '登录成功，欢迎回来');


?>