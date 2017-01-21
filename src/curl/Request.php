<?php

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
