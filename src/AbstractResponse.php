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

    protected $isDecoded = false;

    public function __construct(ResponseInterface $worker, Request $request)
    {
        $this->worker = $worker;
        $this->request = $request;
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
        if (!$this->isDecoded) {
            $this->data = $this->decodeData();
            $this->isDecoded = true;
        }

        return $this->data;
    }

    public function decodeData()
    {
        $data = $this->getBodyContents();
        if (!$this->isRaw() && $this->isJson()) {
            $data = Json::decode($data);
        }

        return $data;
    }

    public function getBodyContents()
    {
        return $this->worker->getBody()->getContents();
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
}
