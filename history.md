hiqdev/yii2-hiart
-----------------

## [Under development]

- Added inversed relations
    - [752c00f] 2016-09-07 Implemented inversed relations [d.naumenko.a@gmail.com]
- Added nested joinWith
    - [1d7e0ef] 2016-08-31 Implemented nested joinWith [d.naumenko.a@gmail.com]
- Changed `api_url` to `base_uri` (as in guzzle)
    - [bb80099] 2016-06-29 DebugPanel - changed `api_url` to `base_uri` parameter [d.naumenko.a@gmail.com]
- Fixed code styling alot
    - [092d2ba] 2016-09-13 Updated PHPDoc [d.naumenko.a@gmail.com]
    - [e3acb67] 2016-08-18 ActiveQuery::prepare() returns $this [d.naumenko.a@gmail.com]
    - [41c0608] 2016-08-17 Added setter and getter for check auth [andreyklochok@gmail.com]
    - [6b3dc59] 2016-07-20 ActiveQuery - added joined relation populating for indexed relations [d.naumenko.a@gmail.com]
    - [659b57b] 2016-07-19 ActiveQuery::addSelect added casting param to array [andreyklochok@gmail.com]
    - [3d00dcd] 2016-07-16 csfixed [sol@hiqdev.com]
    - [d6cc795] 2016-07-12 csfixed [sol@hiqdev.com]
    - [560d87c] 2016-06-26 Removed commented code [d.naumenko.a@gmail.com]
    - [0ee1eee] 2016-06-04 Connection::getHandler() - removed calling Psr7/Client::setUserAgent() call and replaced with config" [d.naumenko.a@gmail.com]
    - [13340a1] 2016-06-06 + separate checkError for overriding [sol@hiqdev.com]
    - [412cecb] 2016-06-02 added ge/le conditions, improved building compare conditions [sol@hiqdev.com]
    - [26679da] 2016-05-12 fixed several scrutinizer bugs [sol@hiqdev.com]

## [0.0.4] - 2016-05-11

- Fixed tests
    - [4296c47] 2016-05-11 fixed tests [sol@hiqdev.com]

## [0.0.3] - 2016-05-11

- Added asset-packagist.org repository to composer.json
    - [354c0f9] 2016-05-11 Added asset-packagist.org repository to composer.json [d.naumenko.a@gmail.com]

## [0.0.2] - 2016-05-11

- Fixed not renamed `hiresoruce` to `hiart`
    - [1b91d73] 2016-05-11 Updated changelog [d.naumenko.a@gmail.com]
    - [a32ac04] 2016-05-11 Renamed all mentions of `hiresource` to `hiart` [d.naumenko.a@gmail.com]
- Fixed `to new tab` link when POST request has no variables in query string
    - [a131b13] 2016-04-11 DebugPanel - fixed `to new tab` link when POST request has no variables in query string [d.naumenko.a@gmail.com]
- Added relations population via `->joinWith()`
    - [1b7a4ac] 2016-03-07 ActiveQuery::populateJoinedRelations() fixed processing of multilevel relations [d.naumenko.a@gmail.com]
    - [314ac7c] 2016-04-08 ActiveQuery - added pupulate method [d.naumenko.a@gmail.com]
    - [da48238] 2016-04-15 Improve populateRelation method [andreyklochok@gmail.com]
- Added `Connection::disableAuth()`, `::enableAuth()` in order to manage auth conditions per connection
    - [3554aaa] 2016-03-25 rehideved [sol@hiqdev.com]
    - [a51bbb7] 2016-03-25 phpcsfixed [sol@hiqdev.com]
    - [ceed267] 2016-03-25 + disable/enableAuth for connection [sol@hiqdev.com]
- Updated CSS classes after yii2-debug extension update
    - [780aa3e] 2016-03-17 Fixed DebugPanel CSS classes because of yii2-debug extenstion update [d.naumenko.a@gmail.com]
