<?php
    checkRoute();
	$errors = array();
	$data = array();
	$_IN_ = $_POST;
	if ($_SERVER['REQUEST_METHOD'] != "POST") {
		array_push($errors, addError($_ERRORS['onlyPOST'][0], $_ERRORS['onlyPOST'][1]));
		http_response_code(405);
		ethrow($errors);
	}
    $user_id = auth();
	$photoRemove = userPhotoRemove($user_id);
	if (!$photoRemove['status']) {
		array_push($errors, addError($_ERRORS['unknownError'][0], $_ERRORS['unknownError'][1]));
		http_response_code(500);
		ethrow($errors);
	}
	$data[0]['status'] = 'success';
	echo responseBuilder($errors, $data);