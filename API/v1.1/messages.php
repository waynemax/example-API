<?php

    $config = config();
    $inParams = getInParams();
    $getParams = query2Array($inParams['getParams']);
    $reqHeaders = getallheaders();

    switch ($method[1]) {
        case 'createChat':
            $strings = strings();
            use_($config['dirAPI'].$v, "controller/users");
            require_once($config['dirAPI'].$v."/cases/messages.createChat.php");
            break;
        case 'getChats':
            $strings = strings();
            use_($config['dirAPI'].$v, [
                "controller/users",
                "controller/files"
            ]);
            require_once($config['dirAPI'].$v."/cases/messages.getChats.php");
            break;
        case 'leaveChat':
            use_($config['dirAPI'].$v, "controller/users");
            require_once($config['dirAPI'].$v."/cases/messages.leaveChat.php");
            break;
        case 'send':
            $strings = strings();
            use_($config['dirAPI'].$v, [
                "controller/users",
                "controller/files",
                "controller/device"
            ]);
            require_once($config['dirAPI'].$v."/cases/messages.send.php");
            break;
        case 'get':
            use_($config['dirAPI'].$v, [
                "controller/files"
            ]);
            require_once($config['dirAPI'].$v."/cases/messages.get.php");
            break;
        case 'getLastUserMessage':
            require_once($config['dirAPI'].$v."/cases/messages.getLastUserMessage.php");
            break;
        case 'getChatInfo':
            $strings = strings();
            use_($config['dirAPI'].$v, [
                "controller/files",
                "controller/users",
            ]);
            require_once($config['dirAPI'].$v."/cases/messages.getChatInfo.php");
            break;
        default:
            methodNotFound();
            break;
    }
