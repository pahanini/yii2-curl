# Request

Request in a simple wrapper around curl. Create new request:

```php
	$request = new Request();
```

Request has two default options `CURLOPT_RETURNTRANSFER = true` and 
`CURLOPT_HEADER = true`. Changing options examples: 

```php
	// single option
	$request->setOption(CURLOPT_TIMEOUT_MS, 100);
	
	// in a massive way
	$request->setOptions(
		[
			CURLOPT_TIMEOUT_MS => 200,
			CURLOPT_REFERER => 'http://github.com',	
		]
	);
	
	// getting option
	$request->getOption(CURLOPT_REFERER);	// returns 'http://github.com'
```

Request url is a simple option. Changing url example:

```php
	$url = 'http://example.com';
	
	// using setOption
	$request->setOption(CURLOPT_URL_MS, $url);
	
	// in chain style
	$request->url($url)->execute();
```

[How to execute request](send.md)
