<?php
    checkRoute();
    $errors = array();
    $data = array();
    $params = array();
    $_IN_ = checkInData("POST", ["code"], []);
    $user_id = auth();
    $code = num($_IN_['code']);
    $codeActivate = checkCodeActivation($code, $user_id);
    $data[0]['status'] = false;
    if ($codeActivate['status'] && $codeActivate['object_key']) {
        $data[0]['status'] = true;
        $userPhoneUpdate = system_userPhoneUpdate($user_id, $codeActivate['object_key']);
        if (!$userPhoneUpdate['status']) {
            $data[0]['status'] = false;
        } else {
            useCodeActivation($code, $user_id);
            $data[0]['code'] = $codeActivate;
        }
    }
    echo responseBuilder($errors, $data);