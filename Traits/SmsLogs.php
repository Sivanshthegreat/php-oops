<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/commonActions/databaseOperations.php";

use commonActions\databaseOperation;

/**
 * Trait SmsLogs - for logging SMS APIs.
 */
trait SmsLogs {

	/**
	 * Create logs for SMS based on service type (twilio, brave)
	 * 
	 * @param string $serviceType The type of SMS service (e.g., 'twilio', 'brave')
	 * @param array $dataForLogs The data to be logged, such as message SID, status, recipient number, and sent time
	 * @return void
	 */
    public static function insertSmsLog(string $serviceType, array $dataForLogs): void
	{
        $db = new commonActions\databaseOperation();

		$columns = ['serviceType', 'messageSid', 'status', 'recipientNumber', 'sentTime'];
		$values = [$serviceType, $dataForLogs['messageSid'], $dataForLogs['status'], $dataForLogs['recipientNumber'], $dataForLogs['sentTime']];

		//adding log for sms
		$db->insert('smsLogs', $columns, $values);

		return;
    }
	
	/**
	 * Retrieve SMS logs
	 * 
	 * @return void
	 */
	public static function getSmsLogs($serviceType): void
	{
		$db = new commonActions\databaseOperation();
		$query = "SELECT * FROM smsLogs WHERE serviceType = {$serviceType} ORDER BY id DESC";
		
		// Fetching logs from database
		$db->fetchData($query, true);
	}

}