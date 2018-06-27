<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

interface ResponseInterface
{
    /**
     * @return RequestInterface
     */
    public function getRequest();

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return string
     */
    public function getRawData();

    /**
     * @param string $name the header name
     * @return array|null Array of the header values, or null when header is not present in the response
     */
    public function getHeader($name);

    /**
     * Returns an associative array of the message's headers
     * Key - Header name
     * Value - array of header values. For example:.
     *
     * ```php
     * ['Location' => ['http://example.com'], 'Expires' => ['Thu, 01 Jan 1970 00:00:00 GMT']]
     * ```
     *
     * @return string[][] Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders();

    /**
     * @return string
     */
    public function getStatusCode();

    /**
     * @return static
     */
    public function getReasonPhrase();
}
