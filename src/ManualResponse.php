<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\curl;

use hiqdev\hiart\AbstractRequest;
use hiqdev\hiart\AbstractResponse;

/**
 * For creating response manually.
 */
class ManualResponse extends AbstractResponse
{
    /**
     * @var mixed
     */
    protected $data;

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
     * @param AbstractRequest $request
     * @param mixed $data
     * @param array $headers
     * @param string $statusCode
     * @param string $reasonPhrase
     */
    public function __construct(RequestInterface $request, $data, array $headers = [], $statusCode = null, $reasonPhrase = null)
    {
        $this->request = $request;
        $this->data = $data;
        $this->headers = $headers;
        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->data;
    }

    /**
     * @param string $name the header name
     * @return array|null
     */
    public function getHeader($name)
    {
        return isset($this->headers[strtolower($name)]) ? $this->headers[strtolower($name)] : null;
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
