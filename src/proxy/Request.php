<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\proxy;

abstract class Request extends \hiqdev\hiart\AbstractRequest
{
    /**
     * @var object
     */
    protected $worker;

    abstract protected function createWorker();

    public function send($options = [])
    {
        $responseWorker = $this->getHandler()->send($this->getWorker(), $options);

        return new $this->responseClass($this, $responseWorker);
    }

    /**
     * @return Worker
     */
    public function getWorker()
    {
        if ($this->worker === null) {
            $this->build();
            $this->worker = $this->createWorker();
        }

        return $this->worker;
    }
}
