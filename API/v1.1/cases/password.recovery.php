<?php
    checkRoute();
	$strings = strings();
    $errors = array();
    $data = array();
    $params = array();
    $mandatoryParams = ["last_name", ["email","phone","code","newPassword","phoneCode"]];
    $_IN_ = checkInData("POST", $mandatoryParams, []);
	$typeKey = 'email';
	if ($_IN_['phone']) {
		$typeKey = 'phone';
	}
	switch ($typeKey) {
		case 'email':
			$object_key_ownerId = emailExists($_IN_[$typeKey]);
			if (!$object_key_ownerId) {
                array_push($errors, addError(
                    $_ERRORS['invalidField'][0],
                    $_ERRORS['invalidField'][1]." `".$typeKey."`. Value not exist"
                ));
                http_response_code(400);
                ethrow($errors);
            }
		    break;
        case 'phone':
            $object_key_ownerId = user_phoneCheck($_IN_[$typeKey], $_IN_['phoneCode'] ? ($_IN_['phoneCode']) : false);
            if (!$object_key_ownerId['status']) {
                array_push($errors, addError(
                    $_ERRORS['invalidField'][0],
                    $_ERRORS['invalidField'][1]." `".$typeKey."`. Value not exist"
                ));
                http_response_code(400);
                ethrow($errors);
            }
            $object_key_ownerId = (int) $object_key_ownerId['user']['id'];
            break;
	}
    $thisUser = getUsersById([
        "fields" => ['id', 'last_name', 'email', 'phone'],
        "users" => [$object_key_ownerId],
        'withoutFields' => []
    ]);
	if (mb_strtoupper($thisUser['data'][0]['last_name']) != mb_strtoupper($_IN_['last_name'])) {
        array_push($errors, addError(
            $_ERRORS['permission_denied'][0],
            $_ERRORS['permission_denied'][1]." field: `last_name` incorrect"
        ));
        http_response_code(400);
        ethrow($errors);
    }
    $code = $_IN_['code'] ? epost($_IN_['code']) : NULL;
    if (!$code) {
        removeCodesActivation($_IN_[$typeKey]);
        $addCodeActivation = addCodeActivation($_IN_[$typeKey], 3600, 2);
        $code = $addCodeActivation['code'];
        switch ($typeKey) {
            case 'email':
                $m = new Mail('utf-8');
                $m->From('***');
                $m->ReplyTo('***');
                $m->To($_IN_[$typeKey]);
                $m->Subject("Восстановление пароля");
                $m->Body("Код для восстановления пароля ***: ".$code);
                $m->Priority(4); // установка приоритета
                $m->smtp_on($strings['smtp']['host'],
                    $strings['smtp']['from'],
                    $strings['smtp']['pass'],
                    $strings['smtp']['port'], 10
                );
                $m->Send();
                $data[0]['status'] = 'success';
                $data[0]['message'] = 'Check your object_key (email or phone) for continue';
                break;
            case 'phone':
                // отправить код на телефон
                break;
        }
    } else {
        $code_checkResponse = checkCodeActivation($code);
        if (!$code_checkResponse['status'] || $code_checkResponse['object_key'] != $_IN_[$typeKey]) {
            array_push($errors, addError(
                $_ERRORS['permission_denied'][0],
                $_ERRORS['permission_denied'][1]." field: `code` incorrect"
            ));
            http_response_code(400);
            ethrow($errors);
        }
        useCodeActivation($code);
        if (!$_IN_['newPassword']) {
            array_push($errors, addError(
                $_ERRORS['needFields'][0],
                $_ERRORS['needFields'][1]." `newPassword`"
            ));
            http_response_code(400);
            ethrow($errors);
        }
        $passwordUpdate = passwordUpdate(num($thisUser['data'][0]['id']), false, $_IN_['newPassword'], true);
        $data[0]['status'] = ($passwordUpdate) ? 'success' : 'fail';
    }
	echo responseBuilder($errors, $data);