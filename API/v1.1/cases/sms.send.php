<?php
    checkRoute();
    $errors = array();
    $data = array();
    $params = array();
    $strings = strings();
    $_IN_ = checkInData("POST", ["phone"], ["phoneCode"]);
    $user_id = auth();
    foreach ($_IN_ as $k => $v) {
        switch ($k) {
            case 'phone':
                $v = num($v);
                if (!(strlen($v) < $strings['phone']['minCountSymbolsInPhone'] || strlen($v) > $strings['phone']['maxCountSymbolsInPhone'])) {
                    $params[$k] = $v;
                    $data[0][$k] = $v;
                } else {
                    array_push($errors, addError(
                        $_ERRORS['invalidField'][0],
                        $_ERRORS['invalidField'][1]." `".$k."`"
                    ));
                    http_response_code(400);
                    ethrow($errors);
                }
                break;
            case 'phoneCode':
                $v = escape($v);
                $params[$k] = $v;
                break;
        }
    }
    $user_phoneCheck = user_phoneCheck($params['phone'], $params['phoneCode'] ? $params['phoneCode'] : $strings['phone']['defaultPhoneCode']);
    $data[0]['status'] = $user_phoneCheck['status'];
    $data[0]['phoneCode'] = $user_phoneCheck['phoneCode'];
    if ($user_phoneCheck['status']) {
        $data[0]['user'] = $user_phoneCheck['user'];
    }
    echo responseBuilder($errors, $data);
