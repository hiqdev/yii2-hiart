<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\curl;

use hiqdev\hiart\AbstractRequest;
use hiqdev\hiart\AbstractResponse;
use hiqdev\hiart\ResponseErrorException;

/**
 * Class Response represents response through cURL library.
 */
class Response extends AbstractResponse
{
    /**
     * @var string
     */
    protected $rawData;

    /**
     * @var array[]
     */
    protected $headers = [];

    /**
     * @var string
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $reasonPhrase;

    /**
     * Response constructor.
     *
     * @param AbstractRequest $request
     * @param string $rawBody the raw response, returned by `curl_exec()` method
     * @param array $info the cURL information, returned by `curl_getinfo()` method
     * @param string $error the cURL error message, if present. Empty string otherwise.
     * @param int $errorCode the cURL error code, if present. Integer `0` otherwise.
     * @throws ResponseErrorException
     */
    public function __construct(AbstractRequest $request, $rawBody, $info, $error, $errorCode)
    {
        $this->request = $request;

        $this->checkTransportError($error, $errorCode);

        $parsedResponse = $this->parseRawResponse($rawBody, $info);
        $this->headers = $parsedResponse['headers'];
        $this->statusCode = $parsedResponse['statusCode'];
        $this->rawData = $parsedResponse['data'];
        $this->reasonPhrase = $parsedResponse['reasonPhrase'];
    }

    /**
     * @return mixed|string
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param string $name the header name
     * @return array|null
     */
    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * Parses raw response and returns parsed information.
     *
     * @param string $data the raw response
     * @param array $info the curl information (result of `gurl_getinfo` call)
     * @return array array with the following keys will be returned:
     *  - data: string, response data;
     *  - headers: array, response headers;
     *  - statusCode: string, the response status-code;
     *  - reasonPhrase: string, the response reason phrase (OK, NOT FOUND, etc)
     */
    protected function parseRawResponse($data, $info)
    {
        $result = [];

        $headerSize = $info['header_size'];
        $result['data'] = substr($data, $headerSize);

        $rawHeaders = explode("\r\n", substr($data, 0, $headerSize));
        // First line is status-code HTTP/1.1 200 OK
        list(, $result['statusCode'], $result['reasonPhrase']) = explode(' ', array_shift($rawHeaders), 3);
        foreach ($rawHeaders as $line) {
            if ($line === '') {
                continue;
            }

            list($key, $value) = explode(': ', $line);
            $result['headers'][$key][] = $value;
        }

        return $result;
    }

    /**
     * Checks $error and $errorCode for transport errors.
     *
     * @param string $error the cURL error message, if present. Empty string otherwise.
     * @param int $errorCode the cURL error code, if present. Integer `0` otherwise.
     * @throws ResponseErrorException when the error is present
     */
    protected function checkTransportError($error, $errorCode)
    {
        if ($error !== '' || $errorCode !== 0) {
            throw new ResponseErrorException($error, $this, $errorCode);
        }
    }

    /**
     * Returns array of all headers.
     * Key - Header name
     * Value - array of header values. For example:.
     *
     * ```php
     * ['Location' => ['http://example.com'], 'Expires' => ['Thu, 01 Jan 1970 00:00:00 GMT']]
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
