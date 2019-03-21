<?php
	require __DIR__ . '/services/twilio/Twilio/autoload.php';
	use Twilio\Rest\Client;

	$sid = $config['services']['twilio']['sid'];
	$token = $config['services']['twilio']['token'];
	$clientTwilio = new Client($sid, $token);

	function twilioSend($to, $body) {
		global $clientTwilio, $config;
		$clientTwilio->messages->create($to, [
			'from' => $config['services']['twilio']['from'][0],
			'body' => $body
		]);
	}
?>