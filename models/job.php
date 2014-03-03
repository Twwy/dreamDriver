<?php

// +----------------------------------------------------------------------+
// | Warning: Design By Everyone's Dreams                                 |
// +----------------------------------------------------------------------+
// | FileName: job.php                                                    |
// +----------------------------------------------------------------------+
// | Version: 1.0                                                         |
// +----------------------------------------------------------------------+
// | Author: Twwy                                                         |
// | Email: twwwwy@gmail.com                                              |
// +----------------------------------------------------------------------+


class job extends model{

	public function add($user_id, $name, $creat_time, $expect){
		$insertArray = array(
			'name' => $name, 
			'user_id' => $user_id,
			'creat_time' => time(),
			'expect_time' => $expect
		);
		$result = $this->db()->insert('job', $insertArray);
		if($result == 0) return false;
		$id = $this->db()->insertId();
		return $id;
	}

	public function conflictName($name, $user_id){
		$sql = "SELECT * FROM job WHERE user_id = '{$user_id}' AND name = '{$name}' AND done_time = 0";
		return $this->db()->query($sql, 'row');		
	}

	public function get($job_id){
		$sql = "SELECT * FROM job WHERE job_id = '{$job_id}'";
		return $this->db()->query($sql, 'row');
	}

	public function user($user_id, $start, $limit, $done, $key = false){
		if($key) $key = " AND name LIKE '%{$key}%'";
		else $key = '';
		if($done < 0) $done = '';
		elseif($done == 0) $done = 'AND done_time = 0';
		else $done = 'AND done_time > 0';
		$sql = "SELECT * FROM job WHERE user_id = '{$user_id}' {$done} {$key} ORDER BY creat_time DESC LIMIT {$start},{$limit}";
		$result = $this->db()->query($sql, 'array');
		$sql = "SELECT count(1) FROM job WHERE user_id = '{$user_id}' {$done} {$key}";	
		$count = $this->db()->query($sql, 'row');
		return array($result, $count['count(1)']);
	}

}
