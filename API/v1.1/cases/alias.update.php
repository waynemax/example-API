<?php
    checkRoute();
	$errors = array();
	$data = array();
	$_IN_ = $_POST;
	if ($_SERVER['REQUEST_METHOD'] != "POST") {
		array_push($errors, addError($_ERRORS['onlyPOST'][0], $_ERRORS['onlyPOST'][1]));
		http_response_code(405);
		ethrow($errors);
	}
	$needFields = array(
		"alias" => true,
		"type" => true,
		"id" => true
	);
	foreach ($needFields as $key => $value) {
		if ($value == true && !$_IN_[$key]) {
			array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `".$key."`"));
			http_response_code(400);
		}
	}
	ethrow($errors);
    $user_id = auth();
	$id = num($_IN_['id']);
	$aliasExist = aliasExist($_IN_['alias']);
	if ($aliasExist) {
		array_push($errors, addError($_ERRORS['same'][0], $_ERRORS['same'][1]." `alias`"));
		http_response_code(200);
		ethrow($errors);
	}
	$type = num($_IN_['type']);
	switch ($type) {
		case 1:
			if ($user_id != $id) {
				array_push($errors, addError($_ERRORS['permission_denied'][0], $_ERRORS['permission_denied'][1]." `id`"));
				http_response_code(403);
				ethrow($errors);
			}
			aliasAdd($_IN_['alias'], $type, $user_id, "id".$user_id);
			userAliasUpdate($user_id, $_IN_['alias']);
		break;
		case 2:
			$getAuthorIdBlog = getAuthorIdBlog($id);
			if (!$getAuthorIdBlog['status']) {
				array_push($errors, addError(
					$_ERRORS['invalidField'][0],
					$_ERRORS['invalidField'][1]." `id`"
				));
				http_response_code(400);
				ethrow($errors);
			}
			if ($user_id != $getAuthorIdBlog['author_id']) {
				array_push($errors, addError(
					$_ERRORS['permission_denied'][0],
					$_ERRORS['permission_denied'][1]." Your id != `author_id`"
				));
				http_response_code(403);
				ethrow($errors);
			}
			aliasAdd($_IN_['alias'], $type, $id, "id".$id);
			blogAliasUpdate($id, $_IN_['alias']);
		break;
		default:
			array_push($errors, addError($_ERRORS['invalidField'][0], $_ERRORS['invalidField'][1]." `type`"));
			http_response_code(400);
			ethrow($errors);
		break;
	}
	$data[0]['status'] = 'success';
	echo responseBuilder($errors, $data);