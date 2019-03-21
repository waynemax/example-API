<?php
	function epochtaSend($to, $body) {
		global $config;
		$src = '<?xml version="1.0" encoding="UTF-8"?>
		<SMS>
			<operations>
			<operation>SEND</operation>
			</operations>
			<authentification>
			<username>'.$config['services']['epochta']['username'].'</username>
			<password>'.$config['services']['epochta']['password'].'</password>
			</authentification>
			<message>
			<sender>Smiber</sender>
			<text>'.$body.'</text>
			</message>
			<numbers>
			<number messageID="msg11">'.$to.'</number>
			</numbers>
		</SMS>';
		$curl = curl_init();
		$curlOptions = [
			CURLOPT_URL => 'http://api.atompark.com/members/sms/xml.php',
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_POST => true,
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_TIMEOUT => 100,
			CURLOPT_POSTFIELDS => ['XML' => $src],
		];
		curl_setopt_array($curl, $curlOptions);
		if (false === ($result = curl_exec($curl))) {
			throw new Exception('Http request failed');
		}
		curl_close($curl);
		return $result;
	}