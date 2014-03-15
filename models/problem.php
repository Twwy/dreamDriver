<?php

// +----------------------------------------------------------------------+
// | Warning: Design By Everyone's Dreams                                 |
// +----------------------------------------------------------------------+
// | FileName: problem.php                                                |
// +----------------------------------------------------------------------+
// | Version: 1.0                                                         |
// +----------------------------------------------------------------------+
// | Author: Twwy                                                         |
// | Email: twwwwy@gmail.com                                              |
// +----------------------------------------------------------------------+


class problem extends model{

	public function add($job_id, $user_id, $describe, $expect){
		$time = time();
		$insertArray = array(
			'describe' => $describe, 
			'user_id' => $user_id,
			'job_id' => $job_id,
			'creat_time' => $time,
			'last_update_time' => $time,
			'expect_time' => $expect
		);
		$result = $this->db()->insert('problem', $insertArray);
		if($result == 0) return false;
		$id = $this->db()->insertId();
		return $id;
	}

	public function get($problem_id){
		$sql = "SELECT * FROM problem WHERE problem_id = '{$problem_id}'";
		return $this->db()->query($sql, 'row');
	}

	public function gets($problems){
		$problemStr = implode(',', $problems);
		$sql = "SELECT * FROM problem WHERE problem_id in ({$problemStr})";
		return $this->db()->query($sql, 'array');
	}

	public function addRelates($problem_id, $relates){
		$insertArray = array();
		foreach ($relates as $value){
			$insertArray[] = array(
				'problem_id' => $problem_id,
				'related_problem_id' => $value
			);
		}
		return $this->db()->insert('problem_related', $insertArray);
	}

	// public function problemShow($job_id, $start_time, $stop_time){
	// 	$sql = "SELECT * FROM problem WHERE job_id = '{$job_id}' AND (show_time < {$stop_time} OR show_time > {$start_time}) LIMIT 0,1000";
	// 	return $this->db()->query($sql, 'array');
	// }
	
	// public function solutionShow($problem_id){
	// 	$sql = "SELECT * FROM solution WHERE problem_id = '{$problem_id}'";
	// 	$result = $this->db()->query($sql, 'array');
	// 	$sql = "SELECT count(1) FROM solution WHERE problem_id = '{$problem_id}'";	
	// 	$count = $this->db()->query($sql, 'row');
	// 	return array($result, $count['count(1)']);
	// }
	

}
