<?php
if(!defined("_INCLUDED_")) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

include_once("./class/Todo.class.php");

try {
	json_result(true, "success", ["count"=>Todo::get_count()]);
} catch(Exception $e) {
	json_result(false, "unknown_error");
}