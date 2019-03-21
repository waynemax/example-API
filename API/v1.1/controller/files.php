<?php
	function fileGet($id) {
		if (!is_null($id)) {
			$res = sfetch("select * from `uploads` where `id` = '".escape($id)."';");
			if ($res) {
				switch ($res['type']) {
					case 'video':
					case 'image':
						if ($res['type'] == 'image') {
							$buildObject = buildImageFileObject($res['file'], $res['o_size'], $res['ext']);
						} elseif ($res['type'] == 'video') {
							$buildObject = buildVideoFileObject($res['file'], $res['ext'], intval($res['ready']));
						}
					break;
				}
				return array(
					"status" => true,
					"file" => $buildObject
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

	function filesShorterV2_en(string $file) {
        $strings = strings();
        $plusExplode = explode("+", $file);
        $file = explode("|", $file);
        $file[0] = str_replace($strings['uploads']['images']['outConstantsStrings'], $strings['uploads']['images']['inConstantsInt'], $file[0]);
        $sizes = explode("&", $file[2]);
        unset($sizes[count($sizes)-1]);
        $maxSize = max(toType($sizes));
        if ($strings['uploads']['images']['outConstants'][$maxSize]) {
            $maxSize = $strings['uploads']['images']['outConstants'][$maxSize];
        }
        $shortReturn = $file[0].$file[1]."+".$maxSize."+".$plusExplode[1];
        return $shortReturn;
    }

    function filesShorterV2_de(string $file) {
        $strings = strings();
	    $file = explode("+", $file);
        if ($strings['uploads']['images']['inConstants'][$file[1]]) {
            $file[1] = toType($strings['uploads']['images']['inConstants'][$file[1]]);
            $sizes = [];
            foreach ($strings['uploads']['images']['scales'] as $k => $v) {
                if ($v <= $file[1]) {
                    $sizes[].= $v;
                }
            }
            $sizes = join("&", $sizes);
        }
        $file[1] = $sizes;
        $file[0] = explode("/", $file[0]);
        if ($strings['uploads']['images']['inConstants'][$file[0][2]]) {
            $file[0][2] = $strings['uploads']['images']['inConstants'][$file[0][2]];
        }
        if ($strings['uploads']['images']['inConstants'][$file[0][3]]) {
            $file[0][3] = $strings['uploads']['images']['inConstants'][$file[0][3]];
        }
        $ext = $file[0][3];
        $file[0] = $file[0][0]."/".$file[0][1]."/".$file[0][2]."/".$file[0][3]."/|".$file[0][4];
        $file = $file[0]."|".$file[1]."&+".$file[2]."+".$ext;
        return $file;
    }

	function filesGet($ids) {
		if (!is_null($ids)) {
			$INids = [];
			foreach ($ids as $k => $v) {
				$INids[] = "'".escape($v)."'";
			}
			$INidsStr = join(",", $INids);
			$q = "select * from `uploads` where `id` IN (".$INidsStr.");";
			$res = sfetch($q, true);
			if ($res) {
				$images = [];
				$videos = [];
				foreach ($res as $kk => $vv) {
					switch ($vv['type']) {
						case 'video':
						case 'image':
							if ($vv['type'] == 'image') {
								$images[] = $vv['file']."+".$vv['o_size']."+".$vv['ext'];
							} elseif ($vv['type'] == 'video') {
								$videos[] = $vv['file']."+".$vv['ext']."+".intval($vv['ready']);
							}
						break;
					}
				}
				return array(
					"status" => true,
					"images" => $images,
					"videos" => $videos
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

	function getStringFileGetInQuery($key, $tbNumMain, $tbNumUploadTable) {
		return "(select Concat_ws('+', tb".$tbNumUploadTable.".file, tb".$tbNumUploadTable.".o_size, tb".$tbNumUploadTable.".ext) as file from uploads tb".$tbNumUploadTable." where tb".$tbNumUploadTable.".id = tb".$tbNumMain.".".$key.") as ".$key;
	}

	function buildImageFileObject($file, $oSize, $ext) {
	    //file - 595/9aa92dce5255/image/png/|7315c47c866fa7d|60&120&240&360&
		$config = config();
		$file = explode("|", $file);
		$name = $file[1];
		$sizes = explode("&", $file[2]);
		unset($sizes[count($sizes)-1]);
		$domain = "";
		$images = [];
		$uploadDir = 'uploads/'. $file[0];
		foreach($sizes as $value) {
			$images[$value] = $config['filesDomain'].$uploadDir.$value."_".$name.".".strtolower($ext);
		}
		$images['original'] = $config['filesDomain'].$uploadDir."o"."_".$name.".".strtolower($ext);
		$oSize = explode("|", $oSize);
		$images['width'] = intval($oSize[0]);
		$images['height'] = intval($oSize[1]);
		$images['type'] = 'image';
		return $images;
	}

	function imageDefault() {
	    global $config;
		$src = 'https://'.$config['domain'].'/static/images/no_photo/';
		$nameDefault = 'default.jpg';
		return [
			60 => $src.'60_'.$nameDefault,
			120 => $src.'120_'.$nameDefault,
			240 => $src.'240_'.$nameDefault,
			360 => $src.'360_'.$nameDefault,
			480 => $src.'480_'.$nameDefault,
			600 => $src.'600_'.$nameDefault,
			720 => $src.'720_'.$nameDefault,
			'original' => $src.'o_'.$nameDefault,
			'width' => 720,
			'height' => 720,
			'type' => 'image',
			'default' => true
		];
	}

	function buildVideoFileObject($file, $ext, $ready) {
		if (!$ready) {
			return [
				'ready' => 0
			];
		} else {
			$config = config();
			$file = explode("|", $file);
			$name = $file[1];
			$sizes = explode("&", $file[2]);
			unset($sizes[count($sizes)-1]);
			$domain = "";
			$videos = [];
			$uploadDir = 'uploads/'.$file[0];
			foreach($sizes as $value) {
				$videos[$value] = $config['filesDomain'].$uploadDir.$value."_".$name.".".strtolower($ext);
			}
			$videos['stream'] = $config['filesDomain'].$uploadDir.$name.".m3u8";
			$oSize = explode("|", $oSize);
			$videos['type'] = 'video';
			$videos['ready'] = true;
			return $videos;
		}
	}