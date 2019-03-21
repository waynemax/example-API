<?php
    checkRoute();
    $errors = array();
    $data = array();
    $mandatoryParams = ['contacts'];
    $optionalParams = ['full'];
    $_IN_ = checkInData("POST", $mandatoryParams, $optionalParams);
    $user_id = auth();
    $contacts = json_decode($_IN_['contacts'], true);
    if (gettype($contacts) != 'array') {
        array_push($errors, addError(
            $_ERRORS['unknownError'][0],
            $_ERRORS['unknownError'][1]." `contacts`"
        ));
        http_response_code(400);
        ethrow($errors);
    }
    $contactsCount = count($contacts);
    if ($contactsCount < 1) {
        array_push($errors, addError(
            $_ERRORS['emptyValue'][0],
            $_ERRORS['emptyValue'][1]." `contacts`"
        ));
        http_response_code(400);
        ethrow($errors);
    }
    $isSetAppContacts = [];
    $allPhonesById = [];
    $allContactsByPhone = [];
    foreach ($contacts as $key => $value) {
        if (!empty($value['phoneNumbers'])) {
            foreach ($value['phoneNumbers'] as $k => $v) {
                $number = phone_formatter($v['number']);
                if (strlen($number) < 23 && strlen($number) > 8) {
                    $allPhonesById[(int)$v['id']] = phone_formatter($v['number'], true);
                    $allContactsByPhone[phone_formatter($v['number'])] = $value;
                }
            }
        }
    }
    if (empty($allPhonesById)) {
        echo responseBuilder([], [[
           'count' => 0
        ]]);
    }
    $params = [];
    $params['typeSearch'] = 'phones';
    $params['phones'] = $allPhonesById;
    $params['fields'] = [
        'id',
        'first_name',
        'last_name',
        'last_seen',
        'nickname',
        'full_name',
        'phone'
    ];
    $checkPhones = getUsersById($params);
    $data[0]['count'] = count($checkPhones['data']);
    $data[0]['allCount'] = $contactsCount;
    $responseItem = [];
    if (!$_IN_['full']) {
        foreach ($checkPhones['data'] as $k => $v) {
            $allContactsByPhone[$v['phone']]['exist'] = true;
            $allContactsByPhone[$v['phone']]['userInfo'] = $v;
            array_push($responseItem, $allContactsByPhone[$v['phone']]);
        }
    } else {
        $usersInfoByPhone = [];
        foreach ($checkPhones['data'] as $k => $v) {
            $usersInfoByPhone[$v['phone']] = $v;
        }
        foreach ($contacts as $key => $value) {
            if (!empty($value['phoneNumbers'])) {
                foreach ($value['phoneNumbers'] as $k => $v) {
                    $number = phone_formatter($v['number']);
                    if (array_key_exists($number, $usersInfoByPhone)) {
                        $contacts[$key]['exist'] = true;
                        $contacts[$key]['userInfo'] = $usersInfoByPhone[$number];
                    } else {
                        $contacts[$key]['exist'] = false;
                    }
                }
            } else {
                $contacts[$key]['exist'] = false;
            }
        }
        $responseItem = $contacts;
    }
    $data[0]['items'] = $responseItem;
    echo responseBuilder([], $data);