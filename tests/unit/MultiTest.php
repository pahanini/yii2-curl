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
        $requestUserAgent = new Request(['url' => 'http://httpbin.org/user-agent']);
        $multi->add($requestIp);
        $multi->add($requestUserAgent);
        $multi->execute();

        $this->assertEquals(200, $requestIp->getResponse()->statusCode);
        $this->assertEquals(200, $requestUserAgent->getResponse()->statusCode);
    }
}
