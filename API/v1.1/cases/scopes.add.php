<?php
    checkRoute();
    $errors = array();
    $data = array();
    $params = array();
    $_IN_ = checkInData("POST", ["id","scopes_id"], []);
    $key = escape($_GET['k']);
    $user_id = auth();
    $id = num($_IN_["id"]);
    $scopes_id = num($_IN_["scopes_id"]);
    if (!userExistById($id)) {
        array_push($errors, addError($_ERRORS['invalidField'][0], $_ERRORS['invalidField'][1]." id"));
        http_response_code(405);
        ethrow($errors);
    }
    $user_scopes = $key ? [$key] : getScopesById($user_id);
    $for_scopes = getScopesById($id);
    if (!isAllowChangeScopes($user_scopes, $for_scopes) && $user_id != $id) {
        array_push($errors, addError($_ERRORS['access_denied'][0], $_ERRORS['access_denied'][1]));
        http_response_code(405);
        ethrow($errors);
    }
    if (!isAllowChangeScopes($user_scopes, [$scopes_id])) {
        array_push($errors, addError($_ERRORS['permission_denied'][0], $_ERRORS['permission_denied'][1]." scopes_id"));
        http_response_code(405);
        ethrow($errors);
    }
    if (in_array($scopes_id, $for_scopes)) {
        array_push($errors, addError($_ERRORS['same'][0], $_ERRORS['same'][1]));
        http_response_code(405);
        ethrow($errors);
    }
    $data['data'] =  setScopes($user_id, $id, $scopes_id);
    echo responseBuilder($errors, (object) $data);
    
