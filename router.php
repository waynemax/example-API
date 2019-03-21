<?php
	$begin_time = time() - 1272000000 + floatval(microtime());
	require_once("setHeaders.php");
	define('ROOT_DIR', __DIR__);

    	$router = true; // dont touch
	function checkRoute() {
		global $router;
		if (!$router) {
		    header('HTTP/1.1 500 OK');
		    die;
		}
	}

	require_once(ROOT_DIR.'/data/config.php');
	require_once(ROOT_DIR.'/data/strings.php');
	require_once(ROOT_DIR.'/data/defines.php');
    	require_once(ROOT_DIR.'/data/fields.php');
	require_once(ROOT_DIR.'/data/errors.php');
	require_once(ROOT_DIR.'/data/regexp.php');
	require_once(ROOT_DIR.'/libs/curl.php');
    	require_once(ROOT_DIR.'/libs/mc.php');
	require_once(ROOT_DIR.'/libs/db.php');
	require_once(ROOT_DIR.'/libs/fcm.php');
    	require_once(ROOT_DIR.'/all_functions.php');
    	require_once(ROOT_DIR.'/routelinks.php');
