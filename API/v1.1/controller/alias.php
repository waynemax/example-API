<?php
	function getObjectByAlias($alias) {
		$alias = urldecode(escape($alias));
		$allAliases = sfetch("SELECT * from `domains` WHERE `alias` = '".$alias."';");
		if ($allAliases) {
			return [
				'status' => true,
				'type' => $allAliases['type'],
				'alias' => $alias,
				'link' => $allAliases['link'],
				'id' => intval($allAliases['object_id'])
			];
		} else {
			$blogsAliases = sfetch("SELECT id, domain from blogs WHERE domain = '".$alias."';");
			if ($blogsAliases) {
				return [
					'status' => true,
					'type' => 2,
					'alias' => $alias,
					'link' => 'blog'.intval($blogsAliases['id']),
					'id' => intval($blogsAliases['id'])
				];
			} else {
				$usersAliases = sfetch("SELECT id, domain from users WHERE domain = '".$alias."';");
				if ($usersAliases) {
					return [
						'status' => true,
						'type' => 1,
						'alias' => $alias,
						'link' => 'id'.intval($usersAliases['id']),
						'id' => intval($usersAliases['id'])
					];
				} else {
					return [
						'status' => false
					];
				}
			}
		}
	}

	function aliasExist($alias) {
		$check = getObjectByAlias($alias);
		return $check['status'];
	}

	function aliasAdd($alias, $type, $object_id, $link = false) {
        $link = (!$link) ? "" : escape($link);
		squery("INSERT INTO `domains` (`id`, `type`, `link`, `object_id`, `alias`)
				VALUES (NULL, ".intval($type).", '".$link."', ".num($object_id).", '".escape($alias)."');");
	}

	function userAliasUpdate($user_id, $alias) {
		squery("UPDATE `users` SET `domain` = '".escape($alias)."' WHERE `id` = ".num($user_id).";");
	}

	function blogAliasUpdate($blog_id, $alias) {
		squery("UPDATE `blogs` SET `domain` = '".escape($alias)."' WHERE `id` = ".num($blog_id).";");
	}