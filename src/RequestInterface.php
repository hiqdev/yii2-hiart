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

interface RequestInterface extends \Serializable
{
    /**
     * @param array $options
     * @throws RequestErrorException
     * @return ResponseInterface
     */
    public function send($options = []);

    /**
     * @return string
     */
    public function getBody();

    /**
     * @return string
     */
    public function getFullUri();

    /**
     * Method returns the Request method in the uppercase, e.g. GET, POST, DELETE.
     * @return string
     */
    public function getMethod();
}
