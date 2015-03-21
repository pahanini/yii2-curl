# A Yii2 cURL library

A lightweight library with support for multiple requests.

[![Build Status](https://travis-ci.org/pahanini/yii2-curl.svg)](https://travis-ci.org/pahanini/yii2-curl)
[![Code Climate](https://codeclimate.com/github/pahanini/yii2-curl/badges/gpa.svg)](https://codeclimate.com/github/pahanini/yii2-curl)
[![Latest Stable Version](https://poser.pugx.org/pahanini/curl/v/stable.svg)](https://packagist.org/packages/pahanini/curl)
[![Latest Unstable Version](https://poser.pugx.org/pahanini/curl/v/unstable.svg)](https://packagist.org/packages/pahanini/curl)
[![License](https://poser.pugx.org/pahanini/curl/license.svg)](https://packagist.org/packages/pahanini/curl)
[![Total Downloads](https://poser.pugx.org/pahanini/curl/downloads.svg)](https://packagist.org/packages/pahanini/curl)

## Documentation

Send single request example:
```php
	$request = new Request();
	$request->url('http://google.com')->execute();

	$response = $request->getResponse();
	echo $response->statusCode; // displays 200
	echo $response->content; // displays content of the page
```


Send multi request example:
```php
	$request1 = new Request();
	$request1->url('http://google.com');

	$request2 = new Request();
	$request2->url('http://bing.com');

	$multi = new Multi();
	$multi->add($request1);
	$multi->add($request2);
	$multi->execute();
```

## Testing

- run `composer install`
- run `codecept build`
- run `codecept run`
