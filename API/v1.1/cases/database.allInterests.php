<?php
    checkRoute();
	$errors = array();
	$data = array();
    $_IN_ = checkInData("GET", [], []);
    $user_id = auth(false, true);
	$subInterests = getSubTags();
	$subInterests = $subInterests['items'];
	$subInterestsSort = [];
    if (!is_null($user_id)) {
        $getUserTags = getUserTags($user_id);
    }
	foreach ($subInterests as $key => $value) {
	    if (!is_null($user_id)) {
            if (in_array((integer) $value['id'], $getUserTags)) {
                $value["selected"] = true;
            } else {
                $value["selected"] = false;
            }
        }
		if (is_null($subInterestsSort[$value['tag_group_id']])) {
			$subInterestsSort[$value['tag_group_id']] = [];
		} else {
			array_push($subInterestsSort[$value['tag_group_id']], $value);
		}
	}
	$response = getTags();
	$interests = $response['items'];
	foreach ($interests as $key => $value) {
		$interests[$key]['list'] = $subInterestsSort[$value['id']];
	}
	array_push($data, $interests);
	echo responseBuilder($errors, $data);