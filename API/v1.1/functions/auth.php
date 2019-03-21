<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 08/09/2017
	 **/

	function getCodeByClient($client_id, $code, $userid) {
		$select = "select * from `auth_codes` where ";
		if ($client_id) {
			return sfetch($select."`client_id` = ".intval($client_id));
		} else if ($code) {
			return sfetch($select."`code` = '".$code."';");
		} else if ($userid) {
			return sfetch($select."`user_id` = ".$userid);
		}
	}

	function isOwner($token, $userId) {
		$userIdByToken = getUserIdByToken(escape($token));
		if ($userIdByToken && $userIdByToken == num($userId)) {
			return tokenValid($token);
		} else {
			return false;
		}
	}

	function tokenValid($access_token) {
		$access_token = escape($access_token);
		$select = "select * from `tokens` where `access_token` = '{$access_token}'";
		$tokenSame = sfetch($select);
		if (!empty($tokenSame) && ((intval($tokenSame['reg_time'])+intval($tokenSame['expires_in'])) > time())) {
			return true;
		} else {
			return false;
		}
	}

	function getUserIdByToken($access_token) {
		$select = "select * from `tokens` where `access_token` = '{$access_token}'";
		$sf = sfetch($select);
		if ($sf) {
			return intval($sf['user_id']);
		} else {
			return false;
		}
	}

	function removeCodeById($id) {
		squery("delete from `auth_codes` where `id` = ".intval($id));
	}

	function getUserSalt($id) {
		$userSalt = sfetch("select salt from users where id = ".intval($id).";");
		if ($userSalt) {
			return $userSalt['salt'];
		} else {
			return false;
		}
	}

	function hashUserPassword($password, $salt) {
		return hash('sha256', $password.$salt);
	}

	function getUserIdByLogin($login) {
		$userId = sfetch("select id from users where `login` = '".escape($login)."' OR `phone` = '+".escape($login)."' OR `email` = '".escape($login)."';");
		if ($userId) {
			return $userId['id'];
		} else {
			return false;
		}
	}

    function getUserIdByPhone($login) {
        $userId = sfetch("select id from users where `phone` = '+".escape($login)."';");
        if ($userId) {
            return $userId['id'];
        } else {
            return false;
        }
    }

	function genSalt($n){
		$key = '';
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
		$counter = strlen($pattern)-1;
		for ($i=0; $i<$n; $i++) {
			$key.= $pattern{rand(0, $counter)};
		}
		return $key;
	}

	function authCheck($params) {
		if (!($params['login'] || $params['phone']) || !$params['password']) {
			return false;
		} else {
            $typeAuth = 'login';
            if ($params['phone'] != '') {
                $typeAuth = 'phone';
            }
            if ($typeAuth == 'login') {
                $id = getUserIdByLogin($params['login']);
            } else {
                $id = getUserIdByPhone($params['phone']);
            }
			if ($id) {
				$salt = getUserSalt($id);
				$realPass = hashUserPassword($params['password'], $salt);
				$passdb = sfetch("select password from users where id = ".$id.";");
				return $realPass == $passdb['password'] ? $id : false;
			}
		}
	}