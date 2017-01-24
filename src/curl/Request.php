<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\curl;

use hiqdev\hiart\AbstractRequest;

/**
 * cURL request implementation.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Request extends AbstractRequest
{
    protected $workerClass = RequestWorker::class;

    protected function createWorker()
    {
    }
}
