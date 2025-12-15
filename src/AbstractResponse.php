<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
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
     * @var string response data. The property contains RAW response data
     * @see decodeData()
     * @see isDecoded
     */
    protected $data;

    /**
     * @var bool whether response is already decoded
     */
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

        if ($this->isRaw()) {
            return $data;
        }

        if ($this->isJson()) {
            return $this->decodeJson($data);
        }

        // TODO: implement decoding for XML and other types

        // throw new ResponseDecodingException('Failed to detect response data type', $this);
        // TODO: throw exception instead of returning
        return $data;
    }

    /**
     * Method returns RAW request data.
     *
     * @return string
     */
    abstract public function getRawData();

    /**
     * Whether the request is RAW and should not be decoded.
     * @return bool
     */
    public function isRaw()
    {
        return $this->request->isRaw();
    }

    /**
     * Method checks whether response is a JSON response.
     * @return bool
     */
    public function isJson()
    {
        $value = $this->getHeader('Content-Type');
        if ($value === null) {
            return false;
        }

        return !empty(preg_grep('|application/json|i', $value));
    }

    /**
     * @param $name
     * @return array
     */
    abstract public function getHeader($name);

    protected function decodeJson(string $data)
    {
        return Json::decode($data);
    }
}
