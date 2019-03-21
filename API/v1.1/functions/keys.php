<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 24/10/2017
	 **/

    define("EMAIL_CODE_ACTIVATION_TYPE", 1);
    define("PHONE_CODE_ACTIVATION_TYPE", 2);

	function addCodeActivation($objectKey, $time, $type = EMAIL_CODE_ACTIVATION_TYPE, array $other, int $user_id = NULL) {
		$objectKey = escape($objectKey);
		$expTime = time() + intval($time);
		switch ($type) {
            case PHONE_CODE_ACTIVATION_TYPE:
                $code = mt_rand(1000, 9999);
                break;
            case EMAIL_CODE_ACTIVATION_TYPE:
                $code = md5(randomStr(32));
                break;
        }
        $other = json_encode($other);
		squery("insert into `activation_keys` (`id`, `code`, `type`, `expiration_time`, `status`, `object_key`, `other`, `user_id`) values (NULL, '".$code."', '".$type."', '".$expTime."', 0, '".$objectKey."', '".$other."', '".$user_id."')");
		return array(
			"status" => true,
			"code" => $code
		);
	}

	function checkCodeActivation(int $code, int $user_id = NULL) {
        $userIdQuerySearch = "";
	    if ($user_id) {
            $userIdQuerySearch = " && `user_id` = '".num($user_id)."' ";
        }
		$sq = sfetch("select * from `activation_keys` where `status` != '1' ".$userIdQuerySearch." && `code` = '".escape($code)."';");
		if ($sq) {
			$expiration_time = intval($sq['expiration_time']);
			if (time() < $expiration_time) {
				return [
					'status' => true,
					'object_key' => $sq['object_key'],
                    'other' => $sq['other'] ? json_decode($sq['other']) : ''
				];
			} else {
                return [
                    'status' => false
                ];
            }
		} else {
			return [
				'status' => false
			];
		}
	}

	function removeCodeActivation($code) {
		squery("delete from `activation_keys` where `code` = '".$code."';");
	}

	function removeCodesActivation($objectKey) {
		squery("delete from `activation_keys` where `object_key` = '".escape($objectKey)."';");
	}

	function useCodeActivation(int $code, int $user_id = NULL) {
        $userIdQuerySearch = "";
        if ($user_id) {
            $userIdQuerySearch = " && `user_id` = '".num($user_id)."' ";
        }
		squery("update `activation_keys` set `status` = '1' where `code` = '".$code."' ".$userIdQuerySearch.";");
	}