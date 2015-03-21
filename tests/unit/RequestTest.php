<?php

namespace tests\unit;

use pahanini\curl\Request;
use Yii;

class RequestTest extends \PHPUnit_Framework_TestCase
{
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
}
