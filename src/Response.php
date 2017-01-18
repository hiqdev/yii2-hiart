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

use Psr\Http\Message\ResponseInterface;
use yii\helpers\Json;

class Response
{
    /**
     * @var ResponseInterface
     */
    protected $worker;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var mixed response data
     */
    protected $data;

    public function __construct(ResponseInterface $worker, Request $request)
    {
        $this->worker = $worker;
        $this->request = $request;
        $this->init();
    }

    public function getWorker()
    {
        return $this->worker;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getData()
    {
        return $this->data;
    }

    public function init()
    {
        $this->data = $this->getBodyContents();
        if (!$this->isRaw() && $this->isJson()) {
            $this->data = Json::decode($this->data);
        }
    }

    public function isRaw()
    {
        return $this->request->isRaw();
    }

    public function isJson()
    {
        return preg_grep('|application/json|i', $this->getHeader('Content-Type'));
    }

    public function getHeader($name)
    {
        return $this->worker->getHeader($name);
    }

    public function getBodyContents()
    {
        return $this->worker->getBody()->getContents();
    }

}
