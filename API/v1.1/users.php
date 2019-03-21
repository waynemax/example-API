<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 08/09/2017
	 **/

	$config = config();
	$inParams = getInParams();
	$getParams = query2Array($inParams['getParams']);
	$reqHeaders = getallheaders();

	switch ($method[1]) {
		case 'photoUpdate':
			use_($config['dirAPI'].$v, "controller/photo");
			require_once($config['dirAPI'].$v."/cases/users.photoUpdate.php");
		    break;
		case 'photoRemove':
			use_($config['dirAPI'].$v, "controller/photo");
			require_once($config['dirAPI'].$v."/cases/users.photoRemove.php");
		    break;
		//case 'update': deleted
		case 'search':
			use_($config['dirAPI'].$v, [
			    "controller/database",
			    "controller/users"
            ]);
			require_once($config['dirAPI'].$v."/cases/users.search.php");
		    break;
		case 'get':
			use_($config['dirAPI'].$v, [
			    "controller/database",
			    "controller/users",
			    "controller/phone",
            ]);
			require_once($config['dirAPI'].$v."/cases/users.get.php");
		    break;
		//case 'join': deleted
		case 'deactivate':
			use_($config['dirAPI'].$v, "controller/users");
			require_once($config['dirAPI'].$v."/cases/users.deactivate.php");
		    break;
        case 'setOnline':
            use_($config['dirAPI'].$v, "controller/users");
            require_once($config['dirAPI'].$v."/cases/users.setOnline.php");
            break;
        case 'haveHash':
            use_($config['dirAPI'].$v, "controller/users");
            require_once($config['dirAPI'].$v."/cases/users.haveHash.php");
            break;
        case 'setHash':
            use_($config['dirAPI'].$v, "controller/users");
            require_once($config['dirAPI'].$v."/cases/users.setHash.php");
            break;
        case 'updateHash':
            use_($config['dirAPI'].$v, "controller/users");
            require_once($config['dirAPI'].$v."/cases/users.updateHash.php");
            break;
		default:
            methodNotFound();
		    break;
	}