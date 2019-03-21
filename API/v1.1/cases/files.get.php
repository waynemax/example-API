<?php
    checkRoute();
	if ($_SERVER['REQUEST_METHOD'] != "GET") {
		array_push($errors, addError($_ERRORS['onlyGET'][0], $_ERRORS['onlyGET'][1]));
		http_response_code(405);
		ethrow($errors);
	}
	$errors = array();
	$data = array();
	$needFields = array(
		"id"
	);
	foreach ($needFields as $key => $value) {
		if (!$getParams[$value]) {
			array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `".$value."`"));
		} else {
			$getParams[$value] = escape($getParams[$value]);
		}
	}
	ethrow($errors);
	$fileInfo = fileGet($getParams['id']);
	array_push($data, array(
		"id" => $getParams['id'],
		"file" => $fileInfo['status'] ? $fileInfo['file'] : ['status' => 'false']
	));
	echo responseBuilder($errors, $data);