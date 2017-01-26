hiqdev/yii2-hiart
-----------------

## [Under development]

- Implemented cURL transport
    - [951ae15] 2017-01-26 csfixed [@SilverFire]
    - [a2a733f] 2017-01-26 Implemented cURL transport [@SilverFire]
- Refactored exceptions
    - [55f40a4] 2017-01-26 Updated stream/Request::send() to follow RequestErrorException API changes [@SilverFire]
    - [1780efc] 2017-01-26 Enhanced AbstractResponse::isJson to prevent warning when there is not Content-Type header available [@SilverFire]
    - [9305ec8] 2017-01-26 Refactored exceptions to get rid of $errorInfo [@SilverFire]
- Improved PHPDocs, refactored many different methods, other minor enhancements equestErrorException
    - [68752e3] 2017-01-26 PHPDocs updated [@SilverFire]
    - [c8064d8] 2017-01-25 Minor enhancements [@SilverFire]
    - [381da06] 2017-01-25 + default getStatusCode and getReasonPhrase to proxy Request [@hiqsol]
    - [3aae6ec] 2017-01-25 added use of tests config [@hiqsol]
    - [f81184a] 2017-01-25 Improved PHPDocs, refactored many different methods, other minor enhancements [@SilverFire]
    - [2a1eefb] 2017-01-25 fixed building auth headers [@hiqsol]
    - [a54f1f6] 2017-01-25 added auth headers NOT TESTED [@hiqsol]
    - [938ad95] 2017-01-25 fixed sending request in DebugAction [@hiqsol]
    - [3f77a7f] 2017-01-25 renamed a bit [@hiqsol]

## [0.1.0] - 2017-01-25

