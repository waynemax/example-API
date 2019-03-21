<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 12/09/2017
	 **/

	require_once ("auth/systemAuth.php");

    function users_setOnline(int $user_id) {
        $config = config();
        squery("UPDATE `users` SET `last_seen` = '".time()."' WHERE `id` = '".$user_id."';");
        return [
            'last_seen' => time(),
            'onlineTime' => (int) $config['onlineTime']
        ];
    }

    function auth_getInfoClientById(int $client_id) {
        $selectQuery = select(
            ['client_id','redirect_uri','display','scope','client_secret'],
            ['clients'],
            "`client_id` = '".num($client_id)."'",
            false,
            0,
            1
        );
        $sf = sfetch($selectQuery);
        if (!$sf) err(
            'not_found',
            'client_id',
            400
        );
        return $sf;
    }

	function exeCAuth($params) {
		global $strings;
		if (!$params['POST']) return false;
		$_POST = $params['POST'];
		$stringsCopy = $strings;
		switch ($_POST['method']) {
			case 'code':
				if (!$_POST['login'] || !$_POST['password']) exit;
				$query = $stringsCopy['API']['methods']['auth.get']
					."?"
					.http_build_query($stringsCopy['API']['data'])
					."&redirect_uri=".$stringsCopy['API']['redirect_uri']
					."&login=".$_POST['login']."&password=".$_POST['password'];
				return file_get_contents($query);
			    break;
			case 'token':
				if (!$_POST['code'] || !$_POST['headers']) exit;
				unset($stringsCopy['API']['data']['scope']);
				unset($stringsCopy['API']['data']['display']);
				$query = $stringsCopy['API']['methods']['auth.token']
				    ."?"
				    .http_build_query($stringsCopy['API']['data'])
				    ."&redirect_uri=".$stringsCopy['API']['redirect_uri']
				    ."&code=".$_POST['code'];
				$ua = $_POST['headers'];
				$ret = curl(array(
					"type" => "GET",
					"url" => $query,
					"headers" => array(
						"User-Agent: ".escape($ua['User-Agent'])
					),
					"typeOut" => array("response")
				));
				return $ret;
			    break;
		}
	}

	function checkAuth($bearer = NULL) {
		if (!is_null($bearer) ) {
			$token = explode(" ", $bearer);
			if (count($token) > 1) {
				$token = trim($token[1]);
			} else {
				return false;
			}
		} else {
		    if ($_GET['access_token']) {
                $token = escape($_GET['access_token']);
            } else {
                return false;
            }
		}
		if (!tokenValid($token)) {
			return false;
		} else {
			return [
				'status' => true,
				'access_token' => $token,
				'user_id' => getUserIdByToken($token)
			];
		}
	}