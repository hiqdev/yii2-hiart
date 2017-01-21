<?php

namespace hiqdev\hiart\curl;

use hiqdev\hiart\AbstractResponse;

/**
 * Guzzle response implementation.
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