- Deleted `Command::getList()`, `ActiveQuery::getList()`
    - [4cf1bce] 2016-02-26 Dropped `getList()` method in `Command`, `ActiveQuery` [d.naumenko.a@gmail.com]
    - [b75495c] 2016-02-09 Collection::setModel - fixed working with scenario [d.naumenko.a@gmail.com]
- Chhanged `Connection::setErrorChecker()` to support a callable function using array syntax
    - [ebbdcac] 2016-02-01 Connection::setErrorChecker now allows to set a callable function using array syntax [d.naumenko.a@gmail.com]
    - [94a1e11] 2016-01-18 refactored $handler and `$_errorChecker` [sol@hiqdev.com]
- Changed url, query and body processing in Connection
    - [24dd86f] 2016-01-18 fixed tests [sol@hiqdev.com]
    - [122c1d3] 2016-01-18 removed old curl request, improved url, query and body passing [sol@hiqdev.com]
- Added user agent
    - [5b2c014] 2016-01-18 added user agent [sol@hiqdev.com]
- Fixed CI
    - [9ad8bc4] 2016-01-17 improved .travis.yml [sol@hiqdev.com]
    - [f434ae4] 2016-01-15 fixed CI [sol@hiqdev.com]
- Changed: started redoing with Guzzle
    - [715a25d] 2016-01-18 renamed makeGuzzleRequest -> handleRequest [sol@hiqdev.com]
    - [32c32d8] 2016-01-15 ActiveQuery::getList() marked as deprecated [d.naumenko.a@gmail.com]
    - [55d33d9] 2016-01-15 Require guzzle, minor code enhancemants [bscheshir.work@gmail.com]
    - [4d29513] 2016-01-15 ActiveRecord::perform() - fixed action name generation for non-bulk requests [d.naumenko.a@gmail.com]
    - [b69881a] 2016-01-14 HiArtException - changed user-friendly message [d.naumenko.a@gmail.com]
    - [f9cb92b] 2016-01-14 Added Collection::checkConsistency property [d.naumenko.a@gmail.com]
    - [3b7614f] 2016-01-13 PHPDoc improved [d.naumenko.a@gmail.com]
    - [1d1d191] 2016-01-13 Merge branch 'bscheshirwork-master' [d.naumenko.a@gmail.com]
    - [19aea44] 2016-01-13 The errorChecker should be set first [bscheshir.work@gmail.com]
    - [747d1a7] 2016-01-11 PHPDocs improved [d.naumenko.a@gmail.com]
    - [cb1f5be] 2016-01-11 STARTED REDOING with Guzzle [sol@hiqdev.com]
    - [3f1a702] 2015-12-31 redone get/post/head/delete with makeRequest [sol@hiqdev.com]
- Added tests and CI
    - [6a97149] 2016-01-18 + ConnectionTest.php [sol@hiqdev.com]
    - [e80518e] 2015-12-30 php-cs-fixed [sol@hiqdev.com]
    - [fb9b595] 2015-12-30 added initial CommandTest [sol@hiqdev.com]
    - [ccdc3c3] 2015-12-30 doing tests and ci [sol@hiqdev.com]
- Changed Collection::models visibility to protected
    - [dd8cbf2] 2015-12-01 BC Breaking: Collection::models visibility changed from public to protected [d.naumenko.a@gmail.com]
