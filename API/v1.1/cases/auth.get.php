<?php
    checkRoute();
	$errors = array();
	$data = array();
    $errors = array();
    $data = array();
    $mandatoryParams = ['client_id','redirect_uri','display','scope','client_secret'];
    $optionalParams = ['login','password','phone'];
    $_IN_ = checkInData("GET", $mandatoryParams, $optionalParams);
    $data = system_auth_getCode($_IN_, $mandatoryParams);
	echo responseBuilder($errors, $data);