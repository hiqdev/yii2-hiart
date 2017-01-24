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

/**
 * Request error exception.
 *
 * For exceptions during request sending.
 */
class RequestErrorException extends Exception
{
    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->errorInfo['request'];
    }
}
