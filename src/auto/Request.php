<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\auto;

use hiqdev\hiart\Query;
use hiqdev\hiart\QueryBuilderInterface;
use hiqdev\hiart\RequestCreatorInterface;
use hiqdev\hiart\RequestErrorException;

/**
 * Auto Request.
 * Detects best available transport in the system.
 */
class Request implements RequestCreatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(QueryBuilderInterface $builder, Query $query)
    {
        $this->builder = $builder;
        $this->query = $query;
    }

    protected $detectedClass;

    /**
     * {@inheritdoc}
     */
    public function createRequest()
    {
        if ($this->detectedClass === null) {
            $this->detectedClass = $this->detectClass();
        }

        return new $this->detectedClass($this->builder, $this->query);
    }

    public $tryClasses = [
        \hiqdev\hiart\guzzle\Request::class,
        \hiqdev\hiart\httpclient\Request::class,
        \hiqdev\hiart\curl\Request::class,
        \hiqdev\hiart\stream\Request::class,
    ];

    public function detectClass()
    {
        foreach ($this->tryClasses as $class) {
            if (class_exists($class) && $class::isSupported()) {
                return $class;
            }
        }

        throw new RequestErrorException('could not auto detect Request class');
    }
}
