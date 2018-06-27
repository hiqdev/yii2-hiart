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

/**
 * Class RequestErrorException represents an error occurred during the request sending.
 */
class RequestErrorException extends Exception
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * RequestErrorException constructor.
     *
     * @param string $message
     * @param RequestInterface $request
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message, RequestInterface $request, $code = 0, \Exception $previous = null)
    {
        $this->request = $request;
        parent::__construct($message, $this->getErrorInfo(), $code, $previous);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->errorInfo['request'];
    }

    /**
     * @return array
     */
    protected function getErrorInfo()
    {
        $request = $this->getRequest();

        return [
            'method' => $request->getMethod(),
            'uri' => $request->getFullUri(),
            'body' => $request->getBody(),
        ];
    }
}
