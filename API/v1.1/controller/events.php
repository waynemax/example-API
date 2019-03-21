<?php
	function eventsAdd(int $receiver_id, int $event_author_id, int $type, $description = NULL, $object_id = NULL, $images = NULL) {
		$dataInsert = [
			'object_id' => $object_id,
			'description' => $description,
			'type' => intval($type),
			'date_create' => time(),
			'images' => $images,
			'event_author_id' => intval($event_author_id),
			'receiver_id' => intval($receiver_id)
		];
		$qInsert = qInsert("events", $dataInsert);
		squery($qInsert['query']);
		return [
			"id" => intval(insertID())
		];
	}

	function eventsGet(int $receiver_id, $params, int $offset = 0, int $count = 20) {
		$args = func_get_args();
		$stringsParams = [];
		$stringsParams['receiver_id'] = " events.receiver_id = ".num($receiver_id)." ";
		$fields = ['id'];
		foreach ($params as $k => $v) {
			switch ($k) {
				case 'type':
					$stringsParams['type'] = " events.type = ".num($v)." ";
				break;
				case 'fields':
					if (!is_null($v)) {
						$fields = [];
						foreach ($v as $kk => $vv) {
							$fields[] = "events.".$vv;
						}
					}
				break;
			}
		}
		$fields = array_merge($fields, [
			"users.id as uid",
			"users.first_name",
			"users.last_name",
			"users.last_seen",
			"users.photo_id",
			"Concat_ws('+', uploads.file, uploads.o_size, uploads.ext) as file"
		]);
		$selectEventsQuery = select(
			$fields,
			[" events left join users on users.id = events.event_author_id left join uploads on uploads.id = users.photo_id "],
			join("&&", $stringsParams),
			false,
			$offset,
			$count
		);
		$selectEventsQuery = sfetch($selectEventsQuery, true);
		if ($selectEventsQuery) {
			return [
				"status" => true,
				"data" => eventsTreatment($selectEventsQuery, [
					"photo_id", "last_seen", "images", "videos", "first_name", "last_name", "uid"
				])
			];
		} else {
			return [
				"status" => false,
				"data" => []
			];
		}
	}

	function eventsTreatment($dataResult, $noOutFields = array()) {
        $data = array();
        foreach ($dataResult as $key => $value) {
            foreach ($value as $k => $v) {
                switch ($k) {
                    case 'file':
                        if (!is_null($v) && $v) {
                            $ve = explode("+", $v);
                            $data[$key]['author']['photo'] = buildImageFileObject($ve[0], $ve[1], $ve[2]);
                            $data[$key]['author']['photo']['default'] = false;
                        } else {
                            $data[$key]['author']['photo'] = imageDefault();
                            $data[$key]['author']['photo']['default'] = true;
                        }
                        continue 2;
                        break;
                    case 'images':
                        if (!is_null($v) && $v) {
                            $files = explode(",", $v);
                            foreach ($files as $filekk => $filevv) {
                                $ve = explode("+", $filevv);
                                $data[$key]['attachments']['images'][] = buildImageFileObject($ve[0], $ve[1], $ve[2]);
                            }
                        }
                        continue 2;
                        break;
                    case 'videos':
                        if (!is_null($v) && $v) {
                            $files = explode(",", $v);
                            foreach ($files as $filekk => $filevv) {
                                $ve = explode("+", $filevv);
                                $data[$key]['attachments']['videos'][] = buildVideoFileObject($ve[0], $ve[1], $ve[2]);
                            }
                        }
                        continue 2;
                        break;
                    case 'uid':
                        if (!is_null($v) && $v) {
                            $data[$key]['author']['id'] = $v;
                        }
                        break;
                    case 'first_name':
                    case 'last_name':
                        if (!is_null($v)) {
                            $data[$key]['author'][$k] = $v;
                        }
                        break;
                    case 'last_seen':
                        if (!is_null($v)) {
                            $data[$key]['author']['online'] = checkOnline($v);
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