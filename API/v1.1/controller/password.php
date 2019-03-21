<?php
	function passwordUpdate($user_id, $hashPassword, $hashNewPassword, $systemPrivilege = false) {
        global $_ERRORS;
        $salt = getUserSalt($user_id);
        $hashPassword = escape($hashPassword);
        $realPass = hashUserPassword($hashPassword, $salt);
        $passdb = sfetch("select password from users where id = " . intval($user_id) . ";");
        if (!$systemPrivilege && ($realPass != $passdb['password'])) {
            http_response_code(403);
            ethrow([addError(
                $_ERRORS['permission_denied'][0],
                $_ERRORS['permission_denied'][1] . " `password`"
            )]);
        }
        $realNewPassword = hashUserPassword($hashNewPassword, $salt);
        squery("UPDATE `users` SET `password` = '" . $realNewPassword . "' WHERE `id` = " . $user_id . ";");
        return true;
    }