- Fixed different issues
    - [fa26180] 2016-01-29 Collection: PHPDocs impreved - changed Model to ActiveRecord [d.naumenko.a@gmail.com]
    - [485f9c7] 2016-01-20 ErrorResponseException - added $response propery, __construct modified [d.naumenko.a@gmail.com]
    - [6d05685] 2015-12-25 Collection::collectData - removed forced typecasting $attributes to array [d.naumenko.a@gmail.com]
    - [e410ea1] 2015-12-17 Add usage to load method Collection class [andreyklochok@gmail.com]
    - [a561e2b] 2015-12-11 Collection::getIds() - changed static pk `id` to dynamic, got from model [d.naumenko.a@gmail.com]
    - [df76b0f] 2015-11-20 Updated PHPDocs to improve type hinting [d.naumenko.a@gmail.com]
    - [c72dff9] 2015-11-17 Collection - fixed PHPdoc, enhanced load() to use primaryKey from the model [d.naumenko.a@gmail.com]
    - [e1b4ddb] 2015-11-16 QueryBuilder - restored accidentally removed orderBy building [d.naumenko.a@gmail.com]
    - [3ec60a2] 2015-11-11 DebugPanel - added link to open query in new tab [d.naumenko.a@gmail.com]
    - [f855d47] 2015-10-29 * ActiveQuery::one() - fixed to use Query options [andreyklochok@gmail.com]
    - [c197d5b] 2015-10-29 * ActiveRecord::getScenarioCommand() - changed command generation logic, updated PHP [d.naumenko.a@gmail.com]
    - [3d51603] 2015-10-26 php-cs-fixed [sol@hiqdev.com]
    - [7218232] 2015-10-26 improved README [sol@hiqdev.com]
    - [7e95cbe] 2015-10-26 * Command::perform - changed request type from PUT to POST [d.naumenko.a@gmail.com]
    - [9d72ad6] 2015-10-26 - Removed ActiveRecord::arrayAttributes method [d.naumenko.a@gmail.com]
- Changed authorization in Connection class, made with configuration callback
    - [93fdf34] 2015-11-03 improved authorizing [sol@hiqdev.com]
- Added passing options to Command through find/One/All()
    - [93159e2] 2015-10-29 + find/One/All options for passing scenario to Command [sol@hiqdev.com]
- Added population of joined relations
    - [58d290c] 2015-10-28 * ActiveQuery::one() - fixed to populate joined relations [d.naumenko.a@gmail.com]
- Changed default limit to ALL
    - [b097fdf] 2015-10-26 x QueryBuilder::buildLimit() - added conversion of `-1` limit to `ALL` [d.naumenko.a@gmail.com]
    - [2eb1a29] 2015-10-22 + Implemented relations population using `with`, `joinWith`  * Changed default limit to ALL :!: [d.naumenko.a@gmail.com]
- Added recursive joining
    - [7bf29fe] 2015-10-23 + ActiveQuery added recusion population of joined relation [d.naumenko.a@gmail.com]
- Added lt/gt to QueryBuilder
    - [1445eb0] 2015-10-25 php-cs-fixed [sol@hiqdev.com]
    - [86796b5] 2015-10-08 QueryBuilder - added lt, gt condition handling [d.naumenko.a@gmail.com]
- Fixed translation, redone Re::l to Yii::t (sol@hiqdev.com)
    - [d286a03] 2015-09-21 fixed translation, redone Re::l to Yii::t [sol@hiqdev.com]
- Removed `gl_key`, `gl_value`
    - [e60f2da] 2015-09-14 ActiveRecord - removed `gl_key`, `gl_value` [d.naumenko.a@gmail.com]
- Added second argument to ActiveQuery::all that will be passed to Command::search
    - [8f703e7] 2015-09-01 ActiveQuery::all - added second argument that will be passed to Command::search [d.naumenko.a@gmail.com]
- Fixed 'raw' processing
    - [3a11077] 2015-08-28 fixed back 'raw' processing [sol@hiqdev.com]
- Fixed PHP warnings
    - [9e108ab] 2015-09-10 fixed PHP warning [sol@hiqdev.com]
    - [b89f0c8] 2015-08-27 fixed PHP warning [sol@hiqdev.com]

## [0.0.1] - 2015-08-26

- Added Connection::errorChecker callback to test if API response was error
    - [2913e20] 2015-08-26 + Connection::errorChecker callback to test if API response was error [sol@hiqdev.com]
