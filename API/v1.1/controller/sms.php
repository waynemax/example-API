<?php
	function smsLogDbAdd($serviceType, $phoneRecipient, $smsType, $price, $currency = 'USD', $text = NULL) {
		$qInsert = qInsert("sms_log", [
				"id" => NULL,
				"service_type" => $serviceType,
				"phone_recipient" => $phoneRecipient,
				"sms_type" => $smsType,
				"date" => time(),
				"price" => $price,
				"currency" => $currency,
                "text" => $text ? escape($text) : NULL
			], NULL
        );
		squery($qInsert['query']);
	}

	function getCountSmsByPeriod($phoneRecipient, $periodType = 1) {
		$phoneRecipient = escape($phoneRecipient);
		switch ($periodType) {
			case 1:
				$seconds = 86400;
				$timeMin = time() - $seconds;
				$selectQuery = select(
					["Count(*)"],
					["sms_log"],
					"date > ".$timeMin." && `phone_recipient` = ".$phoneRecipient
				);
			break;
			case 2:
				$seconds = 180;
				$timeMin = time() - $seconds;
				$selectQuery = select(
					["Count(*)"],
					["sms_log"],
					"date > ".$timeMin." && `phone_recipient` = ".$phoneRecipient
				);
			break;
		}
		$sf = sfetch($selectQuery);
		return $sf ? num($sf['Count(*)']) : 0;
	}

	function getSmsProviderByISOCode($ISO) {
		$selectQuery = sfetch(select(
			["sms_provider"],
			["sms_providers"],
			"phone_code = ".num($ISO)
		));
		$returnProvider = [
			"typeId" => 1,
			"sms_provider" => "TWILIO"
		];
		if ($selectQuery) {
			switch ($selectQuery['sms_provider']) {
				case 'EPOCHTA':
					$returnProvider = [
						"typeId" => 2,
						"sms_provider" => $selectQuery['sms_provider']
					];
				break;
			}
		}
		return $returnProvider;
	}