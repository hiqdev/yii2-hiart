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

    public function __construct(Request $request, $rawData, array $headers)
    {
        $this->request = $request;
        $this->rawData = $rawData;
        $this->headers = $headers;
    }

    public function getRawData()
    {
        return $this->rawData;
    }

    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }
}
