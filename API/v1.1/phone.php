<?php
    /**
     * @author: Wayne Maxim
     * @description: remote data management system
     **/

    $config = config();
    $reqHeaders = getallheaders();
    $inParams = getInParams();
    $getParams = query2Array($inParams['getParams']);

    define("EMAIL_CODE_ACTIVATION_TYPE", 1);
    define("PHONE_CODE_ACTIVATION_TYPE", 2);

    switch ($method[1]) {
        case 'add':
            use_($config['dirAPI'].$v, "functions/keys");
            require_once($config['dirAPI'].$v."/cases/phone.add.php");
            break;
        case 'check':
            use_($config['dirAPI'].$v, "functions/keys");
            require_once($config['dirAPI'].$v."/cases/phone.check.php");
            break;
        case 'activate':
            use_($config['dirAPI'].$v, "functions/keys");
            require_once($config['dirAPI'].$v."/cases/phone.activate.php");
            break;
        case 'isset':
            use_($config['dirAPI'].$v, "functions/keys");
            require_once($config['dirAPI'].$v."/cases/phone.isset.php");
            break;
        case 'sendSmsCode':
            use_($config['dirAPI'].$v, "functions/keys");
            require_once($config['dirAPI'].$v."/cases/phone.sendSmsCode.php");
            break;
        case 'remove':
            require_once($config['dirAPI'].$v."/cases/phone.remove.php");
            break;
        default:
            methodNotFound();
            break;
    }
