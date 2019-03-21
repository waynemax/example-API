<?php
    function system_auth_getCode(array $_IN_PARAMS, array $mandatoryParams) {
        global $_REGEXP;
        $errors = array();
        $data = array();
        $clientFields = auth_getInfoClientById($_IN_PARAMS['client_id']);
        foreach ($mandatoryParams as $key => $value) {
            if ($value == "scope") {
                if (num($_IN_PARAMS[$value]) > num($clientFields[$value])) {
                    err('permission_denied', $value,400);
                }
            } else {
                if ($clientFields[$value] != $_IN_PARAMS[$value]) {
                    err('access_denied', $value, 400);
                }
            }
        }
        switch ($_IN_PARAMS['display']) {
            case 'none':
                if (!($_IN_PARAMS['login'] || $_IN_PARAMS['phone']) || !$_IN_PARAMS['password']) {
                    err('needFields', "login || phone, password", 400);
                }
                $typeAuth = 'login';
                if ($_IN_PARAMS['phone'] != '') {
                    $typeAuth = 'phone';
                }
                if ($typeAuth == 'login') {
                    $_IN_PARAMS['login'] = escape($_IN_PARAMS['login']);
                    if (!preg_match($_REGEXP[0], $_IN_PARAMS['login'])) {
                        err('unacceptable_symbols', " `login`", 400);
                    }
                    if (!preg_match($_REGEXP[1], $_IN_PARAMS['login'])) {
                        err('unacceptable_symbols', " `login`; You can not use numbers only", 400);
                    }
                    if (!preg_match($_REGEXP[3], $_IN_PARAMS['login'][0])) {
                        err('unacceptable_symbols', " `login`; The first character must not be a number", 400);
                    }
                }
                if ($typeAuth == 'phone') {
                    if (!preg_match("|^[0-9]{1,15}$|", $_IN_PARAMS['phone'])) {
                        err('unacceptable_symbols', " `phone`", 400);
                    }
                }
                if ($typeAuth == 'login') {
                    $authArray = array('login' => $_IN_PARAMS['login'], 'password' => $_IN_PARAMS['password']);
                } else {
                    $authArray = array('phone' => $_IN_PARAMS['phone'], 'password' => $_IN_PARAMS['password']);
                }
                $userId = authCheck($authArray);
                if (!$userId) {
                    err('permission_denied', " `login || password`", 400);
                }
                $expiry_time = 3600;
                $codes = getCodeByClient(false, false, $userId);
                $notEmpty = empty($codes) ? false : true;
                if ($notEmpty && ((intval($codes['create_date']) + intval($codes['expiry_time'])) > time())) {
                    array_push($data, array(
                        "redirect_uri" => $_IN_PARAMS['redirect_uri'],
                        "code" => $codes['code']
                    ));
                } else {
                    if ($notEmpty) {
                        removeCodeById($codes['id']);
                    }
                    $code = hash('sha256', "".time());
                    squery("insert into `auth_codes`
					(`id`, `code`, `client_id`, `expiry_time`, `create_date`,`user_id`) values
					(NULL, '".$code."', '".num($_IN_PARAMS['client_id'])."', '".$expiry_time."', '".time()."','{$userId}');"
                    );
                    array_push($data, array(
                        "redirect_uri" => $_IN_PARAMS['redirect_uri'],
                        "code" => $code
                    ));
                }
                return toType($data);
                break;
            default:
                err('permission_denied','`display`', 400);
                break;
        }
    }

    function system_auth_getToken(array $_IN_PARAMS, array $mandatoryParams) {
        $errors = array();
        $data = array();
        $config = config();
        $needFields = $mandatoryParams;
        $selectQuery = select(['redirect_uri','client_secret'],['clients'],"`client_id` = '".num($_IN_PARAMS['client_id'])."'",false,0,1);
        $clientFields = sfetch($selectQuery);
        $codes = getCodeByClient(false, $_IN_PARAMS['code'], NULL);
        if (!$clientFields) {
            err('not_found', '',400);
        }
        foreach ($needFields as $key => $value) {
            switch ($value) {
                case "client_id":
                    if (empty($clientFields)) {
                        err('access_denied', $value,403);
                    }
                    break;
                case "redirect_uri":
                    if (empty($clientFields) || $clientFields['redirect_uri'] != $_IN_PARAMS['redirect_uri']) {
                        err('access_denied', $value,403);
                    }
                    break;
                case "client_secret":
                    if (empty($clientFields) || $clientFields['client_secret'] != $_IN_PARAMS['client_secret']) {
                        err('access_denied', $value,403);
                    }
                    break;
                case "code":
                    if (empty($codes) || $codes['code'] != $_IN_PARAMS['code'] || !((intval($codes['create_date'])+intval($codes['expiry_time'])) > time())) {
                        err('access_denied', $value,403);
                    } else {
                        $userId = (int) $codes['user_id'];
                    }
                    break;
            }
        }
        $ua = apache_request_headers();
        $ua = escape(strtolower($ua['User-Agent']));
        $ip = escape(getIP());
        $tokenSameQuery = "select user_id, refresh_token, access_token, expires_in, reg_time from tokens where user_id = '{$userId}' && ip = '{$ip}' && ua = '{$ua}'";
        $tokenSame = sfetch($tokenSameQuery);
        if (!empty($tokenSame) && ((intval($tokenSame['reg_time']) + intval($tokenSame['expires_in'])) > time())) {
            users_setOnline((int) $tokenSame['user_id']);
            array_push($data, array(
                "access_token" => $tokenSame['access_token'],
                "refresh_token" => $tokenSame['refresh_token'],
                "expires_in" => (int) $tokenSame['expires_in'],
                "reg_time" => (int) $tokenSame['reg_time'],
                "user_id" => (int) $tokenSame['user_id']
            ));
            removeCodeById($codes['id']);
        } else {
            $expires_in = (int) $config['tokenTimeValid'];
            $access_token = genSalt(32);
            $refresh_token = genSalt(32);
            $time = time();
            squery("insert into `tokens` (`id`, `client_id`, `reg_time`, `expires_in`, `access_token`, `refresh_token`, `ip`, `ua`, `user_id`) values (NULL, '{$_IN_PARAMS[client_id]}', '".$time."', '{$expires_in}', '{$access_token}', '{$refresh_token}', '{$ip}', '{$ua}','{$userId}');");
            removeCodeById($codes['id']);
            users_setOnline((int) $userId);
            array_push($data, array(
                "access_token" => $access_token,
                "refresh_token" => $refresh_token,
                "expires_in" => $expires_in,
                "reg_time" => $time,
                "user_id" => (int) $userId
            ));
        }
        return toType($data);
    }