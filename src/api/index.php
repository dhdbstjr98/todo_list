<?php
define("_INCLUDED_", true);

include_once("./common.php");

try {
	required_param("module_category", "module");

	$module_file = "./module/{$module_category}/{$module}.module.php";

	if(!file_exists($module_file))
		throw new InvalidParamException("module");

} catch(EmptyParamException $e) {
	json_result(false, "invalid_api");
} catch(InvalidParamException $e) {
	json_result(false, "invalid_api");
}

include_once($module_file);
?>