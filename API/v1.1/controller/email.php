<?php
	function sendCodeActivationOnEmail($objectKey, $time, $type = 1) {
		$objectKey = escape($objectKey);
		$expTime = time() + intval($time);
		$code = md5(randomStr(32));
		squery("insert into `activation_keys` (`id`, `code`, `type`, `expiration_time`, `status`, `object_key`) values (NULL, '".$code."', '".$type."', '".$expTime."', 0, '".$objectKey."')");
		return array(
			"status" => true,
			"code" => $code
		);
	}

	function getUserEmailValueById($user_id) {
		$sf = sfetch("select `email` from `users` where `id` = '".num($user_id)."';");
		return $sf['email'];
	}

	function removeUserEmail($user_id) {
		squery("UPDATE `users` SET `email` = '' WHERE `id` = ".num($user_id).";");
	}

	function setUserEmailById($user_id, $email) {
	    $query = "UPDATE `users` SET `email` = '".escape($email)."' WHERE `id` = ".num($user_id).";";
		squery($query);
	}

	function emailExists($email) {
		$sq = sfetch("select id from `users` where `email` = '".escape($email)."';");
		if ($sq) {
			return num($sq['id']);
		} else {
			return false;
		}
	}