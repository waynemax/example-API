<?php
    ini_set('display_errors', 'Off');
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Origin, Accept, Auth, auth, Auth-Token');
    header('Content-Type:application/json;charset=UTF-8');

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        header('HTTP/1.1 200 OK');
        die;
    }