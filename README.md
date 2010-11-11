Postmark PHP class
==================

Copyright 2009 - 2010, Markus Hedlund, Mimmin AB, www.mimmin.com
Licensed under The MIT License
Redistributions of files must retain the above copyright notice.

### Additional contributors

* Jeff Downie
* August Trometer
* Hristo Deshev
* jeffreyhunter77
* John Beales


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