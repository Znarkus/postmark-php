Postmark PHP class
==================

Copyright 2009, Markus Hedlund, Mimmin AB, www.mimmin.com
Licensed under The MIT License
Redistributions of files must retain the above copyright notice.


Configuration
-------------

Constants for configuration are:

* `POSTMARKAPP_API_KEY`
* `POSTMARKAPP_MAIL_FROM_ADDRESS`
* `POSTMARKAPP_MAIL_FROM_NAME` [optional]

`POSTMARKAPP_MAIL_FROM_ADDRESS` may be omitted, if method from()
is called.


Usage
-----

	Mail_Postmark::compose()
		->to('address@example.com', 'Name')
		->subject('Subject')
		->messagePlain('Plaintext message')
		->send();

or:

	$email = new Mail_Postmark();
	$email->to('address@example.com', 'Name')
		->subject('Subject')
		->messagePlain('Plaintext message')
		->send();


Debugging
--------

Call method `debug(Mail_Postmark::DEBUG_VERBOSE)` or 
`debug(Mail_Postmark::DEBUG_RETURN)` to enable debug mode.
`DEBUG_VERBOSE` prints debug info and `DEBUG_RETURN` makes 
`send()` return debug info as an array.