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

use GuzzleHttp\Psr7\Request as Worker;

class Request
{
    /**
     * @var Worker
     */
    protected $worker;

    /**
     * @var Query
     */
    protected $query;

    public function __construct(Worker $worker, Query $query)
    {
        $this->worker = $worker;
        $this->query = $query;
    }

    public function getWorker()
    {
        return $this->worker;
    }

    public function getQuery()
    {
        return $this->query;
    }
    public static function fromData(array $data)
    {
        $uri = $data['Uri'];

        $params = $data['QueryParams'];
        if (is_array($params)) {
            $params = http_build_query($params, '', '&');
        }
        if (!empty($params)) {
            $uri .= '?' . $params;
        }

        $headers = $data['headers'];
        $body = $data['Body'];

        $formParams = $data['FormParams'];
        if (is_array($formParams)) {
            $body = http_build_query($formParams, '', '&');
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        $version = empty($data['ProtocolVersion']) ? '1.1' : $data['ProtocolVersion'];

        $request = new Worker($data['Method'], $uri, $headers, $body, $version);

        return new static($request, $data['query']);
    }

    public function getProfile()
    {
        /// TODO serialize request object for profile
        $request = $this->worker;

        return $request->getMethod() . ' ' . $request->getUri() . '#' . $request->getBody();
    }

    public function isRaw()
    {
        return !empty($this->query->options['raw']);
    }
}
