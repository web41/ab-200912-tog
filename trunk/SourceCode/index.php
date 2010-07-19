<?php

define('PRADO_CHMOD',0755);

// Re-define time zone
if (function_exists('date_default_timezone_set'))
	date_default_timezone_set('Asia/Singapore');

// Alex: fix PATH_INFO problem
//if (!isset($_SERVER["PATH_INFO"]) && isset($_SERVER["ORIG_PATH_INFO"]))
//	$_SERVER["PATH_INFO"] = $_SERVER["ORIG_PATH_INFO"];

$frameworkPath='../prado3.1.7/framework/prado.php';

// The following directory checks may be removed if performance is required
$basePath=dirname(__FILE__);
$assetsPath=$basePath.'/assets';
$runtimePath=$basePath.'/protected/runtime';

if(!is_file($frameworkPath))
	die("Unable to find prado framework path $frameworkPath.");
if(!is_writable($assetsPath))
	die("Please make sure that the directory $assetsPath is writable by Web server process.");
if(!is_writable($runtimePath))
	die("Please make sure that the directory $runtimePath is writable by Web server process.");


require_once($frameworkPath);

$application=new TApplication;
$application->run();

?>