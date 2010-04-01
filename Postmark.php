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
 * @version 0.3
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * 
 * Usage:
 * Mail_Postmark::compose()
 *      ->to('address@example.com', 'Name')
 *      ->subject('Subject')
 *      ->messagePlain('Plaintext message')
 *	    ->tag('Test tag')
 *      ->send();
 * 
 * or:
 * 
 * $email = new Mail_Postmark();
 * $email->to('address@example.com', 'Name')
 *      ->subject('Subject')
 *      ->messagePlain('Plaintext message')
 *	    ->tag('Test tag')
 *      ->send();
 */
 
class Mail_Postmark
{
	const DEBUG_OFF = 0;
	const DEBUG_VERBOSE = 1;
	const DEBUG_RETURN = 2;
	
	private $_fromName;
	private $_fromAddress;
	private $_tag;
	private $_toName;
	private $_toAddress;
	private $_replyToName;
	private $_replyToAddress;
	private $_subject;
	private $_messagePlain;
	private $_messageHtml;
	private $_debugMode = self::DEBUG_OFF;
	
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
	* @return Mail_Postmark
	*/
	public static function compose()
	{
		return new self();
	}
	
	/**
	* Turns debug output on
	* @param int $mode One of the debug constants
	* @return Mail_Postmark
	*/
	public function &debug($mode = self::DEBUG_VERBOSE)
	{
		$this->_debugMode = $mode;
		return $this;
	}
	
	/**
	* Specify sender. Overwrites default From.
	* @param string $address E-mail address used in From
	* @param string $name Optional. Name used in From
	* @return Mail_Postmark
	*/
	public function &from($address, $name = null)
	{
		$this->_fromAddress = $address;
		$this->_fromName = $name;
		return $this;
	}
	
	/**
	* Specify sender name. Overwrites default From name, but doesn't change address.
	* @param string $name Name used in From
	* @return Mail_Postmark
	*/
	public function &fromName($name)
	{
		$this->_fromName = $name;
		return $this;
	}

	/**
	* You can categorize outgoing email using the optional Tag  property.
	* If you use different tags for the different types of emails your 
	* application generates, you will be able to get detailed statistics
	* for them through the Postmark user interface.
	* Only 1 tag per mail i supported.
	* 
	* @param string $tag One tag
	* @return Mail_Postmark
	*/
	public function &tag($tag)
	{
		$this->_tag = $tag;
		return $this;
	}
	
	/**
	* Specify receiver
	* @param string $address E-mail address used in To
	* @param string $name Optional. Name used in To
	* @return Mail_Postmark
	*/
	public function &to($address, $name = null)
	{
		$this->_toAddress = $address;
		$this->_toName = $name;
		return $this;
	}
	
	/**
	* Specify reply-to
	* @param string $address E-mail address used in To
	* @param string $name Optional. Name used in To
	* @return Mail_Postmark
	*/
	public function &replyTo($address, $name = null)
	{
		$this->_replyToAddress = $address;
		$this->_replyToName = $name;
		return $this;
	}
	
	/**
	* Specify subject
	* @param string $subject E-mail subject
	* @return Mail_Postmark
	*/
	public function &subject($subject)
	{
		$this->_subject = $subject;
		return $this;
	}
	
	/**
	* Add plaintext message. Can be used in conjunction with messageHtml()
	* @param string $message E-mail message
	* @return Mail_Postmark
	*/
	public function &messagePlain($message)
	{
		$this->_messagePlain = $message;
		return $this;
	}
	
	/**
	* Add HTML message. Can be used in conjunction with messagePlain()
	* @param string $message E-mail message
	* @return Mail_Postmark
	*/
	public function &messageHtml($message)
	{
		$this->_messageHtml = $message;
		return $this;
	}
	
	/**
	* Sends the e-mail. Prints debug output if debug mode is turned on
	* @return Mail_Postmark
	*/
	public function &send()
	{
		if (is_null(POSTMARKAPP_API_KEY)) {
			throw new Exception('Postmark API key is not set');
		}
		
		if (is_null($this->_fromAddress)) {
			throw new Exception('From address is not set');
		}
		
		if (!isset($this->_toAddress)) {
			throw new Exception('To address is not set');
		}
		
		if (!$this->_validateAddress($this->_fromAddress)) {
			throw new Exception("Invalid from address '{$this->_fromAddress}'");
		}
		
		if (!$this->_validateAddress($this->_toAddress)) {
			throw new Exception("Invalid to address '{$this->_toAddress}'");
		}
		
		if (isset($this->_replyToAddress) && !$this->_validateAddress($this->_replyToAddress)) {
			throw new Exception("Invalid reply to address '{$this->_replyToAddress}'");
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
		
		if ($this->_debugMode == self::DEBUG_VERBOSE) {
			echo "JSON: " . json_encode($data) . "\nHeaders: \n\t" . implode("\n\t", $headers) . "\nReturn:\n$return";
		
		} else if ($this->_debugMode == self::DEBUG_RETURN) {
			return array(
				'json' => json_encode($data),
				'headers' => $headers,
				'return' => $return
			);
		}
		
		if (curl_error($ch) != '') {
			throw new Exception(curl_error($ch));
		}
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if (!$this->_isTwoHundred($httpCode)) {
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

		if (!is_null($this->_tag)) {
			$data['Tag'] = $this->_tag;
		}
		
		if (!is_null($this->_replyToAddress)) {
			$data['ReplyTo'] = is_null($this->_replyToName) ? $this->_replyToAddress : "{$this->_replyToName} <{$this->_replyToAddress}>";
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
	
	/**
	* Validates an e-mailadress
	*/
	private function _validateAddress($email)
	{
		// http://php.net/manual/en/function.filter-var.php
		return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	}
}