<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
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
     * @return string
     */
    public function getStatusCode();

    /**
     * @return static
     */
    public function getReasonPhrase();
}