- Fixed PHP warnings
    - [161858f] 2015-08-25 Fix warnings [andreyklochok@gmail.com]
- Changed: moved to src
    - [1218ec5] 2015-08-26 fixed project description [sol@hiqdev.com]
    - [fb39db4] 2015-08-26 php-cs-fixed [sol@hiqdev.com]
    - [f8ece0b] 2015-08-26 moved to src [sol@hiqdev.com]
    - [f63b354] 2015-08-26 rehideved [sol@hiqdev.com]
- Added basics
    - [e534bea] 2015-08-25 Added Connection [d.naumenko.a@gmail.com]
    - [93c054e] 2015-08-25 Added ErrorResponseException, HiResException -> HiArtException, added global checking of error responses, other minor [d.naumenko.a@gmail.com]
    - [3d87c1a] 2015-08-19 Fixed QueryBuiled in condition - force type casting to array [d.naumenko.a@gmail.com]
    - [ae4b098] 2015-08-06 * Collection: + count and populate from selection [sol@hiqdev.com]
    - [104c0fb] 2015-08-02 Collection::set now can accept single item [d.naumenko.a@gmail.com]
    - [e054b8c] 2015-07-30 + Collection::getIds [sol@hiqdev.com]
    - [84ba01e] 2015-07-30 Throw exception when update find an error [andreyklochok@gmail.com]
    - [eb75e05] 2015-07-29 crutch for strange php compiler error [sol@hiqdev.com]
    - [68ea22c] 2015-07-17 In collection - try to add delete mothod with after and before events. In Command fix hard-core Serach [andreyklochok@gmail.com]
    - [ba36245] 2015-06-24 Bulk operations [andreyklochok@gmail.com]
    - [2cba726] 2015-06-11 getScenarioCommand - enh of work with arrays [d.naumenko.a@gmail.com]
    - [fd386c9] 2015-06-10 Fixed validate() - fail if one of models failed to validate [d.naumenko.a@gmail.com]
    - [3cfe245] 2015-06-03 Fix findOne method [andreyklochok@gmail.com]
    - [a9eb1cd] 2015-05-28 Collection triggers events of each model to saved [d.naumenko.a@gmail.com]
- Changed: renamed to hiart
    - [cec1ad7] 2015-05-24 renamed hiart <- hiar in files [sol@hiqdev.com]
    - [9f25b4a] 2015-05-24 RENAMED to hiart [sol@hiqdev.com]
    - [672c3ca] 2015-05-22 Fixed inCond [d.naumenko.a@gmail.com]
    - [7ffd179] 2015-05-19 Collection added `EVENT_BEFORE/AFTER_LOAD` [d.naumenko.a@gmail.com]
    - [d3db42c] 2015-05-15 Collection will not save, if empty [d.naumenko.a@gmail.com]
    - [ff7903c] 2015-05-12 PHPdoc enhancements [d.naumenko.a@gmail.com]
    - [ee60010] 2015-05-12 PHPdoc updated [d.naumenko.a@gmail.com]
    - [23c808d] 2015-05-08 Collection unsed variabled deleted [d.naumenko.a@gmail.com]
    - [1a909ad] 2015-04-24 Merge confilct resolve [andreyklochok@gmail.com]
    - [39fe2be] 2015-04-24 Display API URL in debug panel [andreyklochok@gmail.com]
    - [0eec13c] 2015-04-24 Display API URL in debug panel [andreyklochok@gmail.com]
    - [63a1673] 2015-04-23 Collection - now recognises 3 different types of POST request data structure [d.naumenko.a@gmail.com]
    - [9748652] 2015-04-22 DebugPanel name fix [andreyklochok@gmail.com]
    - [2c7aac4] 2015-04-21 Change Re namespace [andreyklochok@gmail.com]
    - [a012d32] 2015-04-20 * ActiveRecord - added PrimaryValue conception  * Collection - added methods hasErrors, isEmpty [d.naumenko.a@gmail.com]
    - [9fcb34e] 2015-04-17 Restore Re class [andreyklochok@gmail.com]
    - [04e2366] 2015-04-17 Remove use Re [andreyklochok@gmail.com]
    - [92d2b45] 2015-04-17 fixed namespace to hiqdev\hiar [sol@hiqdev.com]
    - [c6d7f10] 2015-04-17 First commit [andreyklochok@gmail.com]

