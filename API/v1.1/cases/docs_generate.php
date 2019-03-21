<?php
    checkRoute();

    if ($_GET['access'] != '') {
        print_r("Требуется пароль");
        exit;
    }

    define('AUTH_SECTION_ID', 1);
    define('USERS_SECTION_ID', 2);
    //define('BLOG_SECTION_ID', 3);
    define('ALIAS_SECTION_ID', 4);
    define('DATABASE_SECTION_ID', 5);
    define('DEVICE_SECTION_ID', 6);
    define('EMAIL_SECTION_ID', 7);
    define('FILES_SECTION_ID', 8);
    define('PASSWORD_SECTION_ID', 9);
    //define('POSTS_SECTION_ID', 10);
    define('SHORTENER_SECTION_ID', 11);
    define('SMS_SECTION_ID', 12);
    //define('SUBSCRIBERS_SECTION_ID', 13);
    //define('SUBSCRIPTIONS_SECTION_ID', 14);
    //define('COMMENTS_SECTION_ID', 15);
    //define('EVENTS_SECTION_ID', 16);
    //define('MONEY_SECTION_ID', 17);
    define('TIME_SECTION_ID', 18);
    //define('LIKES_SECTION_ID', 19);
    define('COMPLAINTS_SECTION_ID', 20);
    //define('COUPONS_SECTION_ID', 21);
    define('SCOPES_SECTION_ID', 22);
    define('TAGS_SECTION_ID', 23);
    //define('ADVERTISING_SECTION_ID', 24);
    //define('REQUISITES_SECTION_ID', 25);

    $sections = [
        AUTH_SECTION_ID => ["AUTH_SECTION_ID", "Авторизация"],
        USERS_SECTION_ID => ["USERS_SECTION_ID", "Пользователи"],
        //BLOG_SECTION_ID => ["BLOG_SECTION_ID", "Блоги"],
        ALIAS_SECTION_ID => ["ALIAS_SECTION_ID", "Адреса страниц"],
        DATABASE_SECTION_ID => ["DATABASE_SECTION_ID", "Каталоги"],
        DEVICE_SECTION_ID => ["DEVICE_SECTION_ID", "Устройства"],
        EMAIL_SECTION_ID => ["EMAIL_SECTION_ID", "Электронная почта"],
        FILES_SECTION_ID => ["FILES_SECTION_ID", "Работа с файлами"],
        PASSWORD_SECTION_ID => ["PASSWORD_SECTION_ID", "Пароли"],
        //POSTS_SECTION_ID => ["POSTS_SECTION_ID", "Посты"],
        SHORTENER_SECTION_ID => ["SHORTENER_SECTION_ID", "Сокращение ссылок"],
        SMS_SECTION_ID => ["SMS_SECTION_ID", "СМС рассылка"],
        //SUBSCRIBERS_SECTION_ID => ["SUBSCRIBERS_SECTION_ID", "Подписчики"],
        //SUBSCRIPTIONS_SECTION_ID => ["SUBSCRIPTIONS_SECTION_ID", "Подписки"],
        //COMMENTS_SECTION_ID => ["COMMENTS_SECTION_ID", "Комментарии"],
        //EVENTS_SECTION_ID => ["EVENTS_SECTION_ID", "События"],
        //MONEY_SECTION_ID => ["MONEY_SECTION_ID", "Финансы"],
        TIME_SECTION_ID => ["TIME_SECTION_ID", "Время"],
        //LIKES_SECTION_ID => ["LIKES_SECTION_ID", "Лайки"],
        COMPLAINTS_SECTION_ID => ["COMPLAINTS_SECTION_ID", "Жалобы"],
        //COUPONS_SECTION_ID => ["COUPONS_SECTION_ID", "Купоны"],
        SCOPES_SECTION_ID => ["SCOPES_SECTION_ID", "Права"],
        TAGS_SECTION_ID => ["TAGS_SECTION_ID", "Интересы"],
        //ADVERTISING_SECTION_ID => ["ADVERTISING_SECTION_ID", "Реклама"],
        //REQUISITES_SECTION_ID => ["REQUISITES_SECTION_ID", "Реквизиты"]
    ];

    $methods_files = [];
    $count_files = 0;
    $beginStr = "@BEGIN;";
    $endStr = "@END;";
    $arrParams = [];

    foreach (scandir(__DIR__, true) as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) == "php" && $file != "docs_generate.php") {
            $count_files++;
            $file_path = __DIR__.DIRECTORY_SEPARATOR.$file;
            $file_content = file_get_contents($file_path);
            $beginPos = strripos($file_content, $beginStr);
            if ($beginPos) {
                $endPos = strripos($file_content, $endStr);
                if ($endPos) {
                    $strLength = $endPos - $beginPos;
                    $file_stringDescription = substr($file_content, $beginPos + strlen($beginStr), $strLength - strlen($beginStr));
                    $file_stringDescription = str_replace('\r','', $file_stringDescription);
                    $file_explodeDescription = explode("\n", $file_stringDescription);
                    foreach ($file_explodeDescription as $stroke) {
                        if ($stroke) {
                            $stroke_explode1 = explode(";", trim($stroke));
                            $stroke_explode2 = explode("==", trim($stroke_explode1[1]));
                            if (count($stroke_explode2) == 2) {
                                switch ($stroke_explode2[0]) {
                                    case 'optional':
                                    case 'response':
                                    case 'mandatory':
                                        if ($stroke_explode2[1]) {
                                            $ex = explode(",", $stroke_explode2[1]);
                                            if (count($ex) > 0) {
                                                $strokeJson = [];
                                                foreach ($ex as $vv) {
                                                    $strokeJsonEx = explode("|", $vv);
                                                    $strokeJson[$strokeJsonEx[0]] = $strokeJsonEx[1];
                                                }
                                            } else {
                                                $stroke_explode2[1] = $stroke_explode2[1];
                                            }
                                            $stroke_explode2[1] = $strokeJson;
                                        }
                                        break;
                                }
                                $arrParams[$stroke_explode2[0]] = $stroke_explode2[1];
                            }
                        }
                    }
                    $methods_files[preg_replace('/\\.[^.\\s]{3,4}$/', '', $file)] = $arrParams;
                }
            }
        }
    }
    cout([
        'count' => $count_files,
        'sections' => $sections,
        'methods' => $methods_files
    ], 1);