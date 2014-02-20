<?php

date_default_timezone_set('PRC');

preg_match('/\/driver\/(.+)$/', $_SERVER['REQUEST_URI'], $match);
// preg_match('/\/(.+)$/', $_SERVER['REQUEST_URI'], $match);
$uri = (empty($match)) ? 'default' : $match[1];


/*路由*/
$router = Array();
function router($path, $func){
	global $router;
	$router[$path] = $func;
}


/*会话*/
session_start();


/*数据库*/
require('database.php');


/*JSON格式*/
function json($result, $msg, $data = array()){
	if($result) exit(json_encode(array('result' => true, 'msg' => $msg , 'data' => $data)));
	exit(json_encode(array('result' => false, 'msg' => $msg, 'data' => $data)));
}


/*POST过滤器*/	//符合rule返回字符串，否则触发callback，optional为真则返回null
function filter($name, $rule, $callback, $optional = false){
	if($optional !== false){
		if(isset($_POST[$name])){
			if(preg_match($rule, $post = trim($_POST[$name]))) return $post;
			else{
				if(is_object($callback)) return $callback();
				else json(false, $callback, array('error_code' => 1));			
			}
		}elseif($optional === true) return null;
		else return $optional;
	}else{
		if(isset($_POST[$name]) && preg_match($rule, $post = trim($_POST[$name]))) return $post;
		else{
			if(is_object($callback)) return $callback();
			else json(false, $callback, array('error_code' => 1));			
		} 
	}

}


/*视图*/
function view($page, $data = Array(), $header = true, $footer = true){
	foreach ($data as $key => $value) $$key = $value;
	
	if(is_bool($header)){
		if($header) require('./view/header.html');
	}else require("./view/{$header}");

	require("./view/{$page}");

	if(is_bool($footer)){
		if($footer) require('./view/footer.html');
	}else require("./view/{$footer}");

}


/*模型*/
class model{
	function db(){
		global $db;
		return $db;
	}
}//model中转db类
function model($value){
	require("./model/{$value}.php");
	return new $value;
}

/*扩展通用函数*/
require('common.php');

/*================路由表<开始>========================*/

require('view.php');
require('active.php');

/*================路由表<结束>========================*/

/*路由遍历*/
foreach ($router as $key => $value) if(preg_match('/^'.$key.'$/', $uri, $matches)) exit($value($matches));

// if(substr($uri, 0, 3) == 'api') json(false, 'Unkown api');
// else header('Location: /404');
header('Location: /404');


?>
