<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\stream;

use hiqdev\hiart\AbstractRequest;
use yii\helpers\Inflector;

/**
 * PHP stream request implementation.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Request extends AbstractRequest
{
    protected $workerClass = RequestWorker::class;

    public $defaultOptions = [
        'http' => [
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer' => false,
        ],
    ];

    public function send($options = [])
    {
        $this->build();

        try {
            $context = stream_context_create($this->prepareContextOptions($options));
            $stream = fopen($this->getFullUri(), 'rb', false, $context);
            $responseContent = stream_get_contents($stream);
            // see http://php.net/manual/en/reserved.variables.httpresponseheader.php
            $responseHeaders = $http_response_header;
            fclose($stream);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return new $this->responseClass($this, $responseContent, $responseHeaders);
    }

    protected function prepareContextOptions($options)
    {
        $requestOptions = [
            'http' => [
                'method' => $this->method,
                'header' => $this->headers,
            ],
        ];

        if (isset($this->body)) {
            $requestOptions['http']['content'] = $this->body;
        }

        $dbOptions = $this->convertContextOptions($this->getDb()->requestOptions);
        $sendOptions = $this->convertContextOptions($options);

        return ArrayHelper::merge($this->defaultOptions, $dbOptions, $requestOptions, $sendOptions);
    }

    /**
     * Converts raw options to stream context options.
     * @param array $options raw options.
     * @return array stream context options.
     */
    protected function convertContextOptions(array $options)
    {
        $contextOptions = [];
        foreach ($options as $key => $value) {
            $section = 'http';
            if (strpos($key, 'ssl') === 0) {
                $section = 'ssl';
                $key = substr($key, 3);
            }
            $key = Inflector::underscore($key);
            $contextOptions[$section][$key] = $value;
        }

        return $contextOptions;
    }
}
