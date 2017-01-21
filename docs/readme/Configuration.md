To use this extension, configure hiart component in your application config:

```php
    'components' => [
        'hiart' => [
            'class' => \hiqdev\hiart\curl\Connection::class,
            'queryBuilderClass' => \hiqdev\hiart\rest\QueryBuilder::class,
            'baseUri' => 'https://site.com/api/v3/',
        ],
    ],
```

Note three main options:

- `class` specifies transport implementation to be used, **cURL** in this case
- `queryBuilderClass` specifies class that actually implements API to be accessed, **REST** in this case
- `baseUri` specifies starting point of the API

Available transports are:

- [cURL](http://php.net/manual/en/book.curl.php)
- [PHP streams](http://php.net/manual/en/book.stream.php)
- [Guzzle](https://github.com/guzzle/guzzle), provided with [yii2-hiart-guzzle](https://github.com/hiqdev/yii2-hiart-guzzle)
- [yii2-httpclient](https://github.com/yiisoft/yii2-httpclient), provided with [yii2-hiart-httpclient](https://github.com/hiqdev/yii2-hiart-httpclient)

You can implement your own transport, it's not difficult see available implementations.
It can be even not HTTP based.

There are `QueryBuilder`s for:

- Basic [REST](https://en.wikipedia.org/wiki/Representational_state_transfer)
- [GitHub API](https://developer.github.com/v3/), provided with [yii2-hiart-github](https://github.com/hiqdev/yii2-hiart-github)
- [HiPanel](https://hipanel.com) API, provided with [hipanel-hiart](https://github.com/hiqdev/hipanel-hiart)

You can implement your own API.
Basically all you need is create your QueryBuilder with these methods:

- `buildMethod(Query $query)`
- `buildHeaders(Query $query)`
- `buildUri(Query $query)`
- `buildQueryParams(Query $query)`
- `buildBody(Query $query)`
- `buildFormParams(Query $query)`

See available implementations and create issues on GitHub.
