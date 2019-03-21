<?php
    checkRoute();
	$errors = array();
	$data = array();
	if ($_SERVER['REQUEST_METHOD'] != "GET") {
		array_push($errors, addError($_ERRORS['onlyGET'][0], $_ERRORS['onlyGET'][1]));
		http_response_code(405);
		ethrow($errors);
	}
	$needFields = array(
		"region_id"
	);
	foreach ($needFields as $key => $value) {
		if (is_null($getParams[$value])) {
			array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `".$value."`"));
			http_response_code(400);
		} else {
			$getParams[$value] = epost($getParams[$value]);
		}
	}
	ethrow($errors);
	$request = ["region_id" => num($getParams['region_id'])];
	if (!is_null($getParams['count'])) {
		$request["count"] = num($getParams['count']);
	}
	if (!is_null($getParams['offset'])) {
		$request["offset"] = num($getParams['offset']);
	}
	if (!is_null($getParams['id'])) {
		$request["id"] = num($getParams['id']);
	}
	if ($getParams['sort']) {
		$request["sort"] = epost($getParams['sort']);
	}
	$response = getCities($request);
	$thisRegion = getRegions([
		"id" => num($getParams['region_id']),
		"count" => 1
	]);
	$thisCountry = getCountries([
		"id" => $thisRegion['items'][0]['country_id'],
		"count" => 1
	]);
	$thisContinent = getContinents([
		"id" => $thisCountry['items'][0]['continent_id'],
		"count" => 1
	]);
	$response = [
		"continent" => $thisContinent['items'][0],
		"country" => $thisCountry['items'][0],
		"region" => $thisRegion['items'][0]
	] + $response;
	array_push($data, $response);
	echo responseBuilder($errors, toType($data));