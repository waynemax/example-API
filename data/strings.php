<?php
  /**
   * @author: Wayne Maxim
   * @description: remote data management system
   * @dateOfCreation: 12/09/2017
   **/

	function strings() {
		$return = [
		    'chats' => [
		      'config' => [
		          'maxMembers' => 30, //максимальное количество человек в чате
		          'maxNumberCharacters' => 1024, //максимальное количество символов в названии
              ]
            ],
			'catalogs' => [
				'range' => [
					'gender' => [0, 2],
					'listIdContinent' => [1, 8],
					'listIdCountry' => [0, 218],
					'listIdRegion' => [0, 1611],
					'listIdCity' => [1, 17287]
				]
			],
			'API' => [
				'protocol' => "https://",
				'domain' => "api.***.***",
				'dir' => "api"
			],
			'domain' => "api.***.***",
			'vApiDefault' => "1.0",
			'dirAPI' => "API"."/"."v",
			'smtp' => [
				'host' => 'mail@***.***',
				'from' => 'do-not-reply@***.***',
				'pass' => '***',
				'port' => 22
			]
		];

		$return['API']['methods'] = [
			'auth.token' => $return['API']['protocol']
				.$return['API']['domain']."/"
				.$return['API']['dir']."/"."auth.token",
			'auth.get' => $return['API']['protocol']
				.$return['API']['domain']."/"
				.$return['API']['dir']."/"."auth.get"
		];

		$return['API']['data'] = [
			'client_id' => 2,
			'client_secret' => "***",
			'scope' => 16383,
			'display' => "none"
		];

		$return['API']['redirect_uri'] = 'default';

		$return['finance'] = [
			'balance' => [
				'minTransferMoney' => 5.00,
				'maxTransferMoney' => 1000.00,
			]
		];

		$return['phone'] = [
            "minCountSymbolsInPhone" => 6,
            "maxCountSymbolsInPhone" => 10,
            "defaultPhoneCode" => "+7"
        ];

		$return['posts'] = [
		    'maxCountObjectsForRepost' => 10
        ];

		$return['events']['types'] = [
			1 	=> "COMMENT_TO_ME",
			2 	=> "LIKE_TO_ME",
			3 	=> "RECEIVED_MONEY",
			4 	=> "REPOST_OF_ME",
			5 	=> "PAID",
			6 	=> "BONUS",
			7 	=> "NEW_FOLLOWER",
			8 	=> "FAIL_CASH_OUT",
			9 	=> "REPLY_TO_ME",
			10 	=> "PAYMENT_FOR_CONTENT",
			11 	=> "EMAIL_REMOVED",
			12 	=> "USER_LOCKED",
			13 	=> "USER_UNLOCKED",
			14 	=> "CONTENT_LOCKED",
			15 	=> "CONTENT_REJECTED",
			16 	=> "CONTENT_UNLOCKED",
			17 	=> "BLOG_LOCKED",
			18 	=> "BLOG_UNLOCKED",
			19 	=> "PROMO_CHARGE",
			20 	=> "PROMO_COUPON"
		];

		$return['uploads']['images'] = [
		    'scales' => [60,120,240,360,480,600,720,1080],
		    'maxUploadCount' => 10,
            'inConstantsInt' => ["1","2","3","4","5","6","7","8","9","10","11","12"],
            'outConstantsStrings' => ["image","jpg","gif","png","60","120","240","360","480","600","720","1080"],
            'inConstants' => [
                "1" => "image",
                "2" => "jpg",
                "3" => "gif",
                "4" => "png",
                "5" => "60",
                "6" => "120",
                "7" => "240",
                "8" => "360",
                "9" => "480",
                "10" => "600",
                "11" => "720",
                "12" => "1080"
            ],
            'outConstants' => [
                "image" => "1",
                "jpg" => "2",
                "gif" => "3",
                "png" => "4",
                "60" => "5",
                "120" => "6",
                "240" => "7",
                "360" => "8",
                "480" => "9",
                "600" => "10",
                "720" => "11",
                "1080" => "12"
            ],
        ];
		return $return;
	}