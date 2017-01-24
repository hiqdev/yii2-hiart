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
 * Response error exception.
 *
 * For exceptions during response processing.
 */
class ResponseErrorException extends Exception
{
    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->errorInfo['response'];
    }
}
