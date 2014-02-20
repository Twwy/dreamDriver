<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file privateGroup.php 									// 
	//     														//
	//	/site.privateGroup										//
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

	$pgroup_id = filter('pgroup_id', '/^[0-9]{1,9}$/', '节点组ID格式错误');

	$sql = "SELECT * FROM pgroup WHERE pgroup_id = '{$pgroup_id}'";
	$pgroup = $user->db()->query($sql, 'row');
	if(empty($pgroup)) json(false, '节点组ID不存在');
	if($pgroup['user_id'] != $user_id) json(false, '无权查看他人节点组');

	$sql = "SELECT node_id FROM node_pgroup WHERE pgroup_id = '{$pgroup_id}'";
	$nodes = $user->db()->query($sql, 'array');

	json(true, '节点组内节点ID获取成功', $nodes);

?>