<?php
define('Perfect','ZED');
header("Content-type: text/html; charset=utf-8");
$system_path = 'framework';

error_reporting(0);
ini_set("display_errors", "Off");

define('ENVIRONMENT', 'debug'); //   debug || product

include ($system_path.'/core/Perfect.php');
$Perfect = new Perfect();
$Perfect->run();
?>