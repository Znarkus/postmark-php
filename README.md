Postmark PHP class
==================

Copyright 2009 - 2011, Markus Hedlund, Mimmin AB, www.mimmin.com
Licensed under the MIT License.
Redistributions of files must retain the above copyright notice.

### Additional contributors

* Jeff Downie
* August Trometer
* Hristo Deshev
* jeffreyhunter77
* John Beales
* Geoff Wagstaff

## Requirements

All in-data must be encoded with UTF-8.


Getting started
---------------

	<?php
	
	// Well, yeah..
	require('Postmark.php');
	
	// Create a "server" in your "rack", then copy it's API key
	define('POSTMARKAPP_API_KEY', 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx');
	
	// Create a "Sender signature", then use the "From Email" here.
	// POSTMARKAPP_MAIL_FROM_NAME is optional, and can be overridden
	// with Mail_Postmark::fromName()
	define('POSTMARKAPP_MAIL_FROM_ADDRESS', 'user@example.com');
	define('POSTMARKAPP_MAIL_FROM_NAME', 'Example');
	
	// Create a message and send it
	Mail_Postmark::compose()
		->addTo('jane@smith.com', 'Jane Smith')
		->subject('Subject')
		->messagePlain('Plaintext message')
		->send();


Configuration
-------------

There are two ways of configuration.

### Adapter

An adapter class should be used for a more dynamic configuration.
The adapter must implement Mail_Postmark_Adapter_Interface. These
are the methods that must be implemented.

* `getApiKey` - Should return the API key
* `setupDefaults(Mail_Postmark &$mail)` - May be used to setup
  a default email, e.g. set From address.
* `log($logData)` - Is called immediately after the email is sent.
  `$logdata` is an array with keys `messageData`, `return`,
  `curlError` and `httpCode`.
  
See `Tests/Adapter.php` for example usage.

### Constants

Constants for configuration are:

* `POSTMARKAPP_API_KEY`
* `POSTMARKAPP_MAIL_FROM_ADDRESS`
* `POSTMARKAPP_MAIL_FROM_NAME` [optional]

`POSTMARKAPP_MAIL_FROM_ADDRESS` may be omitted, if method from()
is called.


Usage
-----

	Mail_Postmark::compose()
		->addTo('address@example.com', 'Name')
		->subject('Subject')
		->messagePlain('Plaintext message')
		->send();

or:

	$email = new Mail_Postmark();
	$email->addTo('address@example.com', 'Name')
		->subject('Subject')
		->messagePlain('Plaintext message')
		->send();


Batch sending
-------------

	$batch = new Mail_Postmark_Batch();
	$batch->add(Mail_Postmark::compose()->addTo('jane@doe.com', 'Name')->subject('Subject')->messagePlain('Plaintext message'));
	$batch->add(Mail_Postmark::compose()->addTo('johnny@doe.com', 'Name')->subject('Subject')->messagePlain('Plaintext message'));
	$batch->send();

or:

	$batch = new Mail_Postmark_Batch();

	$email = new Mail_Postmark();
	$email->addTo('jane@doe.com', 'Name')
	      ->subject('Subject')
	      ->messagePlain('Plaintext message');
	$batch->add($email);

	$email = new Mail_Postmark();
	$email->addTo('johnny@doe.com', 'Name')
	      ->subject('Subject')
	      ->messagePlain('Plaintext message');
	$batch->add($email);
	$batch->send();


Error handling
--------------

See PHPDOC for details on Exceptions thrown. If no API key
is set, an E_USER_ERROR will be raised.


Debugging
---------

Call method `debug(Mail_Postmark::DEBUG_VERBOSE)` or 
`debug(Mail_Postmark::DEBUG_RETURN)` to enable debug mode.
`DEBUG_VERBOSE` prints debug info and `DEBUG_RETURN` makes 
`send()` return debug info as an array.


Unit tests
----------

Unit tests are located in `Tests/`. Simple test is the unit test framework being used.

`Adapter.php` runs all tests relevant for adapter configuration, `Constants.php` runs
relevant tests for constant configration.


E-mail address validation
-------------------------

This class uses a regular expression to validate e-mail addresses, in addition to the
validation Postmark does. This regex isn't perfect. If you need more extensive validation,
please try http://www.dominicsayers.com/isemail/.
