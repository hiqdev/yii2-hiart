<?php
/**
 * ActiveRecord for API
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\debug;

use hiqdev\hiart\Command;
use Yii;
use yii\base\ViewContextInterface;
use yii\log\Logger;

/**
 * Debugger panel that collects and displays HiArt queries performed.
 */
class DebugPanel extends \yii\debug\Panel implements ViewContextInterface
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
            'url'   => $this->getUrl(),
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
        foreach ($messages as $i => $log) {
            list($token, $level, $category, $timestamp) = $log;
            $log[5] = $i;
            if ($level === Logger::LEVEL_PROFILE_BEGIN) {
                $stack[] = $log;
            } elseif ($level === Logger::LEVEL_PROFILE_END) {
                $last = array_pop($stack);
                if ($last !== null && $last[0] === $token) {
                    $timings[$last[5]] = [count($stack), $token, $last[3], $timestamp - $last[3], $last[4]];
                }
            }
        }

        $now = microtime(true);
        while (($last = array_pop($stack)) !== null) {
            $delta = $now - $last[3];
            $timings[$last[5]] = [count($stack), $last[0], $last[2], $delta, $last[4]];
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
