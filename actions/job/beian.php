<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file sit-beian.php 										// 
	//     														//
	//	/site.beian												//
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
	if($siteInfo['last_beian_check'] > time() - 60 * 10) json(false, '请勿频繁进行备案查询(10分钟内)');

	$domain = $siteInfo['domain'];

	
	// $topDomain = false;
	// $topDomain2 = false;

	// $dArray = explode('.', $domain);
	// $topDomain = $dArray[count($dArray) - 2].'.'.$dArray[count($dArray) - 1];
	// if(count($dArray) >= 3) $topDomain2 = $dArray[count($dArray) - 3].'.'.$dArray[count($dArray) - 2].'.'.$dArray[count($dArray) - 1];

	// $beian = $site->beian($topDomain);
	// if(!$beian) json(false, '备案查询问题出现故障，请稍后再试。');

	// if($beian['record'] != true && $topDomain2){		//再试一下三级域名
	// 	$beian = $site->beian($topDomain2);
	// 	if(!$beian) json(false, '备案查询问题出现故障，请稍后再试。');
	// }

	// if($beian['record'] == true){
	// 	$info = @iconv('GBK', 'UTF-8//IGNORE', $beian['info']);
	// 	$site->update($site_id, array('cname_type' => 0,'last_beian_check' => time(), 'beian' => $info));
	// 	json(true, '站点已备案，切换至国内CNAME');
		
	// }else{
	// 	$site->update($site_id, array('last_beian_check' => time()));
	// 	$info = @iconv('GBK', 'UTF-8//IGNORE', $beian['info']);
	// 	if(empty($info)) $info = '未进行备案';
	// 	json(false, $info);
	// }

	$beian = $site->beian($domain);
	if($beian){
		if($beian != 'no'){
			$site->update($site_id, array('cname_type' => 0,'last_beian_check' => time(), 'beian' => $beian));
			json(true, '站点已备案，切换至国内DNS服务器');
		}else{
			$site->update($site_id, array('last_beian_check' => time()));
			json(false, '未进行备案');
		}
	}else json(false, '备案查询系统繁忙');


?>