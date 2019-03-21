<?php
    checkRoute();
	$data = array();
    $_IN_ = checkInData("GET", [], []);
	$response = getEarnings();
	array_push($data, $response);
	echo responseBuilder($errors, $data);