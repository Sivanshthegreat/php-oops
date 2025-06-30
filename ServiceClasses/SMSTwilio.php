<?php

/**
 * Class to provide Twilio SMS Services
 */
require_once '../vendor/autoload.php';
require_once '../ServicesInterfaces/SmsInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Traits/SmsLogs.php';

use Twilio\Rest\Client;

class SMSTwilio implements SmsInterface
{
	use SmsLogs;

	public $twilioObject;
	public $senderNumber;

	public function __construct()
	{
		$creds = $this->getCredentials();
		$this->twilioObject = new Client($creds['sid'], $creds['token']);
		$this->senderNumber = $creds['twilioNumber'];
	}

	/**
	 * Send twilio sms
	 * 
	 * @param $to string The phone number to send the SMS to
	 * @param $message string The message to send
	 * @param $extraInformation array Additional information for the SMS, such as media URL, status callback, and validity period
	 * @return array An array containing the success status, message SID, status, recipient number, and sent time
	 * @throws \Twilio\Exceptions\RestException If there is an error while sending the SMS
	 * @throws \Exception If there is a general error
	 */
	public function send(string $to, string $message, array $extraInformation): array
	{
		$smsParameter = [
	        'from' => $this->senderNumber,
	        'body' => $this->parseTemplate($message),
		];

		if (!empty($extraInformation['mediaUrl'])) {
			$smsParameter['mediaUrl'] = $extraInformation['mediaUrl'];
		}
		if (!empty($extraInformation['statusCallback'])) {
			$smsParameter['statusCallback'] = $extraInformation['statusCallback'];
		}
		if (!empty($extraInformation['validityPeriod'])) {
			$smsParameter['validityPeriod'] = $extraInformation['validityPeriod'];
		}

		try {
			$message = $this->twilioObject->messages->create( $to, $smsParameter );
			$response = [
		        'success' => true,
		        'message_sid' => $message->sid,
		        'status' => $message->status,
		        'to' => $to,
		        'sent_at' => date('c'),
		    ];
		} catch(\Twilio\Exceptions\RestException $e) {
		    throw new \Exception("Twilio SMS Error: " . $e->getMessage(), $e->getCode());
		} catch (\Exception $e) {
		    throw new \Exception("General Error: " . $e->getMessage(), $e->getCode());
		}

		//Adding logs for sms sent
		SmsLogs::insertSmsLog('twilio', [
		    'messageSid' => $response['message_sid'],
		    'status' => $response['status'],
		    'recipientNumber' => $to,
		    'sentTime' => $response['sent_at']
		]);

		return $response;
	}

	/**
	 * Parse the message template and replace placeholders with actual data
	 *
	 * @param $message string The message template containing placeholders
	 * @return string The message with placeholders replaced by actual data
	 */
	public function parseTemplate($message): string
	{
		$data = ['id' => 12345, 'booking_barcode' => 'B00012']; //need to improve

		foreach ($data as $key => $value) {
	        $message = str_replace("{" . $key . "}", $value, $message);
	    }

	    return $message;
	}

	/**
	 * Get Twilio credentials
	 *
	 * @return array An array containing the Twilio SID, token, and sender number
	 */
	public function getCredentials(): array
	{
		return [
			'sid' => 'gshjlksd0-wjksdnsdksd,ds',
			'token' => 'snbdhgeju839ihnhejdkmdd3809-p',
			'twilioNumber' => '+967237928202',
		];
	}
}