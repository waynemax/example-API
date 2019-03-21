<?php
	function device_getAllByUserId(int $user_id) {
        $userHash = (array) users_haveHash($user_id);
        if ($userHash['status']) {
	        $sf = sfetch(select(["*"],["devices"],"`unique_key` = '".$userHash['hash']."'",false,'no','no'),true);
	        $count = count($sf);
	        $tokens = [];
            foreach ($sf as $item) {
                array_push($tokens, $item['fcm_token']);
	        }
	        return [
	          'count' => $count,
              'status' => true,
              'items' => $sf,
              'tokens' => $tokens
            ];
        } else {
            return [
                'status' => false
            ];
        }
    }

	function deviceAdd($params) {
        if ($params['uniqueKey'] && $params['typeDevice'] && $params['ua']) {
            $uniqueKeyExist = uniqueKeyExist($params['uniqueKey'], $params['typeDevice']);
            if (!$uniqueKeyExist['status']) {
                $hashDevice = hash('sha256', time() . $params['ua'] . $params['uniqueKey']);
                squery("insert into `devices` (`id`, `date_create`, `ua`, `user_id`, `type`, `unique_key`, `hash_device`) values (NULL, " . time() . ", '" . escape($params['ua']) . "', 0, " . intval($params['typeDevice']) . ", '" . escape($params['uniqueKey']) . "', '" . $hashDevice . "')");
                return array(
                    "status" => true,
                    "hashDevice" => $hashDevice
                );
            } else {
                $alreadyExistDevice = sfetch("select * from `devices` where `id` = '".((int) $uniqueKeyExist['id'])."'");
                return [
                    "status" => false,
                    "reason" => "alreadyExist",
                    "hashDevice" => $alreadyExistDevice['hash_device'],
                    "fcm_token" => $alreadyExistDevice['fcm_token']
                ];
            }
        } else {
            return array(
                "status" => false
            );
        }
	}

	function deviceUpdate($params) {
		if ($params['hash_device'] && $params['user_id']) {
			squery("update `devices` set `user_id` = '".intval($params['user_id'])."' where `hash_device` = '".escape($params['hash_device'])."';");
		} else {
			return array(
				"status" => false
			);
		}
	}

    function device_removeByHash($hash_device) {
        squery("delete from `devices` where `hash_device` = '".escape($hash_device)."';");
    }

	function tokenPushUpdate($hashDevice, $token, int $user_id) {
	    squery("UPDATE `devices` SET `user_id` = '".num($user_id)."', `fcm_token` = '".escape($token)."' WHERE `hash_device` = '".escape($hashDevice)."';");
	}

	function uniqueKeyExist($unique_key, $typeDevice) {
		if ($unique_key) {
			$ch = sfetch("select `id` from `devices` where `type` = '".escape($typeDevice)."' && `unique_key` = '".escape($unique_key)."';");
			if ($ch) {
				return array(
					"status" => true,
                    "id" => (int) $ch['id']
				);
			} else {
				return array(
					"status" => false
				);
			}
		} else {
			return array(
				"status" => false
			);
		}
	}

	function checkDevice($params) {
		if ($params['hash_device']) {
			$ch = sfetch("select `id` from `devices` where `hash_device` = '".escape($params['hash_device'])."';");
			if ($ch) {
				return array(
					"status" => true
				);
			} else {
				return array(
					"status" => false
				);
			}
		} else {
			return array(
				"status" => false
			);
		}
	}