<?php
    function system_messages_createChat(int $user_id, array $_IN_PARAMS) {
        global $_ERRORS, $strings;
        $errors = [];
        $name = $_IN_PARAMS['name'] ? epost($_IN_PARAMS['name']) : false;
        $countSymbolsTitle = mb_strlen($name);
        if ($name) {
            if ($countSymbolsTitle > $strings['chats']['config']['maxNumberCharacters']) {
                array_push($errors, addError(
                    $_ERRORS['maxNumberCharacters'][0],
                    $_ERRORS['maxNumberCharacters'][1]." : ".$strings['chats']['config']['maxNumberCharacters']
                ));
                http_response_code(400);
                ethrow($errors);
            }
        }
        $user_ids = explode(",", epost($_IN_PARAMS['user_ids']));
        if (in_array($user_id, $user_ids)) {
            array_push($errors, addError(
                $_ERRORS['unknownError'][0],
                $_ERRORS['unknownError'][1]." reason uid: ".$user_id
            ));
            http_response_code(400);
            ethrow($errors);
        }
        $usersExistByIds = usersExistByIds($user_ids);
        if (!$usersExistByIds['status']) {
            array_push($errors, addError(
                $_ERRORS['notAllUsersAreValid'][0],
                $_ERRORS['notAllUsersAreValid'][1]
            ));
            http_response_code(400);
            ethrow($errors);
        }
        if ($usersExistByIds['count'] > $strings['chats']['config']['maxMembers']) {
            array_push($errors, addError(
                $_ERRORS['maxMembersChatLimit'][0],
                $_ERRORS['maxMembersChatLimit'][1]
            ));
            http_response_code(400);
            ethrow($errors);
        }
        $isMultyChat = $usersExistByIds['count'] > 1 ? 1 : 0;
        $params = [];
        if ($isMultyChat) {
            $params['name'] = $name;
        } else {
            $checkExistNotMultyChat = messages_checkExistNotMultyChat($user_id, (int) $usersExistByIds['result'][0]);
            if ($checkExistNotMultyChat['status']) {
                $data[0]['status'] = false;
                $data[0]['reason'] = 'chat_exist';
                $data[0]['original_chat_id'] = $checkExistNotMultyChat['original_chat_id'];
                return $data;
            }
        }
        $createChat = messages_createChat($user_id, $isMultyChat, $user_ids, $params);
        $data[0]['status'] = true;
        $data[0]['response'] = $createChat;
        users_setOnline($user_id);
        return $data;
    }

    function system_messages_leaveChat(int $user_id, array $_IN_PARAMS) {
        global $_ERRORS, $strings;
        $errors = [];
    }

    //without sharding version
    function messages_getNumberChatSection(int $user_id) {
        /**
         * Версия без шардирования. Всегда возвращать 0
         */
        return 0;
        //return (int) substr($user_id, -1);
    }

    function messages_getNameChatTable(int $user_id) {
        $prefix = 'chats_';
        $numberChatSection = messages_getNumberChatSection($user_id);
        return $prefix.$numberChatSection;
    }

    function system_messages_getChatCopiedIdByOriginalChatId(int $user_id, int $originalChatId) {
        $sf = sfetch(
            select(
                ["id"],["chats_peers"],
                "`original_chat_id` = '".num($originalChatId)."' && `author_id` = '".$user_id."'",
                false,
                0,
                1
            )
        );
        if ($sf['id']) {
            return [
              'status' => true,
              'id' => (int) $sf['id']
            ];
        } else {
            return [
              'status' => false
            ];
        }
    }

    function users_incrementChatCounter(int $user_id) {
        squery("UPDATE `users` SET `my_chat_counter` = my_chat_counter + 1 WHERE `id` = ".intval($user_id).";");
        $chat_counter = sfetch(select(["my_chat_counter"], ["users"], " `id` = ".intval($user_id).""));
        return (int) $chat_counter['my_chat_counter'];
    }

    function messages_createChat(int $real_author_id, $isMultyChat = 0, array $user_ids, array $params) {
        array_push($user_ids, $real_author_id);
        $users_incrementChatCounter = users_incrementChatCounter($real_author_id);
        $originalChatParams = [];
        $defaultParams = [
            'id' => null,
            'multy' => '',
            'author_id' => '',
            'real_author_id' => (int) $real_author_id,
            'name' => '',
            'peers' => '',
            'create_date' => '',
            'count_message' => 0,
            'photo' => '',
            'last_update_time' => '',
            'chat_settings' => '',
            'unread' => 0,
            'cm_id' => $users_incrementChatCounter
        ];
        $originalChatParams = $defaultParams;
        unset($originalChatParams['real_author_id']);
        $originalChatParams['multy'] = $isMultyChat;
        $originalChatParams['author_id'] = (int) $real_author_id;
        $originalChatParams['name'] = $params['name'] ? epost($params['name']) : '';
        $originalChatParams['peers'] = join(',', $user_ids);
        $originalChatParams['last_update_time'] = $originalChatParams['create_date'] = time();
        $originalChatValues['count_message'] = 0;
        $originalChatParams['chat_settings'] = json_encode([
            "muted" => 0,
            "status_of_typing" => 0
        ]);
        $originalChatParams['unread'] = 0;
        $q = qInsert('chats', $originalChatParams, NULL);
        squery($q['query']);
        $insertOriginalChat = (int) insertID();
        $chat_peersOriginal = [
            'id' => null,
            'multy' => $isMultyChat,
            'author_id' => '',
            'real_author_id' => (int) $real_author_id,
            'invited_id' => (int) $real_author_id,
            'create_time' => time(),
            'unread' => null,
            'original_chat_id' => $insertOriginalChat
        ];
        $otherValues = [];
        foreach ($user_ids as $k => $v) {
            $chat_peersCopy = $chat_peersOriginal;
            $chat_peersCopy['author_id'] = (int) $v;
            $otherValues[] = "(NULL, ".$chat_peersCopy['multy'].", '".$chat_peersCopy['author_id']."', '".$chat_peersCopy['real_author_id']."', '".$chat_peersCopy['real_author_id']."', '".time()."', '0', '".$insertOriginalChat."')";
        }
        $otherValuesStr = join(',', $otherValues);
        $q2 = qInsert('chats_peers', $chat_peersOriginal, [
            "values" => $otherValuesStr
        ]);
        squery($q2['query']);
        $response = ['original_chat_id' => $insertOriginalChat];
        $chatIdForFront = sfetch(select(['id'],['chats_peers'],"chats_peers.original_chat_id = '".$insertOriginalChat."' && chats_peers.author_id = '".$real_author_id."'", false, 0, 1));
        if ($chatIdForFront) {
            $response['copied_chat_id'] = (int) $chatIdForFront['id'];
        }
        return $response;
    }

    function messsages_getChatIds(int $user_id, array $params) {
        $pre_query = "select original_chat_id from chats_peers  where chats_peers.author_id = '".$user_id."'";
        $sf = sfetch($pre_query, 1);
        if (!$sf) {
            return [
                'count' => 0
            ];
        }
        $valuesKeys = join(',', getValuesByKey($sf, 'original_chat_id'));
        $countQuery = select(['count(id)'],['chats'],"id in(".$valuesKeys.")", false, 0, 1);
        $count = (int) sfetch($countQuery)['count(id)'];
        if ($count < 1) {
            return [
                'count' => 0
            ];
        }
        $needFields = [
            "id", "multy", "name", "peers", "photo",
            "last_update_time", "chat_settings", "unread"
        ];
        $needFieldsStr = join(",", $needFields);
        if ($params['count'] != 'full') {
            $limitStr = " limit ".$params['offset'].",".$params['count'];
        } else {
            $limitStr = "";
        }
        $fullQuery = "select ".$needFieldsStr." from chats where id in(".$valuesKeys.") order by last_update_time desc".$limitStr;
        $fullResult = sfetch($fullQuery, true);
        if (!$fullResult) {
            return [
                'count' => 0
            ];
        }
        if ($params['getUsersInfo']) {
            $usersCollection = (array) messages_buildUserCollection($fullResult,'peers');
            sort($usersCollection);
            if (count($usersCollection) > 0) {
                $usersInfo = users_getUsersInfoByIds($usersCollection, ['id', 'full_name', 'last_seen', 'photo_id']);
                if ($usersInfo['status']) {
                    $usersInfo = $usersInfo['data'];
                }
            }
            foreach ($fullResult as $k => $v) {
                foreach (explode(',', $v['peers']) as $kk => $vv) {
                    $fullResult[$k]['peersInfo'][(int)$vv] = userInfoTreatmentForOneUser($usersInfo[(int)$vv]) ?? NULL;
                }
            }
        }
        foreach ($fullResult as $k => $v) {
            $fullResult[$k]['peers'] = explode(',', $v['peers']);
            $chat_lastMessage = messages_getLastMessageAtChatId((int)$v['id']);
            if ($chat_lastMessage['status']) {
                $fullResult[$k]['last_messages'] = [$chat_lastMessage['items'][0]];
            } else {
                $fullResult[$k]['last_messages'] = [];
            }
        }
        $fullResult = toType($fullResult);
        users_setOnline($user_id);
        return [
            'count' => $count,
            'items' => $fullResult
        ];
    }

    function messsages_getChatInfoById(int $originalChatId) {
        $valuesKeys = join(',', getValuesByKey($sf, 'original_chat_id'));
        $needFields = [
            "id", "multy", "name", "peers",
            "last_update_time", "chat_settings", "unread"
        ];
        $needFieldsStr = join(",", $needFields);
        $fullQuery = select(
            $needFields, ["chats"],"id = '".$originalChatId."'", ["last_update_time desc"], 0, 1
        );
        $fullResult = sfetch($fullQuery, true);
        if (!$fullResult) {
            return [
                'count' => 0
            ];
        }
        $usersCollection = (array) messages_buildUserCollection($fullResult,'peers');
        sort($usersCollection);
        if (count($usersCollection) > 0) {
            $usersInfo = users_getUsersInfoByIds($usersCollection, ['id', 'full_name', 'last_seen', 'photo_id']);
            if ($usersInfo['status']) {
                $usersInfo = $usersInfo['data'];
            }
        }
        foreach ($fullResult as $k => $v) {
            foreach (explode(',', $v['peers']) as $kk => $vv) {
                $fullResult[$k]['peersInfo'][(int)$vv] = $usersInfo[(int)$vv] ?? NULL;
                $userPhoto = $fullResult[$k]['peersInfo'][(int)$vv]['photo_id'];
                if ($userPhoto != "" && !is_null($userPhoto)) {
                    $userPhoto = $fullResult[$k]['peersInfo'][(int)$vv]['photo_id'] = fileGet($userPhoto);
                } else {
                    $userPhoto = $fullResult[$k]['peersInfo'][(int)$vv]['photo_id'] = imageDefault();
                }
            }
        }
        $fullResult[0]['peers'] = explode(',', $fullResult[0]['peers']);
        $fullResult = toType($fullResult);
        return [
            'count' => 1,
            'items' => $fullResult
        ];
    }

    function messages_checkExistNotMultyChat(int $user_id1, int $user_id2) {
        $check = sfetch(select(['id'],['chats'],"multy = 0 && (author_id = '".$user_id1."' || author_id = '".$user_id2."') && (peers = '".$user_id1.",".$user_id2."' || peers = '".$user_id2.",".$user_id1."')",false,0,1));
        if (!$check) {
            return [
                'status' => false
            ];
        } else {
            return [
                'status' => true,
                'original_chat_id' => (int) $check['id']
            ];
        }
    }

    function system_messages_getUserIdsOfChat(int $originalChatId) {
        $originalChatId = num($originalChatId);
        $pre_query = "select peers from chats where chats.id = '".$originalChatId."'";
        $sf = sfetch($pre_query, 1);
        if (!$sf ) {
            return [];
        } else {
            if (!$sf[0]['peers']) {
                return [];
            }
            return count(explode(',', $sf[0]['peers'])) < 2
                ? (array) (int) ($sf[0]['peers'])
                : array_map('intval', explode(',', $sf[0]['peers']));
        }
    }

    function system_messages_excludeUserFromOriginalChat(int $originalChatId, int $removeUserId) {
        $originalChatId = num($originalChatId);
        $getUserIdsOfChat = system_messages_getUserIdsOfChat($originalChatId);
        $indexEl = array_search($removeUserId, $getUserIdsOfChat);
        if (!$indexEl) {
            return [
                'status' => false
            ];
        }
        unset($getUserIdsOfChat[$indexEl]);
        sort($getUserIdsOfChat);
        $newUserIdsOfChat = join(',', $getUserIdsOfChat);
        $qU = qUpdate("chats", [
            "peers" => $newUserIdsOfChat
        ], "chats.id = '".$originalChatId."'");
        squery($qU);
        return [
            'status' => true,
            'newList' => $getUserIdsOfChat
        ];
    }

    function system_messages_addUserInOriginalChat(int $originalChatId, int $addUserId) {
        $originalChatId = num($originalChatId);
        $getUserIdsOfChat = system_messages_getUserIdsOfChat($originalChatId);
        $indexEl = array_search($addUserId, $getUserIdsOfChat);
        if ($indexEl !== false) {
            return [
                'status' => false
            ];
        }
        array_push($getUserIdsOfChat, (int) $addUserId);
        sort($getUserIdsOfChat);
        $newUserIdsOfChat = join(',', $getUserIdsOfChat);
        $qU = qUpdate("chats", [
            "peers" => $newUserIdsOfChat
        ], "chats.id = '".$originalChatId."'");
        squery($qU);
        return [
            'status' => true,
            'newList' => $getUserIdsOfChat
        ];
    }

    function system_messages_checkUserInConversationById(int $originalChatId, int $checkUserId) {
        $originalChatId = num($originalChatId);
        $getUserIdsOfChat = system_messages_getUserIdsOfChat($originalChatId);
        $indexEl = array_search($checkUserId, $getUserIdsOfChat);
        if ($indexEl !== false) {
            return [
                'status' => true
            ];
        } else {
            return [
                'status' => false
            ];
        }
    }

    function system_messages_getSomeFieldsCopiedChatById(int $copiedChatId, array $fields) {
        $originalChatIdQuery = select($fields, ["chats_peers"], "chats_peers.id = '".num($copiedChatId)."'", false, 0, 1);
        $originalChatId_sf = sfetch($originalChatIdQuery);
        if (!$originalChatId_sf) {
            return [
                'status' => false
            ];
        }
        return [
            'status' => true,
            'response' => $originalChatId_sf
        ];
    }

    function system_messages_removeCopiedChatById(int $copiedChatId) {
        squery("delete from chats_peers where chats_peers.id = '".num($copiedChatId)."'");
    }

    function messages_incrementMessageCounter(int $originalChatId) {
        squery("UPDATE `chats` SET `count_message` = count_message + 1 WHERE `id` = ".intval($originalChatId).";");
        $counter = sfetch(select(["count_message"], ["chats"], " `id` = ".intval($originalChatId).""));
        return (int) $counter['count_message'];
    }

    function messages_decrementMessageCounter(int $originalChatId) {
        squery("UPDATE `chats` SET `count_message` = count_message - 1 WHERE `id` = ".intval($originalChatId).";");
        $counter = sfetch(select(["count_message"], ["chats"], " `id` = ".intval($originalChatId).""));
        return (int) $counter['count_message'];
    }

    function messages_buildUserCollection(array $array, $key) {
        $collection = [];
        foreach ($array as $k => $v) {
            $usersChat = explode(',', $v[$key]);
            foreach ($usersChat as $kk => $vv) {
                $vv = (int) $vv;
                if (!in_array($vv, $collection)) {
                    array_push($collection, $vv);
                }
            }
        }
        return $collection;
    }
