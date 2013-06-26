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
* beaudesigns
* Gabriel Bull

## Requirements

All in-data must be encoded with UTF-8.


Getting started
---------------

```php
// Create a "server" in your "rack", then copy it's API key
$postmarkApiKey = 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx';
    
// Create a message and send it
Postmark\Mail::compose($postmarkApiKey)
    ->from('john@smith.com', 'John Smith')
    ->addTo('jane@smith.com', 'Jane Smith')
    ->subject('Subject')
    ->messagePlain('Plaintext message')
    ->send();
```


Configuration
-------------

There are two ways of configuration.

### Adapter

An adapter class should be used for a more dynamic configuration.
The adapter must implement Postmark\MailAdapterInterface. These
are the methods that must be implemented.

* `getApiKey` - Should return the API key
* `setupDefaults(Postmark\Mail &$mail)` - May be used to setup
  a default email, e.g. set From address.
* `log($logData)` - Is called immediately after the email is sent.
  `$logdata` is an array with keys `messageData`, `return`,
  `curlError` and `httpCode`.
  
See `Tests/Adapter.php` for example usage.


Usage
-----

```php
Postmark\Mail::compose($postmarkApiKey)
	->from('address@example.com', 'Name')
	->addTo('address@example.com', 'Name')
	->subject('Subject')
	->messagePlain('Plaintext message')
	->send();
```

or:

```php
$email = new Postmark\Mail($postmarkApiKey);
$email->from('address@example.com', 'Name')
	->addTo('address@example.com', 'Name')
	->subject('Subject')
	->messagePlain('Plaintext message')
	->send();
```


Error handling
--------------

See PHPDOC for details on Exceptions thrown. If no API key
is set, an E_USER_ERROR will be raised.


Debugging
---------

Call method `debug(Postmark\Mail::DEBUG_VERBOSE)` or 
`debug(Postmark\Mail::DEBUG_RETURN)` to enable debug mode.
`DEBUG_VERBOSE` prints debug info and `DEBUG_RETURN` makes 
`send()` return debug info as an array.


Unit tests
----------

Unit tests are located in `Tests/`. Simple test is the unit test framework being used.

`Adapter.php` runs all tests relevant for adapter configuration.


E-mail address validation
-------------------------

This class uses a regular expression to validate e-mail addresses, in addition to the
validation Postmark does. This regex isn't perfect. If you need more extensive validation,
please try http://www.dominicsayers.com/isemail/.
