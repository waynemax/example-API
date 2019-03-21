<?php
    checkRoute();
	$strings = strings();
	$errors = array();
	$data = array();
	$_IN_ = $_POST;
	if ($_SERVER['REQUEST_METHOD'] != "POST") {
		array_push($errors, addError($_ERRORS['onlyPOST'][0], $_ERRORS['onlyPOST'][1]));
		http_response_code(405);
		ethrow($errors);
	}
	$needFields = array(
		"password" => true,
		"newPassword" => true
	);
	foreach ($needFields as $key => $value) {
		if ($value == true && !$_IN_[$key]) {
			array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `".$key."`"));
			http_response_code(400);
		}
	}
	ethrow($errors);
    $user_id = auth();
	$passwordUpdate = passwordUpdate($user_id, $_POST['password'], $_POST['newPassword']);
    $data[0]['status'] = $passwordUpdate ? 'success' : 'fail';
	echo responseBuilder($errors, $data);