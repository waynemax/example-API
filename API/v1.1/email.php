<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 25/10/2017
	 **/

	$config = config();
	$inParams = getInParams();
	$getParams = query2Array($inParams['getParams']);
	$reqHeaders = getallheaders();

	switch ($method[1]) {
		case 'set':
			use_($config['dirAPI'].$v, [
			    "functions/keys",
                "controller/email"
            ]);
			require_once ("libs/smtpClass2.php");
			require_once($config['dirAPI'].$v."/cases/email.set.php");
		    break;
		case 'activate':
			use_($config['dirAPI'].$v, "functions/keys");
			use_($config['dirAPI'].$v, "controller/email");
			require_once($config['dirAPI'].$v."/cases/email.activate.php");
		    break;
		case 'remove':
			use_($config['dirAPI'].$v, "controller/email");
			require_once($config['dirAPI'].$v."/cases/email.remove.php");
		    break;
		default:
            methodNotFound();
		    break;
	}