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
 * Class RequestErrorException represents an error occurred during the request sending
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
     * @param array $errorInfo
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(
        $message,
        RequestInterface $request,
        array $errorInfo = [],
        $code = 0,
        \Exception $previous = null
    ) {
        $this->request = $request;
        $errorInfo = array_merge($this->getDetailsArray(), $errorInfo);

        parent::__construct($message, $errorInfo, $code, $previous);
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
    protected function getDetailsArray()
    {
        $request = $this->getRequest();

        return [
            'method' => $request->getMethod(),
            'uri' => $request->getFullUri(),
            'body' => $request->getBody(),
        ];
    }
}
