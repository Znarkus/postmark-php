<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');

class Mail_Postmark_Adapter implements Postmark\MailAdapterInterface
{
	public static $latestLogEntry;
	
	public static function getApiKey()
	{
		return Postmark\Mail::TESTING_API_KEY;
	}
	
	public static function setupDefaults(Postmark\Mail &$mail)
	{
		$mail->from('foo@bar.com', 'Foo Bar');
	}
	
	public static function log($logData)
	{
		self::$latestLogEntry = $logData;
	}
}

require_once('simpletest/autorun.php');

class AdapterTests extends TestSuite {
	public function AdapterTests()
	{
		$this->TestSuite('Adapter tests');
		$this->addFile(dirname(__FILE__) . '/Tests_Base.php');
		$this->addFile(dirname(__FILE__) . '/Tests_Log.php');
		$this->addFile(dirname(__FILE__) . '/Tests_Attachment.php');
	}
}
