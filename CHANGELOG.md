# hiqdev/yii2-hiart

## [0.4.0] - 2019-02-20

- Fixed Debug panel to follow Yii API changes ([@SilverFire])
- Trigger `EVENT_AFTER_FIND` for joined relations after poppulation for consistency reasons ([@SilverFire])
- Allowed array `Query::$from` in REST QueryBuilder ([@hiqsol], [guillaume.pignolet@inadvans.com])
- Other minor improvements ([@SilverFire], [@hiqsol])

## [0.3.0] - 2018-06-27

- Fixed bugs ([@hiqsol], [@tafid], [@SilverFire], [@BladeRoot])
- Fixed PHP 7.2 compatibility ([@SilverFire])

## [0.2.0] - 2017-04-15

- Added transport autodetection with `auto/Request.php` ([@hiqsol])
- Moved CURL transport here, deprecated `yii2-hiart-curl` ([@hiqsol])
- Switched to PHPUnit 6 ([@hiqsol])
- Improved docs ([@hiqsol], [@SilverFire])
- Added `callWithDisabledAuth` ([@SilverFire])
- Fixed minor issues ([@SilverFire], [@hiqsol])

## [0.1.2] - 2017-02-08

- Improved `AbstractConnection::getDb` to work without DI too ([@hiqsol])

## [0.1.1] - 2017-02-07

- Renamed performScenario to `query` and `batchQuery` ([@hiqsol])
- Added `batchPerform` ([@hiqsol])
- Renamed scenarioCommands to `scenarioActions` ([@hiqsol])
- Renamed from -> `tableName` in ActiveRecord ([@hiqsol], [@SilverFire])
- Refactored a lot, removed junk, improved docs ([@hiqsol], [@SilverFire])
- Removed **cURL** transport into separate package [yii2-hiart-curl] ([@hiqsol], [@SilverFire])
- Refactored exceptions ([@SilverFire])

## [0.1.0] - 2017-01-25

- Changed nearly everything: **REDONE** to be usable for any API ([@hiqsol])
- Added inversed relations ([@hiqsol], [@SilverFire])
- Added nested joinWith ([@SilverFire])
- Changed `api_url` to `base_uri` (as in guzzle) ([@SilverFire])
- Fixed code styling alot ([@SilverFire], [@tafid], [@hiqsol])

## [0.0.4] - 2016-05-11

- Fixed tests ([@hiqsol])

## [0.0.3] - 2016-05-11

- Added asset-packagist.org repository to composer.json ([@SilverFire])

## [0.0.2] - 2016-05-11

- Fixed not renamed `hiresoruce` to `hiart` ([@SilverFire])
- Fixed `to new tab` link when POST request has no variables in query string ([@SilverFire])
- Added relations population via `->joinWith()` ([@SilverFire], [@tafid])
- Added `Connection::disableAuth()`, `::enableAuth()` in order to manage auth conditions per connection ([@hiqsol])
- Updated CSS classes after yii2-debug extension update ([@SilverFire])
- Deleted `Command::getList()`, `ActiveQuery::getList()` ([@SilverFire])
- Chhanged `Connection::setErrorChecker()` to support a callable function using array syntax ([@SilverFire], [@hiqsol])
- Changed url, query and body processing in Connection ([@hiqsol])
- Added user agent ([@hiqsol])
- Fixed CI ([@hiqsol])
- Changed: started redoing with Guzzle ([@hiqsol], [@SilverFire], [bscheshir.work@gmail.com])
- Added tests and CI ([@hiqsol])
- Changed Collection::models visibility to protected ([@SilverFire])
- Fixed different issues ([@SilverFire], [@tafid], [@hiqsol])
- Changed authorization in Connection class, made with configuration callback ([@hiqsol])
- Added passing options to Command through find/One/All() ([@hiqsol])
- Added population of joined relations ([@SilverFire])
- Changed default limit to ALL ([@SilverFire])
- Added recursive joining ([@SilverFire])
- Added lt/gt to QueryBuilder ([@hiqsol], [@SilverFire])
- Fixed translation, redone Re::l to Yii::t (sol@hiqdev.com) ([@hiqsol])
- Removed `gl_key`, `gl_value` ([@SilverFire])
- Added second argument to ActiveQuery::all that will be passed to Command::search ([@SilverFire])
- Fixed 'raw' processing ([@hiqsol])
- Fixed PHP warnings ([@hiqsol])

## [0.0.1] - 2015-08-26

- Added Connection::errorChecker callback to test if API response was error ([@hiqsol])
- Fixed PHP warnings ([@tafid])
- Changed: moved to src ([@hiqsol])
- Added basics ([@SilverFire], [@hiqsol], [@tafid])
- Changed: renamed to hiart ([@hiqsol], [@SilverFire], [@tafid])

## [Development started] - 2015-04-17

[yii2-hiart-curl]: https://github.com/hiqdev/yii2-hiart-curl
[@hiqsol]: https://github.com/hiqsol
[sol@hiqdev.com]: https://github.com/hiqsol
[@SilverFire]: https://github.com/SilverFire
[d.naumenko.a@gmail.com]: https://github.com/SilverFire
[@tafid]: https://github.com/tafid
[andreyklochok@gmail.com]: https://github.com/tafid
[@BladeRoot]: https://github.com/BladeRoot
[bladeroot@gmail.com]: https://github.com/BladeRoot
[Under development]: https://github.com/hiqdev/yii2-hiart/compare/0.3.0...HEAD
[0.0.4]: https://github.com/hiqdev/yii2-hiart/compare/0.0.3...0.0.4
[0.0.3]: https://github.com/hiqdev/yii2-hiart/compare/0.0.2...0.0.3
[0.0.2]: https://github.com/hiqdev/yii2-hiart/compare/0.0.1...0.0.2
[0.0.1]: https://github.com/hiqdev/yii2-hiart/releases/tag/0.0.1
[0.1.0]: https://github.com/hiqdev/yii2-hiart/compare/0.0.4...0.1.0
[0.1.1]: https://github.com/hiqdev/yii2-hiart/compare/0.1.0...0.1.1
[0.1.2]: https://github.com/hiqdev/yii2-hiart/compare/0.1.1...0.1.2
[0.2.0]: https://github.com/hiqdev/yii2-hiart/compare/0.1.2...0.2.0
[0.3.0]: https://github.com/hiqdev/yii2-hiart/compare/0.2.0...0.3.0
[0.4.0]: https://github.com/hiqdev/yii2-hiart/compare/0.3.0...0.4.0
