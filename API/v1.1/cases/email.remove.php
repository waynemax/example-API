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
    $user_id = auth();
	removeUserEmail($user_id);
	$data[0]['status'] = 'success';
	echo responseBuilder($errors, $data);