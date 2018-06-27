<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\debug;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Debug Action is used by [[DebugPanel]] to perform HiArt queries using ajax.
 */
class DebugAction extends \yii\base\Action
{
    /**
     * @var DebugPanel
     */
    public $panel;

    /**
     * @var \yii\debug\controllers\DefaultController
     */
    public $controller;

    public function run($logId, $tag)
    {
        $this->controller->loadData($tag);

        $timings = $this->panel->getTimings();
        ArrayHelper::multisort($timings, 3, SORT_DESC);

        if (!isset($timings[$logId])) {
            throw new HttpException(404, 'Log message not found.');
        }

        $request  = unserialize($timings[$logId][1]);
        $time     = microtime(true);
        $response = $request->send();
        $time     = microtime(true) - $time;

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'time'   => sprintf('%.1f ms', $time * 1000),
            'result' => $response->getData(),
        ];
    }
}
