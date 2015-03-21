# A cURL library with support for multiple requests



[![Build Status](https://travis-ci.org/pahanini/yii2-curl.svg)](https://travis-ci.org/pahanini/yii2-curl)


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