- Changed nearly everything: **REDONE** to be usable for any API
    - [b1c36a2] 2017-01-25 fixed tests bootstrap config [@hiqsol]
    - [1584a18] 2017-01-25 + require phpunit [@hiqsol]
    - [ddadf41] 2017-01-25 moved `$handlerClass` to AbstractRequest [@hiqsol]
    - [e8ec09b] 2017-01-24 csfixed [@hiqsol]
    - [53a4645] 2017-01-24 fixed tests [@hiqsol]
    - [6dd6b93] 2017-01-24 merged [@hiqsol]
    - [ebab31a] 2017-01-24 adding yii2-hiart-github for tests [@hiqsol]
    - [67297d4] 2017-01-24 removed getBaseUri from debug, used getFullUri from request [@hiqsol]
    - [eaf137c] 2017-01-24 improved `AbstractRequest::getDb` [@hiqsol]
    - [4fb00e7] 2017-01-24 improved config and params [@hiqsol]
    - [d128a27] 2017-01-24 improved Connection::getBaseUri to add trailing slash if uri is domain only [@hiqsol]
    - [c648c01] 2017-01-24 and fixed config once more again [@hiqsol]
    - [e1aab41] 2017-01-24 fixed configs again [@hiqsol]
    - [e72ea42] 2017-01-24 fixed configs again [@hiqsol]
    - [e1563ba] 2017-01-24 fixed configs [@hiqsol]
    - [59b3e2e] 2017-01-24 added common config [@hiqsol]
    - [ecc46d2] 2017-01-24 added ConnectionInterface and DI configuration [@hiqsol]
    - [976d36b] 2017-01-24 + params config [@hiqsol]
    - [1cd3d7b] 2017-01-23 changed `getDb()` [@hiqsol]
    - [a766a44] 2017-01-23 added `prepare()` in ActiveRecord, fixed `attributes()` [@hiqsol]
    - [d367d14] 2017-01-23 fixed getting protocol version in stream Response [@hiqsol]
    - [d632d37] 2017-01-23 stream transport looks working [@hiqsol]
    - [2d4076c] 2017-01-23 fixing stream transport [@hiqsol]
    - [8011768] 2017-01-23 add `Connection::userAgent` [@hiqsol]
    - [9ef2129] 2017-01-23 refactored exceptions [@hiqsol]
    - [88b224e] 2017-01-22 still refactoring [@hiqsol]
    - [5d53696] 2017-01-22 refactored proxy transport [@hiqsol]
    - [cd0f032] 2017-01-22 added stream transport implementation [@hiqsol]
    - [8718fc3] 2017-01-22 renamed buildRequest -> build [@hiqsol]
    - [7340b9c] 2017-01-22 fixed minor issues [@hiqsol]
    - [f8fd255] 2017-01-22 inited stream transport implementation [@hiqsol]
    - [4bd0fb1] 2017-01-22 redone preparing config for db handler [@hiqsol]
    - [f0f2ee7] 2017-01-22 simplified `send` function in ProxyRequest [@hiqsol]
    - [e6916d2] 2017-01-22 csfixed [@hiqsol]
    - [46f9765] 2017-01-22 separated out ProxyRequest [@hiqsol]
    - [c4c4eaf] 2017-01-22 + QueryBuilderInterface [@hiqsol]
    - [fcd5feb] 2017-01-21 redone to abstract classes [@hiqsol]
    - [4e5ed01] 2017-01-21 inited curl [@hiqsol]
    - [35293a6] 2017-01-21 inited rest [@hiqsol]
    - [26b1016] 2017-01-21 docs [@hiqsol]
    - [930b738] 2017-01-21 renamed Exception <- HiArtException [@hiqsol]
    - [414d5ad] 2017-01-21 docs [@hiqsol]
    - [6cbe9cb] 2017-01-20 fixed error handling [@hiqsol]
    - [eea4575] 2017-01-20 removed direct perform() from Connection [@hiqsol]
    - [632fe97] 2017-01-20 removed direct HTTP requesting functions: get, head, post, put, delete [@hiqsol]
    - [d45d933] 2017-01-20 more fixes STILL redo NOT FINISHED [@hiqsol]
    - [920c928] 2017-01-20 fixed Query::count [@hiqsol]
    - [e31da56] 2017-01-20 fixed JS for run query result [@hiqsol]
    - [9b24ea4] 2017-01-19 simplified indexBy and createModels [@hiqsol]
    - [bfd8671] 2017-01-19 refactoring NOT FINISHED [@hiqsol]
    - [4369206] 2017-01-19 + serialization in Request [@hiqsol]
    - [aba9fca] 2017-01-19 fixed DebugAction [@hiqsol]
    - [935c7cf] 2017-01-19 improved debug rendering with Timing STILL redo NOT FINISHED [@hiqsol]
    - [b6234de] 2017-01-19 redone debug with detail view [@hiqsol]
    - [f4b70e8] 2017-01-19 reorganized debug into debug dir [@hiqsol]
    - [248410e] 2017-01-18 fixed count, still redoing NOT FINISHED [@hiqsol]
    - [d0c8015] 2017-01-18 received answer from api, ura NOT FINISHED [@hiqsol]
    - [47a9ef5] 2017-01-18 more HUGE redoing with Request and Response NOT FINISHED [@hiqsol]
    - [ce8fb9d] 2017-01-18 added Request and Response wrappers [@hiqsol]
    - [95bd315] 2017-01-17 still trying HUGE redo NOT FINISHED [@hiqsol]
    - [076f91f] 2017-01-16 trying HUGE redo [@hiqsol]
- Added inversed relations
    - [d3a6d46] 2016-12-27 csfixed [@hiqsol]
    - [9247301] 2016-12-27 + config/hisite.php for debug panel [@hiqsol]
    - [db65225] 2016-12-13 Removed $body parameter from Connection::get() method [@SilverFire]
    - [3e43056] 2016-12-08 PHPDoc enhanced [@SilverFire]
    - [27ca6fe] 2016-11-23 Added ilike condition to QueryBuilder [@SilverFire]
    - [d3756e4] 2016-11-09 Added `ni` condition [@SilverFire]
    - [ea50c04] 2016-11-03 Added NotEq condition [@SilverFire]
    - [8047d37] 2016-09-18 redone bumping with `chkipper` [@hiqsol]
    - [752c00f] 2016-09-07 Implemented inversed relations [@SilverFire]
- Added nested joinWith
    - [1d7e0ef] 2016-08-31 Implemented nested joinWith [@SilverFire]
- Changed `api_url` to `base_uri` (as in guzzle)
    - [bb80099] 2016-06-29 DebugPanel - changed `api_url` to `base_uri` parameter [@SilverFire]
