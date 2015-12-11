<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个扩展包加载文件类。
  *
  * @author shsrain <shsrain@163.com>
  */
require_once __DIR__.'/jsonlint/vendor/autoload.php';
require_once __DIR__.'/jsonschema/vendor/autoload.php';
require_once __DIR__.'/json/vendor/autoload.php';
require_once __DIR__.'/validation/vendor/autoload.php';
require_once __DIR__.'/pagination/vendor/autoload.php';
require_once __DIR__.'/phpsocketraw/vendor/autoload.php';
require_once __DIR__.'/Cache/autoload.php';
require_once __DIR__.'/Image/autoload.php';
require_once __DIR__.'/Upload/vendor/autoload.php';
require_once __DIR__.'/OAuth2/Autoloader.php';
\OAuth2\Autoloader::register();
require_once __DIR__.'/SimplePHPCache/vendor/autoload.php';
require_once __DIR__.'/filesystem/vendor/autoload.php';
require_once __DIR__.'/httpSendFile/vendor/autoload.php';
require_once __DIR__.'/FastRoute/vendor/autoload.php';
require_once __DIR__.'/react/vendor/autoload.php';
