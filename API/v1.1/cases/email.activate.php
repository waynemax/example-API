<?php
    checkRoute();
	$errors = array();
	$data = array();
	$_IN_ = $_POST;
	$strings = strings();
	if ($_SERVER['REQUEST_METHOD'] != "POST") {
		array_push($errors, addError($_ERRORS['onlyPOST'][0], $_ERRORS['onlyPOST'][1]));
		http_response_code(405);
		ethrow($errors);
	}
	$needFields = array(
		"code" => true
	);
	foreach ($needFields as $key => $value) {
		if ($value == true && !$_IN_[$key]) {
			array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `".$key."`"));
			http_response_code(400);
			ethrow($errors);
		}
	}
    $user_id = auth();
	$code = $_IN_['code'];
	$checkCodeActivation = checkCodeActivation($code);
	if (!$checkCodeActivation['status']) {
		array_push($errors, addError($_ERRORS['unknownError'][0], $_ERRORS['unknownError'][1]." `code`"));
		http_response_code(400);
		ethrow($errors);
	}
	setUserEmailById($user_id, $checkCodeActivation['object_key']);
	removeCodesActivation($checkCodeActivation['object_key']);
	$data[0]['status'] = 'success';
	$data[0]['email'] = $checkCodeActivation['object_key'];
	echo responseBuilder($errors, $data);