- Fixed code styling alot
    - [092d2ba] 2016-09-13 Updated PHPDoc [@SilverFire]
    - [e3acb67] 2016-08-18 ActiveQuery::prepare() returns $this [@SilverFire]
    - [41c0608] 2016-08-17 Added setter and getter for check auth [@tafid]
    - [6b3dc59] 2016-07-20 ActiveQuery - added joined relation populating for indexed relations [@SilverFire]
    - [659b57b] 2016-07-19 ActiveQuery::addSelect added casting param to array [@tafid]
    - [3d00dcd] 2016-07-16 csfixed [@hiqsol]
    - [d6cc795] 2016-07-12 csfixed [@hiqsol]
    - [560d87c] 2016-06-26 Removed commented code [@SilverFire]
    - [0ee1eee] 2016-06-04 Connection::getHandler() - removed calling Psr7/Client::setUserAgent() call and replaced with config" [@SilverFire]
    - [13340a1] 2016-06-06 + separate checkError for overriding [@hiqsol]
    - [412cecb] 2016-06-02 added ge/le conditions, improved building compare conditions [@hiqsol]
    - [26679da] 2016-05-12 fixed several scrutinizer bugs [@hiqsol]

## [0.0.4] - 2016-05-11

- Fixed tests
    - [4296c47] 2016-05-11 fixed tests [@hiqsol]

## [0.0.3] - 2016-05-11

- Added asset-packagist.org repository to composer.json
    - [354c0f9] 2016-05-11 Added asset-packagist.org repository to composer.json [@SilverFire]

## [0.0.2] - 2016-05-11

- Fixed not renamed `hiresoruce` to `hiart`
    - [1b91d73] 2016-05-11 Updated changelog [@SilverFire]
    - [a32ac04] 2016-05-11 Renamed all mentions of `hiresource` to `hiart` [@SilverFire]
- Fixed `to new tab` link when POST request has no variables in query string
    - [a131b13] 2016-04-11 DebugPanel - fixed `to new tab` link when POST request has no variables in query string [@SilverFire]
- Added relations population via `->joinWith()`
    - [1b7a4ac] 2016-03-07 ActiveQuery::populateJoinedRelations() fixed processing of multilevel relations [@SilverFire]
    - [314ac7c] 2016-04-08 ActiveQuery - added pupulate method [@SilverFire]
    - [da48238] 2016-04-15 Improve populateRelation method [@tafid]
- Added `Connection::disableAuth()`, `::enableAuth()` in order to manage auth conditions per connection
    - [3554aaa] 2016-03-25 rehideved [@hiqsol]
    - [a51bbb7] 2016-03-25 phpcsfixed [@hiqsol]
    - [ceed267] 2016-03-25 + disable/enableAuth for connection [@hiqsol]
- Updated CSS classes after yii2-debug extension update
    - [780aa3e] 2016-03-17 Fixed DebugPanel CSS classes because of yii2-debug extenstion update [@SilverFire]
- Deleted `Command::getList()`, `ActiveQuery::getList()`
    - [4cf1bce] 2016-02-26 Dropped `getList()` method in `Command`, `ActiveQuery` [@SilverFire]
    - [b75495c] 2016-02-09 Collection::setModel - fixed working with scenario [@SilverFire]
- Chhanged `Connection::setErrorChecker()` to support a callable function using array syntax
    - [ebbdcac] 2016-02-01 Connection::setErrorChecker now allows to set a callable function using array syntax [@SilverFire]
    - [94a1e11] 2016-01-18 refactored $handler and `$_errorChecker` [@hiqsol]
- Changed url, query and body processing in Connection
    - [24dd86f] 2016-01-18 fixed tests [@hiqsol]
    - [122c1d3] 2016-01-18 removed old curl request, improved url, query and body passing [@hiqsol]
- Added user agent
    - [5b2c014] 2016-01-18 added user agent [@hiqsol]
- Fixed CI
    - [9ad8bc4] 2016-01-17 improved .travis.yml [@hiqsol]
    - [f434ae4] 2016-01-15 fixed CI [@hiqsol]
- Changed: started redoing with Guzzle
    - [715a25d] 2016-01-18 renamed makeGuzzleRequest -> handleRequest [@hiqsol]
    - [32c32d8] 2016-01-15 ActiveQuery::getList() marked as deprecated [@SilverFire]
    - [55d33d9] 2016-01-15 Require guzzle, minor code enhancemants [bscheshir.work@gmail.com]
    - [4d29513] 2016-01-15 ActiveRecord::perform() - fixed action name generation for non-bulk requests [@SilverFire]
    - [b69881a] 2016-01-14 HiArtException - changed user-friendly message [@SilverFire]
    - [f9cb92b] 2016-01-14 Added Collection::checkConsistency property [@SilverFire]
    - [3b7614f] 2016-01-13 PHPDoc improved [@SilverFire]
    - [1d1d191] 2016-01-13 Merge branch 'bscheshirwork-master' [@SilverFire]
    - [19aea44] 2016-01-13 The errorChecker should be set first [bscheshir.work@gmail.com]
    - [747d1a7] 2016-01-11 PHPDocs improved [@SilverFire]
    - [cb1f5be] 2016-01-11 STARTED REDOING with Guzzle [@hiqsol]
    - [3f1a702] 2015-12-31 redone get/post/head/delete with makeRequest [@hiqsol]
