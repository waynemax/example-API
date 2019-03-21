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
		"uniqueKey" => true,
		"typeDevice" => true
	);
	foreach ($needFields as $key => $value) {
		if ($value == true && !$_POST[$key]) {
			array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `".$key."`"));
			http_response_code(400);
		}
	}
	ethrow($errors);
	$deAdd = deviceAdd(array(
		'ua' => $reqHeaders['User-Agent'],
		'uniqueKey' => $_POST['uniqueKey'],
		'typeDevice' => $_POST['typeDevice']
	));
	if (!$deAdd['status']) {
	    if ($deAdd['reason'] != "alreadyExist") {
            array_push($errors, addError($_ERRORS['same'][0], $_ERRORS['same'][1] . " `uniqueKey`"));
            http_response_code(400);
            ethrow($errors);
        } else {
            array_push($data, array(
                "hashDevice" => $deAdd['hashDevice'],
                "fcm_token" => $deAdd['fcm_token']
            ));
        }
	} else {
        array_push($data, array(
            "hashDevice" => $deAdd['hashDevice']
        ));
    }
	echo responseBuilder($errors, $data);