<?php
    checkRoute();

    $strings = strings();
    $config = config();
    $inParams = getInParams();
    $getParams = query2Array($inParams['getParams']);

    if ($inParams['httpHost'] == $config['domain']) {
        $role = explode("/", $inParams['url']);
        $method = explode(".", $role[2]);
        switch ($role[1]) {
            case 'test':
                switch ($method[0]) {
                    case 'sms':
                        $v = getVersionAPI($getParams);
                        require_once(ROOT_DIR . '/twilio.php');
                        use_($config['dirAPI'].$v, [
                            "controller/sms",
                            $method[0]
                        ]);
                        twilioSend("+".$_GET['phone'], $_GET['sms']);
                        break;
                    case 'fcm':
                        $v = getVersionAPI($getParams);
                        cout($v, true);
                        break;
                    default:
                        cout(1, true);
                        break;
                }
                break;
            case 'api':
                $v = getVersionAPI($getParams);
                switch ($method[0]) {
                    case 'users':
                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            "controller/device",
                            "controller/files",
                            $method[0]
                        ]);
                        break;
                    case 'auth':
                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            $method[0]
                        ]);
                        break;
                    case 'database':
                        use_($config['dirAPI'].$v, [
                            "controller/".$method[0],
                            $method[0]
                        ]);
                        break;
                    case 'complaints':
                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            "controller/".$method[0],
                            $method[0]
                        ]);
                        break;
                    case 'device':
                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            "controller/device",
                            $method[0]
                        ]);
                        break;
                    case 'email':
                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            $method[0]
                        ]);
                        break;
                    case 'password':
                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            "controller/".$method[0],
                            $method[0]
                        ]);
                        break;
                    case 'alias':
                        use_($config['dirAPI'].$v, [
                            "controller/".$method[0],
                            $method[0]
                        ]);
                        break;
                    case 'files':
                        use_($config['dirAPI'].$v, [
                            "controller/files",
                            $method[0]
                        ]);
                        break;
                    case 'sms':
                        require_once(ROOT_DIR . '/twilio.php');
                        use_($config['dirAPI'].$v, [
                            "controller/sms",
                            $method[0]
                        ]);
                        break;
                    case 'events':
                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            "controller/".$method[0],
                            $method[0]
                        ]);
                        break;
                    case 'time':
                        echo responseBuilder([], [(object)[
                            "currentTime" => time()
                        ]]);
                        break;
                    case 'shortener':
                        require_once(ROOT_DIR . '/libs/shortener.php');
                        use_($config['dirAPI'].$v, [
                            $method[0]
                        ]);
                        break;
                    case 'phone':
                        require_once(ROOT_DIR . '/libs/epochta.php');
                        require_once(ROOT_DIR . '/twilio.php');

                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            "controller/sms",
                            "controller/".$method[0],
                            $method[0]
                        ]);
                        break;
                    case 'scopes':
                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            "controller/".$method[0],
                            $method[0]
                        ]);
                        break;
                    case 'docs':
                        use_($config['dirAPI'].$v, [
                            $method[0]
                        ]);
                        break;
                    case 'messages':
                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            "controller/".$method[0],
                            $method[0]
                        ]);
                        break;
                    case 'contacts':
                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            "controller/".$method[0],
                            $method[0]
                        ]);
                        break;
                    case 'test':
                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            $method[0]
                        ]);
                        break;
                    case 'notification':
                        use_($config['dirAPI'].$v, [
                            "functions/auth",
                            "controller/auth",
                            $method[0]
                        ]);
                        break;
                    default:
                        echo responseBuilder(array(
                            0 => $_ERRORS['methodNotFound']
                        ), false);
                        break;
                }
                break;
            default:
                header("Location: http://google.com");
                exit;
                break;
        }
    }