## [Development started] - 2015-04-17

[4296c47]: https://github.com/hiqdev/yii2-hiart/commit/4296c47
[354c0f9]: https://github.com/hiqdev/yii2-hiart/commit/354c0f9
[1b91d73]: https://github.com/hiqdev/yii2-hiart/commit/1b91d73
[a32ac04]: https://github.com/hiqdev/yii2-hiart/commit/a32ac04
[a131b13]: https://github.com/hiqdev/yii2-hiart/commit/a131b13
[1b7a4ac]: https://github.com/hiqdev/yii2-hiart/commit/1b7a4ac
[314ac7c]: https://github.com/hiqdev/yii2-hiart/commit/314ac7c
[da48238]: https://github.com/hiqdev/yii2-hiart/commit/da48238
[3554aaa]: https://github.com/hiqdev/yii2-hiart/commit/3554aaa
[a51bbb7]: https://github.com/hiqdev/yii2-hiart/commit/a51bbb7
[ceed267]: https://github.com/hiqdev/yii2-hiart/commit/ceed267
[780aa3e]: https://github.com/hiqdev/yii2-hiart/commit/780aa3e
[4cf1bce]: https://github.com/hiqdev/yii2-hiart/commit/4cf1bce
[b75495c]: https://github.com/hiqdev/yii2-hiart/commit/b75495c
[ebbdcac]: https://github.com/hiqdev/yii2-hiart/commit/ebbdcac
[94a1e11]: https://github.com/hiqdev/yii2-hiart/commit/94a1e11
[24dd86f]: https://github.com/hiqdev/yii2-hiart/commit/24dd86f
[122c1d3]: https://github.com/hiqdev/yii2-hiart/commit/122c1d3
[5b2c014]: https://github.com/hiqdev/yii2-hiart/commit/5b2c014
[9ad8bc4]: https://github.com/hiqdev/yii2-hiart/commit/9ad8bc4
[f434ae4]: https://github.com/hiqdev/yii2-hiart/commit/f434ae4
[715a25d]: https://github.com/hiqdev/yii2-hiart/commit/715a25d
[32c32d8]: https://github.com/hiqdev/yii2-hiart/commit/32c32d8
[55d33d9]: https://github.com/hiqdev/yii2-hiart/commit/55d33d9
[4d29513]: https://github.com/hiqdev/yii2-hiart/commit/4d29513
[b69881a]: https://github.com/hiqdev/yii2-hiart/commit/b69881a
[f9cb92b]: https://github.com/hiqdev/yii2-hiart/commit/f9cb92b
[3b7614f]: https://github.com/hiqdev/yii2-hiart/commit/3b7614f
[1d1d191]: https://github.com/hiqdev/yii2-hiart/commit/1d1d191
[19aea44]: https://github.com/hiqdev/yii2-hiart/commit/19aea44
[747d1a7]: https://github.com/hiqdev/yii2-hiart/commit/747d1a7
[cb1f5be]: https://github.com/hiqdev/yii2-hiart/commit/cb1f5be
[3f1a702]: https://github.com/hiqdev/yii2-hiart/commit/3f1a702
[6a97149]: https://github.com/hiqdev/yii2-hiart/commit/6a97149
[e80518e]: https://github.com/hiqdev/yii2-hiart/commit/e80518e
[fb9b595]: https://github.com/hiqdev/yii2-hiart/commit/fb9b595
[ccdc3c3]: https://github.com/hiqdev/yii2-hiart/commit/ccdc3c3
[dd8cbf2]: https://github.com/hiqdev/yii2-hiart/commit/dd8cbf2
[fa26180]: https://github.com/hiqdev/yii2-hiart/commit/fa26180
[485f9c7]: https://github.com/hiqdev/yii2-hiart/commit/485f9c7
[6d05685]: https://github.com/hiqdev/yii2-hiart/commit/6d05685
[e410ea1]: https://github.com/hiqdev/yii2-hiart/commit/e410ea1
[a561e2b]: https://github.com/hiqdev/yii2-hiart/commit/a561e2b
[df76b0f]: https://github.com/hiqdev/yii2-hiart/commit/df76b0f
[c72dff9]: https://github.com/hiqdev/yii2-hiart/commit/c72dff9
[e1b4ddb]: https://github.com/hiqdev/yii2-hiart/commit/e1b4ddb
[3ec60a2]: https://github.com/hiqdev/yii2-hiart/commit/3ec60a2
[f855d47]: https://github.com/hiqdev/yii2-hiart/commit/f855d47
[c197d5b]: https://github.com/hiqdev/yii2-hiart/commit/c197d5b
[3d51603]: https://github.com/hiqdev/yii2-hiart/commit/3d51603
[7218232]: https://github.com/hiqdev/yii2-hiart/commit/7218232
[7e95cbe]: https://github.com/hiqdev/yii2-hiart/commit/7e95cbe
[9d72ad6]: https://github.com/hiqdev/yii2-hiart/commit/9d72ad6
[93fdf34]: https://github.com/hiqdev/yii2-hiart/commit/93fdf34
[93159e2]: https://github.com/hiqdev/yii2-hiart/commit/93159e2
[58d290c]: https://github.com/hiqdev/yii2-hiart/commit/58d290c
[b097fdf]: https://github.com/hiqdev/yii2-hiart/commit/b097fdf
[2eb1a29]: https://github.com/hiqdev/yii2-hiart/commit/2eb1a29
[7bf29fe]: https://github.com/hiqdev/yii2-hiart/commit/7bf29fe
[1445eb0]: https://github.com/hiqdev/yii2-hiart/commit/1445eb0
[86796b5]: https://github.com/hiqdev/yii2-hiart/commit/86796b5
[d286a03]: https://github.com/hiqdev/yii2-hiart/commit/d286a03
[e60f2da]: https://github.com/hiqdev/yii2-hiart/commit/e60f2da
[8f703e7]: https://github.com/hiqdev/yii2-hiart/commit/8f703e7
[3a11077]: https://github.com/hiqdev/yii2-hiart/commit/3a11077
[9e108ab]: https://github.com/hiqdev/yii2-hiart/commit/9e108ab
[b89f0c8]: https://github.com/hiqdev/yii2-hiart/commit/b89f0c8
[2913e20]: https://github.com/hiqdev/yii2-hiart/commit/2913e20
[161858f]: https://github.com/hiqdev/yii2-hiart/commit/161858f
[1218ec5]: https://github.com/hiqdev/yii2-hiart/commit/1218ec5
[fb39db4]: https://github.com/hiqdev/yii2-hiart/commit/fb39db4
[f8ece0b]: https://github.com/hiqdev/yii2-hiart/commit/f8ece0b
[f63b354]: https://github.com/hiqdev/yii2-hiart/commit/f63b354
[e534bea]: https://github.com/hiqdev/yii2-hiart/commit/e534bea
[93c054e]: https://github.com/hiqdev/yii2-hiart/commit/93c054e
[3d87c1a]: https://github.com/hiqdev/yii2-hiart/commit/3d87c1a
[ae4b098]: https://github.com/hiqdev/yii2-hiart/commit/ae4b098
[104c0fb]: https://github.com/hiqdev/yii2-hiart/commit/104c0fb
[e054b8c]: https://github.com/hiqdev/yii2-hiart/commit/e054b8c
[84ba01e]: https://github.com/hiqdev/yii2-hiart/commit/84ba01e
[eb75e05]: https://github.com/hiqdev/yii2-hiart/commit/eb75e05
[68ea22c]: https://github.com/hiqdev/yii2-hiart/commit/68ea22c
[ba36245]: https://github.com/hiqdev/yii2-hiart/commit/ba36245
[2cba726]: https://github.com/hiqdev/yii2-hiart/commit/2cba726
[fd386c9]: https://github.com/hiqdev/yii2-hiart/commit/fd386c9
[3cfe245]: https://github.com/hiqdev/yii2-hiart/commit/3cfe245
[a9eb1cd]: https://github.com/hiqdev/yii2-hiart/commit/a9eb1cd
[cec1ad7]: https://github.com/hiqdev/yii2-hiart/commit/cec1ad7
[9f25b4a]: https://github.com/hiqdev/yii2-hiart/commit/9f25b4a
[672c3ca]: https://github.com/hiqdev/yii2-hiart/commit/672c3ca
[7ffd179]: https://github.com/hiqdev/yii2-hiart/commit/7ffd179
[d3db42c]: https://github.com/hiqdev/yii2-hiart/commit/d3db42c
[ff7903c]: https://github.com/hiqdev/yii2-hiart/commit/ff7903c
[ee60010]: https://github.com/hiqdev/yii2-hiart/commit/ee60010
[23c808d]: https://github.com/hiqdev/yii2-hiart/commit/23c808d
[1a909ad]: https://github.com/hiqdev/yii2-hiart/commit/1a909ad
[39fe2be]: https://github.com/hiqdev/yii2-hiart/commit/39fe2be
[0eec13c]: https://github.com/hiqdev/yii2-hiart/commit/0eec13c
[63a1673]: https://github.com/hiqdev/yii2-hiart/commit/63a1673
[9748652]: https://github.com/hiqdev/yii2-hiart/commit/9748652
[2c7aac4]: https://github.com/hiqdev/yii2-hiart/commit/2c7aac4
[a012d32]: https://github.com/hiqdev/yii2-hiart/commit/a012d32
[9fcb34e]: https://github.com/hiqdev/yii2-hiart/commit/9fcb34e
[04e2366]: https://github.com/hiqdev/yii2-hiart/commit/04e2366
[92d2b45]: https://github.com/hiqdev/yii2-hiart/commit/92d2b45
[c6d7f10]: https://github.com/hiqdev/yii2-hiart/commit/c6d7f10
[092d2ba]: https://github.com/hiqdev/yii2-hiart/commit/092d2ba
[752c00f]: https://github.com/hiqdev/yii2-hiart/commit/752c00f
[1d7e0ef]: https://github.com/hiqdev/yii2-hiart/commit/1d7e0ef
[e3acb67]: https://github.com/hiqdev/yii2-hiart/commit/e3acb67
[41c0608]: https://github.com/hiqdev/yii2-hiart/commit/41c0608
[6b3dc59]: https://github.com/hiqdev/yii2-hiart/commit/6b3dc59
[659b57b]: https://github.com/hiqdev/yii2-hiart/commit/659b57b
[3d00dcd]: https://github.com/hiqdev/yii2-hiart/commit/3d00dcd
[d6cc795]: https://github.com/hiqdev/yii2-hiart/commit/d6cc795
[bb80099]: https://github.com/hiqdev/yii2-hiart/commit/bb80099
[560d87c]: https://github.com/hiqdev/yii2-hiart/commit/560d87c
[0ee1eee]: https://github.com/hiqdev/yii2-hiart/commit/0ee1eee
[13340a1]: https://github.com/hiqdev/yii2-hiart/commit/13340a1
[412cecb]: https://github.com/hiqdev/yii2-hiart/commit/412cecb
[26679da]: https://github.com/hiqdev/yii2-hiart/commit/26679da
