<?php
	$config = config();
	$inParams = getInParams();
	$getParams = query2Array($inParams['getParams']);
	$reqHeaders = getallheaders();

	switch ($method[1]) {
		case 'link':
			require_once($config['dirAPI'].$v."/cases/shortener.link.php");
		    break;
		default:
            methodNotFound();
		    break;
	}