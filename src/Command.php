<?php

/*
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * The Command class implements the API for accessing REST API.
 */
class Command extends Component
{
    /**
     * @var Connection
     */
    public $db;
    /**
     * @var string|array the indexes to execute the query on. Defaults to null meaning all indexes
     */
    public $index;
    /**
     * @var string|array the types to execute the query on. Defaults to null meaning all types
     */
    public $type;
    /**
     * @var array list of arrays or json strings that become parts of a query
     */
    public $queryParts;
    public $options = [];

    /**
     * Sends a request to the _search API and returns the result.
     *
     * @param array $options
     *
     * @throws ErrorResponseException
     *
     * @return mixed
     */
    public function search($options = [])
    {
        $query = $this->queryParts;

        $options = array_merge($query, $options);
        $url     = $this->index . ArrayHelper::remove($options, 'scenario', 'Search');

        $result = $this->db->post($url, $options);

        return $result;
    }

    public function getList($options = [])
    {
        $options = array_merge($this->queryParts, $options);
        $command = $this->index . 'GetList';
        $result  = $this->db->post($command, $options);

        return $result;
    }

    public function insert($action, $data, $id = null, $options = [])
    {
        $options = array_merge($data, $options);

        if ($id !== null) {
            return $this->db->put($action . 'Update', array_merge($options, ['id' => $id]));
        } else {
            return $this->db->post($action . 'Create', $options);
        }
    }

    public function get($modelName, $primaryKey, $options)
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

    public function delete($index, $id, $options = [])
    {
        return $this->db->delete($index . 'Delete', array_merge($options, ['id' => $id]));
    }

    public function update($index, $id, $data, $options = [])
    {
        $options['id'] = $id;

        return $this->db->put($index . 'Update', array_merge($data, $options));
    }

    /**
     * @param $action
     * @param array $options
     *
     * @return mixed
     */
    public function perform($action, $options = [])
    {
        return $this->db->post($action, $options);
    }
}
