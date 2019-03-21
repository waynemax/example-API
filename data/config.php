<?php
	/**
	 * @author: Wayne Maxim
	 * @description: remote data management system
	 * @dateOfCreation: 07/09/2017
	 **/

	function config() {
		return array(
			"mainClientDomain" => "likesoul.com",
			"domain" => "api.likesoul.com",
			"vApiDefault" => "1.1",
			"dirAPI" => "API".DIRECTORY_SEPARATOR."v",
			"filesDomain" => "http://files.likesoul.com/",
			"onlineTime" => 180,
			"tokenTimeValid" => 1200,
			"SCOPE_ADMIN_KEY" => "*****",
			"services" => [
				"smsLimit" => [
					"day" => 40,
					"s300" => 10 //300s
				],
				"systemPassword" => "", // системный пароль для отправки тестовых смс
                "yandex" => [
                    "money" => [
                        "shopId" => 0, // идентиикатор который нам выдает яндекс
                        "scid" => 0, // идентиикатор который нам выдает яндекс
                        "url" => "https://money.yandex.ru/eshop.xml"
                    ]
                ],
                "google" => [
					"fcm" => [
						'key' => ''
                    ],
					"shortener" => [
						"keys" => [ // ключи для сокращения ссылок в API GOOGLE SHORTENER
							""
						]
					]
				],
				"epochta" => [
					//"username" => "",
					//"password" => ""
				],
				"twilio" => [
				    'email' => '',
				    'password' => '',
                    'sid' => '',
                    'token' => '',
                    'from' => [
                        '+151***...'
                    ]
				]
			]
		);
	}
