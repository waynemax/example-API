<?php
    checkRoute();
	$errors = array();
	$data = array();
    $mandatoryParams = ["complaint_type", "object_id"];
	$_IN_ = checkInData("POST", $mandatoryParams, []);
	$user_id = auth();
	$object_id = num($_IN_['object_id']);
	switch ($method[1]) {
		case 'user':
			$mainType = 1;
			if (!userExistById($object_id)) {
				array_push($errors, addError(
					$_ERRORS['access_denied'][0],
					$_ERRORS['access_denied'][1]." `user` not exist"
				));
				http_response_code(403);
				ethrow($errors);
			}
		    break;
		case 'comment':
			$mainType = 4;
			if (!commentExistById($object_id)) {
				array_push($errors, addError(
					$_ERRORS['access_denied'][0],
					$_ERRORS['access_denied'][1]." `comment` not exist"
				));
				http_response_code(403);
				ethrow($errors);
			}
		    break;
		case 'blog':
			$mainType = 3;
			if (!blogExistById($object_id)) {
				array_push($errors, addError(
					$_ERRORS['access_denied'][0],
					$_ERRORS['access_denied'][1]." `blog` not exist"
				));
				http_response_code(403);
				ethrow($errors);
			}
		    break;
		case 'post':
			$mainType = 2;
			if (!postExist($object_id)) {
				array_push($errors, addError(
					$_ERRORS['access_denied'][0],
					$_ERRORS['access_denied'][1]." `object_id` not exist"
				));
				http_response_code(403);
				ethrow($errors);
			}
		    break;
	}
	$optionalDescription = NULL;
	foreach ($_IN_ as $k => $v) {
		switch ($k) {
			case 'description':
				if (strlen($v) > 0) {
						$optionalDescription = $v;
						$data[0][$k] = $v;
				} else {
					array_push($errors, addError(
						$_ERRORS['invalidField'][0],
						$_ERRORS['invalidField'][1]." `".$k."`"
					));
					http_response_code(400);
					ethrow($errors);
				}
			    break;
		}
	}
	ethrow($errors);
	$complaintAdd = complaintAdd($user_id, $object_id, $mainType, $optionalDescription, $_IN_['complaint_type']);
	if (!$complaintAdd['status']) {
		array_push($errors, addError(
			$_ERRORS['unknownError'][0],
			$_ERRORS['unknownError'][1]." complaint not added"
		));
		http_response_code(400);
		ethrow($errors);
	}
	$data[0]['status'] = 'success';
	echo responseBuilder($errors, $data);