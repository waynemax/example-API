<?php
    checkRoute();
    $errors = array();
    $data = array();
    $mandatoryParams = ['originalChatId'];
    $optionalParams = [];
    $_IN_ = checkInData("GET", $mandatoryParams, $optionalParams);
    $user_id = auth();
    $idOriginalChat = (int) $_IN_['originalChatId'];
    $checkUserInConversationById = system_messages_checkUserInConversationById($idOriginalChat, $user_id);
    if (!$checkUserInConversationById['status']) {
        array_push($errors, addError(
            $_ERRORS['userIsNotInChat'][0],
            $_ERRORS['userIsNotInChat'][1]
        ));
        http_response_code(400);
        ethrow($errors);
    }
    $messsages_getChatInfoById = messsages_getChatInfoById($idOriginalChat);
    $data['response'] = $messsages_getChatInfoById;
    echo responseBuilder($errors, $data);