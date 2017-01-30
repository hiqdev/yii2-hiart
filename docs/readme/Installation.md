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
