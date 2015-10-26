<?php

/*
 * Tools to use API as ActiveRecord for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-hiart
 * @package   yii2-hiart
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\hiart;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Connection extends Component
{
    const EVENT_AFTER_OPEN = 'afterOpen';

    public $config = [];

    /**
     * Tmporary auth config.
     *
     * @var array
     */
    public $auth = [];

    public $connectionTimeout = null;

    public $dataTimeout = null;

    public static $curl = null;

    private function _getAuth()
    {
        $res = [];
        if (Yii::$app->user->identity) {
            $res['access_token'] = Yii::$app->user->identity->getAccessToken();
        } else {
            Yii::$app->user->loginRequired();
        }

        return $res;
    }

    public function init()
    {
        if (!isset($this->config['api_url'])) {
            throw new InvalidConfigException('HiActiveResource needs api_url configuration');
        }
    }

    public function getHandler()
    {
        if (!self::$curl) {
            self::$curl = static::$curl = curl_init();
        }

        return self::$curl;
    }

    /**
     * Closes the connection when this component is being serialized.
     *
     * @return array
     */
    public function __sleep()
    {
        return array_keys(get_object_vars($this));
    }

    /**
     * Returns the name of the DB driver for the current [[dsn]].
     *
     * @return string name of the DB driver
     */
    public function getDriverName()
    {
        return 'hiresource';
    }

    /**
     * Creates a command for execution.
     *
     * @param array $config the configuration for the Command class
     *
     * @return Command the DB command
     */
    public function createCommand($config = [])
    {
        $config['db'] = $this;
        $command      = new Command($config);

        return $command;
    }

    /**
     * Creates new query builder instance.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return new QueryBuilder($this);
    }

    /**
     * Performs GET HTTP request.
     *
     * @param string $url     URL
     * @param array  $options URL options
     * @param string $body    request body
     * @param bool   $raw     if response body contains JSON and should be decoded
     *
     * @throws HiArtException
     * @throws \yii\base\InvalidConfigException
     *
     * @return mixed response
     */
    public function get($url, $options = [], $body = null, $raw = false)
    {
        $result = $this->httpRequest('POST', $this->createUrl($url), http_build_query($options), $raw);

        return $this->checkResponse($result, $url, $options);
    }

    /**
     * Performs HEAD HTTP request.
     *
     * @param string $url     URL
     * @param array  $options URL options
     * @param string $body    request body
     *
     * @throws HiArtException
     * @throws \yii\base\InvalidConfigException
     *
     * @return mixed response
     */
    public function head($url, $options = [], $body = null)
    {
        $result = $this->httpRequest('HEAD', $this->createUrl($url), http_build_query($options));

        return $this->checkResponse($result, $url, $options);
    }

    /**
     * Performs POST HTTP request.
     *
     * @param string $url     URL
     * @param array  $options URL options
     * @param string $body    request body
     * @param bool   $raw     if response body contains JSON and should be decoded
     *
     * @throws HiArtException
     * @throws \yii\base\InvalidConfigException
     *
     * @return mixed response
     */
    public function post($url, $options = [], $body = null, $raw = false)
    {
        $result = $this->httpRequest('POST', $this->createUrl($url), http_build_query($options), $raw);

        return $this->checkResponse($result, $url, $options);
    }

    /**
     * Performs PUT HTTP request.
     *
     * @param string $url     URL
     * @param array  $options URL options
     * @param string $body    request body
     * @param bool   $raw     if response body contains JSON and should be decoded
     *
     * @throws HiArtException
     * @throws \yii\base\InvalidConfigException
     *
     * @return mixed response
     */
    public function put($url, $options = [], $body = null, $raw = false)
    {
        $result = $this->httpRequest('PUT', $this->createUrl($url), http_build_query($options), $raw);

        return $this->checkResponse($result, $url, $options);
    }

    /**
     * Performs DELETE HTTP request.
     *
     * @param string $url     URL
     * @param array  $options URL options
     * @param string $body    request body
     * @param bool   $raw     if response body contains JSON and should be decoded
     *
     * @throws HiArtException
     * @throws \yii\base\InvalidConfigException
     *
     * @return mixed response
     */
    public function delete($url, $options = [], $body = null, $raw = false)
    {
        $result = $this->httpRequest('DELETE', $this->createUrl($url), http_build_query($options), $raw);

        return $this->checkResponse($result, $url, $options);
    }

    /**
     * @param $url
     * @param array $options
     *
     * @return mixed
     */
    public function perform($url, $options = [])
    {
        $result = $this->httpRequest('POST', $this->createUrl($url), http_build_query($options));

        return $this->checkResponse($result, $url, $options);
    }

    /**
     * Creates URL.
     *
     * @param mixed $path    path
     * @param array $options URL options
     *
     * @return array
     */
    private function createUrl($path, $options = [])
    {
        $options = ArrayHelper::merge($options, $this->_getAuth());
        if (!is_string($path)) {
            $url = urldecode(reset($path));
            if (!empty($options)) {
                $url .= '?' . http_build_query($options);
            }
        } else {
            $url = $path;
            if (!empty($options)) {
                $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($options);
            }
        }

        return [$this->config['api_url'], $url];
    }

    /**
     * Performs HTTP request.
     *
     * @param string $method      method name
     * @param string $url         URL
     * @param string $requestBody request body
     * @param bool   $raw         if response body contains JSON and should be decoded
     *
     * @throws ErrorResponseException
     * @throws HiArtException
     *
     * @return mixed if request failed
     */
    protected function httpRequest($method, $url, $requestBody = null, $raw = false)
    {
        $this->auth = [
            'access_token' => Yii::$app->user->identity->getAccessToken(),
        ];
        $method = strtoupper($method);
        // response body and headers
        $headers = [];
        $body    = '';
        $options = [
            CURLOPT_URL       => $url,
            CURLOPT_USERAGENT => 'Yii Framework ' . Yii::getVersion() .  ' (HiArt)',
            //CURLOPT_ENCODING        => 'UTF-8',
            # CURLOPT_USERAGENT       => 'curl/0.00 (php 5.x; U; en)',
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_HEADER         => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 2,
            // http://www.php.net/manual/en/function.curl-setopt.php#82418
            CURLOPT_HTTPHEADER    => ['Expect:'],
            CURLOPT_WRITEFUNCTION => function ($curl, $data) use (&$body) {
                $body .= $data;

                return mb_strlen($data, '8bit');
            },
            CURLOPT_HEADERFUNCTION => function ($curl, $data) use (&$headers) {
                foreach (explode("\r\n", $data) as $row) {
                    if (($pos = strpos($row, ':')) !== false) {
                        $headers[strtolower(substr($row, 0, $pos))] = trim(substr($row, $pos + 1));
                    }
                }

                return mb_strlen($data, '8bit');
            },
            CURLOPT_CUSTOMREQUEST => $method,
        ];
        if ($this->connectionTimeout !== null) {
            $options[CURLOPT_CONNECTTIMEOUT] = $this->connectionTimeout;
        }
        if ($this->dataTimeout !== null) {
            $options[CURLOPT_TIMEOUT] = $this->dataTimeout;
        }
        if ($requestBody !== null) {
            $options[CURLOPT_POSTFIELDS] = $requestBody;
        }
        if ($method === 'HEAD') {
            $options[CURLOPT_NOBODY] = true;
            unset($options[CURLOPT_WRITEFUNCTION]);
        }
        if (is_array($url)) {
            list($host, $q) = $url;
            if (strncmp($host, 'inet[', 5) === 0) {
                $host = substr($host, 5, -1);
                if (($pos = strpos($host, '/')) !== false) {
                    $host = substr($host, $pos + 1);
                }
            }
            $profile = $method . ' ' . $q . '#' . $requestBody;
            if (preg_match('@^https?://@', $host)) {
                $url = $host . '/' . $q;
            } else {
                throw new HiArtException('Request failed: please specify the protocol (http, https) in reference to the API HiResource Core');
            }
        } else {
            $profile = false;
        }
        $options[CURLOPT_URL] = $url;
        Yii::trace("Sending request to node: $method $url\n$requestBody", __METHOD__);
        if ($profile !== false) {
            Yii::beginProfile($profile, __METHOD__);
        }
        $curl = $this->getHandler();
        curl_setopt_array($curl, $options);
        if (curl_exec($curl) === false) {
            throw new HiArtException('Request failed: ' . curl_errno($curl) . ' - ' . curl_error($curl), [
                'requestUrl'      => $url,
                'requestBody'     => $requestBody,
                'responseBody'    => $this->decodeErrorBody($body),
                'requestMethod'   => $method,
                'responseHeaders' => $headers,
            ]);
        }

        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        Yii::trace(curl_getinfo($curl));
        if ($profile !== false) {
            Yii::endProfile($profile, __METHOD__);
        }
        if ($responseCode >= 200 && $responseCode < 300) {
            if ($method === 'HEAD') {
                return true;
            } else {
                if (isset($headers['content-length']) && ($len = mb_strlen($body, '8bit')) < $headers['content-length']) {
                    throw new HiArtException("Incomplete data received: $len < {$headers['content-length']}", [
                        'requestMethod'   => $method,
                        'requestUrl'      => $url,
                        'requestBody'     => $requestBody,
                        'responseCode'    => $responseCode,
                        'responseHeaders' => $headers,
                        'responseBody'    => $this->decodeErrorBody($body),
                    ]);
                }
                if (isset($headers['content-type']) && !strncmp($headers['content-type'], 'application/json', 16)) {
                    return $raw ? $body : Json::decode($body);
                } else {
                    return $body;
                }
                throw new HiArtException('Unsupported data received from Hiresource: ' . $headers['content-type'], [
                    'requestUrl'      => $url,
                    'requestBody'     => $requestBody,
                    'responseBody'    => $this->decodeErrorBody($body),
                    'requestMethod'   => $method,
                    'responseCode'    => $responseCode,
                    'responseHeaders' => $headers,
                ]);
            }
        } elseif ($responseCode === 404) {
            return false;
        } else {
            throw new HiArtException("Request request failed with code $responseCode.", [
                'requestUrl'      => $url,
                'requestBody'     => $requestBody,
                'responseBody'    => $this->decodeErrorBody($body),
                'requestMethod'   => $method,
                'responseCode'    => $responseCode,
                'responseHeaders' => $headers,
            ]);
        }
    }

    /**
     * Try to decode error information if it is valid json, return it if not.
     *
     * @param $body
     *
     * @return mixed
     */
    protected function decodeErrorBody($body)
    {
        try {
            $decoded = Json::decode($body);
            if (isset($decoded['error'])) {
                $decoded['error'] = preg_replace('/\b\w+?Exception\[/',
                    "<span style=\"color: red;\">\\0</span>\n               ", $decoded['error']);
            }

            return $decoded;
        } catch (InvalidParamException $e) {
            return $body;
        }
    }

    /**
     * Callback to test if API response has error.
     */
    public $errorChecker;

    /**
     * @param array  $response response data from API
     * @param string $url      request URL
     * @param array  $options  request data
     *
     * @throws ErrorResponseException
     *
     * @return array
     */
    protected function checkResponse($response, $url, $options)
    {
        $error = call_user_func($this->errorChecker, $response);
        if ($error) {
            throw new ErrorResponseException($error, [
                'requestUrl' => $url,
                'request'    => $options,
                'response'   => $response,
            ]);
        }

        return $response;
    }
}
