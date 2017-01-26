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
 * Class ResponseErrorException represent exception occurred during the response processing.
 */
class ResponseErrorException extends Exception
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * ResponseErrorException constructor
     *
     * @param string $message the error message
     * @param ResponseInterface $response
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message, ResponseInterface $response, $code = 0, \Exception $previous = null) {
        $this->response = $response;
        parent::__construct($message, $this->getErrorInfo(), $code, $previous);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->response->getRequest();
    }

    /**
     * @return mixed
     */
    public function getResponseData()
    {
        return $this->getResponse()->getData();
    }

    /**
     * @return array
     */
    protected function getErrorInfo()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        return [
            'statusCode' => $response->getStatusCode(),
            'responseData' => $response->getData(),
            'request' => [
                'method' => $request->getMethod(),
                'uri' => $request->getFullUri(),
                'body' => $request->getBody(),
            ],
        ];
    }
}
