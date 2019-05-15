<?php
error_reporting(E_ALL);
ini_set("display_errors", "on");

if(!defined("_INCLUDED_")) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

define("_TIME_", time());
define("_DATETIME_", date("Y-m-d H:i:s", _TIME_));

include_once("./class/DB.class.php");
include_once("./db_setup.php");

function json_result($result, $code, $data=null) {
	if($data == null)
		echo json_encode(["result"=>$result, "code"=>$code], JSON_UNESCAPED_UNICODE);
	else
		echo json_encode(["result"=>$result, "code"=>$code, "data"=>$data], JSON_UNESCAPED_UNICODE);
	exit;
}

// 파라미터를 전역변수화함. 없으면 exception 발생
function required_param() {
	$params = func_get_args();

	foreach($params as $param) {
		if(empty($_REQUEST[$param]))
			throw new EmptyParamException($param);
		
		$GLOBALS[$param] = $_REQUEST[$param];
	}
}

// 파라미터를 전역변수화함. 없어도 exception 발생하지 않음.
function included_param() {
	$params = func_get_args();

	$result = array();

	foreach($params as $param) {
		if(!empty($_REQUEST[$param])) {
			$GLOBALS[$param] = $_REQUEST[$param];
			$result[$param] = $_REQUEST[$param];
		} else {
			$GLOBALS[$param] = false;
		}
	}

	return $result;
}

abstract class UsingParamException extends Exception {
	private $data;

	public function __construct($data) {
		$this->data = $data;
	}

	public function get_data() {
		return $this->data;
	}
};

class EmptyParamException extends UsingParamException {};
class InvalidParamException extends UsingParamException {};