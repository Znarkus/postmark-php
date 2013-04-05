<?php

require_once('simpletest/autorun.php');
require_once(dirname(__FILE__) . '/../../bootstrap.php');

class BaseTests extends UnitTestCase
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
	
	
	public function testCompose()
	{
		$this->assertIsA(Postmark\Mail::compose(Postmark\Mail::TESTING_API_KEY), 'Postmark\\Mail');
	}
	
	public function testBasic()
	{
		$debugData = $this->_mail
			->send();
		#$this->dump($debugData);die;
		$this->assertEqual($debugData['json'], '{"Subject":"The subject","From":"\"Foo Bar\" <foo@bar.com>","To":"\"John Smith\" <john@smith.com>","TextBody":"Test message"}');
	}
	
	public function testMultipleTo()
	{
		$debugData = $this->_mail
			->addTo('jane@smith.com', 'Jane Smith')
			->send();
		
		#$this->dump($debugData);die;
		$this->assertEqual($debugData['json'], '{"Subject":"The subject","From":"\"Foo Bar\" <foo@bar.com>","To":"\"John Smith\" <john@smith.com>, \"Jane Smith\" <jane@smith.com>","TextBody":"Test message"}');
	}
	
	public function testResetTo()
	{
		$debugData = $this->_mail
			->to('jane@smith.com', 'Jane Smith')
			->send();
		
		#$this->dump($debugData);die;
		$this->assertEqual($debugData['json'], '{"Subject":"The subject","From":"\"Foo Bar\" <foo@bar.com>","To":"\"Jane Smith\" <jane@smith.com>","TextBody":"Test message"}');
	}
	
	public function testCc()
	{
		$debugData = $this->_mail
			->addCc('jane@smith.com', 'Jane Smith')
			->addCc('baby@smith.com')
			->send();
		
		#$this->dump($debugData);die;
		$this->assertEqual($debugData['json'], '{"Subject":"The subject","From":"\"Foo Bar\" <foo@bar.com>","To":"\"John Smith\" <john@smith.com>","Cc":"\"Jane Smith\" <jane@smith.com>, baby@smith.com","TextBody":"Test message"}');
	}
	
	public function testBcc()
	{
		$debugData = $this->_mail
			->addBcc('jane@smith.com')
			->addBcc('baby@smith.com', 'Baby Smith')
			->send();
		
		#$this->dump($debugData);die;
		$this->assertEqual($debugData['json'], '{"Subject":"The subject","From":"\"Foo Bar\" <foo@bar.com>","To":"\"John Smith\" <john@smith.com>","Bcc":"jane@smith.com, \"Baby Smith\" <baby@smith.com>","TextBody":"Test message"}');
	}
	
	public function testToValidation()
	{
		$this->expectException('InvalidArgumentException');
		$debugData = $this->_mail
			->addTo('jane..smith@smith.com')
			->send();
	}
	
	public function testCcValidation()
	{
		$this->expectException('InvalidArgumentException');
		$debugData = $this->_mail
			->addCc('jane..smith@smith.com')
			->send();
	}
	
	public function testBccValidation()
	{
		$this->expectException('InvalidArgumentException');
		$debugData = $this->_mail
			->addBcc('jane..smith@smith.com')
			->send();
	}
	
	public function testReplyToValidation()
	{
		$this->expectException('InvalidArgumentException');
		$debugData = $this->_mail
			->replyTo('jane..smith@smith.com')
			->send();
	}
	
	public function testFromValidation()
	{
		$this->expectException('InvalidArgumentException');
		$debugData = $this->_mail
			->from('jane..smith@smith.com')
			->send();
	}
	
	public function testTrimValidation()
	{
		$debugData = $this->_mail
			->to(' jane.smith@smith.com')
			->send();
		
		$this->assertNoErrors();
	}
	
	public function testCustomHeaders()
	{
		$debugData = $this->_mail
			->addHeader('CUSTOM-HEADER', 'value')
			->addHeader('CUSTOM-HEADER-2', 'value 2')
			->send();
		
		#$this->dump($debugData);die;
		$this->assertEqual($debugData['json'], '{"Subject":"The subject","From":"\"Foo Bar\" <foo@bar.com>","To":"\"John Smith\" <john@smith.com>","TextBody":"Test message","Headers":[{"Name":"CUSTOM-HEADER","Value":"value"},{"Name":"CUSTOM-HEADER-2","Value":"value 2"}]}');
	}
	
	public function testNoOutput()
	{
		ob_start();
		$debugData = $this->_mail->debug(Postmark\Mail::DEBUG_OFF)->send();
		$this->assertEqual(ob_get_clean(), '');
	}
	
	public function testReturnTrue()
	{
		$debugData = $this->_mail->debug(Postmark\Mail::DEBUG_OFF)->send();
		$this->assertTrue($debugData);
	}
	
	public function testDisplayName()
	{
		$debugData = $debugData = $this->_mail
			->addTo('jane.smith@smith.com', '"Smith, Jane')
			->send();
		
		$this->assertEqual($debugData['json'], '{"Subject":"The subject","From":"\"Foo Bar\" <foo@bar.com>","To":"\"John Smith\" <john@smith.com>, \"Smith, Jane\" <jane.smith@smith.com>","TextBody":"Test message"}');
	}
}