<?php
    checkRoute();
	$errors = array();
	$data = array();
	if ($_SERVER['REQUEST_METHOD'] != "GET") {
		array_push($errors, addError($_ERRORS['onlyGET'][0], $_ERRORS['onlyGET'][1]));
		http_response_code(405);
		ethrow($errors);
	}
	$request = [];
	if (!is_null($getParams['continent_id'])) {
		$request["continent_id"] = num($getParams['continent_id']);
	}
	if (!is_null($getParams['id'])) {
		$request["id"] = num($getParams['id']);
	}
	if (!is_null($getParams['count'])) {
		$request["count"] = num($getParams['count']);
	}
	if (!is_null($getParams['offset'])) {
		$request["offset"] = num($getParams['offset']);
	}
	if (!is_null($getParams['important'])) {
		$request["important"] = num($getParams['important']);
	}
	if ($getParams['sort']) {
		$request["sort"] = epost($getParams['sort']);
	}
    if ($getParams['search']) {
        $request["search"] = urldecode(epost($getParams['search']));
    }
	array_push($data, getCountries($request));
	echo responseBuilder($errors, toType($data));