<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个咨询相关资源模块入口文件。
  *
  * @author shsrain <shsrain@163.com>
  */

// 定义入口根目录
defined('BASE_PATH') or define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
// 加载核心文件。
require_once __DIR__.'/bootstrap/http/app.php';
$app = app();
// 记录请求日志
// http_log();
$app['loader']->manifest('inquiry',$app);
$app['http']->run();
