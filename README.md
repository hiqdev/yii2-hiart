HiArt
=====

**Tools to use API as ActiveRecord for Yii2**

[![Latest Stable Version](https://poser.pugx.org/hiqdev/yii2-hiart/v/stable)](https://packagist.org/packages/hiqdev/yii2-hiart)
[![Total Downloads](https://poser.pugx.org/hiqdev/yii2-hiart/downloads)](https://packagist.org/packages/hiqdev/yii2-hiart)
[![Build Status](https://img.shields.io/travis/hiqdev/yii2-hiart.svg)](https://travis-ci.org/hiqdev/yii2-hiart)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/hiqdev/yii2-hiart.svg)](https://scrutinizer-ci.com/g/hiqdev/yii2-hiart/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/hiqdev/yii2-hiart.svg)](https://scrutinizer-ci.com/g/hiqdev/yii2-hiart/)
[![Dependency Status](https://www.versioneye.com/php/hiqdev:yii2-hiart/dev-master/badge.svg)](https://www.versioneye.com/php/hiqdev:yii2-hiart/dev-master)

Use your API as ActiveRecord

## Installation

The preferred way to install this yii2-extension is through [composer](http://getcomposer.org/download/).

Either run

```sh
php composer.phar require "hiqdev/yii2-hiart"
```

or add

```json
"hiqdev/yii2-hiart": "*"
```

to the require section of your composer.json.

## Configuration

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

Basically all features of Yii ActiveRecords work if your API provides them.

## License

This project is released under the terms of the BSD-3-Clause [license](LICENSE).
Read more [here](http://choosealicense.com/licenses/bsd-3-clause).

Copyright © 2015-2017, HiQDev (http://hiqdev.com/)

## Acknowledgments

- This project is based on [Yii2 Elasticsearch](https://github.com/yiisoft/yii2-elasticsearch).
