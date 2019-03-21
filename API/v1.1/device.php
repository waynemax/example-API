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
		case 'add':
			require_once($config['dirAPI'].$v."/cases/device.add.php");
		    break;
		case 'tokenPushUpdate':
			require_once($config['dirAPI'].$v."/cases/device.tokenPushUpdate.php");
		    break;
        case 'removeByHash':
            require_once($config['dirAPI'].$v."/cases/device.removeByHash.php");
            break;
		default:
            methodNotFound();
		    break;
	}
?>