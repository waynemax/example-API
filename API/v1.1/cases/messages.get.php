<?php
    checkRoute();
    ob_implicit_flush();
    set_time_limit(0);
    $errors = array();
    $data = array();
    $mandatoryParams = [];
    $optionalParams = ['original_chat_id', 'use_mc', 'last_message_id', 'ts', 'rev', 'preview_length', 'offset', 'count'];
    $_IN_ = checkInData("GET", $mandatoryParams, $optionalParams);
    $_IN_['not_touch'] = true;
    $user_id = auth();
    $response = system_messages_get($user_id, $_IN_);
    $data[0]['response'] = toType($response);
    echo responseBuilder($errors, $data);