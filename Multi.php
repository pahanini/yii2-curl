<?php
/**
 * A cURL library with support for multiple requests
 *
 * @author      Pavel Tetyaev
 * @link        https://github.com/pahanini/yii2-curl
 */

namespace pahanini\curl;

use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Object;

/**
 * This class acts as a wrapper around cURL multi functions
 */
class Multi extends Object
{
    /**
     * @var array All requests
     */
    protected $requests = array();

    /**
     * @var int Stack size of requests
     */
    public $stackSize = 50;

    /**
     * @var resource The cURL resource attached.
     */
    private $_handle;

    /**
     * Adds a Request
     *
     * @param \pahanini\curl\Request $request
     */
    public function add(Request $request)
    {
        if ($request->isExecuted()) {
            throw new InvalidParamException("Can not add executed request");
        }
        $this->requests[] = $request;
    }

    /**
     * Executes all added requests
     */
    public function execute()
    {
        if (($stackSize = (int)$this->stackSize) <= 0) {
            throw new InvalidConfigException("stackSize param expected to be greater then zero");
        }

        if (count($this->requests) < 1) {
            throw new InvalidCallException("No requests added");
        }

        $stacks = array_chunk($this->requests, $stackSize);
        $multiHandle = curl_multi_init();

        foreach ($stacks as $requests) {
            foreach ($requests as $request) {
                if (($status = curl_multi_add_handle($multiHandle, $request->getHandle())) !== CURLM_OK) {
                    throw new Exception("Unable to add request to cURL multi handle ($status)");
                }
            }
            $active = null;
            do {
                curl_multi_exec($multiHandle, $active);
            } while ($active);

            foreach ($requests as $request) {
                $request->setRawResponse(curl_multi_getcontent($request->getHandle()));
                curl_multi_remove_handle($multiHandle, $request->getHandle());
            }
        }
        curl_multi_close($multiHandle);
    }
}
