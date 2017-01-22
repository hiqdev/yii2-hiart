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

use hiqdev\hiart\AbstractResponse;

/**
 * PHP stream response implementation.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Response extends AbstractResponse
{
    /**
     * @var ResponseWorker
     */
    protected $worker;
}
