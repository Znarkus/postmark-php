<?php

require_once('simpletest/autorun.php');
require_once(dirname(__FILE__) . '/../Postmark.php');
require_once(dirname(__FILE__) . '/../Postmark_Batch.php');

class BatchTests extends UnitTestCase
{
	private $_mail1;
	private $_mail2;
	
	public function setUp()
	{
		$this->_mail1 = Mail_Postmark::compose()
			->debug(Mail_Postmark::DEBUG_RETURN)
			->to('john@smith.com', 'John Smith')
			->subject('The subject1')
			->messagePlain('Test message1');
			
		$this->_mail2 = Mail_Postmark::compose()
			->debug(Mail_Postmark::DEBUG_RETURN)
			->to('jane@doe.com', 'Jane Doe')
			->subject('The subject2')
			->messagePlain('Test message2');
	}
	
	public function tearDown()
	{
		unset($this->_mail1, $this->_mail2);
	}
	
	public function testBatch()
	{
		$batch = new Mail_Postmark_Batch();
		$batch->debug(Mail_Postmark::DEBUG_RETURN);
		 
		$batch->add($this->_mail1);
		$batch->add($this->_mail2);
		
		$debugData = $batch->send();

		$this->assertEqual($debugData['json'], '[{"Subject":"The subject1","From":"\"Foo Bar\" <foo@bar.com>","To":"\"John Smith\" <john@smith.com>","TextBody":"Test message1"},{"Subject":"The subject2","From":"\"Foo Bar\" <foo@bar.com>","To":"\"Jane Doe\" <jane@doe.com>","TextBody":"Test message2"}]');
	}
}