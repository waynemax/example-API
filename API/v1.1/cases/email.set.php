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
		"email" => true
	);
	foreach ($needFields as $key => $value) {
		if ($value == true && !$_IN_[$key]) {
			array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `".$key."`"));
			http_response_code(400);
			ethrow($errors);
		}
	}
    $user_id = auth();
	$email = $_IN_['email'];
	if (emailExists($email)) {
		array_push($errors, addError($_ERRORS['same'][0], $_ERRORS['same'][1]." `email`"));
		http_response_code(400);
		ethrow($errors);
	}
	removeCodesActivation($email);
	$addCodeActivation = addCodeActivation($email, 86400);
	$m = new Mail('utf-8');
	$m->From('***');
	$m->ReplyTo('***'); // куда ответить
	$m->To($email);
	$m->Subject("Подтверждение email");
	$m->Body("Для подтверждения email перейдите по следующей ссылке: http://***/email/confirm?code=".$addCodeActivation['code']);
	$m->Priority(4); // установка приоритета
	$m->smtp_on(
		$strings['smtp']['host'],
		$strings['smtp']['from'],
		$strings['smtp']['pass'],
		$strings['smtp']['port'], 10
	);
	$m->Send();
	$data[0]['status'] = 'success';
	$data[0]['code'] = $addCodeActivation['code'];
	echo responseBuilder($errors, $data);