- Added tests and CI
    - [6a97149] 2016-01-18 + ConnectionTest.php [@hiqsol]
    - [e80518e] 2015-12-30 php-cs-fixed [@hiqsol]
    - [fb9b595] 2015-12-30 added initial CommandTest [@hiqsol]
    - [ccdc3c3] 2015-12-30 doing tests and ci [@hiqsol]
- Changed Collection::models visibility to protected
    - [dd8cbf2] 2015-12-01 BC Breaking: Collection::models visibility changed from public to protected [@SilverFire]
- Fixed different issues
    - [fa26180] 2016-01-29 Collection: PHPDocs impreved - changed Model to ActiveRecord [@SilverFire]
    - [485f9c7] 2016-01-20 ErrorResponseException - added $response propery, __construct modified [@SilverFire]
    - [6d05685] 2015-12-25 Collection::collectData - removed forced typecasting $attributes to array [@SilverFire]
    - [e410ea1] 2015-12-17 Add usage to load method Collection class [@tafid]
    - [a561e2b] 2015-12-11 Collection::getIds() - changed static pk `id` to dynamic, got from model [@SilverFire]
    - [df76b0f] 2015-11-20 Updated PHPDocs to improve type hinting [@SilverFire]
    - [c72dff9] 2015-11-17 Collection - fixed PHPdoc, enhanced load() to use primaryKey from the model [@SilverFire]
    - [e1b4ddb] 2015-11-16 QueryBuilder - restored accidentally removed orderBy building [@SilverFire]
    - [3ec60a2] 2015-11-11 DebugPanel - added link to open query in new tab [@SilverFire]
    - [f855d47] 2015-10-29 * ActiveQuery::one() - fixed to use Query options [@tafid]
    - [c197d5b] 2015-10-29 * ActiveRecord::getScenarioCommand() - changed command generation logic, updated PHP [@SilverFire]
    - [3d51603] 2015-10-26 php-cs-fixed [@hiqsol]
    - [7218232] 2015-10-26 improved README [@hiqsol]
    - [7e95cbe] 2015-10-26 * Command::perform - changed request type from PUT to POST [@SilverFire]
    - [9d72ad6] 2015-10-26 - Removed ActiveRecord::arrayAttributes method [@SilverFire]
- Changed authorization in Connection class, made with configuration callback
    - [93fdf34] 2015-11-03 improved authorizing [@hiqsol]
- Added passing options to Command through find/One/All()
    - [93159e2] 2015-10-29 + find/One/All options for passing scenario to Command [@hiqsol]
- Added population of joined relations
    - [58d290c] 2015-10-28 * ActiveQuery::one() - fixed to populate joined relations [@SilverFire]
- Changed default limit to ALL
    - [b097fdf] 2015-10-26 x QueryBuilder::buildLimit() - added conversion of `-1` limit to `ALL` [@SilverFire]
    - [2eb1a29] 2015-10-22 + Implemented relations population using `with`, `joinWith`  * Changed default limit to ALL :!: [@SilverFire]
- Added recursive joining
    - [7bf29fe] 2015-10-23 + ActiveQuery added recusion population of joined relation [@SilverFire]
- Added lt/gt to QueryBuilder
    - [1445eb0] 2015-10-25 php-cs-fixed [@hiqsol]
    - [86796b5] 2015-10-08 QueryBuilder - added lt, gt condition handling [@SilverFire]
- Fixed translation, redone Re::l to Yii::t (sol@hiqdev.com)
    - [d286a03] 2015-09-21 fixed translation, redone Re::l to Yii::t [@hiqsol]
- Removed `gl_key`, `gl_value`
    - [e60f2da] 2015-09-14 ActiveRecord - removed `gl_key`, `gl_value` [@SilverFire]
