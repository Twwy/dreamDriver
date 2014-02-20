<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file reg.php											//
	//															//
	//	user-reg												// 
	//															//
	//	http://twwy.net											//
	//															//
	//////////////////////////////////////////////////////////////


	$mail = filter('mail', '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix', '邮箱格式不符');
	$pass = filter('pass', '/^.{6,40}$/', '密码需要为6-40位字符');

	// $mail = 'zje2008@test.com';
	// $pass = 'b123456';

	$user = model('user');

	$info = $user->get($mail, 'mail');
	if(!empty($info)) json(false, '该邮箱已注册');

	//创建用户
	$user_id = $user->add($mail, $pass);			
	if($user_id === false) json(false, '创建失败');	

	//写入session,记录最后登录时间
	$user->login($user_id, $data);

	setcookie('mail', $mail, time() + 3600 * 24 * 30, '/');

	json(true, '用户注册成功', $user_id);

?>