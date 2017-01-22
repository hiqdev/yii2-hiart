<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

abstract class ProxyRequest extends AbstractRequest
{
    /**
     * @var object
     */
    protected $worker;

    /**
     * @var string transport implementation to be specified in concrete implementation.
     */
    public $handlerClass;

    abstract protected function createWorker();

    public function send($options = [])
    {
        $handler = $this->builder->getHandler();
        $worker = $handler->send($this->getWorker(), $options);

        return new $this->responseClass($worker, $request);
    }

    /**
     * @return Worker
     */
    public function getWorker()
    {
        if ($this->worker === null) {
            $this->buildRequest();
            $this->worker = $this->createWorker();
        }

        return $this->worker;
    }
}
