Postmark PHP class changelog
============================

## 0.5

* Bumped version to 0.5

## 0.4.6

* Made PSR-0 compliant
* Added support for Composer
* Removed constants

## 0.4.5

* Internally uses constants for recipients
* Trim recipient addresses

## 0.4.4

* Quote address display names to support commas, etc: "Smith, Jane" <jane@smith.com>
* Added section "Getting started" and "E-mail address validation" to readme

## 0.4.3

* Fixed debug mode checking and added two test cases. Thanks John Beales (jbeales)

## 0.4.2

* Fixed PHPDOC comments

## 0.4.1

* Fixed a bug where sending errors would not show if debug mode was enabled
* Fixed certificate validation

## 0.4

* SSL with validation against certificate 
* Multiple To
* Validates all To, Cc and Bcc addresses
* Custom headers
* Fixed DEBUG_RETURN bug, thanks hdeshev
* Improved error handling
* Check if subject is set
* Configuration adapter
* Attachments
* Unit tests

## 0.3

* Added tag method to handle the Tag header (Jeff Downie - jeff@iwork.ca)
* Switched fully to markdown for docs

## 0.2

* Added method fromName() to easily override From name
* Added replyTo method to handle the Reply-To header
* Added debug modes
* Improved error handling with address validation etc
* Fixed comments


## 0.1.1

* Minor and mayor bug fixes.


## 0.1

* Initial