- Added second argument to ActiveQuery::all that will be passed to Command::search
    - [8f703e7] 2015-09-01 ActiveQuery::all - added second argument that will be passed to Command::search [@SilverFire]
- Fixed 'raw' processing
    - [3a11077] 2015-08-28 fixed back 'raw' processing [@hiqsol]
- Fixed PHP warnings
    - [9e108ab] 2015-09-10 fixed PHP warning [@hiqsol]
    - [b89f0c8] 2015-08-27 fixed PHP warning [@hiqsol]

## [0.0.1] - 2015-08-26

- Added Connection::errorChecker callback to test if API response was error
    - [2913e20] 2015-08-26 + Connection::errorChecker callback to test if API response was error [@hiqsol]
- Fixed PHP warnings
    - [161858f] 2015-08-25 Fix warnings [@tafid]
- Changed: moved to src
    - [1218ec5] 2015-08-26 fixed project description [@hiqsol]
    - [fb39db4] 2015-08-26 php-cs-fixed [@hiqsol]
    - [f8ece0b] 2015-08-26 moved to src [@hiqsol]
    - [f63b354] 2015-08-26 rehideved [@hiqsol]
- Added basics
    - [e534bea] 2015-08-25 Added Connection [@SilverFire]
    - [93c054e] 2015-08-25 Added ErrorResponseException, HiResException -> HiArtException, added global checking of error responses, other minor [@SilverFire]
    - [3d87c1a] 2015-08-19 Fixed QueryBuiled in condition - force type casting to array [@SilverFire]
    - [ae4b098] 2015-08-06 * Collection: + count and populate from selection [@hiqsol]
    - [104c0fb] 2015-08-02 Collection::set now can accept single item [@SilverFire]
    - [e054b8c] 2015-07-30 + Collection::getIds [@hiqsol]
    - [84ba01e] 2015-07-30 Throw exception when update find an error [@tafid]
    - [eb75e05] 2015-07-29 crutch for strange php compiler error [@hiqsol]
    - [68ea22c] 2015-07-17 In collection - try to add delete mothod with after and before events. In Command fix hard-core Serach [@tafid]
    - [ba36245] 2015-06-24 Bulk operations [@tafid]
    - [2cba726] 2015-06-11 getScenarioCommand - enh of work with arrays [@SilverFire]
    - [fd386c9] 2015-06-10 Fixed validate() - fail if one of models failed to validate [@SilverFire]
    - [3cfe245] 2015-06-03 Fix findOne method [@tafid]
    - [a9eb1cd] 2015-05-28 Collection triggers events of each model to saved [@SilverFire]
- Changed: renamed to hiart
    - [cec1ad7] 2015-05-24 renamed hiart <- hiar in files [@hiqsol]
    - [9f25b4a] 2015-05-24 RENAMED to hiart [@hiqsol]
    - [672c3ca] 2015-05-22 Fixed inCond [@SilverFire]
    - [7ffd179] 2015-05-19 Collection added `EVENT_BEFORE/AFTER_LOAD` [@SilverFire]
    - [d3db42c] 2015-05-15 Collection will not save, if empty [@SilverFire]
    - [ff7903c] 2015-05-12 PHPdoc enhancements [@SilverFire]
    - [ee60010] 2015-05-12 PHPdoc updated [@SilverFire]
    - [23c808d] 2015-05-08 Collection unsed variabled deleted [@SilverFire]
    - [1a909ad] 2015-04-24 Merge confilct resolve [@tafid]
    - [39fe2be] 2015-04-24 Display API URL in debug panel [@tafid]
    - [0eec13c] 2015-04-24 Display API URL in debug panel [@tafid]
    - [63a1673] 2015-04-23 Collection - now recognises 3 different types of POST request data structure [@SilverFire]
    - [9748652] 2015-04-22 DebugPanel name fix [@tafid]
    - [2c7aac4] 2015-04-21 Change Re namespace [@tafid]
    - [a012d32] 2015-04-20 * ActiveRecord - added PrimaryValue conception  * Collection - added methods hasErrors, isEmpty [@SilverFire]
    - [9fcb34e] 2015-04-17 Restore Re class [@tafid]
    - [04e2366] 2015-04-17 Remove use Re [@tafid]
    - [92d2b45] 2015-04-17 fixed namespace to hiqdev\hiar [@hiqsol]
    - [c6d7f10] 2015-04-17 First commit [@tafid]

