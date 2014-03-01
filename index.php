<?php

///////////////////////////////
//Design By Everyone's Dreams//
///////////////////////////////

require('config.php');

$docpathreg = str_replace('/', '\/', str_replace('?', '\?',PATH));
preg_match('/'.$docpathreg.'(.+)$/', $_SERVER['REQUEST_URI'], $match);
$uri = (empty($match)) ? 'default' : $match[1];


/*ROUTER*/
$router = Array();
function router($path, $func){
	global $router;
	$router[$path] = $func;
}

/*SESSSION*/
session_start();

/*DATABASE*/
require('./cores/database.php');
$db = new database(DBHOST, DBNAME, DBUSER, DBPASS);

/*JSON FORMAT*/
function json($result, $msg, $data = array()){
	if($result) exit(json_encode(array('result' => true, 'msg' => $msg , 'data' => $data)));
	exit(json_encode(array('result' => false, 'msg' => $msg, 'data' => $data)));
}

/*POST FILTER*/	//符合rule返回字符串，否则触发callback，optional为真则返回null
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

/*VIEW*/
function view($page, $data = Array(), $header = true, $footer = true){
	foreach ($data as $key => $value) $$key = $value;
	
	if(is_bool($header)){
		if($header) require('./views/header.html');
	}else require("./views/{$header}");

	require("./views/{$page}");

	if(is_bool($footer)){
		if($footer) require('./views/footer.html');
	}else require("./views/{$footer}");

}

/*MODEL*/
class model{
	function db(){
		global $db;
		return $db;
	}
}
function model($value){
	require("./models/{$value}.php");
	return new $value;
}

/*COMMON FUNCTION*/
require('./cores/common.php');

/*================路由表<开始>========================*/

//LOAD VIEW
require('./cores/view.php');
//LOAD ACTION
require('./cores/action.php');

/*================路由表<结束>========================*/

/*TRAVERSE ROUTER*/
foreach ($router as $key => $value) if(preg_match('/^'.$key.'$/', $uri, $matches)) exit($value($matches));
header('Location: '.PATH.'404');


?>
