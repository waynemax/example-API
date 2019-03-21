<?php
    $config = config();
    $inParams = getInParams();
    $getParams = query2Array($inParams['getParams']);
    $reqHeaders = getallheaders();

    switch ($method[1]) {
        case 'mc':
            require_once($config['dirAPI'].$v."/cases/mc.test.php");
            break;
        default:
            methodNotFound();
            break;
    }
