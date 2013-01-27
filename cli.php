<?php
if (isset($_SERVER['REMOTE_ADDR'])) {
	die('Command Line Only!');
}
set_time_limit(0);
$_SERVER['PATH_INFO'] = $_SERVER['REQUEST_URI'] = $argv[1];
require dirname(__FILE__) . '/index.php';
?>