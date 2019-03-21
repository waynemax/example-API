<?php
    checkRoute();
    // МЕТОД ВРЕМЕННО ВЫКЛЮЧЕН
    exit('ssory');
	$errors = array();
	$data = array();
	if ($_SERVER['REQUEST_METHOD'] != "GET") {
		array_push($errors, addError($_ERRORS['onlyGET'][0], $_ERRORS['onlyGET'][1]));
		http_response_code(405);
		ethrow($errors);
	}
	$auth = false;
	if ($reqHeaders['Auth']) {
		$checkAuth = checkAuth($reqHeaders['Auth']);
		if (!$checkAuth['status']) {
			array_push($errors, addError($_ERRORS['needAuth'][0], $_ERRORS['needAuth'][1]));
			http_response_code(401);
			ethrow($errors);
		} else {
			$auth = true;
			$user_id = num($checkAuth['user_id']);
		}
	}
	$fields = [
		"id", "first_name", "last_name", "patronymic", "nickname", "login",
		"bdate", "country", "region", "city", "domain", "last_seen",
		"verified", "full_name", "photo_id", "gender", "has_children", "has_family",
		"confession_type", "earnings_type", "education_type", "social_type"
	];
	if (!is_null($getParams['fields'])) {
		$queryFieldsIn = explode(",", $getParams['fields']);
		$queryFieldsOut  = [];
		foreach ($queryFieldsIn as $keyField => $fieldValue) {
			if (in_array($fieldValue, $fields)) {
				array_push($queryFieldsOut, $fieldValue);
			}
		}
		$fields = count($queryFieldsOut) > 0 ? $queryFieldsOut : $fields;
	}
	$filters = [];
	$filtersAllow = [
		"bdate",
		"country",
		"region",
		"city",
		"online",
		"has_children",
		"has_family",
		"confession_type",
		"earnings_type",
		"education_type",
		"social_type",
		"gender"
	];
	foreach ($getParams as $gKey => $gValue) {
		if (in_array($gKey, $filtersAllow)) {
			$filters[$gKey] = escape($gValue);
		}
	}
	$typeSearch = false;
	if ($getParams['type']) {
		switch ($getParams['type']) {
			case 'subscribers':
				$typeSearch = [
					'user_id' => is_null($getParams['user_id']) ? NULL : num($getParams['user_id']),
					'type' => 'subscribers'
				];
			break;
			case 'subscriptions':
				$typeSearch = [
					'user_id' => is_null($getParams['user_id']) ? NULL : num($getParams['user_id']),
					'type' => 'subscriptions'
				];
			break;
		}
	}
	$response = searchUsers([
            'fields' => $fields,
            'query' => !is_null($getParams['query']) ? escape($getParams['query']) : "",
            'filters' => $filters,
            'offset' => !is_null($getParams['offset']) ? escape($getParams['offset']) : 0,
            'count' => !is_null($getParams['count']) ? escape($getParams['count']) : 20,
            'sort' => !is_null($getParams['sort']) ? escape($getParams['sort']) : false
        ],
	    $typeSearch
	);
	echo responseBuilder($errors, [[
		'count' => $response['count'],
		'items' => $response['data']
	]]);