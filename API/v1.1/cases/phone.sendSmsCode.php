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
    $objectKey = ($params['phoneCode'] ? $params['phoneCode'] : $strings['phone']['defaultPhoneCode']).$params['phone'];
    $user_phoneCheck = user_phoneCheck($params['phone'], $params['phoneCode'] ? $params['phoneCode'] : $strings['phone']['defaultPhoneCode']);
    if ($user_phoneCheck['status']) {
        array_push($errors, addError(
            $_ERRORS['same'][0],
            $_ERRORS['same'][1]." `phone`"
        ));
        http_response_code(400);
        ethrow($errors);
    }
    removeCodesActivation($objectKey);
    $params['phoneCode'] = $params['phoneCode'] ? $params['phoneCode'] : $strings['phone']['defaultPhoneCode'];
    $user_getSmsCode = (int) user_getSmsCode(
        $params['phone'],
        $params['phoneCode'],
        [
            'phoneCode' => $params['phoneCode'],
            'user_id' => $user_id
        ],
        $user_id
    );
    $ISO = substr($params['phone'], 0, 3);
    $getSmsProviderByISOCode = getSmsProviderByISOCode($ISO);
    $sms_typeIdProvider = (int) $getSmsProviderByISOCode['typeId'];
    $sms_provider = $getSmsProviderByISOCode['sms_provider'];
    $user_sendSmsCode = user_sendSmsCode(
        $params['phone'],
        $params['phoneCode'] ? $params['phoneCode'] : $strings['phone']['defaultPhoneCode'],
        $user_getSmsCode,
        $sms_provider
    );
    $data[0]['phoneCode'] = $params['phoneCode'];
    $data[0]['response'] = $user_sendSmsCode;
    $data[0]['code'] = $user_getSmsCode;
    echo responseBuilder($errors, $data);