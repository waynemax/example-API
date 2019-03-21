<?php
    /**
     * @author: Wayne Maxim
     * @description: remote data management system
     **/

    /**
     * Установить номер телефона
     * @param int $user_id
     * @param $phone
     */
    function system_userPhoneUpdate(int $user_id, $phone) {
        $phoneIsSet = user_phoneIsset($user_id);
        if (!$phoneIsSet['status']) {
            squery("UPDATE `users` SET `phone` = '".escape($phone)."' WHERE `id` = '".num($user_id)."';");
            return [
                'status' => true
            ];
        } else {
            return [
                'status' => false
            ];
        }
    }

    /**
     * Отвязка телефона
     * @param int $user_id
     */
    function user_phoneRemove(int $user_id) {
        squery("UPDATE `users` SET `phone` = NULL WHERE `id` = '".num($user_id)."';");
    }

    /**
     * Проверка телефона
     * @param $phone
     * @param string $phoneCode
     * @return array
     */
    function user_phoneCheck($phone, $phoneCode) {
        if (!$phoneCode) {
            $phoneCode = "+7";
        }
        $query = $phoneCode.num($phone);
        $select = sfetch(select(["phone", "id"], ["users"], " `phone` = '".$query."' ",false, 0, 1));
        if (!$select) {
            return [
                "status" => false,
                "phoneCode" => $phoneCode
            ];
        } else {
            return [
                "status" => true,
                "user" => $select,
                "phoneCode" => $phoneCode
            ];
        }
    }

    /**
     * Проверка аккаунта на наличие телефона
     * @param $id
     * @return array
     */
    function user_phoneIsset(int $user_id) {
        $select = sfetch(select(["phone", "id"], ["users"], " `id` = '".num($user_id)."' ",false, 0, 1));
        if (!$select || !$select['phone']) {
            return [
                "status" => false
            ];
        } else {
            return [
                "status" => true,
                "user" => $select
            ];
        }
    }

    /*
     * Приведение телефонного номера к единому стандарту
     */
    function phone_formatter($phone, bool $quotes = false) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        switch (strlen($phone)) {
            case 11:
                if ($phone[0] == "8" || $phone[0] == "7") {
                    $phone[0] = "7";
                }
            default:
                $phone = "+".$phone;
                break;
        }
        if ($quotes) {
            $phone = "'".$phone."'";
        }
        return $phone;
    }

    /**
     * Получение кода активации
     * @param $phone
     * @param string $phoneCode
     * @return mixed
     */
    function user_getSmsCode($phone, $phoneCode = "+7", array $other, int $user_id = NULL) {
        $objectKey = $phoneCode.num($phone);
        $addCodeActivation = addCodeActivation($objectKey, 180, PHONE_CODE_ACTIVATION_TYPE, $other, $user_id);
        return $addCodeActivation['code'];
    }

    /**
     * Отправка кода активации
     * @param $phone
     * @param string $phoneCode
     * @param $code
     * @return array
     */
    function user_sendSmsCode($phone, $phoneCode = "+7", $code, $sms_provider = "TWILIO") {
        global $_ERRORS, $config;

        $errors = [];
        $response = [];
        $to = preg_replace('~\D~','', $phoneCode.$phone); // убираем лишние символы

        // Лимит на сообщения в сутки
        if (getCountSmsByPeriod($to, 1) > $config['services']['smsLimit']['day']) {
            array_push(
                $errors,
                addError(
                    $_ERRORS['access_denied'][0],
                    $_ERRORS['access_denied'][1]." `smsLimit: day`"
                )
            );
            http_response_code(403);
            ethrow($errors);
        }

        // Лимит на сообщения в промежуток 5 минут
        if (getCountSmsByPeriod($to, 2) > $config['services']['smsLimit']['s300']) {
            array_push(
                $errors,
                addError(
                    $_ERRORS['access_denied'][0],
                    $_ERRORS['access_denied'][1]." `smsLimit: 300s`"
                )
            );
            http_response_code(403);
            ethrow($errors);
        }

        $code = (int) $code;
        switch (mb_strtoupper($sms_provider)) {
            case 'TWILIO':
                twilioSend("+".$to, $code);
                $response['status'] = 'success';
                smsLogDbAdd(1, $to, 0, NULL, NULL, $code);
                break;
            case 'EPOCHTA':
                $usd = epochtaSend($to, $code);
                $response['usd'] = $usd;
                $xml2array = json_decode(json_encode(simplexml_load_string($usd)), true);
                $price = round($xml2array['amount'], 2, PHP_ROUND_HALF_DOWN);
                $currency = $xml2array['currency'];
                smsLogDbAdd(2, $to, '0', $price, $currency, $code);
                break;
        }

        return $response;
    }
