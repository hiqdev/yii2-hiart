<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\debug;

use hiqdev\hiart\Command;
use Yii;
use yii\base\ViewContextInterface;
use yii\debug\Panel;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

/**
 * Debugger panel that collects and displays HiArt queries performed.
 */
class DebugPanel extends Panel implements ViewContextInterface
{
    public function init()
    {
        $this->actions['hiart-query'] = [
            'class' => DebugAction::class,
            'panel' => $this,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'HiArt';
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        $timings = $this->getTimings();
        $total = 0;
        foreach ($timings as $timing) {
            $total += $timing[3];
        }

        return $this->render('summary', [
            'url' => $this->getUrl(),
            'count' => count($timings),
            'total' => number_format($total * 1000) . ' ms',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDetail()
    {
        return $this->render('detail', [
            'timings' => Timing::buildAll($this),
        ]);
    }

    private $_timings;

    public function getTimings()
    {
        if ($this->_timings === null) {
            $this->_timings = $this->calculateTimings();
        }

        return $this->_timings;
    }

    public function calculateTimings()
    {
        $messages = $this->data['messages'];
        $timings = [];
        $stack = [];
        $groups = ArrayHelper::index($messages, 1, 0);
        foreach ($groups as $token => $logs) {
            if (count($logs) !== 2) {
                continue;
            }
            $begin = $logs[Logger::LEVEL_PROFILE_BEGIN];
            $end = $logs[Logger::LEVEL_PROFILE_END];
            $timings[$begin[5]] = [
                count($stack),
                $token,
                $end[3],
                $end[3] - $begin[3],
                $begin[4],
            ];
        }
        ksort($timings);

        return $timings;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $target = $this->module->logTarget;
        $messages = $target->filterMessages($target->messages, Logger::LEVEL_PROFILE, [Command::getProfileCategory()]);

        return ['messages' => $messages];
    }

    protected $_viewPath;

    public function setViewPath($value)
    {
        $this->_viewPath = $value;
    }

    public function getViewPath()
    {
        if ($this->_viewPath === null) {
            $this->_viewPath = dirname(__DIR__) . '/views/debug';
        }

        return $this->_viewPath;
    }

    public function render($file, $data)
    {
        return Yii::$app->view->render($file, $data, $this);
    }
}
