<?php
    checkRoute();
	$errors = array();
	$data = array();
	if ($_SERVER['REQUEST_METHOD'] != "GET") {
		array_push($errors, addError($_ERRORS['onlyGET'][0], $_ERRORS['onlyGET'][1]));
		http_response_code(405);
		ethrow($errors);
	}
	if (!array_key_exists("alias", $getParams)) {
		array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `alias`"));
		http_response_code(400);
		ethrow($errors);
	}
	$data = getObjectByAlias($getParams['alias']);
	echo responseBuilder($errors, [$data]);