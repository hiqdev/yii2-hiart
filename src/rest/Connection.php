<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\rest;

use hiqdev\hiart\ResponseInterface;

class Connection extends \hiqdev\hiart\AbstractConnection
{
    public $queryBuilderClass = QueryBuilder::class;

    /**
     * Method checks whether the response is an error.
     *
     * @param ResponseInterface $response
     * @return false|string the error text or boolean `false`, when the response is not an error
     */
    public function getResponseError(ResponseInterface $response)
    {
        $code = $response->getStatusCode();
        if ($code >= 200 && $code < 300) {
            return false;
        }

        return $response->getReasonPhrase();
    }
}
