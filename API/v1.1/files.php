<?php
	$config = config();
	$reqHeaders = getallheaders();
	$inParams = getInParams();
	$getParams = query2Array($inParams['getParams']);

	switch ($method[1]) {
		case 'get':
			require_once($config['dirAPI'].$v."/cases/files.get.php");
		    break;
        /**
         * Документация загрузки файла
         **/
        //files.upload.php
        case 'upload':
        default:
            methodNotFound();
            break;
	}