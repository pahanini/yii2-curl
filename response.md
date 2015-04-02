# Response

Working with response examples:

```php
	$response = $request->execute()->getResponse();
	
	// getting statusCode 
	$response->getStatusCode();
	
	// getting content 
	$response->getContent();
	
	// returns array of headers 
	$response->getHeaders(); 
	
	// outputs 'Content-Type' header value
	$response->getHeader('Content-Type');
	$response->getHeader('Content-Type', 'default value');
```

Replacing default response class:
 
```php
	$request->responseConfig = ['class' => '\my\Class'];
	$response = $request->execute()->getResponse();

	$response->doSomethingAmazing();
```
 