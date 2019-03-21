<?php
    checkRoute();
    $errors = array();
    $data = array();
    $mandatoryParams = [];
    $optionalParams = ['count', 'offset','getUsersInfo'];
    $_IN_ = checkInData("GET", $mandatoryParams, $optionalParams);
    $user_id = auth();
    $params = [
        'count' => 20,
        'offset' => 0
    ];
    foreach ($_IN_ as $k => $v) {
        switch ($k) {
            case 'getUsersInfo':
                $params['getUsersInfo'] = boolval($v);
                break;
            case 'count':
                if ($v != 'full') {
                    $params['count'] = num($v) < 1000 ? num($v) : 20;
                } else {
                    $params['count'] = 'full';
                }
                break;
            case 'offset':
                $params['offset'] = num($_IN_['offset']);
                break;
        }
    }
    $chats = (array) messsages_getChatIds($user_id, $params);
    $data['chats'] = $chats;
    echo responseBuilder($errors, $data);