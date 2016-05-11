hiqdev/yii2-hiart changelog
---------------------------

## 0.0.3 2016-05-11

- Added asset-packagist.org repository to composer.json

## 0.0.2 2016-05-11

- Fixed not renamed `hiresoruce` to `hiart`
- Fixed `to new tab` link when POST request has no variables in query string
- Added relations population via `->joinWith()`
- Added `Connection::disableAuth()`, `::enableAuth()` in order to manage auth conditions per connection
- Updated CSS classes after yii2-debug extension update
- Deleted `Command::getList()`, `ActiveQuery::getList()`
- Chhanged `Connection::setErrorChecker()` to support a callable function using array syntax
- Changed url, query and body processing in Connection
- Added user agent
- Fixed CI
- Changed: started redoing with Guzzle
- Added tests and CI
- Changed Collection::models visibility to protected
- Fixed different issues
- Changed authorization in Connection class, made with configuration callback
- Added passing options to Command through find/One/All()
- Added population of joined relations
- Changed default limit to ALL
- Added recursive joining
- Added lt/gt to QueryBuilder
- Fixed translation, redone Re::l to Yii::t (sol@hiqdev.com)
- Removed `gl_key`, `gl_value`
- Added second argument to ActiveQuery::all that will be passed to Command::search
- Fixed 'raw' processing
- Fixed PHP warnings

## 0.0.1 2015-08-26

- Added Connection::errorChecker callback to test if API response was error
- Fixed PHP warnings
- Changed: moved to src
- Added basics
- Changed: renamed to hiart

## Development started 2015-04-17

