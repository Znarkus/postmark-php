<?php

interface Mail_Postmark_Adapter_Interface
{
	public static function getApiKey();
	public static function setupDefaults(Mail_Postmark &$mail);
	public static function log($logData);
}