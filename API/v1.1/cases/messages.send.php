<?php
    checkRoute();
    $errors = array();
    $data = array();
    $params = array();
    $mandatoryParams = [['chat_id','user_ids'],'message'];
    $optionalParams = ['attachment','forward_messages','sticker_id'];
    $_IN_ = checkInData("POST", $mandatoryParams, $optionalParams);
    $user_id = auth();
    $messages_send = system_messages_send($user_id, $_IN_);
    $data[0]['original_message_id'] = (int) $messages_send['original_message_id'];
    $data[0]['chat_id'] = (int) $messages_send['chat_id'];
    $data[0]['peers'] = (array) $messages_send['peers'];
    echo responseBuilder($errors, $data);
