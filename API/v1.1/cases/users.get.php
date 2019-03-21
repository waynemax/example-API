<?php
    checkRoute();
    $errors = array();
    $data = array();
    $mandatoryParams = [['user_ids','phones']];
    $optionalParams = [];
    $_IN_ = checkInData("GET", $mandatoryParams, $optionalParams);
    $user_id = auth(NULL,true);
	$errors = array();
	$data = array();
	$typeSearch = 'user_ids';
	if ($_IN_['phones']) {
        $typeSearch = 'phones';
    }
    if ($typeSearch == 'user_ids') {
        $user_ids = explode(",", escape($_IN_['user_ids']));
    } else {
	    $phones = explode(",", escape($_IN_['phones']));
    }
	if (count($user_ids) > 100 || count($phones) > 100) {
		array_push($errors, addError($_ERRORS['countLimitUsers'][0], $_ERRORS['countLimitUsers'][1]));
		http_response_code(400);
		ethrow($errors);
	}
    if ($typeSearch == 'user_ids') {
        $users = array();
        foreach ($user_ids as $key => $value) {
            if (preg_match("|^[0-9]{1,11}$|", $value, $match)) {
                array_push($users, $value);
            }
        }
    } elseif ($typeSearch == 'phones') {
	    $phonesTreatment = [];
        foreach ($phones as $key => $value) {
            $value = phone_formatter($value, true);
            array_push($phonesTreatment, $value);
        }
    }
	if (count($users) < 1 && count($phonesTreatment) < 1) {
        array_push($errors, addError($_ERRORS['unknownError'][0], $_ERRORS['unknownError'][1]." (2)"));
        http_response_code(400);
        ethrow($errors);
    }
    if ($typeSearch == 'user_ids') {
        $owner = false;
        if (count($user_ids) == 1 && $user_id) {
            if (num($user_ids[0]) == $user_id) {
                $owner = true;
            }
        }
	}
	if ($owner) {
		$fieldsStandart = [
			"id","first_name", "last_name", "patronymic", "nickname",
			"login", "domain", "last_seen", "full_name", "photo_id"
		];
	} else {
        $fieldsStandart = [
			"id", "first_name", "last_name", "patronymic", "nickname",
            "last_seen", "full_name", "photo_id"
		];
	}
    $fields = $fieldsStandart;
	if (!is_null($getParams['fields'])) {
		$queryFieldsIn = explode(",", $getParams['fields']);
		$queryFieldsOut  = [];
		foreach ($queryFieldsIn as $keyField => $fieldValue) {
			if (in_array($fieldValue, $fields)) {
				array_push($queryFieldsOut, $fieldValue);
			}
		}
		$fields = $queryFieldsOut;
	}
	if (count($fields) < 1) {
        $fields = $fieldsStandart;
    }
    $params = [
        "fields" => $fields,
        'withoutFields' => ['photo_id']
    ];
    if ($typeSearch == 'user_ids') {
        $params['users'] = $users;
    } elseif ($typeSearch == 'phones') {
        $params['phones'] = $phonesTreatment;
    }
    $params['typeSearch'] = $typeSearch;
	$data = getUsersById($params);
	$data = (object) toType($data['data']);
	echo responseBuilder($errors, (object) $data);