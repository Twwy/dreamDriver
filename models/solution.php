<?php

// +----------------------------------------------------------------------+
// | Warning: Design By Everyone's Dreams                                 |
// +----------------------------------------------------------------------+
// | FileName: solution.php                                                |
// +----------------------------------------------------------------------+
// | Version: 1.0                                                         |
// +----------------------------------------------------------------------+
// | Author: Twwy                                                         |
// | Email: twwwwy@gmail.com                                              |
// +----------------------------------------------------------------------+


class solution extends model{

	public function add($job_id, $problem_id, $content, $html){
		$time = time();
		$insertArray = array(
			'problem_id' => $problem_id,
			'job_id' => $job_id,
			'html' => $html,
			'content' => $content,
			'creat_time' => $time,
			'last_update_time' => $time,
		);
		$result = $this->db()->insert('solution', $insertArray);
		if($result == 0) return false;
		$id = $this->db()->insertId();
		return $id;
	}

	public function get($solution_id){
		$sql = "SELECT * FROM solution WHERE solution_id = '{$solution_id}'";
		return $this->db()->query($sql, 'row');
	}

	public function update($updateArray, $where){
		return $this->db()->update('solution', $updateArray, $where);
	}

	public function remove($where){
		return $this->db()->del('solution', $where);
	}

	
	// public function solutionShow($problem_id){
	// 	$sql = "SELECT * FROM solution WHERE problem_id = '{$problem_id}'";
	// 	$result = $this->db()->query($sql, 'array');
	// 	$sql = "SELECT count(1) FROM solution WHERE problem_id = '{$problem_id}'";	
	// 	$count = $this->db()->query($sql, 'row');
	// 	return array($result, $count['count(1)']);
	// }
	

}
