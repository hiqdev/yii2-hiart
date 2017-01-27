<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\curl;

use hiqdev\hiart\AbstractRequest;
use hiqdev\hiart\RequestErrorException;
use yii\helpers\ArrayHelper;

/**
 * Class Request represents request using cURL library.
 */
class Request extends AbstractRequest
{
    protected $responseClass = Response::class;

    /**
     * @var array default cURL options
     */
    public $defaultOptions = [
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
    ];

    /**
     * @param array $options
     * @throws RequestErrorException
     * @return array|mixed
     */
    public function send($options = [])
    {
        try {
            $this->build();

            $curl = curl_init($this->getFullUri());
            curl_setopt_array($curl, $this->prepareCurlOptions($options));
            $response = curl_exec($curl);
            $info = curl_getinfo($curl);
            $error = curl_error($curl);
            $errorCode = curl_errno($curl);
            curl_close($curl);
        } catch (RequestErrorException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new RequestErrorException($e->getMessage(), $this, $e->getCode(), $e);
        }

        return new $this->responseClass($this, $response, $info, $error, $errorCode);
    }

    /**
     * @param array $options
     * @throws RequestErrorException
     * @return array
     */
    protected function prepareCurlOptions($options)
    {
        $requestOptions = $this->buildMethodOptions();
        $requestOptions[CURLOPT_HTTPHEADER] = $this->buildHeaderLines();

        if ($this->getVersion() === '1.1') {
            $requestOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        } elseif ($this->getVersion() === '1.0') {
            $requestOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;
        } else {
            throw new RequestErrorException('Request version "' . $this->getVersion() . '" is not support by cURL', $this);
        }

        return ArrayHelper::merge($this->defaultOptions, $this->getDb()->config, $requestOptions, $options);
    }

    /**
     * @return array
     */
    protected function buildMethodOptions()
    {
        $options = [];

        if ($this->getMethod() === 'GET') {
            return $options;
        }

        if (!empty($this->getBody())) {
            $options[CURLOPT_POSTFIELDS] = $this->getBody();
        }

        if ($this->getMethod() === 'POST') {
            $options[CURLOPT_POST] = true;
        } else {
            $options[CURLOPT_CUSTOMREQUEST] = $this->getMethod();
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function buildHeaderLines()
    {
        $result = [];

        foreach ($this->getHeaders() as $name => $values) {
            $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
            if (is_string($values)) {
                $values = [$values];
            }
            foreach ($values as $value) {
                $result[] = "$name: $value";
            }
        }

        return $result;
    }
}
