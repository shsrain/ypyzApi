<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个核心包加载文件。
  *
  * @author shsrain <shsrain@163.com>
  */
require_once __DIR__.'/container/Pimple/src/Pimple/Container.php';
require_once __DIR__.'/http/Slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();
require_once __DIR__.'/database/notorm/NotORM.php';
require_once __DIR__.'/view/Twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
require_once __DIR__.'/event/evenement/vendor/autoload.php';
require_once __DIR__.'/bus/command/vendor/autoload.php';
