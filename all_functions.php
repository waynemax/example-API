<?php
    checkRoute();

	function use_($path, $files) {
        global $_ERRORS, $_REGEXP, $v, $role, $method, $sqlmode, $connections, $servers, $router, $begin_time;
        $files = (gettype($files) == "string") ? [$files] : $files;
        foreach ($files as $key => $val) {
            if (file_exists(ROOT_DIR.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$val.".php")) {
                require_once(ROOT_DIR.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$val.".php");
            } else {
                cout(responseBuilder(array(
                    0 => $_ERRORS['fileNotFound']
                ), false));
            }
        }
    }

	function responseBuilder($errors, $data) {
        $response = array(
            "errors" => array(),
            "data" => array()
        );
        if ($errors) {
            foreach ($errors as $value) {
                array_push($response['errors'], $value);
            }
        }
        if ($data && empty($response['errors'])) {
            foreach ($data as $value) {
                array_push($response['data'], $value);
            }
        }
        return json($response);
    }

	function rangeInt($val, $min, $max) {
        return ($val >= $min && $val <= $max);
    }

	function checkTime() {
        global $begin_time;
        return time() - 1272000000 + floatval(microtime()) - $begin_time;
    }

	function json($in) {
        return json_encode($in, JSON_UNESCAPED_UNICODE);
    }

	function addError($fieldName, $message) {
        return array(
            "code" => $fieldName,
            "message" => $message
        );
    }

	function ethrow($errors) {
        $response = array(
            "errors" => array(),
            "data" => array()
        );
        if ($errors) {
            foreach ($errors as $value) {
                array_push($response['errors'], $value);
            }
            cout($response, true);
        }
    }

	function fieldsTreatment($in, $outAllow, $default) {
        $fields = $default;
        if (!is_null($in)) {
            $queryFieldsIn = explode(",", $in);
            $queryFieldsOut = [];
            foreach ($queryFieldsIn as $keyField => $fieldValue) {
                if (in_array($fieldValue, $outAllow)) {
                    array_push($queryFieldsOut, $fieldValue);
                }
            }
            $fields = count($queryFieldsOut) > 0 ? $queryFieldsOut : $fields;
        }
        return $fields;
    }

	function toConvert($data, $type) {
        switch (gettype($data)) {
            case 'array':
                $newData = [];
                foreach ($data as $key => $value) {
                    switch ($type) {
                        case 'integer':
                            array_push($newData, num($value));
                            break;
                    }
                }
                return $newData;
                break;
            case 'string':
                switch ($type) {
                    case 'integer':
                        return num($data);
                        break;
                }
                break;
            case 'integer':
                return $data;
                break;
        }
    }

	function getValuesByKey($array, $key) {
        $values = [];
        foreach ($array as $k => $v) {
            array_push($values, $v[$key]);
        }
        return $values;
    }

	function getSqlStringByInId($array) {
        return "in(".join(",", $array).")";
    }

	function query2Array($string) {
        $result = array();
        if (strpos($string, '=')) {
            if (strpos($string, '?') !== false) {
                $q = parse_url($string);
                $string = $q['query'];
            }
        } else {
            return false;
        }
        foreach (explode('&', $string) as $couple) {
            list ($key, $val) = explode('=', $couple);
            $result[$key] = $val;
        }
        return empty($result) ? false : $result;
    }

	function APIexist($v) {
        $config = config();
        return file_exists($config['dirAPI'].$v) ? true : false;
    }

	function getVersionAPI($string) {
        $config = config();
        if ($string) {
            if (APIexist($string['v'])) {
                return $string['v'];
            } else {
                return $config['vApiDefault'];
            }
        } else {
            return $config['vApiDefault'];
        }
    }

	function cout($object, $json = false) {
        if ($json) {
            print_r(json($object));
        } else {
            print_r($object);
        }
        exit;
    }

    function bigintval($value) {
        $value = trim($value);
        if (ctype_digit($value)) {
            return $value;
        }
        $value = preg_replace("/[^0-9](.*)$/", '', $value);
        if (ctype_digit($value)) {
            return $value;
        }
        return 0;
    }

	function num($i) {
        $i = intval($i);
        return ($i > 0) ? $i : 0;
    }

	function esql($text) {
        return stripslashes(addslashes(htmlspecialchars(strip_tags(escape($text)))));
    }

	function epost($text) {
        return stripslashes(addslashes(htmlspecialchars(strip_tags(escape($text)))));
    }

	function change_key($key, $new_key, &$arr, $rewrite = true) {
        if (!array_key_exists($new_key, $arr) || $rewrite){
            $arr[$new_key] = $arr[$key];
            unset($arr[$key]);
            return true;
        }
        return false;
    }

	function dateTreatment($date, $type) {
        switch ($type) {
            case 1:
                return date("m.d.y", $date);
                break;
            case 2:
                return date("m.d.y H:i:s", $date);
                break;
        }
    }

	function checkOnline($last_seen) {
        $config = config();
        $onlineTime = $config['onlineTime'];
        if (num($last_seen) > (time() - $onlineTime)) {
            return true;
        } else {
            return false;
        }
    }

	function getIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

	function getInParams() {
        $_SERVER['REQUEST_URI'] = explode("?", $_SERVER['REQUEST_URI'])[0];
        return array(
            "getParams" => $_SERVER['QUERY_STRING'],
            "url" => $_SERVER['REQUEST_URI'],
            "httpHost" => $_SERVER['HTTP_HOST'],
            "protocol" => $_SERVER['REQUEST_SCHEME'],
            "full" => $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?".$_SERVER['QUERY_STRING']
        );
    }

	function checkInData($request_method, $obligatories, $optional) {
        global $errors, $_ERRORS, $getParams;
        if (is_array($optional)) {
            array_push($optional, 'v');
            array_push($optional, 'access_token');
        }
        $reqMethod = mb_strtoupper($_SERVER['REQUEST_METHOD']);
        if ($reqMethod != mb_strtoupper($request_method)) {
            if ($reqMethod != 'OPTIONS') {
                $errors[] = addError(
                    $_ERRORS['only' . mb_strtoupper($request_method)][0],
                    $_ERRORS['only' . mb_strtoupper($request_method)][1]
                );
                http_response_code(405);
                ethrow($errors);
            }
        }
        $in = mb_strtoupper($request_method) == "POST" ? $_POST : $getParams;
        if (is_array($obligatories) && is_array($optional)) {
            if (!empty($obligatories)) {
                foreach ($obligatories as $key => $value) {
                    switch (gettype($value)) {
                        case 'array':
                            $leastOneFlag = false;
                            foreach ($value as $kk => $vv) {
                                $optional[] = $vv;
                                if ($in[$vv]) {
                                    $leastOneFlag = true;
                                }
                            }
                            if (!$leastOneFlag) {
                                $errors[] = addError(
                                    $_ERRORS['needFields'][0],
                                    $_ERRORS['needFields'][1]." `".join(" || ", $value)."`"
                                );
                                http_response_code(400);
                            }
                            break;
                        case 'string':
                            if ($in[$value] == '') {
                                $errors[] = addError(
                                    $_ERRORS['needFields'][0],
                                    $_ERRORS['needFields'][1]." `".$value."`"
                                );
                                http_response_code(400);
                            }
                            break;
                    }
                }
            }

            if (is_array($in) && !empty($in)) {
                $merge = array_merge($obligatories, $optional);
                foreach ($in as $key => $value) {
                    if (!in_array($key, $merge)) {
                        $errors[] = addError(
                            $_ERRORS['unknownField'][0],
                            $_ERRORS['unknownField'][1]." `".$key."`"
                        );
                        http_response_code(400);
                    }
                }
            }
        }
        ethrow($errors);
        return $in;
    }

	function auth($headerAuth = NULL, $optinalFlag = NULL) {
        global $_ERRORS, $errors;
        if (!$headerAuth) {
            $headerAuth = getallheaders()['Auth-Token'] ?? getallheaders()['Auth'] ?? getallheaders()['auth'];
        }
        $checkAuth = checkAuth($headerAuth);
        if (!$checkAuth['status']) {
            if (!$optinalFlag) {
                $errors[] = addError($_ERRORS['needAuth'][0], $_ERRORS['needAuth'][1]);
                http_response_code(401);
                ethrow($errors);
            } else {
                return NULL;
            }
        }
        return num($checkAuth['user_id']);
    }

	function maxInt(int $inInt, int $maxInt) {
        $inInt = num($inInt);
        return ($inInt >= $maxInt) ? $maxInt : $inInt;
    }

	function randomStr($n){
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz.,*_-=+';
        $counter = strlen($pattern)-1;
        for ($i=0; $i<$n; $i++) {
            $key.= $pattern{rand(0,$counter)};
        }
        return $key;
    }

	function ddmmyyyyChecker($value) {
        global $_ERRORS;
        $dateValidate = explode('.', $value);
        list($day, $month, $year) = $dateValidate;
        $nowY = num(date('Y'));
        if (!rangeInt(num($year), ($nowY-70), ($nowY))) {
            return [
                'status' => false,
                'error' => $_ERRORS['invalidField'][1]." `date`; Year must be: ".($year-70)." <> ".($nowY)
            ];
        }
        if (!checkdate($month, $day, $year)) {
            return [
                'status' => false,
                'error' => $_ERRORS['invalidField'][1]." `date`; "
            ];
        }
        return [
            'status' => true,
            'time' => strtotime($day.".".$month.".".$year)
        ];
    }

    function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

	function toType($data) {
        switch (gettype($data)) {
            case "object":
            case "array":
                $return = [];
                foreach ($data as $key => $value) {
                    $return[$key] = toType($value);
                }
                return $return;
                break;
            case "integer":
                return num($data);
                break;
            case "string":
                return is_numeric($data) ? (int) $data : isJson($data) ? json_decode($data, true) : (string) $data;
                break;
            case "NULL":
                return NULL;
                break;
            default:
                return $data;
                break;
        }
    }

    function mtime() {
        return round(microtime(true) * 1000);
    }

    function err(string $codeError, string $additional = '', int $http_response_code = 200) {
	    global $_ERRORS;
        $errors = [];
        $codeError = $_ERRORS[$codeError] ? $codeError : 'unknownError';
        array_push(
            $errors,
            addError(
                $_ERRORS[$codeError][0],
                $_ERRORS[$codeError][1] . ($additional??'')
            )
        );
        http_response_code($http_response_code);
        ethrow($errors);
    }

	function methodNotFound() {
        global $_ERRORS;
        echo responseBuilder(
            array(0 => (object) [
                'code' => $_ERRORS['methodNotFound'][0],
                'message' => $_ERRORS['methodNotFound'][1]
            ]),
            false
        );
    }