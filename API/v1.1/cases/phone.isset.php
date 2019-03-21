<?php
    checkRoute();
    $errors = array();
    $data = array();
    $params = array();
    $_IN_ = checkInData("GET", NULL, NULL);
    $user_id = auth();
    $phoneIsset = user_phoneIsset($user_id);
    $data[0]['response'] = $phoneIsset;
    echo responseBuilder($errors, $data);