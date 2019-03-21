<?php
    checkRoute();
    $errors = array();
    $data = array();
    $_IN_ = checkInData("POST", [], []);
    $user_id = auth();
    $data[0] = users_updateHash($user_id);
    echo responseBuilder($errors, $data);