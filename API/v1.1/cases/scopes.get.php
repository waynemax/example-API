<?php
    checkRoute();
    $errors = array();
    $data = array();
    $params = array();
    $_IN_ = checkInData("GET", ["id"], []);
    $user_id = auth();
    $user_scopes = getScopesById($user_id);
    $id = num($_IN_["id"]);
    if (!userExistById($id)) {
        array_push($errors, addError($_ERRORS['not_found'][0], $_ERRORS['not_found'][1]));
        http_response_code(405);
        ethrow($errors);
    }
    $allow_scopes = [SCOPE_SUPERVISOR_ID, SCOPE_ADMIN_ID, SCOPE_MODERATOR_ID, SCOPE_FINANCE_ID];
    if ($user_id != $id && !in_array(max($user_scopes), $allow_scopes)) {
        array_push($errors, addError($_ERRORS['access_denied'][0], $_ERRORS['access_denied'][1]));
        http_response_code(405);
        ethrow($errors);
    }
    $data['data']['scopes'] = $user_id == $id ? $user_scopes : getScopesById($id);
    echo responseBuilder($errors, (object) $data);