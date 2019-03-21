<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 07/12/2017
	 **/

	$config = config();
	$reqHeaders = getallheaders();
	$inParams = getInParams();
	$getParams = query2Array($inParams['getParams']);

	switch ($method[1]) {
		case 'user':
			use_($config['dirAPI'].$v, [
				"controller/users"
			]);
			require_once($config['dirAPI'].$v."/cases/complaints.add.php");
		    break;
		case 'comment':
			use_($config['dirAPI'].$v, [
				"controller/comments"
			]);
			require_once($config['dirAPI'].$v."/cases/complaints.add.php");
		    break;
		case 'blog':
			use_($config['dirAPI'].$v, [
				"controller/blog"
			]);
			require_once($config['dirAPI'].$v."/cases/complaints.add.php");
		    break;
		case 'post':
			use_($config['dirAPI'].$v, [
				"controller/posts"
			]);
			require_once($config['dirAPI'].$v."/cases/complaints.add.php");
		    break;
		default:
            methodNotFound();
		    break;
	}