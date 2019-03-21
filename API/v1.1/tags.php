<?php
    /**
     * @author: Wayne Maxim
     * @description: remote data management system
     **/

    $config = config();
    $reqHeaders = getallheaders();
    $inParams = getInParams();
    $getParams = query2Array($inParams['getParams']);

    switch ($method[1]) {
        case 'update':
            require_once($config['dirAPI'].$v."/cases/tags.update.php");
            break;
        case 'get':
            require_once($config['dirAPI'].$v."/cases/tags.get.php");
            break;
        default:
            methodNotFound();
            break;
    }