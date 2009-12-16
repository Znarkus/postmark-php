<?php

/**
 * Postmark PHP class
 * 
 * Copyright 2009, Markus Hedlund, Mimmin AB, www.mimmin.com
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Markus Hedlund (markus@mimmin.com) at mimmin (www.mimmin.com)
 * @copyright Copyright 2009, Markus Hedlund, Mimmin AB, www.mimmin.com
 * @version 0.1
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * 
 * Usage:
 * Mail_Postmark::compose()
 *      ->to('address@example.com', 'Name')
 *      ->subject('Subject')
 *      ->messagePlain('Plaintext message')
 *      ->send();
 * 
 * or:
 * 
 * $email = new Mail_Postmark();
 * $email->to('address@example.com', 'Name')
 *      ->subject('Subject')
 *      ->messagePlain('Plaintext message')
 *      ->send();
 */
class Mail_Postmark
{
	private $_fromName;
	private $_fromAddress;
	private $_toName;
	private $_toAddress;
	private $_subject;
	private $_messagePlain;
	private $_messageHtml;
	private $_debugMode = false;
	
	/**
	* Initialize
	*/
	public function __construct()
	{
		$this->_default('POSTMARKAPP_MAIL_FROM_NAME', null);
		$this->_default('POSTMARKAPP_MAIL_FROM_ADDRESS', null);
		$this->_default('POSTMARKAPP_API_KEY', null);
		$this->from(POSTMARKAPP_MAIL_FROM_ADDRESS, POSTMARKAPP_MAIL_FROM_NAME)->messageHtml(null)->messagePlain(null);
	}
	
	/**
	* New e-mail
	* @return Mail
	*/
	public static function compose()
	{
		return new self();
	}
	
	/**
	* Turns debug output on
	* @return Mail
	*/
	public function &debug()
	{
		$this->_debugMode = true;
		return $this;
	}
	
	/**
	* Specify sender. Overwrites default From.
	* @param $address E-mail address used in From
	* @param $name Optional. Name used in From
	* @return Mail
	*/
	public function &from($address, $name)
	{
		$this->_fromAddress = $address;
		$this->_fromName = $name;
		return $this;
	}
	
	/**
	* Specify receiver
	* @param $address E-mail address used in To
	* @param $name Optional. Name used in To
	* @return Mail
	*/
	public function &to($address, $name)
	{
		$this->_toAddress = $address;
		$this->_toName = $name;
		return $this;
	}
	
	/**
	* Specify subject
	* @param @subject E-mail subject
	* @return Mail
	*/
	public function &subject($subject)
	{
		$this->_subject = $subject;
		return $this;
	}
	
	/**
	* Add plaintext message. Can be used in conjunction with messageHtml()
	* @param $message E-mail message
	* @return Mail
	*/
	public function &messagePlain($message)
	{
		$this->_messagePlain = $message;
		return $this;
	}
	
	/**
	* Add HTML message. Can be used in conjunction with messagePlain()
	* @param $message E-mail message
	* @return Mail
	*/
	public function &messageHtml($message)
	{
		$this->_messageHtml = $message;
		return $this;
	}
	
	/**
	* Sends the e-mail. Prints debug output if debug mode is turned on
	* @return Mail
	*/
	public function &send()
	{
		if (is_null(POSTMARKAPP_API_KEY)) {
			throw new Exception("Postmark API key is not set");
		}
		
		if (is_null($this->_fromAddress)) {
			throw new Exception("From address is not set");
		}
	
		$data = $this->_prepareData();
		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
			'X-Postmark-Server-Token: ' . POSTMARKAPP_API_KEY
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.postmarkapp.com/email');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$return = curl_exec($ch);
		
		if ($this->_debugMode) {
			echo "JSON: " . json_encode($data) . "\nHeaders: \n\t" . implode("\n\t", $headers) . "\nReturn:\n$return";
		}
		
		if (curl_error($ch) != '') {
			throw new Exception(curl_error($ch));
		}
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if ($this->_isTwoHundred($httpCode)) {
			$message = json_decode($return)->Message;
			throw new Exception("Error while mailing. Postmark returned HTTP code $httpCode with message \"$message\"");
		}
		
		return $this;
	}
	
	/**
	* Prepares the data array
	*/
	private function _prepareData()
	{
		$data = array(
			'Subject' => $this->_subject
		);
		
		$data['From'] = is_null($this->_fromName) ? $this->_fromAddress : "{$this->_fromName} <{$this->_fromAddress}>";
		$data['To'] = is_null($this->_toName) ? $this->_toAddress : "{$this->_toName} <{$this->_toAddress}>";
		
		if (!is_null($this->_messageHtml)) {
			$data['HtmlBody'] = $this->_messageHtml;
		}
		
		if (!is_null($this->_messagePlain)) {
			$data['TextBody'] = $this->_messagePlain;
		}
		
		return $data;
	}
	
	/**
	* If a number is 200-299
	*/
	private function _isTwoHundred($value)
	{
		return intval($value / 100) == 2;
	}
	
	/**
	* Defines a constant, if it isn't defined
	*/
	private function _default($name, $default)
	{
		if (!defined($name)) {
			define($name, $default);
		}
	}
}