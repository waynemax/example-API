<?php
    $config = config();
    $inParams = getInParams();
    $getParams = query2Array($inParams['getParams']);
    $reqHeaders = getallheaders();

    switch ($method[1]) {
        case 'add':
            use_($config['dirAPI'].$v, [
                "controller/users"
            ]);
            require_once($config['dirAPI'].$v."/cases/scopes.add.php");
            break;
        case 'get':
            use_($config['dirAPI'].$v, [
                "controller/users"
            ]);
            require_once($config['dirAPI'].$v."/cases/scopes.get.php");
            break;
        case 'remove':
            use_($config['dirAPI'].$v, [
                "controller/users"
            ]);
            require_once($config['dirAPI'].$v."/cases/scopes.remove.php");
            break;
        default:
            methodNotFound();
            break;
    }