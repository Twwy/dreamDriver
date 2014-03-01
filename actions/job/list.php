<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file list 												// 
	//     														//
	//	/site.list												//
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

	$search = filter('search', '/^[a-zA-Z0-9\x{4e00}-\x{9fa5}\-\_\.]{0,255}$/u', '格式错误，只支持中文英文数字-_.');

	$pagenum = 1;

	$site = model('site');
	$limit = 10;
	$start = ($pagenum - 1) * $limit;
	
	if(empty($search)) list($list, $count) = $site->user($user_id, $start, $limit);
	else list($list, $count) = $site->user($user_id, $start, $limit, $search);

	foreach ($list as $key => $value) {
		$list[$key]['level'] = $site->getLevel($value['site_id']);
	}

	$data = array(
		'list' => $list,
		'total' => $count,
		'limit' => $limit,
		'pagenum' => $pagenum
	);

	json(true, '获取成功', $data);

?>