<?php
	$config = config();
	$inParams = getInParams();
	$getParams = query2Array($inParams['getParams']);
	$reqHeaders = getallheaders();

	switch ($method[1]) {
		case 'send':
			require_once($config['dirAPI'].$v."/cases/sms.send.php");
		    break;
		default:
            methodNotFound();
		    break;
	}