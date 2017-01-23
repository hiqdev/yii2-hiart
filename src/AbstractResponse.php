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

use yii\helpers\Json;

abstract class AbstractResponse implements ResponseInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var mixed response data
     */
    protected $data;

    protected $isDecoded = false;

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
        $data = $this->getRawData();
        if (!$this->isRaw() && $this->isJson()) {
            $data = Json::decode($data);
        }

        return $data;
    }

    abstract public function getRawData();

    public function isRaw()
    {
        return $this->request->isRaw();
    }

    public function isJson()
    {
        return !empty(preg_grep('|application/json|i', $this->getHeader('Content-Type')));
    }

    abstract public function getHeader($name);
}
