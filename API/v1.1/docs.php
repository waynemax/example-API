<?php

    $config = config();
    $inParams = getInParams();
    $getParams = query2Array($inParams['getParams']);
    $reqHeaders = getallheaders();

    switch ($method[1]) {
        case 'generate':
            require_once($config['dirAPI'].$v."/cases/docs_generate.php");
            break;
        default:
            methodNotFound();
            break;
    }
