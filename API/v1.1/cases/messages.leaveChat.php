<?php
    checkRoute();
    $errors = array();
    $data = array();
    $mandatoryParams = [['copiedChatId','originalChatId']];
    $optionalParams = [];
    $_IN_ = checkInData("GET", $mandatoryParams, $optionalParams);
    $user_id = auth();
    if ($_IN_['copiedChatId'] && $_IN_['originalChatId']) {
        array_push($errors, addError(
            $_ERRORS['invalidField'][0],
            $_ERRORS['invalidField'][1]. "copiedChatId || originalChatId"
        ));
        http_response_code(400);
        ethrow($errors);
    }
    if ($_IN_['originalChatId']) {
        $copiedChatId = system_messages_getChatCopiedIdByOriginalChatId($user_id, $_IN_['originalChatId']);
        if (!$copiedChatId['status']) {
            array_push($errors, addError(
                $_ERRORS['userIsNotInChat'][0],
                $_ERRORS['userIsNotInChat'][1]
            ));
            http_response_code(400);
            ethrow($errors);
        }
        $copiedChatId = (int) $copiedChatId['id'];
    } else {
        $copiedChatId = num($_IN_['copiedChatId']);
    }
    $originalChatFields = system_messages_getSomeFieldsCopiedChatById($copiedChatId, [
        'original_chat_id',
        'author_id',
        'multy'
    ]);
    if (!$originalChatFields['status']) {
        array_push($errors, addError(
            $_ERRORS['copyChatNotExist'][0],
            $_ERRORS['copyChatNotExist'][1].", ID:".$copiedChatId
        ));
        http_response_code(400);
        ethrow($errors);
    }
    $multy = (bool) $originalChatFields['response']['multy'];
    if (!$multy) {
        array_push($errors, addError(
            $_ERRORS['itsNotMultiChat'][0],
            $_ERRORS['itsNotMultiChat'][1]
        ));
        http_response_code(400);
        ethrow($errors);
    }
    $idOriginalChat = (int) $originalChatFields['response']['original_chat_id'];
    $checkUserInConversationById = system_messages_checkUserInConversationById($idOriginalChat, $user_id);
    if (!$checkUserInConversationById['status']) {
        array_push($errors, addError(
            $_ERRORS['userIsNotInChat'][0],
            $_ERRORS['userIsNotInChat'][1]
        ));
        http_response_code(400);
        ethrow($errors);
    }
    $authorCopiedChat = (int) $originalChatFields['response']['author_id'];
    if ($user_id != $authorCopiedChat) {
        array_push($errors, addError(
            $_ERRORS['access_denied'][0],
            $_ERRORS['access_denied'][1].", user_id/auth"
        ));
        http_response_code(403);
        ethrow($errors);
    }
    $excludeUserFromOriginalChat = system_messages_excludeUserFromOriginalChat($idOriginalChat, $user_id);
    system_messages_removeCopiedChatById($copiedChatId);
    $data[0]['status'] = true;
    echo responseBuilder($errors, $data);