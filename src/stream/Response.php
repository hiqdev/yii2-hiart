<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\stream;

use hiqdev\hiart\AbstractResponse;

/**
 * PHP stream response implementation.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Response extends AbstractResponse
{
    protected $rawData;

    protected $headers;

    protected $statusCode;

    protected $reasonPhrase;

    public function __construct(Request $request, $rawData, array $rawHeaders)
    {
        $this->request = $request;
        $this->rawData = $rawData;
        $this->headers = $this->parseHeaders($rawHeaders);
    }

    public function getRawData()
    {
        return $this->rawData;
    }

    public function getHeader($name)
    {
        $name = strtolower($name);

        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    public function parseHeaders($headers)
    {
        foreach ($headers as $header) {
            if (strncmp($header, 'HTTP/', 5) === 0) {
                $parts = explode(' ', $header, 3);
                $this->version = substr($parts[0], 6);
                $this->statusCode = $parts[1];
                $this->reasonPhrase = $parts[2];
            } elseif (($pos = strpos($header, ':')) !== false) {
                $name = strtolower(trim(substr($header, 0, $pos)));
                $value = trim(substr($header, $pos + 1));
                $result[$name][] = $value;
            } else {
                $result['raw'][] = $header;
            }
        }

        return $result;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }
}
