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

use hiqdev\hiart\Request;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class Timing
{
    protected $panel;

    protected $logId;
    protected $duration;
    protected $traces;
    protected $dbname;
    protected $method;
    protected $uri;
    protected $body;

    public function __construct(DebugPanel $panel, $logId)
    {
        $this->panel = $panel;
        $this->logId = $logId;
    }

    public static function buildAll(DebugPanel $panel)
    {
        $rawTimings = $panel->getTimings();
        ArrayHelper::multisort($rawTimings, 3, SORT_DESC);

        $timings = [];
        foreach ($rawTimings as $logId => $rawTiming) {
            $timings[] = static::buildOne($panel, $logId, $rawTiming);
        }

        return $timings;
    }

    public static function buildOne($panel, $logId, $rawTiming)
    {
        $new = new static($panel, $logId);
        $new->updateFromRaw($rawTiming);

        return $new;
    }

    public function updateFromRaw($rawTiming)
    {
        $this->duration = $rawTiming[3];
        $this->traces = $rawTiming[4];
        $profile = $rawTiming[1];

        foreach (Request::decodeProfile($profile) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getLogId()
    {
        return $this->logId;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUrlEncoded()
    {
        return Html::encode($this->getFullUri());
    }

    public function getBodyEncoded()
    {
        return Html::encode($this->body);
    }

    public function getDuration()
    {
        return sprintf('%.1f ms', $this->duration * 1000);
    }

    public function getTrace()
    {
        $result = '';
        if (!empty($this->traces)) {
            $result .= Html::ul($this->traces, [
                'class' => 'trace',
                'item' => function ($trace) {
                    return "<li>{$trace['file']}({$trace['line']})</li>";
                },
            ]);
        }

        return $result;
    }

    public function getRunLink()
    {
        $ajaxUrl = Url::to(['hiart-query', 'logId' => $this->logId, 'tag' => $this->panel->tag]);

        return Html::a('run query', $ajaxUrl, [
            'class' => 'hiart-link',
            'data' => ['id' => $this->logId],
        ]);
    }

    public function getNewTabLink()
    {
        $sign = strpos($this->uri, '?') === false ? '?' : '';
        $newTabUrl = rtrim($this->getFullUri(), '&') . $sign . $this->body;

        return Html::a('to new tab', $newTabUrl, ['target' => '_blank']);
    }

    public function getFullUri()
    {
        return $this->getBaseUri() . '/'. $this->uri;
    }

    public function getBaseUri()
    {
        $this->panel->getBaseUri($this->dbname);
    }
}
