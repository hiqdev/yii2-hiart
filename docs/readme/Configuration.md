To use this extension, configure hiart component in your application config:

```php
    'components' => [
        'hiart' => [
            'class' => \hiqdev\hiart\rest\Connection::class,
            'requestClass' => \hiqdev\hiart\curl\Request::class,
            'baseUri' => 'https://site.com/api/v3/',
        ],
    ],
```

Note three main options:

- `class` specifies class that actually implements API to be accessed, **REST** in this case
- `requestClass` specifies transport implementation to be used, **cURL** in this case
- `baseUri` specifies starting point of the API

Available transports are:

- [PHP streams](http://php.net/manual/en/book.stream.php), **default**, included in this package
- [cURL](http://php.net/manual/en/book.curl.php), included in this package
- [Guzzle](https://github.com/guzzle/guzzle), provided with [yii2-hiart-guzzle](https://github.com/hiqdev/yii2-hiart-guzzle)
- [yii2-httpclient](https://github.com/yiisoft/yii2-httpclient), provided with [yii2-hiart-httpclient](https://github.com/hiqdev/yii2-hiart-httpclient)

You can implement your own transport, it's not difficult see available implementations.
All you need is to create two classes:
It can be even not HTTP based.

There are `QueryBuilder`s for:

- Basic [REST](https://en.wikipedia.org/wiki/Representational_state_transfer), included in this package
- [GitHub API](https://developer.github.com/v3/), provided with [yii2-hiart-github](https://github.com/hiqdev/yii2-hiart-github)
- [HiPanel API](https://hipanel.com/), provided with [hipanel-hiart](https://github.com/hiqdev/hipanel-hiart)

You can implement your own API.
Basically all you need is to create your QueryBuilder with these methods:

- `buildMethod(Query $query)`
- `buildHeaders(Query $query)`
- `buildUri(Query $query)`
- `buildQueryParams(Query $query)`
- `buildBody(Query $query)`
- `buildFormParams(Query $query)`

See available implementations and create issues on GitHub.
