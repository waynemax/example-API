<?php
    checkRoute();
    $errors = array();
    $data = array();
    $params = [];
    $_IN_ = checkInData("GET", []);
    $user_id = auth();
    $userTags = getUserTags($user_id, true);
    $data[0]['tags'] = $userTags;
    echo responseBuilder($errors, $data);
