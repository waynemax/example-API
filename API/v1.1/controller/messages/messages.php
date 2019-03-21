<?php
    function system_messages_get(int $user_id, array $_IN_PARAMS) {
        global $_ERRORS;
        $errors = [];
        $methodTypeCount = 0;
        $original_chat_id = $_IN_PARAMS['original_chat_id'] ? num($_IN_PARAMS['original_chat_id']) : false;
        if ($original_chat_id) {
            $checkUserInConversationById = system_messages_checkUserInConversationById($original_chat_id, $user_id);
            if (!$checkUserInConversationById['status']) {
                array_push($errors, addError(
                    $_ERRORS['userIsNotInChat'][0],
                    $_ERRORS['userIsNotInChat'][1]
                ));
                http_response_code(400);
                ethrow($errors);
            }
        }
        $count = 20;
        $offset = 0;
        $lpSearch = '';
        $lp = false;
        $rev = true;
        $useMc = false;
        $last_message_id = false;
        foreach ($_IN_PARAMS as $k => $v) {
            switch ($k) {
                case 'count':
                    $count = num($v) < 200 ? num($v) : 20;
                    break;
                case 'offset':
                    $offset = num($v);
                    break;
                case 'ts':
                    $methodTypeCount++;
                    $lp = true;
                    $ts = num($v);
                    $yesterdayTS = time() - 10800;
                    if ($ts < $yesterdayTS) {
                        array_push($errors, addError(
                            $_ERRORS['notValidTs'][0],
                            $_ERRORS['notValidTs'][1]
                        ));
                        http_response_code(400);
                        ethrow($errors);
                    }
                    $lpSearch = " && `create_date` >= '".$ts."'";
                    break;
                case 'last_message_id':
                    $lp = true;
                    $last_message_id = num($v);
                    $lpSearch = " && `id` > '".$last_message_id."'";
                    break;
                case 'rev':
                    $rev = boolval($v);
                    break;
                case 'use_mc':
                    $useMc = boolval($v);
                    break;
            }
        }
        $searchWithoutLMID = false;
        if ($last_message_id !== false && $last_message_id == 0) {
            $searchWithoutLMID = true;
        }
        if (($useMc && !is_numeric($last_message_id)) || ($methodTypeCount > 1)) {
            array_push($errors, addError(
                $_ERRORS['unknownError'][0],
                $_ERRORS['unknownError'][1]." `last_message_id` || `ts` || (`last_message_id` && `use_mc`) (0)"
            ));
            http_response_code(400);
            ethrow($errors);
        }
        if ($original_chat_id) {
            $chatSearch = "`chat_id` = '".$original_chat_id."'";
        } else {
            if (!$lp) {
                array_push($errors, addError(
                    $_ERRORS['needFields'][0],
                    $_ERRORS['needFields'][1]." `last_message_id` || `ts` *"
                ));
                http_response_code(400);
                ethrow($errors);
            }
            $pre_query = "select original_chat_id from chats_peers  where chats_peers.author_id = '".$user_id."'";
            $sf = sfetch($pre_query, 1);
            if (!$sf) {
                return [
                    'error' => 'user_chatNotFound',
                    'ts' => time(),
                    'last_message_id' => $last_message_id,
                    'count' => 0,
                    'items' => []
                ];
            }
            $valuesKeys = join(',', getValuesByKey($sf, 'original_chat_id'));
            $chatSearch = "`chat_id` in(".$valuesKeys.")";
        }
        $revSort = $rev ? 'desc' : 'asc';
        $treatmentParams = [];
        if ($_IN_PARAMS['preview_length']) {
            $treatmentParams['preview_length'] = (int) $_IN_PARAMS['preview_length'];
        }
        if (!$useMc) {
            if (!$lp) {
                $query = select(['*'], ['messages'], $chatSearch, ['`create_date` ' . $revSort], $offset, $count);
                $allCount = select(['count(id)'], ['messages'], $chatSearch, ['`create_date` ' . $revSort], 'no', 'no');
                $allCountFetch = sfetch($allCount, true);
                $getMessages = sfetch($query, true);
                if ($getMessages) {
                    $messages = messages_objectTreatment($getMessages, $treatmentParams);
                    return [
                        'ts' => time(),
                        'last_message_id' => $messages['last_message_id'],
                        'allCount' => $allCountFetch[0]['count(id)'],
                        'count' => count($messages['items']),
                        'items' => $messages['items']
                    ];
                }
            } else {
                $query = "select * from `messages` where ".$chatSearch. $lpSearch . " order by `create_date` " . $revSort;
                $endtime = time() + 25;
                while (time() <= $endtime) {
                    $getMessages = sfetch($query, true);
                    if ($getMessages) {
                        $messages = messages_objectTreatment($getMessages, $treatmentParams);
                        return [
                            'ts' => time(),
                            'last_message_id' => $messages['last_message_id'],
                            'count' => count($messages['items']),
                            'items' => $messages['items']
                        ];
                    }
                }
            }
        } else {
            $endtime = time() + 25;
            while (time() <= $endtime) {
                $lastChatPoint = (int) messages_lp_getPointLastMessageIDOfChat($original_chat_id);
                if (!$lastChatPoint) {
                    $db_last_message_id = system_messages_getLastUserMessage($user_id);
                    if ($db_last_message_id) {
                        $lastChatPoint = (int) $db_last_message_id['items'][0]['original_message_id'];
                    } else {
                        array_push($errors, addError(
                            $_ERRORS['longPollError1'][0],
                            $_ERRORS['longPollError1'][1]
                        ));
                        http_response_code(400);
                        ethrow($errors);
                    }
                }
                if ($lastChatPoint > $last_message_id) {
                    $query = "select * from `messages` where ".$chatSearch . $lpSearch . " order by `create_date` " . $revSort;
                    $getMessages = sfetch($query, true);
                    if ($getMessages) {
                        $messages = messages_objectTreatment($getMessages, $treatmentParams);
                        return [
                            'ts' => time(),
                            'last_message_id' => $messages['last_message_id'],
                            'count' => count($messages['items']),
                            'items' => $messages['items']
                        ];
                    }
                }
            }
        }
        $new_last_message_id = system_messages_getLastUserMessage($user_id)['items'][0]['original_message_id'];
        return [
            'ts' => time(),
            'last_message_id' => $new_last_message_id,
            'count' => 0,
            'items' => []
        ];
    }

    function messages_getLastMessageAtChatId(int $original_chat_id) {
        $query = select(['original_message_id'],['messages_links'],"`original_chat_id` = '".$original_chat_id."'",["id desc"],0,1);
        $sf = sfetch($query);
        if (!$sf) {
            return [
                'status' => false
            ];
        } else {
            $lastCopyMessageId = (int)$sf['original_message_id'];
            $sff = sfetch(select(['id','main_type_message','create_date','message_context'],['messages'],"`id` = '".$lastCopyMessageId ."'",false,0,1));
            if (!$sff) {
                return [
                    'status' => false
                ];
            } else {
                return [
                    'status' => true,
                    'items' => [$sff]
                ];
            }
        }
    }

    function messages_setNewLastUpdateTime(int $original_chat_id) {
        squery("UPDATE `chats` SET `last_update_time` = '".time()."' WHERE `id` = '".num($original_chat_id)."';");
    }

    function system_messages_getLastUserMessage(int $user_id) {
       $query = select(['original_message_id','create_date'],['messages_links'],"`author_id` = '".num($user_id)."'",['id desc'],0,1);
       $sf = sfetch($query);
       if (!$sf) {
           return [
               'items' => [
                   [
                       'original_message_id' => 0,
                       'create_date' => time()
                   ]
               ]
           ];
       } else {
           return [
               'items' => [$sf]
           ];
       }
    }

    function messages_objectTreatment($data, $params = []) {
        $lastMessageId = 0;
        $tdata = [];
        foreach ($data as $k => $v) {
            $tdata[$k] = [];
            foreach ($v as $kk => $vv) {
                switch ($kk) {
                    case 'images':
                        if (!is_null($vv)) {
                            if ($vv) {
                                $ex = explode(",", $vv);
                                if (count($ex) > 0) {
                                    $images = [];
                                    foreach ($ex as $kkk => $vvv) {
                                        $image = filesShorterV2_de($vvv);
                                        $oSize = explode("+", $image)[1];
                                        $imageExplode = explode("|", $image);
                                        $image = $imageExplode[0]."|".$imageExplode[1]."|".$imageExplode[2];
                                        $imageExplode2 = explode("+", $image);
                                        $file = $imageExplode2[0];
                                        $ext = explode("/", $imageExplode[0])[3];
                                        $buildImageFileObject = buildImageFileObject($file, $oSize, $ext);
                                        array_push($images, $buildImageFileObject);
                                    }
                                    $tdata[$k][$kk] = $images;
                                }
                            }
                        }
                        break;
                    case 'create_date':
                    case 'author_id':
                    case 'chat_id':
                    case 'main_type_message':
                        $tdata[$k][$kk] = (int) $vv;
                        break;
                    case 'id':
                        $tdata[$k][$kk] = (int) $vv;
                        if ((int) $vv > $lastMessageId) {
                            $lastMessageId = (int) $vv;
                        }
                        break;
                    case 'message_context':
                        $tdata[$k][$kk] = $vv;
                        if ($params['preview_length']) {
                            $tdata[$k][$kk] = mb_substr($vv,0, num($params['preview_length']),'UTF-8');
                        }
                        break;
                }
            }
        }
        return [
            'last_message_id' => $lastMessageId,
            'items' => $tdata
        ];
    }

    function messages_lp_setPointLastMessageTs(int $user_id, $point) {
        return mc_set('messages_lastTsByUserId'.$user_id, $point, 3600);
    }

    function messages_lp_getPointLastMessageTs(int $user_id) {
        return mc_get('messages_lastTsByUserId'.$user_id);
    }

    function messages_lp_setPointLastMessageTsOfChat(int $chat_id, $ts) {
        return mc_set('messages_lastTsByChatId'.$chat_id, $ts, 86400);
    }

    function messages_lp_getPointLastMessageTsOfChat(int $chat_id) {
        return mc_get('messages_lastTsByChatId'.$chat_id);
    }

    function messages_lp_setPointLastMessageIDOfChat(int $chat_id, $ts) {
        return mc_set('messages_lastIDByChatId'.$chat_id, $ts, 86400);
    }

    function messages_lp_getPointLastMessageIDOfChat(int $chat_id) {
        return mc_get('messages_lastIDByChatId'.$chat_id);
    }

    function system_messages_send(int $user_id, array $_IN_PARAMS) {
        global $_ERRORS, $strings;
        $errors = [];
        $params = [];
        $params['user_id'] = $user_id;
        if ($_IN_PARAMS['user_ids'] && $_IN_PARAMS['chat_id']) {
            array_push($errors, addError(
                $_ERRORS['invalidField'][0],
                $_ERRORS['invalidField'][1] . " `chat_id` || `user_ids`"
            ));
            http_response_code(400);
            ethrow($errors);
        }
        $attachmentsFlag = false;
        $imagesFlag = false;
        $videosFlag = false;
        foreach ($_IN_PARAMS as $key => $value) {
            switch ($key) {
                case 'attachment':
                    $attachments = [];
                    if (!is_null($value)) {
                        if ($value) {
                            $value = json_decode($value, true);
                            foreach ($value as $kk => $vv) {
                                switch ($kk) {
                                    case 'images':
                                        if (count($value[$kk]) > 0) {
                                            $filesGet = filesGet($vv);
                                            if ($filesGet['status']) {
                                                $count = count($filesGet[$kk]);
                                                if ($count > 0) {
                                                    if ($count > $strings['uploads']['images']['maxUploadCount']) {
                                                        err('maxUploadCountImages', 'maxUploadCountImages', 400);
                                                    }
                                                    $attachmentsFlag = true;
                                                    $imagesFlag = true;
                                                    $attachments['images'] = [];
                                                    foreach ($filesGet['images'] as $kkk => $vvv) {
                                                        array_push($attachments['images'], filesShorterV2_en($vvv));
                                                    }
                                                    $params['attachment']['images'] = join(',', $attachments['images']);
                                                }
                                            }
                                        }
                                        break;
                                }
                            }
                        }
                    }

                    break;
                case 'message':
                    $params['message_context'] = epost($value);
                    if (strlen($params['message_context']) < 1) {
                        array_push($errors, addError(
                            $_ERRORS['shortMessage'][0],
                            $_ERRORS['shortMessage'][1] . " `" . $key ."`"
                        ));
                        http_response_code(400);
                        ethrow($errors);
                    }
                    if (!strlen($params['message_context']) > 10000) {
                        array_push($errors, addError(
                            $_ERRORS['longMessage'][0],
                            $_ERRORS['longMessage'][1] . " `" . $key ."`"
                        ));
                        http_response_code(400);
                        ethrow($errors);
                    }
                    break;
                case 'chat_id':
                    $peersInChat = (array) system_messages_getUserIdsOfChat(num($value));
                    if (!in_array($user_id, $peersInChat)) {
                        array_push($errors, addError(
                            $_ERRORS['access_denied'][0],
                            $_ERRORS['access_denied'][1] . " `" . $key . "`. You are not in chat"
                        ));
                        http_response_code(400);
                        ethrow($errors);
                    }
                    $params['chat_id'] = (int) $_IN_PARAMS[$key];
                    $params['peers'] = $peersInChat;
                    break;
                case 'user_ids':
                    $user_ids = explode(",", epost($_IN_PARAMS['user_ids']));
                    $data = system_messages_createChat($user_id, [
                        'user_ids' => join(',', $user_ids)
                    ]);
                    if ($data[0]['status']) {
                        $params['chat_id'] = (int) $data[0]['response']['original_chat_id'];
                    } else {
                        if ($data[0]['reason'] == 'chat_exist') {
                            $params['chat_id'] = (int) $data[0]['original_chat_id'];
                        } else {
                            array_push($errors, addError(
                                $_ERRORS['unknownError'][0],
                                $_ERRORS['unknownError'][1] . " `" . $key . "`"
                            ));
                            http_response_code(400);
                            ethrow($errors);
                        }
                    }
                    $params['peers'] = toType($user_ids);
                    array_push($params['peers'], $user_id);
                    break;
            }
        }
        $params['main_type_message'] = MESSAGE_TYPE_MESSAGE;
        if ($attachments) {
            if ($imagesFlag) {
                $params['main_type_message'] = IMAGE_TYPE_MESSAGE;
            }
        }
        $ts = time();
        $originalMessageParams = [
            'chat_id' => $params['chat_id'],
            'main_type_message' => $params['main_type_message'],
            'author_id' => $params['user_id'],
            'create_date' => $ts,
            'message_context' => $params['message_context']
        ];
        if ($imagesFlag) {
            $originalMessageParams['images'] = $params['attachment']['images'];
        }
        $query = qInsert('messages', $originalMessageParams);
        squery($query['query']);
        $insertOriginalMessage = (int) insertID();
        $params['original_message_id'] = $insertOriginalMessage;
        messages_setNewLastUpdateTime($params['chat_id']);
        $linkMessagesParams = [
            'author_id' => NULL,
            'original_message_id' => $insertOriginalMessage,
            'create_date' => $ts,
            'original_chat_id' => $params['chat_id']
        ];
        $linksValues = [];
        $allPushTokens = [];
        foreach ($params['peers'] as $k => $v) {
            if (num($v) != $user_id) {
                $userPushTokens = device_getAllByUserId($v);
                if ($userPushTokens['status']) {
                    foreach ($userPushTokens['tokens'] as $value) {
                        array_push($allPushTokens, $value);
                    }
                }
            }
            array_push(
                $linksValues,
                "('".num($v)."','".$linkMessagesParams['original_message_id']."','".$linkMessagesParams['create_date']."','".$linkMessagesParams['original_chat_id']."')"
            );
        }
        $author_info = getUsersById([
            "users" => [$params['user_id']],
            "fields" => ["id","first_name","last_name","full_name","photo_id"]
        ]);
        $userAuthorObject = [];
        if ($author_info['status']) {
            array_push($userAuthorObject, $author_info['data'][0]);
        }
        $fcmData = ["full"=> [
            "notify_type" => NOTIFY_TYPE_MESSAGE,
            "send_reason" => [
                "type" => "user",
                "userObjects" => $userAuthorObject
            ],
            "messages" => [
                array (
                    'id' => $params['original_message_id'],
                    'chat_id' => $params['chat_id'],
                    'main_type_message' => $params['main_type_message'],
                    'author_id' => $params['user_id'],
                    'create_date' => $ts,
                    'message_context' => $params['message_context'],
                )
            ]
        ]];
        $fcmS = fcmSend($allPushTokens, $fcmData, $userAuthorObject['full_name'], $params['message_context']);
        $queryLinks = qInsert('messages_links', $linkMessagesParams, [
            'values' => join(',', $linksValues)
        ]);
        squery($queryLinks['query']);
        messages_lp_setPointLastMessageIDOfChat((int) $params['chat_id'], $insertOriginalMessage);
        users_setOnline($user_id);
        return $params;
    }