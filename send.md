# Sending request

Request object can be send with two ways:

- single request
- multi request (in parallel)


## Single request

It's simple

```php
	
	$request = new Request();
	// set required options here
	
	$response = $request->send();
	// or
	$response = $request->execute()->getResponse();
```

You also can get raw response using `getRawResponse()` method.


## Send many requests in parallel

```php
	$request1 = new Request();
	$request2 = new Request();
	// set required options here
	
	$multi = new Multi();
	$multi->stackSize = 100; // all requests are stacked, set stack size here!
	$multi->add($request1);
	$multi->add($request2);
	$multi->execute();
	
	// getting results
	$response1 = $request1->getResponse();
	$response2 = $request2->getResponse();
```

[How to get response](response.md)


