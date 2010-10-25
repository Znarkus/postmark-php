<?php

require_once('simpletest/autorun.php');
require_once(dirname(__FILE__) . '/../Postmark.php');

/**
* Only run from Adapter test suite
*/
class LogTests extends UnitTestCase
{
	private $_mail;
	
	public function setUp()
	{
		$this->_mail = Mail_Postmark::compose()
			->debug(Mail_Postmark::DEBUG_RETURN)
			->to('john@smith.com', 'John Smith')
			->subject('The subject')
			->messagePlain('Test message');
	}
	
	public function tearDown()
	{
		unset($this->_mail);
	}
	
	
	public function testBase()
	{
		$debugData = $this->_mail
			->send();
		
		$this->assertEqual($debugData['json'], json_encode(Mail_Postmark_Adapter::$latestLogEntry['messageData']));
	}
}