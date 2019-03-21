<?php
    checkRoute();
    $errors = array();
    $data = array();
    $_IN_ = checkInData("POST", [], []);
    $user_id = auth();
    $data[0] = users_setOnline($user_id);
    echo responseBuilder($errors, $data);
