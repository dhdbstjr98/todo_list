<?php
if(!defined("_INCLUDED_")) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

include_once("./class/Todo.class.php");
include_once("./class/Notification.class.php");

try {
	required_param("subject", "content", "deadline");
	included_param("star");								// star는 0이 될 수 있음

	$todo = Todo::insert($subject, $content, $deadline, $star);
	Notification::insert("insert", $todo->get_no());

	json_result(true, "success", ["todo"=>$todo]);
} catch(EmptyParamException $e) {
	json_result(false, "empty_param", ["param"=>$e->get_data()]);
} catch(InvalidParamException $e) {
	json_result(false, "invalid_column", ["column"=>$e->get_data()]);
} catch(Exception $e) {
	json_result(false, "unknown_error");
}