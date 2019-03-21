<?php
	function getTags() {
		$query = "select * from `tags`;";
		return [
			"items" => sfetch($query, true)
		];
	}

	function getSubTags() {
		$query = "select * from `sub_tags`;";
		return [
			"items" => sfetch($query, true)
		];
	}

	function getEducation() {
		$query = "select * from `education`;";
		return [
			"items" => sfetch($query, true)
		];
	}

	function getEarnings() {
		$query = "select * from `earnings`;";
		return [
			"items" => sfetch($query, true)
		];
	}

	function getSocial() {
		$query = "select * from `social_types`;";
		return [
			"items" => sfetch($query, true)
		];
	}

	function getConfessions() {
		$query = "select * from `confessions`;";
		return [
			"items" => sfetch($query, true)
		];
	}

	function getCities($params) {
		$offset = 0;
		$count = 20;
		$sort = 'id asc';
		$buildBool = false;
		foreach ($params as $key => $value) {
			switch ($key) {
				case 'region_id':
					$buildParams.= " `region_id` = ".num($value);
					$buildBool = true;
				break;
				case 'count':
					$count = num($params[$key]);
				break;
				case 'id':
					$buildBool = true;
					$buildParams = " `id` = ".num($value);
				break;
				case 'offset':
					$offset = num($params[$key]);
				break;
				case 'sort':
					switch ($params[$key]) {
						case 'name_asc':
							$sort = '`name` asc';
						break;
						case 'name_desc':
							$sort = '`name` desc';
						break;
						case 'id_desc':
							$sort = '`id` desc';
						break;
					}
				break;
			}
		}
        $buildParams = $buildBool ? "where".$buildParams : '';
		$countQuery = "select COUNT(*) from `cities` ".$buildParams;
		$countAll = sfetch($countQuery);
		$countAll = $countAll['COUNT(*)'];
		$query = "select id, name, region_id from `cities` ".$buildParams." order by ".$sort." limit ".$offset.",".$count.";";
		return [
			"count" => $countAll,
			"items" => sfetch($query, true)
		];
	}

	function getRegions($params) {
		$offset = 0;
		$count = 20;
		$sort = 'id asc';
		$buildBool = false;
		foreach ($params as $key => $value) {
			switch ($key) {
				case 'country_id':
					$buildParams.= " `country_id` = ".num($value);
					$buildBool = true;
				break;
				case 'id':
					$buildBool = true;
					$buildParams = " `id` = ".num($value);
				break;
				case 'count':
					$count = num($params[$key]);
				break;
				case 'offset':
					$offset = num($params[$key]);
				break;
				case 'sort':
					switch ($params[$key]) {
						case 'name_asc':
							$sort = '`name` asc';
						break;
						case 'name_desc':
							$sort = '`name` desc';
						break;
						case 'id_desc':
							$sort = '`id` desc';
						break;
					}
				break;
			}
		}
        $buildParams = $buildBool ? "where".$buildParams : '';
		$countQuery = "select COUNT(*) from `regions` ".$buildParams;
		$countAll = sfetch($countQuery);
		$countAll = $countAll['COUNT(*)'];
		$query = "select id, name, country_id from `regions` ".$buildParams." order by ".$sort." limit ".$offset.",".$count.";";
		return [
			"count" => $countAll,
			"items" => sfetch($query, true)
		];
	}

	function getCountries($params) {
		$offset = 0;
		$sort = 'id asc';
		$buildBool = false;
		$search = '';
		foreach ($params as $key => $value) {
			switch ($key) {
				case 'continent_id':
					$buildBool = true;
					$buildParams = " `continent_id` = ".num($value);
				    break;
				case 'important':
					if ($params[$key]) {
						$buildBool = true;
						$buildParams = " `important` = 1";	
					}
				    break;
				case 'id':
					$buildBool = true;
					$buildParams = " `id` = ".num($value);
				    break;
				case 'count':
					$count = num($params[$key]);
				    break;
				case 'offset':
					$offset = num($params[$key]);
				    break;
                case 'search':
                    $buildBool = true;
                    $buildParams = " `name` like '".$params['search']."%' or `phone_code` like '".$params['search']."%'";
                    break;
				case 'sort':
					switch ($params[$key]) {
						case 'name_asc':
							$sort = '`name` asc';
						    break;
						case 'name_desc':
							$sort = '`name` desc';
						    break;
						case 'id_desc':
							$sort = '`id` desc';
						    break;
                        case 'code_desc':
                            $sort = '`phone_code` desc';
                            break;
                        case 'code_asc':
                            $sort = '`phone_code` asc';
                            break;
					}
				    break;
			}
		}

        $buildParams = $buildBool ? "where ".$buildParams : '';
		$countQuery = "select COUNT(*) from `countries` ".$buildParams;
		$countAll = sfetch($countQuery);
		$countAll = $countAll['COUNT(*)'];
		if (is_null($count)) $count = $countAll;
		$query = "select id, name, phone_code, continent_id from `countries` ".$buildParams." order by ".$sort." limit ".$offset.",".$count.";";
		return [
			"count" => $countAll,
			"items" => sfetch($query, true)
		];
	}

	function getContinents($params) {
		$offset = 0;
		$sort = 'id asc';
		$buildBool = false;
		foreach ($params as $key => $value) {
			switch ($key) {
				case 'id':
					$buildBool = true;
					$buildParams = " `id` = ".num($value);
				    break;
				case 'count':
					$count = num($params[$key]);
				    break;
				case 'offset':
					$offset = num($params[$key]);
				    break;
				case 'sort':
					switch ($params[$key]) {
						case 'name_asc':
							$sort = '`name` asc';
						    break;
						case 'name_desc':
							$sort = '`name` desc';
						    break;
						case 'id_desc':
							$sort = '`id` desc';
						    break;
					}
				    break;
			}
		}

        $buildParams = $buildBool ? "where ".$buildParams : '';
		$countQuery = "select COUNT(*) from `continents` ".$buildParams;
		$countAll = sfetch($countQuery);
		$countAll = $countAll['COUNT(*)'];
		if (is_null($count)) $count = $countAll;
		$query = "select id, name from `continents` ".$buildParams." order by ".$sort." limit ".$offset.",".$count.";";
		return [
			"count" => $countAll,
			"items" => sfetch($query, true)
		];
	}