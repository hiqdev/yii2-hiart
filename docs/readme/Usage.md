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
