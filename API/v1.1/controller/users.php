<?php
	function getOnlineRange() {
		$config = config();
		$onlineTime = $config['onlineTime'];
		$v = time() - num($onlineTime);
		return "last_seen > ".num($v);
	}

	function userInfoTreatment($dataResult, $noOutFields = array()) {
		$data = array();
		foreach ($dataResult as $key => $value) {
			foreach ($value as $k => $v) {
				switch ($k) {
					case 'photo_id':
						if (!is_null($v)){
							$ve = explode("+", $v);
							$data[$key]['photo'] = buildImageFileObject($ve[0], $ve[1], $ve[2]);
							$data[$key]['photo']['default'] = false;
						} else {
							$data[$key]['photo'] = imageDefault();
							$data[$key]['photo']['default'] = true;
						}
						continue 2;
					    break;
					case 'last_seen':
						if (!is_null($v)) {
							$data[$key]['online'] = checkOnline($v);
						}
					    break;
					case 'bdate':
						if (!is_null($v)) {
							$realvalue = num($dataResult[$key][$k]);
							$data[$key]['bday_format'] = dateTreatment($realvalue, 1);
						}
					    break;
					case 'country':
						if (!is_null($v) && num($v) > -1) {
							$realvalue = num($v);
							$data[$key]['location']['country'] = getCountries(["id" => num($v)]);
						}
					    break;
					case 'region':
						if (!is_null($v) && num($v) > -1) {
							$realvalue = num($v);
							$data[$key]['location']['region'] = getRegions(["id" => num($v)]);
						}
					    break;
					case 'city':
						if (!is_null($v) && num($v) > 0) {
							$realvalue = num($v);
							$data[$key]['location']['city'] = getCities(["id" => num($v)]);
						}
					    break;
					default:
						$realvalue = false;
					    break;
				}
				if (!in_array($k, $noOutFields)) {
					$data[$key][$k] = $realvalue ? $realvalue : $dataResult[$key][$k];
				}
			}
		}
		return $data;
	}

    function userInfoTreatmentForOneUser($value) {
        $data = array();
        foreach ($value as $k => $v) {
            switch ($k) {
                case 'photo_id':
                    if (!is_null($v)){
                        $fg = filesGet([$v]);
                        if ($fg['status']) {
                            $v = $fg['images'][0];
                            $ve = explode("+", $v);
                            $data['photo'] = buildImageFileObject($ve[0], $ve[1], $ve[2]);
                            $data['photo']['default'] = false;
                        } else {
                            $data['photo'] = imageDefault();
                            $data['photo']['default'] = true;
                        }
                    } else {
                        $data['photo'] = imageDefault();
                        $data['photo']['default'] = true;
                    }
                    continue 2;
                    break;
                case 'last_seen':
                    if (!is_null($v)) {
                        $data['online'] = checkOnline($v);
                    }
                    $data['last_seen'] = $v;
                    break;
                default:
                    $data[$k] = $v;
                    break;
            }
        }
        return $data;
    }

	function userDeactivate($user_id) {
		$user_id = num($user_id);
		squery("UPDATE `users` SET `deactivated` = '1' WHERE `id` = ".$user_id.";");
	}

	function userExistById($user_id) {
		$user_id = num($user_id);
		$cQuery = "select id from users where `id` = '".$user_id."' && `deactivated` = 0;";
		$ch = sfetch($cQuery);
        return ($ch) ? true : false;
	}

	function usersExistByIds(array $user_ids) {
        $check_user_ids = [];
        $inCount = count($user_ids);
	    foreach ($user_ids as $key => $value) {
            array_push($check_user_ids, num($value));
        }
        if (count($check_user_ids) < 1)  {
	        return [
	            'status' => false,
                'count' => 0,
                'result' => []
            ];
        }
        $cQuery = "select id from users where `id` in(".join(',', $check_user_ids).") && `deactivated` != 1;";
	    $ch = sfetch($cQuery, true);
        $countResult = count($ch);
        $status = ($countResult != count($check_user_ids)) ? false : true;
        return [
            'status' => $status,
            'count' => $countResult,
            'inCount' => $inCount,
            'result' => toConvert(getValuesByKey($ch, 'id'), 'integer')
        ];
    }

	function getUsersById($params) {
		$noOutFields = !is_null($withoutFields['withoutFields']) ? $withoutFields['withoutFields'] : [];
		$fields = [];
		foreach ($params['fields'] as $k => $v) {
			switch ($v) {
				case 'photo_id':
					array_push($fields, getStringFileGetInQuery('photo_id', 1, 2));
					continue 2;
				break;
			}
			array_push($fields, "tb1.".$v);
		}
		switch ($params['typeSearch']) {
            case 'phones':
                $search = "phone in(".join(',', $params['phones']).")";
                break;
            default:
                $search = "id in(".join(',', $params['users']).")";
                break;
        }
		$que = "select ".join(", ", $fields)." from users tb1 where ".$search;
		$dataResult = sfetch($que, true);
		$data = array();
		$data = userInfoTreatment($dataResult, $noOutFields);
		return ['data' => $data, 'status' => true];
	}

	function users_getUsersInfoByIds(array $users_id, array $fields) {
	    $users_id = escape(join(',', $users_id));
	    $query = select($fields, ['users'], "id in(".$users_id.")", false, 0, 1000);
        $dataResult = sfetch($query, true);
        if (!$dataResult) {
            return [
               'status' => false
            ];
        }
        $treatmentDataResult = [];
        foreach ($dataResult as $k => $v) {
            $id = (int) $v['id'];
            foreach ($v as $kk => $vv) {
                switch ($kk) {
                    case 'last_seen':
                        if (!is_null($vv)) {
                            $v['online'] = checkOnline($v);
                        }
                        break;
                }
            }
            $treatmentDataResult[$id] = $v;
        }
        return [
            'status' => true,
            'data' => (array) $treatmentDataResult
        ];
    }

    function users_haveHash(int $user_id) {
	    $sf = sfetch("select hash from users where `id` = '".$user_id."'",false);
	    if ($sf) {
	        if ($sf['hash'] != "") {
	            return [
                    "status" => true,
                    "hash" => $sf['hash']
                ];
            } else {
	            return [
                    "status" => false
                ];
            }
        } else {
	        return [
	            "status" => false
            ];
        }
    }

    function users_setHash(int $user_id) {
        $isHave = users_haveHash($user_id);
        if ($isHave['status']) {
            return [
                'status' => false,
                'reason' => 'Hash already exist',
                'hash' => $isHave['hash']
            ];
        }
        $newHash = genSalt(64);
	    $sq = squery("UPDATE `users` SET `hash` = '".$newHash."' WHERE `id` = '".$user_id."';");
        return [
            'status' => true,
            'hash' => $newHash
        ];
    }

    function users_updateHash(int $user_id) {
        $newHash = genSalt(64);
	    $sq = squery("UPDATE `users` SET `hash` = '".$newHash."' WHERE `id` = '".$user_id."';");
        return [
            'status' => true,
            'hash' => $newHash
        ];
    }

    function searchUsers($params, $typeParams = NULL) {
        $strings = strings();
        $config = config();
        $auth = false;
        $myId = NULL;
        $filters = [];
        $separator1 = "";
        $offset = 0;
        $count = 20;
        $sort = 'id asc';
        if (is_null($params['query'])) $query = "";
        foreach ($params as $pk => $pv) {
            switch ($pk) {
                case 'auth':
                    if ($pv['auth'] == true) {
                        $auth = true;
                        $myId = num($params['auth']['myId']);
                    }
                    break;
                case 'filters':
                    if (!is_null($pv)) {
                        $filters = $pv;
                        $separator1 = "&& ";
                    }
                    break;
                case 'query':
                    $query = urldecode(escape($pv));
                    break;
                case 'offset':
                    $offset = num($pv);
                    break;
                case 'count':
                    $count = num($pv) < 1000 ? num($pv) : 20;
                    break;
                case 'sort':
                    switch ($pv) {
                        case 'name_asc':
                            $sort = 'tb1.full_name asc';
                            break;
                        case 'name_desc':
                            $sort = 'tb1.full_name desc';
                            break;
                        case 'id_desc':
                            $sort = 'tb1.id desc';
                            break;
                    }
                    break;
            }
        }
        $typeQueryFilters = '';
        $typeTable = '';
        if (!is_null($typeParams)) {
            switch ($typeParams['type']) {
                case 'subscriptions':
                    if ($typeParams['user_id']) {
                        $typeTable = ", subscriptions tb2";
                        $typeQueryFilters = " && tb2.user_id = '".num($typeParams['user_id'])."' && tb1.id = tb2.whom_signed_id";
                    }
                    break;
                case 'subscribers':
                    if ($typeParams['user_id']) {
                        $typeTable = ", subscriptions tb2";
                        $typeQueryFilters = " && tb2.whom_signed_id = '".num($typeParams['user_id'])."' && tb1.id = tb2.user_id";
                    }
                    break;
            }
        }
        $filterQuery = [];
        foreach ($filters as $k => $v) {
            switch ($k) {
                case 'gender':
                case 'has_children':
                case 'social_type':
                case 'education_type':
                case 'earnings_type':
                case 'confession_type':
                case 'has_family':
                case 'has_children':
                    if (rangeInt(num($v), $strings['catalogs']['range'][$k][0], $strings['catalogs']['range'][$k][1])) {
                        array_push($filterQuery, "tb1.".$k." = ".$v);
                    }
                    break;
                case 'online':
                    array_push($filterQuery, "tb1.".getOnlineRange());
                    break;
                case 'city':
                case 'region':
                case 'country':
                    if (is_int(num($v))) {
                        array_push($filterQuery, "tb1.".$k." = '".num($v)."'");
                    }
                    break;
            }
        }
        if (count($filterQuery) > 0) {
            $filterQuery = $separator1.implode(" && ", $filterQuery);
        } else {
            $filterQuery = "";
        }
        foreach ($params['fields'] as $kk => $vv) {
            switch ($vv) {
                case 'photo_id':
                    $params['fields'][$kk] = getStringFileGetInQuery('photo_id', 1, 2);
                    continue 2;
                    break;
            }
            $params['fields'][$kk] = "tb1.".$vv;
        }
        $selectStr = "select ".join(", ", $params['fields'])." ";
        $searchQStr = "((LOWER(tb1.full_name) like LOWER('%".$query."%') || LOWER(Concat(tb1.last_name, ' ', tb1.first_name)) like LOWER('%".$query."%')))";
        $query = "from users tb1".$typeTable." where ".$searchQStr.$typeQueryFilters." ".$filterQuery;
        $limitStr = " order by ".$sort." limit ".$offset.",".$count.";";
        $countQuery = "select COUNT(*) ".$query;
        $countAll = sfetch($countQuery);
        $countAll = intval($countAll['COUNT(*)']);
        $query = $selectStr.$query.$limitStr;
        $dataResult = sfetch($query, true);
        $dataResult = userInfoTreatment($dataResult, ['photo_id']);
        return [
            'status' => true, 'data' => $dataResult, 'count' => $countAll
        ];
    }