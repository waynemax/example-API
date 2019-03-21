<?php
    checkRoute();
	$errors = array();
	$data = array();
	$_IN = $_POST;
	if ($_SERVER['REQUEST_METHOD'] != "POST") {
		array_push($errors, addError($_ERRORS['onlyPOST'][0], $_ERRORS['onlyPOST'][1]));
		http_response_code(405);
		ethrow($errors);
	}
	$needFields = array(
		"password"
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
	$salt = getUserSalt($user_id);
	$hashPassword = $_POST['password'];
	$realPass = hashUserPassword($hashPassword, $salt);
	$passdb = sfetch("select password from users where id = ".num($user_id).";");
	if ($realPass != $passdb['password']) {
		http_response_code(403);
		ethrow([addError(
			$_ERRORS['permission_denied'][0],
			$_ERRORS['permission_denied'][1]." `password`"
		)]);
	}
	$userDeactivate = userDeactivate($user_id);
	echo responseBuilder($errors, [[
		'status' => 'success'
	]]);