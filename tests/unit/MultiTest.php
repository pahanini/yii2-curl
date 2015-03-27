<?php

namespace tests\unit;

use pahanini\curl\Multi;
use pahanini\curl\Request;
use Yii;

class MultiTest extends \PHPUnit_Framework_TestCase
{
    public function testRawResponse()
    {
        $multi = new Multi();
        $requestIp = new Request(['url' => 'http://httpbin.org/ip']);
        $requestIp->setOption(CURLOPT_REFERER, 'http://github.com');
        $requestUserAgent = new Request(['url' => 'http://httpbin.org/user-agent']);
        $multi->add($requestIp);
        $multi->add($requestUserAgent);
        $multi->execute();

        $this->assertEquals(2, count($multi->getRequests()));
        $this->assertEquals(200, $requestIp->getResponse()->statusCode);
        $this->assertEquals('http://github.com', $requestIp->getOption(CURLOPT_REFERER));
        $this->assertEquals(200, $requestUserAgent->getResponse()->statusCode);
    }

    public function testException()
    {
        $request1 = new Request();
        $request1->setOptions([
            CURLOPT_URL => "http://httpbin.org",
        ]);
        $request2 = new Request();
        $request2->setOptions([
            CURLOPT_CONNECTTIMEOUT_MS => 1,
            CURLOPT_TIMEOUT_MS => 1,
            CURLOPT_URL => "http://httpbin.org/delay/3",
        ]);
        $multi = new Multi();
        $multi->add($request1);
        $multi->add($request2);
        $multi->execute();
        $this->assertEquals(200, $request1->response->statusCode);
        $this->assertEquals(0, $request2->response->statusCode);
    }
}
