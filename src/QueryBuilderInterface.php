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
 * QueryBuilder interface.
 *
 * QueryBuilder builds a request from the specification given as a [[Query]] object.
 */
interface QueryBuilderInterface
{
    public function buildAuth(Query $query);

    public function buildMethod(Query $query);

    public function buildUri(Query $query);

    public function buildHeaders(Query $query);

    /**
     * Builds transport protocol version.
     * @param Query $query
     * @return string
     */
    public function buildProtocolVersion(Query $query);

    public function buildQueryParams(Query $query);

    public function buildFormParams(Query $query);

    public function buildBody(Query $query);
}
