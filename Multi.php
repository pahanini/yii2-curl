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
     * @var int Stack size of requests
     */
    public $stackSize = 50;

    /**
     * @var array Keeps all this requests
     */
    private $_requests = array();

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
        $this->_requests[] = $request;
    }

    /**
     * Executes all added requests
     *
     * @throws \pahanini\curl\Exception
     */
    public function execute()
    {
        if (($stackSize = (int)$this->stackSize) <= 0) {
            throw new InvalidConfigException("stackSize param expected to be greater then zero");
        }

        if (count($this->_requests) < 1) {
            throw new InvalidCallException("No requests added");
        }

        $stacks = array_chunk($this->_requests, $stackSize);
        $multiHandle = curl_multi_init();

        foreach ($stacks as $requests) {
            foreach ($requests as $request) {
                if (($status = curl_multi_add_handle($multiHandle, $request->getHandle())) !== CURLM_OK) {
                    throw new Exception("Unable to add request to cURL multi handle ($status)");
                }
            }
            $active = null;
            do {
                $code = curl_multi_exec($multiHandle, $active);
            } while ($code == CURLM_CALL_MULTI_PERFORM);

            while ($active && $code == CURLM_OK) {
                if (curl_multi_select($multiHandle) === -1) {
                    usleep(100);
                }
                do {
                    $code = curl_multi_exec($multiHandle, $active);
                } while ($code == CURLM_CALL_MULTI_PERFORM);
            }
            foreach ($requests as $request) {
                $request->setRawResponse(curl_multi_getcontent($request->getHandle()));
                curl_multi_remove_handle($multiHandle, $request->getHandle());
            }
            if ($code !== CURLM_OK) {
                throw new Exception("Error executing multi request, exit code = " . $code);
            }
        }
        curl_multi_close($multiHandle);
    }

    /**
     * @return \pahanini\curl\Request[] Array of [[\pahanini\curl\Request]] attached
     */
    public function getRequests()
    {
        return $this->_requests;
    }
}
