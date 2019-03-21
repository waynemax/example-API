<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 27/10/2017
	 **/

	$config = config();
	$inParams = getInParams();
	$getParams = query2Array($inParams['getParams']);
	$reqHeaders = getallheaders();

	switch ($method[1]) {
		case 'update':
			require_once($config['dirAPI'].$v."/cases/password.update.php");
		    break;
		case 'recovery':
			use_($config['dirAPI'].$v, [
					"functions/keys",
					"controller/users",
					"controller/phone",
					"controller/email"
				]
			);
			require_once("libs/smtpClass2.php");
			require_once(ROOT_DIR . '/epochta.php');
			require_once(ROOT_DIR . '/twilio.php');
			require_once($config['dirAPI'].$v."/cases/password.recovery.php");
		break;
		default:
            methodNotFound();
		    break;
	}
?>