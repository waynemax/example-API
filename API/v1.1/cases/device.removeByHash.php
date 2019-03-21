<?php
    checkRoute();
    $errors = array();
    $data = array();
    if ($_SERVER['REQUEST_METHOD'] != "POST") {
        array_push($errors, addError($_ERRORS['onlyPOST'][0], $_ERRORS['onlyPOST'][1]));
        http_response_code(405);
        ethrow($errors);
    }
    $needFields = array(
        "hash_device" => true,
    );
    foreach ($needFields as $key => $value) {
        if ($value == true && !$_POST[$key]) {
            array_push($errors, addError($_ERRORS['needFields'][0], $_ERRORS['needFields'][1]." `".$key."`"));
            http_response_code(400);
        }
    }
    ethrow($errors);
    device_removeByHash($_POST['hash_device']);
    $data[0]['status'] = true;
    echo responseBuilder($errors, $data);