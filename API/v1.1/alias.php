<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 26/10/2017
	 **/

	$config = config();
	$inParams = getInParams();
	$getParams = query2Array($inParams['getParams']);
	$reqHeaders = getallheaders();

	switch ($method[1]) {
		case 'get':
			require_once($config['dirAPI'].$v."/cases/alias.get.php");
		    break;
		case 'update':
			use_($config['dirAPI'].$v, [
				"functions/auth",
				"controller/auth",
				"controller/blog"
			]);
			require_once($config['dirAPI'].$v."/cases/alias.update.php");
		    break;
		default:
            methodNotFound();
		    break;
	}
?>