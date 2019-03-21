<?php
    checkRoute();
	$errors = array();
	$data = array();
	if ($_SERVER['REQUEST_METHOD'] != "GET") {
		array_push($errors, addError($_ERRORS['onlyGET'][0], $_ERRORS['onlyGET'][1]));
		http_response_code(405);
		ethrow($errors);
	}
	if (!$getParams['refresh_token']) {
		array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `refresh_token`"));
		ethrow($errors);
	}
	$getParams['refresh_token'] = escape($getParams['refresh_token']);
	$fetchRefresh = sfetch("select * from tokens where refresh_token = '{$getParams[refresh_token]}'");
	if (!$fetchRefresh) {
		array_push($errors, addError($_ERRORS['not_found'][0], $_ERRORS['not_found'][1]." `refresh_token`"));
		ethrow($errors);
	}
	$ua = apache_request_headers();
	$ua = escape(strtolower($ua['User-Agent']));
	$ip = $_SERVER['REMOTE_ADDR'];
	if ($fetchRefresh['ua'] != $ua && $fetchRefresh['ip'] != $ip) {
		array_push($errors, addError($_ERRORS['access_denied'][0], $_ERRORS['access_denied'][1]." `refresh_token`"));
		ethrow($errors);
	}
	$new_access_token = genSalt(32);
	$new_refresh_token = genSalt(32);
	$newRegTime = time();
	squery("update `tokens` set `reg_time` = '{$newRegTime}', `ip` = '{$ip}', `ua` = '{$ua}', `access_token` = '{$new_access_token}', `refresh_token` = '{$new_refresh_token}' where `id` = '{$fetchRefresh[id]}';");
	array_push($data, array(
		"access_token" => $new_access_token,
		"refresh_token" => $new_refresh_token,
		"expires_in" => $fetchRefresh['expires_in'],
		"reg_time" => $newRegTime,
		"user_id" => $fetchRefresh['user_id']
	));
	echo responseBuilder($errors, $data);