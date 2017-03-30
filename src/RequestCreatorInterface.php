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

interface RequestCreatorInterface
{
    /**
     * @param QueryBuilderInterface $builder
     * @param Query $query
     */
    public function __construct(QueryBuilderInterface $builder, Query $query);

    /**
     * Creates request.
     * @return RequestInterface
     */
    public function createRequest();
}
