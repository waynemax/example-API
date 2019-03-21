<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 08/09/2017
	 **/

	$config = config();
	$reqHeaders = getallheaders();
	$inParams = getInParams();
	$getParams = query2Array($inParams['getParams']);

	switch ($method[1]) {
		case 'relevance':
			require_once($config['dirAPI'].$v."/cases/auth.relevance.php");
			break;
		case 'remove':
			require_once($config['dirAPI'].$v."/cases/auth.remove.php");
		break;
		case 'removeAll':
			require_once($config['dirAPI'].$v."/cases/auth.removeAll.php");
		    break;
		case 'refresh':
			require_once($config['dirAPI'].$v."/cases/auth.refresh.php");
		    break;
		case 'token':
			require_once($config['dirAPI'].$v."/cases/auth.token.php");
		    break;
		case 'get':
			require_once($config['dirAPI'].$v."/cases/auth.get.php");
		    break;
		default:
            methodNotFound();
		    break;
	}