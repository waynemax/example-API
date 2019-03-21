<?php
    checkRoute();
    $errors = array();
    $data = array();
    $mandatoryParams = [];
    $optionalParams = [];
    $_IN_ = checkInData("GET", $mandatoryParams, $optionalParams);
    $user_id = auth();
    $response = system_messages_getLastUserMessage($user_id);
    $data[0]['response'] = toType($response);
    echo responseBuilder($errors, $data);
















