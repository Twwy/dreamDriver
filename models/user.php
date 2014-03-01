<?php


	//////////////////////////////////////////////////////////////
	//															//
	//	file user.php											//
	//															//
	//	model user												// 
	//															//
	//															//
	//	http://twwy.net											//
	//															//
	//////////////////////////////////////////////////////////////

class user extends model{

	public function add($mail,$pass){				//添加用户
		$salt = random('str', 27);
		$passwd = $this->passEncode($pass, $salt);
		$insertArray = array(
			'mail' => $mail, 
			'usalt' => $salt, 
			'passwd' => $passwd,
			'creat_time' => time()
		);
		$result = $this->db()->insert('user', $insertArray);
		if($result == 0) return false;
		return $this->db()->insertId();	
	}

	public function get($value, $type = 'user_id'){		//获取用户 mail 或 user_id和name
		$whereArray = array(
			'user_id' => " user_id = '{$value}' ",
			'mail' => " mail = '{$value}' ",
			'name' => " name = '{$value}' "
		);
		$sql = "SELECT * FROM user WHERE {$whereArray[$type]}";
		return $this->db()->query($sql, 'row');	
	}

	public function passEncode($pass, $salt){  		//密码加密 
		return md5("{$salt}2312twwylolo?{$salt}{$pass}--sd22445532");
	}

	public function sessionCheck($callback = false){		//session检查
		if(empty($_SESSION['user_id'])){
			if($callback) return $callback();
			else exit(header('Location: ./'));
		}else return $_SESSION['user_id'];
	}

	public function login($user_id, $data = array()){	//用户登录
		$_SESSION['user_id'] = $user_id;
		$_SESSION['data'] = $data;	//覆盖用户缓存数据

		$updateArray = array('last_login_time' => time());
		$result = $this->update($user_id, $updateArray);
		if($result > 0) return true;
		return false;
	}

	public function update($user_id, $updateArray){
		return $this->db()->update('user', $updateArray, "user_id = '{$user_id}'");
	}

}

?>
