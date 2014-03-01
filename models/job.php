<?php

	//////////////////////////////////////////////////////////////
	//															//
	//	file job.php 											//
	//															//
	//	model job												// 
	//                                             	        	//
	//                                             	        	//
	//	http://twwy.net                               	  		//
	//                                                  		//
	//////////////////////////////////////////////////////////////


class job extends model{

	public function add($user_id, $name, $creat_time){
		$insertArray = array(
			'name' => $name, 
			'user_id' => $user_id,
			'creat_time' => time()
		);
		$result = $this->db()->insert('job', $insertArray);
		if($result == 0) return false;
		$id = $this->db()->insertId();
		return $id;
	}

	public function getByName($name, $user_id){
		$sql = "SELECT * FROM job WHERE user_id = '{$user_id}' AND name = '{$name}'";
		return $this->db()->query($sql, 'row');
	}

	public function undone($user_id){
		$sql = "SELECT count(1) FROM job WHERE user_id = '{$user_id}' AND done_time = 0";
		$rs = $this->db()->query($sql, 'row');
		return $rs['count(1)'];
	}

	public function user($user_id, $start, $limit, $key = false){
		if($key) $key = " AND name LIKE '%{$key}%'";
		else $key = '';
		$sql = "SELECT * FROM job WHERE user_id = '{$user_id}' {$key} ORDER BY creat_time DESC LIMIT {$start},{$limit}";
		$result = $this->db()->query($sql, 'array');
		$sql = "SELECT count(1) FROM job WHERE user_id = '{$user_id}' {$key}";	
		$count = $this->db()->query($sql, 'row');
		return array($result, $count['count(1)']);
	}

	public function get($job_id){
		$sql = "SELECT * FROM job WHERE job_id = '{$job_id}'";
		return $this->db()->query($sql, 'row');
	}

	public function problemAdd($job_id, $user_id, $describe, $show_time){
		$time = time();
		$insertArray = array(
			'describe' => $describe, 
			'user_id' => $user_id,
			'job_id' => $job_id,
			'show_time' => $show_time,
			'creat_time' => $time,
			'last_update_time' => $time
		);
		$result = $this->db()->insert('problem', $insertArray);
		if($result == 0) return false;
		$id = $this->db()->insertId();
		return $id;
	}

	public function problemShow($job_id, $start_time, $stop_time){
		$sql = "SELECT * FROM problem WHERE job_id = '{$job_id}' AND (show_time < {$stop_time} OR show_time > {$start_time}) LIMIT 0,1000";
		return $this->db()->query($sql, 'array');
	}
	
	public function solutionShow($problem_id){
		$sql = "SELECT * FROM solution WHERE problem_id = '{$problem_id}'";
		$result = $this->db()->query($sql, 'array');
		$sql = "SELECT count(1) FROM solution WHERE problem_id = '{$problem_id}'";	
		$count = $this->db()->query($sql, 'row');
		return array($result, $count['count(1)']);
	}
	

}
