<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\proxy;

abstract class Response extends \hiqdev\hiart\AbstractResponse
{
    /**
     * @var object
     */
    protected $worker;

    public function __construct(Request $request, $worker)
    {
        $this->request = $request;
        $this->worker = $worker;
    }

    public function getWorker()
    {
        return $this->worker;
    }

    public function getHeader($name)
    {
        return $this->worker->getHeader($name);
    }
}
