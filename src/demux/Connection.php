<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\demux;

use hiqdev\hiart\ResponseInterface;

class Connection extends \hiqdev\hiart\AbstractConnection
{
    /**
     * @var Callback
     */
    public $selector;

    public function getResponseError(ResponseInterface $response)
    {
        return 'must not be called';
    }
}
