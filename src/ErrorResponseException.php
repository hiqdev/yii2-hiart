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
 * Class ErrorResponseException.
 */
class ErrorResponseException extends Exception
{
    /**
     * @var array The API response
     */
    public $response;

    public function __construct($message, array $errorInfo = [], $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $errorInfo, $code, $previous);
        $this->response = $this->errorInfo['response'];
    }
}
