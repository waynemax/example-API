<?php
    /**
     * @author: Wayne Maxim
     * @description: remote data management system
     * @dateOfCreation: 11/09/2017
     **/

    $config = config();
    $inParams = getInParams();
    $getParams = query2Array($inParams['getParams']);
    $reqHeaders = getallheaders();

    switch ($method[1]) {
        case 'sync':
            use_($config['dirAPI'].$v, [
                "controller/phone",
                "controller/users"
            ]);
            require_once($config['dirAPI'].$v."/cases/contacts.sync.php");
            break;
        default:
            methodNotFound();
            break;
    }