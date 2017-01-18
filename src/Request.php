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

use GuzzleHttp\Psr7\ServerRequest;

class Request
{
    /**
     * @var ServerRequest
     */
    protected $worker;

    /**
     * @var Query
     */
    protected $query;

    public function __construct(ServerRequest $worker, Query $query)
    {
        $this->worker = $worker;
        $this->query = $query;
    }

    public function getWorker()
    {
        return $this->worker;
    }

    public static function fromData(array $data)
    {
        $request = new ServerRequest($data['Method'], $data['Uri']);

        if (!empty($data['Headers'])) {
            foreach ($data['Headers'] as $header => $value) {
                $request = $request->withHeader($header, $value);
            }
        }

        foreach (['ProtocolVersion', 'UploadedFiles', 'CookieParams', 'QueryParams', 'ParsedBody'] as $name) {
            $value = $data[$name];
            if (!empty($value)) {
                $request = $request->{'with' . $name}($value);
            }
        }

        return new static($request, $data['query']);
    }

    public function getProfile()
    {
        /// TODO serialize request object for profile
        $request = $this->worker;
        $body = array_merge($request->getParsedBody(), $request->getQueryParams());

        return $request->getMethod() . ' ' . $request->getUri() . '#' . http_build_query($body);
    }

    public function isRaw()
    {
        return !empty($this->query->options['raw']);
    }
}
