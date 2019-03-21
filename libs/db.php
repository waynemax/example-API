<?php
	checkRoute();
	
	$sqlmode = true;
	$connections = array();
    $servers = array(
        'default' => array(
            'DB_HOST'     => 'localhost',
            'DB_USERNAME' => '',
            'DB_PASSWORD' => '',
            'DB_TABLE'    => ''
        )
    );

	function connectAdd($server = false) {
		global $connections, $servers;
		if (!$server) $server = 'default';
		if (!isset($connections[$server])) {
			$config = $servers[$server];
			$connections[$server]  = mysqli_connect(
				$config['DB_HOST'],
				$config['DB_USERNAME'],
				$config['DB_PASSWORD'],
				$config['DB_TABLE']
			);
			if (mysqli_connect_errno()) {
				die('Connecting to MySQL failed: '.mysqli_connect_error());
			} else {
				squery('SET NAMES utf8mb4');
				squery("SET CHARACTER SET 'utf8mb4'");
				squery("SET SESSION collation_connection = 'utf8mb4_unicode_ci'");
			}
		}
		return $connections[$server];
	}

	function squery($query, $server = false) {
		$connection = connectAdd($server);
		$res = mysqli_query($connection, $query);
		if (!$res) {
			die("MySQL query failed: {$query} <br> ".mysqli_error($connection));
		}
		return $res;
	}

	function sfetch($query, $multiple = false, $server = false) {
		$res = squery($query, $server);
		if ($multiple) {
			$rows = array();
			while ($row = mysqli_fetch_assoc($res)) {
				$rows[] = $row;
			}
		} else {
			$rows = mysqli_fetch_assoc($res);
		}
		mysqli_free_result($res);
		return $rows;
	}

	function insertID($server = false) {
		$connection = connectAdd($server);
		return $connection ? mysqli_insert_id($connection) : false;
	}

	function escape($string, $server = false) {
		$connection = connectAdd($server);
		return $connection ? mysqli_real_escape_string($connection, $string) : addslashes($string);
	}

	function closeConnection($server = false) {
		$connection = connectAdd($server);
		return $connection ? mysqli_close($connection) : false;
	}

	function select($fields, $tables, $where = false, $order = false, $offset = 0, $count = 20) {
		$query = [];
		$query['select'] = "select";
		$query['fields'] = join(", ", $fields);
		$query['from'] = "from";
		$query['tables'] = join(", ", $tables);
		if ($where) {
			$query['where'] = "where";
			$query['whereQuery'] = $where;
		}
		if ($order) {
			$query['order'] = "order by";
			$query['sort'] = join(", ", $order);
		}
		$limitFlag = true;
		if ($offset === "no" || $count === "no") {
            $limitFlag = false;
        }
        if ($limitFlag) {
            $query['limit'] = "limit";
            $query['offset'] = intval($offset).",";
            $query['count'] = intval($count);
        }
		return join(" ", $query);
	}

	function qInsert($table, $data, $params = NULL) {
		$query = [];
		$keys = [];
		$values = [];
		foreach ($data as $k => $v) {
			$keys[] = "`".$k."`";
			switch (gettype($v)) {
				case "integer":
					$values[] = $v;
				break;
				case "string":
					$values[] = "'".escape($v)."'";
				break;
				case "NULL":
					$values[] = "NULL";
				break;
				default:
					$values[] = "'".escape($v)."'";
				break;
			}
		}
		$query['insert'] = "insert into `".$table."` (";
		$query['keys'] = join(", ", $keys);
		$query['valuesStr'] = ") VALUES ";
		if ($params['values']) {
			$query['values'] = $params['values'].";";
		} else {
			$query['values'] = "(".join(", ", $values).");";
		}
		return [
			"query" => join(" ", $query),
			"values" => $values
		];
	}

	function otherValuesBuilder($data) {
	    $values = [];
        foreach ($data as $k => $v) {
            switch (gettype($v)) {
                case "integer":
                    $values[] = $v;
                    break;
                case "string":
                    $values[] = "'".escape($v)."'";
                    break;
                case "NULL":
                    $values[] = "NULL";
                    break;
                default:
                    $values[] = "'".escape($v)."'";
                    break;
            }
        }
        return "(".join(", ", $values).")";
    }

	function qUpdate(String $table, array $data, String $where) {
	    $query = [];
	    $set = [];
	    foreach ($data as $k => $v) {
            switch (gettype($v)) {
                case "integer":
                    $value = $v;
                    break;
                case "string":
                    $value = "'".escape($v)."'";
                    break;
                case "NULL":
                    $value = "NULL";
                    break;
                default:
                    $value = "'".escape($v)."'";
                    break;
            }
            $set[] = $k.'='.$value;
        }
        $query['update'] = "UPDATE `".$table."` SET";
	    $query['set'] = join(',', $set);
        if ($where) {
            $query['where'] = "where";
            $query['whereQuery'] = $where;
        }
        return join(" ", $query);
    }