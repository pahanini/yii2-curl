<?php
/**
 * A cURL library with support for multiple requests
 *
 * @author      Pavel Tetyaev
 * @link        https://github.com/pahanini/yii2-curl
 */

namespace pahanini\curl;

use yii\base\InvalidConfigException;
use yii\base\Object;

/**
 * Response
 *
 * Represents an HTTP response.
 */
class Response extends Object
{
    /**
     * @var string keeps page content
     */
    private $_content;

    /**
     * @var null|[]
     */
    private $_headers;

    /**
     * @var \pahanini\curl\Request
     */
    private $_request;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!$this->_request) {
            throw new InvalidConfigException("Empty request");
        }
        $request = $this->getRequest();
        $headerSize = $request->getInfo(CURLINFO_HEADER_SIZE);
        $response = $request->getRawResponse();
        $this->_content = (strlen($response) === $headerSize) ? '' : substr($response, $headerSize);
        if ($headerSize) {
            $headers = rtrim(substr($response, 0, $headerSize));
            $headers = array_slice(preg_split('/(\\r?\\n)/', $headers), 1);
            foreach ($headers as $header) {
                $tmp = explode(': ', $header);
                if (count($tmp) == 2) {
                    $this->_headers[$tmp[0]] = $tmp[1];
                }
            }
        }
    }

    /**
     * Returns content
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Returns config with given name
     * @param $name
     * @param null $default
     * @return null
     */
    public function getHeader($name, $default = null)
    {
        return isset($this->_headers[$name]) ? $this->_headers[$name] : $default;
    }

    /**
     * Returns array of headers
     * @return null|[]
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @return \pahanini\curl\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return int Last http code
     */
    public function getStatusCode()
    {
        return $this->getRequest()->getInfo(CURLINFO_HTTP_CODE);
    }

    /**
     * @param \pahanini\curl\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->_headers = [];
        $this->_request = $request;
    }
}
