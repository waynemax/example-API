<?php
    checkRoute();
    $errors = array();
    $data = array();
    $mandatoryParams = ['user_ids'];
    $optionalParams = ['name'];
    $_IN_ = checkInData("POST", $mandatoryParams, $optionalParams);
    $user_id = auth();
    $data = system_messages_createChat($user_id, $_IN_);
    echo responseBuilder($errors, $data);