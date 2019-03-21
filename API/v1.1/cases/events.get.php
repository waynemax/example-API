<?php
    checkRoute();
	$errors = array();
	$data = array();
	$_IN_ = $getParams;
	$params = [];
	$count = 20;
	$offset = 0;
	if ($_SERVER['REQUEST_METHOD'] != "GET") {
		array_push($errors, addError($_ERRORS['onlyGET'][0], $_ERRORS['onlyGET'][1]));
		ethrow($errors);
	}
    $user_id = auth();
	$fieldsOutAllow = [
		"id",
		"object_id",
		"description",
		"type",
		"date_create",
		"images",
		"event_author_id",
		"receiver_id"
	];
	$fields = [
		"id",
		"object_id",
		"description",
		"type",
		"date_create",
		"images",
		"event_author_id",
		"receiver_id"
	];
	if (!is_null($_IN_['fields'])) {
		$queryFieldsIn = explode(",", $_IN_['fields']);
		$queryFieldsOut = [];
		foreach ($queryFieldsIn as $keyField => $fieldValue) {
			if (in_array($fieldValue, $fieldsOutAllow)) {
				array_push($queryFieldsOut, $fieldValue);
			}
		}
		$fields = count($queryFieldsOut) > 0 ? $queryFieldsOut : $fields;
	}
	$params['fields'] = $fields;
	foreach ($_IN_ as $k => $v) {
		switch ($k) {
			case 'type':
				$params[$k] = num($v);
			break;
			case 'count':
				$count = num($v) < 1000 ? num($v) : 20;
			break;
			case 'offset':
				$offset = $_IN_['offset'] ? num($_IN_['offset']) : 0;
			break;
		}
	}
	$eventsGet = eventsGet($user_id, $params, $offset, $count);
	if (count($eventsGet['data']) > 0) {
		$data[0] = (object) ["items" => $eventsGet['data']];
	} else {
		$data[0] = (object) ["items" => []];
	}
	echo responseBuilder($errors, $data);