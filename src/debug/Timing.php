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

use hiqdev\hiart\helpers\Request2Curl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

class Timing
{
    /**
     * @var DebugPanel
     */
    protected $panel;

    protected $logId;

    protected $duration;

    protected $traces;

    protected $request;

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
        $this->request = unserialize($rawTiming[1]);
        $this->duration = $rawTiming[3];
        $this->traces = $rawTiming[4];
    }

    public function getLogId()
    {
        return $this->logId;
    }

    public function getMethod()
    {
        return $this->request->getMethod();
    }

    public function getUrlEncoded()
    {
        return Html::encode($this->request->getFullUri());
    }

    public function getBodyEncoded()
    {
        return Html::encode($this->request->getBody());
    }

    public function getHeaders(): array
    {
        $headers = [];
        foreach ($this->request->getHeaders() as $header => $value) {
            $headers[] = Html::encode("$header: $value");
        }

        return $headers;
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
                    return '<li>' . $this->panel->getTraceLine($trace) . '</li>';
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
        $uri = rtrim($this->request->getFullUri(), '?');
        $sign = strpos($uri, '?') === false ? '?' : '&';
        $newTabUrl = rtrim($uri, '&') . $sign . $this->request->getBody();

        return Html::a('to new tab', $newTabUrl, ['target' => '_blank']);
    }

    public function getCopyAsCurlLink(): string
    {
        $curl = Json::htmlEncode((string)(new Request2Curl($this->request)));

        return Html::a('copy as cURL', '#', [
            'onclick' => new JsExpression("
                (function () {
                    if (navigator.clipboard && window.isSecureContext) {
                        return navigator.clipboard.writeText('$curl');
                    } else {
                        let textArea = document.createElement('textarea');
                        textArea.value = '$curl';
                        textArea.style.position = 'fixed';
                        textArea.style.left = '-999999px';
                        textArea.style.top = '-999999px';
                        document.body.appendChild(textArea);
                        textArea.focus();
                        textArea.select();
                        return new Promise((res, rej) => {
                            document.execCommand('copy') ? res() : rej();
                            textArea.remove();
                        });
                    }
                })();
                return false;
            "),
        ]);
    }
}
