<?php

/**
 * Batchmode for Postmark PHP class
 * 
 * @author Ruud Kamphuis <ruud@1plus1media.nl>
 * @copyright Copyright 2011, Ruud Kamphuis, 1+1 media, www.1plus1media.nl
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * 
 * Usage:
 * 
 * $batch = new Mail_Postmark_Batch();
 * $batch->add(Mail_Postmark::compose()->addTo('jane@doe.com', 'Name')->subject('Subject')->messagePlain('Plaintext message'));
 * $batch->add(Mail_Postmark::compose()->addTo('johnny@doe.com', 'Name')->subject('Subject')->messagePlain('Plaintext message'));
 * $batch->send();
 * 
 * or:
 * 
 * $batch = new Mail_Postmark_Batch();
 * 
 * $email = new Mail_Postmark();
 * $email->addTo('jane@doe.com', 'Name')
 *       ->subject('Subject')
 *       ->messagePlain('Plaintext message');
 * $batch->add($email);
 * 
 * $email = new Mail_Postmark();
 * $email->addTo('johnny@doe.com', 'Name')
 *       ->subject('Subject')
 *       ->messagePlain('Plaintext message');
 * $batch->add($email);
 * $batch->send();
 *
 */
class Mail_Postmark_Batch extends Mail_Postmark
{
	protected $_apiUrl = 'https://api.postmarkapp.com/email/batch';
	protected $_messages = array();
	
	/**
	 * Adds a message to the queue
	 */
	public function add(Mail_Postmark $message)
	{
		$this->_messages[] = $message;
	}
	
	/**
	 * Sends the messages and clears the queue
	 */
	public function send()
	{
		$result = parent::send();
		$this->_messages = array();
		
		return $result;
	}
	
	/**
	 * This function is called bij parent::send() to generate the batch array
	 */
	public function _prepareData()
	{
		$data = array();
		foreach($this->_messages AS $message) {
			$data[] = $message->_prepareData();
		}
		return $data;
	}
	
	/**
	 * Validates all the messages in the queue
	 */
	public function _validateData()
	{
		foreach($this->_messages AS $message) {
			$message->_validateData();
		}
	}
}