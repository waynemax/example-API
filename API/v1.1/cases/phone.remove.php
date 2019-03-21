<?php
    checkRoute();
    $errors = array();
    $data = array();
    $params = array();
    $strings = strings();
    $user_id = auth();
    user_phoneRemove($user_id);
    $data[0]['success'] = 'success';
    echo responseBuilder($errors, $data);
