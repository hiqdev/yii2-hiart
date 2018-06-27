<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\tests;

class Mock
{
    public $name;
    public $args;
    public $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function __call($name, $args)
    {
        $this->name = $name;
        $this->args = $args;

        return $this->result;
    }
}
