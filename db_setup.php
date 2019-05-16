<?php
if(!defined("_INCLUDED_")) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

DB::init("{host}","{database}","{user}","{password}");
DB::query("SET CHARSET utf8mb4");