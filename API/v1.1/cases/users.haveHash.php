<?php
    checkRoute();
    $errors = array();
    $data = array();
    $_IN_ = checkInData("GET", [], []);
    $user_id = auth();
    $data[0] = users_haveHash($user_id);
    echo responseBuilder($errors, $data);
