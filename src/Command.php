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

use Yii;

/**
 * The Command class implements execution of request.
 */
class Command extends \yii\base\Component
{
    /**
     * @var AbstractConnection
     */
    public $db;

    /**
     * @var RequestInterface request object
     */
    protected $request;

    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Sends a request to retrieve data.
     * In API this could be get, search or list request.
     * @param array $options send options
     * @throws ResponseErrorException
     * @return ResponseInterface response object
     */
    public function search($options = [])
    {
        $this->request->getQuery()->addAction('search');

        return $this->send($options);
    }

    /**
     * Sends a request to create/insert data.
     * @param mixed $table entity to create
     * @param mixed $columns attributes of object to create
     * @param array $params request parameters
     * @return $this
     */
    public function insert($table, $columns, array $params = [])
    {
        $request = $this->db->getQueryBuilder()->insert($table, $columns, $params);

        return $this->setRequest($request);
    }

    /**
     * Sends a request to update data.
     * @param mixed $table entity to update
     * @param mixed $columns attributes of object to update
     * @param array $condition
     * @param array $params request parameters
     * @return $this
     */
    public function update($table, $columns, $condition = [], array $params = [])
    {
        $request = $this->db->getQueryBuilder()->update($table, $columns, $condition, $params);

        return $this->setRequest($request);
    }

    /**
     * Sends a request to delete data.
     * @param mixed $table entity to update
     * @param array $condition
     * @param array $params request parameters
     * @return $this
     */
    public function delete($table, $condition, array $params = [])
    {
        $request = $this->db->getQueryBuilder()->delete($table, $condition, $params);

        return $this->setRequest($request);
    }

    /**
     * Creates and executes request with given data.
     * @param string $action
     * @param string $table
     * @param mixed $body
     * @param array $params request parameters
     * @return ResponseInterface response object
     */
    public function perform($action, $table, $body = [], array $params = [])
    {
        $request = $this->db->getQueryBuilder()->perform($action, $table, $body, $params);
        $this->setRequest($request);

        return $this->send();
    }

    /**
     * Executes the request.
     * @param array $options send options
     * @return ResponseInterface response object
     */
    public function send($options = [])
    {
        $profile = serialize($this->request);
        $category = static::getProfileCategory();
        Yii::beginProfile($profile, $category);
        $response = $this->request->send($options);
        Yii::endProfile($profile, $category);
        $this->db->checkResponse($response);

        return $response;
    }

    public static function getProfileCategory()
    {
        return __METHOD__;
    }
}
