<?php

namespace tests\unit;

use pahanini\curl\Request;
use Yii;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testClone()
    {
        $request1 = new Request();
        $request1->url = 'http://httpbin.org';
        $request2 = clone $request1;
        $this->assertFalse($request1->getHandle() === $request2->getHandle());
        $this->assertTrue($request1->url == $request2->url);
    }

    public function testOptions()
    {
        $request = new Request();
        $request->url('http://httpbin.org');
        $this->assertEquals('http://httpbin.org', $request->url);
        $request->setOptions([
           CURLOPT_REFERER => 'http://github.com'
        ]);
        $this->assertEquals('http://httpbin.org', $request->url);
        $this->assertEquals('http://github.com', $request->getOption(CURLOPT_REFERER));
    }

    public function testRawResponse()
    {
        $request = new Request([
            'url' => 'http://httpbin.org/user-agent',
            'options' => [
                CURLOPT_USERAGENT => 'Tester',
                CURLOPT_HEADER => 0,
            ]
        ]);
        $content = json_decode($request->execute()->getRawResponse(), true);
        $this->assertTrue($request->isExecuted());
        $this->assertNull ($request->getErrorMessage());
        $this->assertTrue($request->isSuccessful());
        $this->assertEquals('Tester', $content['user-agent']);
    }

    public function testResponse()
    {
        $request = new Request([
            'url' => 'http://httpbin.org/user-agent',
            'options' => [
                CURLOPT_USERAGENT => 'Tester',
            ]
        ]);
        $response = $request->execute()->getResponse();
        $content = json_decode($response->content, true);
        $this->assertTrue($request->isExecuted());
        $this->assertNull ($request->getErrorMessage());
        $this->assertTrue($request->isSuccessful());
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Tester', $content['user-agent']);
    }

    public function testUrlOptions()
    {
        $url = 'http://httpbin.org/';
        $request = new Request();
        $request->setOption(CURLOPT_URL, $url);
        $this->assertEquals($url, $request->url);
    }
}
