<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file remark.php 										// 
	//     														//
	//	/site.remark											//
	//                                        	        		//
	//	This file is part of the openCDN project				//
	//															//
	//	http://ocdn.me                              	    	//
	//                                                  		//
	//////////////////////////////////////////////////////////////

	$user = model('user');
	$user_id = $user->sessionCheck(function(){
		json(false, '未登录');
	});

	$remark = filter('remark', '/^[a-zA-Z0-9\x{4e00}-\x{9fa5}\-\_\.]{0,255}$/u', '格式错误，只支持中文英文数字-_.');
	$site_id = filter('site_id', '/^[0-9]{1,9}$/', '站点ID格式错误');

	$len = mb_strlen($remark, 'utf8');
	if($len > 20) json(false, '最多20个字符');

	$site = model('site');
	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');

	$return = $site->update($site_id, array('remark' => $remark));

	if($return > 0){
		json(true, '备注更改成功');
	}else json(false, '备注未更改');


?>