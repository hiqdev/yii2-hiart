To use this extension, configure hiart component in your application config:

```php
    'components' => [
        'hiart' => [
            'class' => \hiqdev\hiart\rest\Connection::class,
            'requestClass' => \hiqdev\hiart\auto\Request::class,
            'baseUri' => 'https://site.com/api/v3/',
        ],
    ],
```

Note three main options:

- `class` specifies class that actually implements API to be accessed, **REST** in this case
- `requestClass` specifies transport implementation to be used, **auto** in this case
- `baseUri` specifies starting point of the API

### Transports

Available transports are:

- **auto** - auto detects best supported transport
- [PHP streams](http://php.net/manual/en/book.stream.php), the most generic fallback, included in this package
- [cURL](http://php.net/manual/en/book.curl.php), included in this package
- [Guzzle](https://github.com/guzzle/guzzle), provided with [yii2-hiart-guzzle](https://github.com/hiqdev/yii2-hiart-guzzle)
- [yii2-httpclient](https://github.com/yiisoft/yii2-httpclient), provided with [yii2-hiart-httpclient](https://github.com/hiqdev/yii2-hiart-httpclient)

You can implement your own transport.
All you need is to create two classes: `Request` and `Response`, it's not difficult see available implementations.
Transport can be even non-HTTP based.

### Query builders

QueryBuilder is what actually implements an API.

We are developing `QueryBuilder`s for:

- Basic [REST API](https://en.wikipedia.org/wiki/Representational_state_transfer), included in this package
- [GitHub API](https://developer.github.com/v3/), provided with [yii2-hiart-github](https://github.com/hiqdev/yii2-hiart-github)
- [HiPanel API](https://hipanel.com/), provided with [hipanel-hiart](https://github.com/hiqdev/hipanel-hiart)

You can implement your own API.
Basically all you need is create your `QueryBuilder` class with these methods:

- `buildMethod(Query $query)`
- `buildHeaders(Query $query)`
- `buildUri(Query $query)`
- `buildQueryParams(Query $query)`
- `buildBody(Query $query)`
- `buildFormParams(Query $query)`

See available implementations and ask questions using issues on GitHub.
