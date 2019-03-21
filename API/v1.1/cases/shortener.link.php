<?php
    checkRoute();
	$errors = array();
	$data = array();
    $_IN_ = checkInData("POST", ['systemPassword', 'link'], []);
	if ($_IN_['systemPassword'] != $config['services']['systemPassword']) {
		array_push($errors, addError($_ERRORS['access_denied'][0], $_ERRORS['access_denied'][1]." `systemPassword`"));
		http_response_code(403);
	}
	ethrow($errors);
	$data[0]['short'] = shortener($_IN_['link']);
	$data[0]['link'] = escape($_IN_['link']);
	echo responseBuilder($errors, $data);