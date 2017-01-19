<?php
/**
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart\debug;

use hiqdev\hiart\Command;
use Yii;
use yii\base\ViewContextInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\log\Logger;

/**
 * Debugger panel that collects and displays HiArt queries performed.
 */
class DebugPanel extends \yii\debug\Panel implements ViewContextInterface
{
    public $db = 'hiart';

    public function init()
    {
        $this->actions['hiart-query'] = [
            'class' => DebugAction::class,
            'panel' => $this,
            'db' => $this->db,
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
        $timings = $this->calculateTimings();
        $queryCount = count($timings);
        $queryTime = 0;
        foreach ($timings as $timing) {
            $queryTime += $timing[3];
        }
        $queryTime = number_format($queryTime * 1000) . ' ms';
        $url = $this->getUrl();
        $output = <<<HTML
<div class="yii-debug-toolbar__block">
    <a href="$url" title="Executed $queryCount queries which took $queryTime.">
        HiArt
        <span class="yii-debug-toolbar__label">$queryCount</span>
        <span class="yii-debug-toolbar__label">$queryTime</span>
    </a>
</div>
HTML;

        return $queryCount > 0 ? $output : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getDetail()
    {
        $apiUrl = null;
        $timings = $this->calculateTimings();
        ArrayHelper::multisort($timings, 3, SORT_DESC);

        // Try to get API URL
        try {
            $component = Yii::$app->get('hiart');
            $apiUrl = (StringHelper::endsWith($component->config['base_uri'],
                '/')) ? $component->config['base_uri'] : $component->config['base_uri'] . '/';
        } catch (\yii\base\InvalidConfigException $e) {
            // Pass
        }

        $rows = [];
        foreach ($timings as $logId => $timing) {
            $message = $timing[1];
            $traces = $timing[4];
            if (($pos = mb_strpos($message, '#')) !== false) {
                $url = mb_substr($message, 0, $pos);
                $body = mb_substr($message, $pos + 1);
            } else {
                $url = $message;
                $body = null;
            }

            $traceString = '';
            if (!empty($traces)) {
                $traceString .= Html::ul($traces, [
                    'class' => 'trace',
                    'item' => function ($trace) {
                        return "<li>{$trace['file']}({$trace['line']})</li>";
                    },
                ]);
            }

            $ajaxUrl = Url::to(['hiart-query', 'logId' => $logId, 'tag' => $this->tag]);
            $runLink = Html::a('run query', $ajaxUrl, [
                'class' => 'hiart-link',
                'data' => ['id' => $logId],
            ]) . '<br/>';

            $path = preg_replace('/^[A-Z]+\s+/', '', $url);
            if (strpos($path, '?') !== false) {
                $newTabUrl = $apiUrl . rtrim($path, '&') . '&' . $body;
            } else {
                $newTabUrl = $apiUrl . $path . '?' . $body;
            }

            $rows[] = [
                'logId'         => $logId,
                'duration'      => sprintf('%.1f ms', $timing[3] * 1000),
                'traceString'   => $traceString,
                'runLink'       => $runLink,
                'newTabLink'    => Html::a('to new tab', $newTabUrl, ['target' => '_blank']) . '<br/>',
                'urlEncoded'    => Html::encode((isset($apiUrl)) ? str_replace(' ', ' ' . $apiUrl, $url) : $url),
                'bodyEncoded'   => Html::encode($body),
            ];
        }

        return $this->render('detail', compact('rows'));
    }

    private $_timings;

    public function calculateTimings()
    {
        if ($this->_timings !== null) {
            return $this->_timings;
        }

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

        return $this->_timings = $timings;
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
