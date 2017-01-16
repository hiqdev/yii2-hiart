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

use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;

/**
 * The Command class implements execution of query.
 */
class Command extends Component
{
    /**
     * @var Connection
     */
    public $db;

    /**
     * @var Query Query object
     */
    public $query;

    /**
     * @var string request method e.g. POST
     */
    protected $method;

    /**
     * @var string request url, without site
     */
    protected $url;

    /**
     * @var array request query vars (GET parameters)
     */
    protected $queryVars;

    /**
     * @var string request body vars (POST parameters)
     */
    protected $body;

    /**
     * @var bool do not decode request
     */
    protected $raw = false;

    /**
     * Sends a request to retrieve data.
     * In API this could be get, search or list request.
     * @param array $options
     * @throws ErrorResponseException
     * @return mixed
     */
    public function search($options = [])
    {
        return $this->makeRequest('search', $options);
    }

    /**
     * Sends a request to create/insert data.
     * @param mixed $from entity to create 
     * @param mixed $data attributes of object to create
     * @param mixed $options operation options
     * @return mixed
     */
    public function create($from, $data, array $options = [])
    {
        $this->query->from($from)->addParts($data);

        return $this->makeRequest('create', $options);
    }

    public function update($index, $data, $where, $options = [])
    {
        $options['id'] = $id;

        return $this->db->put($index . 'Update', array_merge($data, $options));

        return $this->makeRequest('update', $options);
    }

    public function get($modelName, $primaryKey, $options = [])
    {
        return $this->db->post($modelName . 'GetInfo', ArrayHelper::merge(['id' => $primaryKey], $options));
    }

    public function mget($index, $type, $ids, $options = [])
    {
        $body = Json::encode(['ids' => array_values($ids)]);

        return $this->db->post([$index, $type, '_mget'], $options, $body);
    }

    public function exists($index, $type, $id)
    {
        return $this->db->head([$index, $type, $id]);
    }

    public function delete($from, $id, $options = [])
    {
        $this->query->from($from)->where(['id' => $id]);

        return $this->makeRequest('delete', $options);
    }

    /**
     * Performs str
     * @param string $url URL
     * @param mixed $body request parameters
     * @return mixed
     */
    public function perform($url, $body = [])
    {
        return $this->db->post($url, [], $body);
        return $this->makeRequest($action, $options);
    }

    public function makeRequest($method, $action, array $options = [])
    {
        return $this->db->makeRequest(
            $this->buildMethod($action, $options),
            $this->buildUrl($action, $options),
            $this->buildQuery($action, $options),
            $this->buildBody($action, $options),
            $this->buildRaw($action, $options)
        );
    }

    public function buildUrl($action, array $options)
    {
        return $query->from . Inflector::id2camel($action);
    }

    public function getQueryVars($action, $options)
    {
    }

    public function getBody($action, $options)
    {
    }

    public function getRaw($action, $options)
    {
        return $this->raw;
    }
}
