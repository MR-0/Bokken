<?php

class DataBase {

	public static $connection;

	public static function init ($host, $user, $password, $name, $port = null, $socket = null) {

		// host, user, password, name, port, socket
		self::$connection = new mysqli($host, $user, $password, $name);
		self::$connection->set_charset("utf8");
		
		if (self::$connection->connect_error) {
			error_log(self::$connection->error_list);
			return null;
		}

		return self::$connection;
	}

	private static function scape ($str) {
		return self::$connection->real_escape_string($str);
	}

	private static function scapeArray ($data) {
		$scaped = array();
		
		foreach ($data as $key => $value) {
			$key = self::scape($key);
			$value = self::scape($value);
			$scaped[$key] = $value;
		}
		
		return $scaped;
	}

	public static function query ($query) {
		return self::$connection->query("$query");
	}

	public static function insert ($table, $data) {
		$table = self::scape($table);
		$data = self::scapeArray($data);
		$keys = array_keys($data);
		$cols = implode(', ', $keys);
		$vals = "'". implode("', '", $data) ."'";
		
		return self::$connection->query("INSERT INTO $table ($cols) VALUES ($vals)");
	}
	
	public static function select ($table, $data = null, $type = 'AND') {
		$table = self::scape($table);
		$cols = 1;
		
		if ($data) {
			$data = self::scapeArray($data);
			$cols = array();
			foreach ($data as $key => $value) {
				$cols[] = "$key = '$value'";
			}
			$cols = implode(" $type ", $cols);
		}
		
		return self::$connection->query("SELECT * FROM $table WHERE $cols");
	}

	public static function selectOr ($table, $data = null) {
		return self::select($table, $data, 'OR');
	}

	public static function count ($table) {
		$table = self::scape($table);
		$query = self::$connection->query("SELECT COUNT(1) FROM $table");
		return $query ? $query->fetch_row()[0] : null;
	}
	
	public static function update ($table, $data) {
		$table = self::scape($table);
		$data = self::scapeArray($data);
		$keys = array_keys($data);
		$where_key = $keys[0];
		$where_val = array_shift($data);
		$where = "$where_key = '$where_val'";
		$sets = array();
		
		foreach ($data as $k => $v) {
			$sets[] = "$k = '$v'";
		}
		$sets = implode(', ', $sets);
		
		return self::$connection->query("UPDATE $table SET $sets WHERE $where");
	}

	public static function open (
		$host = '',
		$user = '',
		$pass = '',
		$name = ''
		) {
			return self::init( $host, $user, $pass, $name );
		}

	public static function close () {
		return self::$connection->close();
	}
}