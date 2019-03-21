<?php
    checkRoute();
    $errors = array();
    $data = array();
    $params = [];
    $_IN_ = checkInData("POST", ["ids"], []);
    $user_id = auth();
    foreach ($_IN_ as $k => $v) {
        switch ($k) {
            case 'ids':
                if (is_null($v)) {
                    array_push($errors, addError(
                        $_ERRORS['unknownError'][0],
                        $_ERRORS['unknownError'][1]." `".$k."`"
                    ));
                    http_response_code(400);
                    ethrow($errors);
                }
                $v = explode(",", escape($v));
                $tags_valid = tags_valid($v);
                $tags_Ids = tags_getIds($tags_valid);
                if (empty($tags_Ids)) {
                    array_push($errors, addError(
                        $_ERRORS['unknownError'][0],
                        $_ERRORS['unknownError'][1]." `".$k."`"
                    ));
                    http_response_code(400);
                    ethrow($errors);
                }
                break;
        }
    }
    $myTags = getUserTags($user_id);
    $myTagsAndInTags = array_unique(array_merge($myTags, $tags_Ids), SORT_NUMERIC); // общий массив
    $newCollectionTags = [];
    foreach ($myTagsAndInTags as $key => $value) {
        if (!in_array($value, $myTags) && in_array($value, $tags_Ids)) {
            $newCollectionTags[] = $value;
        }
        if (in_array($value, $myTags) && !in_array($value, $tags_Ids)) {
            $newCollectionTags[] = $value;
        }
    }
    user_tagsUpdate($user_id, $newCollectionTags);
    $data[0]['tags'] = $newCollectionTags;
    echo responseBuilder($errors, $data);
