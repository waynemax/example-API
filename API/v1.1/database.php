<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 13/09/2017
	 **/

	$reqHeaders = getallheaders();
	$config = config();
	$inParams = getInParams();
	$getParams = query2Array($inParams['getParams']);

	switch ($method[1]) {
		case 'allInterests':
            use_($config['dirAPI'].$v, [
                "functions/auth",
                "controller/auth",
                "controller/tags"
            ]);
			require_once($config['dirAPI'].$v."/cases/database.allInterests.php");
		    break;
		case 'education':
			require_once($config['dirAPI'].$v."/cases/database.education.php");
		    break;
		case 'earnings':
			require_once($config['dirAPI'].$v."/cases/database.earnings.php");
		    break;
		case 'confessions':
			require_once($config['dirAPI'].$v."/cases/database.confessions.php");
		    break;
		case 'socialStatus':
			require_once($config['dirAPI'].$v."/cases/database.socialStatus.php");
		    break;
		case 'cities':
			require_once($config['dirAPI'].$v."/cases/database.cities.php");
		    break;
		case 'regions':
			require_once($config['dirAPI'].$v."/cases/database.regions.php");
		    break;
		case 'countries':
			require_once($config['dirAPI'].$v."/cases/database.countries.php");
		    break;
		case 'continents':
			require_once($config['dirAPI'].$v."/cases/database.continents.php");
		    break;
		default:
            methodNotFound();
		    break;
	}