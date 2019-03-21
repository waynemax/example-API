<?php
	function curl($array) {
		$type = $array['type'];
		$url = $array['url'];
		if ($type == "POST") $data = $array['data'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $array['headers']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if ($type == "POST") curl_setopt($ch, CURLOPT_POST, true);
		if ($array['typeOut'][0] == "info") curl_setopt($ch, CURLOPT_HEADER, true);
		if ($array['typeOut'][0] == "info") curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		if ($type == "POST") curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$out = curl_exec($ch);
		$outInfo = curl_getinfo($ch);
		curl_close($ch);
		switch ($array['typeOut'][0]) {
			case "response":
				return ($array['typeOut'][1] == "json") ? json_encode($out) : $out;
			break;
			case "info":
				return ($array['typeOut'][1] == "json") ? json_encode($outInfo) : $outInfo;
			break;
		}
	}