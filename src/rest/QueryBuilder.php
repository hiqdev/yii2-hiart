<?php

namespace hiqdev\hiart\rest;

class QueryBuilder extends AbstractQueryBuilder
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
