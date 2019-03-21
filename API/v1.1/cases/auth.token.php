<?php
    checkRoute();
    $errors = array();
    $data = array();
    $mandatoryParams = ['redirect_uri','client_secret','code','client_id'];
    $optionalParams = [];
    $_IN_ = checkInData("GET", $mandatoryParams, $optionalParams);
    $data = system_auth_getToken($_IN_, $mandatoryParams);
    echo responseBuilder($errors, $data);