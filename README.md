# audiomack-api-examples

Example code describing how to use the Audiomack API.

* Example 1: `php-auth` uses the `pear/http_oauth` composer package available from https://github.com/pear/HTTP_OAuth (PHP)
* Example 2: `php-risan` uses the `risan/oauth1` composer package available from https://github.com/risan/oauth1 (PHP)

## Authentication example

To run the authentication example on your development machine, you can do the following:

* `cd php-auth`
* `composer install`
* php -S localhost:3030
* Visit <a href="http://localhost:3030/">http://localhost:3030/</a> in your browser

## Known Issues
- Run `composer update` to avoid Depreciation errors
- add `set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ .'/vendor/pear/http_request2');` to config.php to avoid ``` Warning: require_once(HTTP/Request2.php): Failed to open stream: No such file or directory in``` error.
