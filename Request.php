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
 * Request
 *
 * This class acts as a wrapper around cURL functions.
 */
class Request extends Object
{
    /**
     * @var null|string Request url
     */
    public $url;

    /**
     * @var boolean Whether this request has been executed
     */
    private $_is_executed = false;

    /**
     * @var resource The cURL resource attached.
     */
    private $_handle;

    /**
     * @var array
     */
    private $_options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
    ];

    /**
     * Response object.
     *
     * @var Response|null
     */
    private $_response;

    /**
     * @var mixed Raw response
     */
    private $_rawResponse;

    /**
     * Shutdown sequence.
     * @return void
     */
    public function __destruct()
    {
        if (is_resource($this->_handle)) {
            curl_close($this->_handle);
        }
    }

    /**
     * @return string|null Retrieve the latest error.
     */
    public function getErrorMessage()
    {
        if ($this->_handle) {
            $error = curl_error($this->_handle);
        }
        return (isset($error) && $error !== '') ?  $error : null;
    }

    /**
     * Retrieve the cURL handle
     *
     * @throws InvalidConfigException
     * @return resource
     */
    public function getHandle()
    {
        if ($this->_handle === null) {
            if (empty($this->url)) {
                throw new InvalidConfigException("Url must not be empty");
            }
            $this->_handle = curl_init($this->url);
        }
        return $this->_handle;
    }

    /**
     * Get information regarding the request.
     *
     * Wrapper around `curl_getinfo` php function.
     *
     * @link http://php.net/manual/ru/function.curl-getinfo.php
     * @param  int $opt Default 0
     * @return mixed
     */
    public function getInfo($opt = 0)
    {
        return curl_getinfo($this->_handle, $opt);
    }

    /**
     * Returns option with given name
     *
     * @param string $name Name of the option
     * @param string $default Default value
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        return isset($this->_options[$name]) ? $this->_options[$name] : $default;
    }

    /**
     * Returns options array
     *
     * @return string
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Get the raw response.
     *
     * @return string
     */
    public function getRawResponse()
    {
        return $this->_rawResponse;
    }

    /**
     * Get the response
     *
     * @return array
     */
    public function getResponse()
    {
        if (!$this->_response) {
            return $this->_response = new Response(['request' => $this]);
        }
    }

    /**
     * Sets an option for the request
     *
     * On attempt to set CURLOPT_URL option function will change current [[$url]].
     * So next call of [[getOptions]] will return array without CURLOPT_URL key.
     *
     * @param [] $options
     */
    public function setOption($option, $value)
    {
        $this->setOptions([$option => $value]);
    }

    /**
     * Sets an options for the request in massive way
     *
     * On attempt to set CURLOPT_URL option function will change current [[$url]] and remove CURLOPT_URL
     * from array. So next call of [[getOptions]] will return array without CURLOPT_URL key.
     *
     * @param [] $options
     */
    public function setOptions(array $options)
    {
        if (isset($options[CURLOPT_URL])) {
            $this->url = $options[CURLOPT_URL];
            unset($options[CURLOPT_URL]);
        }
        $this->_options = $options + $this->_options;
    }

    /**
     * Sets a raw response
     *
     * @param string $value
     * @param boolean $isExecuted If change is_executed flag
     */
    public function setRawResponse($value, $isExecuted = true)
    {
        $this->_rawResponse = $value;
        $this->_is_executed = $isExecuted;
    }

    /**
     * Executes the request
     *
     * @throws Exception
     * @throws InvalidConfigException
     * @return \pahanini\curl\Request
     */
    public function execute()
    {
        $handle = $this->getHandle();
        curl_setopt_array($handle, $this->_options);
        if (($this->_rawResponse  = curl_exec($handle)) === false) {
            throw new Exception($this->getErrorMessage());
        }
        $this->_is_executed = true;
        return $this;
    }

    /**
     * Whether the request has been executed
     *
     * @return boolean
     */
    public function isExecuted()
    {
        return $this->_is_executed;
    }

    /**
     * Whether the request was successful.
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return ($this->getErrorMessage() === null) ? true : false;
    }

    /**
     * Sets url in chain style
     */
    public function url($value)
    {
        $this->url = $value;
        return $this;
    }
}
