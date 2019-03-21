<?php
    checkRoute();
	$errors = array();
	$data = array();
	if ($_SERVER['REQUEST_METHOD'] != "GET") {
		array_push($errors, addError($_ERRORS['onlyGET'][0], $_ERRORS['onlyGET'][1]));
		http_response_code(405);
		ethrow($errors);
	}
	if (!$getParams['access_token']) {
		array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `access_token`"));
		ethrow($errors);
	}
	$getParams['access_token'] = escape($getParams['access_token']);
	$fetchAccess = sfetch("select * from tokens where access_token = '{$getParams[access_token]}'");
	if ($fetchAccess) {
		array_push($data, array("status" => "true"));
	} else {
		array_push($errors, addError("status", "false"));
	}
	echo responseBuilder($errors, $data);