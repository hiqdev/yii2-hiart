<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

class ResponseDecodingException extends ResponseErrorException
{
    /**
     * @return mixed
     */
    public function getName()
    {
        return 'Failed to decode the response';
    }

    /**
     * {@inheritdoc}
     */
    protected function getErrorInfo()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        return [
            'statusCode' => $response->getStatusCode(),
            'data' => $response->getRawData(),
            'headers' => $response->getHeaders(),
            'request' => [
                'method' => $request->getMethod(),
                'uri' => $request->getFullUri(),
                'body' => $request->getBody(),
            ],
        ];
    }
}
