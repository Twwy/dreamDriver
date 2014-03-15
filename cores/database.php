<?php

///////////////////////////////
//Design By Everyone's Dreams//
///////////////////////////////

class database{
	private $dbObj = false;

	function __construct($dbHost, $dbName, $dbUser, $dbPass){
		$this->dbObj = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=UTF8", $dbUser, $dbPass, Array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES'UTF8';"));
		$this->dbObj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function query($sql, $type = 'array'){
		//$sql = $this->dbObj->quote($sql);
		switch($type){
			case 'array':
				$dbObj = $this->dbObj->query($sql);
				if(!$dbObj) return false;
				$result = $dbObj->fetchAll(PDO::FETCH_ASSOC);
				break;
			case 'row':
				$dbObj = $this->dbObj->query($sql);
				if(!$dbObj) return false;
				$result = $dbObj->fetch(PDO::FETCH_ASSOC);
				break;
			case 'exec':
				$result = $this->dbObj->exec($sql);
				break;
		}
		if($result) return $result;
		else return Array();
	}

	public function insert($table, $insertArray){
		$query = "INSERT INTO {$table} ";
		if(isset($insertArray[0])){			//批量插入
			foreach ($insertArray as $inkey => $inValue) {
				$values = array_values($inValue);
				foreach($values as $key => $value) $values[$key] = $this->dbObj->quote($value);
				$values = implode(',', $values);
				if($inkey == 0){		//第一个插入要加字段

					$columns = array_keys($inValue);
					foreach($columns as $key => $value) $columns[$key] = "{$table}.{$value}";
					$columns = implode(',', $columns);
					
					$query .= " ({$columns}) VALUES ({$values}) ";
				}else{
					$query .= " ,({$values}) ";
				}
			}
		}else{
			$columns = array_keys($insertArray);
			$values = array_values($insertArray);
			foreach($values as $key => $value) $values[$key] = $this->dbObj->quote($value);
			foreach($columns as $key => $value) $columns[$key] = "{$table}.{$value}";
			$columns = implode(',', $columns);
			$values = implode(',', $values);
			$query .= " ({$columns}) VALUES ({$values}) ";
		}
		return $this->dbObj->exec($query);
	}

	public function update($table, $updateArray, $where){
		$updates = Array();
		foreach ($updateArray as $key => $value){
			if($value != NULL) $updates[] = $key.'='.$this->dbObj->quote($value);
			else $updates[] = $key.'= NULL';
		}
		unset($updateArray);
		$updates = implode(',', $updates);
		$query = "UPDATE {$table} SET {$updates} WHERE {$where}";
		return $this->dbObj->exec($query);
	}

	public function del($table, $where){
		$query = "DELETE FROM $table WHERE {$where}";
		return $this->dbObj->exec($query);
	}

	public function insertId(){
		return $this->dbObj->lastInsertId(); 
	}

	public function quote($value){
		return $this->dbObj->quote($value);
	}
}

?>