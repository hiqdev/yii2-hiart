<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\rest;

use hiqdev\hiart\ResponseErrorException;
use hiqdev\hiart\ResponseInterface;

class Connection extends \hiqdev\hiart\AbstractConnection
{
    public $queryBuilderClass = QueryBuilder::class;

    public function checkResponse(ResponseInterface $response)
    {
        $code = $response->getStatusCode();
        if ($code >= 200 && $code < 300) {
            return;
        }

        throw new ResponseErrorException($response->getReasonPhrase(), [
            'request' => $response->getRequest()->getParts(),
            'response' => $response->getData(),
        ], $code);
    }
}
