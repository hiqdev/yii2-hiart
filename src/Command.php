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

use Yii;

/**
 * The Command class implements execution of request.
 */
class Command extends \yii\base\Component
{
    /**
     * @var Connection
     */
    public $db;

    /**
     * @var Request request object
     */
    protected $request;

    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Sends a request to retrieve data.
     * In API this could be get, search or list request.
     * @throws ErrorResponseException
     * @return mixed
     */
    public function search()
    {
        $this->request->getQuery()->addAction('search');

        return $this->execute();
    }

    /**
     * Sends a request to create/insert data.
     * @param mixed $table entity to create
     * @param mixed $columns attributes of object to create
     * @return mixed
     */
    public function insert($table, $columns, array $options = [])
    {
        $request = $this->db->getQueryBuilder()->insert($table, $columns, $options);

        return $this->setRequest($request);
    }

    public function update($table, $columns, $condition = [], array $options = [])
    {
        $request = $this->db->getQueryBuilder()->update($table, $columns, $condition, $options);

        return $this->setRequest($request);
    }

    public function delete($table, $condition, array $options = [])
    {
        $request = $this->db->getQueryBuilder()->delete($table, $condition, $options);

        return $this->setRequest($request);
    }

    /**
     * Creates and executes request with given data.
     * @param string $action
     * @param mixed $body request parameters
     * @return mixed
     */
    public function perform($action, $table, $body = [], array $options = [])
    {
        $request = $this->db->getQueryBuilder()->perform($action, $table, $body, $options);
        $this->setRequest($request);

        return $this->execute();
    }

    /**
     * Executes the request.
     * @param string $url URL
     * @param mixed $body request parameters
     * @return mixed
     */
    public function execute()
    {
        $profile = $this->request->getProfile();
        Yii::beginProfile($profile, __METHOD__);
        $response = $this->db->send($this->request);
        Yii::endProfile($profile, __METHOD__);

        return $response->getData();
    }

}
