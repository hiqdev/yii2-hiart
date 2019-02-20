<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\proxy;

use GuzzleHttp\Exception\BadResponseException;

abstract class Request extends \hiqdev\hiart\AbstractRequest
{
    /**
     * @var object
     */
    protected $worker;

    abstract protected function createWorker();

    public function send($options = [])
    {
        try {
            $responseWorker = $this->getHandler()->send($this->getWorker(), $options);
        } catch (BadResponseException $exception) {
            $responseWorker = $exception->getResponse();
        }

        return new $this->responseClass($this, $responseWorker);
    }

    /**
     * @return object Worker
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