## [Development started] - 2015-04-17

[@hiqsol]: https://github.com/hiqsol
[sol@hiqdev.com]: https://github.com/hiqsol
[@SilverFire]: https://github.com/SilverFire
[d.naumenko.a@gmail.com]: https://github.com/SilverFire
[@tafid]: https://github.com/tafid
[andreyklochok@gmail.com]: https://github.com/tafid
[@BladeRoot]: https://github.com/BladeRoot
[bladeroot@gmail.com]: https://github.com/BladeRoot
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
[b1c36a2]: https://github.com/hiqdev/yii2-hiart/commit/b1c36a2
[1584a18]: https://github.com/hiqdev/yii2-hiart/commit/1584a18
[ddadf41]: https://github.com/hiqdev/yii2-hiart/commit/ddadf41
[e8ec09b]: https://github.com/hiqdev/yii2-hiart/commit/e8ec09b
[53a4645]: https://github.com/hiqdev/yii2-hiart/commit/53a4645
[6dd6b93]: https://github.com/hiqdev/yii2-hiart/commit/6dd6b93
[ebab31a]: https://github.com/hiqdev/yii2-hiart/commit/ebab31a
[67297d4]: https://github.com/hiqdev/yii2-hiart/commit/67297d4
[eaf137c]: https://github.com/hiqdev/yii2-hiart/commit/eaf137c
[4fb00e7]: https://github.com/hiqdev/yii2-hiart/commit/4fb00e7
[d128a27]: https://github.com/hiqdev/yii2-hiart/commit/d128a27
[c648c01]: https://github.com/hiqdev/yii2-hiart/commit/c648c01
[e1aab41]: https://github.com/hiqdev/yii2-hiart/commit/e1aab41
[e72ea42]: https://github.com/hiqdev/yii2-hiart/commit/e72ea42
[e1563ba]: https://github.com/hiqdev/yii2-hiart/commit/e1563ba
[59b3e2e]: https://github.com/hiqdev/yii2-hiart/commit/59b3e2e
[ecc46d2]: https://github.com/hiqdev/yii2-hiart/commit/ecc46d2
[976d36b]: https://github.com/hiqdev/yii2-hiart/commit/976d36b
[1cd3d7b]: https://github.com/hiqdev/yii2-hiart/commit/1cd3d7b
[a766a44]: https://github.com/hiqdev/yii2-hiart/commit/a766a44
[d367d14]: https://github.com/hiqdev/yii2-hiart/commit/d367d14
[d632d37]: https://github.com/hiqdev/yii2-hiart/commit/d632d37
[2d4076c]: https://github.com/hiqdev/yii2-hiart/commit/2d4076c
[8011768]: https://github.com/hiqdev/yii2-hiart/commit/8011768
[9ef2129]: https://github.com/hiqdev/yii2-hiart/commit/9ef2129
[88b224e]: https://github.com/hiqdev/yii2-hiart/commit/88b224e
[5d53696]: https://github.com/hiqdev/yii2-hiart/commit/5d53696
[cd0f032]: https://github.com/hiqdev/yii2-hiart/commit/cd0f032
[8718fc3]: https://github.com/hiqdev/yii2-hiart/commit/8718fc3
[7340b9c]: https://github.com/hiqdev/yii2-hiart/commit/7340b9c
[f8fd255]: https://github.com/hiqdev/yii2-hiart/commit/f8fd255
[4bd0fb1]: https://github.com/hiqdev/yii2-hiart/commit/4bd0fb1
[f0f2ee7]: https://github.com/hiqdev/yii2-hiart/commit/f0f2ee7
[e6916d2]: https://github.com/hiqdev/yii2-hiart/commit/e6916d2
[46f9765]: https://github.com/hiqdev/yii2-hiart/commit/46f9765
[c4c4eaf]: https://github.com/hiqdev/yii2-hiart/commit/c4c4eaf
[fcd5feb]: https://github.com/hiqdev/yii2-hiart/commit/fcd5feb
[4e5ed01]: https://github.com/hiqdev/yii2-hiart/commit/4e5ed01
[35293a6]: https://github.com/hiqdev/yii2-hiart/commit/35293a6
[26b1016]: https://github.com/hiqdev/yii2-hiart/commit/26b1016
[930b738]: https://github.com/hiqdev/yii2-hiart/commit/930b738
[414d5ad]: https://github.com/hiqdev/yii2-hiart/commit/414d5ad
[6cbe9cb]: https://github.com/hiqdev/yii2-hiart/commit/6cbe9cb
[eea4575]: https://github.com/hiqdev/yii2-hiart/commit/eea4575
[632fe97]: https://github.com/hiqdev/yii2-hiart/commit/632fe97
[d45d933]: https://github.com/hiqdev/yii2-hiart/commit/d45d933
[920c928]: https://github.com/hiqdev/yii2-hiart/commit/920c928
[e31da56]: https://github.com/hiqdev/yii2-hiart/commit/e31da56
[9b24ea4]: https://github.com/hiqdev/yii2-hiart/commit/9b24ea4
[bfd8671]: https://github.com/hiqdev/yii2-hiart/commit/bfd8671
[4369206]: https://github.com/hiqdev/yii2-hiart/commit/4369206
[aba9fca]: https://github.com/hiqdev/yii2-hiart/commit/aba9fca
[935c7cf]: https://github.com/hiqdev/yii2-hiart/commit/935c7cf
[b6234de]: https://github.com/hiqdev/yii2-hiart/commit/b6234de
[f4b70e8]: https://github.com/hiqdev/yii2-hiart/commit/f4b70e8
[248410e]: https://github.com/hiqdev/yii2-hiart/commit/248410e
[d0c8015]: https://github.com/hiqdev/yii2-hiart/commit/d0c8015
[47a9ef5]: https://github.com/hiqdev/yii2-hiart/commit/47a9ef5
[ce8fb9d]: https://github.com/hiqdev/yii2-hiart/commit/ce8fb9d
[95bd315]: https://github.com/hiqdev/yii2-hiart/commit/95bd315
[076f91f]: https://github.com/hiqdev/yii2-hiart/commit/076f91f
[d3a6d46]: https://github.com/hiqdev/yii2-hiart/commit/d3a6d46
[9247301]: https://github.com/hiqdev/yii2-hiart/commit/9247301
[db65225]: https://github.com/hiqdev/yii2-hiart/commit/db65225
[3e43056]: https://github.com/hiqdev/yii2-hiart/commit/3e43056
[27ca6fe]: https://github.com/hiqdev/yii2-hiart/commit/27ca6fe
[d3756e4]: https://github.com/hiqdev/yii2-hiart/commit/d3756e4
[ea50c04]: https://github.com/hiqdev/yii2-hiart/commit/ea50c04
[8047d37]: https://github.com/hiqdev/yii2-hiart/commit/8047d37
[Under development]: https://github.com/hiqdev/yii2-hiart/compare/0.1.0...HEAD
[0.0.4]: https://github.com/hiqdev/yii2-hiart/compare/0.0.3...0.0.4
[0.0.3]: https://github.com/hiqdev/yii2-hiart/compare/0.0.2...0.0.3
[0.0.2]: https://github.com/hiqdev/yii2-hiart/compare/0.0.1...0.0.2
[0.0.1]: https://github.com/hiqdev/yii2-hiart/releases/tag/0.0.1
[0.1.0]: https://github.com/hiqdev/yii2-hiart/compare/0.0.4...0.1.0
[a2a733f]: https://github.com/hiqdev/yii2-hiart/commit/a2a733f
[55f40a4]: https://github.com/hiqdev/yii2-hiart/commit/55f40a4
[68752e3]: https://github.com/hiqdev/yii2-hiart/commit/68752e3
[1780efc]: https://github.com/hiqdev/yii2-hiart/commit/1780efc
[9305ec8]: https://github.com/hiqdev/yii2-hiart/commit/9305ec8
[c8064d8]: https://github.com/hiqdev/yii2-hiart/commit/c8064d8
[381da06]: https://github.com/hiqdev/yii2-hiart/commit/381da06
[3aae6ec]: https://github.com/hiqdev/yii2-hiart/commit/3aae6ec
[f81184a]: https://github.com/hiqdev/yii2-hiart/commit/f81184a
[2a1eefb]: https://github.com/hiqdev/yii2-hiart/commit/2a1eefb
[a54f1f6]: https://github.com/hiqdev/yii2-hiart/commit/a54f1f6
[938ad95]: https://github.com/hiqdev/yii2-hiart/commit/938ad95
[3f77a7f]: https://github.com/hiqdev/yii2-hiart/commit/3f77a7f
[951ae15]: https://github.com/hiqdev/yii2-hiart/commit/951ae15
