<?php
if(!defined("_INCLUDED_")) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

include_once("./class/Todo.class.php");

try {
	included_param("star", "is_impending", "is_done");

	$list = Todo::get_list($star, $is_impending, $is_done);

	json_result(true, "success", ["list"=>$list]);
} catch(EmptyParamException $e) {
	json_result(false, "empty_param", ["param"=>$e->get_data()]);
} catch(InvalidParamException $e) {
	json_result(false, "invalid_column", ["column"=>$e->get_data()]);
} catch(Exception $e) {
	json_result(false, "unknown_error");
}