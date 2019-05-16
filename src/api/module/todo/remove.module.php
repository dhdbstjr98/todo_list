<?php
if(!defined("_INCLUDED_")) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

include_once("./class/Todo.class.php");

try {
	required_param("no");

	$todo = Todo::get_todo($no);
	$todo->remove();

	json_result(true, "success");
} catch(EmptyParamException $e) {
	json_result(false, "empty_param", ["param"=>$e->get_data()]);
} catch(NonExistentTodoException $e) {
	json_result(false, "nonexistent_todo");
} catch(Exception $e) {
	json_result(false, "unknown_error");
}