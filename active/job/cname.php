<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file sit-cname.php 										// 
	//     														//
	//	/site.cname												//
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

	$site_id = filter('site_id', '/^[0-9]{1,9}$/', '站点ID格式错误');

	$site = model('site');
	$siteInfo = $site->get($site_id);
	if(empty($siteInfo)) json(false, '站点不存在');
	if($siteInfo['user_id'] != $user_id) json(false, '无权操作他人的站点');

	$cname = dns_get_record($siteInfo['domain'], DNS_CNAME);
	// print_r($cname);

	$check = array('cname_valid' => 0, 'ucname_valid' => 0);
	foreach ($cname as $value) {
		if($siteInfo['cname_type'] == 0 && $siteInfo['cname'] == $value['target']){
			$check['cname_valid'] = 1;
		}
		if($siteInfo['cname_type'] == 1 && $siteInfo['ucname'] == $value['target']){
			$check['ucname_valid'] = 1;
		}
	}
	// print_r($check);

	$return = $site->update($site_id, $check);

	json(true, 'DNS记录检测完毕');



?>