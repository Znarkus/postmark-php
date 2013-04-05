<?php

require_once('simpletest/autorun.php');
require_once(dirname(__FILE__) . '/../../bootstrap.php');

/**
* Only run from Adapter test suite
*/
class LogTests extends UnitTestCase
{
	private $_mail;
	
	public function setUp()
	{
		$this->_mail = Postmark\Mail::compose(Postmark\Mail::TESTING_API_KEY)
			->debug(Postmark\Mail::DEBUG_RETURN)
			->from('foo@bar.com', 'Foo Bar')
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