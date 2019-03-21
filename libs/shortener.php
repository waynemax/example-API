<?php
	require __DIR__ . '/services/google/Googl.class.php';
	
	$key = $config['services']['google']['shortener']['keys'][0];
	$googlShortener = new Googl($key);

	function shortener($url) {
		global $googlShortener;
		if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)) {
			return $googlShortener->shorten($url);
		} else {
			return false;
		}
	}