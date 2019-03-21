<?php
    checkRoute();
	$errors = array();
	$data = array();
	if ($_SERVER['REQUEST_METHOD'] != "POST") {
		array_push($errors, addError($_ERRORS['onlyPOST'][0], $_ERRORS['onlyPOST'][1]));
		http_response_code(405);
		ethrow($errors);
	}
    $user_id = auth();
	$needFields = array(
		"hashDevice" => true,
		"token" => true
	);
	foreach ($needFields as $key => $value) {
		if ($value == true && !$_POST[$key]) {
			array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `".$key."`"));
			http_response_code(400);
		}
	}
	ethrow($errors);
	$tokenPushUpdate = tokenPushUpdate($_POST['hashDevice'], $_POST['token'], $user_id);
	array_push($data, array("status" => true));
	echo responseBuilder($errors, $data);