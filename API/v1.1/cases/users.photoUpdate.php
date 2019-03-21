<?php
    checkRoute();
	$errors = array();
	$data = array();
	if ($_SERVER['REQUEST_METHOD'] != "POST") {
		array_push($errors, addError($_ERRORS['onlyPOST'][0], $_ERRORS['onlyPOST'][1]));
		http_response_code(405);
		ethrow($errors);
	}
	$needFields = array(
		"photo_id"
	);
	foreach ($needFields as $key => $value) {
		if (!$_POST[$value]) {
			array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `".$value."`"));
			http_response_code(400);
		} else {
			$_POST[$value] = escape($_POST[$value]);
		}
	}
	ethrow($errors);
    $user_id = auth();
	$photoUpdate = userPhotoUpdate($user_id, $_POST['photo_id']);
	if (!$photoUpdate['status']) {
		array_push($errors, addError($_ERRORS['invalidField'][0], $_ERRORS['invalidField'][1]." `photo_id`"));
		http_response_code(400);
		ethrow($errors);
	}
	$data[0]['id'] = epost($_POST['photo_id']);
	$data[0]['status'] = 'success';
	$data[0]['response'] = $photoUpdate['file'];
	echo responseBuilder($errors, $data);