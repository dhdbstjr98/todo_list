<?php
if(!defined("_INCLUDED_")) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

class DB {
	static private $db;

	static public function init($host, $db, $user, $password) {
		self::$db = new PDO("mysql:host={$host};dbname={$db}", $user, $password);
	}

	static public function prepare($sql) {
		return self::$db->prepare($sql);
	}

	static public function query($sql, $array=[]) {
		$st = self::prepare($sql);
		$st->execute($array);
		return $st;
	}

	static public function fetch($sql, $array=[]) {
		$st = self::query($sql, $array);
		return $st->fetch(PDO::FETCH_ASSOC);
	}

	static public function fetch_all($sql, $array=[]) {
		$st = self::query($sql, $array);
		return $st->fetchAll(PDO::FETCH_ASSOC);
	}

	static public function insert_id() {
		return self::$db->lastInsertId();
	}
}