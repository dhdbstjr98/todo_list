<?php
if(!defined("_INCLUDED_")) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

include_once("./class/Todo.class.php");
include_once("./class/Notification.class.php");

try {
	required_param("no");
	included_param("subject","content","deadline","star","is_done");

	$todo = Todo::get_todo($no);
	$todo->edit($subject, $content, $deadline, $star, $is_done);
	
	if($subject !== null && $subject !== $todo->get_subject())
		Notification::insert("edit", $todo->get_no());
	elseif($content !== null && $content !== $todo->get_content())
		Notification::insert("edit", $todo->get_no());
	elseif($deadline !== null && $deadline !== $todo->get_deadline())
		Notification::insert("edit", $todo->get_no());

	if($deadline !== null) {
		Notification::trace_impending($todo->get_no());
		Notification::trace_dead($todo->get_no());
	}

	if($is_done !== null) {
		if($is_done)
			Notification::insert("done", $todo->get_no());
		else
			Notification::remove("done", $todo->get_no());
	}

	if($star !== null)
		Notification::insert("star", $todo->get_no());

	json_result(true, "success");
} catch(EmptyParamException $e) {
	json_result(false, "empty_param", ["param"=>$e->get_data()]);
} catch(InvalidParamException $e) {
	json_result(false, "invalid_column", ["column"=>$e->get_data()]);
} catch(NonExistentTodoException $e) {
	json_result(false, "nonexistent_todo");
} catch(Exception $e) {
	json_result(false, "unknown_error");
}