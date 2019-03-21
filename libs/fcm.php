<?php
	function fcmSend($tokens, $data, $title, $text) {
		$config = config();
		$fcmObject = [
			"typeOut" => ["response"],
			"url" => "https://fcm.googleapis.com/fcm/send",
			"type" => "POST",
			"headers" => [
				"Authorization:key=".$config['services']['google']['fcm']['key'],
				"Content-Type:application/json"
			],
			"data" => json_encode([
				"registration_ids" => $tokens,
				"content_available" => false,
				"notification" => [
					'title' => $title,
					'text' => $text,
					'icon' => 'ic_launcher',
					'sound' => 'default',
                    'channel' => 'default'
                    //'badge' => '1'
				],
				"data" => $data
			])
		];
		$fcmReturn = curl($fcmObject);
		return [
		    "httpReturn" => $fcmReturn,
            "requestData" => $data
        ];
	}