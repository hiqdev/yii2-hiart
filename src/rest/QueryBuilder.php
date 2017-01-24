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

use hiqdev\hiart\Query;

class QueryBuilder extends \hiqdev\hiart\AbstractQueryBuilder
{
    /**
     * This function is for you to provide your authentication.
     * @param Query $query
     */
    public function buildAuth(Query $query)
    {
    }

    public function buildMethod(Query $query)
    {
        static $defaultMethods = [
            'get'       => 'GET',
            'put'       => 'PUT',
            'head'      => 'HEAD',
            'post'      => 'GET',
            'search'    => 'GET',
            'insert'    => 'POST',
            'update'    => 'PUT',
            'delete'    => 'DELETE',
        ];

        return isset($defaultMethods[$query->action]) ? $defaultMethods[$query->action] : 'POST';
    }

    public function buildUri(Query $query)
    {
        return $query->from;
    }

    public function buildHeaders(Query $query)
    {
        return [];
    }

    public function buildProtocolVersion(Query $query)
    {
        return null;
    }

    public function buildQueryParams(Query $query)
    {
        return [];
    }

    public function buildFormParams(Query $query)
    {
        return [];
    }

    public function buildBody(Query $query)
    {
        return null;
    }
}
