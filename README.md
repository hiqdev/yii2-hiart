# HiArt

**ActiveRecord for API**

[![Latest Stable Version](https://poser.pugx.org/hiqdev/yii2-hiart/v/stable)](https://packagist.org/packages/hiqdev/yii2-hiart)
[![Total Downloads](https://poser.pugx.org/hiqdev/yii2-hiart/downloads)](https://packagist.org/packages/hiqdev/yii2-hiart)
[![Build Status](https://img.shields.io/travis/hiqdev/yii2-hiart.svg)](https://travis-ci.org/hiqdev/yii2-hiart)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/hiqdev/yii2-hiart.svg)](https://scrutinizer-ci.com/g/hiqdev/yii2-hiart/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/hiqdev/yii2-hiart.svg)](https://scrutinizer-ci.com/g/hiqdev/yii2-hiart/)
[![Dependency Status](https://www.versioneye.com/php/hiqdev:yii2-hiart/dev-master/badge.svg)](https://www.versioneye.com/php/hiqdev:yii2-hiart/dev-master)

This Yii2 extension provides [ActiveRecord](http://en.wikipedia.org/wiki/Active_record_pattern)
implementation that allows to access remote/web API same way as you do with normal
[Yii2 DB ActiveRecord](http://www.yiiframework.com/doc-2.0/guide-db-active-record.html).

## Installation

The preferred way to install this yii2-extension is through [composer](http://getcomposer.org/download/).

```sh
composer require "hiqdev/yii2-hiart"
```

### Performance

This package provides only **PHP streams** implementation which is clearly slower then **cURL**.
So if you have cURL extension available in your system please use `hiqdev/yii2-hiart-curl` package instead.

```sh
composer require "hiqdev/yii2-hiart-curl"
```

or add these lines to the require section of your composer.json:

```json
    "hiqdev/yii2-hiart-curl": "*"
```

## Configuration

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

## Usage

Define your Model:

```php
class User extends \hiqdev\hiart\ActiveRecord
{
    public function rules()
    {
        return [
            ['id', 'integer', 'min' => 1],
            ['login', 'string', 'min' => 2, 'max' => 32],
        ];
    }
}
```

Note that you use general `hiqdev\hiart\ActiveRecord` class not specific for certain API.
API is specified in connection options and you don't need to change model classes when
you change API.

Then you just use your models same way as DB ActiveRecord models.

```php
$user = new User();
$user->login = 'sol';

$user->save();

$admins = User::find()->where(['type' => User::ADMIN_TYPE])->all();
```

Basically all the features of [Yii2 ActiveRecord] work if your API provides them.

[Yii2 ActiveRecord]: http://www.yiiframework.com/doc-2.0/guide-db-active-record.html

## License

This project is released under the terms of the BSD-3-Clause [license](LICENSE).
Read more [here](http://choosealicense.com/licenses/bsd-3-clause).

Copyright © 2015-2017, HiQDev (http://hiqdev.com/)

## Acknowledgments

- This project is based on [Yii2 Elasticsearch](https://github.com/yiisoft/yii2-elasticsearch).
