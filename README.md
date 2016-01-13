HiArt
=====

**Tools to use API as ActiveRecord for Yii2**

[![Latest Stable Version](https://poser.pugx.org/hiqdev/yii2-hiart/v/stable)](https://packagist.org/packages/hiqdev/yii2-hiart)
[![Total Downloads](https://poser.pugx.org/hiqdev/yii2-hiart/downloads)](https://packagist.org/packages/hiqdev/yii2-hiart)
[![Build Status](https://img.shields.io/travis/hiqdev/yii2-hiart.svg)](https://travis-ci.org/hiqdev/yii2-hiart)
[![Code Coverage](https://scrutinizer-ci.com/g/hiqdev/yii2-hiart/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/hiqdev/yii2-hiart/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hiqdev/yii2-hiart/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hiqdev/yii2-hiart/?branch=master)
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
        'hiresource' => [
            'class' => 'hiqdev\hiart\Connection',
            'config' => [
                'api_url' => 'https://api.site.com/',
            ],
        ],
    ],
```

## Usage

Define your Model

```php
class MyModel extends \hiqdev\hiart\ActiveRecord
{
    public function attributes()
    {
        return ['id', 'name', 'else'];
    }
}
```

## License

This project is released under the terms of the BSD-3-Clause [license](LICENSE).
Read more [here](http://choosealicense.com/licenses/bsd-3-clause).

Copyright © 2015, HiQDev (http://hiqdev.com/)

## Acknowledgments

- This project is based on [Yii2 Elasticsearch](https://github.com/yiisoft/yii2-elasticsearch).
