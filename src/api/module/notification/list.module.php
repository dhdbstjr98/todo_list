<?php
if(!defined("_INCLUDED_")) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

include_once("./class/Notification.class.php");

try {
	Notification::trace_impending();
	Notification::trace_dead();

	$list = Notification::get_list();

	json_result(true, "success", ["list"=>$list]);
} catch(Exception $e) {
	json_result(false, "unknown_error");
}